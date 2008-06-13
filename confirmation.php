<?
$page = "index";
require('variables.php');
require('variablesdb.php');
require('top.php');

// $query = mysql_query("SELECT * FROM student");
// $number=mysql_num_rows($query);
// echo "Total records in Student table= ". $number; 

?>



	<?php
	
	
	

	
	
// Passkey that got from the link th euser clicked / got in his verification mail
$passkey=$_GET['passkey'];

//deb echo "Using $passkey as passkey...<br />";

// Retrieve data from table where row that match this passkey
$sql1="SELECT * FROM $playerstable WHERE Confirmation ='$passkey'";
$result1=mysql_query($sql1,$db);

//deb echo "pass 1 - rsult1 = $result1";

// If successfully queried
if($result1){

//deb echo "pass 2";
	
	// Count how many row has this passkey
	$count=mysql_num_rows($result1);
		
		// if found this passkey in our database >>
		if($count==1){
		//deb  echo "Found the passkey!";
		
		
		// Change the users passkey to a Ok, as a sign of the user passing the registration
		
		
		// "UPDATE example SET age='22' WHERE age='21'"
		
		// // Shows the numner of seconds since 70.
		$timesec = date("U");
		
		// $sql2="UPDATE $playerstable SET Confirmation='Ok' WHERE Confirmation ='$passkey'";
		

		$sql2="UPDATE $playerstable SET Confirmation='Ok', Joined='$timesec' WHERE Confirmation ='$passkey'";
		$result2=mysql_query($sql2, $db);
		
		if($result2){
		//deb  echo "Passkey was used successfully & cleared.";
		?>
		
		<br>
		<img align="center" src="activated.jpg">
		
		<?php
		
			}
			else {
			
			echo "Database Error: Couldnt clear validation key. Please retry or contact admin if the problem remains.";
			}
		
			
				
		
		}
		
		else {
			echo "1) Either you have already verfied once, in which case you can now login with your user & pass, <i>or</i> 2) you supplied us with the wrong Confirmation code, in which case you should please check if you used the very exact verification link sent to you by mail and try again.";
		}
		
		


}
	
	
	?>


<?php
require('bottom.php');
?>