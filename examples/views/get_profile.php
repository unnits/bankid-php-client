<?php

declare(strict_types=1);

use Unnits\BankId\Client as BankIdClient;
use GuzzleHttp\Client as GuzzleClient;
use Unnits\BankId\Enums\Scope;

/**
 * Must match one of the Redirect URIs in BankId's developer portal
 */
$redirectUri = 'http://localhost:8000/api/v1/contracts/contract-fields';

$client = new BankIdClient(
    httpClient: new GuzzleClient,
    baseUri: $_ENV['BANK_ID_URI'],
    clientId: $_ENV['CLIENT_ID'],
    clientSecret: $_ENV['CLIENT_SECRET'],
    redirectUri: $redirectUri
);

$profile = null;
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === parse_url($redirectUri, PHP_URL_PATH)) {
    $code = $_GET['code'] ?? null;
    $state = $_GET['state'] ?? null;

    $token = $client->getToken($code);
    $profile = $client->getProfile($token);
}

$state = '1234';

$link = (string)$client->getAuthUri($state, scopes: [
    Scope::OpenId,
    Scope::BirthDate,
    Scope::Verification,
    Scope::Name,
]);

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Unnits/BankIdClient - Ukázka získávání údajů o uživateli</title>
</head>

<body>
<?php if ($profile === null): ?>
    <a href="<?= $link ?>">Předvyplnit pomocí BankId</a>
<?php else: ?>
    <ul>
        <li>Jméno: <?= $profile->givenName ?></li>
        <li>Příjmení: <?= $profile->familyName ?></li>
        <li>Věk: <?= $profile->age ?></li>
        <li>Místo narození: <?= $profile->birthPlace ?></li>
        <li>...</li>
    </ul>
<?php endif; ?>
</body>
</html>
