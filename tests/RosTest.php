<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Mockery;
use OpenSSLAsymmetricKey;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Unnits\BankId\Client;
use Unnits\BankId\DTO\DocumentObject;
use Unnits\BankId\DTO\JsonWebKey;
use Unnits\BankId\DTO\RequestObject;
use Unnits\BankId\DTO\SignArea;
use Unnits\BankId\DTO\StructuredScope;
use Unnits\BankId\Enums\AcrValue;
use Unnits\BankId\Enums\JsonWebKeyType;
use Unnits\BankId\Enums\JsonWebKeyUsage;
use Unnits\BankId\Enums\ResponseType;
use Unnits\BankId\Enums\Scope;

/**
 * @see https://developer.bankid.cz/docs/api/bankid-for-sep#operations-tag-Sign
 * @see https://developer.bankid.cz/docs/apis_sep#api-for-sign
 */
class RosTest extends TestCase
{
    /**
     * @throws ClientExceptionInterface
     */
    public function test_basic_logic_works(): void
    {
        $documentSize = 68000;

        $ros = new RequestObject(
            maxAge: 3600,
            bankId: '29a0cec1-8e0f-4a1f-b0ce-92264b9922e8',
            acrValues: AcrValue::LOA3->value,
            scopes: [
                Scope::OpenId,
                Scope::Email,
                Scope::BirthDate,
                Scope::Name
            ],
            responseType: ResponseType::Code,
            structuredScope: new StructuredScope(
                documentObject: new DocumentObject(
                    documentTitle: 'Document for signature',
                    documentSize: $documentSize,
                    documentSubject: 'New contract',
                    documentLanguage: 'cs.CZ',
                    documentId: 'loremipsum',
                    documentAuthor: 'John Doe',
                    documentHash: '123456',
                    documentReadByEndUser: true,
                    hashAlgorithm: '2.16.840.1.101.3.4.2.3',
                    documentCreatedAt: new DateTime(),
                    signArea: new SignArea(
                        x: 320,
                        y: 400,
                        width: 140,
                        height: 40,
                        page: 0,
                    ),
                    documentUri: 'https://bankid.unnits.com/documents/63b24705-92c5-464c-963d-188c27763fe3'
                )
            ),
            txn: '9123203d-f2e3-4dd9-a11d-eb486ff353f9',
            state: 'H4YMSE8zcLS-xXLR7ZZ-toUcrYGeMLBLB9BbCtAo-2o',
            nonce: '1jZ2YXn0kAa0Jfnn7Fig8Gz-wc_GBaQgDVzYtdXTkmI',
            clientId: 'd0cba9e6-ffff-487c-ffff-56395480c342',
        );

        //
        $httpClient = Mockery::mock(ClientInterface::class);

        $httpClient->shouldReceive('sendRequest');


        $bankIdClient = new Client(
            httpClient: $httpClient,
            baseUri: 'https://oidc.sandbox.bankid.cz',
            clientId: 'foo',
            clientSecret: 'bar',
            redirectUri: 'http://localhost/callback'
        );

        //

        $appKey = openssl_pkey_new();
        assert($appKey instanceof OpenSSLAsymmetricKey);

        $appKeyDetails = openssl_pkey_get_details($appKey);
        assert(is_array($appKeyDetails));

        //

        $bankIdKey = openssl_pkey_new();
        assert($bankIdKey instanceof OpenSSLAsymmetricKey);

        $bankIdKeyDetails = openssl_pkey_get_details($bankIdKey);
        assert(is_array($bankIdKeyDetails));

        $response = $bankIdClient->createRequestObject($ros, new JsonWebKey(
            type: JsonWebKeyType::RSA,
            usage: JsonWebKeyUsage::Signature,
            chain: [$appKeyDetails['key']]
        ));
    }
}
