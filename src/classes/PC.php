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
}