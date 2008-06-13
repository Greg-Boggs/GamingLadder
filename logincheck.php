<?php
//logincheck.php
	$current_player = $nameincookie;
	$sql="SELECT * FROM $playerstable WHERE name = '$current_player'";
	$result=mysql_query($sql,$db);
	$row = mysql_fetch_array($result);

	if ($row[mail] == "") {
		echo "In order for the ladder to work smoothly the admin needs your mail. It won't be sold or spammed, so please update your profile with your <i>correct</i> e-mail. Don't worry about other players mailing you, you can hide it in the settings if you wish to. <b>You won't be able to report games untill this has been done.</b> Please contact us if you have any questions or need help with anything.";
		exit;
		}

	if ($row[Confirmation] != "" AND $row[Confirmation] != "Ok") {
		//deb echo "This is the confirmation key from the database: $row[Confirmation]<br />";
		
		echo "<b>You cant report games because you have not activated your account.</b><br /><br />When you registered a mail was sent to you containing an activation link. Please find that mail and click the activation link. Dont forget to check your spam / trash bin, as some services wrongly flag our activation mail as spam. Contact admin if you have missplaced your activation email.";
		require('bottom.php');
		exit;
		}

	if ($row[Confirmation] != "" OR "Ok") {
		echo "Your account is not activated. Please check your mail for the activation link that was sent to you when you registered.<br /> Dont forget to check your spam / trash bin, as some services falsly flag our activation mail as spam.";
		exit;
	}
		
		if ($row[mail] == "") {
		echo "In order for the ladder to work smoothly the admin needs your mail. It won't be sold or spammed, so please update your profile with your <i>correct</i> e-mail. Don't worry about other players mailing you, you can hide it in the settings if you wish to. <b>You won't be able to report games untill this has been done.</b> Please contact us if you have any questions or need help with anything.";
		exit;
		}
?>

