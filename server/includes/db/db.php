<?php
	$db_params = array();
	$settings = file(dirname(__FILE__) . '/db.settings');
	
	function c_assoc($v){
		global $db_params;
		$x = explode("=",$v);
		$db_params[$x[0]] = $x[1];
	}
	
	array_map("c_assoc",explode(",",chop($settings[0])));

	//Global Database Connect, odd flags set...
	global $dbconn;
	$dbconn = mysql_connect($db_params['DBHOSTNAME'],$db_params['DBUSERNAME'],$db_params['DBPASSWORD'], false, 65536);
	if(!$dbconn){
		die('Not connected : ' . mysql_error());
	}

	$db_selected = mysql_select_db($db_params['DBNAME'], $dbconn);
	if(!$db_selected){
		die('Can\'t use ' . $db_params['DBNAME'] . ': ' . mysql_error());
	}
?>
