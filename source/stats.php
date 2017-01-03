<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2017 The GaiaBB Group
 * http://www.GaiaBB.com
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group 
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
 * http://forums.xmbforum2.com/
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





require_once ('header.php');

loadtpl('stats');

$shadow = shadowfx();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

nav($lang['altstats']);
btitle($lang['altstats']);

eval('echo "' . template('header') . '";');

if ($CONFIG['stats'] == 'off') {
    error($lang['fnasorry'], false);
}

smcwcache();

$restrict = 'WHERE';
switch ($self['status']) {
    case 'Member':
        $restrict .= " f.private != '3' AND";
    case 'Moderator':
    case 'Super Moderator':
        $restrict .= " f.private != '2' AND";
    case 'Administrator':
        $restrict .= " f.userlist = '' AND f.password = '' AND";
    case 'Super Administrator':
        break;
    default:
        $restrict .= " f.private != '5' AND f.private != '3' AND f.private != '2' AND f.userlist = '' AND f.password = '' AND";
        break;
}

$q = $db->query("SELECT COUNT(uid) FROM " . X_PREFIX . "members UNION ALL SELECT COUNT(tid) FROM " . X_PREFIX . "threads UNION ALL SELECT COUNT(pid) FROM " . X_PREFIX . "posts");
$members = $db->result($q, 0);
if ($members == false) {
    $members = 0;
}

$threads = $db->result($q, 1);
if ($threads == false) {
    $threads = 0;
}

$posts = $db->result($q, 2);
if ($posts == false) {
    $posts = 0;
}
$db->free_result($q);

$query = $db->query("SELECT regdate FROM " . X_PREFIX . "members ORDER BY regdate LIMIT 0, 1");
$days = ($onlinetime - @$db->result($query, 0)) / 86400;
if ($days > 0) {
    $membersday = number_format(($members / $days), 2);
} else {
    $membersday = number_format(0, 2);
}
$db->free_result($query);

$query = $db->query("SELECT COUNT(fid) FROM " . X_PREFIX . "forums WHERE type = 'forum'");
$forums = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(fid) FROM " . X_PREFIX . "forums WHERE type = 'forum' AND status = 'on'");
$forumsa = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(postnum) FROM " . X_PREFIX . "members WHERE postnum > '0'");
$membersact = $db->result($query, 0);
$db->free_result($query);

if ($posts == 0 || $members == 0 || $threads == 0 || $forums == 0 || $days < 1) {
    message($lang['stats_incomplete'], false);
}

$mempost = 0;
$query = $db->query("SELECT SUM(postnum) FROM " . X_PREFIX . "members");
$mempost = number_format(($db->result($query, 0) / $members), 2);
$db->free_result($query);

$forumpost = 0;
if ($forums > 0) {
    $query = $db->query("SELECT SUM(posts) FROM " . X_PREFIX . "forums");
    $forumpost = number_format(($db->result($query, 0) / $forums), 2);
    $db->free_result($query);
}

$threadreply = 0;
if ($threads > 0) {
    $query = $db->query("SELECT SUM(replies) FROM " . X_PREFIX . "threads");
    $threadreply = number_format(($db->result($query, 0) / $threads), 2);
    $db->free_result($query);
}

$mapercent = "0%";
if ($members > 0) {
    $mapercent = number_format(($membersact * 100 / $members), 2) . '%';
}

$viewmost = array();
$query = $db->query("SELECT t.views, t.tid, t.subject FROM " . X_PREFIX . "threads t, " . X_PREFIX . "forums f $restrict f.fid = t.fid AND f.status = 'on' ORDER BY views DESC LIMIT 0,5");
while (($views = $db->fetch_array($query)) != false) {
    $views['subject'] = shortenString(censor($views['subject']), 80, X_SHORTEN_SOFT | X_SHORTEN_HARD, '...');
    $viewmost[] = '<a href="viewtopic.php?tid=' . intval($views['tid']) . '">' . $views['subject'] . '</a> (' . $views['views'] . ')<br />';
}
$viewmost = implode("\n", $viewmost);
$db->free_result($query);

