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
					$tablelink .= $value['TABLE_NAME'].'.'.
								  $value['COLUMN_NAME'].' = '.
								  $value['REFERENCED_TABLE_NAME'].'.'.
								  $value['REFERENCED_COLUMN_NAME'].' AND ';
				}

				$tablelink .= $this->name.'.id = '.$id;

				$sql = "SELECT $variables FROM $tablejoin ON $tablelink";
				// echo $sql.'<br>';
				// return $sql;
				$result = $this->dbconn->query($sql);
				$tmpmain = $result->fetchAll(PDO::FETCH_ASSOC);

				$tmpmain = $this->removeExcept($tmpmain);
				$tmpmain = $this->mergeArray($tmpmain, $this->merge);

				$array[] = $tmpmain;
			}

			if (count($array) == 1){
				return $array[0][0];
			}
			else{
				for ($i = 0; $i < count($array); $i++) { 
					$array[$i] = $array[$i][0];
				}
				return $array;
			}
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

			$array = $this->removeExcept($array);

			return $array[0];
		}
	}

	public function Translate($array)
	{
		
	}

	public function insert($values)
	{
		foreach ($values as $key => $value) {
			$arr = $value;
			if (isAssoc($arr)){
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
							$values .= $arr[$v] !== '' ? "'".$arr[$v]."', " : 'null, ';
						}
					}
				}
				$values = rtrim($values, ', ');

				$sql = "INSERT INTO $key ($fields) VALUES ($values)";
				echo $sql."\n";
				//$this->dbconn->query($sql);
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
					echo $sql."\n";
					//$this->dbconn->query($sql);
				}
			}
		}
	}

	public function update($id, $values)
	{
		foreach ($values as $key => $value) {
			$arr = $value;

			$avaliable_fields = $this->dbconn->query("DESCRIBE $key");
			$avaliable_fields = $avaliable_fields->fetchAll(PDO::FETCH_ASSOC);

			if (isAssoc($arr)){

				$sql = "UPDATE $key SET ";
				for($i = 1; $i < count($avaliable_fields); $i++){
					foreach ($avaliable_fields[$i] as $k => $v) {
						if ($k == 'Field'){
							$sql .= $v ." = '". $arr[$v] ."', ";
						}
					}
				}
				
				$sql = rtrim($sql, ', ') . " WHERE id = $id";

				//echo $sql."\n";
				$this->dbconn->query($sql);
			}
			else{
				$ids = $this->dbconn->query("SELECT id FROM $key WHERE $this->name"."_id = $id");
				$ids = $ids->fetchAll(PDO::FETCH_NUM);

				for ($a = 0; $a < count($arr); $a++) {


					$sql = "UPDATE $key SET ";

					for ($i = 2; $i < count($avaliable_fields); $i++){
						foreach ($avaliable_fields[$i] as $k => $field) {
							if ($k == 'Field'){
								$sql .= $field ." = '". $arr[$a] ."', ";
							}
						}
					}
					$sql = rtrim($sql, ', ') . " WHERE id = ".$ids[$a][0];

					//echo $sql."\r";
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

	private function mergeArray($array, $merged)
	{
		if (count($merged) > 0){
			foreach ($merged as $mergedkey => $mergedvalue) {
				$tmpmerged = [];
				foreach ($array as $arrkey => $innerarr) {
					foreach ($innerarr as $innerkey => $innerval) {
						if ($mergedvalue == $innerkey){
							$tmpmerged[] = $innerval;
						}
					}
				}
				if (count($tmpmerged) > 0){
					$array[0][$mergedvalue] = $tmpmerged;
				}
			}
			for ($i = 1; $i < count($array[0]); $i++) { 
				unset($array[$i]);
			}
		}
		return $array;
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
function isAssoc(array $arr)
{
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}