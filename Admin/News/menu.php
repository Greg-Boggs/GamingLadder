<p class="textalt">Go to:
<?
if ($page =="index"){
echo"<a href=../index.php><font color='$color4'>admin index</font></a>";
}else{
echo"<a href=../index.php>admin index</a>";
}
echo" | ";
if ($page =="post"){
echo"<a href=post.php><font color='$color4'>post news</font></a>";
}else{
echo"<a href=post.php>post news</a>";
}
echo" | ";
if ($page =="view"){
echo"<a href=view.php><font color='$color4'>views news</font></a>";
}else{
echo"<a href=view.php>view news</a>";
}
echo" | ";
?>
</p>