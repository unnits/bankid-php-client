<?php

use Unnits\BankId\Client as BankIdClient;
use GuzzleHttp\Client as GuzzleClient;

require_once '../vendor/autoload.php';

$baseUri = 'https://oidc.sandbox.bankid.cz';
$redirectUri = 'http://localhost/api/v1/bankid/callback';

$clientId = '*****';
$clientSecret = '*****';

$client = new BankIdClient(
    httpClient: new GuzzleClient,
    baseUri: $baseUri,
    clientId: $clientId,
    clientSecret: $clientSecret,
    redirectUri: $redirectUri,
);

//

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

$profile = null;

if ($path === parse_url($redirectUri, PHP_URL_PATH)) {
    $code = $_GET['code'] ?? null;
    $state = $_GET['state'] ?? null;

    $token = $client->getToken($code);
    $profile = $client->getProfile($token);
}

//

$state = '1234';
$link = (string)$client->getAuthUri($state);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BankId example</title>
</head>

<body>
    <?php if ($profile === null): ?>
        <a href="<?= $link ?>">
            Předvyplnit pomocí BankId
            (<?= $link ?>)
        </a>
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
