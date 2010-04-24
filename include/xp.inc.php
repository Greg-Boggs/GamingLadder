<?php
/* XP function - Calculates how much XP a player has.

   v.1.0

    The goal is to create a "fun" statistic that typicaly targets the younger audience/players.
	We don't really do anything complicated at all here and don't actually measure skills or 
    let the Xp and Lvl be skill related - on the contrary - Xp and Lvl is only supposed to measure
   __how much__ a player has played, not how well he has performed. How well he performs is
    tracked way better by the Elo value and the rest of the ladder statistics.


*/ 



function GetLvl($wins,$losses,$XpForWin,$XpForLoss,$XpToLvl1,$LvlFactor){
global $PlayerLvl, $CountingXp, $PlayerXp;

// Let's see what lvl the player is by placing his Xp on the level chart.....

$PlayerLvl= 0;
$CountingXp = $XpToLvl1;

$XpFromWins = ($wins * $XpForWin);
$XpFromLosses = ($losses * $XpForLoss);
$PlayerXp = ($XpFromLosses + $XpFromWins);
$i = 0;


while ($i == 0) {


	if ($PlayerXp > $CountingXp) {
	
		// increase the players level
		$PlayerLvl++;
	//DEB	echo "player is level : " . $PlayerLvl . "<br>";
		
		// Raise the limit for the next level
			$CurrentXpLimit = $CountingXp;
	//DEB		echo "$ countingXp: $CountingXp <br>";
			$CountingXp = $CountingXp + $XpToLvl1 + ($LvlFactor * $PlayerLvl);
			
//DEB		echo "New $ countingXp: $CountingXp <br>";
//DEB		echo "Players XP: " . $PlayerXp . "<br>";
			
		// DEB echo "<br><br>Xp for Lvl ". $PlayerLvl .": ". $CurrentXpLimit . " | Next lvl: ". $CountingXp;		
	}
	
	if ($PlayerXp <= $CountingXp) {
		// echo "<br> Found players lvl: <b> Player is lvl " . $PlayerLvl ."</b>";
		// Break xp search Loop
		$i = 1;
	//DEB	echo "<b>PlayerXp is less than CountingXP<br></b>";
		}
	
	}
	
// echo "Level: ".$PlayerLvl ."(". round($PlayerXp,0) ." - ".round((($PlayerXp/$CountingXp)*100),0)."% of ". $CountingXp. ")";

}

?>