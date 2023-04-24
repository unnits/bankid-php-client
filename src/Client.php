<?php

declare(strict_types=1);

namespace Unnits\BankId;

use Exception;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A128CBCHS256;
use Jose\Component\Encryption\Algorithm\KeyEncryption\RSAOAEP256;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\JWEBuilder;
use Jose\Component\Encryption\Serializer\CompactSerializer;
use OpenSSLAsymmetricKey;
use RuntimeException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Unnits\BankId\DTO\AuthToken;
use Unnits\BankId\DTO\JsonWebKey;
use Unnits\BankId\DTO\JsonWebKeySet;
use Unnits\BankId\DTO\Profile;
use Unnits\BankId\DTO\RequestObject;
use Unnits\BankId\DTO\RequestObjectCreationResponse;
use Unnits\BankId\Enums\Scope;
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
     * @param string|null $requestUri
     * @param Scope[] $scopes
     * @return AuthorizationUri
     */
    public function getAuthUri(string $state, ?string $requestUri = null, array $scopes = []): AuthorizationUri
    {
        return new AuthorizationUri(
            baseUri: $this->baseUri,
            clientId: $this->clientId,
            redirectUri: $this->redirectUri,
            state: $state,
            scopes: $scopes,
            requestUri: $requestUri,
        );
    }

    /**
     * @param string $code
     * @return AuthToken
     * @throws ClientExceptionInterface
     * @throws TokenCreationException
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

        $content = json_decode(
            $response->getBody()->getContents(),
            associative: true
        );

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
     * @param OpenSSLAsymmetricKey $applicationSignatureKey
     * @return RequestObjectCreationResponse
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function createRequestObject(
        RequestObject $requestObject,
        OpenSSLAsymmetricKey $applicationSignatureKey
    ): RequestObjectCreationResponse {
        // 1. podepsat JSON vzniklý z $requestObject
        // 2. zašifrovat výsledný JSON klíčem z BankId

        $body = json_encode($requestObject);
        assert(is_string($body));

        $jwkSet = $this->getBankIdJWKSet();
        $encryptionKeys = $jwkSet->getEncryptionKeys();

        if (count($encryptionKeys) === 0) {
            throw new RuntimeException('Could not find any BankId JWKs to encrypt content with.');
        }

        $signedContent = $this->signUsingJsonWebSignature($body, $applicationSignatureKey);
        $encryptedContent = $this->encryptUsingJsonWebEncryption($signedContent, $encryptionKeys[0]);

        $request = new Request(
            method: 'POST',
            uri: sprintf('%s/ros', $this->baseUri),
            headers: [
                'Content-Type' => 'application/jwe'
            ],
            body: $encryptedContent
        );

        $response = $this->httpClient->sendRequest($request);

        $content = json_decode(
            $response->getBody()->getContents(),
            associative: true
        );

        return RequestObjectCreationResponse::create($content);
    }

    /**
     * @return JsonWebKeySet
     * @throws ClientExceptionInterface
     */
    public function getBankIdJWKSet(): JsonWebKeySet
    {
        $request = new Request(
            method: 'GET',
            uri: sprintf('%s/.well-known/jwks', $this->baseUri)
        );

        $response = $this->httpClient->sendRequest($request);

        $content = json_decode(
            $response->getBody()->getContents(),
            associative: true
        );

        return JsonWebKeySet::create($content);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc7515
     * @param string $data
     * @param OpenSSLAsymmetricKey $privateKey
     * @return string
     */
    private function signUsingJsonWebSignature(string $data, OpenSSLAsymmetricKey $privateKey): string
    {
        $header = json_encode([
            'alg' => 'HS256',
        ]);

        assert(is_string($header));

        $b64EncodedHeader = base64_encode($header);
        $b64EncodedData = base64_encode($data);

        $message = sprintf('%s.%s', $b64EncodedHeader, $b64EncodedData);

        $signature = '';

        if (
            !openssl_sign(
                $message,
                $signature,
                $privateKey,
                OPENSSL_ALGO_SHA256,
            )
        ) {
            throw new RuntimeException('Openssl signature failed.');
        };

        return sprintf(
            '%s.%s.%s',
            $b64EncodedHeader,
            $b64EncodedData,
            base64_encode($signature)
        );
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc7516
     * @see https://web-token.spomky-labs.com/v/v1.x/components/encrypted-tokens-jwe/jwe-creation
     * @param string $data
     * @param JsonWebKey $publicKey
     * @return string
     */
    private function encryptUsingJsonWebEncryption(string $data, JsonWebKey $publicKey): string
    {
        $keyEncryptionAlgorithm = new RSAOAEP256();
        $contentEncryptionAlgorithm = new A128CBCHS256();

        $jweBuilder = new JWEBuilder(
            keyEncryptionAlgorithmManager: new AlgorithmManager([$keyEncryptionAlgorithm]),
            contentEncryptionAlgorithmManager: new AlgorithmManager([$contentEncryptionAlgorithm]),
            compressionManager: new CompressionMethodManager()
        );

        $jwk = new JWK([
            'kty' => strtoupper($publicKey->type->value),
            'x5c' => $publicKey->chain,
            'n' => $publicKey->nModulus,
            'e' => $publicKey->publicExponent,
        ]);

        $jwe = $jweBuilder->create()
            ->withPayload($data)
            ->withSharedProtectedHeader([
                'enc' => $contentEncryptionAlgorithm->name(),
                'alg' => $keyEncryptionAlgorithm->name(),
            ])
            ->addRecipient($jwk)
            ->build();

        return (new CompactSerializer())
            ->serialize($jwe);
    }
}
