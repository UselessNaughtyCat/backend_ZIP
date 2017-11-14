<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true,
        'debug' => true,
    ],
];
$app = new \Slim\App($config);

require '../src/RouteController.php';

$app->run();