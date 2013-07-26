<?php
	session_start();
	
	$gathering_id 	= $_POST['gathering_id'];
	$name 		= $_POST['name'];
	$descr 		= $_POST['descr'];
	$start_date	= date('Y-m-d H:i:s',strtotime($_POST['start']));
	$end_date	= date('Y-m-d H:i:s',strtotime($_POST['end']));
	$recur 		= $_POST['recur'] == "on" ? 1 : 0;
	$recur_day 	= $_POST['recur_day'];
	$church_id	= $_POST['church_id'];
	$events		= split(",",$_POST['event_ids']);
	
	//print_r($events);exit;
	
	//Outstring	
	$outString = '{"success":';	
	
	if($action == "add"){
		$insertSQL = "INSERT INTO gathering (name,descr,start,end,recur,recur_day,date_added,church_id) ";
		$insertSQL .= "VALUES('" . $name . "','" . $descr . "','" . $start_date . "','" . $end_date . "'," . $recur . "," . $recur_day . ",now()," . $church_id . ")";
		
		$res = mysql_query($insertSQL);

		if(mysql_affected_rows() == 1){
			$outString .= 'true,"message":"Gathering Successfully Added."';
		} else {
			$outString .= 'false,"message":"An error occured:' . mysql_error() . '"';	
		}

		$gathering_id = mysql_insert_id();
		
		//Insert events for this gathering
		for($i=0;$i<count($events);$i++){
			mysql_query("INSERT INTO events_gatherings (event_id,gathering_id) VALUES(" . $events[$i] . "," . $gathering_id . ")");	
		}


	} elseif($action == "delete"){
		$deleteSQL = "DELETE FROM gathering WHERE id = " . $gathering_id;
		$res = mysql_query($deleteSQL);

		if(mysql_affected_rows() == 1){
			$outString .= 'true';
		} else {
			$outString .= 'false';	
		}
	} elseif($action == "load"){
		$gathering_id = $_POST['gathering_id'];
		
		$dataQuery = "SELECT id,name,descr,DATE_FORMAT(start,'%m/%d/%Y') as start,DATE_FORMAT(end,'%m/%d/%Y') as end,recur,recur_day,date_added,church_id FROM gathering WHERE id = " . $gathering_id;
		$res = mysql_query($dataQuery);
		$row = mysql_fetch_assoc($res);
		
		$outString .= 'true,"data":' . json_encode($row);
	} elseif($action == "edit"){
		$recur = $_POST['recur'] == "on" ? 1 : 0;
		$recur_day = $_POST['recur_day'];		
		$gathering_id = $_POST['gathering_id'];

		$dataQuery = "UPDATE gathering SET name = '" . $name . "',descr = '" . $descr . "',start='" . $start_date  . "',end = '" . $end_date . "', recur = " . $recur . ", recur_day = " . $recur_day . ", church_id = " . $church_id . " WHERE id = " . $gathering_id;

		$res = mysql_query($dataQuery);

		if($res){
			$outString .= 'true,"message":"Gathering Successfully Updated."';
		} else {
			$outString .= 'false,"message":"An error occured."';	
		}

		//Delete, then insert events for this gathering
		mysql_query("DELETE FROM events_gatherings WHERE gathering_id = " . $gathering_id);	

		for($i=0;$i<count($events);$i++){
			mysql_query("INSERT INTO events_gatherings (event_id,gathering_id) VALUES(" . $events[$i] . "," . $gathering_id . ")");	
		}
	} else {
		$cntQuery = "SELECT COUNT(*) as cnt FROM gathering";
		if($_SESSION['User']['site_admin'] != 1){
			$cntQuery .= " WHERE church_id = " . $_SESSION['User']['church_id'];
		}		
		
		$num_result = mysql_query($cntQuery) or die (mysql_error()."<br>".$cntQuery);
		$a = mysql_fetch_assoc($num_result);	
		
		$dataQuery = "SELECT * FROM gathering ";
		if($_SESSION['User']['site_admin'] != 1){
			$dataQuery .= " WHERE church_id = " . $_SESSION['User']['church_id'];
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