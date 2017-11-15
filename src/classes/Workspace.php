<?php
require '../src/classes/Table.php';

class Workspace extends Table
{
	protected $name = 'Workspace';
	protected $links = [
		'Workspace_PCs' => ['PC', 'Workspace'],
		'Workspace_Monitors' => ['Monitor', 'Workspace'],
		'Workspace_Headphones' => ['Headphone', 'Workspace'],
	];
	protected $except = [
	];
}