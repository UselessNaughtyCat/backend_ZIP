<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true,
        'debug' => true,
    ],
];

$app = new \Slim\App($config);


$app->get('/', function (Request $request, Response $response){
	echo "начальная страница";
});

// PC routes
require '../src/routes/PC.php';

$app->run();
