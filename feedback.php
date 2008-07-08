<?php
session_start();
require('conf/variables.php');
require('autologin.inc.php');
require('logincheck.inc.php');
require('top.php');
?>




<form name="form1" enctype="multipart/form-data" method="post" action="feedback.php">
<h3>Feedback</h3>



<table>


	<tr><td>sportsmanship</td><td><select size="1" name="sportsmanship">
		<option selected></option>
		<option>1</option>
		<option>2</option>
		<option>3</option>
		<option>4</option>
		<option>5</option>
	</select>
	</td>
		
	<br/>
	

<tr><td valign="top">
<p valign="top">game comment</p></td>
<td valign="top"><textarea name="comment" rows="5" cols="60"></textarea> </td>
</tr>
<tr><td>
	<input type="submit" name="report" value="Send Feedback" onclick="lookupAjax();" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>"/>
	</td></tr>
</table>

</form>










<?php
echo "<br /><br />";
require('bottom.php');
?>
