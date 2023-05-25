<?php
	include "details.php";
	
	$codeInfo = ""; 
	
	// Gets the user input and strips off possible special html characters. 
	
	if(isset($_POST["submitBtn"])){	
		
		$code = htmlspecialchars(strip_tags($_POST["code"])); 
		
		$codeInfo = "Code: ".$code; 
		
	}else{
		$code = null;  
	}
	
	// If the code wasn't found in the database, this message gets displayed on the page. 
	
	$message = "<span id='info'>Code not found</span>"; 
	
	// Splits the date and time values and displays them separately on the page. 
	
	function splitDateTime($d){
		
		$dt = explode(" ", $d);
		$justDate = $dt[0];
		$justTime = $dt[1];

		$justDateExploded = explode("-", $justDate);
		$justDateImploded = implode("/", $justDateExploded); 
		
		$dateTime = []; 
		
		array_push($dateTime, $justDateImploded);
		array_push($dateTime, $justTime);
			
		return $dateTime; 
	}
		
	// Calls the readDetails method which accepts the unique code as a parameter and creates a div for each row found in the table,
	// which is related to the passed code. Every created div contains the name, min value, max value, date, time and random number related to the passed unique code. 
	// If the user didn't specify the name while generating random numbers, the name value id defaulted to "Not Specified". 
	
	function createRows(){
		 
		 global $code;
		 global $message; 
		 
		 if($code != null){
			 $db = new Database(); 
		  
			 $conn = $db->connect("localhost", "verifyrandom", "root", "");
				
			 $nextEntry = new Details($conn);
			 
			 $data = $nextEntry->readDetails($code); 
			 
			 if(!$data){
				 echo $message; 
			 }
			 
			 foreach($data as $newRow){
				 
				 $justDate = splitDateTime($newRow["dateTime"])[0]; 
				 $justTime = splitDateTime($newRow["dateTime"])[1]; 
				 
				 echo "<div class='data' id='name'>{$newRow['name']}</div>
				<div class='data' id='minVal'>{$newRow['minimum']}</div>
				<div class='data' id='maxVal'>{$newRow['maximum']}</div>
				<div class='data' id='date'>{$justDate}</div>
				<div class='data' id='time'>{$justTime}</div>
				<div class='data' id='number'>{$newRow['result']}</div>"; 
			 }
		 }else{
			 return ""; 
		 }
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="styles/stylesHeader.css">
	<link rel="stylesheet" type="text/css" href="styles/stylesFooter.css">
	<link rel="stylesheet" type="text/css" href="styles/stylesVerify.css">

	<link rel="shortcut icon" type="image/png" href="image/favicon.png">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Nixie+One&display=swap" rel="stylesheet">

	<title>VerifyRandom - Verification</title>

</head>
<body>
	<header>
		<a href="index.php"><h1>VerifiedRandom.org</h1></a>
	</header>
	<nav>
		<div class="navigation">
			<a href="index.php">Home</a>
		</div>
		<div class="navigation">
			<a href="#">Verify</a>
		</div>
		<div class="navigation">
			<a href="contact.html">Contact</a>
		</div>
		<div class="navigation">
			<a href="about.html">About</a>
		</div>
	</nav>
	<div id="wrapper">
		<form id="form" action="" method="POST">
			<label>Verification Code</label>
			<input type="text" name="code" id="code">
			<input type="submit" name="submitBtn" value="Verify" id="btn">
		</form>
		<div id="displayCode"><?= $codeInfo ?></div>
		<div id="titleWrapper">
			<div class="title">Name</div>
			<div class="title">Min Value</div>
			<div class="title">Max Value</div>
			<div class="title">Date</div>
			<div class="title">Time</div>
			<div class="title">Result</div>
		</div>
		<div id="dataWrapper"><?= createRows(); ?></div>
	</div>
	<footer>
		<a href="index.php">Home</a>
		<a href="#">Verify</a>
		<a href="contact.html">Contact</a>
		<a href="about.html">About</a>
		<a href="terms.html" id="tos">Terms of Service</a>
		<a href="index.php" id="logo">VerifiedRandom.org</a>
	</footer>
</body>
</html>