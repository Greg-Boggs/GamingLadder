<p class="textalt">Go to:
<?
if ($page =="index"){
echo"<a href=index.php><font color='$color4'>index</font></a>";
}else{
echo"<a href=index.php>index</a>";
}
echo" | ";
if ($page =="addadmin"){
echo"<a href=addadmin.php><font color='$color4'>add admin</font></a>";
}else{
echo"<a href=addadmin.php>add admin</a>";
}
echo" | ";
if ($page =="news"){
echo"<a href=News/index.php><font color='$color4'>news</font></a>";
}else{
echo"<a href=News/index.php>news</a>";
}
echo" | ";
if ($page =="league"){
echo"<a href=League/index.php><font color='$color4'>league manegement</font></a>";
}else{
echo"<a href=League/index.php>league manegement</a>";
}
echo" | ";
if ($page =="settings"){
echo"<a href=settings.php><font color='$color4'>settings</font></a>";
}else{
echo"<a href=settings.php>settings</a>";
}
echo" | ";
if ($page =="post"){
echo"<a href=post.php><font color='$color4'>add page</font></a>";
}else{
echo"<a href=post.php>add page</a>";
}
echo" | ";
if ($page =="view"){
echo"<a href=view.php><font color='$color4'>view pages</font></a>";
}else{
echo"<a href=view.php>view pages</a>";
}
echo" | <a href=rerankladder.php>rerank ladder</a>";
?>
</p>