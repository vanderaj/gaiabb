<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
 * https://forums.xmbforum2.com/
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
// phpcs:disable PSR1.Files.SideEffects
require_once 'header.php';

loadtpl('search', 'search_results_row', 'search_results_none', 'search_results', 'search_nextlink');

$shadow = shadowfx();
$meta = metaTags();

smcwcache();

eval('$css = "' . template('css') . '";');

nav($lang['textsearch']);
btitle($lang['textsearch']);

$nextlink = $searchresults = '';
$srchfrom = intval(getRequestVar('srchfrom'));
$srchtxt = $db->escape(getRequestVar('srchtxt'));
$srchuname = $db->escape(getRequestVar('srchuname'));
$srchfid = formArray('srchfid');

// Ensure that all fids are integers, or empty
$srchfid = array_map('intval', $srchfid);

if (empty($srchfid)) {
    $srchfid[] = 'all';
}

$filter_distinct = getRequestVar('filter_distinct');
$filter_distinct = valYesNo($filter_distinct);

$page = getInt('page');
if ($page < 1) {
    $page = 1;
}

validatePpp();

eval('echo "' . template('header') . '";');

if ($CONFIG['searchstatus'] == 'off') {
    error($lang['fnasorry'], false);
}

if (X_GUEST) {
    error($lang['textnoaction'], false);
}

