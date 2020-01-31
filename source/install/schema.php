<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB's installer (ajv)
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * This file is part of GaiaBB
 *
 *    GaiaBB is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    GaiaBB is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with GaiaBB.  If not, see <http://www.gnu.org/licenses/>.
 *
 **/

if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

require_once('common.model.php');

function schema_create_addresses($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "addresses`");
    $db->query("CREATE TABLE `" . $tablepre . "addresses` (
        `username` varchar(32) NOT NULL DEFAULT '',
        `addressname` varchar(32) NOT NULL DEFAULT '',
        KEY `addressname` (`addressname`),
		KEY `username` (`username`)
    ) ENGINE=MyISAM");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
// Note to data modellers.
//
// The primary key MUST always appear first.
// MyISAM doesn't have foreign keys, and we have never built the schema around
// them. Do not use them - always escape the table and column names as some
// prefixes are pretty weird.
function schema_create_adminlogs($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "adminlogs`");
    $db->query("CREATE TABLE `" . $tablepre . "adminlogs` (
    	`uid` int(7) NOT NULL DEFAULT '0',
    	`username` varchar(32) NOT NULL DEFAULT '',
        `action` varchar(64) NOT NULL DEFAULT '',
        `fid` smallint(6) NOT NULL DEFAULT '0',
        `tid` int(10) NOT NULL DEFAULT '0',
        `date` int(10) NOT NULL DEFAULT '0',
        KEY `username` (`username`(8)),
        KEY `action` (`action`(8)),
        KEY `fid` (`fid`)
        ) ENGINE=MyISAM
    ");
}

function schema_create_attachments($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "attachments`");
    $db->query("CREATE TABLE `" . $tablepre . "attachments` (
        `aid` int(10) NOT NULL auto_increment,
        `tid` int(10) NOT NULL DEFAULT '0',
        `pid` int(10) NOT NULL DEFAULT '0',
        `filename` varchar(120) NOT NULL DEFAULT '',
        `filetype` varchar(120) NOT NULL DEFAULT '',
        `filesize` varchar(120) NOT NULL DEFAULT '',
        `fileheight` varchar(5) NOT NULL DEFAULT '',
        `filewidth` varchar(5) NOT NULL DEFAULT '',
        `attachment` longblob NOT NULL,
        `downloads` int(10) NOT NULL DEFAULT '0',
        PRIMARY KEY  (`aid`),
        KEY `tid` (`tid`),
        KEY `pid` (`pid`),
        KEY `filesize` (`filesize`(8)),
        KEY `downloads` (`downloads`),
        KEY `filename` (`filename`(8))
        ) ENGINE=MyISAM");
}

function schema_create_banned($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "banned`");
    $db->query("CREATE TABLE `" . $tablepre . "banned` (
        `ip1` smallint(3) NOT NULL DEFAULT '0',
        `ip2` smallint(3) NOT NULL DEFAULT '0',
        `ip3` smallint(3) NOT NULL DEFAULT '0',
        `ip4` smallint(3) NOT NULL DEFAULT '0',
        `dateline` int(10) NOT NULL DEFAULT '0',
        `id` smallint(6) NOT NULL AUTO_INCREMENT,
        PRIMARY KEY  (`id`),
        KEY `ip1` (`ip1`),
        KEY `ip2` (`ip2`),
        KEY `ip3` (`ip3`),
        KEY `ip4` (`ip4`)
        ) ENGINE=MyISAM
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_dateformats($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "dateformats`");
    $db->query("CREATE TABLE `" . $tablepre . "dateformats` (
        `dateformat` varchar(10) NOT NULL DEFAULT '',
        `did` int(3) NOT NULL auto_increment,
        PRIMARY KEY  (`did`)
        ) ENGINE=MyISAM
    ");
}

function schema_create_favorites($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "favorites`");
    $db->query("CREATE TABLE `" . $tablepre . "favorites` (
        `tid` int(10) NOT NULL DEFAULT '0',
        `username` varchar(32) NOT NULL DEFAULT '',
        `type` varchar(32) NOT NULL DEFAULT '',
        KEY `tid` (`tid`),
        KEY `username` (`username`(8))
        ) ENGINE=MyISAM
    ");
}

function schema_create_forums($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "forums`");
    $db->query("CREATE TABLE `" . $tablepre . "forums` (
        `type` varchar(15) NOT NULL DEFAULT '',
        `fid` smallint(6) NOT NULL auto_increment,
        `name` varchar(50) NOT NULL DEFAULT '',
        `status` set('on','off') NOT NULL DEFAULT 'on',
        `lastpost` int(7) NOT NULL DEFAULT '0',
        `moderator` varchar(100) NOT NULL DEFAULT '',
        `displayorder` smallint(6) NOT NULL DEFAULT '0',
        `private` varchar(30) DEFAULT '1',
        `description` text,
        `allowsmilies` set('yes','no') NOT NULL DEFAULT 'yes',
        `allowbbcode` set('yes','no') NOT NULL DEFAULT 'yes',
        `userlist` text NOT NULL DEFAULT '',
        `theme` smallint(3) NOT NULL DEFAULT '0',
        `posts` int(100) NOT NULL DEFAULT '0',
        `threads` int(100) NOT NULL DEFAULT '0',
        `fup` smallint(6) NOT NULL DEFAULT '0',
        `postperm` varchar(7) NOT NULL DEFAULT '',
        `allowimgcode` set('yes','no') NOT NULL DEFAULT 'yes',
        `attachstatus` set('on','off') NOT NULL DEFAULT 'on',
        `pollstatus` set('on','off') NOT NULL DEFAULT 'on',
        `password` varchar(32) NOT NULL DEFAULT '',
        `guestposting` set('on','off') NOT NULL DEFAULT 'off',
        `minchars` smallint(5) NOT NULL DEFAULT '0',
        `attachnum` tinyint(2) NOT NULL DEFAULT '3',
        `frules_status` set('on','off') NOT NULL DEFAULT 'off',
        `frules` text,
        `mt_status` set('on', 'off') NOT NULL DEFAULT 'off',
        `mt_open` text NOT NULL DEFAULT '',
        `mt_close` text NOT NULL DEFAULT '',
        `closethreads` set('on','off') NOT NULL DEFAULT 'off',
        `quickreply` set('on','off') NOT NULL DEFAULT 'on',
        `subjectprefixes` text NOT NULL DEFAULT '',
        `mpnt` smallint(5) NOT NULL DEFAULT '0',
        `mpnp` smallint(5) NOT NULL DEFAULT '0',
        `mpfa` smallint(5) NOT NULL DEFAULT '0',
        `postcount` set('on','off') NOT NULL DEFAULT 'on',
        PRIMARY KEY  (`fid`),
        KEY `fup` (`fup`),
        KEY `type` (`type`),
        KEY `displayorder` (`displayorder`),
        KEY `private` (`private`),
        KEY `status` (`status`),
        KEY `lastpost` (`lastpost`)
        ) ENGINE=MyISAM
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_guestcount($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "guestcount`");
    $db->query("CREATE TABLE `" . $tablepre . "guestcount` (
        `ipaddress` varchar(15) NOT NULL DEFAULT '',
        `onlinetime` int(10) NOT NULL DEFAULT '0',
        KEY `ipaddress` (`ipaddress`),
        KEY `onlinetime` (`onlinetime`)
        ) ENGINE=MyISAM
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_faq($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "faq`");
    $db->query("CREATE TABLE `" . $tablepre . "faq` (
        `type` varchar(15) NOT NULL DEFAULT '',
        `fid` smallint(6) NOT NULL auto_increment,
        `name` text NOT NULL DEFAULT '',
        `status` set('on','off') NOT NULL DEFAULT '',
        `displayorder` smallint(6) NOT NULL DEFAULT '0',
        `description` text NOT NULL DEFAULT '',
        `allowsmilies` set('yes','no') NOT NULL DEFAULT 'yes',
        `allowbbcode` set('yes','no') NOT NULL DEFAULT 'yes',
        `fup` smallint(6) NOT NULL DEFAULT '0',
        `allowimgcode` set('yes','no') NOT NULL DEFAULT 'yes',
        `code` varchar(20) NOT NULL DEFAULT '',
        `view` int(1) NOT NULL DEFAULT '0',
        PRIMARY KEY  (`fid`),
        KEY `fup` (`fup`),
        KEY `type` (`type`),
        KEY `status` (`status`),
        KEY `displayorder` (`displayorder`)
        ) ENGINE=MyISAM
    ");
}

