<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2019 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
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


require_once('header.php');

loadtpl('massmod_openclose', 'massmod_topuntop', 'massmod_bump', 'massmod_copy', 'massmod_merge', 'massmod_delete', 'massmod_empty', 'massmod_move', 'massmod_markthread', 'viewforum_newtopic', 'viewforum_newpoll', 'viewforum_password', 'viewforum_thread', 'viewforum_invalidforum', 'viewforum_nothreads', 'viewforum', 'viewforum_subforum_lastpost', 'viewforum_thread_lastpost', 'viewforum_admin', 'viewforum_thread_admin', 'viewforum_subforum', 'viewforum_subforums', 'viewforum_multipage_admin', 'viewforum_multipage', 'viewforum_subforum_nolastpost', 'viewforum_dotfolders', 'viewforum_search', 'viewforum_rules', 'viewforum_mpn_info', 'viewforum_nothreads_admin');

$shadow = shadowfx();
$meta = metaTags();
smcwcache();

eval('$css = "' . template('css') . '";');

if ($fid === 0) {
    error($lang['textnoforum']);
}

$query = $db->query("SELECT * FROM " . X_PREFIX . "forums WHERE fid = '$fid' AND status = 'on'");
$rows = $db->num_rows($query);
$forum = $db->fetch_array($query);
$db->free_result($query);

if ($rows === 0 || isset($forum['type']) && $forum['type'] != 'forum' && $forum['type'] != 'sub') {
    error($lang['textnoforum']);
}

$fup = array();
if (isset($forum['type']) && $forum['type'] == 'sub') {
    $forum['fup'] = intval($forum['fup']);
    $query = $db->query("SELECT private, userlist, name, fid FROM " . X_PREFIX . "forums WHERE fid = '$forum[fup]'");
    $fup = $db->fetch_array($query);
    $db->free_result($query);
    if (!privfcheck($fup['private'], $fup['userlist'])) {
        error($lang['privforummsg']);
    }
} else
    if (isset($forum['type']) && $forum['type'] != 'forum') {
        error($lang['textnoforum']);
    }

$authorization = privfcheck($forum['private'], $forum['userlist']);
if (!$authorization) {
    error($lang['privforummsg']);
}

pwverify($forum['password'], 'viewforum.php?fid=' . $fid, $fid, true);

if (isset($forum['type']) && $forum['type'] == 'forum') {
    nav(stripslashes($forum['name']));
    btitle(stripslashes($forum['name']));
} else
    if (isset($forum['type']) && $forum['type'] == 'sub') {
        nav('<a href="viewforum.php?fid=' . intval($fup['fid']) . '">' . stripslashes($fup['name']) . '</a>');
        nav(stripslashes($forum['name']));
        btitle(stripslashes($fup['name']));
        btitle(stripslashes($forum['name']));
    }

// check if member has enough posts to access forum if set
if (isset($forum['mpfa']) && $forum['mpfa'] != 0 && isset($self['postnum']) && $self['postnum'] < $forum['mpfa'] && !X_SADMIN) {
    $message = str_replace("*posts*", $forum['mpfa'], $lang['mpfae']);
    error($message);
}

eval('echo "' . template('header') . '";');

// display forum rules if set
$forumrules = '';
if (isset($forum['frules_status']) && $forum['frules_status'] == 'on' && !empty($forum['frules'])) {
    $forum['frules'] = postify($forum['frules']);
    $forum['frules'] = stripslashes($forum['frules']);
    eval('$forumrules = "' . template('viewforum_rules') . '";');
}

$subforums = $forumlist = '';
if (count($fup) == 0) {
    $query = $db->query("SELECT f.*, l.uid AS lp_uid, l.username AS lp_user, l.dateline AS lp_dateline, l.pid AS lp_pid FROM " . X_PREFIX . "forums f LEFT  JOIN " . X_PREFIX . "lastposts l ON l.tid = f.lastpost WHERE f.type =  'sub' AND f.fup =  '$fid' AND f.status =  'on' ORDER  BY f.displayorder");

    if ($db->num_rows($query) != 0) {
        $fulist = $forum['userlist'];
        while (($sub = $db->fetch_array($query)) != false) {
            $forumlist .= forum($sub, 'viewforum_subforum');
        }
        $forum['userlist'] = $fulist;
        eval('$subforums = "' . template('viewforum_subforums') . '";');
        $db->free_result($query);
    }
}

$newtopiclink = $newpolllink = '';
if (!postperm($forum, 'thread')) {
    $newtopiclink = $newpolllink = '';
} else {
    if (X_GUEST && isset($forum['guestposting']) && $forum['guestposting'] != 'on') {
        $newtopiclink = $newpolllink = '';
    } else {
        eval('$newtopiclink = "' . template('viewforum_newtopic') . '";');
        if (isset($forum['pollstatus']) && $forum['pollstatus'] != 'off') {
            eval('$newpolllink = "' . template('viewforum_newpoll') . '";');
        }
    }
}