$replymost = array();
$query = $db->query("SELECT t.replies, t.tid, t.subject FROM " . X_PREFIX . "threads t, " . X_PREFIX . "forums f $restrict f.fid = t.fid AND f.status = 'on' ORDER BY replies DESC LIMIT 0,5");
while (($reply = $db->fetch_array($query)) != false) {
    $reply['subject'] = shortenString(censor($reply['subject']), 80, X_SHORTEN_SOFT | X_SHORTEN_HARD, '...');
    $replymost[] = '<a href="viewtopic.php?tid=' . intval($reply['tid']) . '">' . $reply['subject'] . '</a> (' . $reply['replies'] . ')<br />';
}
$replymost = implode("\n", $replymost);
$db->free_result($query);

$latest = array();
$query = $db->query("SELECT l.dateline as lp_dateline, l.pid as lp_pid, t.tid, t.subject FROM " . X_PREFIX . "threads t, " . X_PREFIX . "forums f, " . X_PREFIX . "lastposts l $restrict l.tid = t.tid AND f.fid = t.fid AND f.status = 'on' ORDER BY l.dateline DESC LIMIT 0,5");
$adjTime = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
while (($last = $db->fetch_array($query)) != false) {
    $lpdate = gmdate($self['dateformat'], $last['lp_dateline'] + $adjTime);
    $lptime = gmdate($self['timecode'], $last['lp_dateline'] + $adjTime);
    $thislast = $lang['lpoststats'] . ' ' . $lang['lastreply1'] . ' ' . $lpdate . ' ' . $lang['textat'] . ' ' . $lptime;
    $last['subject'] = shortenString(censor($last['subject']), 80, X_SHORTEN_SOFT | X_SHORTEN_HARD, '...');
    $latest[] = '<a href="viewtopic.php?tid=' . intval($last['tid']) . '">' . $last['subject'] . '</a> (' . $thislast . ')<br/>';
}
$latest = implode("\n", $latest);
$db->free_result($query);

$query = $db->query("SELECT f.posts, f.threads, f.fid, f.name FROM " . X_PREFIX . "forums f $restrict f.fid = f.fid AND f.type = 'forum' OR f.type = 'sub' AND f.status = 'on' ORDER BY posts DESC LIMIT 0, 1");
$pop = $db->fetch_array($query);
$popforum = '<a href="viewforum.php?fid=' . intval($pop['fid']) . '"><strong>' . $pop['name'] . '</strong></a>';
$db->free_result($query);

$postsday = 0;
if ($days > 0) {
    $postsday = number_format($posts / $days, 2);
}

$timesearch = $onlinetime - 86400;
$eval = $lang['evalnobestmember'];

$query = $db->query("SELECT uid, username, regdate, postnum, COUNT(postnum) AS Total FROM " . X_PREFIX . "members WHERE regdate <='$timesearch' GROUP BY postnum ORDER BY postnum DESC LIMIT 1");
$info = $db->fetch_array($query);
$db->free_result($query);

if ($info['Total'] > 0) {
    
    if (X_MEMBER) {
        $membesthtml = '<a href="viewprofile.php?memberid=' . intval($info['uid']) . '"><strong>' . trim($info['username']) . '</strong></a>';
    } else {
        $membesthtml = '<strong>' . trim($info['username']) . '</strong>';
    }
    $bestmemberpost = $info['postnum'];
    $eval = $lang['evalbestmember'];
}

eval($eval);
eval($lang['evalstats1']);
eval($lang['evalstats2']);
eval($lang['evalstats3']);
eval($lang['evalstats4']);
eval($lang['evalstats5']);
eval($lang['evalstats6']);
eval($lang['evalstats7']);
eval($lang['evalstats8']);
eval($lang['evalstats9']);
eval($lang['evalstats10']);
eval($lang['evalstats11']);
eval($lang['evalstats12']);
eval($lang['evalstats13']);
eval($lang['evalstats14']);
eval($lang['evalstats15']);

eval('echo stripslashes("' . template('stats') . '");');

loadtime();
eval('echo "' . template('footer') . '";');

