<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2022 The GaiaBB Group
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
 *
 **/

define('ROOT', './');
require_once ROOT . 'header.php';

loadtpl(
    'index',
    'index_boardoffmsg',
    'index_category',
    'index_category_spacer',
    'index_forum',
    'index_forum_lastpost',
    'index_forum_nolastpost',
    'index_forum_row',
    'index_login',
    'index_member',
    'index_member_avatar',
    'index_member_notepad',
    'index_member_pm',
    'index_news',
    'index_stats',
    'index_whosonline',
    'index_whosonline_iconkey',
    'index_whosonline_key',
    'index_whosonline_today'
);

$shadow = shadowfx();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

$welcome = $whosonline = $statsblock = '';
$onlinekey = $boardoffmsg = $newsblock = '';
$gid = getInt('gid');
if ($gid > 0) {
    $welcome = '';
    if (X_GUEST) {
        eval('$welcome = "' . template('index_login') . '";');
    } else {
        $noteover = $pmover = $gallover = $profover = $optover = $markover = '';
        if ($THEME['celloverfx'] == 'on') {
            $noteover = celloverfx('usercp.php?action=notepad');
            $pmover = celloverfx('pm.php');
            $gallover = celloverfx('usercp.php?action=gallery');
            $profover = celloverfx('usercp.php?action=profile');
            $optover = celloverfx('usercp.php?action=options');
            $markover = celloverfx('markread.php');
        }
        $notepadblock = '';
        if ($CONFIG['notepadstatus'] == 'on') {
            eval('$notepadblock = "' . template('index_member_notepad') . '";');
        }
        $pmblock = '';
        if ($CONFIG['pmstatus'] == 'on') {
            eval('$pmblock = "' . template('index_member_pm') . '";');
        }
        $avatarblock = '';
        if ($CONFIG['avatars_status'] == 'on') {
            eval('$avatarblock = "' . template('index_member_avatar') . '";');
        }
        eval('$welcome = "' . template('index_member') . '";');
    }
    $CONFIG['indexnews'] = 'off';
    $CONFIG['whosonlinestatus'] = 'off';
    $CONFIG['indexstats'] = 'off';
    $qcat = $db->query("SELECT name FROM " . X_PREFIX . "forums WHERE fid = '$gid' AND type = 'group' LIMIT 1");
    $cat = $db->fetch_array($qcat);
    $db->free_result($qcat);
    nav(stripslashes($cat['name']));
    btitle(stripslashes($cat['name']));
} else {
    $gid = 0;
    nav($lang['texthome']);
    btitle($lang['texthome']);
}

// eval('echo "'.template('header').'";');

eval('$header = "' . template('header') . '";');
echo $header;

$q = $db->query("SELECT uid, username FROM " . X_PREFIX . "members ORDER BY regdate DESC LIMIT 1");
$lastmember = $db->fetch_array($q);
$db->free_result($q);

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

if (X_MEMBER) {
    $memhtml = '<a href="viewprofile.php?memberid=' . intval($lastmember['uid']) . '"><strong>' . trim($lastmember['username']) . '</strong></a>';
} else {
    $memhtml = '<strong>' . trim($lastmember['username']) . '</strong>';
}

