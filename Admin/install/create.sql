CREATE TABLE `webl_admin` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(40) default NULL,
  `password` varchar(40) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `webl_games` (
  `game_id` int(10) NOT NULL auto_increment,
  `winner` varchar(40) default NULL,
  `loser` varchar(40) default NULL,
  `faction1` varchar(40) default NULL,
  `faction2` varchar(40) default NULL,
  `date` varchar(40) default NULL,
  `elo_change` int(10),
  PRIMARY KEY  (`game_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `webl_deleted_games` (
  `game_id` int(10) NOT NULL auto_increment,
  `winner` varchar(40) default NULL,
  `loser` varchar(40) default NULL,
  `date` varchar(40) default NULL,
  `elo_change` int(10),
  PRIMARY KEY  (`game_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `webl_news` (
  `news_id` int(10) NOT NULL auto_increment,
  `title` varchar(100) default NULL,
  `date` varchar(100) default NULL,
  `news` text,
  PRIMARY KEY  (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `webl_players` (
  `player_id` int(10) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `passworddb` varchar(255) default NULL,
  `approved` varchar(10) default 'no',
  `mail` varchar(50) default NULL,
  `Jabber` varchar(40) default NULL,
  `icq` varchar(15) default NULL,
  `aim` varchar(40) default NULL,
  `msn` varchar(100) default NULL,
  `country` varchar(40) default NULL,
  `games` int(10) default '0',
  `wins` int(10) default '0',
  `losses` int(10) default '0',
  `streakwins` int(10) default '0',
  `streaklosses` int(10) default '0',
  `rating` int(10) default '1500',
  `ip` varchar(100) default NULL,
  `Avatar` varchar(100) default 'No avatar',
  `LastGame` text,
  `HaveVersion` varchar(40) default NULL,
  `MsgMe` char(3) NOT NULL default 'Yes',
  `CanPlay` text,
  `Confirmation` text NOT NULL,
  `question` text COMMENT 'The secret question for password retrieval',
  `answer` text COMMENT 'answer to the secret question',
  `Joined` int(10) default NULL,
  `Titles` varchar(160) default NULL COMMENT 'Special People',
  PRIMARY KEY  (`player_id`),
  UNIQUE KEY `name` (`name`),
  FULLTEXT KEY `country` (`country`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `webl_vars` (
  `vars_id` int(10) NOT NULL auto_increment,
  `color1` varchar(20) default NULL,
  `color2` varchar(20) default NULL,
  `color3` varchar(20) default NULL,
  `color4` varchar(20) default NULL,
  `color5` varchar(20) default NULL,
  `color6` varchar(20) default NULL,
  `color7` varchar(20) default NULL,
  `font` varchar(80) default NULL,
  `fontweight` varchar(40) default NULL,
  `fontsize` varchar(20) default NULL,
  `numgamespage` int(10) default NULL,
  `numplayerspage` int(10) default NULL,
  `statsnum` int(10) default NULL,
  `hotcoldnum` varchar(10) default NULL,
  `gamesmaxdayplayer` int(10) default NULL,
  `gamesmaxday` int(10) default NULL,
  `approve` varchar(10) default NULL,
  `approvegames` varchar(10) default NULL,
  `system` varchar(20) default NULL,
  `pointswin` int(10) default NULL,
  `pointsloss` int(10) default NULL,
  `report` varchar(20) default NULL,
  `leaguename` varchar(100) default NULL,
  `titlebar` varchar(100) default NULL,
  `newsitems` int(10) default NULL,
  `copyright` varchar(200) default NULL,
  PRIMARY KEY  (`vars_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `webl_waiting` (
  `id` int(10) NOT NULL auto_increment,
  `username` varchar(25) NOT NULL default '',
  `time` varchar(10) NOT NULL default '',
  `entered` varchar(12) NOT NULL default '',
  `meetingplace` varchar(3) default NULL,
  `rating` int(5) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

