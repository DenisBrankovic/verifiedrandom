<?php
	session_start();
	
	include "details.php";	 
	
	// Calls the deleteOldEntries function if the SESSION["oldOnesDeleted"] is false. 
	
	if(!isset($_SESSION["oldOnesDeleted"])){
		deleteOldEntries(); 
	}
	
	// When the hidden form on the home page is submitted, newEntry function is called and the submitted data gets parsed and saved into the database.
	// The data is in arrays of values because user needs to be able to create multiple random numbers, names and ranges in one session. 
	// All the values generated in one session relate to a single unique code, which is generated when the Save button is clicked. 
	
	if(isset($_POST["save"])){
		newEntry();
	} 
	
	// Deletes entries older than 30 days. The function is called on first home page load during one session. 
	
	function deleteOldEntries(){
		if(!isset($_SESSION["oldOnesDeleted"])){
			$db = new Database(); 
				  
			$conn = $db->connect("localhost", "verifyrandom", "root", "");
					
			$nextEntry = new Details($conn);
			
			$nextEntry->deleteOldOnes(); 
		}
		
		$_SESSION["oldOnesDeleted"] = true; 
	}
	
	function newEntry(){
		
		global $lastRndNumber; 
		global $message; 
		
		// Gets the values of the hidden form inside the number generator.
		
		if($_POST["minimum"] != null && $_POST["maximum"] != null){	
			
			$name = $_POST["name"]; 
			$minimum = $_POST["minimum"];
			$maximum = $_POST["maximum"]; 
			$result = $_POST["result"];
			$time = $_POST["time"];
			$code = $_POST["code"]; 
						
			$db = new Database(); 
				  
			$conn = $db->connect("localhost", "verifyrandom", "root", "");
				
			$nextEntry = new Details($conn); 
			$nextEntry->code = $code; 	
			
			// Saves the unique code into the "uniquecode" table and displays the code in the number generator. 
			if($nextEntry->saveUniqueCode()){
				$message = $nextEntry->code;
			}else{
				$message = "Error"; 
					
				return $message; 
			}
				
			$counter = 0;
			
			// Parses the arrays of data and saves them into the database. 
			while($counter < sizeof(explode(",", $_POST["name"]))){
					
				$nextOne = new Details($conn);
							
				$nextEntry->name = explode(",", $_POST["name"])[$counter] != "" ? explode(",", $_POST["name"])[$counter] : "Not Specified"; 
				$nextEntry->minimum = explode(",", $_POST["minimum"])[$counter];
				$nextEntry->maximum = explode(",", $_POST["maximum"])[$counter]; 
				$nextEntry->dateTime = explode(",", $_POST["time"])[$counter]; 
				$nextEntry->result = explode(",", $_POST["result"])[$counter]; 
							
				if($nextEntry->saveDetails()){
					$counter++;
				}else{
					$message = "Error";
						
					return $message; 
				}
			}
			
			// Displays the last generated number at the end of the session. 
			
			$lastRndNumber = $nextEntry->result;
			
			return $message; 
		}
	}
	 
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	
	<link rel="stylesheet" type="text/css" href="styles/stylesFront.css">
	<link rel="stylesheet" type="text/css" href="styles/stylesFooter.css">
	<link rel="shortcut icon" type="image/png" href="image/favicon.png">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Nixie+One&display=swap" rel="stylesheet">
	
	<title>VerifiedRandom</title>

