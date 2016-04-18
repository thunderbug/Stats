<?php
class TimeConverter {
	
	public function getHour($seconds, $m, $s){
		$hrs  = floor($seconds/3600);
		$temp = $seconds - (3600*$hrs);
		$mins = floor($temp/60);
		$temp = $temp - (60*$mins);
		$secs = $temp;
		
		$string = $hrs."h";
		if ($m) $string .= " ".$mins."m";
		if ($s) $string .= " ".$secs."s";
		return $string;
	}
	
	public function getDate($datetime){
		return date('jS F Y \a\t G:i', strtotime($datetime));
	}
	
	public function getDateFormat($datetime){
		return date('d/m/y \a\t H:i', strtotime($datetime));
	}
}