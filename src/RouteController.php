<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/{className}[/[{id}]]', function (Request $request, Response $response) {
    $className = $request->getAttribute('className');
    $id 	   = $request->getAttribute('id') ? $request->getAttribute('id') : null;

    require '../src/classes/'.$className.'.php';

    $currentTable = new $className();
    $array = $id != null ? $currentTable->select($id) : $currentTable->selectAll();
    
    echo "<pre>";
	print_r($array);
	print_r($currentTable->Translate($array));
    print_r(json_encode($currentTable->Translate($array), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo "</pre>";
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