<?php


// Ladder of Wesnoth Search
// v.0.01   


// include ("logincheck.inc.php");
require('conf/variables.php');
$page = "search";

?>


<?php

// All this happens after the user presses the submit button ----------

if ($_GET[submit]) {





// Countries
	
	if ($_GET[Country] != "") {

	
		$country = $_GET[Country];
	
		// partial working match mysql example
		// "SELECT * FROM product WHERE product_id LIKE '%" . $product . "%'"
		
		
		$sql="SELECT * FROM $playerstable WHERE  country='$country' ORDER BY rating DESC, totalgames DESC";
		$result=mysql_query($sql,$db);
		
		$num = mysql_num_rows($result);
	
		echo "Number of players from $_GET[Country]: $num";

		while ($poop = mysql_fetch_array($result))
		{
			echo $poop['name'];
			echo " ";
			echo $poop['rating'];
			echo "<br>";
			echo "<br>";
		}
	}

//And we display the results



}

// Submitt button events stop here.-------------------------------------



?>
<form method="GET">
<table>

<tr><td><p class="text"><b>Win %</b></p></td>

<td>&nbsp;<select size="1" name="RFromO" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<option>>=</option>
<option><=</option>
</select></td>


<td>
<select size="1" name="RFromV" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<option></option>
<option>10</option>
<option>20</option>
<option>30</option>
<option>40</option>
<option>50</option>
<option>60</option>
<option>70</option>
<option>80</option>
<option>90</option>
</select>%
</td>

</tr>

<tr><TD>Country:</TD>
<td>
<select size="1" name="Country" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text">
<?php include ("countries.inc.php"); ?>
</select>
</td>
</tr>

</table>
<input type="Submit" name="submit" value="Submit." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text">

</form>

