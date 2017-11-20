<?php
require '../src/classes/Table.php';

class Workspace extends Table
{
	protected $name = 'Workspace';
	protected $links = [
		'Workspace_PCs' => ['Workspace', 'PC',],
		'Workspace_Monitors' => ['Workspace', 'Monitor',],
		'Workspace_Headphones' => ['Workspace', 'Headphone',],
	];
}