<?php

declare(strict_types=1);

namespace Unnits\BankId;

use Exception;
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
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Unnits\BankId\DTO\AuthToken;
use Unnits\BankId\DTO\Bank;
use Unnits\BankId\DTO\IdentityToken;
use Unnits\BankId\DTO\Profile;
use Unnits\BankId\DTO\RequestObject;
use Unnits\BankId\DTO\RequestObjectCreationResponse;
use Unnits\BankId\DTO\TokenInfo;
use Unnits\BankId\DTO\UserInfo;
use Unnits\BankId\Enums\ClientAssertionType;
use Unnits\BankId\Enums\JsonWebKeyUsage;
use Unnits\BankId\Enums\Scope;
use Unnits\BankId\Enums\TokenType;
use Unnits\BankId\Exceptions\BankIdException;
use Unnits\BankId\Exceptions\GetProfileException;
use Unnits\BankId\Exceptions\GetUserInfoException;
use Unnits\BankId\Exceptions\LogoutException;
use Unnits\BankId\Exceptions\RequestObjectCreationException;
use Unnits\BankId\Exceptions\TokenCreationException;
use Unnits\BankId\Exceptions\TokenInfoException;
use Unnits\BankId\Http\BankIdResponse;
use Unnits\BankId\OIDC\Configuration;

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
     * @param string|null $nonce
     * @return AuthorizationUri
     */
    public function getAuthUri(
        string $state,
        ?string $bankId = null,
        ?string $requestUri = null,
        array $scopes = [Scope::OpenId],
        ?string $nonce = null,
    ): AuthorizationUri {
        return new AuthorizationUri(
            baseUri: $this->baseUri,
            clientId: $this->clientId,
            redirectUri: $this->redirectUri,
            state: $state,
            bankId: $bankId,
            scopes: $scopes,
            requestUri: $requestUri,
            nonce: $nonce,
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
        try {
            $response = $this->sendRequest(new Request(
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
            ));

            return AuthToken::create($response->getBody()->data);
        } catch (BankIdException $e) {
            throw TokenCreationException::create($e);
        }
    }

    /**
     * @param AuthToken $token
     * @return Profile
     * @throws ClientExceptionInterface
     * @throws GetProfileException
     */
    public function getProfile(AuthToken $token): Profile
    {
        try {
            $response = $this->sendRequest(new Request(
                method: 'GET',
                uri: sprintf('%s/profile', $this->baseUri),
                headers: [
                    'Authorization' => sprintf('Bearer %s', $token->value)
                ]
            ));

            return Profile::create($response->getBody()->data);
        } catch (BankIdException $e) {
            throw GetProfileException::create($e);
        }
    }

    /**
     * @param RequestObject $requestObject
     * @param JWK $applicationSignatureKey
     * @return RequestObjectCreationResponse
     * @throws ClientExceptionInterface
     * @throws RequestObjectCreationException
     * @throws JsonException
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

        try {
            $response = $this->sendRequest(new Request(
                method: 'POST',
                uri: sprintf('%s/ros', $this->baseUri),
                headers: [
                    'Content-Type' => 'application/jwe'
                ],
                body: $encryptedContent
            ));

            return RequestObjectCreationResponse::create(
                $response->getBody()->data,
                $response->getTraceId(),
            );
        } catch (BankIdException $e) {
            throw RequestObjectCreationException::create($e);
        }
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
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @return Bank[]
     */
    public function getAvailableBanks(): array
    {
        $request = new Request(
            method: 'GET',
            uri: 'https://oidc.bankid.cz/api/v1/banks',
        );

        $response = $this->httpClient->sendRequest($request);

        $banks = json_decode(
            $response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return array_map(
            fn (array $bank) => Bank::create($bank),
            $banks['items'] ?? []
        );
    }

    /**
     * @throws TokenInfoException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function getTokenInfo(
        AuthToken|string $token,
        ?TokenType $tokenTypeHint = null,
        ?string $clientAssertion = null,
        ?ClientAssertionType $clientAssertionType = null,
        bool $useClientCredentials = false,
    ): TokenInfo {
        $tokenValue = is_string($token)
            ? $token
            : $token->value;

        $body = [
            'token' => $tokenValue,
        ];

        if ($tokenTypeHint !== null) {
            $body['token_type_hint'] = $tokenTypeHint->value;
        }

        if ($useClientCredentials) {
            $body['client_id'] = $this->clientId;
            $body['client_secret'] = $this->clientSecret;
        } else {
            if ($clientAssertion !== null) {
                $body['client_assertion'] = $clientAssertion;
            }

            if ($clientAssertionType !== null) {
                $body['client_assertion_type'] = $clientAssertionType->value;
            }
        }

        try {
            $response = $this->sendRequest(new Request(
                method: 'POST',
                uri: sprintf('%s/token-info', $this->baseUri),
                headers: [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                body: http_build_query($body)
            ));

            return TokenInfo::create($response->getBody()->data);
        } catch (BankIdException $e) {
            throw TokenInfoException::create($e);
        }
    }

    /**
     * @param string|IdentityToken $idTokenHint
     * @param string|null $redirectUri
     * @param string|null $state
     * @return void
     * @throws ClientExceptionInterface
     * @throws LogoutException
     */
    public function logout(string|IdentityToken $idTokenHint, ?string $redirectUri = null, ?string $state = null): void
    {
        $uri = $this->getLogoutUri($idTokenHint, $redirectUri, $state);

        try {
            $this->sendRequest(new Request(
                method: 'POST',
                uri: (string)$uri
            ));
        } catch (BankIdException $e) {
            throw LogoutException::create($e);
        }
    }

    public function getLogoutUri(
        string|IdentityToken $idTokenHint,
        ?string $redirectUri = null,
        ?string $state = null
    ): LogoutUri {
        return new LogoutUri(
            $this->baseUri,
            $idTokenHint,
            $redirectUri,
            $state
        );
    }

    /**
     * @param AuthToken $token
     * @return UserInfo
     * @throws ClientExceptionInterface
     * @throws GetUserInfoException
     */
    public function getUserInfo(AuthToken $token): UserInfo
    {
        try {
            $response = $this->sendRequest(new Request(
                method: 'GET',
                uri: sprintf('%s/userinfo', $this->baseUri),
                headers: [
                    'Authorization' => sprintf('Bearer %s', $token->value)
                ]
            ));

            return UserInfo::create($response->getBody()->data);
        } catch (BankIdException $e) {
            throw GetUserInfoException::create($e);
        }
    }

    /**
     * @return Configuration
     * @throws ClientExceptionInterface
     * @throws LogoutException
     */
    public function getOpenIdConnectConfiguration(): Configuration
    {
        $request = new Request(
            method: 'GET',
            uri: sprintf('%s/.well-known/openid-configuration', $this->baseUri)
        );

        $response = $this->httpClient->sendRequest($request);

        $content = Utils::jsonDecode(
            $response->getBody()->getContents(),
            assoc: true
        );

        assert(is_array($content));

        if ($response->getStatusCode() !== 200) {
            throw new LogoutException(sprintf(
                'Failed logging user out: (%d %s) %s. Trace id: %s',
                $response->getStatusCode(),
                $content['error'],
                $content['error_description'],
                $response->getHeaderLine('traceId')
            ));
        }

        return Configuration::create($content);
    }

    public function getClientId(): string
    {
        return $this->clientId;
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

        // contentEncryptionAlgorithmManager has been deprecated
        // as of web-token/jwt-library:3.3.0
        // @see https://github.com/web-token/jwt-framework/blob/3.3.x/src/Library/Encryption/JWEBuilder.php#L58
        $jweBuilder = new JWEBuilder(
            new AlgorithmManager([
                $keyEncryptionAlgorithm,
                $contentEncryptionAlgorithm
            ]),
            contentEncryptionAlgorithmManager: null,
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
     * @param RequestInterface $request
     * @return BankIdResponse
     * @throws BankIdException
     * @throws ClientExceptionInterface
     */
    private function sendRequest(RequestInterface $request): BankIdResponse
    {
        $response = new BankIdResponse(
            $this->httpClient->sendRequest($request)
        );

        $code = $response->originalResponse->getStatusCode();
        $body = $response->getBody();

        if ($code !== 200) {
            throw new BankIdException(
                traceId: $response->getTraceId(),
                statusCode: $code,
                error: $body->stringOrNull('error'),
                description: $body->stringOrNull('error_description'),
            );
        }

        return $response;
    }
}
