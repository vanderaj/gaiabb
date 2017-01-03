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

loadtpl('topic_activity', 'topic_activity_dotfolders', 'topic_activity_multipage', 'topic_activity_none', 'topic_activity_threads');

$shadow = shadowfx();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

nav($lang['topicactivity']);
btitle($lang['topicactivity']);

eval('echo "' . template('header') . '";');

if ($CONFIG['topicactivity_status'] == 'off') {
    error($lang['fnasorry'], false);
}

smcwcache();

$t_extension = get_extension($lang['toppedprefix']);
switch ($t_extension) {
    case 'gif':
    case 'jpg':
    case 'jpeg':
    case 'png':
        $lang['toppedprefix'] = '<img src="' . $THEME['imgdir'] . '/' . $lang['toppedprefix'] . '" alt="' . $lang['toppedprefix'] . '" title="' . $lang['toppedprefix'] . '" border="0px" />';
        break;
}

$p_extension = get_extension($lang['pollprefix']);
switch ($p_extension) {
    case 'gif':
    case 'jpg':
    case 'jpeg':
    case 'png':
        $lang['pollprefix'] = '<img src="' . $THEME['imgdir'] . '/' . $lang['pollprefix'] . '" alt="' . $lang['pollprefix'] . '" title="' . $lang['pollprefix'] . '" border="0px" />';
        break;
}

$days = getRequestInt('days');
if ($days < 1) {
    $days = 1;
}

$srchfrom = $onlinetime - (86400 * $days);

$type = getRequestVar('type');
$srchtype = '';
switch ($type) {
    case 'unanswered':
        $srchtype = "t.replies = 0 AND";
        break;
    case 'hot':
        $srchtype = "t.replies >= '$CONFIG[hottopic]' AND";
        break;
    case 'closed':
        $srchtype = "t.closed = 'yes' AND";
        break;
    case 'moved':
        $srchtype = "t.closed LIKE '%moved%' AND";
        break;
    case 'topped':
        $srchtype = "t.topped = 1 AND";
        break;
    case 'poll':
        $srchtype = "t.pollopts != '' AND";
        break;
    default:
        $srchtype = '';
        break;
}

$sort = getRequestVar('sort');
$srchsort = '';
switch ($sort) {
    case 'subject':
        $srchsort = "t.subject";
        break;
    case 'author':
        $srchsort = "t.author";
        break;
    case 'forum':
        $srchsort = "f.name";
        break;
    case 'replies':
        $srchsort = "t.replies";
        break;
    case 'views':
        $srchsort = "t.views";
        break;
    case 'lastpost':
        $srchsort = "l.dateline";
        break;
    default:
        $srchsort = "l.dateline";
        break;
}

$order = getRequestVar('order');
$srchorder = '';
switch ($order) {
    case 'asc':
        $srchorder = "ASC";
        break;
    default:
        $srchorder = "DESC";
        break;
}

$page = getRequestInt('page');
if ($page < 1) {
    $page = 1;
}
$start_limit = ($page - 1) * $self['tpp'];

$threadcount = 0;
$threads = '';

$dotadd1 = $dotadd2 = '';
if ($CONFIG['dotfolders'] == 'on' && X_MEMBER) {
    $dotadd1 = "DISTINCT p.author AS dotauthor, ";
    $dotadd2 = "LEFT JOIN " . X_PREFIX . "posts p ON (t.tid = p.tid AND p.author = '$self[username]')";
}

$threadcount = 0;
$threads = '';
$fidarray = array();
$query = $db->query("SELECT DISTINCT f.fid, f.password, f.private, f.userlist FROM (" . X_PREFIX . "threads t, " . X_PREFIX . "forums f) LEFT JOIN " . X_PREFIX . "lastposts l ON t.tid = l.tid WHERE l.dateline >= '$srchfrom' AND t.fid = f.fid");
while (($forums = $db->fetch_array($query)) != false) {
    $authorization = privfcheck($forums['private'], $forums['userlist']);
    if ($authorization == true || X_SADMIN) {
        $fidpw = isset($_COOKIE['fidpw' . $forums['fid']]) ? $_COOKIE['fidpw' . $forums['fid']] : '';
        if ((($forums['password'] == $fidpw) || $forums['password'] == '') || X_SADMIN) {
            array_push($fidarray, $forums['fid']);
        }
    }
}
$db->free_result($query);

