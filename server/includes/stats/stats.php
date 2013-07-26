<?php
	function service_stats($church_id){
		$ytd_total 	= 0;
		$last_rec	= 0;
		$mon_total	= 0;
		$row_ts		= 0;	
		$attendance_dash 	= array();
		$temp_array		= array();
		$max_date		= "";
		$month_begin		= date("Y-m-d",mktime(0,0,0,date("n"),1,date("Y")));
		$year_begin		= date("Y-m-d",mktime(0,0,0,1,1,date("Y")));
		$service_days_in_month 	= 0;
		$service_days_in_year	= 0;
		$months_passed 		= (date('n') - date('n',strtotime($year_begin))) + (date('j')/get_days_in_month(date('n'),date("Y")));		
		
		
		$dataQuery = "SELECT id,name,recur_day FROM gathering WHERE church_id = " . $church_id . " AND recur = 1 ORDER BY name;";
	
		$res = mysql_query($dataQuery);
		
		while($row = mysql_fetch_assoc($res)){		
			$dataQuery = "SELECT * FROM attendance WHERE gathering_id = " . $row['id'];
			
			$res2 = mysql_query($dataQuery);
			
			$temp_array = array();
			if(mysql_num_rows($res2) > 0){
				while($row2 = mysql_fetch_assoc($res2)){
					if(!$max_date || ($max_date && strtotime($row2['t_stamp']) > $max_date)){
						$max_date = strtotime($row2['t_stamp']);
					}				
					array_push($temp_array,$row2);		
				}
				
				for($i = 0; $i < count($temp_array); $i++){
					$row_ts = strtotime($temp_array[$i]['t_stamp']);
					if(strtotime($temp_array[$i]['t_stamp']) == $max_date){
						$last_rec += $temp_array[$i]['a_count'];
					}
					
					if((date("n",$row_ts) == date("n")) && (date("Y",$row_ts) == date("Y"))){
						$mon_total += $temp_array[$i]['a_count'];
					}
					
					if((date("Y",$row_ts) == date("Y"))){
						$ytd_total += $temp_array[$i]['a_count'];
					}				
				}		
				
				$service_days_in_month = dayYTD($month_begin,$row['recur_day']);
				$service_days_in_year = dayYTD($year_begin,$row['recur_day']);					
				
				array_push($attendance_dash,array(
								'gathering_id'=>$row['id'],
								'name'=>$row['name'],
								'last_rec'=>$last_rec,
								'ytd_total'=>$ytd_total,
								'mon_avg'=>round($mon_total/($service_days_in_month > 0 ? $service_days_in_month : 1),0),
								'ytd_avg'=>round($ytd_total/($service_days_in_year > 0 ? $service_days_in_year : 1),0),
								'ytd_mon_avg'=>round($ytd_total/($months_passed > 0 ? $months_passed : 1),0)								
								)
					);
				$last_rec = 0;
				$ytd_total = 0;
				$row_ts = 0;
				$mon_total = 0;			
			}
		}
		
		return $attendance_dash;
	}
?>
