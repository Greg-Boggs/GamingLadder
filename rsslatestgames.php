<?php

// RSS Feed -Latest Games
// Version 1.00


$page = "rssfeed";
$time = time();
require('conf/variables.php');
require_once 'rss_generator.inc.php';

$selfUrl = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ? 'http://' : 'https://');
$selfUrl .= $_SERVER['HTTP_HOST'];

//	if ($row[Confirmation] != "" AND $row[Confirmation] != "Ok")

$sql = "SELECT DATE_FORMAT(reported_on, '%a, %d %b %Y %H:%i:%s') as pubdate, winner, loser, winner_elo, loser_elo, reported_on, winner_points, loser_points FROM $gamestable ORDER BY reported_on DESC LIMIT 0,20";
$result = mysqli_query($db, $sql);


// Lets start using the class...


$rss_channel = new rssGenerator_channel();
$rss_channel->atomLinkHref = '';
$rss_channel->title = 'Recent Games';
$rss_channel->link = $selfUrl;
$rss_channel->description = 'The newest games at Ladder of Wesnoth.';
$rss_channel->language = 'en-us';
$rss_channel->generator = 'PHP RSS Feed Generator';
$rss_channel->managingEditor = 'spam@eyerouge.com (eyerouge)';
$rss_channel->webMaster = 'spam@eyerouge.com (eyerouge)';

while ($row = mysqli_fetch_array($result)) {
    $item = new rssGenerator_item();
    $item->title = $row['winner'] . " beats " . $row['loser'];
    $describtiontxt = $row['winner'] . " " . $row['winner_elo'] . " (" . $row['winner_points'] . "), ";
    $describtiontxt .= $row['loser'] . " " . $row['loser_elo'] . " (" . $row['loser_points'] . ") ";
    $describtiontxt .= $row['pubdate'] . " GMT";


    $item->description = $describtiontxt;
    $item->link = $selfUrl . '/profile.php?name=' . $row['winner'];
    $item->guid = 'http://fake.com' . rand(1, 9999);
    //$item->pubDate = 'Tue, 07 Mar 2006 00:00:01 GMT';
    $item->pubDate = $row['pubdate'] . " GMT";
    $rss_channel->items[] = $item;
}


$rss_feed = new rssGenerator_rss();
$rss_feed->encoding = 'UTF-8';
$rss_feed->version = '2.0';
header('Content-Type: text/xml');
echo $rss_feed->createFeed($rss_channel);


?>
