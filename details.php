<?php
	include "database.php"; 
	
	 class Details{
		 
		 private $conn; 
		 
		 public $codeId; 
		 public $code; 
		 public $name;
		 public $minimum;
		 public $maximum;
		 public $dateTime; 
		 public $result; 
		 
		 public function __construct($db){
			 $this->conn = $db; 
		 }
		 
		 // The "details" table contains the codeId, name, min-max values, datetime of the random number generation and the random number itself. 
		 // The "uniquecode" table contains the unique codes, codeId and time of creation.
		 // The "code" column in the "uniquecode" table has a unique constraint set. 
		 // Every unique code can contain many random numbers, names, min-max ranges and generated random numbers.
		 // The "details" table has a foreign key (codeId), which points to the unique code that the data in the "details" table is related to. 

		
		// Reads and returns the codeId from the "uniquecode" table, which is saved as a foreign key for every new entry into the "details" table. 
		
		 public function getCodeId($code){
			 $query = "SELECT * FROM uniquecode WHERE code = ?";
			 
			 $stmt = $this->conn->prepare($query); 
			 			 
			 $stmt->bindParam(1, $code);
			 $stmt->execute(); 
			 
			 $row = $stmt->fetchAll(PDO::FETCH_ASSOC); 
			 
			 $codeId = $row[0]["codeId"]; 
						 
			 return $codeId; 
		 }
		 
		 // Reads all the data from both tables, stores it into an array and returns the created array. 
		 
		 public function readDetails($code){
			 $query = "SELECT * FROM details JOIN uniquecode on details.codeId = uniquecode.codeId WHERE uniquecode.code  = ?"; 
			 
			 $stmt = $this->conn->prepare($query); 		 
			 
			 $stmt->bindParam(1, $code); 
			 $stmt->execute(); 
			 
			 $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			 			 
			 $allEntries = array();

			 foreach($row as $entry){
				 $this->code = $entry["code"];
				 $this->name = $entry["name"];
				 $this->minimum = $entry["minimum"];
				 $this->maximum = $entry["maximum"];
				 $this->dateTime = $entry["dateTime"];
				 $this->result = $entry["result"]; 
				 
				 array_push($allEntries, $entry);
			 }
			 return $allEntries;
		 }
		 		
		 // Saves the generated unique code into the "uniquecode" table. 
		 
		 public function saveUniqueCode(){
				 $query = "INSERT INTO uniquecode SET code=:code, creationTime=:creationTime"; 
							 
				 $stmt = $this->conn->prepare($query);
				 
				 $dt = Date("Y-m-d H:i:s");				 
				 
				 $stmt->bindParam(":code", $this->code);
				 $stmt->bindParam(":creationTime", $dt); 
										 
				 if($stmt->execute()){
					 return true;
				 }else{
					 return false; 
				 }
		 }
		 
		 // Saves the data into the "details" table. This method gets the values of the hidden form on the home page. The form is inside the number generator. 
		 
		 public function saveDetails(){
			 $query = "INSERT INTO details SET codeId=:codeId, name=:name, minimum=:minimum, maximum=:maximum, dateTime=:dateTime, result=:result";
			 
			 $stmt = $this->conn->prepare($query); 
			 
			 $this->name = htmlspecialchars(strip_tags($this->name)); 
			 $this->codeId = $this->getCodeId($this->code);
			 
			 $stmt->bindParam(":codeId", $this->codeId); 
			 $stmt->bindParam(":name", $this->name);
			 $stmt->bindParam(":minimum", $this->minimum);
			 $stmt->bindParam(":maximum", $this->maximum); 
			 $stmt->bindParam(":dateTime", $this->dateTime);
			 $stmt->bindParam(":result", $this->result); 
			 
			 if($stmt->execute()){
				 return true; 
			 }
			 
			 return false; 
		 }
		 
		 // Calls the stored procedure in the database, which deletes entries at least 30 days old. This method is called on home page load. 
		 
		 public function deleteOldOnes(){
  			
			$query = "CALL deleteOldEntries()"; 
		  			
			$stmt = $this->conn->prepare($query); 
		  			
			if($stmt->execute()){
				return true;
			}
			
			return false;
		}
	 }
	 
								