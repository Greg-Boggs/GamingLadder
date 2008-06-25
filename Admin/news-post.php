<?PHP
session_start();
$GLOBALS['prefix'] = "../";
require('./../conf/variables.php');
require_once 'security.inc.php';
require('./../top.php');

if ($_POST[submit]) {
    $sql = "INSERT INTO $newstable (date, title, news) VALUES ('$_POST[date]', '$_POST[titlenews]', '$_POST[news]')";
	$result = mysql_query($sql);
	echo "<p class='header'>News posted.</p>";
} else {
?>
<p class="header">Post news.</p>
<form method="post" action="news-post.php">
<table border="0" cellpadding="0" width="100%">
<tr>
<td><p class="text">Date:</p></td>
</tr>
<tr>
<td><input type="Text" size="45" name="date" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br></td>
</tr>
<tr>
<td><p class="text">Title:</p></td>
</tr>
<tr>
<td><input type="Text" size="45" name="titlenews" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br></td>
</tr>
<tr>
<td><p class="text">Text:</p></td>
</tr>
<tr>
<td valign="top"><textarea name="news" cols="45" rows="10" wrap="VIRTUAL" style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"></textarea></td>
</td>
</tr>
<tr><td>
<p class="text"><br>
<table border="1" cellspacing="1" cellpadding="2" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>">
<tr>
<td bordercolor="<?echo"$color7" ?>"><a onclick="picture()"><u>picture</u></a>
</td>
<td bordercolor="<?echo"$color7" ?>"><a onclick="ahref()"><u>link</u></a>
</td>
<td bordercolor="<?echo"$color7" ?>"><a onclick="italicThis()"><u><i>italic</i></u></a>
</td>
<td bordercolor="<?echo"$color7"?>"><a onclick="underlineThis()"><u>underline</u></a>
</td>
<td bordercolor="<?echo"$color7" ?>"><a onclick="boldThis()"><u><b>bold<b></u></a>
</td>
</tr>
</table>
</td></tr>
<tr><td height="5"></td></tr>
<tr><td>
<table border="1" cellspacing="1" cellpadding="2" bgcolor="<?echo"$color5" ?>" bordercolor="<?echo"$color1" ?>">
<tr>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/smile.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/sad.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/biggrin.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/cry.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/none.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/mad.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/rolleyes.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/laugh.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/bigrazz.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/dead.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/wink.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/bigeek.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/cool.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/no.gif" width="15" height="15"></td>
<td align="center" bordercolor="<?echo"$color7" ?>"><img border="0" src="../graphics/smileys/yes.gif" width="15" height="15"></td>
</tr>
<tr>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:)</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:(</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:d</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:'(</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:s</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:@</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:r</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:h</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:p</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:x</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">;)</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:o</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">:b</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">(n)</td>
<td align="center" bordercolor="<?echo"$color7" ?>" class="text">(y)</td>
</tr>
</table>
</td></tr>
<tr><td height="5"></td></tr>
</table>
<p class="text"><input type="Submit" name="submit" value="Post." style="background-color: <?echo"$color5" ?>; border: 1 solid <?echo"$color1" ?>" class="text"><br>
</form>
<?
}
require('./../bottom.php');
?>
