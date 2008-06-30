<?
session_start();
require('conf/variables.php');
require('top.php');
?>
<b>Table of Content</b>
<ol>
    <li><a href="faq.php#mrules">Rules</a> - v0.14 (18 April 2008)</li>
    <li><a href="faq.php#magreement">Agreement</a></li>
    <li><a href="faq.php#mfaq">Faq</a> - v0.2 (5 June 2008)</li>
</ol>

<p>
<b>Notice:</b> The ladder is <i>not</i> in any way associated with the official Wesnoth forum, it's moderators and/or the developers of Wesnoth. Please don't use the forum to start new ladder threads and don't contact the Wesnoth staff about ladder related issues.
</p>

<a name="mrules"></a><h2>Rules</h2>
<h3>Ladder Rating & Ranking</h3>
<ol>
    <li>To be <em>rated</em> you just need to play a game. The more games you play against different opponents, the more accurate your rating becomes. All players that have played one or more games always have a rating.</li>
    <li>To be <em>ranked</em> you must have played at least;
    <ol>
        <li><?php echo "$gamestorank" ?> games <b>and</b></li>
        <li>have played at least one game the recent <?php echo "$passivedays";?> days. The rank orders players by comparing their rating and also the amount of games they've played. The more, the better, in both cases.</li>
    </ol></li>
    <li>To be listed in the ladder table you must;
    <ol>
        <li>have a rating that's <?php echo "$ladderminelo"; ?> or above, <b>and</b></li>
        <li>have played at least one game the recent <?php echo "$passivedays";?> days <b>and</b></li>
        <li>played at least  <?php echo "$gamestorank" ?> games in total. You can read the <a href="faq.php#passive">details here</a>.</li>
    </ol></li>
</ol>

<h3>Game setup</h3>
<ol>
<li>All games are 1 vs 1. You're allowed to play however many games you want, with whoever that is participating in the ladder and that wants to compete with you. To find an opponent for a ladder game you'd have to contact people in the ladder and/or create (ladder) games on the offical server. You can find players by using the ladder <a href="friends.php">in-game friends list</a>, contacting them via some kind of <a href="http://www.pidgin.im">instant messenger</a> and also email challange them via their profile.</li>
<li>Game must be played on one of the <i>Official Servers</i> using the original unmodified software.</li>
<li>The name of the game must start with the prefix <i>(L)</i> or <i>(Ladder)</i>. It's recomended but not mandatory to let it be followed by <i>ladder.subversiva.org</i> and don't forget your <i>ladder nick & your Elo rating</i> in paranthesis. The ranking <i>may</i> be rounded to nearest hundredth (1545 becomes 1500). An example of a ladder game name according to the above would be: <i>(L) ladder.subversiva.org (eyerouge 1600)</i></li> 
<li>Only the current stable <i>and</i> development versions of Battle for Wesnoth for <i>your plattform</i> are legal for you. Only exceptions to this are if those versions don't perform well on your system, in which case you're allowed to use one older subversion. Always make sure both you and your opponent use the same version before the game begins to avoid all kinds of trouble.</li>
<li>You must have downloaded your copy of Wesnoth from a site that the  wesnoth.org download section links to.</li>
<li>The loading of Saved Games isn't allowed unless you both explicitly agree that it is or the rules suggest it.</li>
<li>The only legit era is the <i>Default</i> one. No other eras are accepted.</li>
<li>All official 2 player maps associated with the specific game version and that come with the game are allowed if they're made for a 1vs1 common game duel. Notice that survival maps are not allowed. Neither are the following maps: Wesbowl.</li> 
<li>Use map settings: On</li>
<li>Time limits <em>must be activated</em> and set according to the following.
    <ul>
        <li>Reservoir: 270</li>
        <li>Init. Limit: 270</li>
        <li>Turn Bonus: 200</li>
        <li>Action Bonus: 20</li>
    </ul>These are the lowest values allowed unless you <i>agree</i> on another higher or lower setting with your opponent <i>before</i> the game starts. (If you're an apprentice be warned: <i>For many total newcomers to the world of Wesnoth the recomended time setting can be harsh.</i> You have to eat many Wose roots before you can play the game skillfully under the perils of time. Try to agree on more time before starting the game if you're worried about this.)</li> 
