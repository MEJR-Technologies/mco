<?php	session_start();		//$sort and $action set in proxy/index.php	/$sort = $sort == "id" ? "church_id" : $sort;		//Post params	$church_id 		= $_POST['church_id'];	$church_name	= $_POST['church_name'];			$errors = 0;	$message = "";		//String for JSON output		$outString = '{"success":';				if($action == "load"){		$dataQuery = "SELECT church_id, church_name FROM churches";		$res = mysql_query($dataQuery);		$row = mysql_fetch_assoc($res);				$outString .= 'true,"data":' . json_encode($row);	} elseif($action == "delete"){			$deleteSQL = "DELETE FROM churches WHERE church_id = " . $church_id;			$res = mysql_query($deleteSQL);						if(mysql_affected_rows() == 1){				$outString .= 'true';			} else {				$outString .= 'false';				}	} elseif($action == "edit"){				if($errors == 0){			$dataQuery = "UPDATE churches SET church_id = " . $church_id . ", church_name = '" . $church_name. "'";			$dataQuery .= " WHERE church_id = " . $church_id;						$res = mysql_query($dataQuery);			if(mysql_affected_rows() == 1){				$outString .= 'true,"message":"Church successfully updated."';			} else {				$outString .= 'false,"message":"A database error occured: ' . mysql_error() . '"';			}		} else {			$outString .= 'false,"message":"' . $message . '"';					}	} elseif($action == "add"){						if(strlen($church_name) < 1){			$errors++;			if(strlen($message) > 0){				$message .= "<br>";				}			$message .= "Church Name cannot be blank.";					}				if($errors == 0){			$dataQuery = "INSERT INTO churches (church_id,church_name) ";			$dataQuery .= "VALUES( " . $church_id . ",'" . $church_name . "')";			$res = mysql_query($dataQuery);			if(mysql_affected_rows() == 1){				$outString .= 'true,"message":"Church successfully added."';			} else {				$outString .= 'false,"message":"A database error occured: ' . mysql_error() . '"';				}		} else {			$outString .= 'false,"message":"' . $message . '"';					}			} else {  		$cntQuery = "SELECT COUNT(*) as cnt FROM churches";			$num_result = mysql_query($cntQuery) or die (mysql_error()."<br>".$cntQuery);		$a = mysql_fetch_assoc($num_result);						$dataQuery = "SELECT church_id,church_name FROM churches";			$dataQuery .= " ORDER BY $sort $dir";				if($start && $limit){			$dataQuery .= " LIMIT $start,$limit";		}				$res = mysql_query($dataQuery);				$outString .= 'true, "total":"'.$a['cnt'].'", "results":[';		$c = 0;		while ($row = mysql_fetch_assoc($res)){			if ($c != 0){				$outString .= ',';			}			$outString .= json_encode($row);			$c++;				}		$outString .= ']';	}		$outString .= '}';		echo $outString;	exit;?>