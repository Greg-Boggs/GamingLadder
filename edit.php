<?php

// v. 1.07

$page = "edit";
require('variables.php');
require('variablesdb.php');
require('top.php');
?>

<?php 

// Lets check to see if there are Ladder cookies to see if the user is logged in. If so, we wont show the login box....


// First we extract the info from the cookies... There are 2 of them, one containing username, other one the password.

	if (isset($_COOKIE["LadderofWesnoth1"]) AND isset($_COOKIE["LadderofWesnoth2"])) {
		//DEB echo "2 Cookies are set..."; 
		
			
		$nameincookie = $_COOKIE["LadderofWesnoth1"];
		$passincookie =  $_COOKIE["LadderofWesnoth2"];
		

	
		// Now lets compare the cookies with the database. If there is a playername and pass that corresponds, he's logged in...
		$sql = "SELECT * FROM $playerstable WHERE name='$nameincookie' AND passworddb='$passincookie'";
		$result = mysql_query($sql,$db);
		$bajs = mysql_fetch_array($result); 
		
		//DEB echo "<div align='right'>Vnameincookie: $nameincookie</div>";
		//DEB echo "bajsplayerid: $bajs[name] $bajs[player_id]";
		
			if ($bajs[player_id] > 0) { 
				$loggedin = 1;
			} else { $loggedin = 0; }
		}


if ($loggedin == 0) {
	
	echo "<h1>Oops...</h1><br><p>You're trying to edit a profile, but for that to happen you must first be logged in, must you not?</p>";
	require('bottom.php');
	exit;
}
	

?>

<?php echo "<br><h2>$nameincookie</h2>"; ?>