</head>
<body>
	<header>
		<a href="#"><h1>VerifiedRandom.org</h1></a>
	</header>
	<nav>
		<div class="navigation">
			<a href="#">Home</a>
		</div>
		<div class="navigation">
			<a href="verify.php">Verify</a>
		</div>
		<div class="navigation">
			<a href="contact.html">Contact</a>
		</div>	
		<div class="navigation">
			<a href="about.html">About</a>
		</div>
	</nav>
	<!-- The text on the home page is displayed before the number generator in desktop mode, but has to come after the number generator in mobile mode. 
		This is why the div containing the text is duplicated. The upper version of the text is displayed in desktop mode, but hidden in mobile mode, while
		the lower version of the text is displayed in mobile mode (below the number generator) and hidden in desktop mode. -->
		
		<div id="mainLeft">
			<h3 id="mainTitle">The only FREE verified number generation service on the web.</h3>
			<p><a href="#">VerifiedRandom.org</a> is a simple, free service that offers verifiable results and true random number generation for giveaway, contests, lotteries and 
			much more.<br><br>
			<a href="#">VerifiedRandom.org</a> provides TRUE random numbers which are verifiable by any user with the unique verification code 
			at: <a href="verify.php">VerifiedRandom.org/verify</a>.<br><br>
			We use a propitiatory system which generates random numbers from a physical process tather than a hardware process. Numbers generated gathered from thermal noise, 
			which is unpredictable and ever changing. Other services utilize hardware algorithms to generate their numbers, which can cause predictable results, which are not 
			able to be verified by users without paying subscription fees.<br><br>
			<a href="#">VerifiedRandom.org</a> has always been and will forever be a free service for those who wish to host giveaways, lotteries, contests, etc. And give their 
			users a way to verify the results against our database to ensure that the drawing was -in fact- random.<br><br>
			No two verification codes are alike, and results are stored for a minimum of six months from the date of draw. Each drawing requires the input of a name, minimum, 
			and maximum value. When generated, the result will be displayed with a date and time in UTC format, as well as a verification code. If the verification code displayed 
			matches the timestamp on our servers you can rest assured that the drawing was -in fact- truly random. </p>
		</div>
		<div id="mainRight">
			<div id="content">
				<div id="top">
					<h3>Verified Random Number Generator</h3>
				</div>
				<form id="middle" action="" method="POST">
					<div class="form">
						<div class="lbl">Name: </div>
						<input class="txt" id="name" type="text">
					</div>
					<div class="form">
						<div class="lbl">Min:</div>
						<input class="txt" id="minTxt" type="number" value="1">
					</div>	
					<div class="form">
						<div class="lbl">Max:</div>
						<input class="txt" id="maxTxt" type="number" value="100">
					</div>
					<div class="form">
						<div id="generateBtn">Generate</div>
						<input class="formHidden" id="nameHidden" type="txt" name="name">
						<input class="formHidden" id="minHidden" type="txt" name="minimum">
						<input class="formHidden" id="maxHidden" type="txt" name="maximum">
						<input class="formHidden" id="resultHidden" type="txt" name="result">
						<input class="formHidden" id="timeHidden" type="txt" name="time">
						<input class="formHidden" id="codeHidden" type="txt" name="code">
						<input id="saveBtn" type="submit" name="save" value="Save">
					</div>
					<span id="info" style="margin-bottom: 0">Press SAVE when completed to generate verification code</span>
					<div id="blue"></div>
				</form>
			</div>
			<div id="bottom" class="allResults resultHidden">
					<h3 class="allResults">Result:</h3>
					<div id="result" class="allResults"><?= $lastRndNumber ?></div>
					<div id="rangeWrapper" class="allResults">
						<div class="range">Min:</div>
						<div id="minRes" class="range"></div>
						<div class="range">Max:</div>
						<div id="maxRes" class="range"></div>
					</div>
					<div id="dateTimeWrapper" class="allResults">
						<div id="date">22.10.2019.</div>
						<div id="time">22:45:18 UTC</div>
					</div>
					<div id="codeWrapper" class="allResults">
						<div id="codeTitle">Verification Code:</div>
						<div id="code"><?= $message ?></div>
					</div>
				</div>
		</div>
		<!-- Hidden in desktop mode, displayed in mobile mode. --> 
		<div id="mainLeftMobile">
			<h3 id="mainTitleMobile">The only FREE verified number generation service on the web.</h3>
			<p><a href="#">VerifiedRandom.org</a> is a simple, free service that offers verifiable results and true random number generation for giveaway, contests, lotteries and 
			much more.<br><br>
			<a href="#">VerifiedRandom.org</a> provides TRUE random numbers which are verifiable by any user with the unique verification code 
			at: <a href="verify.php">VerifiedRandom.org/verify</a>.<br><br>
			We use a propitiatory system which generates random numbers from a physical process tather than a hardware process. Numbers generated gathered from thermal noise, 
			which is unpredictable and ever changing. Other services utilize hardware algorithms to generate their numbers, which can cause predictable results, which are not 
			able to be verified by users without paying subscription fees.<br><br>
			<a href="#">VerifiedRandom.org</a> has always been and will forever be a free service for those who wish to host giveaways, lotteries, contests, etc. And give their 
			users a way to verify the results against our database to ensure that the drawing was -in fact- random.<br><br>
			No two verification codes are alike, and results are stored for a minimum of six months from the date of draw. Each drawing requires the input of a name, minimum, 
			and maximum value. When generated, the result will be displayed with a date and time in UTC format, as well as a verification code. If the verification code displayed 
			matches the timestamp on our servers you can rest assured that the drawing was -in fact- truly random. </p>
		</div>
		<footer>
			<a href="#">Home</a>
			<a href="verify.php">Verify</a>
			<a href="contact.html">Contact</a>
			<a href="about.html">About</a>
			<a href="terms.html" id="tos">Terms of Service</a>
			<a href="#" id="logo">VerifiedRandom.org</a>
		</footer>
	<script src="frontPage.js"></script>
</body>
</html>