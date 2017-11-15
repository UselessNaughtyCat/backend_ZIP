<?php
	class db{

		private $dbhost = 'localhost';
		private $dbuser = 'root';
		private $dbpass = '';
		private $dbname = 'slim';

		public function connect(){
			$mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
			$dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
			$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $dbConnection;
		}
	}

	//data:text/html,<form action=http://localhost/public/test/1 method=post><input type=hidden name=_METHOD value=PUT><input name=name><input name=last_name><input type=submit></form>