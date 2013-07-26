<?php
	session_start();
	
	$message 	= $_POST['message'];
	$category 	= $_POST['category'];
	$subject	= $_POST['subject'];
	$email		= $_SESSION['User']['user_email'];
	
	//Outstring	
	$outString = '{"success":';	

	if($action = "add"){	
		$insertSQL = "INSERT INTO contact (date_added,category,subject,message,email) ";
		$insertSQL .= "VALUES(now(),'" . $category . "','" . $subject . "','" . $message . "','" . $email . "')";
	
		$res = mysql_query($insertSQL);

		if(mysql_affected_rows() == 1){
			mail("mclaugh2004@gmail.com","Contact Form Submission","A user has submitted feedback via the contact form.");
			$outString .= 'true,"message":"Your message has been submitted.<br>Should your message require a response you will hear from us shortly."';
		} else {
			$outString .= 'false,"message":"An error occured:' . mysql_error() . '"';	
		}
	}
	
	$outString .= '}';	
	echo $outString;
	exit;
?>	
