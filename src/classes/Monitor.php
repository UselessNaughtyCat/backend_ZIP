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
}