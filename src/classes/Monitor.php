<?php
require '../src/classes/Table.php';

class Monitor extends Table
{
	protected $name = 'Monitor';
	protected $links = [
		'Monitor_Video_Outputs' => ['Monitor', 'Video_Output',],
	];
	protected $except = [
		'Monitor_Video_Outputs.id', 
		'Monitor_Video_Outputs.Monitor_id', 
		'Monitor_Video_Outputs.Video_Output_id',
		'Video_Output.id',
	];
	protected $merge = [
		'Video_Output.name',
	];
}