<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/{className}[/[{id}]]', function (Request $request, Response $response) {
    $className = $request->getAttribute('className');
    $id 	   = $request->getAttribute('id') ? $request->getAttribute('id') : null;

    require '../src/classes/'.$className.'.php';

    $currentTable = new $className();
    $array = $id != null ? $currentTable->select($id) : $currentTable->selectAll();
    
    $json = json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    print_r($json);
});

$app->post('/{className}/add', function (Request $request, Response $response) {
	$className = $request->getAttribute('className');

	require '../src/classes/'.$className.'.php';

	$currentTable = new $className();
	$currentTable->insert($request->getParsedBody());
});

$app->put('/{className}/update/{id}', function (Request $request, Response $response) {
	$className = $request->getAttribute('className');
	$id 	   = $request->getAttribute('id');

	require '../src/classes/'.$className.'.php';

	$currentTable = new $className();
	$currentTable->update($id, $request->getParsedBody());
});

$app->delete('/{className}/delete/{id}', function (Request $request, Response $response) {
	$className = $request->getAttribute('className');
	$id 	   = $request->getAttribute('id');

	require '../src/classes/'.$className.'.php';

	$currentTable = new $className();
	$currentTable->delete($id);
});