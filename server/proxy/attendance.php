<?php
	session_start();
	
	//Include stats library
	require_once '../includes/stats/stats.php';
	
	$sort = $_POST['sort'] == "id" ? "t_stamp" : $_POST['sort']; 
	$dir = $_POST['dir'] ? $_POST['dir'] : "ASC"; 
	$gathering_id 	= $_POST['gathering_id'];
	$event_id 	= $_POST['event_id'];
	$event_date 	= date('Y-m-d H:i:s',strtotime($_POST['event_date']));
	$count		= $_POST['count'];
	$church_id	= $_SESSION['User']['church_id'];
	$attendance_id 	= $_POST['attendance_id'];

	$start = $_POST['start'] ? $_POST['start'] : 0;
	$limit = $_POST['limit'] ? $_POST['limit'] : 25;
	
	//Outstring	
	$outString = '{"success":';

	if($action == "add"){
		$insertSQL = "INSERT INTO attendance (t_stamp,a_count,event_id,gathering_id,church_id) ";
		$insertSQL .= "VALUES('" . $event_date . "'," . $count . "," . $event_id . "," . $gathering_id . "," . $church_id . ")";
		
		$res = mysql_query($insertSQL);

		if(mysql_affected_rows() == 1){
			$outString .= 'true,"message":"Attendance Record Successfully Added."';
		} else {
			$outString .= 'false,"message":"An error occured:' . mysql_error() . '"';	
		}
	} elseif($action == "delete"){
		$deleteSQL = "DELETE FROM attendance WHERE id = " . $attendance_id;

		$res = mysql_query($deleteSQL);

		if(mysql_affected_rows() == 1){
			$outString .= 'true';
		} else {
			$outString .= 'false';	
		}
	} elseif($action == "dashboard"){
		$stats_array = service_stats($church_id);
		$outString .= 'true, "total":"' . count($stats_array) . '", "results":';
		$outString .= json_encode($stats_array);				
	} else {
		$dataQuery = "SELECT a.id as attendance_id,";
		//$dataQuery .= "DATE_FORMAT(a.t_stamp,'%m/%d/%Y') as attendance_date,";
		$dataQuery .= "a.t_stamp,";
		$dataQuery .= "a.a_count as attendance_count,";
		$dataQuery .= "e.name as event_name,";
		$dataQuery .= "e.id as event_id,";
		$dataQuery .= "g.id as gathering_id,";
		//$dataQuery .= "CONCAT_WS(' - ',g.name,DATE_FORMAT(a.t_stamp,'%m/%d/%Y')) as gathering_name,";
		$dataQuery .= "g.name as gathering_name,";
		$dataQuery .= "e.teacher as teacher ";
		$dataQuery .= "FROM attendance a INNER JOIN event e ON a.event_id = e.id ";
		$dataQuery .= "INNER JOIN gathering g ON a.gathering_id = g.id ";
		$dataQuery .= "WHERE a.church_id = " . $church_id . " ";
		$dataQuery .= "ORDER BY $sort $dir ";

		
		$cntQuery = $dataQuery;
		
		$num_result = mysql_query($cntQuery) or die (mysql_error()."<br>".$cntQuery);
		$a = mysql_num_rows($num_result);			
		
		//if($start != "" && $limit != ""){
			$dataQuery .= "LIMIT $start,$limit";			
		//}

		//echo $dataQuery;exit;
		
		$res = mysql_query($dataQuery);
		
		$outString .= 'true, "total":"' . $a . '", "results":[';
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