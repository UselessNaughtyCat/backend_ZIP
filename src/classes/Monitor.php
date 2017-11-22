<?php
require '../src/classes/Table.php';

class Monitor extends Table
{
	protected $name = 'Monitor';
	protected $links = [
		'Monitor_Video_Outputs' => ['Monitor', 'Video_Output',],
	];
	protected $except = [
		'Video_Output.id',
	];
	
	protected function beforeUpdate($array)
	{
		foreach ($array['Monitor_Video_Outputs'] as $key => $value) {
			$max_id = $this->dbconn->query("SELECT id FROM Video_Output WHERE name = '$value'");
			$max_id = $max_id->fetchAll(PDO::FETCH_NUM)[0][0];
			$array['Monitor_Video_Outputs'][$key] = $max_id;
		}
		return $array;
	}
}