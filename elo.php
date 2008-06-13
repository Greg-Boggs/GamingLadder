<?php 

$p1 = 1500;
$p2= 1500;
$p1win = 12;
$y = 1;

while ($y >= 0) {
	
// nevermind te funtrcion names...

	$p1win = EloMasterWins($p1,$p2);
	$p2win = EloNewbWins($p1,$p2);

/* this modifies it all... *

	$p1modifier = 1; 
	$p2modifier = 1;
	
	
	if (($p1 > $p2)  && (($p1 - $p2) <= 100 ) ) { $p1modifier = 1;}
	if (($p1 > $p2)  && (($p1 - $p2) >= 101 ) ) { $p1modifier = 0.5;}
	if (($p1 > $p2)  && (($p1 - $p2) >= 150 ) ) { $p1modifier = 0.25;}	
	if (($p1 > $p2)  && (($p1 - $p2) >= 200 ) ) { $p1modifier = 0;}
		
	$newp1win = floor($p1win* $p1modifier);
	
		
	if (($p2 > $p1)  && (($p2 - $p1) <= 100 ) ) { $p2modifier = 1;}
	if (($p2 > $p1)  && (($p2 - $p1) >= 101 ) ) { $p2modifier = 0.5;}
	if (($p2 > $p1)  && (($p2 - $p1) >= 150 ) ) { $p2modifier = 0.25;}	
	if (($p2 > $p1)  && (($p2 - $p1) >= 200 ) ) { $p2modifier = 0;}
		
	$newp2win = floor($p2win* $p2modifier);
*/

// shos mod stuff ... echo "<b>$p1</b> new <i>($newp1win)</i> old ($p1win) VS <b>$p2</b> new <i>($newp2win)</i> old ($p2win)<br>";

$oldp1 = $p1;
$oldp2 = $p2;

	$p1 = $p1 + $p1win;
	$p2 = $p2 - $p1win;
	
$x++;

echo "<b>$x :</b> $oldp1 won over $oldp2 | new ratings: $p1 / $p2  | points moved: $p1win p<br>";
$y = $p1win;
	
}




function EloNewbWins($oldratingloser, $oldratingwinner) {			
		
			$constant = 24;	
		$rw1 = $oldratingwinner - $oldratingloser;
		$rw2 = -$rw1/400;		
		$rw3 = pow(10,$rw2);		
		$rw4 = $rw3 + 1;		
		$rw5 = 1/$rw4;		
		$rw6 = 1 - $rw5;		
		$rw7 = $constant * $rw6;		
		return round($rw7); // hur många p man får, $ratingdifferent

}

function EloMasterWins($oldratingloser, $oldratingwinner) {			
		
		$constant = 24;	
		$rw1 = $oldratingloser - $oldratingwinner;
		$rw2 = -$rw1/400;		
		$rw3 = pow(10,$rw2);		
		$rw4 = $rw3 + 1;		
		$rw5 = 1/$rw4;		
		$rw6 = 1 - $rw5;		
		$rw7 = $constant * $rw6;		
		return round($rw7); // hur många p man får, $ratingdifferent

}



?>