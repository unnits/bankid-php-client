<?php

declare(strict_types=1);

use Unnits\BankId\Client as BankIdClient;

$guzzle = new GuzzleClient();

$baseUri = 'https://oidc.sandbox.bankid.cz';
$clientId = 'dc371349-b63e-4f6e-ba57-2854e0106b51';
$clientSecret = 'AIFVOH5LYcJplupUT1EJSw4zwVuSAvQql4TnBwMn7Bh3mQN62UBcimdBLTkT_p2ET8lw0rAjgCajzLON_kVVUuU';

$client = new BankIdClient(
    client: $guzzle,
    baseUri: $baseUri,
    clientId: $clientId,
    clientSecret: $clientSecret,
);

$client->getProfile();