<li>Settings within the game lobby: Use your <i>exact ladder</i> name. Be in different teams, have the map default amount of gold and default income.</li>
<li>Observers: It's encouraged that you allow observers, but you have the right to play ladder games with observers turned off. If at least one player requests that observers are turned off then observers must be turned off. A player who enters a game with observers on <em>automatically agrees</em> on allowing observers. Notice: If you allow observers your opponent or his friends can observe your moves with another instance of Wesnoth. In general people don't cheat.</li>

</ol>

<h3>Players</h3>
<ol>
<li>If two players communicate the language used should be English <i>unless</i> they both agree to use another language. If an observer speaks openly in a game, so that the players can see the text, she/he is only allowed to do so in the very same language the players use. Private chat <i>between</i> the observers may occur in any language.</li>
<li>The <i>winner</i> of the game is allowed to report the result to the ladder after each finished game. The loser isn't.</li>
<li>Except for the cases below the winner/loser is declared by Wesnoth.</li>
<li>In cases where <em>one</em> of the players get disconnected and/or disconnects the other player must a) wait 5 minutes for b) the disconnecter to return to the game or server lobby and c) state his/her intention to continue the game.<em> If</em> the disconnecter does all this then the game must be loaded by the non-disconnecter and continued from the most recent save. If the non-disconnector lacks the save game it may be created/loaded by the disconnecter instead. If a player disconnects and fails to return to the game or server lobby within 5 minutes he/she loses the game. If a player disconnects and returns to the game or server lobby within time but doesn't explicitly state that he/she wishes to continue the game he/she loses the game. <em>Everything</em> in this paragraph can be handled in another way if the players <em>all agree </em>on it.</li>
<li>In cases where <em>all </em>players get disconnected due to official server failure then a) all players must try to rejoin the server lobby within 10 minutes from the disconnect in order to resume the game by loading. b) If the server is still officially unreachable after 15 minutes the game ends as a draw and isn't reported to the ladder unless both players agree on something else. c) If one player reconnects, waits 15 minutes for the other player to show up and the other player doesn't, the player who re-connected wins the game.</li>
<li>If all parties at any time agree that they began playing the game using the wrong settings they're allowed to disconnect and start a new game instead. The correct settings are defined in this document.</li>
<li>Always save a replay of your game - it might be of value in the future and will also prevent the most common cheaters.</li>
<li>If you manage to make a false report by misstake please send us a mail and we'll correct it.</li>
<li>Only use your exact ladder nick and try to have a unique one. If you, against all reason and recomendation, don't use the same nick ingame as in the ladder, you must be prepared to verify that you are who you say you if a player requests it. Verification is done via the IM that you have specified in your profile here in the ladder.</li>
<li>As soon as an out of sync (OOS) message is shown to a player that player must report it to the other. The game must then be loaded from <i>the most recent saved turn before</i> the first OOS-message. OOS errors mean that the game sends different types of info to the players, which in the end will result you seeing all kinds of things that haven't isn't seen by your opponent and vice versa. In essence you'll be playing two separate games against nobody, which doesn't make any sense. If you ignore the OOS-error messages and keep on playing the game is declared unvalid unless it's re-loaded according to the above.</li>
<li>In cases where the OOS problem arise, the player requsiting a re-load due to the OSS error must be able to validate the error with a screenshot and a save file <i>if</i> the opponent doesn't get the same error message <i>and</i> the opponent actually demands the validation.</li>

 <li>It's strongly recomended that you always finish a game the same day it was started because experience tells us that players you don't know can be problematic to get hold of. If however both players explicitly agree that they want to finish the game another time by saving and then reloading it in the future they are allowed to do so. In such a case the players must both agree on where and how to meet (in GMT if they use different time zones) in order to continue the game, <i>before</i> they part. If they don't agree before they part the player <i>who parts first</i> from the ongoing game will be declared the loser of the game. Once again we'd like to warn you from continuing games in the future - it often just causes trouble. If you play with the timer enabled this is also needed less often.</li>
<li>If both players agree that a situation that isn't covered by the rules arises they can, <em>if they agree on the solution</em>, decide themself how it should be handled. If the players don't agree on how it should be handled then the game is declared a draw and the result is not reported to the ladder. Whenever any of this happens the players must contact the ladder admin with an explanation of what happend.</li>
</ol>

