<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Utils;
use Unnits\BankId\Client as BankIdClient;
use GuzzleHttp\Client as GuzzleClient;
use Unnits\BankId\DTO\DocumentObject;
use Unnits\BankId\DTO\RequestObject;
use Unnits\BankId\DTO\SignArea;
use Unnits\BankId\DTO\StructuredScope;
use Unnits\BankId\Enums\AcrValue;
use Unnits\BankId\Enums\ResponseType;
use Unnits\BankId\Enums\Scope;

$redirectUri = 'http://localhost/api/v1/bankid/callback';

$httpClient = new GuzzleClient();

$client = new BankIdClient(
    httpClient: $httpClient,
    baseUri: $_ENV['BANK_ID_URI'],
    clientId: $_ENV['DEMO_CLIENT_ID'],
    clientSecret: $_ENV['DEMO_CLIENT_SECRET'],
    redirectUri: $redirectUri,
);

$profile = null;
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$keyPath = __DIR__ . '/../storage/private-key.pem';
$keyContent = file_get_contents($keyPath);

assert(is_string($keyContent));

$privateKey = openssl_pkey_get_private($keyContent);

assert($privateKey instanceof OpenSSLAsymmetricKey);

$documentUri = $_ENV['DEMO_DOCUMENT_URI'];

$tmpFile = tempnam('/tmp', '');
assert(is_string($tmpFile));

$response = $httpClient->get($documentUri, [
    'sink' => $tmpFile
]);

$documentSize = filesize($tmpFile);
$documentHash = hash_file('sha512', $tmpFile);

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
            documentHash: $documentHash,
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
            documentUri: $documentUri
        )
    ),
    txn: '9123203d-f2e3-4dd9-a11d-eb486ff353f9',
    state: 'H4YMSE8zcLS-xXLR7ZZ-toUcrYGeMLBLB9BbCtAo-2o',
    nonce: '1jZ2YXn0kAa0Jfnn7Fig8Gz-wc_GBaQgDVzYtdXTkmI',
    clientId: 'd0cba9e6-ffff-487c-ffff-56395480c342',
);

$response = $client->createRequestObject($ros, $privateKey);

dd($response);

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Unnits/BankIdClient</title>
</head>

<body>
    Podpis dokumentů
</body>
</html>
