<?php
	
	// format the uptime
	// static uptime string
	function format_uptime($seconds) {
		$secs = intval($seconds % 60);
		$mins = intval($seconds / 60 % 60);
		$hours = intval($seconds / 3600 % 24);
		$days = intval($seconds / 86400);
		
		if( $days > 0 ) {
			$uptimeString .= $days;
			$uptimeString .= (($days == 1) ? " day" : " days");
		}
		if( $hours > 0 ) {
			$uptimeString .= (($days > 0) ? ", " : "") . $hours;
			$uptimeString .= (($hours == 1) ? " hour" : " hours");
		}
		if( $mins > 0 ) {
			$uptimeString .= (($days > 0 || $hours > 0) ? ", " : "") . $mins;
			$uptimeString .= (($mins == 1) ? " minute" : " minutes");
		}
		if( $secs > 0 ) {
			$uptimeString .= (($days > 0 || $hours > 0 || $mins > 0) ? ", " : "") . $secs;
			$uptimeString .= (($secs == 1) ? " second" : " seconds");
		}
		return $uptimeString;
	}
	
	// read server uptime
	$uptime = exec("cat /proc/uptime");
	$uptime = @split(" ", $uptime);
	$uptimeSecs = $uptime[0];
	
	// get the static uptime
	$staticUptime = "Server Uptime: " . format_uptime($uptimeSecs);
	
?>