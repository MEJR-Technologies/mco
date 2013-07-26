<?php header('Content-Type: text/xml'); ?>
<?php
	if($_GET['noData'] == 1){
		echo '<graph  caption="Attendance" animation="1" formatNumberScale="0" numberPrefix="" pieSliceDepth="30" decimalPrecision="0" shownames="1" ></graph>';
		exit;
	}
	
	session_start();
	
	function random_color(){
	    mt_srand((double)microtime()*1000000);
	    $c = '';
	    while(strlen($c)<6){
		$c .= sprintf("%02X", mt_rand(0, 255));
	    }
	    return $c;
	}	
	
	$start_date 	= $_POST['start_date'];
	$end_date   	= $_POST['end_date'];
	$gathering_id	= $_POST['gathering_id'];
	$event_ids	= $_POST['event_ids'];
	$chart_type	= $_POST['chart_type'];
	$church_id	= $_SESSION['User']['church_id'];
	
	if($chart_type == 'pie'){
		$xmlStr = '<graph  caption="Attendance" animation="1" formatNumberScale="0" numberPrefix="" pieSliceDepth="30" decimalPrecision="0" shownames="1">';
	} else {
		$xmlStr = '<graph  yAxisName="Attendance By Event"  caption="Attendance" decimalPrecision="0" divlinedecimalPrecision="0" imitsdecimalPrecision="0" shownames="1">';		
	}	
	
	//Get all	
	$chart_sql = "select SUM(a.a_count) as total,e.name";
	$chart_sql .= " from attendance a inner join gathering g on a.gathering_id = g.id inner join event e on a.event_id = e.id";
	
	if(empty($event_ids)){
		$chart_sql .= " and a.event_id IN (SELECT event_id FROM events_gatherings where gathering_id = a.gathering_id) ";
	} else {
		$chart_sql .= " and a.event_id IN (" . $event_ids . ") ";	
	}
	
	$chart_sql .= " and a.church_id = " . $church_id;
	
	if($start_date && $end_date){
		$chart_sql .= " and UNIX_TIMESTAMP(a.t_stamp) BETWEEN '" . strtotime($start_date) . "' and '" . strtotime($end_date) . "'";
	} elseif($start_date){
		$chart_sql .= " and UNIX_TIMESTAMP(a.t_stamp) >= '" . strtotime($start_date) . "'";			
	} elseif($end_date){
		$chart_sql .= " and UNIX_TIMESTAMP(a.t_stamp) <= '" . strtotime($end_date) . "'";			
	}
	$chart_sql .= " and a.gathering_id = " . $gathering_id;
	$chart_sql .= " group by e.name";
	
	$chart_data = mysql_query($chart_sql);	
	
	while($row = mysql_fetch_assoc($chart_data)){
		$xmlStr .= '<set name="' . $row['name'] . '" value="' . $row['total'] . '" color="' . random_color() . '"/>'; 
	}
	
	$xmlStr .= '</graph>';
?>
<?php
	//echo $chart_sql;exit;
	echo $xmlStr;	
?>