<a name="magreement"></a><h2>Agreement</h2>
<ol>
<li>By registering or using the ladder you agree to follow all rules and regulations.</li>
<li>You swear that you won't ever cheat, report false results or fail to report correct ones, abuse or exploit this system in any way.</li>
<li>You are only allowed to have one account. If you want to create a new account and delete your old please contact us and be ready to give eyerouge your password in order for us to verify that you're the real owner of the account that's about to be deleted.</li>
<li>If you suspect a player breaks the agreement you must contact us. Don't fear - we treat all correspondence with discretion. Whatever you do, <em>don't </em>start rumours or accusations in the open as it never leads to anything productive.</li>
<li>Using this ladder and our services isn't a right - it's a privilege that we  choose to give to you as long as we want and as long as it's possible.</li>
<li>If there are breaches in the agreement and/or other apparent reasons to do so the site administration has the right to ban users and/or delete/modify results, together with any other necessary measures that a situation might require for us to secure the ladders integrity and the intended functions of it.</li>
<li>The rules are always a subject for change. If many players require a modification, addition or deletion or if the admin sees fit the rules will be revised. Revisions are marked with a version number and they're announced in the news section as well as in here.</li>
</ol>

<a name="mfaq"></a><h2>FAQ</h2>
<ul style="list-style: none">
    <li><a href="faq.php#what">What is this site?</a></li>
    <li><a href="faq.php#howtoreport">How do I report the result of a game?</a></li>
    <li><a href="faq.php#unranked">I've played x games. Why am I still unranked?</a></li>
    <li><a href="faq.php#unladder">Why don't I show in the ladder listing?</a></li>
    <li><a href="faq.php#cheaters">Won't people cheat?</a></li>
    <li><a href="faq.php#logins">I can't login...</a></li>
    <li><a href="faq.php#friends">Is there an easy way to add all ladder members as friends in-game?</a></li>
    <li><a href="faq.php#suckage">Does my rating suck? Is having 1500 good?</a></li>
    <li><a href="faq.php#passive">What does passive rank mode mean?</a></li>
    <li><a href="faq.php#elochange">Help! My Elo points changed for no reason!</a></li>
    <li><a href="faq.php#countdown">What is the countdown in days for in my profile?</a></li>
    <li><a href="faq.php#avgp">Wtf is Average P W/L/T in the profile?</a></li>
    <li><a href="faq.php#rulings">Where do I get a ruling?</a></li>
    <li><a href="faq.php#rulessuck">The rules suck.</a></li>
    <li><a href="faq.php#playnowlist">What is the stuff next to the players nick in the waiting for game list?</a></li>
    <li><a href="faq.php#elo">How is the rating calculated?</a></li>
    <li><a href="faq.php#projection">What are the points in () that I see in the profiles?</a></li>
    <li><a href="faq.php#provisional">What is a provisional player?</a></li>
    <li><a href="faq.php#protection">Won't players just play against weaker players?</a></li>
    <li><a href="faq.php#nofun">It's not fun or a good idea to use a ranking system. The Wesnoth developers/community/santa is against it.</a></li>
    <li><a href="faq.php#help">How can I help?</a></li>
    <li><a href="faq.php#profile">How can I edit my profile?</a></li>
    <li><a href="faq.php#avatars">What good are the avatars?</a></li>
    <li><a href="faq.php#ideas">I have an idea. Who do I contact?</a></li>
    <li><a href="faq.php#contact">How do I contact you?</a></li>
    <li><a href="faq.php#graphics">Where's the graphics from?</a></li>
    <li><a href="faq.php#aboutofficial">Why is the ladder not an official part of the Wesnoth community?</a></li>
</ul>
<br />
<a name="what"></a><h4>What is this site?</h4>
<p>
It's a site that helps you keep track of your skills in the excellent open source game Battle for Wesnoth (wesnoth.org). By playing against others who also use the site you can report the results of your games and get a rating. You can also use the system to find players of simillar skill. All you have to do to start participating is to Join, play a game against another person that uses the ladder, and then let the winner report the result of the game.
</p>

<a name="howtoreport"></a><h4>How do I report the result of a game?</h4>
<p>
Granted you have created an account, you press Report in the main menu. You then tell us who the winner / loser is, and also your account password. That's it.
</p>

