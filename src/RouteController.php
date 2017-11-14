<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/{className}/{id}', function (Request $request, Response $response) {
    $className = $request->getAttribute('className');
    $id 	   = $request->getAttribute('id');

    require '../src/classes/'.$className.'.php';

    $currentTable = new $className();
    $array = $currentTable->select($id);
    
    echo "<pre>";
    print_r($array);
    echo "</pre>";
});

// $app->post('/{className}/add', function (Request $request, Response $response) {
//     $className = $request->getAttribute('className');
//     require '../src/classes/'.$className.'.php';
//     $pendos = new $className();
//     $pendos->Connect();
//     echo $pendos->GetSomeString();
// });