// check if member has enough posts to access forum if set
$minimal_posts_needed = '';
if (isset($forum['mpnp']) && $forum['mpnp'] != 0 || isset($forum['mpnt']) && $forum['mpnt'] != 0 || isset($forum['mpfa']) && $forum['mpfa'] != 0) {
    $Ffie = $showfi = '';
    if (isset($forum['mpfa']) && $forum['mpfa'] != 0) {
        $lang['mpfai'] = str_replace("*posts*", $forum['mpfa'], $lang['mpfai']);
        $showfi .= $lang['mpfai'];
        $Ffie = '<br />';
    }

    if (isset($forum['mpnp']) && $forum['mpnp'] != 0) {
        $lang['mpnpi'] = str_replace("*posts*", $forum['mpnp'], $lang['mpnpi']);
        $showfi .= $Ffie;
        $showfi .= $lang['mpnpi'];
        $Ffie = '<br />';

        if (isset($forum['mpnp']) && isset($self['postnum']) && $self['postnum'] < $forum['mpnp'] && !X_SADMIN) {
            $replylink = '';
        }
    }

    if (isset($forum['mpnt']) && $forum['mpnt'] != 0) {
        $lang['mpnti'] = str_replace("*posts*", $forum['mpnt'], $lang['mpnti']);
        $showfi .= $Ffie;
        $showfi .= $lang['mpnti'];
        if (isset($forum['mpnt']) && isset($self['postnum']) && $self['postnum'] < $forum['mpnt'] && !X_SADMIN) {
            $newpolllink = $newtopiclink = '';
        }
    }
    eval('$minimal_posts_needed = "' . template('viewforum_mpn_info') . '";');
}

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

$type = strtolower(formVar('type'));
$sort = strtolower(formVar('sort'));
$order = strtolower(formVar('order'));
$days = formInt('days');

if ($days < 1) {
    $days = 1;
}

$srchfrom = '';
if (onSubmit('activitysubmit')) {
    $srchfrom = $onlinetime - (86400 * $days);
    $srchfrom = "AND l.dateline >= '$srchfrom'";
}

$srchtype = '';
switch ($type) {
    case 'unanswered':
        $srchtype = "AND replies = '0'";
        break;
    case 'hot':
        $srchtype = "AND replies >= '$CONFIG[hottopic]'";
        break;
    case 'closed':
        $srchtype = "AND closed = 'yes'";
        break;
    case 'moved':
        $srchtype = "AND closed LIKE '%moved%'";
        break;
    case 'topped':
        $srchtype = "AND topped = '1'";
        break;
    case 'poll':
        $srchtype = "AND pollopts != ''";
        break;
    default:
        $srchtype = '';
        break;
}