function schema_create_lastposts($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "lastposts`");
    $db->query("CREATE TABLE `" . $tablepre . "lastposts` (
		`tid` int(7) NOT NULL DEFAULT '0',
		`uid` int(5) NOT NULL DEFAULT '0',
		`username` varchar(255) NOT NULL DEFAULT '',
		`dateline` int(10) NOT NULL DEFAULT '0',
		`pid` int(8) NOT NULL DEFAULT '0',
		KEY `tid` (`tid`)
		) ENGINE=MyISAM
	");
}

function schema_create_members($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "members`");
    $db->query("CREATE TABLE `" . $tablepre . "members` (
        `uid` int(32) NOT NULL auto_increment,
        `username` varchar(32) NOT NULL DEFAULT '',
        `password` varchar(32) NOT NULL DEFAULT '',
        `regdate` int(10) NOT NULL DEFAULT '0',
        `postnum` mediumint(5) NOT NULL DEFAULT '0',
        `email` varchar(75) DEFAULT NULL,
        `site` varchar(75) DEFAULT NULL,
        `aim` varchar(75) DEFAULT NULL,
        `status` varchar(35) NOT NULL DEFAULT '',
        `location` varchar(75) DEFAULT NULL,
        `bio` text DEFAULT NULL,
        `sig` text NOT NULL DEFAULT '',
        `showemail` set('yes','no') NOT NULL DEFAULT 'no',
        `timeoffset` decimal(4,2) NOT NULL DEFAULT '0.00',
        `icq` varchar(75) NOT NULL DEFAULT '',
        `avatar` varchar(120) DEFAULT NULL,
        `yahoo` varchar(75) NOT NULL DEFAULT '',
        `customstatus` varchar(75) NOT NULL DEFAULT '',
        `theme` smallint(3) NOT NULL DEFAULT '0',
        `bday` varchar(50) DEFAULT NULL,
        `langfile` varchar(40) NOT NULL DEFAULT '',
        `tpp` smallint(6) NOT NULL DEFAULT '0',
        `ppp` smallint(6) NOT NULL DEFAULT '0',
        `newsletter` set('yes','no') NOT NULL DEFAULT 'yes',
        `regip` varchar(15) NOT NULL DEFAULT '',
        `timeformat` int(5) NOT NULL DEFAULT '0',
        `msn` varchar(75) NOT NULL DEFAULT '',
        `ban` varchar(15) NOT NULL DEFAULT '0',
        `dateformat` varchar(10) NOT NULL DEFAULT '',
        `ignorepm` text,
        `lastvisit` bigint(15) DEFAULT NULL,
        `mood` varchar(75) NOT NULL DEFAULT 'Not Set',
        `pwdate` int(10) NOT NULL DEFAULT '0',
        `invisible` set('1','0') DEFAULT '0',
		`pmfolders` text NOT NULL,
		`saveogpm` set('yes','no') NOT NULL DEFAULT 'yes',
		`emailonpm` set('yes','no') NOT NULL DEFAULT 'no',
        `daylightsavings` set('3600','0') NOT NULL DEFAULT '0',
        `viewavatars` set('yes','no') NOT NULL DEFAULT 'yes',
        `photo` varchar(120) DEFAULT NULL,
        `psorting` set('ASC','DESC') NOT NULL DEFAULT 'ASC',
        `viewsigs` set('yes','no') NOT NULL DEFAULT 'yes',
        `firstname` varchar(30) NOT NULL DEFAULT '',
        `lastname` varchar(30) NOT NULL DEFAULT '',
        `showname` set('yes','no') NOT NULL DEFAULT 'yes',
        `occupation` varchar(75) DEFAULT NULL,
        `notepad` text NOT NULL DEFAULT '',
        `blog` varchar(75) DEFAULT NULL,
        `views` int(100) NOT NULL DEFAULT '0',
        `expview` set('yes','no') NOT NULL DEFAULT 'no',
        `threadnum` smallint(5) NOT NULL DEFAULT '0',
        `readrules` set('yes','no') NOT NULL DEFAULT 'no',
        `forcelogout` set('yes','no') NOT NULL DEFAULT 'no',
        PRIMARY KEY  (`uid`),
        KEY `username` (`username`(8)),
        KEY `status` (`status`),
        KEY `postnum` (`postnum`),
        KEY `password` (`password`),
        KEY `email` (`email`),
        KEY `regdate` (`regdate`),
        KEY `invisible` (`invisible`),
        KEY `threadnum` (`threadnum`)
        ) ENGINE=MyISAM
    ");
}

function schema_create_modlogs($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "modlogs`");
    $db->query("CREATE TABLE `" . $tablepre . "modlogs` (
	        `uid` int(7) NOT NULL DEFAULT '0',
			`username` varchar(32) NOT NULL DEFAULT '',
	        `action` varchar(64) NOT NULL DEFAULT '',
	        `fid` smallint(6) NOT NULL DEFAULT '0',
	        `tid` int(10) NOT NULL DEFAULT '0',
	        `date` int(10) NOT NULL DEFAULT '0',
	        KEY `username` (`username`(8)),
	        KEY `action` (`action`(8)),
	        KEY `fid` (`fid`)
	        ) ENGINE=MyISAM
	    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_pluglinks($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "pluglinks`");
    $db->query("CREATE TABLE `" . $tablepre . "pluglinks` (
        `id` SMALLINT(6) NOT NULL auto_increment,
        `name` text NOT NULL DEFAULT '',
        `url` text NOT NULL DEFAULT '',
        `img` text NOT NULL DEFAULT '',
        `displayorder` smallint(6) NOT NULL DEFAULT '0',
        `status` set('on','off') NOT NULL DEFAULT '',
        PRIMARY KEY (`id`),
        KEY `status` (`status`),
        KEY `displayorder` (`displayorder`)
        ) ENGINE=MyISAM
    ");
}

function schema_create_posts($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "posts`");
    $db->query("CREATE TABLE `" . $tablepre . "posts` (
        `fid` smallint(6) NOT NULL DEFAULT '0',
        `tid` int(10) NOT NULL DEFAULT '0',
        `pid` int(10) NOT NULL auto_increment,
        `author` varchar(32) NOT NULL DEFAULT '',
        `message` text NOT NULL DEFAULT '',
        `subject` tinytext NOT NULL DEFAULT '',
        `dateline` int(10) NOT NULL DEFAULT '0',
        `icon` varchar(50) DEFAULT NULL,
        `usesig` varchar(15) NOT NULL DEFAULT '',
        `useip` varchar(15) NOT NULL DEFAULT '',
        `bbcodeoff` varchar(15) NOT NULL DEFAULT '',
        `smileyoff` varchar(15) NOT NULL DEFAULT '',
        PRIMARY KEY  (`pid`),
        KEY `fid` (`fid`),
        KEY `tid` (`tid`),
        KEY `dateline` (`dateline`),
        KEY `author` (`author`(8))
        ) ENGINE=MyISAM
    ");
}

function schema_create_ranks($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "ranks`");
    $db->query("CREATE TABLE `" . $tablepre . "ranks` (
        `title` varchar(100) NOT NULL DEFAULT '',
        `posts` int(100) DEFAULT NULL,
        `id` smallint(5) NOT NULL auto_increment,
        `stars` smallint(6) NOT NULL DEFAULT '0',
        `allowavatars` set('yes','no') NOT NULL DEFAULT 'yes',
        `avatarrank` varchar(90) DEFAULT NULL,
        PRIMARY KEY  (`id`),
        KEY `title` (`title`)
        ) ENGINE=MyISAM
    ");
}

function schema_create_restricted($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "restricted`");
    $db->query("CREATE TABLE `" . $tablepre . "restricted` (
        `name` varchar(32) NOT NULL DEFAULT '',
        `id` smallint(6) NOT NULL auto_increment,
        `case_sensitivity` enum('0','1') NOT NULL DEFAULT '1',
        `partial` enum('0','1') NOT NULL DEFAULT '1',
        PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_robotcount($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "robotcount`");
    $db->query("CREATE TABLE `" . $tablepre . "robotcount` (
        `ipaddress` varchar(15) NOT NULL DEFAULT '',
        `onlinetime` int(10) NOT NULL DEFAULT '0',
        KEY `ipaddress` (`ipaddress`),
        KEY `onlinetime` (`onlinetime`)
        ) ENGINE=MyISAM
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_robots($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "robots`");
    $db->query("CREATE TABLE `" . $tablepre . "robots` (
        `robot_id` mediumint(9) NOT NULL auto_increment,
        `robot_string` varchar(50) NOT NULL DEFAULT '',
        `robot_fullname` varchar(50) NOT NULL DEFAULT '',
        PRIMARY KEY  (`robot_id`),
        UNIQUE KEY `bot_string` (`robot_string`)
        ) ENGINE=MyISAM
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_settings($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "settings`");
    $db->query("CREATE TABLE `" . $tablepre . "settings` (
			`config_name` varchar(200) NOT NULL DEFAULT '',
			`config_value` varchar(200) NOT NULL DEFAULT '',
			PRIMARY KEY (`config_name`,`config_value`)
		) ENGINE=MyISAM
	");
}

