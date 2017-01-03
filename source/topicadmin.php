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
require_once ('topicadmin.inc.php');

$kill = false;

loadtpl('topicadmin_delete', 'topicadmin_openclose', 'topicadmin_move', 'topicadmin_topuntop', 'topicadmin_bump', 'topicadmin_empty', 'topicadmin_split_row', 'topicadmin_split', 'topicadmin_merge', 'topicadmin_threadprune_row', 'topicadmin_threadprune', 'topicadmin_copy', 'topicadmin_report', 'topicadmin_markthread');

$shadow = shadowfx();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

smcwcache();

$thread = array();
if ($tid > 0) {
    $query = $db->query("SELECT * FROM " . X_PREFIX . "threads WHERE tid = '$tid'");
    $thread = $db->fetch_array($query);
    $thread['subject'] = stripslashes($thread['subject']);
    $thread['subject'] = shortenString(censor($thread['subject']), 100, X_SHORTEN_SOFT | X_SHORTEN_HARD, '...');
    $fid = $thread['fid'];
    $db->free_result($query);
}

$query = $db->query("SELECT * FROM " . X_PREFIX . "forums WHERE fid = '$fid'");
$forums = $db->fetch_array($query);
$db->free_result($query);

$query = $db->query("SELECT name, fid FROM " . X_PREFIX . "forums WHERE fid = '$forums[fup]'");
$fup = $db->fetch_array($query);
$db->free_result($query);

if (! empty($forums['type']) && isset($forums['type']) && $forums['type'] == 'forum') {
    nav('<a href="viewforum.php?fid=' . $fid . '">' . stripslashes($forums['name']) . '</a>');
    nav('<a href="viewtopic.php?tid=' . $tid . '">' . $thread['subject'] . '</a>');
    btitle(stripslashes($forums['name']));
    btitle(stripslashes($thread['subject']));
} else 
    if (isset($forums['type']) && isset($forums['type']) == 'sub') {
        nav('<a href="viewforum.php?fid=' . $fup['fid'] . '">' . stripslashes($fup['name']) . '</a>');
        nav('<a href="viewforum.php?fid=' . $fid . '">' . stripslashes($forums['name']) . '</a>');
        nav('<a href="viewtopic.php?tid=' . $tid . '">' . $thread['subject'] . '</a>');
        btitle(stripslashes($fup['name']));
        btitle(stripslashes($forums['name']));
        btitle(stripslashes($thread['subject']));
    } else {
        $kill = true;
    }

$config_cache->expire('settings');
$config_cache->expire('theme');
$config_cache->expire('pluglinks');
$config_cache->expire('whosonline');
$config_cache->expire('forumjump');

switch ($action) {
    case 'delete':
        nav($lang['textdeletethread']);
        btitle($lang['textdeletethread']);
        break;
    case 'top':
        nav($lang['texttopthread']);
        btitle($lang['texttopthread']);
        break;
    case 'close':
        nav($lang['textclosethread']);
        btitle($lang['textclosethread']);
        break;
    case 'copy':
        nav($lang['copythread']);
        btitle($lang['copythread']);
        break;
    case 'move':
        nav($lang['textmovemethod1']);
        btitle($lang['textmovemethod1']);
        break;
    case 'getip':
        nav($lang['textgetip']);
        btitle($lang['textgetip']);
        break;
    case 'bump':
        nav($lang['textbumpthread']);
        btitle($lang['textbumpthread']);
        break;
    case 'report':
        nav($lang['textreportpost']);
        btitle($lang['textreportpost']);
        break;
    case 'split':
        nav($lang['textsplitthread']);
        btitle($lang['textsplitthread']);
        break;
    case 'merge':
        nav($lang['textmergethread']);
        btitle($lang['textmergethread']);
        break;
    case 'threadprune':
        nav($lang['textprunethread']);
        btitle($lang['textprunethread']);
        break;
    case 'empty':
        nav($lang['textemptythread']);
        btitle($lang['textemptythread']);
        break;
    case 'votepoll':
        nav($lang['textvote']);
        btitle($lang['textvote']);
        break;
    case 'markthread':
        nav($lang['markthread']);
        btitle($lang['markthread']);
        if (isset($forums['mt_status']) && $forums['mt_status'] == 'off') {
            $kill = true;
        }
        break;
    default:
        nav($lang['error']);
        btitle($lang['error']);
        $kill = true;
        break;
}

