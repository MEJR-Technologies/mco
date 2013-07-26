<?php
	session_start();
	
	//Post params
	$event_id 			= $_POST['event_id'];
	$name	 			= $_POST['name'];
	$descr 				= $_POST['descr'];
	$attendance_data 	= $_POST['attendance_data'] == "on" ? 1 : 0;
	$offering_data 		= $_POST['offering_data'] == "on" ? 1 : 0;
	$teacher 			= $_POST['teacher'];
	$event_subject 		= $_POST['event_subject'];
	$church_id 			= $_POST['church_id'];
	$event_type_id 		= $_POST['event_type_id'];	
	
	$errors = 0;
	$message = "";	
	
	//Outstring
	$outString = '{"success":';	
	
	if($action == "add"){
		$insertSQL = "INSERT INTO event (name,descr,date_added,attendance_data,offering_data,teacher,event_subject,church_id,event_type_id) ";
		$insertSQL .= "VALUES('" . $name . "','" . $descr . "',now()," . $attendance_data . "," . $offering_data . ",'" . $teacher . "','" . $event_subject . "'," . $church_id . "," . $event_type_id . ")";
		$res = mysql_query($insertSQL);

		if(mysql_affected_rows() == 1){
			$outString .= 'true,"message":"Event Successfully Added."';
		} else {
			$outString .= 'false,"message":"An error occured:' . mysql_error() . '"';	
		}

	} elseif($action == "delete"){
		$deleteSQL = "DELETE FROM event WHERE id = " . $event_id;
		$res = mysql_query($deleteSQL);

		if(mysql_affected_rows() == 1){
			$outString .= 'true';
		} else {
			$outString .= 'false';	
		}

	} elseif($action == "load"){
		$dataQuery = "SELECT event_type_id,id,name,descr,DATE_FORMAT(date_added,'%m/%d/%Y') as date_added,attendance_data,offering_data,teacher,church_id,event_subject FROM event WHERE id = " . $event_id;
		$res = mysql_query($dataQuery);
		$row = mysql_fetch_assoc($res);
		
		$outString .= 'true,"data":' . json_encode($row);
	} elseif($action == "edit"){

		$dataQuery = "UPDATE event SET name = '" . $name . "'";
		$dataQuery .= ",descr = '" . $descr . "'";
		$dataQuery .= ",attendance_data =" . $attendance_data;
		$dataQuery .= ",offering_data = " . $offering_data;
		$dataQuery .= ",teacher = '" . $teacher . "'";
		$dataQuery .= ",event_subject = '" . $event_subject . "'";
		$dataQuery .= ",event_type_id = " . $event_type_id;
		$dataQuery .= " WHERE id = " . $event_id;

		$res = mysql_query($dataQuery);

		if(mysql_affected_rows() == 1){
			$outString .= 'true,"message":"Event Successfully Updated."';
		} else {
			$outString .= 'false,"message":"An error occured."';	
		}
	} elseif($action == "list" || $_GET['action'] == "list"){
		if($_SESSION['User']['site_admin']){
			$dataQuery = "SELECT id,name FROM event ORDER BY name";
		} else {
			$dataQuery = "SELECT id,name FROM event WHERE church_id = " . $_SESSION['User']['church_id'] . " ORDER BY name";
		}
		$res = mysql_query($dataQuery);

		$outString .= 'true, "results":[';
		$c = 0;
		while ($row = mysql_fetch_assoc($res)){
			if ($c != 0){
				$outString .= ',';
			}
			$outString .= json_encode($row);
			$c++;		
		}
		$outString .= ']';
	} elseif($action == "eventValuesList" || $_GET['action'] == "eventValuesList"){
		$dataQuery = "SELECT event_id as id FROM events_gatherings WHERE gathering_id = " . $_POST['gathering_id'];
		$res = mysql_query($dataQuery);
	
		$jsonOut = '{"map_values":"';
			$c = 0;
			while ($row = mysql_fetch_assoc($res)){
				if ($c != 0){
					$jsonOut.= ',';
				}
				$jsonOut .= $row['id'];
				$c++;
			}
		$jsonOut .= '"}';
		echo $jsonOut;
		exit;
	} elseif($action == "attendance"){
		$dataQuery = "SELECT e.id,e.name FROM events_gatherings eg INNER JOIN event e ON eg.event_id = e.id WHERE eg.gathering_id = " . $_POST['gathering_id'] . " ORDER BY e.name ASC";
		$res = mysql_query($dataQuery);
	
		$outString .= 'true, "results":[';
		$c = 0;
		while ($row = mysql_fetch_assoc($res)){
			if ($c != 0){
				$outString.= ',';
			}
			$outString .= json_encode($row);
			$c++;
		}
		$outString .= ']';		
	} else {
		$cntQuery = "SELECT COUNT(*) as cnt FROM event";
		if($_SESSION['User']['site_admin'] != 1){
			$cntQuery .= " WHERE church_id = " . $_SESSION['User']['church_id'];
		}		
		
		$num_result = mysql_query($cntQuery) or die (mysql_error()."<br>".$cntQuery);
		$a = mysql_fetch_assoc($num_result);	
		
		$dataQuery = "SELECT e.*,et.name as event_type FROM event e LEFT OUTER JOIN eventtype et on e.event_type_id = et.id ";
		if($_SESSION['User']['site_admin'] != 1){
			$dataQuery .= " WHERE e.church_id = " . $_SESSION['User']['church_id'];
		}
		$dataQuery .= " ORDER BY $sort $dir";
		
		if($start && $limit){
			$dataQuery .= " LIMIT $start,$limit";
		}		
		
		//echo $dataQuery;exit;
		
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
