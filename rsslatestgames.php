<?php 

// RSS Feed -Latest Games
// Version 1.00


$page = "rssfeed";
$time=time();
require('variables.php');
require('variablesdb.php');
require_once 'rss_generator.inc.php';


//	if ($row[Confirmation] != "" AND $row[Confirmation] != "Ok")

$sql="SELECT winner, loser, date, elo_change FROM $gamestable ORDER BY game_id DESC LIMIT 0,20";
$result=mysql_query($sql,$db);


// Lets start using the class...


  $rss_channel = new rssGenerator_channel();
  $rss_channel->atomLinkHref = '';
  $rss_channel->title = 'Recent Games';
  $rss_channel->link = 'http://ladder.subversiva.org';
  $rss_channel->description = 'The newest games at Ladder of Wesnoth.';
  $rss_channel->language = 'en-us';
  $rss_channel->generator = 'PHP RSS Feed Generator';
  $rss_channel->managingEditor = 'spam@eyerouge.com (eyerouge)';
  $rss_channel->webMaster = 'spam@eyerouge.com (eyerouge)';

while ($row = mysql_fetch_array($result)) {
  $item = new rssGenerator_item();
  $item->title = $row[winner] ." beats ". $row[loser];
  $describtiontxt =  $row[date] . " / " . $row[RankMove] . " p";
	
	
  $item->description = $describtiontxt;
  $item->link = 'http://ladder.subversiva.org/profile.php?name='. $row[winner];
  $item->guid = 'http://fake.com'. rand(1,9999);
  //$item->pubDate = 'Tue, 07 Mar 2006 00:00:01 GMT';
$item->pubDate = date("D, d M Y H:i:s") . " GMT";
  $rss_channel->items[] = $item;
}



  $rss_feed = new rssGenerator_rss();
  $rss_feed->encoding = 'UTF-8';
  $rss_feed->version = '2.0';
  header('Content-Type: text/xml');
  echo $rss_feed->createFeed($rss_channel);


?>