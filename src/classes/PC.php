<?php
require '../src/classes/Table.php';

class PC extends Table
{
	protected $name = 'PC';
	protected $links = [
		'PC_Video_Outputs' => ['PC', 'Video_Output',],
	];
	protected $except = [
		'PC_Video_Outputs.id', 
		'PC_Video_Outputs.PC_id', 
		'PC_Video_Outputs.Video_Output_id',
		'Video_Output.id',
	];
	protected $merge = [
		'Video_Output.name',
	];

	protected function beforeUpdate($array)
	{
		foreach ($array['PC_Video_Outputs'] as $key => $value) {
			// echo $value;
			$max_id = $this->dbconn->query("SELECT id FROM Video_Output WHERE name = '$value'");
			$max_id = $max_id->fetchAll(PDO::FETCH_NUM)[0][0];

			$array['PC_Video_Outputs'][$key] = $max_id;
			// print_r($max_id);
		}

		return $array;
	}
}