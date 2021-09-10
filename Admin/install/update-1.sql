-- DB Update to add Factions
-- See pull request: https://github.com/Greg-Boggs/GamingLadder/pull/18
--
ALTER TABLE webl_games ADD faction1 varchar(40) default NULL;
ALTER TABLE webl_games ADD faction2 varchar(40) default NULL;