$srchsort = '';
switch ($sort) {
    case 'subject':
        $srchsort = "t.subject";
        break;
    case 'author':
        $srchsort = "t.author";
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

$srchorder = '';
switch ($order) {
    case 'asc':
        $srchorder = "ASC";
        break;
    default:
        $srchorder = "DESC";
        break;
}

$page = getInt('page');
$page = (isset($page) && is_numeric($page)) ? ($page < 1 ? 1 : ((int)$page)) : 1;
$start_limit = ($page > 1) ? (($page - 1) * $self['tpp']) : 0;

$dotadd1 = $dotadd2 = '';
if ($CONFIG['dotfolders'] == 'on' && X_MEMBER) {
    $dotadd1 = "DISTINCT p.author AS dotauthor, ";
    $dotadd2 = "LEFT JOIN " . X_PREFIX . "posts p ON (t.tid = p.tid AND p.author = '" . $self['username'] . "')";
}

$viewforum_thread = 'viewforum_thread';

$status1 = '';
if (X_STAFF && isset($self['status']) && $self['status'] != 'Moderator') {
    $status1 = 'Moderator';
} else
    if (isset($self['status']) && $self['status'] == 'Moderator') {
        $status1 = modcheck($forum['moderator']);
    }

if ($status1 == 'Moderator') {
    $viewforum_thread = 'viewforum_thread_admin';
    include_once('mass_mod.inc.php');
}

$topicsnum = 0;
$threadlist = '';
$querytop = $db->query("SELECT $dotadd1 t.*, m.uid, l.uid as lp_uid, l.username as lp_user, l.dateline as lp_dateline, l.pid as lp_pid FROM " . X_PREFIX . "threads t $dotadd2 LEFT JOIN " . X_PREFIX . "members m ON (m.username = t.author) LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid WHERE t.fid = '$fid' $srchfrom $srchtype ORDER BY topped $srchorder, $srchsort $srchorder LIMIT $start_limit, " . $self['tpp']);
while (($thread = $db->fetch_array($querytop)) != false) {
    $thread['subject'] = shortenString(censor($thread['subject']), 80, X_SHORTEN_SOFT | X_SHORTEN_HARD, '...');
    $tmOffset = ($self['timeoffset'] * 3600) + $self['daylightsavings'];

    if (!empty($thread['icon']) && file_exists($THEME['smdir'] . '/' . $thread['icon'])) {
        $thread['icon'] = '<img src="' . $THEME['smdir'] . '/' . $thread['icon'] . '" alt="' . $thread['icon'] . '" title="' . $thread['icon'] . '" border="0px" />';
    } else {
        $thread['icon'] = '';
    }

    if ($thread['author'] != $lang['textanonymous']) {
        if (X_MEMBER) {
            $authorlink = '<a href="viewprofile.php?memberid=' . intval($thread['uid']) . '"><strong>' . trim($thread['author']) . '</strong></a>';
        } else {
            $authorlink = '<strong>' . trim($thread['author']) . '</strong>';
        }
    } else {
        $authorlink = $lang['textanonymous'];
    }

    $dalast = trim($thread['lp_dateline']);

    if ($thread['lp_user'] != $lang['textanonymous'] && $thread['lp_user'] != '') {
        if (X_MEMBER) {
            $thread['lp_user'] = '<a href="viewprofile.php?memberid=' . intval($thread['lp_uid']) . '"><strong>' . trim($thread['lp_user']) . '</strong></a>';
        } else {
            $thread['lp_user'] = '<strong>' . trim($thread['lp_user']) . '</strong>';
        }
    } else {
        $thread['lp_user'] = $lang['textanonymous'];
    }

    $lastPid = isset($thread['lp_pid']) ? $thread['lp_pid'] : 0;
    $lastreplydate = gmdate($self['dateformat'], $thread['lp_dateline'] + $tmOffset);
    $lastreplytime = gmdate($self['timecode'], $thread['lp_dateline'] + $tmOffset);
    $lastpost = $lang['lastreply1'] . ' ' . $lastreplydate . ' ' . $lang['textat'] . ' ' . $lastreplytime . '<br />' . $lang['textby'] . ' ' . $thread['lp_user'];

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

    eval('$lastpostrow = "' . template('viewforum_thread_lastpost') . '";');

    if ($thread['pollopts'] == 1) {
        $prefix = $lang['pollprefix'] . ' ';
    }

    if ($thread['topped'] == 1) {
        $prefix = $lang['toppedprefix'] . ' ';
    }

    $postnum = $thread['replies'] + 1;
    if ($postnum > $self['ppp']) {
        $pagelinks = multi($postnum, $self['ppp'], 0, 'viewtopic.php?tid=' . intval($thread['tid']));
        $multipage2 = '(<span class="smalltxt">' . $pagelinks . '</span>)';
    } else {
        $pagelinks = $multipage2 = '';
    }

    $mouseover = celloverfx('viewtopic.php?tid=' . intval($thread['tid']));
    eval('$threadlist .= "' . template($viewforum_thread) . '";');
    $prefix = '';
    $topicsnum++;
}
$db->free_result($querytop);

if ($topicsnum == 0) {
    if (X_ADMIN || X_SMOD || (X_STAFF && $status1 == 'Moderator')) {
        eval('$threadlist = "' . template('viewforum_nothreads_admin') . '";');
    } else {
        eval('$threadlist = "' . template('viewforum_nothreads') . '";');
    }
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

$totalquery = $db->query("SELECT $dotadd1 t.* FROM " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid $dotadd2 WHERE t.fid = '$fid' $srchfrom $srchtype");
$total = $db->num_rows($totalquery);
$db->free_result($totalquery);

$mpurl = 'viewforum.php?fid=' . $fid . '&amp;type=' . $type . '&amp;days=' . $days . '&amp;sort=' . $sort . '&amp;order=' . $order;
if (($multipage = multi($total, $self['tpp'], $page, $mpurl)) === false) {
    $multipage = '';
} else {
    if (X_ADMIN || X_SMOD || (X_STAFF && $status1 == 'Moderator')) {
        eval('$multipage = "' . template('viewforum_multipage_admin') . '";');
    } else {
        eval('$multipage = "' . template('viewforum_multipage') . '";');
    }
}

$dotlegend = '';
if ($CONFIG['dotfolders'] == 'on' && X_MEMBER) {
    eval('$dotlegend = "' . template('viewforum_dotfolders') . '";');
}

$fsearch = $mt_option = '';
if (X_ADMIN || X_SMOD || (X_STAFF && $status1 == 'Moderator')) {
    eval('$fsearch = "' . template('viewforum_search') . '";');
    $mt_option = '';
    if (isset($forum['mt_status']) && $forum['mt_status'] == 'on') {
        $mt_option = '<option value="markthread">' . $lang['massmod_markthread'] . '</option>';
    }
    eval('echo stripslashes("' . template('viewforum_admin') . '");');
} else {
    $fsearch = '';
    if (X_MEMBER) {
        eval('$fsearch = "' . template('viewforum_search') . '";');
    }
    eval('echo stripslashes("' . template('viewforum') . '");');
}

loadtime();
eval('echo "' . template('footer') . '";');

