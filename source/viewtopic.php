<?php

/**
 * GaiaBB
 * Copyright (c) 2011-2025 The GaiaBB Group
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
 * http://www.xmbforum.com
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
 **/

if (isset($GET['action']) && $_GET['action'] == 'attachment') {
    define('CACHECONTROL', 'IMAGE');
}

define('ROOT', './');

require_once ROOT . 'header.php';
require_once ROOTCLASS . 'attachments.class.php';
require_once ROOTCLASS . 'thread.class.php';
require_once ROOTINC . 'theme.inc.php';

validatePpp();

$tid = getInt('tid');
$pid = getInt('pid');
$aid = getInt('aid');

$page = getInt('page');
$page = (isset($page) && is_numeric($page)) ? ($page < 1 ? 1 : ((int) $page)) : 1;
$start_limit = ($page > 1) ? (($page - 1) * $self['ppp']) : 0;

$toptopic = formVar('toptopic');

$goto = getVar('goto');
if ($goto == 'lastpost') {
    if ($tid > 0) {
        $query = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
        $posts = $db->result($query, 0);
        $db->free_result($query);

        if ($posts == 0) {
            eval('$css = "' . template('css') . '";');
            error($lang['textnothread']);
        }

        $query = $db->query("SELECT pid FROM " . X_PREFIX . "lastposts WHERE tid = '$tid' LIMIT 1");
        $pid = $db->result($query, 0);
        $db->free_result($query);
    } elseif ($fid > 0) {
        $query = $db->query("SELECT f.lastpost as tid, l.pid FROM " . X_PREFIX . "forums f LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = f.lastpost WHERE fid = '$fid' LIMIT 1");
        $lastpost = $db->fetch_array($query);
        $db->free_result($query);

        $pid = $lastpost['pid'];
        $tid = $lastpost['tid'];

        $query = $db->query("SELECT p.pid, p.tid FROM " . X_PREFIX . "posts p, " . X_PREFIX . "forums f WHERE p.fid = f.fid and (f.fup = '$fid') ORDER BY p.pid DESC LIMIT 0,1");
        $fupPosts = $db->fetch_array($query);
        $db->free_result($query);

        if ($fupPosts['pid'] > $pid) {
            $pid = $fupPosts['pid'];
            $tid = $fupPosts['tid'];
        }

        $query = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
        $posts = $db->result($query, 0);
        $db->free_result($query);
    }

    if (isset($self['psorting']) && $self['psorting'] == 'DESC') {
        $page = 1;
    } else {
        $page = quickpage($posts, $self['ppp']);
    }
    redirect("viewtopic.php?tid=$tid&page=$page#pid$pid", 0);
}

loadtpl(
    'functions_bbcode',
    'functions_bbcodeinsert',
    'functions_smilieinsert_smilie',
    'functions_smilieinsert',
    'viewtopic_reply',
    'viewtopic_quickreply',
    'viewtopic',
    'viewtopic_invalid',
    'viewtopic_modoptions',
    'viewtopic_newpoll',
    'viewtopic_newtopic',
    'viewtopic_poll_options_view',
    'viewtopic_poll_options',
    'viewtopic_poll_submitbutton',
    'viewtopic_poll',
    'viewtopic_post',
    'viewtopic_post_email',
    'viewtopic_post_site',
    'viewtopic_post_icq',
    'viewtopic_post_aim',
    'viewtopic_post_msn',
    'viewtopic_post_yahoo',
    'viewtopic_post_search',
    'viewtopic_post_profile',
    'viewtopic_post_pm',
    'viewtopic_post_ip',
    'viewtopic_post_repquote',
    'viewtopic_post_report',
    'viewtopic_post_edit',
    'viewtopic_post_delete',
    'viewtopic_post_attachmentimage',
    'viewtopic_post_attachment',
    'viewtopic_post_attach_noborder',
    'viewtopic_post_attimg_noborder',
    'viewtopic_post_sig',
    'viewtopic_post_nosig',
    'viewtopic_printable',
    'viewtopic_printable_row',
    'viewtopic_multipage',
    'viewtopic_post_attachment_none',
    'viewforum_rules',
    'viewtopic_post_blog',
    'viewtopic_post_rpg',
    'viewforum_mpn_info',
    'viewtopic_next_prev_links',
    'viewtopic_captcha'
);

$shadow = shadowfx();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

