<?php
	$dataQuery = "SELECT * FROM churches ORDER BY church_name";
	$res = mysql_query($dataQuery);
	
	//$outString = '{"success":true, "total":"'.$a['cnt'].'", "results":[';
	$c = 0;
	$outString = '{"churches":[';	
	while ($row = mysql_fetch_assoc($res)){
		if ($c != 0){
			$outString .= ',';
		}
		$outString .= json_encode($row);
		$c++;		
	}
	$outString .= ']}';
	echo $outString;
	exit;
?>