if (noSubmit('searchsubmit')) {
    $forumselect = forumList('srchfid[]', true, true);
    eval('echo stripslashes("' . template('search') . '");');
} else {
    if ((!empty($srchtxt) && strlen($srchtxt) < 3) || (!empty($srchuname) && strlen($srchuname) < 3)) {
        error($lang['textsearchnothing'], false);
    }

    $offset = ($page - 1) * $self['ppp'];
    $start = $offset;
    $end = $self['ppp'];

    $sql = "SELECT count(p.tid), p.*, m.status AS status, m.username, t.tid AS ttid, t.subject AS tsubject, t.replies+1 as posts, f.fid, f.private AS fprivate, f.userlist AS fuserlist, f.password AS password FROM (" . X_PREFIX . "posts p, " . X_PREFIX . "threads t) LEFT JOIN " . X_PREFIX . "members m ON m.username = p.author LEFT JOIN " . X_PREFIX . "forums f ON  f.fid = t.fid WHERE p.tid = t.tid";

    if ($srchfrom == 0) {
        $srchfrom = $onlinetime;
        $srchfromold = 0;
    } else {
        $srchfromold = $srchfrom;
    }

    $ext = array();

    $srchfrom = $onlinetime - $srchfrom;
    if ($srchtxt) {
        $sql .= " AND (p.message LIKE '%$srchtxt%' OR p.subject LIKE '%$srchtxt%' OR t.subject LIKE '%$srchtxt')";
        $ext[] = 'srchtxt=' . $srchtxt;
    }

    if (!empty($srchuname)) {
        $sql .= " AND p.author = '$srchuname'";
        $ext[] = 'srchuname=' . $srchuname;
    }

    $all = false;
    $strsql = '';
    if (!empty($srchfid)) {
        foreach ($srchfid as $dummy => $fid) {
            if ($fid == 'all') {
                $all = true;
                break;
            }
            $strsql .= "'" . intval($fid) . "', ";
        }
        $strsql = substr($strsql, 0, -2);
    }

    if ($all == false) {
        $sql .= " AND p.fid IN ($strsql)";
        $ext[] = 'srchfid IN (' . $strsql . ')';
    }

    if ($srchfrom) {
        $sql .= " AND p.dateline >= '$srchfrom'";
        $ext[] = 'srchfrom' . ((int) $srchfromold);
    }

    $sql .= " GROUP BY dateline ORDER BY dateline DESC LIMIT $start, $end";

    $pagenum = $page + 1;

    $querysrch = $db->query($sql);
    $results = $db->numRows($querysrch);

    if (!empty($srchuname)) {
        $srchtxt = '\0';
    }

    if ($filter_distinct == 'yes') {
        $temparray = array();
        $searchresults = '';
        while (($post = $db->fetchArray($querysrch)) != false) {
            $fidpw = isset($_COOKIE['fidpw' . $post['fid']]) ? $_COOKIE['fidpw' . $post['fid']] : '';
            $authorization = privfcheck($post['fprivate'], $post['fuserlist']);
            if ((!empty($post['password']) && $post['password'] != $fidpw) && !X_SADMIN) {
                continue;
            }

            if ($authorization) {
                if (!array_key_exists($post['ttid'], $temparray)) {
                    $tid = $post['ttid'];
                    $temparray[$tid] = true;

                    if ($post['status'] == 'Banned') {
                        $post['message'] = $lang['bannedpostmsg'];
                    }

                    $message = $post['message'];

                    $srchtxt = str_replace(array(
                        '_ ',
                        ' _',
                        '% ',
                        ' %',
                    ), '', $srchtxt);
                    $position = strpos($message, $srchtxt, 0);
                    $show_num = 100;
                    $msg_leng = strlen($message);

                    if ($position <= $show_num) {
                        $min = 0;
                        $add_pre = '';
                    } else {
                        $min = $position - $show_num;
                        $add_pre = '...';
                    }

                    if (($msg_leng - $position) <= $show_num) {
                        $max = $msg_leng;
                        $add_post = '';
                    } else {
                        $max = $position + $show_num;
                        $add_post = '...';
                    }

                    $show = substr($message, $min, $max);
                    $show = preg_replace("/($srchtxt)/i", '<span style="background-color: ' . $THEME['highlight'] . '"><strong><em>\\0</em></strong></span>', $show);
                    $show = postify($show);

                    $date = gmdate($self['dateformat'], $post['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
                    $time = gmdate($self['timecode'], $post['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
                    $poston = $date . ' ' . $lang['textat'] . ' ' . $time;
                    $postby = $post['author'];

                    $post['tsubject'] = censor($post['tsubject']);
                    if (trim($post['subject']) == '') {
                        $post['subject'] = $post['tsubject'];
                    }

                    if ($post['posts'] > $self['ppp']) {
                        $pbefore = $db->result($db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE tid = '" . $post['ttid'] . "' AND pid < '" . $post['pid'] . "'"), 0);
                        $page = ceil(($pbefore + 1) / $self['ppp']);
                    } else {
                        $page = 1;
                    }

                    $mouseover = celloverfx('viewtopic.php?tid=' . intval($post['ttid']) . '#pid' . intval($post['pid']));

                    eval('$searchresults .= "' . template('search_results_row') . '";');
                }
            }
        }
    } else {
        while (($post = $db->fetchArray($querysrch)) != false) {
            $fidpw = isset($_COOKIE['fidpw' . $post['fid']]) ? $_COOKIE['fidpw' . $post['fid']] : '';
            $authorization = privfcheck($post['fprivate'], $post['fuserlist']);
            if (($post['password'] != $fidpw && !empty($post['password'])) && !X_SADMIN) {
                continue;
            }

            if ($authorization) {
                $tid = $post['ttid'];

                if ($post['status'] == 'Banned') {
                    $post['message'] = $lang['bannedpostmsg'];
                }
                $message = $post['message'];

                $srchtxt = str_replace(array(
                    '_ ',
                    ' _',
                    '% ',
                    ' %',
                ), '', $srchtxt);

                $position = 0;
                if (!empty($srchtxt)) {
                    $position = strpos($message, $srchtxt, 0);
                }

                $show_num = 100;
                $msg_leng = strlen($message);

                if ($position <= $show_num) {
                    $min = 0;
                    $add_pre = '';
                } else {
                    $min = $position - $show_num;
                    $add_pre = '...';
                }

                if (($msg_leng - $position) <= $show_num) {
                    $max = $msg_leng;
                    $add_post = '';
                } else {
                    $max = $position + $show_num;
                    $add_post = '...';
                }

                $show = substr($message, $min, $max);
                $show = preg_replace("/($srchtxt)/i", '<span style="background-color: ' . $THEME['highlight'] . '"><strong><em>\\0</em></strong></span>', $show);
                $show = postify($show);
                $date = gmdate($self['dateformat'], $post['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
                $time = gmdate($self['timecode'], $post['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
                $poston = $date . ' ' . $lang['textat'] . ' ' . $time;
                $postby = $post['author'];

                $post['tsubject'] = censor($post['tsubject']);
                if (trim($post['subject']) == '') {
                    $post['subject'] = $post['tsubject'];
                } else {
                    $post['tsubject'] = $post['subject'];
                }

                if ($post['posts'] > $self['ppp']) {
                    $pbefore = $db->result($db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE tid = '" . $post['ttid'] . "' AND pid < '" . $post['pid'] . "'"), 0);
                    $page = ceil(($pbefore + 1) / $self['ppp']);
                } else {
                    $page = 1;
                }

                $mouseover = celloverfx('viewtopic.php?tid=' . intval($post['ttid']) . '#pid' . intval($post['pid']));

                eval('$searchresults .= "' . template('search_results_row') . '";');
            }
        }
    }

    if ($results == 0) {
        eval('$searchresults = "' . template('search_results_none') . '";');
    } elseif ($results == $self['ppp']) {
        $ext = htmlspecialchars(implode('&amp;', $ext));
        eval('$nextlink = "' . template('search_nextlink') . '";');
    }

    eval('echo stripslashes("' . template('search_results') . '");');
}

loadtime();
eval('echo "' . template('footer') . '";');
