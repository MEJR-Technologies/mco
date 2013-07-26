<?php
	session_start();	
	$email 		= $_POST['email'];
	$action 	= $_POST['action'];
	$pwd		= $_POST['pwd'];
	$pwd_confirm	= $_POST['pwd_confirm'];
	
	$outString 	= '{"success":';
	
	function generatePassword($length = 8){
		$password = "";		
		$possible = "0123456789bcdfghjkmnpqrstvwxyz";		
		$i = 0; 
		
		while ($i < $length) { 
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		
			if (!strstr($password, $char)) { 
				$password .= $char;
				$i++;
			}	
		}
		
		return $password;	
	}	

	if($action == 'change'){
		$user_id = $_SESSION['User']['id'];
		
		if($pwd == $pwd_confirm){
			$dataQuery = "UPDATE users SET password = '" . md5($pwd) . "' WHERE user_id = " . $user_id;
			$res = mysql_query($dataQuery);

			if($res){
				$outString .= 'true,"message":"Your password has been reset."';				
			} else {
				$outString .= 'false,"message":"An error occurred."';				
			}
		} else {
			$outString .= 'false,"message":"Your passwords did not match.<br/><br/>Please Try Again."';			
		}
	} else {
		$dataQuery = "SELECT user_id FROM users WHERE user_email = '" . $email . "'";
		$res = mysql_query($dataQuery);
		$user_row = mysql_fetch_assoc($res);
		
		$user_id = $user_row['user_id'];
		
		if($user_id){
			//generate random password
			$password = generatePassword();		
			
			//update query
			$dataQuery = "UPDATE users SET password = '" . md5($password) . "' WHERE user_id = " . $user_id;
			
			//echo $dataQuery;exit;		
			
			//run query
			mysql_query($dataQuery);
			
			$outString .= 'true,"message":"Your password has been reset, please check your email."';
			
			$headers = 'From: support@mychurchoffice.net' . "\r\n" . 'Reply-To: support@mychurchoffice.net';		
			
			mail($email,"My Church Office Password Reset","Your password has been reset.\n\nNew Password: " . $password,$headers);		
		} else {
			$outString .= 'false,"message":"The email address you entered is not in our system.<br><br>Please Try Again."';		
		}
	}
	
	$outString .= '}';	
	echo $outString;
	exit;
?>
