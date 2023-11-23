<?php

declare(strict_types=1);

use Unnits\BankId\Client as BankIdClient;
use GuzzleHttp\Client as GuzzleClient;

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

$configuration = $client->getOpenIdConnectConfiguration();
?>

<div class="get-oidc-configuration">
    <pre><code><?= print_r($configuration, true) ?></code></pre>
</div>
