<?php
	$num_seconds	= 24 * 60 * 60;

	//Pass the day of week you are trying to find 0-6 (0 = Sunday, 6 = Saturday)
	//Returns the date of the last time the day of week occured
	function lastDay($day_of_week,$begin = ''){
		global $num_seconds;
		$begin = !$begin ? date('U') : strtotime($begin);

		$lastDay = "";

		for($i = $begin; $i > 0; $i -= $num_seconds){
			if(date('w',$i) == $day_of_week){
				$lastDay = $i;
				break;
			}
		}

		return $lastDay;

	}

	//Pass the day of week you are trying to find 0-6 (0 = Sunday, 6 = Saturday)
	//Returns the date of the next time the day of week will occur
	function nextDay($day_of_week,$begin = ''){
		global $num_seconds;
		$begin = !$begin ? date('U') : strtotime($begin);
		$lastDay = "";

		for($i = $begin; $i > 0; $i += $num_seconds){
			if(date('w',$i) == $day_of_week){
				$lastDay = $i;
				break;
			}
		}

		return $lastDay;
	}

	//Pass the date you want to start looking from (yyyy-mm-dd)
	//and the date you want to stop looking (yyyy-mm-dd) - optional, defaults to today
	//also pass the day of week you are trying to find 0-6 (0 = Sunday, 6 = Saturday)
	//Returns the number of times that day has occured within the specified range
	function dayYTD($begin,$day_of_week,$end = ''){
		global $num_seconds;

		$num_seconds	= 24 * 60 * 60;
		$begin		= date('U',strtotime($begin));
		$end		= !$end ? date('U') : strtotime($end);

		$day_ytd = 0;

		for($i = $end; $i >= $begin; $i -= $num_seconds){
			if(date('w',$i) == $day_of_week){
				$day_ytd++;	
			}
		}

		return $day_ytd;
	}
	
	function get_days_in_month($month, $year){ 
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year %400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
	}
?>