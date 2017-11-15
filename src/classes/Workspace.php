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
	protected $except = [                    
		'Workspace_PCs.id',
		'Workspace_PCs.Workspace_id',
		'Workspace_PCs.PC_id',
		'Workspace_Monitors.id',
		'Workspace_Monitors.Workspace_id',
		'Workspace_Monitors.Monitor_id',
		'Workspace_Headphones.id',
		'Workspace_Headphones.Workspace_id',
		'Workspace_Headphones.Headphone_id',
	];
	protected $merge = [
		'Headphone.id',
		'Headphone.name',
		'Headphone.comment',
	];
}