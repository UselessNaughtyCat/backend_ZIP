<?php

class Table
{
	private $dbhost = 'localhost';
	private $dbuser = 'root';
	private $dbpass = '';
	private $dbname = 'ufanet_work';

	protected $name = '';
	protected $links = [];
	protected $except = [];
	protected $dbconn = null;

	function __construct()
	{
		$this->connect();
	}

	public function connect(){
		$mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
		$dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
		$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->dbconn = $dbConnection;
	}

	public function getTableName()
	{
		return $this->name != '' ? $this->name : 'table not selected';
	}
	
	public function selectAll()
	{

	}

	public function select($id)
	{
		if (count($this->links) > 0)
		{
			$array = [];
			foreach ($this->links as $key => $links) {
				require '../src/classes/SQL_Query_Linked_Tables.php';
				$sql = str_replace("{db_name}", $this->dbname, $sql);
				$sql .= " AND `TABLE_NAME` = '$key'";
				$result = $this->dbconn->query($sql);
				$linkedtables = $result->fetchAll(PDO::FETCH_ASSOC);
				$tmpmain = [];
	
				$tables = [];			
				$tables[] = $key;
				foreach ($links as $v) {
					$tables[] = $v;
				}
				$variables = '';
				foreach ($tables as $tablekey => $table) {
					$result = $this->dbconn->query("DESCRIBE $table");
					$arr = $result->fetchAll(PDO::FETCH_ASSOC);
					foreach ($arr as $arrkey => $tmparr) {
						foreach ($tmparr as $tmparrkey => $value) {
							if ($tmparrkey == 'Field'){
								$variables .= "$table.$value AS '$table.$value', ";
							}
						}
					}
				}
				$variables = rtrim($variables, ', ');

				$tablejoin = $key.' ';
				foreach ($links as $value) {
					$tablejoin .= 'INNER JOIN '.$value.' ';
				}

				$tablelink = '';
				foreach ($linkedtables as $k => $value) {
					$tablelink .= $value['TABLE_NAME'].'.'.$value['COLUMN_NAME'].' = '.$value['REFERENCED_TABLE_NAME'].'.'.$value['REFERENCED_COLUMN_NAME'];
					if ($k != (count($linkedtables) - 1)){
						$tablelink .= ' AND ';
					}
				}

				$sql = "SELECT $variables FROM $tablejoin ON $tablelink";
				$result = $this->dbconn->query($sql);
				$tmpmain = $result->fetchAll(PDO::FETCH_ASSOC);

				// $tmpmain = $this->removeExcept($tmpmain);

				// $array[0] = $this->mergeArray($array[0], $array[1]);
				// unset($array[1]);
				$array[] = $tmpmain;
			}
			return $array;
		}
		else
		{			
			$variables = '';
			$result = $this->dbconn->query("DESCRIBE $this->name");
			$arr = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($arr as $arrkey => $tmparr) {
				foreach ($tmparr as $tmparraykey => $value) {
					if ($tmparraykey == 'Field'){
						$variables .= "$this->name.$value AS '$this->name.$value', ";
					}
				}
			}
			$variables = rtrim($variables, ', ');

			$sql = "SELECT $variables FROM $this->name WHERE id = $id";
			$result = $this->dbconn->query($sql);
			$array = $result->fetchAll(PDO::FETCH_ASSOC);

			// $array = $this->removeExcept($array);

			return $array;
		}
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

	private function removeExcept($array)
	{
		foreach ($array as $arrkey => $tmparr) {
			foreach ($tmparr as $tmparrkey => $val) {
				foreach ($this->except as $exckey => $excval) {
					if ($tmparrkey == $excval){
						unset($array[$arrkey][$tmparrkey]);
					}
				}
			}
		}

		return $array;
	}

	private function arrayDifferenses($array1, $array2)
	{		
		$count = 0;
		if (count($array1) == count($array2)){
			foreach ($array1 as $key => $value) {
				if ($array1[$key] != $array2[$key]){
					$count += 1;
				}
			}
		}
		return $count;
	}

	private function arrayMerge($array1, $array2)
	{
		$merged = [];
		if (count($array1) == count($array2)){
			foreach ($array1 as $key => $value) {
				if ($array1[$key] == $array2[$key]){
					$merged[$key] = $array1[$key];
				}
				else{
					$merged[$key] = [$array1[$key], $array2[$key]];
				}
			}
		}
		return $merged;
	}
}

function array_keys_to_values($array)
{
	$new = [];
	foreach ($array as $k => $v) {
		$new[] = $k;
	}		
	foreach ($array as $k => $tmp) {
		foreach ($tmp as $k1 => $v) {
			$new[] = $v;
		}
	}
	return $new;
}
