<?php

declare(strict_types=1);

namespace Unnits\BankId;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Utils;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Core\JWKSet;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A128CBCHS256;
use Jose\Component\Encryption\Algorithm\KeyEncryption\RSAOAEP256;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\JWEBuilder;
use Jose\Component\Encryption\Serializer\CompactSerializer;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer as SignatureCompactSerializer;
use JsonException;
use RuntimeException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Unnits\BankId\DTO\AuthToken;
use Unnits\BankId\DTO\Profile;
use Unnits\BankId\DTO\RequestObject;
use Unnits\BankId\DTO\RequestObjectCreationResponse;
use Unnits\BankId\Enums\JsonWebKeyUsage;
use Unnits\BankId\Enums\Scope;
use Unnits\BankId\Exceptions\RequestObjectCreationException;
use Unnits\BankId\Exceptions\TokenCreationException;

class Client
{
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly string $baseUri,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $redirectUri,
    ) {
        //
    }

    /**
     * @param string $state
     * @param string|null $bankId
     * @param string|null $requestUri
     * @param Scope[] $scopes
     * @return AuthorizationUri
     */
    public function getAuthUri(
        string $state,
        ?string $bankId = null,
        ?string $requestUri = null,
        array $scopes = [Scope::OpenId]
    ): AuthorizationUri {
        return new AuthorizationUri(
            baseUri: $this->baseUri,
            clientId: $this->clientId,
            redirectUri: $this->redirectUri,
            state: $state,
            bankId: $bankId,
            scopes: $scopes,
            requestUri: $requestUri,
        );
    }

    /**
     * @param string $code
     * @return AuthToken
     * @throws ClientExceptionInterface
     * @throws TokenCreationException
     * @throws Exception
     */
    public function getToken(string $code): AuthToken
    {
        $request = new Request(
            method: 'POST',
            uri: sprintf('%s/token', $this->baseUri),
            headers: [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            body: http_build_query([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ])
        );

        $response = $this->httpClient->sendRequest($request);

        $content = Utils::jsonDecode(
            $response->getBody()->getContents(),
            assoc: true
        );

        assert(is_array($content));

        if ($response->getStatusCode() !== 200) {
            throw new TokenCreationException(sprintf(
                'Failed creating new token: (%s) %s',
                $content['error'],
                $content['error_description'],
            ));
        }

        return AuthToken::create($content);
    }

    /**
     * @param AuthToken $token
     * @return Profile
     * @throws ClientExceptionInterface
     */
    public function getProfile(AuthToken $token): Profile
    {
        $request = new Request(
            method: 'GET',
            uri: sprintf('%s/profile', $this->baseUri),
            headers: [
                'Authorization' => sprintf('Bearer %s', $token->value)
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $content = json_decode(
            $response->getBody()->getContents(),
            associative: true
        );

        return Profile::create($content);
    }

    /**
     * @param RequestObject $requestObject
     * @param JWK $applicationSignatureKey
     * @return RequestObjectCreationResponse
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function createRequestObject(
        RequestObject $requestObject,
        JWK $applicationSignatureKey
    ): RequestObjectCreationResponse {
        // 1. podepsat JSON vzniklý z $requestObject
        // 2. zašifrovat výsledný JSON klíčem z BankId

        $body = json_encode($requestObject);
        assert(is_string($body));

        $jwkSet = $this->getBankIdJWKSet();
        $encryptionKey = $jwkSet->selectKey(JsonWebKeyUsage::Encryption->value);

        if ($encryptionKey === null) {
            throw new RuntimeException('Could not find any BankId JWKs to encrypt content with.');
        }

        $signedContent = $this->signUsingJsonWebSignature($body, $applicationSignatureKey);
        $encryptedContent = $this->encryptUsingJsonWebEncryption($signedContent, $encryptionKey);

        $request = new Request(
            method: 'POST',
            uri: sprintf('%s/ros', $this->baseUri),
            headers: [
                'Content-Type' => 'application/jwe'
            ],
            body: $encryptedContent
        );

        $response = $this->httpClient->sendRequest($request);
        $code = $response->getStatusCode();

        if ($code !== 200) {
            throw new RequestObjectCreationException(sprintf(
                'ROS creation request failed with status %d and message %s',
                $code,
                $response->getBody(),
            ));
        }

        $content = json_decode(
            $response->getBody()->getContents(),
            associative: true
        );

        return RequestObjectCreationResponse::create($content);
    }

    /**
     * @return JWKSet
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getBankIdJWKSet(): JWKSet
    {
        $request = new Request(
            method: 'GET',
            uri: sprintf('%s/.well-known/jwks', $this->baseUri)
        );

        return JWKSet::createFromJson(
            $this->httpClient
                ->sendRequest($request)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc7515
     * @param string $data
     * @param JWK $privateKey
     * @return string
     */
    private function signUsingJsonWebSignature(string $data, JWK $privateKey): string
    {
        $algorithm = new RS256();

        $jwsBuilder = new JWSBuilder(new AlgorithmManager([
            $algorithm
        ]));

        $jws = $jwsBuilder->create()
            ->withPayload($data)
            ->addSignature($privateKey, ['alg' => $algorithm->name()])
            ->build();

        return (new SignatureCompactSerializer())
            ->serialize($jws, signatureIndex: 0);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc7516
     * @see https://web-token.spomky-labs.com/v/v1.x/components/encrypted-tokens-jwe/jwe-creation
     * @param string $data
     * @param JWK $publicKey
     * @return string
     */
    private function encryptUsingJsonWebEncryption(string $data, JWK $publicKey): string
    {
        $keyEncryptionAlgorithm = new RSAOAEP256();
        $contentEncryptionAlgorithm = new A128CBCHS256();

        $jweBuilder = new JWEBuilder(
            keyEncryptionAlgorithmManager: new AlgorithmManager([$keyEncryptionAlgorithm]),
            contentEncryptionAlgorithmManager: new AlgorithmManager([$contentEncryptionAlgorithm]),
            compressionManager: new CompressionMethodManager()
        );

        $jwe = $jweBuilder->create()
            ->withPayload($data)
            ->withSharedProtectedHeader([
                'kid' => $publicKey->get('kid'),
                'enc' => $contentEncryptionAlgorithm->name(),
                'alg' => $keyEncryptionAlgorithm->name(),
            ])
            ->addRecipient($publicKey)
            ->build();

        return (new CompactSerializer())
            ->serialize($jwe);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @return String[]
     */
    public function getAvailableBanks(string $bank): array
    {
        $request = new Request(
            method: 'GET',
            uri: 'https://oidc.bankid.cz/api/v1/banks',
        );

        $response = $this->httpClient->sendRequest($request);

        return json_decode(
            $response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
