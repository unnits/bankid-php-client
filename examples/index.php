<?php

use Dotenv\Dotenv;

require_once '../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$view = match($path) {
    '/' => 'home.php',
    '/get-user-information', '/api/v1/contracts/bank-id' => 'get_profile.php',
    '/sign-document' => 'sign_document.php',
    default => null,
};

if ($view === null) {
    echo '404 not found';
    exit;
}

require_once __DIR__ . '/views/header.php';
require_once sprintf('views/%s', $view);
require_once __DIR__ . '/views/footer.php';
