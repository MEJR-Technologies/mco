<?php
	session_start();
	
	//$sort and $action set in proxy/index.php
	$sort = $sort == "id" ? "user_id" : $sort;
	
	//Post params
	$user_id 		= $_POST['user_id'];
	$password 		= $_POST['password'];
	$confirm_password 	= $_POST['confirm_password'];
	$email 			= $_POST['user_email'];
	$has_admin 		= $_POST['has_admin'] == "on" ? 1 : 0;
	$has_edit 		= $_POST['has_edit'] == "on" ? 1 : 0;
	$is_active 		= $_POST['is_active'] == "on" ? 1 : 0;
	$has_upload 		= $_POST['has_upload'] == "on" ? 1 : 0;
	$username 		= $_POST['username'];
	$church_id 		= $_POST['church_id'];	
	
	$errors = 0;
	$message = "";
	
	//String for JSON output	
	$outString = '{"success":';	
	
	
	if($action == "load"){
		$dataQuery = "SELECT user_id,church_id,username,user_email,has_admin,has_edit,is_active,has_upload FROM users WHERE user_id = " . $user_id;
		$res = mysql_query($dataQuery);
		$row = mysql_fetch_assoc($res);
		
		$outString .= 'true,"data":' . json_encode($row);
	} elseif($action == "delete"){
			$deleteSQL = "DELETE FROM users WHERE user_id = " . $user_id;
			$res = mysql_query($deleteSQL);
			
			if(mysql_affected_rows() == 1){
				$outString .= 'true';
			} else {
				$outString .= 'false';	
			}

	} elseif($action == "edit"){
		if($password){
			if($confirm_password){
				if(md5($password) == md5($confirm_password)){
					$new_password = md5($password);
				}
			} else {
				$errors++;
				if(strlen($message) > 0){
					$message .= "<br>";	
				}
				$message .= "Password and Password Confirmation Must Match.";
			}
		}
		
		if($errors == 0){
			$dataQuery = "UPDATE users SET church_id = " . $church_id . ", user_modified = now(), user_email = '" . $email . "',has_admin = " . $has_admin . ",has_edit = " . $has_edit . ", is_active = " . $is_active . ",has_upload = " . $has_upload;
			
			if($new_password){
				$dataQuery .= ", password = '" . $new_password . "'";				
			}
			
			$dataQuery .= " WHERE user_id = " . $user_id;
			
			$res = mysql_query($dataQuery);

			if(mysql_affected_rows() == 1){
				$outString .= 'true,"message":"User successfully updated."';
			} else {
				$outString .= 'false,"message":"A database error occured: ' . mysql_error() . '"';
			}
		} else {
			$outString .= 'false,"message":"' . $message . '"';			
		}
	} elseif($action == "add"){
		if(md5($password) == md5($confirm_password)){
			$new_password = md5($password);
		} else {
			$errors++;
			if(strlen($message) > 0){
				$message .= "<br>";	
			}
			$message .= "Password and Password Confirmation Must Match.";			
		}
		
		if(strlen($username) < 1){
			$errors++;
			if(strlen($message) > 0){
				$message .= "<br>";	
			}
			$message .= "Username cannot be blank.";			
		}
		
		if($errors == 0){
			$dataQuery = "INSERT INTO users (church_id,username,password,user_email,user_modified,has_admin,has_edit,site_admin,is_active,has_upload) ";
			$dataQuery .= "VALUES( " . $church_id . ",'" . $username . "','" . $new_password . "','" . $email . "',now()," . $has_admin . "," . $has_edit . ",0," . $is_active . "," . $has_upload . ")";
			$res = mysql_query($dataQuery);

			if(mysql_affected_rows() == 1){
				$outString .= 'true,"message":"User successfully added."';
			} else {
				$outString .= 'false,"message":"A database error occured: ' . mysql_error() . '"';	
			}
		} else {
			$outString .= 'false,"message":"' . $message . '"';			
		}		
	} else {  
		$cntQuery = "SELECT COUNT(*) as cnt FROM users";
		
		$num_result = mysql_query($cntQuery) or die (mysql_error()."<br>".$cntQuery);
		$a = mysql_fetch_assoc($num_result);	
			
		$dataQuery = "SELECT user_id,church_id,username,user_email,user_last_login,has_admin,has_edit,is_active,has_upload FROM users ";
		$dataQuery .= " ORDER BY $sort $dir";
		
		if($start && $limit){
			$dataQuery .= " LIMIT $start,$limit";
		}
		
		$res = mysql_query($dataQuery);
		
		$outString .= 'true, "total":"'.$a['cnt'].'", "results":[';
		$c = 0;
		while ($row = mysql_fetch_assoc($res)){
			if ($c != 0){
				$outString .= ',';
			}
			$outString .= json_encode($row);
			$c++;		
		}
		$outString .= ']';
	}
	
	$outString .= '}';	
	echo $outString;
	exit;
?>
