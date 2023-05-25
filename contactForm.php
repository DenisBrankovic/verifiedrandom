<?php

	if(isset($_POST["submit"])){
		 $name = $_POST["name"];
		 $subject = $_POST["subject"]; 
		 $mailFrom = $_POST["mail"];
		 $message = $_POST["message"];
		 		 
		 $mailTo = "le5o02xoi4x4@verifiedrandom.org"; 
		 $headers = "From: my website. Sender: ".$mailFrom; 
		 $txt = "Incoming mail from ".$name.".\n\n".$message;
		 mail($mailTo, $subject, $txt, $headers);
		 header("Location: index.php?mailsend");
		 
	 }else{
		 header("Location: contactForm.php"); 
	 }