<a name="unranked"></a><h4>I've played x games. Why am I still unranked?</h4>
<p>
You must play at least 3 games to get a ranking. The more games you play, the more accurate it gets. 3 is currently the bare minimum.
</p>

<a name="unladder"></a><h4>Why don't I show in the ladder listing?</h4>
<p>
You show up in the ladder listing if you <i>are</i> ranked and also have a rating of at least <?php echo "$ladderminelo";?>. While any player can get to see his ranking, only the ranked players that have a rating of at least <?php echo "$ladderminelo";?> will appear on the ladders list. This is to keep it free from clutter and to only show the top players.
</p>

<a name="cheaters"></a><h4>Won't people cheat?</h4>
<p>
In short, this is an imaginary problem. So the answer is no.<br />
I've written a more extensive answer to this one <a href="http://www.wesnoth.org/wiki/Competitive_Gaming">in here</a>.
</p>

<a name="logins"></a><h4>I can't log in...</h4>
<p>
These are the reasons for why you can't log in. You;
</p>
<ol>
<li>Haven't registered: The site requires that you're a registered user to enter it.</li>
<li>Registered but haven't activated your account: When you register you get an e-mail with an activation link. Find the e-mail, check your spam in, and click the activation link. If you haven't activated your account the site <i>will tell you</i> this when you try to log in.</li>
<li>Registered, activated your account, but get a wrong username/password error: You enter the wrong username and/or password. Check your info in the welcome e-mail, we included it there for you to remember it better.</li>
<li>Registered, activated your account, but nothing at all happens when you ty to log in - you don't even get an error message: This is a common problem and depends on your browser not accepting cookies. Please change your browser settings so it accepts our cookies. If you use Internet Explorer you could also try to add <i>http://chaosrealm.net</i> and  <i>http://chaosrealm.net/wesnoth</i> and <i>http://ladder.subversiva.org</i> to your trusted sites list within IE. We do however strongly suggest that you abandon the use of Internet Explorer and go with an open source browser instead, like for example Firefox.</li>
</ol>

<a name="friends"></a><h4>Is there an easy way to add all ladder members as friends in-game?</h4>
<p>
Yes. Amazing you asked that question ;) Use our always up to date <a href="friends.php">friends list.</a>
</p>

<a name="suckage"></a><h4>Does my rating suck? Is having 1500 good?</h4>
<p>
Many players that are not familiar with Elo's Rating System believe that 1500 is a newcomers rating. Understand this: <em>It is not the case!</em> When a player registers at the ladder he/she gets a rating of 1500, but, that rating is really the expected rating of an <em>average player</em> and not of a newcomer to the game. This means that people that are new to the game or still learning it are <em>expected</em> to have a rating thats way lower than 1500. It's normal. A player who has been around for a while and knows the game is expected to be average and have around 1500, while a really skilled veteran would have a higher. As a reference, players that have around 2000 are considered strong, and those beyond 2500 grandmasters.
</p>
<p>
Whatever your rating is, it <i>doesn't suck</i>. The rating is a measure of your skills, <em>in relation to the other players</em> on the ladder. It gets better and more accurate the more you play the game and the more different people you meet. Becoming good at Wesnoth takes very long time and a lot of patience. If you lack either your Wesnoth career will be short. Use the ladder as a personal measure tool, to see your own development and to find players that are about the same skill level as you are - that's when the game is most fun to play. Please <em>don't</em> see it as competition until you are truly ready for it and know you can handle the heat. And never lose faith because the rating says you are a newcomer skill wise or rank low on the ladder. After all, you are supposed to until you start mastering the game. With time your skills will grow, and so will your rating.
</p>

<a name="passive"></a><h4>What does passive rank mode mean?</h4>
<p>
In order to keep the ladder meaningfull, up to date and to encourage activity we have a rule that says that you must have played at least one game within <?php echo "$passivedays";?> days if you wish to be listed as a valid contender in the ladder. If you don't play a game for <?php print $passivedays + 1;?> days your account will be set in <em>passive rating mode</em>. While beeing in a passive rating mode you won't get listed as competing in the ladder and you'll temporarily lose your place in it <em>until</em> you play a new game. You'll then be considered to be an active player again and you will be automatically removed from the passive rating mode. You will of course also <i>regain your proper rating</i>, taking the new game into account, as usual. And to answer the question everybody fears: No, you <em>won't lose</em> rating while beeing in passive rating mode. If you had 1500 when you were put in the passive mode, was passive for 3 months and then play a game where you win 10 points, you would then be an active player again, but now with 1510 points.
</p>

