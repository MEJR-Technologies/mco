<?php
	require_once '../includes/db/db.php';
	require_once '../includes/misc/date_functions.php';	
	
	$include 		= $_GET['proxy'];
	$start 			= $_GET['start'];
	$limit 			= $_GET['limit'];	
	$sort 			= $_GET['sort'] ? $_GET['sort'] : 'id';
	$dir 			= $_GET['dir'] ? $_GET['dir'] : 'ASC';
	$action			= $_POST['action'];
	
	include($include);
?>
