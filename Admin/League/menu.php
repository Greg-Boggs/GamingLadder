<p class="textalt">Go to:
<?
if ($page =="index"){
echo"<a href=../index.php><font color='$color4'>admin index</font></a>";
}else{
echo"<a href=../index.php>admin index</a>";
}
echo" | ";
if ($approve == "yes") {
if ($page =="adduser"){
echo"<a href=adduser.php><font color='$color4'>add player</font></a>";
}else{
echo"<a href=adduser.php>add player</a>";
}
echo" | ";
}
if ($page =="edituser"){
echo"<a href=edituser.php><font color='$color4'>edit player</font></a>";
}else{
echo"<a href=edituser.php>edit player</a>";
}
echo" | ";
if ($page =="blockuser"){
echo"<a href=blockuser.php><font color='$color4'>block player</font></a>";
}else{
echo"<a href=blockuser.php>block player</a>";
}
echo" | ";
if ($page =="deleteuser"){
echo"<a href=deleteuser.php><font color='$color4'>delete player</font></a>";
}else{
echo"<a href=deleteuser.php>delete player</a>";
}
echo" | ";
if ($page =="report"){
echo"<a href=report.php><font color='$color4'>report</font></a>";
}else{
echo"<a href=report.php>report</a>";
}
echo" | ";
if ($approvegames == "yes") {
if ($page =="addgame"){
echo"<a href=addgame.php><font color='$color4'>record game</font></a>";
}else{
echo"<a href=addgame.php>record game</a>";
}
echo" | ";
}
if ($page =="deletegame"){
echo"<a href=deletegame.php?startgames=0&finishgames=$numgamespage><font color='$color4'>delete game</font></a>";
}else{
echo"<a href=deletegame.php?startgames=0&finishgames=$numgamespage>delete game</a>";
}
echo" | ";
if ($page =="ip"){
echo"<a href=ip.php><font color='$color4'>ip check</font></a>";
}else{
echo"<a href=ip.php>ip check</a>";
}
echo" | ";
if ($page =="resetseason"){
echo"<a href=resetseason.php><font color='$color4'>reset season</font></a>";
}else{
echo"<a href=resetseason.php>reset season</a>";
}
echo" | ";
?>
</p>