if ($gid == 0) {
    $welcome = '';
    if (X_GUEST) {
        eval('$welcome = "' . template('index_login') . '";');
    } else {
        $noteover = $pmover = $gallover = $profover = $optover = $markover = '';
        if ($THEME['celloverfx'] == 'on') {
            $noteover = celloverfx('usercp.php?action=notepad');
            $pmover = celloverfx('pm.php');
            $gallover = celloverfx('usercp.php?action=gallery');
            $profover = celloverfx('usercp.php?action=profile');
            $optover = celloverfx('usercp.php?action=options');
            $markover = celloverfx('markread.php');
        }
        $notepadblock = '';
        if ($CONFIG['notepadstatus'] == 'on') {
            eval('$notepadblock = "' . template('index_member_notepad') . '";');
        }
        $pmblock = '';
        if ($CONFIG['pmstatus'] == 'on') {
            eval('$pmblock = "' . template('index_member_pm') . '";');
        }
        $avatarblock = '';
        if ($CONFIG['avatars_status'] == 'on') {
            eval('$avatarblock = "' . template('index_member_avatar') . '";');
        }
        eval('$welcome = "' . template('index_member') . '";');
    }

    $guestcount = $robotcount = $membercount = $hiddencount = 0;

    $onlineMembers = array();
    $onlineRobots = array();

    $q = $db->query("SELECT m.status, m.username, m.uid, m.invisible, w.* FROM " . X_PREFIX . "whosonline w LEFT JOIN " . X_PREFIX . "members m ON m.username = w.username ORDER BY w.username ASC");
    while ($online = $db->fetch_array($q)) {
        switch ($online['username']) {
            case 'xguest123':
                $guestcount++;
                break;
            case 'xrobot123':
                $onlineRobots[] = $online;
                $robotcount++;
                break;
            default:
                if ($online['invisible'] != 0 && X_ADMIN) {
                    $onlineMembers[] = $online;
                    $hiddencount++;
                } else if ($online['invisible'] != 0) {
                    $hiddencount++;
                } else {
                    $onlineMembers[] = $online;
                    $membercount++;
                }
                break;
        }
    }
    $db->free_result($q);

    $onlinetotal = $guestcount + $robotcount + $membercount + $hiddencount;
    $CONFIG['mostonlinecount'] = (int) $CONFIG['mostonlinecount'];
    if ($onlinetotal > $CONFIG['mostonlinecount']) {
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$onlinetime' WHERE config_name = 'mostonlinetime'");
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$onlinetotal' WHERE config_name = 'mostonlinecount'");
    }

    $CONFIG['mostonlinetime'] = (int) $CONFIG['mostonlinetime'];
    if ($CONFIG['mostonlinetime'] == 0) {
        $mosttext = $lang['Most_Users_None'];
    } else {
        $mostdate = gmdate($self['dateformat'], $CONFIG['mostonlinetime'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $mosttime = gmdate($self['timecode'], $CONFIG['mostonlinetime'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $mosttext = '<strong>' . $CONFIG['mostonlinecount'] . '</strong> ' . $lang['Most_On'] . ' <strong>' . $mostdate . '</strong> ' . $lang['Most_At'] . ' <strong>' . $mosttime . '</strong>';
    }

    if ($membercount != 1) {
        $membern = '<strong>' . $membercount . '</strong> ' . $lang['textmembers'];
    } else {
        $membern = '<strong>1</strong> ' . $lang['textmem'];
    }

    if ($guestcount != 1) {
        $guestn = '<strong>' . $guestcount . '</strong> ' . $lang['textguests'];
    } else {
        $guestn = '<strong>1</strong> ' . $lang['textguest1'];
    }

    if ($robotcount != 1) {
        $robotn = '<strong>' . $robotcount . '</strong> ' . $lang['textrobots'];
    } else {
        $robotn = '<strong>1</strong> ' . $lang['textrobot1'];
    }

    if ($hiddencount != 1) {
        $hiddenn = '<strong>' . $hiddencount . '</strong> ' . $lang['texthmems'];
    } else {
        $hiddenn = '<strong>1</strong> ' . $lang['texthmem'];
    }

    if (X_ADMIN) {
        eval($lang['whosoneval']);
        $memonmsg = '<span class="smalltxt">' . $lang['whosonmsg'] . '</span>';
    } else {
        eval($lang['whosoneval2']);
        $memonmsg = '<span class="smalltxt">' . $lang['whosonmsg2'] . '</span>';
    }

    $memtally = array();
    $num = 1;
    $show_total = (X_ADMIN) ? ($membercount + $hiddencount) : ($membercount);

    $show_inv_key = false;
    for ($mnum = 0; $mnum < $show_total; $mnum++) {
        $pre = $suff = $icon = '';
        $online = $onlineMembers[$mnum];
        switch ($online['status']) {
            case 'Super Administrator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_supadmin.gif" alt="' . $lang['ranksupadmin'] . '" title="' . $lang['ranksupadmin'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                    $suff = '</em></u></strong></span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                    $suff = '</em></u></strong></span>';
                }
                break;
            case 'Administrator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_admin.gif" alt="' . $lang['rankadmin'] . '" title="' . $lang['rankadmin'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                    $suff = '</u></strong></span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                    $suff = '</u></strong></span>';
                }
                break;
            case 'Super Moderator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_supmod.gif" alt="' . $lang['ranksupmod'] . '" title="' . $lang['ranksupmod'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                    $suff = '</strong></em></span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                    $suff = '</strong></em></span>';
                }
                break;
            case 'Moderator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_mod.gif" alt="' . $lang['rankmod'] . '" title="' . $lang['rankmod'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                    $suff = '</strong></span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                    $suff = '</strong></span>';
                }
                break;
            default:
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_mem.gif" alt="' . $lang['rankmem'] . '" title="' . $lang['rankmem'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                    $suff = '</span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                    $suff = '</span>';
                }
                break;
        }

        if ($online['invisible'] != 0) {
            $pre .= '<strike>';
            $suff = '</strike>' . $suff;
            if (!X_ADMIN && $online['username'] != $self['username']) {
                $num++;
                continue;
            }
        }

        if ($online['username'] == $self['username'] && $online['invisible'] != 0) {
            $show_inv_key = true;
        }

        if (X_MEMBER) {
            $memtally[] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . trim($online['username']) . '' . $suff . '</a>';
            $num++;
        } else {
            $memtally[] = $icon . '' . $pre . '' . trim($online['username']) . '' . $suff;
            $num++;
        }
    }

    $hidden = '';
    if (X_ADMIN || $show_inv_key === true) {
        $hidden = ' [ <strike>' . $lang['texthmem'] . '</strike> ]';
    }

    $robotkey = '';
    if ($CONFIG['whosrobot_status'] == 'on' && $robotcount > 0) {
        if ($THEME['riconstatus'] == 'on') {
            $robotkey = ' [ <img src="' . $THEME['ricondir'] . '/online_robot.gif" alt="' . $lang['textrobot1'] . '" title="' . $lang['textrobot1'] . '" border="0px" />' . $lang['textrobots'] . ' ]';
        } else {
            $robotkey = ' [ ' . $lang['textrobots'] . ' ]';
        }
        for ($rnum = 0; $rnum < $robotcount; $rnum++) {
            $online = $onlineRobots[$rnum];
            if ($CONFIG['whosrobotname_status'] == 'on') {
                if ($THEME['riconstatus'] == 'on') {
                    $memtally[] = '<img src="' . $THEME['ricondir'] . '/online_robot.gif" alt="' . $lang['textrobot1'] . '" title="' . $lang['textrobot1'] . '" border="0px" />' . $online['robotname'];
                } else {
                    $memtally[] = $online['robotname'];
                }
            } else {
                if ($THEME['riconstatus'] == 'on') {
                    $memtally[] = '<img src="' . $THEME['ricondir'] . '/online_robot.gif" alt="' . $lang['textrobot1'] . '" title="' . $lang['textrobot1'] . '" border="0px" />' . $lang['textrobot1'];
                } else {
                    $memtally[] = $lang['textrobot1'];
                }
            }
            $num++;
        }
    }

    $guests = '';
    if ($CONFIG['whosguest_status'] == 'on' && $guestcount > 0) {
        if ($THEME['riconstatus'] == 'on') {
            $guests = ' [ <img src="' . $THEME['ricondir'] . '/online_guest.gif" alt="' . $lang['textguest1'] . '" title="' . $lang['textguest1'] . '" border="0px" />' . $lang['textguests'] . ' ]';
        } else {
            $guests = ' [ ' . $lang['textguests'] . ' ]';
        }
        for ($mani = 0; $mani < $guestcount; $mani++) {
            if ($THEME['riconstatus'] == 'on') {
                $memtally[] = '<img src="' . $THEME['ricondir'] . '/online_guest.gif" alt="' . $lang['textguest1'] . '" title="' . $lang['textguest1'] . '" border="0px" />' . $lang['textguest1'];
            } else {
                $memtally[] = $lang['textguest1'];
            }
            $num++;
        }
    }

    $memtally = implode(', ', $memtally);

    if ($memtally == '') {
        $memtally = '&nbsp;';
    }

    $datecut = $onlinetime - (3600 * 24);
    if (X_ADMIN) {
        $q = $db->query("SELECT uid, username, status, lastvisit FROM " . X_PREFIX . "members WHERE lastvisit >= '$datecut' ORDER BY username ASC");
    } else {
        $q = $db->query("SELECT uid, username, status, lastvisit FROM " . X_PREFIX . "members WHERE lastvisit >= '$datecut' AND invisible != '1' ORDER BY username ASC");
    }

    $todaymembersnum = 0;
    $todaymembers = array();
    $pre = $suff = $icon = '';
    while ($memberstoday = $db->fetch_array($q)) {
        switch ($memberstoday['status']) {
            case 'Super Administrator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_supadmin.gif" alt="' . $lang['ranksupadmin'] . '" title="' . $lang['ranksupadmin'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                    $suff = '</em></u></strong></span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                    $suff = '</em></u></strong></span>';
                }
                break;
            case 'Administrator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_admin.gif" alt="' . $lang['rankadmin'] . '" title="' . $lang['rankadmin'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                    $suff = '</u></strong></span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                    $suff = '</u></strong></span>';
                }
                break;
            case 'Super Moderator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_supmod.gif" alt="' . $lang['ranksupmod'] . '" title="' . $lang['ranksupmod'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                    $suff = '</strong></em></span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                    $suff = '</strong></em></span>';
                }
                break;
            case 'Moderator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_mod.gif" alt="' . $lang['rankmod'] . '" title="' . $lang['rankmod'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                    $suff = '</strong></span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                    $suff = '</strong></span>';
                }
                break;
            default:
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_mem.gif" alt="' . $lang['rankmem'] . '" title="' . $lang['rankmem'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                    $suff = '</span>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                    $suff = '</span>';
                }
                break;
        }

        if (X_MEMBER) {
            $todaymembers[] = $icon . '<a href="viewprofile.php?memberid=' . intval($memberstoday['uid']) . '">' . $pre . '' . trim($memberstoday['username']) . '' . $suff . '</a>';
            ++$todaymembersnum;
        } else {
            $todaymembers[] = $icon . '' . $pre . '' . trim($memberstoday['username']) . '' . $suff;
            ++$todaymembersnum;
        }
    }
    $db->free_result($q);

    if ($todaymembersnum == 1) {
        $memontoday = '<strong>' . $todaymembersnum . '</strong>' . $lang['textmembertoday'];
    } else {
        $memontoday = '<strong>' . $todaymembersnum . '</strong>' . $lang['textmemberstoday'];
    }

    $q = $db->query("SELECT COUNT(*) AS guests FROM " . X_PREFIX . "guestcount WHERE onlinetime >= '$datecut'");
    $gueststoday = $db->result($q, 0);
    $db->free_result($q);
    $gueststodaycount = $gueststoday;

    if ($gueststodaycount == 0) {
        $gueststodaycount = 0;
    }

    if ($gueststodaycount == 0) {
        $todayguests = $lang['tgvisitno'];
    } else if ($gueststodaycount == 1) {
        $todayguests = $lang['tgvisit1'];
    } else {
        $todayguests = $lang['tgvisitand'] . ' <strong>' . $gueststodaycount . '</strong>' . $lang['tgvisitcount'];
    }

    $q = $db->query("SELECT COUNT(*) AS robots FROM " . X_PREFIX . "robotcount WHERE onlinetime >= '$datecut'");
    $robotstoday = $db->result($q, 0);
    $db->free_result($q);
    $robotstodaycount = $robotstoday;

    if ($robotstodaycount == 0) {
        $robotstodaycount = 0;
    }

    if ($robotstodaycount == 0) {
        $todayrobots = $lang['trvisitno'];
    } else if ($robotstodaycount == 1) {
        $todayrobots = $lang['trvisit1'];
    } else {
        $todayrobots = $lang['trvisitand'] . '<strong>' . $robotstodaycount . '</strong>' . $lang['trvisitcount'];
    }

    $todaymembers = implode(', ', $todaymembers);

    $whosonline = '';
    if ($CONFIG['whosonlinestatus'] == 'on') {
        $whostodayblock = '';
        if ($CONFIG['whosonlinetoday'] == 'on') {
            eval('$whostodayblock = "' . template('index_whosonline_today') . '";');
        }
        if ($THEME['riconstatus'] == 'on') {
            eval('$onlinekey = "' . template('index_whosonline_iconkey') . '";');
        } else {
            eval('$onlinekey = "' . template('index_whosonline_key') . '";');
        }
        eval('$whosonline = "' . template('index_whosonline') . '";');
    }

    $newsblock = '';
    if ($CONFIG['indexnews'] == 'on') {
        $CONFIG['indexnewstxt'] = postify($CONFIG['indexnewstxt']);
        $CONFIG['indexnewstxt'] = stripslashes($CONFIG['indexnewstxt']);
        eval('$newsblock = "' . template('index_news') . '";');
    }

    // $gid is set to 0 here !
    if ($gid = 0) {
        $cq = $db->query("SELECT f.name as cat_name, f.fid as cat_fid, l.uid as lp_uid, l.username as lp_user, l.pid as lp_pid, l.dateline as lp_dateline FROM " . X_PREFIX . "forums f LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = f.lastpost WHERE type = 'group' ORDER BY displayorder ASC");
    } else {
        $cq = $db->query("SELECT f.*, c.name as cat_name, c.fid as cat_fid, l.uid as lp_uid, l.username as lp_user, l.pid as lp_pid, l.dateline as lp_dateline FROM " . X_PREFIX . "forums f LEFT JOIN " . X_PREFIX . "forums c ON (f.fup = c.fid) LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = f.lastpost WHERE (c.type = 'group' AND f.type = 'forum' AND c.status = 'on' AND f.status = 'on') OR (f.type = 'forum' AND f.fup = '' AND f.status = 'on') ORDER BY c.displayorder ASC, f.displayorder ASC");
    }
} else {
    $cq = $db->query("SELECT f.*, l.uid as lp_uid, l.username as lp_user, l.pid as lp_pid, l.dateline as lp_dateline, c.name as cat_name, c.fid as cat_fid FROM " . X_PREFIX . "forums f LEFT JOIN " . X_PREFIX . "forums c ON (f.fup = c.fid) LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = f.lastpost WHERE (c.type = 'group' AND f.type = 'forum' AND c.status = 'on' AND f.status = 'on' AND f.fup = '$gid') ORDER BY c.displayorder ASC, f.displayorder ASC");
}

$boardoffmsg = '';
if (X_ADMIN && $CONFIG['bbstatus'] == 'off') {
    eval('$boardoffmsg = "' . template('index_boardoffmsg') . '";');
}

$statsblock = '';
if ($CONFIG['indexstats'] == 'on') {
    eval('$statsblock = "' . template('index_stats') . '";');
}

if ($THEME['space_cats'] == 'on') {
    eval('$spacer = "' . template('index_category_spacer') . '";');
    eval('$catrow1 = "' . template('index_forum_row') . '";');
    $catrow2 = $spcolor = $sptagbr = '';
} else {
    $spacer = $catrow1 = '';
    eval('$catrow2 = "' . template('index_forum_row') . '";');
    $spcolor = ' bgcolor="' . $THEME['bordercolor'] . '"';
    $sptagbr = $shadow . '<br />';
}

if ($CONFIG['showsubs'] == 'on') {
    $index_subforums = array();
    if ($gid == 0) {
        $query = $db->query("SELECT fid, fup, name, private, userlist FROM " . X_PREFIX . "forums WHERE status = 'on' AND type = 'sub' ORDER BY fup, displayorder");
        while ($queryrow = $db->fetch_array($query)) {
            $index_subforums[] = $queryrow;
        }
        $db->free_result($query);
    }
}

$lastcat = 0;
$forumlist = $cforum = '';
while ($row = $db->fetch_array($cq)) {
    $cforum = forum($row, 'index_forum');

    if ($lastcat != $row['cat_fid'] && !empty($cforum)) {
        $lastcat = $row['cat_fid'];
        eval('$forumlist .= "' . template('index_category') . '";');
    }
    $forumlist .= $cforum;
}
$db->free_result($cq);

eval('echo stripslashes("' . template('index') . '");');

loadtime();
eval('echo "' . template('footer') . '";');