$fidlist = "'" . implode("', '", $fidarray) . "'";
$query = $db->query("SELECT $dotadd1 t.*, m.uid as authorid, f.name, l.uid as lp_uid, l.username as lp_user, l.dateline as lp_dateline, l.pid as lp_pid FROM (" . X_PREFIX . "threads t, " . X_PREFIX . "forums f) $dotadd2 LEFT JOIN " . X_PREFIX . "members m ON t.author = m.username LEFT JOIN " . X_PREFIX . "lastposts l ON (t.tid = l.tid) WHERE $srchtype l.dateline >= '$srchfrom' AND t.fid = f.fid AND f.fid IN ($fidlist) ORDER BY $srchsort $srchorder LIMIT $start_limit, " . $self['tpp']);
while (($thread = $db->fetch_array($query)) != false) {
    $thread['subject'] = shortenString(censor($thread['subject']), 80, X_SHORTEN_SOFT | X_SHORTEN_HARD, '...');
    $tmOffset = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
    
    if ($thread['author'] != $lang['textanonymous']) {
        if (X_MEMBER) {
            $author = '<a href="viewprofile.php?memberid=' . intval($thread['authorid']) . '"><strong>' . trim($thread['author']) . '</strong></a>';
        } else {
            $author = '<strong>' . trim($thread['author']) . '</strong>';
        }
    } else {
        $author = $lang['textanonymous'];
    }
    
    $dalast = trim($thread['lp_dateline']);
    
    if ($thread['lp_user'] != $lang['textanonymous'] && $thread['lp_user'] != '') {
        
        if (X_MEMBER) {
            $lastpostauthor = '<a href="viewprofile.php?memberid=' . intval($thread['lp_uid']) . '"><strong>' . trim($thread['lp_user']) . '</strong></a>';
        } else {
            $lastpostauthor = '<strong>' . trim($thread['lp_user']) . '</strong>';
        }
    } else {
        $lastpostauthor = $lang['textanonymous'];
    }
    
    $lastPid = isset($thread['lp_pid']) ? $thread['lp_pid'] : 0;
    $lastpostdate = gmdate($self['dateformat'], $thread['lp_dateline'] + $tmOffset);
    $lastposttime = gmdate($self['timecode'], $thread['lp_dateline'] + $tmOffset);
    $lastpost = $lang['lastreply1'] . ' ' . $lastpostdate . ' ' . $lang['textat'] . ' ' . $lastposttime . '<br />' . $lang['textby'] . ' ' . $lastpostauthor;
    
    if (! empty($thread['icon']) && file_exists($THEME['smdir'] . '/' . $thread['icon'])) {
        $posticon = '<img src="' . $THEME['smdir'] . '/' . $thread['icon'] . '" alt="' . $thread['icon'] . '" title="' . $thread['icon'] . '" border="0px" />';
    } else {
        $posticon = '';
    }
    
    if ($thread['replies'] >= $CONFIG['hottopic']) {
        $folder = 'hot_folder.gif';
    } else 
        if ($thread['pollopts'] == 1) {
            $folder = 'folder_poll.gif';
        } else {
            $folder = 'folder.gif';
        }
    
    $oldtopics = isset($_COOKIE['oldtopics']) ? $_COOKIE['oldtopics'] : '';
    
    if (($oT = strpos($oldtopics, '|' . $lastPid . '|')) === false && $thread['replies'] >= $CONFIG['hottopic'] && $lastvisit < $dalast) {
        $folder = 'hot_red_folder.gif';
    } else 
        if ($lastvisit < $dalast && $oT === false && $thread['pollopts'] == 1) {
            $folder = 'folder_new_poll.gif';
        } else 
            if ($lastvisit < $dalast && $oT === false) {
                $folder = 'red_folder.gif';
            }
    
    if ($CONFIG['dotfolders'] == 'on' && isset($thread['dotauthor']) == $self['username'] && X_MEMBER) {
        $folder = 'dot_' . $folder;
    }
    
    $folder = '<img src="' . $THEME['imgdir'] . '/' . $folder . '" alt="' . $lang['altfolder'] . '" title="' . $lang['altfolder'] . '" border="0px" />';
    
    if ($thread['closed'] == 'yes') {
        $folder = '<img src="' . $THEME['imgdir'] . '/lock_folder.gif" alt="' . $lang['altclosedtopic'] . '" title="' . $lang['altclosedtopic'] . '" border="0px" />';
    }
    
    $prefix = '';
    $moved = explode('|', $thread['closed']);
    if ($moved[0] == 'moved') {
        $prefix = $lang['moved'] . ' ';
        $thread['realtid'] = intval($thread['tid']);
        $thread['tid'] = $moved[1];
        $thread['replies'] = '-';
        $thread['views'] = '-';
        $folder = '<img src="' . $THEME['imgdir'] . '/lock_folder.gif" alt="' . $lang['altclosedtopic'] . '" title="' . $lang['altclosedtopic'] . '" border="0px" />';
        $query = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE tid = '$thread[tid]'");
        $postnum = 0;
        if ($query !== false) {
            $postnum = $db->result($query, 0);
        }
    } else {
        $thread['realtid'] = intval($thread['tid']);
    }
    
    if ($thread['pollopts'] == 1) {
        $prefix = $lang['pollprefix'] . ' ';
    }
    
    if ($thread['topped'] == 1) {
        $prefix = $lang['toppedprefix'] . ' ';
    }
    
    $postnum = $thread['replies'] + 1;
    if ($postnum > $self['ppp']) {
        $pagelinks = multi($postnum, $self['ppp'], 0, 'viewtopic.php?tid=' . intval($thread['tid']));
        $pages = '(<span class="smalltxt">' . $pagelinks . '</span>)';
    } else {
        $pagelinks = $pages = '';
    }
    
    $mouseover = celloverfx('viewtopic.php?tid=' . intval($thread['tid'] . ''));
    eval('$threads .= "' . template('topic_activity_threads') . '";');
    $prefix = '';
    $threadcount ++;
}
$db->free_result($query);

