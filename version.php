<?
$page = "versions";
require('conf/variables.php');
require('top.php');
?>
<p class="text">
<h2><b>Site development & Changes</b></h2>


<b>16 Apr 08</b>
<ul>
<li>Fix: Removed duplicate column when viewing Newcomers in statistics.</li>
<li>Added: Flags for China, Romania and Thailand were added.</li>
</ul>


<b>27 mar 08</b>
<ul>
<li>Added: Title field. Players who do something for the wesnoth community can request to get a title, viewable in their profile.</li>
<li>Fix: United Arab Emirates typo fixed in the country listing, along with the addiiton of the flag.</li>
</ul>

<b>20 mar 08</b>
<ul>
<li>Added: Lithuanian flag.</li>
<li>Added: In the profile you can now see ho wmany points a player wins in average when she wins, how many she loses in average when she loses, and how many points she gains/loses per game no matter if she lost or won it. Very interesting numbers if you get the picture.</li>
<lI>Added: In the profile, after the rating, you can now see how you rank within your Elo class.</lI>
</ul>

<b>05 mar 08</b>
<ul>
<li>Added: Ladder colours - visually shows who has played the most games, has the highest win streak and who hasn't lost a game.</li>
<li>Added: In others profile you can now see, if you are logged in, the points you would win or lose by playing against that person.</li>
<lI>Update: Registration e-mail. Now includes info about the nature of the rating system.</lI>
</ul>

<b>01 mar 08</b>
<ul>
<li>Added: Passive mode - only players who play at least one game every 30:th day will show in the ladder table.</li>
<lI>Added: The player can now, while beeing logged in and viewing her profile, see how many days she has until beeing put into passive mode. Whenever a game is played and reported the amount of days is reset to 30.</lI>
</ul>

<b>29 feb 08</b>
<ul>
<li>Update: Re-vamped the play-now list functions. It now shows on what server the player thats looking for a game can be found and also the players rating.</li>
</ul>

<b>23 feb 08</b>
<ul>
<li>Fixed: A bug that allowed players to change their passwords without verifying them and also saving them the wrong way if it happened, effectlivley locking the player out from his/her account.</li>
<li>Added: Wesnoth ingame friends list, at Docs demand. There's now an easy way to add all ladder players as friends.</li>
<li>Update: New players that signup must now use Wesnoth multiplayer legite nicknames, no spaces or frakking wicked characters allowed any more.</li>
<li>Update: A player needs to play at least 3 games to get ranked, and to have a rating of at least 1500 to be listed in the ladder table.</li>
</ul>


<b>17 dec 07</b>
<ul>
<li>Added: "I'm looking for a game"-list, which now allows you to find ladder opponents with even more ease. No hunting in chat channels or open browsers required. Just the push of a button ; )</li>
</ul>

<b>10 dec 07</b>
<ul>
<li>Update: Error message when you enter an invalid activation code, as it could actually mean that you've already verified and that it's already working.</li>
<li>Update: Rank in profile now shows nothing if you're unranked, opposed to whatever the rank was for 1500.</li>
<li>Update: Recent games in profile doesn't show if you have <1 played games. </li>
</ul>

<b>04 dec 07</b>
<ul>
<li>Added: Nauro flag</li>
<li>Added: Rebel shaman as avatar.</li>
</ul>

<b>15 nov 07</b>
<ul>
<li>Added: Rank is now seen in the profile.</li>
<li>Added: Czechoslovacias flag.</li>
<li>Update: Names of winners & losers are linkified to their profile in profile game history.</li>
<li>Update: Names of winners & losers are linkified to their profile in global game history.</li>
</ul>

<b>05 nov 07</b>
<ul>
<li>Added: Slovenia and Ukraine flags.</li>
<li>Added: Jabber field in profile.</li>
<li>Added: New password function in profile.</li>
<li>Added: + & - in point history in profile.</li>
</ul>

<b>03 nov 07</b>
<ul>
<li>Update: Way profile is displayed.</li>
<li>Added: Challenge function and link to it in profile.</li>
<li>Update: E-mail, IM:s and times when a user can play wont be seen if he doesnt want to be contacted. Neither will challenge.</li>
</ul>


<b>31 oct 07</b>
<ul>
<li>Update: E-mail routine was changed.</li>
<li>Update: Username & password is now inlcuded in the activation mail, as a reminder.</li>
<li>Added: Profile shows 20 latest games.</li>
<li>Update: All game entries will from now on also show how many points the players got/lost. Before this could only be seen for the latst played game.</li>
<li>Added: Vatican & Slovakian flags.</li>
</ul>

<b>29 oct 07</b>
<ul>
<li>Update: The ladder now marks you in green if you're logged in, making it easier to find yourself amongst the other heroes.</li>
<li>Added: Player who try to login won't be able to do so unless they have actiavted their account.</li>
	<li>Update: Password encryption via hashing & salting and what not.</li>
	
	<li>Added: Login system.</li>
	<li>Added: Profile item in menu when logged in.</li>
	<li>Update: Removed the winner drop down box when reporting a game. Winner is always you and your name is there since you must be logged in to report a game.</li>
	<li>Update: Join icon in menu is only seen when logged out.</li>
	<li>Added: When logged in  your nick is displayed in the top right, along with the option to log out at the bottom right.</li>
	
</ul>

<b>27 oct 07</b>
<ul>
	<li>Added: Show the 3 newest players in the news.</li>
	<li>Added: Show a list of all players in inverted join order in the statistics page, placing the newest first & descending, making it easy to see all newcomers.</li>
	
	<li>Changed layout on index.php into 2-column and with smaller font size.</li>
	<li>Added: More stats on index.php.</li>
	
	
</ul>


<b>13 oct 07</b>
<ul>
	<li>Changed the password label so it now shows more clearly that it is the winner who must report a game.</li>
	<li>Added new countries to the countries list when registering/updating profile. There are now a zillion nations in it.</li>
	<li>Added profile info so it now includes a hint about when a player is most likley to be able to play a game.</li>
</ul>


<b>12 oct 07</b>
<ul>
	<li>Added this (version.php) page.</li>
	<li>Changed the way e-mail/msn info is displayed in profile & player sections: They're now obfuscated, @ is converted to (at) and . becomes (dot).</li>
	<li>Added message me/don't message me to play a game info in Profiles & the abolity to change it in profile setup.</li>
	<li>Added version of wesnoth info in Profiles & in profile setup.</li>
	
</ul>

<b>before that</b>

<ul>
<li>Extra password verfication field was added in Join.</li>
<li>Mandatory option to set your version of Wesnoth when Joining.</li>
<li>Mandatory to type e-mail when joining</li>
<li>New members get a verfication mail with a link that they need to click to activate their account.</li>
<li>Only activated accounts can report a game.</li>
<li>Its now clearer which fields are mandatory or not.</li>
<li>The date of the latest game played, the result of it, who your opponent was and how many points you lost/won can now be seen in your profile (just click your name).

If you've played games and it shows 0 it's because you played them before this was implemented - check it after the next game you play</li>
<li>Added plenty of new avatars</li>
<li>Added timestamp to reported games so it now shows when a game was reported/played.</li>

<li>FiX: There was a bug in the original code concerning the Elo counting that has been taken care of. It gave/took too many points. All ratings have been updated so they're correct now.</li>




</ul>


<?php
require('bottom.php');
?>