$notexist = false;
$notexist_txt = $posts = $captcha = '';
$query = $db->query("SELECT t.tid, t.fid, t.subject, t.views, t.replies, t.closed, t.topped, l.uid as lp_uid, l.username as lp_user, l.dateline as lp_dateline, l.pid as lp_pid FROM " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid WHERE t.tid = '$tid'");
if ($query === false || $db->num_rows($query) != 1) {
    $db->free_result($query);
    error($lang['textnothread']);
}

$thread = $db->fetch_array($query);
$db->free_result($query);

if (strpos($thread['closed'], '|') !== false) {
    $moved = explode('|', $thread['closed']);
    if ($moved[0] == 'moved') {
        redirect('viewforum.php?tid=' . $moved[1], 0);
    }
}

$lastPid = isset($thread['lp_pid']) ? $thread['lp_pid'] : 0;
$oldtopics = isset($_COOKIE['oldtopics']) ? $_COOKIE['oldtopics'] : '';
if (!$oldtopics) {
    put_cookie('oldtopics', '|' . $lastPid . '|', $onlinetime + 600, $cookiepath, $cookiedomain, false, X_SET_HEADER);
} elseif (false === strpos($oldtopics, '|' . $lastPid . '|')) {
    $expire = $onlinetime + 600;
    $oldtopics .= $lastPid . '|';
    put_cookie('oldtopics', $oldtopics, $expire, $cookiepath, $cookiedomain, false, X_SET_HEADER);
}

$thread['subject'] = shortenString(censor($thread['subject']), 80, X_SHORTEN_SOFT | X_SHORTEN_HARD, '...');
$fid = intval($thread['fid']);

$query = $db->query("SELECT * FROM " . X_PREFIX . "forums WHERE fid='$fid' AND status='on'");
if ($query === false) {
    $db->free_result($query);
    error($lang['textnoforum']);
}
$forum = $db->fetch_array($query);

if ((isset($forum['type']) && $forum['type'] != 'forum' && $forum['type'] != 'sub') || $db->num_rows($query) != 1) {
    $db->free_result($query);
    error($lang['textnoforum']);
}
$db->free_result($query);

$authorization = true;
if (isset($forum['type']) && $forum['type'] == 'sub') {
    $query = $db->query("SELECT name, fid, private, userlist FROM " . X_PREFIX . "forums WHERE fid = '$forum[fup]'");
    $fup = $db->fetch_array($query);
    $db->free_result($query);
    $authorization = privfcheck($fup['private'], $fup['userlist']);
}

if (!$authorization || !privfcheck($forum['private'], $forum['userlist'])) {
    btitle();
    error($lang['privforummsg']);
}

pwverify($forum['password'], 'viewtopic.php?tid=' . $tid, $fid, true);

if (isset($forum['type']) && $forum['type'] == 'forum') {
    nav('<a href="viewforum.php?fid=' . $fid . '">' . stripslashes($forum['name']) . '</a>');
    nav(stripslashes($thread['subject']));
    btitle(stripslashes($forum['name']));
    btitle(stripslashes($thread['subject']));
} elseif (isset($forum['type']) && isset($forum['type']) == 'sub') {
    nav('<a href="viewforum.php?fid=' . $fup['fid'] . '">' . stripslashes($fup['name']) . '</a>');
    nav('<a href="viewforum.php?fid=' . $fid . '">' . stripslashes($forum['name']) . '</a>');
    nav(stripslashes($thread['subject']));
    btitle(stripslashes($fup['name']));
    btitle(stripslashes($forum['name']));
    btitle(stripslashes($thread['subject']));
}

$allowimgcode = (isset($forum['allowimgcode']) && $forum['allowimgcode'] == 'yes') ? $lang['texton'] : $lang['textoff'];
$allowsmilies = (isset($forum['allowsmilies']) && $forum['allowsmilies'] == 'yes') ? $lang['texton'] : $lang['textoff'];
$allowbbcode = (isset($forum['allowbbcode']) && $forum['allowbbcode'] == 'yes') ? $lang['texton'] : $lang['textoff'];

smcwcache();

if ($bbcode_js != '') {
    $bbcode_js_sc = 'bbcodefns-' . $bbcode_js . '.js';
} else {
    $bbcode_js_sc = 'bbcodefns.js';
}

eval('$bbcodescript = "' . template('functions_bbcode') . '";');

if ($CONFIG['smileyinsert'] == 'on' && $smiliesnum > 0) {
    $max = ($smiliesnum > 16) ? 16 : $smiliesnum;

    srand((double) microtime() * 1000000);
    $keys = array_rand($smiliecache, $max);

    $smilies = array();
    $smilies[] = '<table border="0px"><tr>';
    $i = 0;
    $total = 0;
    $pre = 'opener.';
    foreach ($keys as $key) {
        if ($total == 16) {
            break;
        }
        $smilie['code'] = $key;
        $smilie['url'] = $smiliecache[$key];

        if ($i >= 4) {
            $smilies[] = '</tr><tr>';
            $i = 0;
        }
        eval('$smilies[] = "' . template('functions_smilieinsert_smilie') . '";');
        $i++;
        $total++;
    }
    $smilies[] = '</tr></table>';
    $smilies = implode("\n", $smilies);
}

$usesig = false;
$replylink = $quickreply = $newpolllink = '';

$status1 = modcheck($forum['moderator']);

if (empty($action)) {
    if (X_MEMBER && !empty($self['sig'])) {
        $usesig = true;
    }

    if (X_STAFF && $status1 == 'Moderator') {
        $toptopic = formYesNo('toptopic');
        $topcheck = ($toptopic == 'yes') ? $cheHTML : '';
        $closetopic = formYesNo('closetopic');
        $closecheck = ($closetopic == 'yes' || isset($forum['closethreads']) && $forum['closethreads'] == 'on') ? $cheHTML : '';
    } else {
        $topcheck = $closecheck = '';
    }

    $topoption = $closeoption = '';
    if (X_STAFF && $status1 == 'Moderator') {
        $topoption = '<input type="checkbox" name="toptopic" value="yes" ' . $topcheck . ' />&nbsp;' . $lang['topmsgques'] . '<br />';
        $closeoption = '<input type="checkbox" name="closetopic" value="yes" ' . $closecheck . ' />&nbsp;' . $lang['closemsgques'] . '<br />';
    }

    eval('echo "' . template('header') . '";');

    if (X_MEMBER || X_GUEST) {
        if ($THEME['threadopts'] == 'image') {
            $nonmemthreadopts = '<a href="viewtopic.php?fid=' . $fid . '&amp;tid=' . $tid . '&amp;action=printable"><img src="' . $THEME['imgdir'] . '/print.gif" border="0px" alt="' . $lang['textprintver'] . '" title="' . $lang['textprintver'] . '" /></a>';
        } else {
            if ($THEME['threadopts'] == 'text') {
                $nonmemthreadopts = '<a href="viewtopic.php?fid=' . $fid . '&amp;tid=' . $tid . '&amp;action=printable">' . $lang['textprintver'] . '</a>';
            }
        }
    }

    $memthreadopts = '';
    if (X_MEMBER) {
        if ($THEME['threadopts'] == 'image') {
            $memthreadopts = ' <a href="usercp.php?action=subscriptions&amp;subadd=' . $tid . '"><img src="' . $THEME['imgdir'] . '/subscribe.gif" border="0px" alt="' . $lang['textsubscribe'] . '" title="' . $lang['textsubscribe'] . '" /></a> <a href="usercp.php?action=favorites&amp;favadd=' . $tid . '"><img src="' . $THEME['imgdir'] . '/favorites.gif" border="0px" alt="' . $lang['textaddfav'] . '" title="' . $lang['textaddfav'] . '" /></a>';
        } else {
            if ($THEME['threadopts'] == 'text') {
                $memthreadopts = ' | <a href="usercp.php?action=subscriptions&amp;subadd=' . $tid . '">' . $lang['textsubscribe'] . '</a> | <a href="usercp.php?action=favorites&amp;favadd=' . $tid . '">' . $lang['textaddfav'] . '</a>';
            }
        }
    }

    pwverify($forum['password'], 'viewtopic.php?tid=' . $tid, $fid);

    $ppthread = postperm($forum, 'thread');
    $ppreply = postperm($forum, 'reply');
    $ppedit = postperm($forum, 'edit');
    $ppdelete = postperm($forum, 'delete');

    $usesigcheck = $usesig ? $cheHTML : '';
    $codeoffcheck = formYesNo('bbcodeoff') == 'yes' ? $cheHTML : '';
    $smileoffcheck = formYesNo('smileyoff') == 'yes' ? $cheHTML : '';

    if (X_MEMBER) {
        $self['psorting'] = (isset($self['psorting']) && $self['psorting'] == 'ASC') ? 'ASC' : 'DESC';
    } else {
        $self['psorting'] = (isset($self['psorting']) && $self['psorting'] == 'DESC') ? 'DESC' : 'ASC';
    }

    if ($thread['closed'] == 'yes') {
        if (X_SADMIN) {
            eval('$replylink = "' . template('viewtopic_reply') . '";');
            if (isset($forum['quickreply']) && $forum['quickreply'] == 'on') {
                eval('$quickreply = "' . template('viewtopic_quickreply') . '";');
            }
        }
        $closeopen = $lang['textopenthread'];
    } else {
        if (X_MEMBER || X_GUEST && isset($forum['guestposting']) && $forum['guestposting'] == 'on') {
            $closeopen = $lang['textclosethread'];
            eval('$replylink = "' . template('viewtopic_reply') . '";');
            if (isset($forum['quickreply']) && $forum['quickreply'] == 'on') {
                if (X_GUEST) {
                    if ($CONFIG['captcha_status'] == 'on' && function_exists('ImageCreate')) {
                        eval('$captcha_js = "' . template('register_captchajs') . '";');
                        eval('$captcha = "' . template('viewtopic_captcha') . '";');
                    }
                }
                eval('$quickreply = "' . template('viewtopic_quickreply') . '";');
            }
        }
    }

    if (!$ppthread) {
        $newtopiclink = $newpolllink = '';
        if (!$ppreply || (X_GUEST && isset($forum['guestposting']) && $forum['guestposting'] != 'on')) {
            $replylink = $quickreply = '';
        }
    } else {
        if (X_GUEST && isset($forum['guestposting']) && $forum['guestposting'] != 'on') {
            $newtopiclink = $newpolllink = '';
        } else {
            eval('$newtopiclink = "' . template('viewtopic_newtopic') . '";');
            if (isset($forum['pollstatus']) && $forum['pollstatus'] != 'off') {
                eval('$newpolllink = "' . template('viewtopic_newpoll') . '";');
            }
        }

        if (!$ppreply || (X_GUEST && isset($forum['guestposting']) && $forum['guestposting'] != 'on')) {
            $replylink = $quickreply = '';
        }
    }

    $topuntop = ($thread['topped'] == 1) ? $lang['textuntopthread'] : $lang['texttopthread'];

    $specialrank = array();
    $rankposts = array();
    $qranks = $db->query("SELECT id, title, posts, stars, allowavatars, avatarrank FROM " . X_PREFIX . "ranks");
    while ($query = $db->fetch_array($qranks)) {
        $title = $query['title'];
        $rposts = $query['posts'];

        if ($title == 'Super Administrator' || $title == 'Administrator' || $title == 'Super Moderator' || $title == 'Moderator') {
            $specialrank[$title]['id'] = $query['id'];
            $specialrank[$title]['title'] = $query['title'];
            $specialrank[$title]['posts'] = $query['posts'];
            $specialrank[$title]['stars'] = $query['stars'];
            $specialrank[$title]['allowavatars'] = $query['allowavatars'];
            $specialrank[$title]['avatarrank'] = $query['avatarrank'];
        } else {
            $rankposts[$rposts]['id'] = $query['id'];
            $rankposts[$rposts]['title'] = $query['title'];
            $rankposts[$rposts]['posts'] = $query['posts'];
            $rankposts[$rposts]['stars'] = $query['stars'];
            $rankposts[$rposts]['allowavatars'] = $query['allowavatars'];
            $rankposts[$rposts]['avatarrank'] = $query['avatarrank'];
        }
    }
    $db->free_result($qranks);

    $db->query("UPDATE " . X_PREFIX . "threads SET views = views+1 WHERE tid = '$tid' LIMIT 1");
    $query = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
    $num = $db->result($query, 0);
    $db->free_result($query);

    $mpurl = 'viewtopic.php?tid=' . $tid;
    $multipage = '';
    if (($multipage = multi($num, $self['ppp'], $page, $mpurl)) !== false) {
        eval('$multipage = "' . template('viewtopic_multipage') . '";');
    }

    // Check if the Next-Thread-Link and Previous-Thread-Link should exist
    $next_prev_links = $npt_list = '';
    $threadController = new thread();
    if ($npt_list = $threadController->PrevNextThreads()) {
        if (isset($npt_list['previous']) && isset($npt_list['next'])) {
            $npt_list['previous'] = '<td class="navtd" align="' . $lang_align . '" width="50%"><font class="smalltxt"><strong>&laquo;&nbsp;<a href="' . $npt_list['previous'] . '">' . $lang['prevthread'] . '</a></strong></font></td>';
            $npt_list['next'] = '<td class="navtd" align="' . $lang_nalign . '" width="50%"><font class="smalltxt"><strong><a href="' . $npt_list['next'] . '">' . $lang['nextthread'] . '</a>&nbsp;&raquo;</strong></font></td>';
        }

        if (isset($npt_list['previous']) && !isset($npt_list['next'])) {
            $npt_list['previous'] = '<td class="navtd" align="' . $lang_align . '" width="100%"><font class="smalltxt"><strong>&laquo;&nbsp;<a href="' . $npt_list['previous'] . '">' . $lang['prevthread'] . '</a></strong></font></td>';
            $npt_list['next'] = '';
        }

        if (!isset($npt_list['previous']) && isset($npt_list['next'])) {
            $npt_list['previous'] = '';
            $npt_list['next'] = '<td class="navtd" align="' . $lang_nalign . '" width="100%"><font class="smalltxt"><strong><a href="' . $npt_list['next'] . '">' . $lang['nextthread'] . '</a>&nbsp;&raquo;</strong></font></td>';
        }
        eval('$next_prev_links = "' . template('viewtopic_next_prev_links') . '";');
    }

    $pollhtml = '';
    $poll = array();
    $vote_id = $voted = 0;

    $query = $db->query("SELECT vote_id FROM " . X_PREFIX . "vote_desc WHERE topic_id = '$tid'");
    if ($query) {
        $vote_id = $db->fetch_array($query);
        $vote_id = ($vote_id !== null ) ? (int) $vote_id['vote_id'] : 0;
    }
    $db->free_result($query);

    if ($vote_id > 0 && isset($forum['pollstatus']) && $forum['pollstatus'] != 'off') {
        if (X_MEMBER) {
            $query = $db->query("SELECT COUNT(vote_id) AS cVotes FROM " . X_PREFIX . "vote_voters WHERE vote_id = '$vote_id' AND vote_user_id = '$self[uid]'");
            if ($query) {
                $voted = $db->fetch_array($query);
                $voted = (int) $voted['cVotes'];
            }
        }

        $viewresults = (isset($_GET['viewresults']) && $_GET['viewresults'] == 'yes') ? 'yes' : '';
        if ($voted === 1 || $thread['closed'] == 'yes' || X_GUEST || $viewresults) {
            if ($viewresults) {
                $results = ' - [ <a href="viewtopic.php?tid=' . $tid . '"><font color="' . $THEME['cattext'] . '">' . $lang['backtovote'] . '</font></a> ]';
            } else {
                $results = '';
            }

            $num_votes = 0;
            $query = $db->query("SELECT vote_result, vote_option_text FROM " . X_PREFIX . "vote_results WHERE vote_id = '$vote_id'");
            while ($result = $db->fetch_array($query)) {
                $num_votes += $result['vote_result'];
                $pollentry = array();
                $pollentry['name'] = postify($result['vote_option_text']);
                $pollentry['votes'] = $result['vote_result'];
                $poll[] = $pollentry;
            }
            $db->free_result($query);

            reset($poll);
            foreach ($poll as $num => $array) {
                $pollimgnum = 0;
                $pollbar = '';
                if ($array['votes'] > 0) {
                    $orig = round($array['votes'] / $num_votes * 100, 2);
                    $percentage = round($orig, 2);
                    $percentage .= '%';
                    $poll_length = (int) $orig;
                    if ($poll_length > 97) {
                        $poll_length = 97;
                    }
                    $pollbar = '<img src="' . $THEME['imgdir'] . '/pollbar.gif" height="10" width="' . $poll_length . '%" alt="' . $lang['altpollpercentage'] . '" title="' . $lang['altpollpercentage'] . '" border="0px" />';
                } else {
                    $percentage = '0%';
                }
                eval('$pollhtml .= "' . template('viewtopic_poll_options_view') . '";');
                $buttoncode = '';
            }
        } else {
            $results = ' - [ <a href="viewtopic.php?tid=' . $tid . '&amp;viewresults=yes"><font color="' . $THEME['cattext'] . '">' . $lang['viewresults'] . '</font></a> ]';
            $query = $db->query("SELECT vote_option_id, vote_option_text FROM " . X_PREFIX . "vote_results WHERE vote_id = '$vote_id'");
            while ($result = $db->fetch_array($query)) {
                $poll['id'] = (int) $result['vote_option_id'];
                $poll['name'] = $result['vote_option_text'];
                eval('$pollhtml .= "' . template('viewtopic_poll_options') . '";');
            }
            $db->free_result($query);
            eval('$buttoncode = "' . template('viewtopic_poll_submitbutton') . '";');
        }
        eval('$poll = "' . template('viewtopic_poll') . '";');
    }

    if (empty($poll)) {
        $poll = '';
    }

    $attachments = new attachment();
    $attachments->get_attachments($tid);

    $thisbg = $THEME['altbg2'];
    $querypost = $db->query("SELECT p.*, m.*,w.time FROM " . X_PREFIX . "posts p LEFT JOIN " . X_PREFIX . "members m ON m.username = p.author LEFT JOIN " . X_PREFIX . "whosonline w ON p.author = w.username WHERE p.fid = '$fid' AND p.tid = '$tid' ORDER BY p.pid $self[psorting] LIMIT $start_limit, " . $self['ppp']);
    $tmoffset = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
    while ($post = $db->fetch_array($querypost)) {
        $onlinenow = $lang['memberisoff'];
        if (!empty($post['time']) && $post['author'] != 'xguest123') {
            switch ($post['invisible']) {
                case '1':
                    $onlinenow = X_ADMIN ? $lang['memberison'] . ' (' . $lang['hidden'] . ')' : $lang['memberisoff'];
                    break;
                default:
                    $onlinenow = $lang['memberison'];
                    break;
            }
        }

        $date = gmdate($self['dateformat'], $post['dateline'] + $tmoffset);
        $time = gmdate($self['timecode'], $post['dateline'] + $tmoffset);

        $poston = $lang['textposton'] . ' ' . $date . ' ' . $lang['textat'] . ' ' . $time;

        if (!empty($post['icon']) && file_exists($THEME['smdir'] . '/' . $post['icon'])) {
            $post['icon'] = '<img src="' . $THEME['smdir'] . '/' . $post['icon'] . '" alt="' . $post['icon'] . '" title="' . $post['icon'] . '" border="0px" />';
        } else {
            $post['icon'] = '';
        }

        if ($post['author'] != 'Anonymous') {
            if ($post['status'] == 'Banned') {
                $post['message'] = $lang['bannedpostmsg'];
            }

            $encodename = rawurlencode($post['author']);
            $tuid = intval($post['uid']);

            $rpg = '';
            if ($CONFIG['rpg_status'] == 'yes') {
                if (isset($self['expview']) && $self['expview'] == 'yes') {
                    $jointime = ($onlinetime - $post['regdate']) / 86400;
                    if ($jointime < 0.01) {
                        $jointime = 0.01; // stop NaN, whilst still not really worrying about stuff
                    }
                    $postsperday = sprintf('%.2f', ($post['postnum'] / $jointime));
                    $level = pow(log10($post['postnum']), 3);
                    $showlevel = floor($level + 1);
                    $ep = floor(100 * ($level - floor($level)));
                    $hpmulti = round($postsperday / 6, 1);

                    if ($hpmulti > 1.5) {
                        $hpmulti = 1.5;
                    }

                    if ($hpmulti < 1) {
                        $hpmulti = 1;
                    }

                    $maxhp = $level * 25 * $hpmulti;
                    $hp = $postsperday / 10;

                    if ($hp >= 1) {
                        $hp = $maxhp;
                    } else {
                        $hp = floor($hp * $maxhp);
                    }

                    $hp = floor($hp);
                    $maxhp = floor($maxhp);

                    if ($maxhp <= 0) {
                        $zhp = 1;
                    } else {
                        $zhp = $maxhp;
                    }

                    $hpf = floor(100 * ($hp / $zhp)) - 1;
                    $maxmp = ($jointime * $level) / 5;
                    $mp = $post['postnum'] / 3;

                    if ($mp >= $maxmp) {
                        $mp = $maxmp;
                    }

                    $maxmp = floor($maxmp);
                    $mp = floor($mp);

                    if ($maxmp <= 0) {
                        $zmp = 1;
                    } else {
                        $zmp = $maxmp;
                    }

                    $mpf = floor(100 * ($mp / $zmp)) - 1;
                    eval('$rpg = "' . template('viewtopic_post_rpg') . '";');
                }
            }

            $email = '';
            if (X_MEMBER && !empty($post['email']) && $post['showemail'] == 'yes') {
                eval('$email = "' . template('viewtopic_post_email') . '";');
            }

            $site = '';
            if (X_MEMBER && !empty($post['site'])) {
                $post['site'] = str_replace('http://', '', $post['site']);
                $post['site'] = "http://$post[site]";
                eval('$site = "' . template('viewtopic_post_site') . '";');
            }

            $blog = '';
            if (X_MEMBER && !empty($post['blog'])) {
                $post['blog'] = str_replace('http://', '', $post['blog']);
                $post['blog'] = "http://$post[blog]";
                eval('$blog = "' . template('viewtopic_post_blog') . '";');
            }

            $icq = '';
            if (X_MEMBER && !empty($post['icq'])) {
                eval('$icq = "' . template('viewtopic_post_icq') . '";');
            }

            $aim = '';
            if (X_MEMBER && !empty($post['aim'])) {
                eval('$aim = "' . template('viewtopic_post_aim') . '";');
            }

            $msn = '';
            if (X_MEMBER && !empty($post['msn'])) {
                eval('$msn = "' . template('viewtopic_post_msn') . '";');
            }

            $yahoo = '';
            if (X_MEMBER && !empty($post['yahoo'])) {
                eval('$yahoo = "' . template('viewtopic_post_yahoo') . '";');
            }

            $search = '';
            if (X_MEMBER) {
                eval('$search = "' . template('viewtopic_post_search') . '";');
            }

            $profile = '';
            if (X_MEMBER) {
                eval('$profile = "' . template('viewtopic_post_profile') . '";');
            }

            $pm = '';
            if (X_MEMBER && !($CONFIG['pmstatus'] == 'off' && isset($self['status']) && $self['status'] == 'Member')) {
                eval('$pm = "' . template('viewtopic_post_pm') . '";');
            }

            $showtitle = $post['status'];
            $rank = array();
            if ($post['status'] == 'Administrator' || $post['status'] == 'Super Administrator' || $post['status'] == 'Super Moderator' || $post['status'] == 'Moderator') {
                $sr = $post['status'];
                $rankinfo = $specialrank[$sr];
                $rank['allowavatars'] = $rankinfo['allowavatars'];
                $rank['title'] = $rankinfo['title'];
                $rank['stars'] = $rankinfo['stars'];
                $rank['avatarrank'] = $rankinfo['avatarrank'];
            } elseif ($post['status'] == 'Banned') {
                $rank['allowavatars'] = 'no';
                $rank['title'] = $lang['textbanned'];
                $rank['stars'] = 0;
                $rank['avatarrank'] = '';
            } else {
                $last_max = -1;
                foreach ($rankposts as $key => $rankstuff) {
                    if ($post['postnum'] >= $key && $key > $last_max) {
                        $last_max = $key;
                        $rankinfo = $rankstuff;
                        $rank['allowavatars'] = $rankinfo['allowavatars'];
                        $rank['title'] = $rankinfo['title'];
                        $rank['stars'] = $rankinfo['stars'];
                        $rank['avatarrank'] = $rankinfo['avatarrank'];
                    }
                }
            }

            $allowavatars = $rank['allowavatars'];

            switch ($post['status']) {
                case 'Moderator':
                    $star = 'star_mod.gif';
                    break;
                case 'Super Moderator':
                    $star = 'star_supmod.gif';
                    break;
                case 'Administrator':
                    $star = 'star_admin.gif';
                    break;
                case 'Super Administrator':
                    $star = 'star_supadmin.gif';
                    break;
                default:
                    $star = 'star.gif';
                    break;
            }
            $stars = str_repeat('<img src="' . $THEME['imgdir'] . '/' . $star . '" alt="*" title="*" border="0px" />', $rank['stars']) . '<br />';

            $icon = $pre = $suff = '';
            switch ($post['status']) {
                case 'Super Administrator':
                    if ($THEME['riconstatus'] == 'on') {
                        $icon = '<img src="' . $THEME['ricondir'] . '/online_supadmin.gif" alt="' . $lang['ranksupadmin'] . '" title="' . $lang['ranksupadmin'] . '" border="0px" />';
                        $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                        $suff = '</em></u></strong></span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    } else {
                        $icon = '';
                        $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                        $suff = '</em></u></strong></span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    }
                    break;
                case 'Administrator':
                    if ($THEME['riconstatus'] == 'on') {
                        $icon = '<img src="' . $THEME['ricondir'] . '/online_admin.gif" alt="' . $lang['rankadmin'] . '" title="' . $lang['rankadmin'] . '" border="0px" />';
                        $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                        $suff = '</u></strong></span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    } else {
                        $icon = '';
                        $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                        $suff = '</u></strong></span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    }
                    break;
                case 'Super Moderator':
                    if ($THEME['riconstatus'] == 'on') {
                        $icon = '<img src="' . $THEME['ricondir'] . '/online_supmod.gif" alt="' . $lang['ranksupmod'] . '" title="' . $lang['ranksupmod'] . '" border="0px" />';
                        $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                        $suff = '</strong></em></span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    } else {
                        $icon = '';
                        $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                        $suff = '</strong></em></span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    }
                    break;
                case 'Moderator':
                    if ($THEME['riconstatus'] == 'on') {
                        $icon = '<img src="' . $THEME['ricondir'] . '/online_mod.gif" alt="' . $lang['rankmod'] . '" title="' . $lang['rankmod'] . '" border="0px" />';
                        $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                        $suff = '</strong></span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    } else {
                        $icon = '';
                        $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                        $suff = '</strong></span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    }
                    break;
                default:
                    if ($THEME['riconstatus'] == 'on') {
                        $icon = '<img src="' . $THEME['ricondir'] . '/online_mem.gif" alt="' . $lang['rankmem'] . '" title="' . $lang['rankmem'] . '" border="0px" />';
                        $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                        $suff = '</span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    } else {
                        $icon = '';
                        $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                        $suff = '</span>';
                        $postauthor = $icon . '' . $pre . '' . $post['author'] . '' . $suff;
                    }
                    break;
            }

            $showtitle = (!empty($rank['title'])) ? $rank['title'] . '<br />' : $rank['title'] . '<br />';
            $customstatus = (!empty($post['customstatus'])) ? censor($post['customstatus']) . '<br />' : '';

            if ($allowavatars == 'no') {
                $post['avatar'] = '';
            }

            if (!empty($rank['avatarrank'])) {
                $rank['avatar'] = '<img src="' . $rank['avatarrank'] . '" alt="' . $lang['Rank_Avatar_Alt'] . '" title="' . $lang['Rank_Avatar_Alt'] . '" border="0px" /><br />';
            }

            $tharegdate = gmdate($self['dateformat'], $post['regdate'] + $tmoffset);

            $avatar = '';
            if (isset($self['viewavatars']) && $self['viewavatars'] == 'yes') {
                if ($CONFIG['avastatus'] == 'on') {
                    if (!empty($post['avatar']) && $allowavatars != 'no') {
                        $avatar = '<img src="' . $post['avatar'] . '" title="' . $lang['altavatar'] . '" alt="' . $lang['altavatar'] . '" border="0px" />';
                    } else {
                        $avatar = '<img src="./images/no_avatar.gif" alt="' . $lang['altnoavatar'] . '" title="' . $lang['altnoavatar'] . '" border="0px" />';
                    }
                }
            }

            $mood = '';
            if (!empty($post['mood'])) {
                $post['mood'] = censor($post['mood']);
                $mood = '<strong>' . $lang['mood'] . '</strong> ' . postify($post['mood'], 'no', 'no', 'yes', 'yes', false, 'yes', 'yes');
            }

            $aka = '';
            if ($post['showname'] == 'yes') {
                if (!empty($post['firstname']) || !empty($post['lastname'])) {
                    $post['firstname'] = censor($post['firstname']);
                    $post['lastname'] = censor($post['lastname']);
                    $aka = '<strong>' . $lang['aka'] . '</strong> ' . $post['firstname'] . ' ' . $post['lastname'] . '<br />';
                }
            }

            if (!empty($post['theme']) && $post['theme'] != 0 && isset(${'theme' . $post['theme']})) {
                $membertheme = '<br /><strong>' . $lang['texttheme'] . '</strong> ' . ${'theme' . $post['theme']};
            } else {
                $membertheme = '<br /><strong>' . $lang['texttheme'] . '</strong> ' . ${'theme' . $CONFIG['theme']} . $lang['defaulttheme'];
            }
        } else {
            $postauthor = $lang['textanonymous'];
            $showtitle = $lang['textunregistered'] . '<br />';
            $post['postnum'] = $tharegdate = 'N/A';
            $stars = $avatar = $email = $site = $icq = $msn = $aim = $yahoo = $profile = '';
            $search = $pm = $mood = $customstatus = $aka = $blog = $rpg = $location = $membertheme = '';
            $icon = $pre = $suff = $rank['avatar'] = '';
        }

        $ip = '';
        if ($status1 == 'Moderator') {
            eval('$ip = "' . template('viewtopic_post_ip') . '";');
        }

        $repquote = '';
        if (X_ADMIN || $status1 == 'Moderator' || ($thread['closed'] != 'yes')) {
            if (X_MEMBER || (X_GUEST && isset($forum['guestposting']) && $forum['guestposting'] == 'on')) {
                eval('$repquote = "' . template('viewtopic_post_repquote') . '";');
            }
        }

        $reportlink = '';
        if (X_MEMBER && $post['author'] != $self['username'] && $CONFIG['reportpost'] == 'on') {
            eval('$reportlink = "' . template('viewtopic_post_report') . '";');
        }

        if (!empty($post['subject'])) {
            $post['subject'] = censor($post['subject']) . '<br />';
            $post['subject'] = str_replace('&amp;', '&', $post['subject']);
        }

        $edit = '';
        if (X_ADMIN || $status1 == 'Moderator' || ($thread['closed'] != 'yes' && $post['author'] == $self['username'])) {
            if ($ppedit) {
                eval('$edit = "' . template('viewtopic_post_edit') . '";');
            }
        }

        $delete = '';
        if (X_ADMIN || $status1 == 'Moderator' || ($thread['closed'] != 'yes' && $post['author'] == $self['username'])) {
            if ($ppdelete) {
                eval('$delete = "' . template('viewtopic_post_delete') . '";');
            }
        }

        $bbcodeoff = $post['bbcodeoff'];
        $smileyoff = $post['smileyoff'];
        $post['message'] = postify($post['message'], $smileyoff, $bbcodeoff, $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode']);

        $attachment = $attachments->get_post_attachments($post['pid']);

        if (isset($self['viewsigs']) && $self['viewsigs'] == 'yes' && ($post['postnum'] > $CONFIG['viewsigminposts'])) {
            if ($post['usesig'] == 'yes' && !empty($post['sig'])) {
                $post['sig'] = censor($post['sig']);
                $post['sig'] = postify($post['sig'], 'no', 'no', $forum['allowsmilies'], $CONFIG['sigbbcode'], $forum['allowimgcode']);
                eval("\$post['message'] .= \"" . template('viewtopic_post_sig') . "\";");
            } elseif (empty($post['sig'])) {
                $usesig = false;
                eval("\$post['message'] .= \"" . template('viewtopic_post_nosig') . "\";");
            }
        } else {
            if (X_MEMBER && !isset($self['viewsigs']) && $self['viewsigs'] == 'no') {
                eval("\$post['message'] .= \"" . template('viewtopic_post_nosig') . "\";");
            }
        }

        if (!isset($rank['avatar'])) {
            $rank['avatar'] = '';
        }

        $location = '';
        if ($CONFIG['viewlocation'] == 'on' && !empty($post['location'])) {
            $location = '<br /><strong>' . $lang['textlocation'] . '</strong> ' . $post['location'];
        }

        if (!$notexist) {
            eval('$posts .= "' . template('viewtopic_post') . '";');
        } else {
            eval('$posts .= "' . template('viewtopic_invalid') . '";');
        }

        if ($thisbg == $THEME['altbg2']) {
            $thisbg = $THEME['altbg1'];
        } else {
            $thisbg = $THEME['altbg2'];
        }
    }
    $db->free_result($querypost);

    $modoptions = $mt_option = '';
    if ($status1 == 'Moderator') {
        $mt_option = '';
        if (isset($forum['mt_status']) && $forum['mt_status'] == 'on') {
            $mt_option = '<option value="markthread">' . $lang['markthread'] . '</option>';
        }
        eval('$modoptions = "' . template('viewtopic_modoptions') . '";');
    }

    $forumrules = '';
    if (isset($forum['frules_status']) && $forum['frules_status'] == 'on' && !empty($forum['frules'])) {
        $forum['frules'] = postify($forum['frules']);
        eval('$forumrules = "' . template('viewforum_rules') . '";');
    }

    if (isset($forum['mpfa']) && $forum['mpfa'] != 0 && isset($self['postnum']) && $self['postnum'] < $forum['mpfa'] && !X_SADMIN) {
        $message = str_replace("*posts*", $forum['mpfa'], $lang['mpfae']);
        error($message, false);
    }

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
                $replylink = $quickreply = '';
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
    eval('echo stripslashes("' . template('viewtopic') . '");');
    loadtime();
    eval('echo "' . template('footer') . '";');
    exit;
} elseif ($action == 'attachment' && isset($forum['attachstatus']) && $forum['attachstatus'] != 'off' && $pid > 0 && $tid > 0 && $aid > 0) {
    pwverify($forum['password'], 'viewtopic.php?tid=' . $tid, $fid, true);
    if (X_GUEST && $CONFIG['viewattach'] == 'no') {
        error($lang['Download_Halt_Msg']);
    }

    $query = $db->query("SELECT * FROM " . X_PREFIX . "attachments WHERE pid = '$pid' AND tid = '$tid' AND aid = '$aid'");
    $file = $db->fetch_array($query);
    $db->free_result($query);

    $db->query("UPDATE " . X_PREFIX . "attachments SET downloads = downloads+1 WHERE pid = '$pid' AND aid = '$aid'");

    if ($file['filesize'] != strlen($file['attachment'])) {
        error($lang['File_Corrupt']);
    }

    $type = $file['filetype'];

    if ($type == "image/pjpeg") {
        $type = "image/jpeg";
    }

    $name = str_replace(' ', '_', $file['filename']);
    $size = (int) $file['filesize'];
    $type = (strtolower($type) == 'text/html') ? 'text/plain' : $type;

    header("Content-type: $type");
    header("Content-length: $size");
    header("Content-Disposition: inline; filename = $name");
    header("Content-Description: PHP Generated Attachments");
    header("Cache-Control: public; max-age=604800");
    header("Expires: 604800");

    echo $file['attachment'];
    exit;
} elseif ($action == 'printable') {
    pwverify($forum['password'], 'viewtopic.php?tid=' . $tid, $fid, true);

    $querypost = $db->query("SELECT p.*, m.*,w.time FROM " . X_PREFIX . "posts p LEFT JOIN " . X_PREFIX . "members m ON m.username = p.author LEFT JOIN " . X_PREFIX . "whosonline w ON p.author = w.username WHERE p.fid = '$fid' AND p.tid = '$tid' ORDER BY p.pid $self[psorting] LIMIT $start_limit, " . $self['ppp']);
    $posts = '';
    $tmoffset = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
    while ($post = $db->fetch_array($querypost)) {
        if ($post['status'] == 'Banned') {
            $post['message'] = $lang['bannedpostmsg'];
        } else {
            $post['message'] = censor($post['message']);
            $post['message'] = postify($post['message'], $post['smileyoff'], $post['bbcodeoff'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode']);
        }
        $date = gmdate($self['dateformat'], $post['dateline'] + $tmoffset);
        $time = gmdate($self['timecode'], $post['dateline'] + $tmoffset);
        $poston = $date . ' ' . $lang['textat'] . ' ' . $time;

        eval('$posts .= "' . template('viewtopic_printable_row') . '";');
    }
    $db->free_result($querypost);
    eval('echo stripslashes("' . template('viewtopic_printable') . '");');
}