if ($kill) {
    error($lang['notpermitted']);
}

eval('echo "' . template('header') . '";');

$mod = new mod();

if ($action != 'report' && $action != 'votepoll') {
    $mod->statuscheck($fid);
}

switch ($action) {
    case 'report':
        
        // This get's unset by other code, so I've redefined it. ~martijn
        $pid = getRequestInt('pid');
        
        if ($CONFIG['reportpost'] == 'off') {
            error($lang['fnasorry'], false);
        }
        
        if (onSubmit('reportsubmit')) {
            $mod->doReport();
        }
        if (noSubmit('reportsubmit')) {
            $mod->viewReport();
        }
        break;
    case 'votepoll':
        $mod->doVote();
        break;
    case 'getip':
        $mod->viewIP();
        break;
    case 'markthread':
        if (onSubmit('markthreadsubmit')) {
            $mod->doMarkThread();
        }
        if (noSubmit('markthreadsubmit')) {
            $mod->viewMarkThread();
        }
        break;
    case 'delete':
        if (onSubmit('deletesubmit')) {
            $mod->doDelete();
        }
        if (noSubmit('deletesubmit')) {
            $mod->viewDelete();
        }
        break;
    case 'close':
        $query = $db->query("SELECT closed FROM " . X_PREFIX . "threads WHERE fid = '$fid' AND tid = '$tid'");
        if ($db->num_rows($query) == 0) {
            $db->free_result($query);
            error($lang['textnothread'], false);
        }
        $closed = $db->result($query, 0);
        $db->free_result($query);
        
        if (onSubmit('closesubmit')) {
            $mod->doClose($closed);
        }
        if (noSubmit('closesubmit')) {
            $mod->viewClose($closed);
        }
        break;
    case 'move':
        if (onSubmit('movesubmit')) {
            $mod->doMove();
        }
        if (noSubmit('movesubmit')) {
            $mod->viewMove();
        }
        break;
    case 'top':
        $query = $db->query("SELECT topped FROM " . X_PREFIX . "threads WHERE fid = '$fid' AND tid = '$tid'");
        if ($db->num_rows($query) == 0) {
            error($lang['textnothread'], false);
        }
        $topped = $db->result($query, 0);
        $db->free_result($query);
        
        if (onSubmit('topsubmit')) {
            $mod->doTop($topped);
        }
        if (noSubmit('topsubmit')) {
            $mod->viewTop($topped);
        }
        break;
    case 'bump':
        if (onSubmit('bumpsubmit')) {
            $mod->doBump();
        }
        if (noSubmit('bumpsubmit')) {
            $mod->viewBump();
        }
        break;
    case 'empty':
        if (onSubmit('emptysubmit')) {
            $mod->doEmpty();
        }
        if (noSubmit('emptysubmit')) {
            $mod->viewEmpty();
        }
        break;
    case 'split':
        if (onSubmit('splitsubmit')) {
            $mod->doSplit();
        }
        if (noSubmit('splitsubmit')) {
            $mod->viewSplit();
        }
        break;
    case 'merge':
        if (onSubmit('mergesubmit')) {
            $mod->doMerge();
        }
        if (noSubmit('mergesubmit')) {
            $mod->viewMerge();
        }
        break;
    case 'threadprune':
        if (onSubmit('threadprunesubmit')) {
            $mod->doPrune();
        }
        if (noSubmit('threadprunesubmit')) {
            $mod->viewPrune();
        }
        break;
    case 'copy':
        if (onSubmit('copysubmit')) {
            $mod->doCopy();
        }
        if (noSubmit('copysubmit')) {
            $mod->viewCopy();
        }
        break;
    default:
        error($lang['testnothingchos'], false);
        break;
}

loadtime();
eval('echo "' . template('footer') . '";');
