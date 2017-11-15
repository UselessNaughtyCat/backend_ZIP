<link rel="stylesheet" type="text/css" href="../../../src/style/style.css">
<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


// get and show PC info
$app->get('/api/PC/{id}', function (Request $request, Response $response){
	$id = $request->getAttribute('id');

	if (intval($id)){
		$sql = "SELECT * FROM PC WHERE id = $id";

		try {
			$db = new db();
			$db = $db->connect();
			$smtp = $db->query($sql);
			$PC = $smtp->fetchAll(PDO::FETCH_ASSOC)[0];
			$db = null;

			$HDD = $PC['HDD'];
			$RAM = $PC['RAM'];
			$processor = $PC['processor'];

			echo "<div class='container'>","<form action='http://localhost/public/api/PC/add' method='post'>";
			echo "<input value='$HDD' name='HDD'>","<br>";
			echo "<input value='$RAM' name='RAM'>","<br>";
			echo "<input value='$processor' name='processor'>", "<br>";
			echo "<input type='submit'>";
			echo "</form>";

			echo "<form action='http://localhost/public/api/PC/delete/$id' method='delete'>";
			echo "<input type='hidden' name='_METHOD' value='DELETE'/>";
			echo "<input type='submit' value='delete'>";
			echo "</div>";

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
});

$app->post('/api/PC/add', function (Request $request, Response $response){
	$HDD = $request->getParam('HDD');
	$RAM = $request->getParam('RAM');
	$processor = $request->getParam('processor');

	$sql = "INSERT INTO PC (HDD, RAM, processor) VALUES (:HDD, :RAM, :processor)";

	try {
		$db = new db();
		$db = $db->connect();
		$smtp = $db->prepare($sql);

		$smtp->bindParam(':HDD', $HDD);
		$smtp->bindParam(':RAM', $RAM);
		$smtp->bindParam(':processor', $processor);

		$smtp->execute();

		echo "PC added";

	} catch (Exception $e) {
		echo $e->getMessage();
	}


	// $result = array(
	// 	'HDD' => '50 гб',
	// 	'RAM' => '1 гб',
	// 	'processor' => '1.0 ггц'
	// );
	
	// return $response->withJson(json_encode($result), 200);

});

$app->delete('/api/PC/delete/{id}', function (Request $request, Response $response){
	$id = $request->getAttribute('id');

	$sql = "DELETE FROM PC WHERE id = :id";

	try {
		$db = new db();
		$db = $db->connect();

		$smtp = $db->prepare($sql);
		$smtp->bindParam(':id', $id);
		$smtp->execute();

		echo "PC deleted";

	} catch (Exception $e) {
		echo $e->getMessage();
	}
});

?>