<?php

class Table
{
	private $dbhost = 'localhost';
	private $dbuser = 'root';
	private $dbpass = '';
	private $dbname = 'ufanet_work';

	protected $table = '';

	function __construct()
	{
		$this->connect();
	}

	public function connect(){
		$mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
		$dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
		$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbConnection;
	}

	public function getTableName()
	{
		return $this->table != '' ? $this->table : 'table not selected';
	}
	
	public function selectAll()
	{

	}

	public function select($id)
	{

	}

	public function delete($id)
	{
		
	}

	public function update($id, $values)
	{

	}

	public function insert($id, $values)
	{

	}
}