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
		// 'PC.id',
		// 'Monitor.id',
		// 'Headphone.id',
	];
	protected $merge = [
		'Headphone.id',
		'Headphone.name',
		'Headphone.comment',
		'PC.id',
		'PC.HDD',
		'PC.RAM',
		'PC.processor',
		'PC.domain_name',
		'PC.comment',
		'PC.MAC',
		'Monitor.id',
		'Monitor.diagonal',
		'Monitor.name',
		'Monitor.comment',
	];
}