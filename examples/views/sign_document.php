<?php

declare(strict_types=1);

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;
use Jose\Component\KeyManagement\JWKFactory;
use Unnits\BankId\Client as BankIdClient;
use GuzzleHttp\Client as GuzzleClient;
use Unnits\BankId\DTO\DocumentObject;
use Unnits\BankId\DTO\RequestObject;
use Unnits\BankId\DTO\SignArea;
use Unnits\BankId\DTO\StructuredScope;
use Unnits\BankId\Enums\AcrValue;
use Unnits\BankId\Enums\ResponseType;
use Unnits\BankId\Enums\Scope;

/**
 * Must match one of the Redirect URIs in BankId's developer portal
 */
$redirectUri = 'http://localhost:8000/api/v1/contracts/bank-id';

$httpClient = new GuzzleClient();

$client = new BankIdClient(
    httpClient: $httpClient,
    baseUri: $_ENV['BANK_ID_URI'],
    clientId: $_ENV['DEMO_CLIENT_ID'],
    clientSecret: $_ENV['DEMO_CLIENT_SECRET'],
    redirectUri: $redirectUri,
);

// Move your demo .pdf file into the documentPath
$documentPath = __DIR__ . '/../storage/template.pdf';

$documentSize = filesize($documentPath);

assert(is_int($documentSize));

$documentHash = hash_file('sha512', $documentPath);
assert(is_string($documentHash));

// 1. first we create new request object providing document's metadata

// Must match the .pdf's creation date (in UTC)
// (not the file itself, but created_at metadata in the pdf file)
$documentCreatedAt = new DateTime($_ENV['DEMO_DOCUMENT_CREATED_AT']);

$ros = new RequestObject(
    maxAge: 3600,
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
            documentSize: $documentSize,
            documentLanguage: 'cs.CZ',
            documentId: 'loremipsum',
            documentHash: $documentHash,
            documentReadByEndUser: true,
            hashAlgorithm: '2.16.840.1.101.3.4.2.3',
            documentCreatedAt: $documentCreatedAt,
            signArea: new SignArea(
                x: 320,
                y: 400,
                width: 140,
                height: 40,
                page: 0,
            )
        ),
    ),
    txn: '9123203d-f2e3-4dd9-a11d-eb486ff353f9',
    state: 'H4YMSE8zcLS-xXLR7ZZ-toUcrYGeMLBLB9BbCtAo-2o',
    nonce: '1jZ2YXn0kAa0Jfnn7Fig8Gz-wc_GBaQgDVzYtdXTkmI',
    clientId: $_ENV['DEMO_CLIENT_ID'],
);


$keyPath = __DIR__ . '/../storage/private-key.pem';
$privateKey = JWKFactory::createFromKeyFile($keyPath);

$response = $client->createRequestObject($ros, $privateKey);

$traceId = $response->getTraceId();
$requestUri = $response->requestUri;

$uploadUri = $response->uploadUri;
assert(is_string($uploadUri));

$authUri = null;

// 2. now we upload the file itself
try {
    $httpClient->request(
        method: 'POST',
        uri: $uploadUri,
        options: [
            RequestOptions::MULTIPART => [
                ['name' => 'file', 'contents' => Utils::tryFopen($documentPath, 'r')]
            ],
        ]
    );

    $authUri = $client->getAuthUri(
        state: '1234',
        requestUri: $requestUri,
        scopes: [Scope::OpenId]
    );
} catch (GuzzleException $e) {
    //
}

?>

<div class="document-signature">
    <?php if ($authUri === null): ?>
        <p><?= $e->getMessage() ?></p>
    <?php else: ?>
        <p>Trace ID: <?= $traceId ?? 'null' ?></p>

        <a class="login-button" href="<?= $authUri ?>">
            <img
                alt="BankiD logo"
                class="login-button-logo"
                src="https://idp.bankid.cz/resources/vzt9w/login/bankid/img/logo-white.svg"
            >

            <span>Digitally sign document via Bank iD</span>
        </a>
    <?php endif; ?>
</div>
