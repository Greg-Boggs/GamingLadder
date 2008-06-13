<?
//start

//configure database info
 $adminemail = "spam@eyerouge.com";
 $databaseserver = "localhost"; //usually localhost
 $databasename = "yourinfo"; //the name of your database
 $databaseuser = "yourinfo"; //the name of the database-user
 $databasepass = "yourinfo"; // the password to your database
 $directory ="http://yourinfo.com" ; //the location of your ladder directory (no trailing slash)


//configure the tables in the database
 $deletedgames = "webl_deleted_games";
 $playerstable = "webl_players"; //the name of the table that contains information about the players
 $gamestable = "webl_games"; //the name of the table that stores the played games
 $newstable = "webl_news"; // the name of the table that stores the news
 $varstable = "webl_vars"; //the name of the table that stores various information
 $admintable = "webl_admin"; //name of the table that stores the admin login information
 $pagestable = "webl_pages"; //name of the table that stores additional pages
 $waitingtable = "webl_waiting"; //name of the table that stores the players waiting for a game...
 //junk? $wesnothdir = "/foobardir"; //relative path to the site dir, with / at the start

//config security
$salt = "yourinfo"; // Notice: If you have a running ladder with registered users DONT ever change the salt! Nobody will be able to login then, ever, if you lose the salt.


//config etc
$titlebar = "Wesnoth Ladder - Competitive Gaming Ladder";
$numindexnewbs = 3; // number of newcomers to show in index sidebar..
$numindexresults = 10; //num of latest played games results to show in index sidebar
$numindexhiscore = 10; //number of people listed in the hiscore in the index sidebar
$numindexnews = 10; // news items displayed at index bottom...
$newsitems = 3; //news items to show full text of...this needs to be fixed - index shows 3 even if it says 5 in here.. lol
$numindexdeled = 5; // how many deleted games are shown at index.

// redundant... remove if all works as it should: $kvalue = 32; // Which is the K-value in the Elo formula?


// Don't change the values below unless you have some basic understanding of how Elo works, the settings that are there already are okey for most people.  

$passivedays = 30; // number of days a player has to play a game before he's put in passive rating mode
$ladderminelo = 1300; // all players that have this or higher elo rating will be listed in the ladder... the rest are not _visible_ in it.
$gamestorank = 10; // number of games a player must have played in order to get a ranking (not the same as getting an elo rating!)
$playersshown = 200; //Number of players to show per php query on stats

define("BOTTOM_K_VAL", 32); //Max rating per game for players < 1800
define("MIDDLE_K_VAL", 24); //Max rating per game for players < 2100
define("TOP_K_VAL", 16); // Max rating per game for top players.

define("MIDDLE_RATING", 2100); //this is the highest rating you can have before becoming a top player
define("BOTTOM_RATING", 1800); //this is the highest rating you can have before becoming a middle player

define("BASE_RATING", 1500);    // this is what a new player gets
define("MAX_DIFFERENCE", 1000); //1200 is so high this never gets used
define("PROVISIONAL", 10); //The number of games it takes to not be provisional anymore 
define("PROVISIONAL_PROTECTION", 2); //The amount provisional rating is divided by.

$passivedaysd2 = 60;
$ladderminelod2 = 1499;
$gamestorankd2 = 2;



//finish
?>