function schema_create_smilies($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "smilies`");
    $db->query("CREATE TABLE `" . $tablepre . "smilies` (
        `type` varchar(15) NOT NULL DEFAULT '',
        `code` varchar(40) NOT NULL DEFAULT '',
        `url` varchar(40) NOT NULL DEFAULT '',
        `id` smallint(6) NOT NULL auto_increment,
        PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_subscriptions($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "subscriptions`");
    $db->query("CREATE TABLE `" . $tablepre . "subscriptions` (
        `tid` int(10) NOT NULL DEFAULT '0',
        `username` varchar(32) NOT NULL DEFAULT '',
        `type` varchar(32) NOT NULL DEFAULT '',
        KEY `tid` (`tid`),
        KEY `username` (`username`(8))
        ) ENGINE=MyISAM
    ");
}

function schema_create_templates($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "templates`");
    $db->query("CREATE TABLE `" . $tablepre . "templates` (
  				`id` smallint(6) NOT NULL AUTO_INCREMENT,
  				`name` varchar(32) NOT NULL DEFAULT '',
  				`template` text NOT NULL,
  				PRIMARY KEY (`id`),
  				KEY `name` (`name`)
				) ENGINE=MyISAM 			
    ");
}

function schema_create_themes($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "themes`");
    $db->query("CREATE TABLE `" . $tablepre . "themes` (
        `themeid` smallint(3) NOT NULL auto_increment,
        `name` varchar(32) NOT NULL DEFAULT '',
        `bgcolor` varchar(25) NOT NULL DEFAULT '',
        `altbg1` varchar(15) NOT NULL DEFAULT '',
        `altbg2` varchar(15) NOT NULL DEFAULT '',
        `link` varchar(15) NOT NULL DEFAULT '',
        `bordercolor` varchar(15) NOT NULL DEFAULT '',
        `header` varchar(15) NOT NULL DEFAULT '',
        `headertext` varchar(15) NOT NULL DEFAULT '',
        `top` varchar(15) NOT NULL DEFAULT '',
        `catcolor` varchar(15) NOT NULL DEFAULT '',
        `tabletext` varchar(15) NOT NULL DEFAULT '',
        `text` varchar(15) NOT NULL DEFAULT '',
        `borderwidth` varchar(15) NOT NULL DEFAULT '',
        `tablewidth` varchar(15) NOT NULL DEFAULT '',
        `tablespace` varchar(15) NOT NULL DEFAULT '',
        `font` varchar(40) NOT NULL DEFAULT '',
        `fontsize` varchar(40) NOT NULL DEFAULT '',
        `boardimg` varchar(128) DEFAULT NULL,
        `imgdir` varchar(120) NOT NULL DEFAULT '',
        `smdir` varchar(120) NOT NULL DEFAULT '',
        `cattext` varchar(15) NOT NULL DEFAULT '',
        `outerbgcolor` varchar(25) NOT NULL DEFAULT '',
        `outertable` set('round','square','none') NOT NULL DEFAULT 'none',
        `outertablewidth` varchar(15) NOT NULL DEFAULT '',
        `outerbordercolor` varchar(15) NOT NULL DEFAULT '',
        `outerborderwidth` varchar(15) NOT NULL DEFAULT '0',
        `shadowfx` set('on','off') NOT NULL DEFAULT 'off',
        `threadopts` set('text','image') NOT NULL DEFAULT 'image',
        `themestatus` set('on','off') NOT NULL DEFAULT 'on',
        `navsymbol` varchar(50) NOT NULL DEFAULT '',
        `celloverfx` set('on','off') NOT NULL DEFAULT 'on',
        `riconstatus` set('on','off') NOT NULL DEFAULT 'off',
        `spacolor` varchar(15) NOT NULL DEFAULT '',
        `admcolor` varchar(15) NOT NULL DEFAULT '',
        `spmcolor` varchar(15) NOT NULL DEFAULT '',
        `modcolor` varchar(15) NOT NULL DEFAULT '',
        `memcolor` varchar(15) NOT NULL DEFAULT '',
        `ricondir` varchar(120) NOT NULL DEFAULT 'images/ricons',
        `highlight` varchar(15) NOT NULL DEFAULT '',
        `space_cats` set('on','off') NOT NULL DEFAULT 'on',
        PRIMARY KEY  (`themeid`),
        KEY `name` (`name`)
        ) ENGINE=MyISAM
    ");
}

function schema_create_threads($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "threads`");
    $db->query("CREATE TABLE `" . $tablepre . "threads` (
        `tid` int(10) NOT NULL auto_increment,
        `fid` smallint(6) NOT NULL DEFAULT '0',
        `subject` varchar(128) NOT NULL DEFAULT '',
        `icon` varchar(75) NOT NULL DEFAULT '',
        `views` int(100) NOT NULL DEFAULT '0',
        `replies` smallint(100) NOT NULL DEFAULT '0',
        `author` varchar(32) NOT NULL DEFAULT '',
        `closed` varchar(15) NOT NULL DEFAULT '',
        `topped` tinyint(1) NOT NULL DEFAULT '0',
        `pollopts` text NOT NULL DEFAULT '',
        PRIMARY KEY  (`tid`),
        KEY `fid` (`fid`),
        KEY `tid` (`tid`),
        KEY `author` (`author`(8)),
        KEY `closed` (`closed`)
        ) ENGINE=MyISAM
    ");
}

