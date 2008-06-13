<?php 

require('conf/variables.php');
$sql="set @num  = 0";
$result=mysql_query($sql,$db);
$sql="select * from (
select
   @num := @num + 1 as rank,
   name,
   rating
from webl_players
order by rating desc) as A";

$result=mysql_query($sql,$db);
$cur=1;
$odd = 1;
while ($row = mysql_fetch_array($result)) {

echo $row['name'] .",". $row['rank']. ", " . $row['rating'] . "</br>\n";
}

require('bottom.php');
?>
