<?php
	
	class Database{
		private $host;
		private $dbName;
		private $username;
		private $password;
		public $conn;
		
		public function connect($host, $dbName, $username, $password){
			
			$this->conn = null; 
			
			$this->host = $host; 
			$this->dbName = $dbName;
			$this->username = $username;
			$this->password = $password;
			
			try{
				$this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->dbName, $this->username, $this->password); 
				$this->conn->exec("set names utf8"); 
			}catch(PDOException $exception){
				echo "Connection error: ".$exception->getMessage(); 
			}
			return $this->conn; 
		}
	}
	