<a name="elochange"></a><h4>Help! My Elo points changed for no reason!</h4>
<p>
Nothing ever happens without reason, and if it does then the ladder needs some fixing. These are the only times your Elo points will change:
</p>
<ul>
<li>You have undone one or several games you reported. When doing so you will still have the points from the game(s) for a while. In the not distant future they will however disappear, as they should, giving you your real Elo rating.</li>
<li>For a simillar reason your Elo might change without you ever undoing a game: If you play against Donald, and he, before you two play, has made a false or wrong report, then Donald will have a higher Elo rating then he really has when you play against him. This means that if you win over Donald, that maybe had an Elo of 1600, you win too many points since he really never had that many to begin with.  Later on, after you have played Donald,  his rating get's corrected by him/us undoing some of his wrongfully reported games he played before he met you. Thus, the points you won when you beat Donald could be lowered to be correct. The points you will get for winning over Donald are whatever his correct Elo would give you, at the time you played.</li>
<li>The above could be extended to x amount of players: If Donald's Elo is based on say 2 wrongly reported games, and Donald plays you, and you play Anna, and Anna plays Flash, then you, Anna and Flash (and Donald of course) would all get corrected Elo ratings the second Donald's rating is corrected. The cool part with this is that it doesn't matter how complex the relations are: The ladder will always fix it. You don't ever have to think about this, as your rating would usually be correct or about to be corrected automagically if needed. Add to that that it's also very rare to play against a player that has made false or wrong reports since a vast majority seldom or never report false/wrong.</li>
<li>You played a game and won or lost, and it was reported. Hence your Elo would change. Check your game history to see if it refreshes your memory.</li>
<li>Somebody reported a game which you supposedly played. Check your profiles game history to see the most recent games. If somebody reported a false game, please contact that person and ask him/her to undo it. If you get no response then contact us and we'll take care of it. Never contact us directly without trying to get in touch with that person.</li>
<li>Once in a while we upgrade the ladders rating system and in particular the forumulas we use to calculate you Elo. In such a rare case it would be announced by us as news, and the effect would be global on the ladder, so all other players would get equal changes in Elo points as you. Typically we adjust the K-values and ranges where they're valid. However, all such changes have retroactive effects in the name of fairness.</li>
</ul>

<a name="countdown"></a><h4>What is the countdown in days for in my profile?</h4>
<p>
It shows you how many days you have left until you're put in passive rank mode. You can only see this if you're logged in and view your own profile.
</p>