function schema_create_pm($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "pm`");
    $db->query("CREATE TABLE `" . $tablepre . "pm` (
				  `pmid` bigint(10) NOT NULL AUTO_INCREMENT,
				  `msgto` varchar(32) NOT NULL DEFAULT '',
				  `msgfrom` varchar(32) NOT NULL DEFAULT '',
				  `msgto_uid` int(7) NOT NULL DEFAULT '0',
				  `msgfrom_uid` int(7) NOT NULL DEFAULT '0',
				  `type` set('incoming','outgoing','draft') NOT NULL DEFAULT '',
				  `owner` varchar(32) NOT NULL DEFAULT '',
				  `folder` varchar(32) NOT NULL DEFAULT '',
				  `subject` varchar(64) NOT NULL DEFAULT '',
				  `message` text NOT NULL,
				  `dateline` int(10) NOT NULL DEFAULT '0',
				  `readstatus` set('yes','no') NOT NULL DEFAULT '',
				  `sentstatus` set('yes','no') NOT NULL DEFAULT '',
				  `usesig` set('yes','no') NOT NULL DEFAULT 'yes',
				  PRIMARY KEY (`pmid`),
				  KEY `msgto` (`msgto`(8)),
				  KEY `msgfrom` (`msgfrom`(8)),
				  KEY `folder` (`folder`(8)),
				  KEY `readstatus` (`readstatus`),
				  KEY `owner` (`owner`(8))
				) ENGINE=MyISAM
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_pm_attachments($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "pm_attachments`");
    $db->query("CREATE TABLE `" . $tablepre . "pm_attachments` (
				  `aid` int(10) NOT NULL AUTO_INCREMENT,
				  `pmid` int(10) NOT NULL DEFAULT '0',
				  `filename` varchar(120) NOT NULL DEFAULT '',
				  `filetype` varchar(120) NOT NULL DEFAULT '',
				  `filesize` varchar(120) NOT NULL DEFAULT '',
				  `fileheight` varchar(5) NOT NULL DEFAULT '',
				  `filewidth` varchar(5) NOT NULL DEFAULT '',
				  `attachment` longblob NOT NULL,
				  `owner` varchar(32) NOT NULL DEFAULT '',
				  PRIMARY KEY (`aid`),
				  KEY `pmid` (`pmid`),
				  KEY `owner` (`owner`(8)),
				  KEY `filesize` (`filesize`(8)),
				  KEY `filename` (`filename`(8))
				) ENGINE=MyISAM 
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_create_vote_tables($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "vote_desc`");
    $db->query("CREATE TABLE `" . $tablepre . "vote_desc` (
				  `vote_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				  `topic_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
				  `vote_text` text NOT NULL,
				  `vote_start` int(11) NOT NULL DEFAULT '0',
				  `vote_length` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`vote_id`),
				  KEY `topic_id` (`topic_id`)
				  ) ENGINE=MyISAM
    ");

    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "vote_results`");
    $db->query("CREATE TABLE `" . $tablepre . "vote_results` (
        `vote_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
        `vote_option_id` tinyint(4) unsigned NOT NULL DEFAULT '0',
        `vote_option_text` varchar(255) NOT NULL DEFAULT '',
        `vote_result` int(11) NOT NULL DEFAULT '0',
        KEY `vote_option_id` (`vote_option_id`),
        KEY `vote_id` (`vote_id`)
        ) ENGINE=MyISAM
    ");

    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "vote_voters`");
    $db->query("CREATE TABLE `" . $tablepre . "vote_voters` (
        `vote_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
        `vote_user_id` mediumint(8) NOT NULL DEFAULT '0',
        `vote_user_ip` char(8) NOT NULL DEFAULT '',
        KEY `vote_id` (`vote_id`),
        KEY `vote_user_id` (`vote_user_id`),
        KEY `vote_user_ip` (`vote_user_ip`)
        ) ENGINE=MyISAM
    ");
}

function schema_create_whosonline($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "whosonline`");
    $db->query("CREATE TABLE `" . $tablepre . "whosonline` (
        `username` varchar(32) NOT NULL DEFAULT '',
        `ip` varchar(15) NOT NULL DEFAULT '',
        `time` int(10) NOT NULL DEFAULT '0',
        `location` varchar(150) NOT NULL DEFAULT '',
        `invisible` set('1','0') DEFAULT '0',
        `robotname` varchar(50) NOT NULL DEFAULT '',
        KEY `username` (`username`(8)),
        KEY `ip` (`ip`),
        KEY `time` (`time`),
        KEY `invisible` (`invisible`)
        ) ENGINE=MyISAM
    ");

}

function schema_create_words($db, $tablepre)
{
    $db->query("DROP TABLE IF EXISTS `" . $tablepre . "words`");
    $db->query("CREATE TABLE `" . $tablepre . "words` (
        `find` varchar(60) NOT NULL DEFAULT '',
        `replace1` varchar(60) NOT NULL DEFAULT '',
        `id` smallint(6) NOT NULL auto_increment,
        PRIMARY KEY  (`id`),
        KEY `find` (`find`)
        ) ENGINE=MyISAM
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_insert_dateformats($db, $tablepre)
{
    $db->query("INSERT INTO `" . $tablepre . "dateformats` (did, dateformat) VALUES (1, 'dd-mm-yy'),(2, 'dd-mm-yyyy'),(3, 'mm-dd-yy'),(4, 'mm-dd-yyyy'),(5, 'dd/mm/yy'),(6, 'dd/mm/yyyy'),(7, 'mm/dd/yy'),(8, 'mm/dd/yyyy'),(9, 'F d, Y'),(10, 'M d, Y'),(11, 'd F Y'),(12, 'F jS, Y'),(13, 'F jS Y'),(14, 'dd.mm.yy'),(15, 'dd.mm.yyyy'),(16, 'Ymd')");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_insert_faq($db, $tablepre)
{
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('group', 'User Maintenance', 'on', 1, '', '', '', 0, '', 'usermaint', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'Is registration required?', 'on', 1, 'That depends on the board settings because administrators can change the settings so that you have to be registered to view posts. You also usually have to be registered to reply and start new posts but this does depend on the board settings. To register simply click the register link at the top of the board.<br /><br />It is advised that you register so you can receive e-mails from the administrator.', 'yes', 'yes', 1, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'Does this forum use cookies?', 'on', 2, 'Yes. This bulletin board uses cookies to store your log in information, last visit, and threads that you have visited. We do this to make it easier for you so you can see which posts contain new replies and so you do not have to enter your username and password when posting or other certain things.<br /><br />If you log out, your cookies will be cleared.', 'yes', 'yes', 1, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'How do I add a signature?', 'on', 3, 'To add a signature to your posts you have to log into your UserCP and insert into the signature text box the signature you wish to use.<br /><br />BB Code maybe turned off or on. This can effect what you can insert into your signature.', 'yes', 'yes', 1, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'How do I get my own picture (avatar) under my name?', 'on', 4, 'Again in your UserCP there is a place for an Avatar and avatar is the image under your name. Check with your Admin about the size of your avatar, its usually considered common courtesy to use one under 150 pixels wide.', 'yes', 'yes', 1, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'How can lost passwords be recovered?', 'on', 5, 'If you have forgotten your password, do not worry. Head over to the lost password section and fill in the form and your password will be e-mailed to you.', 'yes', 'yes', 1, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'What is U2U?', 'on', 6, 'U2U means User to User. It is a simple messaging client that you can use to send messages to fellow members on this board. You can check your U2U inbox by clicking the U2U link or going to your UserCP.<br /><br />The board administrator might have disabled this function for certain users.', 'yes', 'yes', 1, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('group', 'Using the Board', 'on', 2, '', '', '', 0, '', 'using', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'Where do I log in?', 'on', 1, 'There is a button that says Login in the menu at the top, clicking this button will take you to the login page, where you can login. Here you simply input your username, and your password, click the login button, and thats it!', 'yes', 'yes', 8, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'Where do I log out?', 'on', 2, 'You can logout by clicking Logout at the top of the page. When you logout the cookies that store your username and password will be removed and you will become a Guest or Anonymous user.', 'yes', 'yes', 8, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'How do I search the board?', 'on', 3, 'By clicking on the Search button in the menu. Then inputting what you wish to search for, you can restrict where you search with the drop down lists.', 'yes', 'yes', 8, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'How do I U2U a member?', 'on', 4, 'First click on the U2U button in the menu, another smaller window will pop-up, from there you can access the Send a U2U screen, by clicking on it at the top. Place the users name in the To field, and then insert a subject and a message and click Send. ', 'yes', 'yes', 8, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'Where can I view all the members?', 'on', 5, 'You can view all the members by clicking on the Members List link in the menu.', 'yes', 'yes', 8, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('group', 'Posting and reading messages', 'on', 3, '', '', '', 0, '', 'messages', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'How do I post a new message?', 'on', 1, 'When inside of a forum, clicking on Post New Topic button, will allow you to post. You need to fill in your information, a topic and a message, then click on the Post New Topic button.', 'yes', 'yes', 14, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'Can I reply to a message?', 'on', 2, 'Yes, this is done the same as posting a new one, except that you must be in a Topic, and you need to click Post Reply instead of Post new Topic.', 'yes', 'yes', 14, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'Is it possible to delete a message?', 'on', 3, 'No, you are not permitted to delete your own messages. This is a administrative option only. This ensures the content is reviewed properly incase of abuse.', 'yes', 'yes', 14, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'How do I edit one of my posts?', 'on', 4, 'You can edit a post by clicking on the Edit Post button underneath your message. You can only edit your own posts and sometimes this feature is disabled.', 'yes', 'yes', 14, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'Can I insert an attachment?', 'on', 5, 'Yes you can insert an attachment with any of your posts. The file size of the attachment must be under 1 Megabyte for it to be accepted. You can attach a file on the New Post and Post Reply pages with the upload field.', 'yes', 'yes', 14, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'What are smilies?', 'on', 6, 'Smilies are the little faces to the right of the input for message. They display graphical faces instead of simply a :).<br />Here is a list of current supported smilies: ', 'yes', 'yes', 14, 'yes', '', 1)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'How can I create and vote in polls?', 'on', 7, 'You can create a poll by visiting the forum you want to post the poll in and click on Start Poll. The screen following after you click the button is just like a normal new thread page but has an extra box for Poll Answers. You should enter one answer per line. You can vote on polls in threads by visiting the thread with the poll in it and selecting the option you want to vote for, then clicking the submit button. You can only vote on a poll once, so once you vote, you cant change your mind. The Administrator could have disabled this option for each forum.', 'yes', 'yes', 14, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('group', 'Misc Questions', 'on', 4, '', '', '', 0, '', 'misc', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'What is BB code?', 'on', 1, 'You can use BB Code, a simplified version of HTML, in your posts to create certain effects.<br /><br />[b]Text here[/b]   (Bold Text)<br /><br />[i]Text here[/i]   (Italicized Text)<br /><br />[u]Text here[/u]   (Underlined Text)<br /><br />[url]http://www.php.net[/url]   (Link)<br /><br />[url=http://www.php.net]Home Page of PHP[/url]   (Link)<br /><br />[email] mail@yourdomain.ext[/email]   (E-Mail Link)<br /><br />[email=mail@yourdomain.ext]E-mail Me![/email]   (E-Mail Link)<br /><br />[quote]Text here[/quote]   (Quoted Text)<br /><br />[code]Text here[/code]   (Text With Preserved Formatting)<br /><br />[img]http://www.php.net/gifs/php_logo.gif[/img]   (Image)<br /><br />[img=50x50]http://www.php.net/gifs/php_logo.gif[/img]   (Resized Image)<br /><br />[flash=200x100]http://www.macromedia.com/flash.swf[/flash]   (Flash Movie)<br /><br />[color=red]This color is red[/color]   (Colored Text)<br /><br />[size=3]This font size is 3[/size]   (Sized Text)<br /><br />[font=Tahoma]This font is Tahoma[/font]   (Different Font Than DEFAULT)<br /><br />[align=center]This is centered[/align]   (Aligned Text)<br /><br />[list]<br />[*]List Item #1<br />[*]List Item #2<br />[*]List Item #2<br />[/list]   (List)', '', '', 22, '', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'Can I become a moderator?', 'on', 2, 'Most of the time the answer is no, but ask your Admin.', 'yes', 'yes', 22, 'yes', '', 0)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('item', 'What are User Ranks?', 'on', 3, 'Based on the number of posts you have, you are assigned to a rank. Each rank has its own status and its own amount of stars. Below are the user rank settings for this board:', 'yes', 'yes', 22, 'yes', '', 2)");
    $db->query("INSERT INTO " . $tablepre . "faq (type, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode, code, view) VALUES ('rulesset', 'The board administrator has requested that all users must agree again to the terms before they can continue using the services on this board.<br />Please read the following terms and if you agree to them, hit the I agree button found at the bottom of the terms.', '', 0, '', 'yes', 'yes', 0, 'yes', '', 0)");
}

function schema_insert_ranks($db, $tablepre)
{
    $db->query("INSERT INTO `" . $tablepre . "ranks` (title, posts, stars, allowavatars) VALUES ('Newbie', 0, 1, 'yes')");
    $db->query("INSERT INTO `" . $tablepre . "ranks` (title, posts, stars, allowavatars) VALUES ('Junior Member', 2, 2, 'yes')");
    $db->query("INSERT INTO `" . $tablepre . "ranks` (title, posts, stars, allowavatars) VALUES ('Member', 100, 3, 'yes')");
    $db->query("INSERT INTO `" . $tablepre . "ranks` (title, posts, stars, allowavatars) VALUES ('Senior Member', 500, 4, 'yes')");
    $db->query("INSERT INTO `" . $tablepre . "ranks` (title, posts, stars, allowavatars) VALUES ('Posting Freak', 1000, 5, 'yes')");
    // special ranks - DO NOT CHANGE ANY BELOW
    $db->query("INSERT INTO `" . $tablepre . "ranks` (title, posts, stars, allowavatars) VALUES ('Moderator', -1, 6, 'yes')");
    $db->query("INSERT INTO `" . $tablepre . "ranks` (title, posts, stars, allowavatars) VALUES ('Super Moderator', -1, 7, 'yes')");
    $db->query("INSERT INTO `" . $tablepre . "ranks` (title, posts, stars, allowavatars) VALUES ('Administrator', -1, 8, 'yes')");
    $db->query("INSERT INTO `" . $tablepre . "ranks` (title, posts, stars, allowavatars) VALUES ('Super Administrator', -1, 9, 'yes')");
}

function schema_insert_restricted($db, $tablepre)
{
    $db->query("INSERT INTO `" . $tablepre . "restricted` (`name`, `case_sensitivity`, `partial`) VALUES ('Anonymous', '1', '0')");
    $db->query("INSERT INTO `" . $tablepre . "restricted` (`name`, `case_sensitivity`, `partial`) VALUES ('xguest123', '1', '0')");
    $db->query("INSERT INTO `" . $tablepre . "restricted` (`name`, `case_sensitivity`, `partial`) VALUES ('||~|~||', '1', '1')");
    $db->query("INSERT INTO `" . $tablepre . "restricted` (`name`, `case_sensitivity`, `partial`) VALUES ('#|#', '1', '1')");
    $db->query("INSERT INTO `" . $tablepre . "restricted` (`name`, `case_sensitivity`, `partial`) VALUES ('//||//', '1', '1')");
    $db->query("INSERT INTO `" . $tablepre . "restricted` (`name`, `case_sensitivity`, `partial`) VALUES ('<script', '1', '1')");
    $db->query("INSERT INTO `" . $tablepre . "restricted` (`name`, `case_sensitivity`, `partial`) VALUES ('xrobot123', '1', '0')");
}

function schema_insert_settings($db, $tablepre)
{
    if (function_exists('ImageCreate')) {
        $regimage = 'on';
    } else {
        $regimage = 'off';
    }

    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('adminemail','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('adminnotes','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('allowrankedit','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('attachborder','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('attachicon_status','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('attachimgpost','on');");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('attach_num_default','3')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avastatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatars_perpage','20')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatars_perrow','5')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatars_status','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatar_filesize','15000')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatar_max_height','125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatar_max_width','125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatar_new_height','125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatar_new_width','125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatar_path','./images/avatars/uploads')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avatar_whocanupload','all')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('avgalpath','./images/avatars/gallery')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('bbcimg_status','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('bbc_maxht','800')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('bbc_maxwd','600')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('bbinsert','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('bbname','GaiaBB')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('bboffreason','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('bbrules','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('bbrulestxt','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('bbstatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('boardurl','https://github.com/vanderaj/gaiabb/')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('captcha_colortype','multiple')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('captcha_fontpath','include/captcha')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('captcha_maxattempts','5')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('captcha_maxchars','6')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('captcha_minchars','4')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('captcha_status','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('comment','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('contactus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('coppa','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('copyright','Logo is (C) 2004 mrfeldi. Posts are (C) of the individuals who posted them - GaiaBB takes no responsibility for members postings.')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('customposts','100')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('dateformat','F jS, Y')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('daylightsavings','0')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('def_tz','10')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('dotfolders','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('doublee','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('editedby','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('emailcheck','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('faqstatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('floodctrl','60')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('footer_options','queries-phpsql-loadtimes')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('forumjump','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('hideprivate','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('hottopic','25')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('indexnews','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('indexnewstxt','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('indexstats','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('ipcheck','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('ipreg','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('langfile','English')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('login_max_attempts','5')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('maxsigchars','255')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('max_attach_size','512000')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('max_attheight','800')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('max_attwidth','800')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('max_avatar_size','125x125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('max_photo_size','125x125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('max_reg_day','100')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('memberperpage','30')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('memliststatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('metatag_description','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('metatag_keywords','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('metatag_status','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('mod_status','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('mostonlinecount','0')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('mostonlinetime','0')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('notepadstatus','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('notifyonreg','pm')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('photostatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('photo_filesize','15000')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('photo_max_height','125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('photo_max_width','125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('photo_new_height','125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('photo_new_width','125')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('photo_path','./images/photos')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('photo_whocanupload','all')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('pmattachstatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('pmposts','5')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('pmquota','999')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('pmstatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('pmwelcomefrom','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('pmwelcomemessage','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('pmwelcomestatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('pmwelcomesubject','Welcome to GaiaBB!')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('postperpage','30')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('predformat','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('regimage','$regimage')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('regstatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('regviewonly','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('reportpost','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('resetsig','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('rpg_status','yes')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('schemaver','41')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('searchstatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('showsubs','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('show_full_info','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('sigbbcode','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('sitename','GaiaBB')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('siteurl','https://github.com/vanderaj/gaiabb/')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smcols','4')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smileyinsert','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smtotal','16')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smtphost','localhost')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smtppassword','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smtpport','25')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smtpServer','localhost')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smtptimeout','30')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smtpusername','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('smtp_status','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('specq','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('stats','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('theme','8')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('timeformat','12')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('topicactivity_status','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('topicperpage','30')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('usernamenotify','')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('viewattach','no')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('viewlocation','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('viewsigminposts','5')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('whosguest_status','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('whosonlinestatus','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('whosonlinetoday','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('whosoptomized','on')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('whosrobotname_status','off')");
    $db->query("INSERT INTO `" . $tablepre . "settings` (`config_name`,`config_value`) VALUES ('whosrobot_status','off')");

}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_insert_robots($db, $tablepre)
{
    $db->query("INSERT INTO `" . $tablepre . "robots` VALUES
        (1,'acme.spider','Acme Spider'),
        (2,'ahoythehomepagefinder','Ahoy! The Homepage Finder'),
        (3,'alkaline','Alkaline'),
        (4,'appie','Walhello appie'),
        (5,'arachnophilia','Arachnophilia'),
        (6,'architext','ArchitextSpider'),
        (7,'aretha','Aretha'),
        (8,'ariadne','ARIADNE'),
        (9,'arks','arks'),
        (10,'aspider','ASpider (Associative Spider)'),
        (11,'atn.txt','ATN Worldwide'),
        (12,'atomz','Atomz.com Search Robot'),
        (13,'auresys','AURESYS'),
        (14,'backrub','BackRub'),
        (15,'bigbrother','Big Brother'),
        (16,'bjaaland','Bjaaland'),
        (17,'blackwidow','BlackWidow'),
        (18,'blindekuh','Die Blinde Kuh'),
        (19,'bloodhound','Bloodhound'),
        (20,'brightnet','bright.net caching robot'),
        (21,'bspider','BSpider'),
        (22,'cactvschemistryspider','CACTVS Chemistry Spider'),
        (23,'calif[^r]','Calif'),
        (24,'cassandra','Cassandra'),
        (25,'cgireader','Digimarc Marcspider/CGI'),
        (26,'checkbot','Checkbot'),
        (27,'churl','churl'),
        (28,'cmc','CMC/0.01'),
        (29,'collective','Collective'),
        (30,'combine','Combine System'),
        (31,'conceptbot','Conceptbot'),
        (32,'coolbot','CoolBot'),
        (33,'core','Web Core / Roots'),
        (34,'cosmos','XYLEME Robot'),
        (35,'cruiser','Internet Cruiser Robot'),
        (36,'cusco','Cusco'),
        (37,'cyberspyder','CyberSpyder Link Test'),
        (38,'deweb','DeWeb(c) Katalog/Index'),
        (39,'dienstspider','DienstSpider'),
        (40,'digger','Digger'),
        (41,'diibot','Digital Integrity Robot'),
        (42,'directhit','Direct Hit Grabber'),
        (43,'dnabot','DNAbot'),
        (44,'download_express','DownLoad Express'),
        (45,'dragonbot','DragonBot'),
        (46,'dwcp','DWCP (Dridus Web Cataloging Project)'),
        (47,'e-collector','e-collector'),
        (48,'ebiness','EbiNess'),
        (49,'eit','EIT Link Verifier Robot'),
        (50,'elfinbot','ELFINBOT'),
        (51,'emacs','Emacs-w3 Search Engine'),
        (52,'emcspider','ananzi'),
        (53,'esther','Esther'),
        (54,'evliyacelebi','Evliya Celebi'),
        (55,'nzexplorer','nzexplorer'),
        (56,'fdse','Fluid Dynamics Search Engine robot'),
        (57,'felix','Felix IDE'),
        (58,'ferret','Wild Ferret Web Hopper #1, #2, #3'),
        (59,'fetchrover','FetchRover'),
        (60,'fido','fido'),
        (61,'finnish','H�m�h�kki'),
        (62,'fireball','KIT-Fireball'),
        (63,'[^a]fish','Fish search'),
        (64,'fouineur','Fouineur'),
        (65,'francoroute','Robot Francoroute'),
        (66,'freecrawl','Freecrawl'),
        (67,'funnelweb','FunnelWeb'),
        (68,'gama','gammaSpider, FocusedCrawler'),
        (69,'gazz','gazz'),
        (70,'gcreep','GCreep'),
        (71,'getbot','GetBot'),
        (72,'geturl','GetURL'),
        (73,'golem','Golem'),
        (74,'googlebot','Googlebot (Google)'),
        (75,'grapnel','Grapnel/0.01 Experiment'),
        (76,'griffon','Griffon'),
        (77,'gromit','Gromit'),
        (78,'gulliver','Northern Light Gulliver'),
        (79,'hambot','HamBot'),
        (80,'harvest','Harvest'),
        (81,'havindex','havIndex'),
        (82,'hometown','Hometown Spider Pro'),
        (83,'htdig','ht://Dig'),
        (84,'htmlgobble','HTMLgobble'),
        (85,'hyperdecontextualizer','Hyper-Decontextualizer'),
        (86,'iajabot','iajaBot'),
        (87,'ibm','IBM_Planetwide'),
        (88,'iconoclast','Popular Iconoclast'),
        (89,'ilse','Ingrid'),
        (90,'imagelock','Imagelock'),
        (91,'incywincy','IncyWincy'),
        (92,'informant','Informant'),
        (93,'infoseek','InfoSeek Robot 1.0'),
        (94,'infoseeksidewinder','Infoseek Sidewinder'),
        (95,'infospider','InfoSpiders'),
        (96,'inspectorwww','Inspector Web'),
        (97,'intelliagent','IntelliAgent'),
        (98,'irobot','I, Robot'),
        (99,'iron33','Iron33'),
        (100,'israelisearch','Israeli-search'),
        (101,'javabee','JavaBee'),
        (102,'jbot','JBot Java Web Robot'),
        (103,'jcrawler','JCrawler'),
        (104,'jeeves','Jeeves'),
        (105,'jobo','JoBo Java Web Robot'),
        (106,'jobot','Jobot'),
        (107,'joebot','JoeBot'),
        (108,'jubii','The Jubii Indexing Robot'),
        (109,'jumpstation','JumpStation'),
        (110,'katipo','Katipo'),
        (111,'kdd','KDD-Explorer'),
        (112,'kilroy','Kilroy'),
        (113,'ko_yappo_robot','KO_Yappo_Robot'),
        (114,'labelgrabber.txt','LabelGrabber'),
        (115,'larbin','larbin'),
        (116,'legs','legs'),
        (117,'linkidator','Link Validator'),
        (118,'linkscan','LinkScan'),
        (119,'linkwalker','LinkWalker'),
        (120,'lockon','Lockon'),
        (121,'logo_gif','logo.gif Crawler'),
        (122,'lycos','Lycos'),
        (123,'macworm','Mac WWWWorm'),
        (124,'magpie','Magpie'),
        (125,'marvin','marvin/infoseek'),
        (126,'mattie','Mattie'),
        (127,'mediafox','MediaFox'),
        (128,'merzscope','MerzScope'),
        (129,'meshexplorer','NEC-MeshExplorer'),
        (130,'mindcrawler','MindCrawler'),
        (131,'moget','moget'),
        (132,'momspider','MOMspider'),
        (133,'monster','Monster'),
        (134,'motor','Motor'),
        (135,'muscatferret','Muscat Ferret'),
        (136,'mwdsearch','Mwd.Search'),
        (137,'myweb','Internet Shinchakubin'),
        (138,'netcarta','NetCarta WebMap Engine'),
        (139,'netcraft','Netcraft Web Server Survey'),
        (140,'netmechanic','NetMechanic'),
        (141,'netscoop','NetScoop'),
        (142,'newscan-online','newscan-online'),
        (143,'nhse','NHSE Web Forager'),
        (144,'nomad','Nomad'),
        (145,'northstar','The NorthStar Robot'),
        (146,'occam','Occam'),
        (147,'octopus','HKU WWW Octopus'),
        (148,'openfind','Openfind data gatherer'),
        (149,'orb_search','Orb Search'),
        (150,'packrat','Pack Rat'),
        (151,'pageboy','PageBoy'),
        (152,'parasite','ParaSite'),
        (153,'patric','Patric'),
        (154,'pegasus','pegasus'),
        (155,'perignator','The Peregrinator'),
        (156,'perlcrawler','PerlCrawler 1.0'),
        (157,'phantom','Phantom'),
        (158,'piltdownman','PiltdownMan'),
        (159,'pimptrain','Pimptrain.com\'s robot'),
        (160,'pioneer','Pioneer'),
        (161,'pitkow','html_analyzer'),
        (162,'pjspider','Portal Juice Spider'),
        (163,'pka','PGP Key Agent'),
        (164,'plumtreewebaccessor','PlumtreeWebAccessor'),
        (165,'poppi','Poppi'),
        (166,'portalb','PortalB Spider'),
        (167,'puu','GetterroboPlus Puu'),
        (168,'python','The Python Robot'),
        (169,'raven','Raven Search'),
        (170,'rbse','RBSE Spider'),
        (171,'resumerobot','Resume Robot'),
        (172,'rhcs','RoadHouse Crawling System'),
        (173,'roadrunner','Road Runner: The ImageScape Robot'),
        (174,'robbie','Robbie the Robot'),
        (175,'robi','ComputingSite Robi/1.0'),
        (176,'robofox','RoboFox'),
        (177,'robozilla','Robozilla'),
        (178,'roverbot','Roverbot'),
        (179,'rules','RuLeS'),
        (180,'safetynetrobot','SafetyNet Robot'),
        (181,'scooter','Scooter (AltaVista)'),
        (182,'search_au','Search.Aus-AU.COM'),
        (183,'searchprocess','SearchProcess'),
        (184,'senrigan','Senrigan'),
        (185,'sgscout','SG-Scout'),
        (186,'shaggy','ShagSeeker'),
        (187,'shaihulud','Shai\'Hulud'),
        (188,'sift','Sift'),
        (189,'simbot','Simmany Robot Ver1.0'),
        (190,'site-valet','Site Valet'),
        (191,'sitegrabber','Open Text Index Robot'),
        (192,'sitetech','SiteTech-Rover'),
        (193,'slcrawler','SLCrawler'),
        (194,'slurp','Inktomi Slurp'),
        (195,'smartspider','Smart Spider'),
        (196,'snooper','Snooper'),
        (197,'solbot','Solbot'),
        (198,'spanner','Spanner'),
        (199,'speedy','Speedy Spider'),
        (200,'spider_monkey','spider_monkey'),
        (201,'spiderbot','SpiderBot'),
        (202,'spiderline','Spiderline Crawler'),
        (203,'spiderman','SpiderMan'),
        (204,'spiderview','SpiderView(tm)'),
        (205,'spry','Spry Wizard Robot'),
        (206,'ssearcher','Site Searcher'),
        (207,'suke','Suke'),
        (208,'suntek','suntek search engine'),
        (209,'sven','Sven'),
        (210,'tach_bw','TACH Black Widow'),
        (211,'tarantula','Tarantula'),
        (212,'tarspider','tarspider'),
        (213,'techbot','TechBOT'),
        (214,'templeton','Templeton'),
        (215,'teoma_agent1','TeomaTechnologies'),
        (216,'titin','TitIn'),
        (217,'titan','TITAN'),
        (218,'tkwww','The TkWWW Robot'),
        (219,'tlspider','TLSpider'),
        (220,'ucsd','UCSD Crawl'),
        (221,'udmsearch','UdmSearch'),
        (222,'urlck','URL Check'),
        (223,'valkyrie','Valkyrie'),
        (224,'victoria','Victoria'),
        (225,'visionsearch','vision-search'),
        (226,'voyager','Voyager'),
        (227,'vwbot','VWbot'),
        (228,'w3index','The NWI Robot'),
        (229,'w3m2','W3M2'),
        (230,'wallpaper','WallPaper'),
        (231,'wanderer','the World Wide Web Wanderer'),
        (232,'wapspider','w@pSpider by wap4.com'),
        (233,'webbandit','WebBandit Web Spider'),
        (234,'webcatcher','WebCatcher'),
        (235,'webcopy','WebCopy'),
        (236,'webfetcher','Webfetcher'),
        (237,'webfoot','The Webfoot Robot'),
        (238,'weblayers','Weblayers'),
        (239,'weblinker','WebLinker'),
        (240,'webmirror','WebMirror'),
        (241,'webmoose','The Web Moose'),
        (242,'webquest','WebQuest'),
        (243,'webreader','Digimarc MarcSpider'),
        (244,'webreaper','WebReaper'),
        (245,'websnarf','Websnarf'),
        (246,'webspider','WebSpider'),
        (247,'webvac','WebVac'),
        (248,'webwalk','webwalk'),
        (249,'webwalker','WebWalker'),
        (250,'webwatch','WebWatch'),
        (251,'wget','Wget'),
        (252,'whatuseek','whatUseek Winona'),
        (253,'whowhere','WhoWhere Robot'),
        (254,'wired-digital','Wired Digital'),
        (255,'wmir','w3mir'),
        (256,'wolp','WebStolperer'),
        (257,'wombat','The Web Wombat'),
        (258,'worm','The World Wide Web Worm'),
        (259,'wwwc','WWWC Ver 0.2.5'),
        (260,'wz101','WebZinger'),
        (261,'xget','XGET'),
        (262,'nederland.zoek','Nederland.zoek'),
        (263,'antibot','Antibot'),
        (264,'awbot','AWBot'),
        (265,'baiduspider','BaiDuSpider'),
        (266,'bobby','Bobby'),
        (267,'boris','Boris'),
        (268,'bumblebee','Bumblebee (relevare.com)'),
        (269,'cscrawler','CsCrawler'),
        (270,'daviesbot','DaviesBot'),
        (271,'digout4u','Digout4u'),
        (272,'echo','EchO!'),
        (273,'exactseek','ExactSeek Crawler'),
        (274,'ezresult','Ezresult'),
        (275,'fast-webcrawler','Fast-Webcrawler (AllTheWeb)'),
        (276,'gigabot','GigaBot'),
        (277,'gnodspider','GNOD Spider'),
        (278,'ia_archiver','Alexa (IA Archiver)'),
        (279,'internetseer','InternetSeer'),
        (280,'jennybot','JennyBot'),
        (281,'justview','JustView'),
        (282,'linkbot','LinkBot'),
        (283,'linkchecker','LinkChecker'),
        (284,'mercator','Mercator'),
        (285,'msiecrawler','MSIECrawler'),
        (286,'perman','Perman surfer'),
        (287,'petersnews','Petersnews'),
        (288,'pompos','Pompos'),
        (289,'psbot','psBot'),
        (290,'redalert','Red Alert'),
        (291,'shoutcast','Shoutcast Directory Service'),
        (292,'slysearch','SlySearch'),
        (293,'turnitinbot','Turn It In'),
        (294,'ultraseek','Ultraseek'),
        (295,'unlost_web_crawler','Unlost Web Crawler'),
        (296,'voila','Voila'),
        (297,'webbase','WebBase'),
        (298,'webcompass','webcompass'),
        (299,'wisenutbot','WISENutbot (Looksmart)'),
        (300,'yandex','Yandex bot'),
        (301,'zyborg','Zyborg (Looksmart)'),
        (308,'mixcat','morris - mixcat crawler'),
        (305,'netresearchserver','Net Research Server'),
        (306,'vagabondo','vagabondo (test version WiseGuys webagent)'),
        (307,'szukacz','Szukacz crawler'),
        (309,'grub-client','Grub\'s distributed crawler'),
        (310,'fluffy','fluffy (searchhippo)'),
        (311,'webtrends link analyzer','webtrends link analyzer'),
        (312,'naverrobot','naver'),
        (313,'steeler','steeler'),
        (314,'bordermanager','bordermanager'),
        (315,'nutch','Nutch'),
        (316,'teradex','Teradex'),
        (317,'deepindex','DeepIndex'),
        (318,'npbot','NPBot'),
        (319,'webcraftboot','Webcraftboot'),
        (320,'franklin locator','Franklin locator'),
        (321,'internet ninja','Internet ninja'),
        (322,'space bison','Space bison'),
        (323,'gornker','gornker crawler'),
        (324,'gaisbot','Gaisbot'),
        (325,'cj spider','CJ spider'),
        (326,'semanticdiscovery','Semantic Discovery'),
        (327,'zao','Zao'),
        (328,'web downloader','Web Downloader'),
        (329,'webstripper','Webstripper'),
        (330,'zeus','Zeus'),
        (331,'webrace','Webrace'),
        (332,'christcrawler','ChristCENTRAL'),
        (333,'webfilter','Webfilter'),
        (334,'webgather','Webgather'),
        (335,'surveybot','Surveybot'),
        (336,'nitle blog spider','Nitle Blog Spider'),
        (337,'galaxybot','Galaxybot'),
        (338,'fangcrawl','FangCrawl'),
        (339,'searchspider','SearchSpider'),
        (340,'msnbot','MSN Bot'),
        (341,'computer_and_automation_research_institute_crawler','computer and automation research institute crawler'),
        (342,'overture-webcrawler','overture-webcrawler'),
        (343,'exalead ng','exalead ng'),
        (344,'denmex websearch','denmex websearch'),
        (345,'linkfilter.net url verifier','linkfilter.net url verifier'),
        (346,'mac finder','mac finder'),
        (347,'polybot','polybot'),
        (348,'quepasacreep','quepasacreep'),
        (349,'xenu link sleuth','xenu link sleuth'),
        (350,'hatena antenna','hatena antenna'),
        (351,'timbobot','timbobot'),
        (352,'waypath scout','waypath scout'),
        (353,'technoratibot','technoratibot'),
        (354,'frontier','frontier'),
        (355,'blogosphere','blogosphere'),
        (356,'my little bot','my little bot'),
        (357,'illinois state tech labs','illinois state tech labs'),
        (358,'splatsearch.com','splatsearch'),
        (359,'blogshares bot','blogshares bot'),
        (360,'fastbuzz.com','fastbuzz'),
        (361,'obidos-bot','obidos'),
        (362,'blogwise.com-metachecker','blogwise.com metachecker'),
        (363,'bravobrian bstop','bravobrian bstop'),
        (364,'feedster crawler','feedster'),
        (365,'isspider','blogpulse'),
        (366,'syndic8','syndic8'),
        (367,'blogvisioneye','blogvisioneye'),
        (368,'downes/referrers','downes/referrers'),
        (369,'naverbot','naverbot'),
        (370,'soziopath','soziopath'),
        (371,'nextopiabot','nextopiabot'),
        (372,'ingrid','ingrid'),
        (373,'vspider','vspider'),
        (374,'yahoo','Yahoo'),
        (375,'sherlock-spider','Sherlock Spider'),
        (376,'mercubot','Mercubot'),
        (377,'mediapartners-google','Mediapartners Google'),
        (378,'jetbot','JetBot'),
        (379,'faxobot','FaxoBot'),
        (380,'cosmixcrawler','cosmix crawler'),
        (381,'exabot','exabot'),
        (382,'sitespider','sitespider'),
        (383,'pipeliner','pipeliner'),
        (384,'ccgcrawl','ccgcrawl'),
        (385,'cydralspider','cydralspider'),
        (386,'crawlconvera','crawlconvera'),
        (387,'blogwatcher','blogwatcher'),
        (388,'mozdex','mozdex'),
        (389,'aleksika spider','aleksika spider'),
        (390,'e-societyrobot','e-societyrobot'),
        (391,'enterprise_search','enterprise search'),
        (392,'seekbot','seekbot')
    ");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_insert_problue($db, $tablepre)
{
    $db->query("INSERT INTO `" . $tablepre . "themes` VALUES (0, 'GaiaBB Pro Blue', '#FFFFFF', '#97A6BF', '#E4EAF2', '#000000', '#5176B5', '#E4EAF2', '#000000', 'topbg.gif', 'catbar.gif', '#000000', '#000000', '1px', '97%', '5px', 'Tahoma, Arial, Helvetica, Verdana', '11px', 'logo.png', 'images/problue', 'images/smilies', '#000000', '#97A6BF', 'square', '100%', '#5176B5', '1px', 'on', 'image', 'on', 'lastpost.gif', 'on', 'on', '', '', '', '', '', 'images/ricons', '', 'on')");
    $themeid = $db->insert_id();

    $db->query("UPDATE " . $tablepre . "settings SET config_value = '$themeid' WHERE config_name = 'theme'");
    $db->query("UPDATE `" . $tablepre . "forums` SET theme = 0");
    $db->query("UPDATE `" . $tablepre . "members` SET theme = 0");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function schema_insert_smilies($db, $tablepre)
{
    $db->query("DELETE FROM " . $tablepre . "smilies WHERE type='smiley' AND code in (':punk:',':post:',':no:',':ninja:',':mad:',':love:',':crazy:',':cool:',':borg:',':blush:',':lol:',':kiss:',':info:',':grind:',':fakesniff:',':!:',':dork:',':D',':?:',':rolleyes:',':(',':)',':smilegrin:',':smirk:',':sniffle:',':spin:',':starhit:',':td:',':tu:',':yes:',';)')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':punk:', 'punk.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':post:', 'post.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':no:', 'no.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':ninja:', 'ninja.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':mad:', 'mad.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':love:', 'love.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':crazy:', 'crazy.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':cool:', 'cool.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':borg:', 'borg.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':blush:', 'blush.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':lol:', 'lol.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':kiss:', 'kiss.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':grind:', 'grind.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':fakesniff:', 'fake_sniffle.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':!:', 'exclamation.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':dork:', 'dork.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':D', 'bigsmile.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':?:', 'question.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':rolleyes:', 'rolleyes.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':(', 'sad.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':)', 'smile.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':smilegrin:', 'smilegrin.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':smirk:', 'smirk.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':sniffle:', 'sniffle.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':spin:', 'spin.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':starhit:', 'starhit.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':td:', 'thumbdown.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':tu:', 'thumbup.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ':yes:', 'yes.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('smiley', ';)', 'wink.gif')");

    $db->query("DELETE FROM " . $tablepre . "smilies WHERE type = 'picon' and url in ('post.gif','info.gif','yes.gif','smile.gif','no.gif','exclamation.gif','question.gif','thumbdown.gif','thumbup.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('picon', '', 'post.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('picon', '', 'info.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('picon', '', 'yes.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('picon', '', 'smile.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('picon', '', 'no.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('picon', '', 'exclamation.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('picon', '', 'question.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('picon', '', 'thumbup.gif')");
    $db->query("INSERT INTO " . $tablepre . "smilies (type, code, url) VALUES ('picon', '', 'thumbdown.gif')");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function create_tables($db, $tablepre, $prgBar, $start, $incr = 0.05)
{
    global $debug_log;

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_addresses($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_adminlogs($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_attachments($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_banned($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_dateformats($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_faq($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_guestcount($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_favorites($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_lastposts($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_forums($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_members($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_modlogs($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_pluglinks($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_posts($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_ranks($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_restricted($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_robotcount($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_robots($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_settings($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_smilies($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_templates($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_themes($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_threads($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_pm($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_subscriptions($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_pm_attachments($db, $tablepre);

    setBar($prgBar, $start);
    schema_create_vote_tables($db, $tablepre);
    $start += $incr * 3;

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_whosonline($db, $tablepre);

    setBar($prgBar, $start);
    $start += $incr;
    schema_create_words($db, $tablepre);

    setBar($prgBar, $start);
    return 0;   // SUCCESS
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function insert_data($db, $tablepre, $prgbar, $start, $incr)
{
    setBar($prgbar, $start);
    schema_insert_restricted($db, $tablepre);

    $start += $incr;
    setBar($prgbar, $start);
    schema_insert_dateformats($db, $tablepre);

    $start += $incr;
    setBar($prgbar, $start);
    schema_insert_faq($db, $tablepre);

    $start += $incr;
    setBar($prgbar, $start);
    $db->query("INSERT INTO `" . $tablepre . "forums` (`type`, `name`, `status`, `displayorder`, `private`, `description`, `fup`, `postperm`) VALUES ('group','DEFAULT Category','on',1,'' ,'',0,''), ('forum','DEFAULT Forum','on',1,'1','You can change this text in Admin CP -> Forums -> More Options.',1,'1|1|1')");

    $start += $incr;
    setBar($prgbar, $start);
    schema_insert_ranks($db, $tablepre);

    $start += $incr;
    setBar($prgbar, $start);
    reset_settings($db, $tablepre);

    $start += $incr;
    setBar($prgbar, $start);
    schema_insert_smilies($db, $tablepre);

    $start += $incr;
    setBar($prgbar, $start);
    schema_insert_robots($db, $tablepre);

    $start += $incr;
    setBar($prgbar, $start);
    reset_templates($db, $tablepre);

    $start += $incr;
    setBar($prgbar, $start);
    schema_insert_problue($db, $tablepre);

    $start += $incr;
    setBar($prgbar, $start);
    createsa($db, $tablepre);

    return 0;   // SUCCESS
}

/**
 * Factory reset the settings back to out of the box defaults
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function reset_settings($db, $tablepre)
{
    $boardUrl = get_boardurl();
    $array = parse_url($boardUrl);
    $yourdomain = $array['host'];
    $db->query("DELETE FROM `" . $tablepre . "settings`");
    schema_insert_settings($db, $tablepre);

    $db->query("UPDATE " . $tablepre . "settings SET config_value = 'Your Forums' WHERE config_name = 'bbname'");
    $db->query("UPDATE " . $tablepre . "settings SET config_value = '$yourdomain' WHERE config_name = 'sitename'");
    $db->query("UPDATE " . $tablepre . "settings SET config_value = '$boardUrl' WHERE config_name = 'siteurl'");
    $db->query("UPDATE " . $tablepre . "settings SET config_value = '$boardUrl' WHERE config_name = 'boardurl'");
    $db->query("UPDATE " . $tablepre . "settings SET config_value = 'F jS, Y' WHERE config_name = 'dateformat'");
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function reset_templates($db, $tablepre)
{
    $db->query("DELETE FROM " . $tablepre . "templates");
    $stream = fopen(ROOT . 'admin/templates.gbb', 'r');
    $file = fread($stream, filesize(ROOT . 'admin/templates.gbb'));
    fclose($stream);
    $templates = explode("|#*GBB TEMPLATE FILE*#|", $file);
    foreach ($templates as $key => $val) {
        $template = explode("|#*GBB TEMPLATE*#|", $val);
        if (isset ($template[1])) {
            $template[1] = addslashes($template[1]);
        } else {
            $template[1] = '';
        }
        $db->query("INSERT INTO " . $tablepre . "templates (`name`, `template`) VALUES ('" . addslashes($template[0]) . "', '" . addslashes($template[1]) . "')");
    }
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
function check_db_api($database)
{
    $retval = true;

    switch ($database) {
        case 'mysql':
        case 'mysql5php5.class':
            if (!defined('SQL_NUM')) {
                $retval = false;
            }
            break;

        default:
            $retval = false;
            break;
    }
    return $retval;
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param    $varname    type, what it does
 * @return   type, what the return does
 */
// Check if the admin account is named after one of several administration users (sa, root, etc)
function is_priv_db_user($admin)
{
    $priv_accts = array("root", "sa");

    return in_array($admin, $priv_accts);
}
