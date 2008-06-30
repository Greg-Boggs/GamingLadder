<?php 

// RSS Feed - Newest Players
// Version 1.09


$page = "rssfeed";
$time=time();
require('conf/variables.php');
require_once 'rss_generator.inc.php';


//	if ($row[Confirmation] != "" AND $row[Confirmation] != "Ok")

$sql="SELECT name, country, joined FROM $playerstable ORDER BY player_id DESC LIMIT 0,20";
$result=mysql_query($sql,$db);


// Lets start using the class...


  $rss_channel = new rssGenerator_channel();
  $rss_channel->atomLinkHref = '';
  $rss_channel->title = 'New PLayers';
  $rss_channel->link = 'http://ladder.subversiva.org';
  $rss_channel->description = 'The newest players at Ladder of Wesnoth.';
  $rss_channel->language = 'en-us';
  $rss_channel->generator = 'PHP RSS Feed Generator';
  $rss_channel->managingEditor = 'spam@eyerouge.com (eye)';
  $rss_channel->webMaster = 'spam@eyerouge.com (eye)';

while ($row = mysql_fetch_array($result)) {
  $item = new rssGenerator_item();
  $item->title = $row[name];
  $describtiontxt =  "";
	
	// What, except for the name, will we show in the description?

	if ($row[country] != "No country") { $describtiontxt = $describtiontxt . " - " . $row[country]; }

	// if ($row[LastGame] != "") { $describtiontxt = $describtiontxt . "<br>Last game: " . $row[LastGame]; }

  $item->description = $describtiontxt;
  $item->link = 'http://ladder.subversiva.org/profile.php?name='. $row[name];
  $item->guid = 'http://ladder.subversiva.org/profile.php?name='. $row[name];
  //$item->pubDate = 'Tue, 07 Mar 2006 00:00:01 GMT';
$item->pubDate = date("D, d M Y H:i:s", $row['joined']) . " GMT";
  $rss_channel->items[] = $item;
}



  $rss_feed = new rssGenerator_rss();
  $rss_feed->encoding = 'UTF-8';
  $rss_feed->version = '2.0';
  header('Content-Type: text/xml');
  echo $rss_feed->createFeed($rss_channel);


?>
