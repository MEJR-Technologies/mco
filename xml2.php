<?php
	session_start();
	
	$start_date 	= $_POST['start_date']; 
	$end_date   	= $_POST['end_date'];
	$gathering_id	= $_POST['gathering_id'];
	$event_ids	= $_POST['event_ids'];
	$chart_type	= $_POST['chart_type'];
	$church_id	= $_SESSION['User']['church_id'];
	
	$xmlStr = '<graph  caption="Attendance" animation="1" formatNumberScale="0" numberPrefix="$" pieSliceDepth="30" decimalPrecision="0" shownames="1" >';	
	
	//Get all	
	if($event_ids != ''){
		$chart_sql = "select SUM(a.a_count) as total,e.name";
		$chart_sql .= " from attendance a inner join gathering g on a.gathering_id = g.id inner join event e on a.event_id = e.id";
		$chart_sql .= " and a.event_id IN (SELECT event_id FROM events_gatherings where gathering_id = a.gathering_id) ";
		$chart_sql .= " and a.church_id = " . $church_id . " group by e.name";		
	} else {
		$chart_sql = "select SUM(a.a_count) as total,e.name";
		$chart_sql .= " from attendance a inner join gathering g on a.gathering_id = g.id inner join event e on a.event_id = e.id";
		$chart_sql .= " and a.event_id IN (" . $event_ids . ") ";
		$chart_sql .= " and a.church_id = " . $church_id . " group by e.name";
	}
	
	$chart_data = mysql_query($chart_sql);
	
	while($row = mysql_fetch_assoc($chart_data)){
		$xmlStr .= '<set name="' . $row['name'] . '" value="' . $row['value'] . '" color="AFD8F8"/>'; 
	}
	
	$xmlStr .= '</graph>';
?>

<?php header('Content-Type: text/xml'); ?>

<?php echo strtotime($_POST['end_date']); ?>
