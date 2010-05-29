<?php 

//  Some functions that are usefull but generic enough to be in an own file.



// TimerOn and TimerOff starts and stops a timer and displays the result. ----------------------

function TimerOn(){
	global $TimerStartTime;
	// Time it
	  $TimerStartTime = microtime();
	    $TimerStartArray = explode(" ", $TimerStartTime);
	    $TimerStartTime  = $TimerStartArray[1] + $TimerStartArray[0];
}

function TimerOff(){
	global $TimerStartTime;

	$TimerEndTime= microtime();
	$TimerEndArray = explode(" ", $TimerEndTime);
	$TimerEndTime = $TimerEndArray[1] + $TimerEndArray[0];
	$totaltime2 = $TimerEndTime - $TimerStartTime;
	return round($totaltime2, 3);
}

// This will find a url that starts with http: and turn it into a link by linkifying the word before it. Example: "This is my homepage http://my.page" would output: "This is my <a href="http://my.page>homepage</a>". Inpout diesnt actually have to be a url, it can be any text.

function Linkify($url) {
	  
	$U = explode(' ',$url);
	  
	foreach ($U as $k => $u) {

		if ((stristr($u,'http:') || (count(explode(' ',$u)) > 1)) && (!stristr($u,'href'))) {

			 
			$P[$j] = $U[$k];
			$U[$k] = "<a href=\"". $P[$j] . "\">". $U[($k-1)] . "</a>";
			unset($U[($k-1)]);
			return Linkify( implode(' ',$U));
	    }
	  }
	  return implode(' ',$U);
}

// Takes a timestamp in the 2009-01-01 15:34:11 format and returns the month and date.

function GetOnlyMonthDay($timestamp) {

$month = substr($timestamp, 5, 2);  
$date = substr($timestamp, 8, 2);  
return $date ."/". $month;



}
// Bullshit function to get microtime working in date()

function udate($format, $utimestamp = null){
	
    if (is_null($utimestamp))
        $utimestamp = microtime(true);

    $timestamp = floor($utimestamp);
    $milliseconds = round(($utimestamp - $timestamp) * 1000000);

    return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}

// echo udate('H:i:s.u'); // 19:40:56.78128

//Function sets the first letter of the string $str to upper case...
function first_letter($str) {
    return strtoupper(substr($str, 0, 1)).substr($str, 1);
}



?>