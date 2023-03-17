<?php

declare(strict_types=1);

namespace Unnits\BankId;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Unnits\BankId\DTO\AuthToken;
use Unnits\BankId\DTO\Profile;
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

    public function getAuthUri(string $state): AuthorizationUri
    {
        return new AuthorizationUri(
            baseUri: $this->baseUri,
            clientId: $this->clientId,
            redirectUri: $this->redirectUri,
            state: $state,
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
}