if ($threadcount == 0) {
    eval('$threads = "' . template('topic_activity_none') . '";');
}

$type_unanswered = $type_hot = $type_closed = '';
$type_moved = $type_topped = $type_poll = $type_active = '';
switch ($type) {
    case 'unanswered':
        $type_unanswered = $selHTML;
        break;
    case 'hot':
        $type_hot = $selHTML;
        break;
    case 'closed':
        $type_closed = $selHTML;
        break;
    case 'moved':
        $type_moved = $selHTML;
        break;
    case 'topped':
        $type_topped = $selHTML;
        break;
    case 'poll':
        $type_poll = $selHTML;
        break;
    default:
        $type_active = $selHTML;
        break;
}

$sort_subject = $sort_author = $sort_forum = '';
$sort_replies = $sort_views = $sort_lastpost = '';
switch ($sort) {
    case 'subject':
        $sort_subject = $selHTML;
        break;
    case 'author':
        $sort_author = $selHTML;
        break;
    case 'forum':
        $sort_forum = $selHTML;
        break;
    case 'replies':
        $sort_replies = $selHTML;
        break;
    case 'views':
        $sort_views = $selHTML;
        break;
    case 'lastpost':
        $sort_lastpost = $selHTML;
        break;
    default:
        $sort_lastpost = $selHTML;
        break;
}

$order_asc = $order_desc = '';
switch ($order) {
    case 'asc':
        $order_asc = $selHTML;
        break;
    default:
        $order_desc = $selHTML;
        break;
}

$days1 = $days5 = $days10 = $days20 = '';
$days30 = $days60 = $days90 = $daysyear = '';
switch ($days) {
    case '1':
        $days1 = $selHTML;
        break;
    case '5':
        $days5 = $selHTML;
        break;
    case '10':
        $days10 = $selHTML;
        break;
    case '20':
        $days20 = $selHTML;
        break;
    case '30':
        $days30 = $selHTML;
        break;
    case '60':
        $days60 = $selHTML;
        break;
    case '90':
        $days90 = $selHTML;
        break;
    case '365':
        $daysyear = $selHTML;
        break;
    default:
        $days1 = $selHTML;
        break;
}

$dotlegend = '';
if ($CONFIG['dotfolders'] == 'on' && X_MEMBER) {
    eval('$dotlegend = "' . template('topic_activity_dotfolders') . '";');
}

$totalquery = $db->query("SELECT $dotadd1 t.*, f.password, f.private, f.userlist, f.name FROM " . X_PREFIX . "threads t $dotadd2, " . X_PREFIX . "forums f, " . X_PREFIX . "lastposts l WHERE l.tid = t.tid AND $srchtype l.dateline >= '$srchfrom' AND t.fid = f.fid AND f.fid IN ($fidlist)");
$total = $db->num_rows($totalquery);
$db->free_result($totalquery);

$mpurl = 'activity.php?type=' . $type . '&amp;days=' . $days . '&amp;sort=' . $sort . '&amp;order=' . $order;
$multipage = '';
if (($multipage = multi($total, $self['tpp'], $page, $mpurl)) !== false) {
    eval('$multipage = "' . template('topic_activity_multipage') . '";');
}

eval('echo stripslashes("' . template('topic_activity') . '");');

loadtime();
eval('echo "' . template('footer') . '";');
