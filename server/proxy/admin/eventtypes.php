<?php
	session_start();
	
	//Post params
	$event_type_id 		= $_POST['event_type_id'];
	$name	 		= $_POST['name'];
	$descr 			= $_POST['descr'];
	$church_id 		= $_POST['church_id'];	
	
	$errors = 0;
	$message = "";

	//Outstring	
	$outString = '{"success":';	
	
	
	if($action == "add"){
		$insertSQL = "INSERT INTO eventtype (name,descr,church_id) ";
		$insertSQL .= "VALUES('" . $name . "','" . $descr . "'," . $church_id . ")";

		$res = mysql_query($insertSQL);

		if(mysql_affected_rows() == 1){
			$outString .= 'true,"message":"Event Type Successfully Added."';
		} else {
			$outString .= 'false,"message":"An error occured:' . mysql_error() . '"';	
		}
	} elseif($action == "delete"){
		$deleteSQL = "DELETE FROM eventtype WHERE id = " . $event_type_id;

		$res = mysql_query($deleteSQL);

		if(mysql_affected_rows() == 1){
			$outString .= 'true';
		} else {
			$outString .= 'false';	
		}
	} elseif($action == "load"){
		$dataQuery = "SELECT id,name,descr,church_id FROM eventtype WHERE id = " . $event_type_id;
		$res = mysql_query($dataQuery);
		$row = mysql_fetch_assoc($res);
		
		$outString .= 'true,"data":' . json_encode($row);
	} elseif($action == "edit"){

		$dataQuery = "UPDATE eventtype SET name = '" . $name . "'";
		$dataQuery .= ",descr = '" . $descr . "'";
		$dataQuery .= " WHERE id = " . $event_type_id;

		$res = mysql_query($dataQuery);

		if(mysql_affected_rows() == 1){
			$outString .= 'true,"message":"Event Type Successfully Updated."';
		} else {
			$outString .= 'false,"message":"An error occured."';	
		}		
	} else {
		$cntQuery = "SELECT COUNT(*) as cnt FROM eventtype";
		if($_SESSION['User']['site_admin'] != 1){
			$cntQuery .= " WHERE church_id = " . $_SESSION['User']['church_id'];
		}		
		
		$num_result = mysql_query($cntQuery) or die (mysql_error()."<br>".$cntQuery);
		$a = mysql_fetch_assoc($num_result);	
		
		$dataQuery = "SELECT id,name,descr FROM eventtype ";
		if($_SESSION['User']['site_admin'] != 1){
			$dataQuery .= "WHERE church_id = " . $_SESSION['User']['church_id'];
		}
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
