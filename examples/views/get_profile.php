<?php

declare(strict_types=1);

use Unnits\BankId\Client as BankIdClient;
use GuzzleHttp\Client as GuzzleClient;
use Unnits\BankId\Enums\Scope;

/**
 * MUST match one of the Redirect URIs in BankId's developer portal
 */
$redirectUri = 'http://localhost:8000/api/v1/contracts/bank-id';

$client = new BankIdClient(
    httpClient: new GuzzleClient,
    baseUri: $_ENV['BANK_ID_URI'],
    clientId: $_ENV['DEMO_CLIENT_ID'],
    clientSecret: $_ENV['DEMO_CLIENT_SECRET'],
    redirectUri: $redirectUri
);

$profile = null;
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === parse_url($redirectUri, PHP_URL_PATH)) {
    $code = $_GET['code'] ?? null;
    $state = $_GET['state'] ?? null;

    $token = $client->getToken($code);
    $profile = $client->getProfile($token);

//    $tokenInfo = $client->getTokenInfo($token);
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
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Unnits/BankIdClient - Example of receiving user profile information</title>

    <style>
        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 14px;
            line-height: 165%;
            box-sizing: border-box;
        }

        .login-button {
            background: black;
            border-radius: 8px;
            color: white;
            padding: 9px 12px;
            text-decoration: none;
            display: inline-flex;
        }

        .login-button span {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .login-button-logo {
            width: 70px;
            padding-right: 10px;
            margin-right: 10px;
            border-right: 1px solid white;
        }

        pre {
            background: #eee;
            padding: 20px;
            overflow: scroll;
        }
    </style>
</head>

<body>
<?php if ($profile === null): ?>
    <a class="login-button" href="<?= $link ?>">
        <img
            alt="BankiD logo"
            class="login-button-logo"
            src="https://idp.bankid.cz/resources/vzt9w/login/bankid/img/logo-white.svg"
        >

        <span>Sign In with Bank iD</span>
    </a>
<?php else: ?>
    <a href="/">Back</a>

    <ul>
        <li>Given name: <?= $profile->givenName ?></li>
        <li>Family name : <?= $profile->familyName ?></li>
        <li>Age: <?= $profile->age ?></li>
        <li>Birth place: <?= $profile->birthPlace ?></li>
        <li>...</li>
    </ul>

    <h2>Profile</h2>
    <pre><code><?= print_r($profile, true) ?></code></pre>

    <?php if (isset($token)): ?>
        <h2>Access Token</h2>
        <?= $token->identityToken->structuredScope->documentObject->documentUri ?? '' ?>

        <pre><code><?= print_r($token, true) ?></code></pre>
    <?php endif; ?>

    <?php if (isset($tokenInfo)): ?>
        <h2>Token Info</h2>
        <pre><code><?= print_r($tokenInfo, true) ?></code></pre>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>
