<?php

declare(strict_types=1);

use Unnits\BankId\Client as BankIdClient;
use GuzzleHttp\Client as GuzzleClient;
use Unnits\BankId\Enums\Scope;
use Unnits\BankId\LogoutUri;

/**
 * MUST match one of the Redirect URIs in BankId's developer portal
 */
$redirectUri = 'http://localhost:8000/api/v1/contracts/bank-id';

$logoutRedirectUri = 'http://localhost:8000/logout';

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

    $tokenInfo = $client->getTokenInfo($token, useClientCredentials: true);

    $documentUri = $token->identityToken->structuredScope->documentObject->documentUri ?? null;

    $logoutUri = (string)$client->getLogoutUri($token->identityToken->rawValue ?? '', $logoutRedirectUri, '12345');

    $userInfo = $client->getUserInfo($token);
}

$state = '1234';

$link = (string)$client->getAuthUri($state, scopes: [
    Scope::OpenId,
    Scope::BirthDate,
    Scope::Verification,
    Scope::Name,
]);

?>

<div class="get-profile">
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
        <ul>
            <li><a href="/">Back</a></li>


            <li>
                <form
                    method="post"
                    action="<?= $logoutUri ?>"
                >
                    <button class="logout-button" type="submit">Logout</button>
                </form>
            </li>

            <?php if (!empty($documentUri)): ?>
                <li><a href="<?= $documentUri ?>">Download signed document</a></li>
            <?php endif; ?>
        </ul>

        <ul>
            <li>Given name: <?= $profile->givenName ?></li>
            <li>Family name : <?= $profile->familyName ?></li>
            <li>Age: <?= $profile->age ?></li>
            <li>Birth place: <?= $profile->birthPlace ?></li>
            <li>...</li>
        </ul>

        <h2>Profile</h2>
        <pre><code><?= print_r($profile, true) ?></code></pre>

        <?php if (isset($userInfo)): ?>
            <h2>User info</h2>

            <pre><code><?= print_r($userInfo, true) ?></code></pre>
        <?php endif; ?>

        <?php if (isset($token)): ?>
            <h2>Access Token</h2>

            <pre><code><?= print_r($token, true) ?></code></pre>
        <?php endif; ?>

        <?php if (isset($tokenInfo)): ?>
            <h2>Token Info</h2>
            <pre><code><?= print_r($tokenInfo, true) ?></code></pre>
        <?php endif; ?>
    <?php endif; ?>
</div>