<a name="avgp"></a><h4>Wtf is Average P W/L/T in the profile?</h4>
<p>
The Average P WLT shows x / y / z, which translates to the average points the player wins when she wins, the average points the player loses when she loses, and also the average points the player gets/loses per game in total. I hope you all see the potential already, as this great info actually tells you what kind of a player you/the others are:
</p>
<p>
The higher average win points a player has, the more higher rated players she plays (and wins) against. If a player always plays with opponents that have about the same rating as her she would have a an average win point of 12. The higher win point, the tougher opponents the player challenged and won over, and vice versa.
</p>
<p>
As for the average loss points, the opposite is the case: The lower average loss point (remember it's a negative number, so -11 is lower than -10) a player has the more ass whooping she got from players that have a lower rating than herself. Again, if this number is somewhere around -12, then the player usually loses against other players that had about the same rating as herself when the game took place.
</p><p>
The average points in total is the less usable of the 3 numbers: It displays all the points you have earned - the points you have lost / the total amount of games you have played. With other words, it tells you how many points you get or lose in an average game. In reality though this average game can never exist and the other two figures are way better pointers towards that end.
</p>

<a name="rulings"></a><h4>Where do I get a ruling?</h4>
<p>
The ladder admin doesn't support or encourage rulings of any kind. The reasons for that are many practical ones: It would require a constant supply of veteran players/truly objective refeeres. On top of that, even veteran players have different opinions from time to time. In many cases there's simply no wesnoth oracle that can state something as a fact, on the contrary, most of their work would be qualified guesses. 
</p>
<p>Add to this the more theoretical reasons that are separate from the already mentioned: We don't want to centralize the ladder or the running of it more than necessary. We don't want a boss or ruler to dictate who wins or not, since it's not our perspective on how an open source community should work. On the contrary, we think it should be self-sustaining. Lastly, the rules are pretty clear about most things and simple to follow. Whenever they say that a game is won or lost then it is just that. If you think that the rules need to be revised we'd welcome ny input you have on them granted you argue for your point. We update them whenever there's a need.
</p>

<a name="rulessuck"></a><h4>The rules suck.</h4>
<p>
Somebody is bound to feel tha way, but it's not really interesting unless you can explain why and you have sound arguments. If a majoirty of players want a rule change we'll fix it. Just prove your case. After all we're here to serve you, not enforce a special kind of game play nobody is interested in. The rules are still very early in development and we'd welcome any feedback.
</p>

<a name="playnowlist"></a><h4>What is the stuff next to the players nick in the waiting for game list?</h4>
<p>
When a player puts him/her self in the waiting for game list the following info is seen in the entry:
</p>
<ol>
    <li>Nickname - the name of the player looking for a game.</li>
    <li>(rating) - this is the elo rating of the player.</li>
    <li>time - the amount of time that the player is still available.</li>
    <li>dev / sta / im - where to meet the player: If he/she is already waiting in the lobby of the development or stable version or if you should make contact by using an instant messenger.</li>
</ol>

<a name="elo"></a><h4>How is the rating calculated?</h4>
<p>
We use a version of the Elo system, which is the same as most chess clubs use to rank their players. In short, the better players you beat, the more points you get and vice versa. The same rules apply to all players. We don't want to encourage players to start optimizing their points and doing calculations: Play because it's fun, not to milk out every point.
</p>
<p>
The higher the K-value you have (and the better player you beat), the more points you win when winning. As in the real world we make it harder to get points the better rated you are. This insures us that inflation is cut down and, more importantly, that the top rated players really prove their skills. 
It also let's the newcomers obtain a more correct rating faster. Keep in mind that a player with an Elo of 1500 is an average player that knows the game. We consider a player that has more than <?php echo MIDDLE_RATING;?> to be <i>very skilled</i> compared to most of the competition, and having about 2500 equals to a grand master. Statistically speaking it should be rare to see players which such high ratings, and only a few are expected to reach those numbers.
</p>
The K value you get is based on your current Elo rating. These are the K-values we use:
<ul>
    <li>0 to <?php echo BOTTOM_RATING ." Elo points give a K value of ". BOTTOM_K_VAL; ?></li>
    <li><?php echo BOTTOM_RATING+1 ." to ". MIDDLE_RATING ." Elo points give a K value of ". MIDDLE_K_VAL; ?></li>
    <li><?php echo MIDDLE_RATING+1 ."  to x Elo points give a K value of ". TOP_K_VAL; ?></li>
</ul>

<a name="projection"></a><h4>What are the points in () that I see in the profiles?</h4>
<p>
If you are logged in and view another players profile you'll see something that looks like this: (10p / 8p) near his name. That is the projected win/loss of points for you, if you play against him/her. In our example it means that you would win 10p if you won (and he/she lose 10p) and that he/she would win 8p if he/she won. It could of course be any other numbers, like (2p / 13p). The points you win or lose depend on your rating compared with that persons rating. The better you are than the person, the less you win by winning over him/her.
</p>

<a name="provisional"></a><h4>What is a provisional player?</h4>
<p>
In a players profile you can sometimes see the title <i>provisional</i>. In an effort to encourage competitive play, all new players to the ladder will be given <?php echo PROVISIONAL;?> games of new player protection: Their rank will be simply "provisional". Playing a game against a provisional player is worth the normal amount of points you would get divided by <?php echo PROVISIONAL_PROTECTION;?>, and the points you would lose to one is also divided by <?php echo PROVISIONAL_PROTECTION;?>. We use the provisonal mode to both encourage competitive players and to protect new players, to give them a chance to try out opposition on the ladder and get a feel for it. It also makes it less risky for highly rated players to play with newcomers which still haven't got their true rating in their profile due to the lack of games.
</p>

<a name="protection"></a><h4>Won't players just play against weaker players or stop playing to maintain their high rating?</h4>
<p>
First of all, it's true that players have great freedom to choose their oponents and wouldn't rush into a game against a person thats much more skilled than them. That said, why would/should they? If players behave like that then their opponents would get easy wins, so it's fair to say that this balances it self. If Anna won't play others who are over-skilled compared to her, nor would anyone else play against her if she's over-skilled compared to him/her.
</p>
<p>
Second, the larger the rating difference is, the less points the higher ranked player will win. If the higher ranked player (2000) wins against the lower ranked (1500) he gets 1 p. If the lower ranked player wins against the higher in our example he gets 23 p. This suggests that the higher ranked player is taking a large risk point wise when facing an average player with 1500: While she can only win 1 p, she can lose 23 p. With that in mind, is the higher ranked player with 2000 likely to want to play the lesser ranked player, with 1500? In many cases, we believe it's not so. What this shows us is that players will usually stick to their own division, looking for other players with skills that are around their own. It is also a fact that the higher rated player would win 0 points if the difference in rating is &gt;= 670.
</p>
<p>
Third, a player can't really stop playing to "protect" her rating: The ladder demands that she plays at least one game every <?php echo "$passivedays"; ?> days. If she doesn't she will be temporarily put into passive rating mode and won't be listed in the ladder. Then there's also the fact that a skilled player who only playes the bare minimum amount of games will face a lot of indirect competition of the other skilled players since they get to play more games, thus get more chances of improving their rating, which results in the passive player climbing down the ladder.
</p>

<a name="nofun"></a><h4>It's not fun or a good idea to use a ranking system. The Wesnoth developers/community/santa is against it.</h4>
<p>
Fine. Don't use it then. The best part with this is that it's of free will. Nobody forces you to use it, and those who do use it don't bother you, do they? This is here for the people who <i>want to</i> use it.
</p>

<a name="help"></a><h4>How can I help?</h4>
<ul>
    <li>If you're a PHP coder that knows basic php and mysql you're welcome to contact us as we want some more simple functions added to the system.</li>
    <li>If you're not a coder you can find us one.</li>
    <li>If are good with graphics we welcome everything that's under GPL and that's Wesnoth related. Especially new menu icons or logo. Contact us before you start working.</li>
    <li>Link to us and use us</li>
    <li>Keep supporting the Wesnoth community and learn your friends to play</li>
</ul>

<a name="profile"></a><h4>How can I edit my profile?</h4>
<p>
Use <a href="edit.php">this page</a> or the icon with the text Profile in the menu that appears once you've logged in.
</p>

<a name="avatars"></a><h4>What good are the Avatars?</h4>
<p>
None at all. It's aesthetic, but we could implement an icon system like Battle.nets. For now it's just nice and let's you select something you like as a visual representation. If you miss an avatar plese send it to us if it's legal and if it's a *.gif that uses transparency &amp; that is a maximum of 44 pixels high.
</p>

<a name="ideas"></a><h4>I have an idea for this system / it lacks a function, who do I tell?</h4>
<p>
We don't have a php coder yet and need a guy (or girl) before we can start developing the system further. If you know basic php &amp; mysql you are welcome to contact us already. If you want to you can also use this <a href="http://www.wesnoth.org/forum/viewtopic.php?t=17941">forum thread</a> to post your ideas.
</p>

<a name="contact"></a><h4>How do I contact you?</h4>
<p>
Easiest way is to write me a mail &gt;&gt; eyerouge (thething) eyerouge (theotherone) com and to write it in english.
</p>

<a name="graphics"></a><h4>Where's the graphics from?</h4>
<p>
It's all from Wesnoth and/or it's community. We've used what we could find and what we believe is free under the nice license.
</p>

<a name="aboutofficial"></a><h4>Why is the ladder not an official part of the Wesnoth community?</h4>
<ul>
    <li>The ladder is not official because the concept of it, rating players, is controversial and/or not wanted among some Wesnoth developers. Their exact reasons may vary, but most of them probably believe that using ratings is a bad or for other reasons an unwanted thing which should be opposed.</li>
    <li>We didn't ask for permission to start the ladder, nor did we see it as something that was needed. Hence, from it's birth, the project has been unofficial.</li>
    <li>Upon requesting a dedicated section on the Wesnoth forum we got denied, because the ladder is not official.</li>
</ul>

<?php
require('bottom.php');
?>