<?php


	
if ($_POST[submit]) {
	
	// Lets generate the encrypted pass, after all, its the one thats stored in the database... we do it by applying the salt and hashing it twice.
// We need to take the users real pass, "encrypt" it the same way we did when he registered, and then compare the results.
// $salt is read from config file

$passworddb2 = $salt.$_POST[passworddb];
$passworddb2 = md5($passworddb2); 
$passworddb2 = md5($passworddb2); 




	
	
	
$sql="SELECT * FROM $playerstable WHERE name='$nameincookie' AND passworddb = '$passworddb2'";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
if ($num > 0) {
$msn = trim(strip_tags($_POST[msn]));
$icq = trim(strip_tags($_POST[icq]));
$aim = trim(strip_tags($_POST[aim]));
$mail = trim(strip_tags($_POST[mail]));


	if ($_POST[MonM] != "") {$CanPlay = "$CanPlay $_POST[MonM]";}
	if ($_POST[MonN] != "") {$CanPlay = "$CanPlay $_POST[MonN]";}
	if ($_POST[MonA] != "") {$CanPlay = "$CanPlay $_POST[MonA]";}
	if ($_POST[MonE] != "") {$CanPlay = "$CanPlay $_POST[MonE]";}
	if ($_POST[MonNi] != "") {$CanPlay = "$CanPlay $_POST[MonNi]";}
	
	if ($_POST[TueM] != "") {$CanPlay = "$CanPlay $_POST[TueM]";}
	if ($_POST[TueN] != "") {$CanPlay = "$CanPlay $_POST[TueN]";}
	if ($_POST[TueA] != "") {$CanPlay = "$CanPlay $_POST[TueA]";}
	if ($_POST[TueE] != "") {$CanPlay = "$CanPlay $_POST[TueE]";}
	if ($_POST[TueNi] != "") {$CanPlay = "$CanPlay $_POST[TueNi]";}
	
	if ($_POST[WedM] != "") {$CanPlay = "$CanPlay $_POST[WedM]";}
	if ($_POST[WedN] != "") {$CanPlay = "$CanPlay $_POST[WedN]";}
	if ($_POST[WedA] != "") {$CanPlay = "$CanPlay $_POST[WedA]";}
	if ($_POST[WedE] != "") {$CanPlay = "$CanPlay $_POST[WedE]";}
	if ($_POST[WedNi] != "") {$CanPlay = "$CanPlay $_POST[WedNi]";}
	
	if ($_POST[ThuM] != "") {$CanPlay = "$CanPlay $_POST[ThuM]";}
	if ($_POST[ThuN] != "") {$CanPlay = "$CanPlay $_POST[ThuN]";}
	if ($_POST[ThuA] != "") {$CanPlay = "$CanPlay $_POST[ThuA]";}
	if ($_POST[ThuE] != "") {$CanPlay = "$CanPlay $_POST[ThuE]";}
	if ($_POST[ThuNi] != "") {$CanPlay = "$CanPlay $_POST[ThuNi]";}
	
		
	if ($_POST[FriM] != "") {$CanPlay = "$CanPlay $_POST[FriM]";}
	if ($_POST[FriN] != "") {$CanPlay = "$CanPlay $_POST[FriN]";}
	if ($_POST[FriA] != "") {$CanPlay = "$CanPlay $_POST[FriA]";}
	if ($_POST[FriE] != "") {$CanPlay = "$CanPlay $_POST[FriE]";}
	if ($_POST[FriNi] != "") {$CanPlay = "$CanPlay $_POST[FriNi]";}
			
	if ($_POST[SatM] != "") {$CanPlay = "$CanPlay $_POST[SatM]";}
	if ($_POST[SatN] != "") {$CanPlay = "$CanPlay $_POST[SatN]";}
	if ($_POST[SatA] != "") {$CanPlay = "$CanPlay $_POST[SatA]";}
	if ($_POST[SatE] != "") {$CanPlay = "$CanPlay $_POST[SatE]";}
	if ($_POST[SatNi] != "") {$CanPlay = "$CanPlay $_POST[SatNi]";}
		
	if ($_POST[SunM] != "") {$CanPlay = "$CanPlay $_POST[SunM]";}
	if ($_POST[SunN] != "") {$CanPlay = "$CanPlay $_POST[SunN]";}
	if ($_POST[SunA] != "") {$CanPlay = "$CanPlay $_POST[SunA]";}
	if ($_POST[SunE] != "") {$CanPlay = "$CanPlay $_POST[SunE]";}
	if ($_POST[SunNi] != "") {$CanPlay = "$CanPlay $_POST[SunNi]";}		


// check if the user wants a new password than the old. 

if  ($_POST[newpassworddb] == $_POST[newpassworddb2]) {
	// if the same new pass was entered twice he should get a new pass.... one will be generated now:
	
	
	
if ($_POST[newpassworddb] != "" && $_POST[newpassworddb2] != "") {
		
		
		// Apparently he wants a new pass... let's compare the new pass verification:
		
		
		$newpassworddb = $salt.$_POST[newpassworddb];
		$newpassworddb = md5($newpassworddb); 
		$newpassworddb = md5($newpassworddb); 
		
		}
	
	
	} else { echo "New password verification didn't match. Please re-type and verify the new password."; exit;}



// Depending on if he wants a new pass or wants to keep the old we need 2 different sql queries...

if ($newpassworddb && $newpassworddb != "")  {

$sql = "UPDATE $playerstable SET mail = '$mail', icq = '$icq', aim = '$aim', msn = '$msn', country = '$_POST[country]', Avatar = '$_POST[avatar]', MsgMe = '$_POST[msgme]', HaveVersion = '$_POST[version]', CanPlay = '$CanPlay', Jabber = '$_POST[jabber]', passworddb = '$newpassworddb' WHERE name='$nameincookie' AND passworddb = '$passworddb2'"; } else {

$sql = "UPDATE $playerstable SET mail = '$mail', icq = '$icq', aim = '$aim', msn = '$msn', country = '$_POST[country]', Avatar = '$_POST[avatar]', MsgMe = '$_POST[msgme]', HaveVersion = '$_POST[version]', CanPlay = '$CanPlay', Jabber = '$_POST[jabber]' WHERE name='$nameincookie' AND passworddb = '$passworddb2'"; }


$result = mysql_query($sql);
echo "<p class='text'>Profile is now updated.</p>";
if ($newpassworddb && $newpassworddb !="") { echo "<p class='text'><br>Since you changed password you have been loged out. Please login with your new password.</p>"; }

}
else {
echo "<p class='text'>The password you entered is incorrect.<br><br><a href='edit.php'>Please try again.</a></p>";
}
} else {
?>
<?php
$sql="SELECT * FROM $playerstable WHERE name = '$nameincookie' ORDER BY name ASC";
$result=mysql_query($sql,$db);
$row = mysql_fetch_array($result);
?>
<form method="post">
<table border="0" cellpadding="0">

<tr>
<td>
	<input type=hidden name="editname" value="<?echo $nameincookie?>">
</td>
</tr>

<tr>
<td><p class="text"><b>Password:</b></p></td>
<td><input type="password" size="15" name="passworddb" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"> You <i>must</i> enter this to save settings.</td>
</tr>

<tr>
<td><p class="text">New Password:</p></td>
<td><input type="password" size="15" name="newpassworddb" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"> Leave empty unless changing password.</td>
</tr>


<tr>
<td><p class="text">Verify New Password:</p></td>
<td><input type="password" size="15" name="newpassworddb2" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>



<tr>
<td><p class="text"><b>Mail:</b></p></td>
<td><input type="Text" name="mail" value="<?echo "$row[mail]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>

<tr>
<td><p class="text">Jabber:</p></td>
<td><input type="Text" name="jabber" value="<?echo "$row[Jabber]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"> Open source, Same as g-mail chat.</td>
</tr>

<tr>
<td><p class="text">Icq:</p></td>
<td><input type="Text" name="icq" value="<?echo "$row[icq]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Aim:</p></td>
<td><input type="Text" name="aim" value="<?echo "$row[aim]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Msn:</p></td>
<td><input type="Text" name="msn" value="<?php echo "$row[msn]" ?>" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></td>
</tr>
<tr>
<td><p class="text">Country:</p></td>
<td><select size="1" name="country" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">
<option selected><?php echo "$row[country]" ?></option>
<option>No country</option>
<?php include ("countries.inc"); ?>
</select></td>
</tr><tr><td><p class="text">Avatar:</p></td>
<td>&nbsp;<select size="1" name="avatar" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"><option selected><?php echo "$row[Avatar]" ?></option>
<?php include 'avatars.inc'; ?>
</select></td></tr>



<tr><td><p class="text"><b>My version of Wesnoth:</b></p></td>

<td>&nbsp;<select size="1" name="version" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<?php echo "<option>$row[HaveVersion]</option>"; ?>
<option>Stable</option>
<option>Development</option>
<option>Both</option>
</select></td></tr>

<tr><td><p class="text">Msg me to play:</p></td>
<td>&nbsp;<select size="1" name="msgme" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<?php echo "<option>$row[MsgMe]</option>"; ?>
<option>Yes</option>
<option>No</option>
</select></td></tr>
</table>



<p class="text">I can usually play these times (GMT):</p>
<table width="100%">


<tr>

<td></td>
<td>Morning</td>
<td>Noon</td>
<td>Afternoon</td>
<td>Evening</td>
<td>Night</td>
</tr>



<tr>
<td bgcolor="#E7D9C0">Monday</td>


<td bgcolor="#E7D9C0"><input type="checkbox" name="MonM" value="MonM" <?php $pos1 = strpos("$row[CanPlay]", "MonM"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>


<td bgcolor="#E7D9C0"><input type="checkbox" name="MonN" value="MonN" <?php $pos1 = strpos("$row[CanPlay]", "MonN"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="MonA" value="MonA" <?php $pos1 = strpos("$row[CanPlay]", "MonA"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="MonE" value="MonE" <?php $pos1 = strpos("$row[CanPlay]", "MonE"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="MonNi" value="MonG" <?php $pos1 = strpos("$row[CanPlay]", "MonG"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
</tr>



<tr>
<td>Tuesday</td>
<td><input type="checkbox" name="TueM" value="TueM" <?php $pos1 = strpos("$row[CanPlay]", "TueM"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="TueN" value="TueN" <?php $pos1 = strpos("$row[CanPlay]", "TueN"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="TueA" value="TueA" <?php $pos1 = strpos("$row[CanPlay]", "TueA"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="TueE" value="TueE" <?php $pos1 = strpos("$row[CanPlay]", "TueE"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="TueNi" value="TueG" <?php $pos1 = strpos("$row[CanPlay]", "TueG"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
</tr>


<tr>
<td bgcolor="#E7D9C0">Wednesday</td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedM" value="WedM" <?php $pos1 = strpos("$row[CanPlay]", "WedM"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedN" value="WedN" <?php $pos1 = strpos("$row[CanPlay]", "WedN"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedA" value="WedA" <?php $pos1 = strpos("$row[CanPlay]", "WedA"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedE" value="WedE" <?php $pos1 = strpos("$row[CanPlay]", "WedE"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="WedNi" value="WedG" <?php $pos1 = strpos("$row[CanPlay]", "WedG"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
</tr>

<tr>
<td>Thursday</td>
<td><input type="checkbox" name="ThuM" value="ThuM" <?php $pos1 = strpos("$row[CanPlay]", "ThuM"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="ThuN" value="ThuN" <?php $pos1 = strpos("$row[CanPlay]", "ThuN"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="ThuA" value="ThuA" <?php $pos1 = strpos("$row[CanPlay]", "ThuA"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="ThuE" value="ThuE" <?php $pos1 = strpos("$row[CanPlay]", "ThuE"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="ThuNi" value="ThuG" <?php $pos1 = strpos("$row[CanPlay]", "ThuG"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
</tr>

<tr>
<td bgcolor="#E7D9C0">Friday</td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriM" value="FriM" <?php $pos1 = strpos("$row[CanPlay]", "FriM"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriN" value="FriN" <?php $pos1 = strpos("$row[CanPlay]", "FriN"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriA" value="FriA" <?php $pos1 = strpos("$row[CanPlay]", "FriA"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriE" value="FriE" <?php $pos1 = strpos("$row[CanPlay]", "FriE"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="FriNi" value="FriG" <?php $pos1 = strpos("$row[CanPlay]", "FriG"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
</tr>

<tr>
<td>Saturday</td>
<td><input type="checkbox" name="SatM" value="SatM" <?php $pos1 = strpos("$row[CanPlay]", "SatM"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="SatN" value="SatN" <?php $pos1 = strpos("$row[CanPlay]", "SatN"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="SatA" value="SatA" <?php $pos1 = strpos("$row[CanPlay]", "SatA"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="SatE" value="SatE" <?php $pos1 = strpos("$row[CanPlay]", "SatE"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td><input type="checkbox" name="SatNi" value="SatG" <?php $pos1 = strpos("$row[CanPlay]", "SatG"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
</tr>

<tr>
<td bgcolor="#E7D9C0">Sunday</td> 
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunM" value="SunM" <?php $pos1 = strpos("$row[CanPlay]", "SunM"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunN" value="SunN" <?php $pos1 = strpos("$row[CanPlay]", "SunN"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunA" value="SunA" <?php $pos1 = strpos("$row[CanPlay]", "SunA"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunE" value="SunE" <?php $pos1 = strpos("$row[CanPlay]", "SunE"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>
<td bgcolor="#E7D9C0"><input type="checkbox" name="SunNi" value="SunG" <?php $pos1 = strpos("$row[CanPlay]", "SunG"); if ($pos1 != FALSE) { echo "checked"; }?>/></td>

</table>

<p><input type="Submit" name="submit" value="Submit." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>

</form>
<?php
}


require('bottom.php');
?>