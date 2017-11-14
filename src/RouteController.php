<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/{className}', function (Request $request, Response $response) {
    $className = $request->getAttribute('className');
    require '../src/classes/'.$className.'.php';
    $pendos = new $className();
    $pendos->Connect();
    echo $pendos->getTableName();
});

// $app->post('/{className}/add', function (Request $request, Response $response) {
//     $className = $request->getAttribute('className');
//     require '../src/classes/'.$className.'.php';
//     $pendos = new $className();
//     $pendos->Connect();
//     echo $pendos->GetSomeString();
// });