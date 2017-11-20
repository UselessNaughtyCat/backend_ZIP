<?php

class Table
{
	private $dbhost = 'localhost';
	private $dbuser = 'root';
	private $dbpass = '';
	private $dbname = 'ufanet_work';

	protected $name   = '';
	protected $links  = [];
	protected $except = [];
	protected $merge  = [];
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
		$sql = "SELECT id FROM $this->name";
		$result = $this->dbconn->query($sql);
		$id = $result->fetchAll(PDO::FETCH_NUM);

	    $array = [];
	    for ($i=0; $i < count($id); $i++) { 
	    	$tmpid = $id[$i][0];
			$array[] = $this->select($tmpid);
	    }	    
	    
	    return $array;
	}

	public function select($id)
	{
		if (count($this->links) > 0)
		{
			$array[$this->name] = $this->simpleSelect($this->name, $id, null, false)[0];
			$linksArray = [];
			foreach ($this->links as $key => $links) {
				$linkTable = $this->simpleSelect($key, null, $this->name.'_id = '.$id, false);

				for ($i=0; $i < count($linkTable); $i++) { 
					foreach ($linkTable[$i] as $linkKey => $linkVal) {
						$refID = null;
						$refName = null;
						for ($j = 0; $j < count($links); $j++) { 
							if (strpos($linkKey, $links[$j]) > -1){
								$sub = substr($links[$j], strpos($linkKey, $links[$j]));
							}
							if ($sub != null && $sub !== $this->name){
								$refID = $linkVal;
								$refName = $sub;
							}
							$sub = '';
							if ($refID != null && $refName != null){
								$linkTable[$i] = $this->simpleSelect($refName, $refID, null, false)[0];
							}
						}
					}
				}
				
				for ($i = 0; $i < count($linkTable); $i++) { 
					if (count($linkTable[$i]) > 1){
						continue;
					}
					foreach ($linkTable[$i] as $value) {
						$linkTable[$i] = $value;
					}
				}
				if (count($linkTable) > 0){
					$linksArray[str_replace($this->name.'_', "", $key)] = $linkTable;
				}
			}
			foreach ($linksArray as $key => $value) {
				$array[$this->name][$key] = $value;
			}
			// print_r($array);
		}
		else
		{			
			$array[$this->name] = $this->simpleSelect($this->name, $id, null, false)[0];
			// print_r($array);
		}
		return $array;
	}

	public function insert($values)
	{
		$values = $this->linksOut($values);
		$values = $this->beforeUpdate($values);

		// print_r($values);
		
		foreach ($values as $key => $value) {
			$arr = $value;
			if ($this->isAssoc($arr)){
				$avaliable_fields = $this->dbconn->query("DESCRIBE $key");
				$avaliable_fields = $avaliable_fields->fetchAll(PDO::FETCH_ASSOC);

				$fields = '';
				for ($i = 0; $i < count($avaliable_fields); $i++){
					foreach ($avaliable_fields[$i] as $k => $v) {
						if ($k == 'Field'){
							$fields .= $v.', ';
						}
					}
				}
				$fields = rtrim($fields, ', ');

				$values = '';
				for ($i = 0; $i < count($avaliable_fields); $i++){
					foreach ($avaliable_fields[$i] as $k => $v) {
						if ($k == 'Field'){
							$values .= $arr[$v] !== null ? "'".$arr[$v]."', " : 'null, ';
						}
					}
				}
				$values = rtrim($values, ', ');

				$sql = "INSERT INTO $key ($fields) VALUES ($values)";
				// echo $sql."\n";
				$this->dbconn->query($sql);
			}
			else{
				for ($a = 0; $a < count($arr); $a++) { 
					$max_id = $this->dbconn->query("SELECT MAX(id) FROM $this->name");
					$max_id = $max_id->fetchAll(PDO::FETCH_NUM)[0][0];
					// echo $this->name."\n".$max_id."\n";

					$avaliable_fields = $this->dbconn->query("DESCRIBE $key");
					$avaliable_fields = $avaliable_fields->fetchAll(PDO::FETCH_ASSOC);

					$fields = '';
					for ($i = 0; $i < count($avaliable_fields); $i++){
						foreach ($avaliable_fields[$i] as $k => $v) {
							if ($k == 'Field'){
								$fields .= $v.', ';
							}
						}
					}
					$fields = rtrim($fields, ', ');

					$values = "null, $max_id, ".$arr[$a];

					$sql = "INSERT INTO $key ($fields) VALUES ($values)";
					// echo $sql."\n";
					$this->dbconn->query($sql);
				}
			}
		}
		
	}

	public function update($id, $values)
	{
		$values = $this->linksOut($values);
		$values = $this->beforeUpdate($values);

		foreach ($values as $key => $value) {
			$arr = $value;

			$avaliable_fields = $this->dbconn->query("DESCRIBE $key");
			$avaliable_fields = $avaliable_fields->fetchAll(PDO::FETCH_ASSOC);

			if ($this->isAssoc($arr)){

				$sql = "UPDATE $key SET ";
				for($i = 1; $i < count($avaliable_fields); $i++){
					foreach ($avaliable_fields[$i] as $k => $v) {
						if ($k == 'Field'){
							$sql .= $v ." = '". $arr[$v] ."', ";
						}
					}
				}
				
				$sql = rtrim($sql, ', ') . " WHERE id = $id";

				// echo $sql."\n";
				$this->dbconn->query($sql);
			}
			else{
				$ids = $this->dbconn->query("SELECT id FROM $key WHERE $this->name"."_id = $id");
				$ids = $ids->fetchAll(PDO::FETCH_NUM);

				for($i = 0; $i < count($ids); $i++){
					$this->dbconn->query("DELETE FROM $key WHERE id = ".$ids[$i][0]);
					//echo "DELETE FROM $key WHERE id = ".$ids[$i][0]."\r";
				}

				for ($a = 0; $a < count($arr); $a++) {
					$fields = '';
					for ($i = 0; $i < count($avaliable_fields); $i++){
						foreach ($avaliable_fields[$i] as $k => $v) {
							if ($k == 'Field'){
							$fields .= $v.', ';
							}
						}
					}
					$fields = rtrim($fields, ', ');
					$values = "null, $id, ".$arr[$a];

					$sql = "INSERT INTO $key ($fields) VALUES ($values)";
					// echo $sql."\n";
					$this->dbconn->query($sql);
				}
			}
		}
	}

	public function delete($id)
	{
		// так же нужно будет подефолту удалять из 
		// связанных таблиц и чекать на хедфон

		$sql = "DELETE FROM $this->name WHERE id = $id";
		$this->dbconn->query($sql);
	}

	protected function beforeUpdate($array)
	{
		return $array;
	}

	private function simpleSelect($table, $id = null, $condition = null, $isFullName = true)
	{
		$variables = '';
		if ($isFullName){
			$result = $this->dbconn->query("DESCRIBE $table");
			$arr = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($arr as $arrkey => $tmparr) {
				foreach ($tmparr as $tmparraykey => $value) {
					if ($tmparraykey == 'Field'){
						$variables .= "$table.$value AS '$table.$value', ";
					}
				}
			}
			$variables = rtrim($variables, ', ');
		}
		else{
			$variables = "*";
		}

		if ($id != null) {
			$conditions = "WHERE id = $id";
		}
		elseif ($condition != null) {
			$conditions = "WHERE $condition";
		}
		else {
			$conditions = '';
		}

		$sql = "SELECT $variables FROM $table $conditions";
		// echo $sql."\n";
		$result = $this->dbconn->query($sql);
		$array = $result->fetchAll(PDO::FETCH_ASSOC);

		foreach ($array as $arrkey => $tmparr) {
			foreach ($tmparr as $tmparrkey => $val) {
				foreach ($this->except as $exckey => $excval) {
					if ($tmparrkey == $excval){
						unset($array[$arrkey][$tmparrkey]);
					}
				}
			}
		}

		if (!$isFullName){
			foreach ($this->except as $oneExcept) {
				if (strpos($oneExcept, $table) > -1){
					$strOneExcept = str_replace($table.'.', '', $oneExcept);
					for ($i = 0; $i < count($array); $i++) { 
						foreach ($array[$i] as $key => $value) {
							if ($key === $strOneExcept){
								unset($array[$i][$key]);
							}
						}
					}
				}
			}
		}

		return $array;
	}

	private function linksOut($array)
	{
		foreach ($array as $arrayKey => $inner) {
			foreach ($inner as $innerKey => $value) {
				foreach ($this->links as $key => $val) {
					if (strpos($key, $innerKey)){
						$array[$key] = $value;
						unset($array[$arrayKey][$innerKey]);
					}
				}
			}
		}

		return $array;
	}
	private function isAssoc(array $arr)
	{
	    if (array() === $arr) return false;
	    return array_keys($arr) !== range(0, count($arr) - 1);
	}
}