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

loadtpl('memberlist_row', 'memberlist', 'memberlist_admin', 'memberlist_multipage', 'memberlist_separator', 'memberlist_results_none');

$shadow = shadowfx();
$meta = metaTags();

smcwcache();

eval('$css = "' . template('css') . '";');

switch ($action) {
    case 'list':
        nav($lang['textmemberlist']);
        btitle($lang['textmemberlist']);
        break;
    default:
        nav($lang['error']);
        btitle($lang['error']);
        break;
}

$desc = getVar('desc');
if (strtolower($desc) != 'desc') {
    $desc = 'asc';
} else {
    $desc = 'desc';
}

$order = getRequestVar('order');
$srchmem = $db->escape(stripslashes(urldecode(getRequestVar('srchmem'))));
$srchemail = $db->escape(stripslashes(urldecode(getRequestVar('srchemail'))));
$srchip = $db->escape(stripslashes(urldecode(getRequestVar('srchip'))));

$list = getRequestVar('list');
if ($list != '' && $list != 'misc') {
    $list = substr($list, 0, 1);
    if (!eregi('^[a-z]', $list)) {
        $list = '';
    }
}

$page = getInt('page');
if ($page < 1) {
    $page = 1;
}

$orderby = $memberlist = $multipage = '';
switch ($action) {
    case 'list':
        if ($CONFIG['memliststatus'] == 'off') {
            error($lang['fnasorry']);
        }

        if (X_GUEST) {
            error($lang['textnoaction']);
        }
        if ($THEME['celloverfx'] == 'on') {
            $sortby_fx = "onmouseover=\"this.style.backgroundColor='$THEME[altbg1]';\" onmouseout=\"this.style.backgroundColor='$THEME[altbg2]';\"";
        } else {
            $sortby_fx = '';
        }
        $letters = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            $lang['lettermisc']
        );

        $lettersort = '<tr>';

        if (!empty($list)) {
            if ($THEME['celloverfx'] == 'on') {
                $lettersort .= "<td class=\"ctrtablerow\" bgcolor=\"$THEME[altbg2]\" onmouseover=\"this.style.backgroundColor='$THEME[altbg1]';\" onmouseout=\"this.style.backgroundColor='$THEME[altbg2]';\" onclick=\"location.href='memberlist.php?action=list'\" style=\"cursor:hand\"><a href=\"memberlist.php?action=list\"><u><strong>$lang[letterall]</strong></u></a></td>";
            } else {
                $lettersort .= '<td class="ctrtablerow" bgcolor="' . $THEME['altbg2'] . '"><a href="memberlist.php?action=list"><u><strong>' . $lang['letterall'] . '</strong></u></a></td>';
            }
        } else {
            $lettersort .= '<td class="ctrtablerow" bgcolor="' . $THEME['altbg1'] . '">[ <strong>' . $lang['letterall'] . '</strong> ]</td>';
        }

        for ($i = 0; $i < count($letters); $i++) {
            if ($list == strtolower($letters[$i])) {
                $lettersort .= '<td class="ctrtablerow" bgcolor="' . $THEME['altbg1'] . '">[ <strong>' . $letters[$i] . '</strong> ]</td>';
            } else {
                if ($THEME['celloverfx'] == 'on') {
                    $lettersort .= "<td class=\"ctrtablerow\" bgcolor=\"$THEME[altbg2]\" onmouseover=\"this.style.backgroundColor='$THEME[altbg1]';\" onmouseout=\"this.style.backgroundColor='$THEME[altbg2]';\" onclick=\"location.href='memberlist.php?action=list&amp;list=" . strtolower($letters[$i]) . "'\" style=\"cursor:hand\"><a href=\"memberlist.php?action=list&amp;list=" . strtolower($letters[$i]) . "\"><u><strong>$letters[$i]</strong></u></a></td>";
                } else {
                    $lettersort .= '<td class="ctrtablerow" bgcolor="' . $THEME['altbg2'] . '"><a href="memberlist.php?action=list&amp;list=' . strtolower($letters[$i]) . '"><u><strong>' . $letters[$i] . '</strong></u></a></td>';
                }
            }
        }

        $lettersort .= '</tr>';

        $ltrqry = '';
        if ($list != '' && $list != 'misc') {
            $ltrqry = "WHERE username LIKE '$list%'";
        }

        if ($list == 'misc') {
            $ltrqry = "WHERE username NOT LIKE 'A%' ";
            for ($i = 0; $i < count($letters); $i++) {
                $ltrqry .= "AND username NOT LIKE '$letters[$i]%' ";
            }
        }

        $listsort = '';
        if ($list != '' && $list != 'misc') {
            $listsort = '&amp;list=' . $list;
        }

        $start_limit = ($page > 1) ? (($page - 1) * $CONFIG['memberperpage']) : 0;

        if ($order != 'username' && $order != 'postnum' && $order != 'status' && $order != 'threadnum' && $order != 'lastvisit') {
            $orderby = 'uid';
            $order = 'uid';
        } else
            if ($order == 'status') {
                $orderby = "if (status = 'Super Administrator',1, if (status = 'Administrator', 2, if (status = 'Super Moderator', 3, if (status = 'Moderator', 4, if (status = 'member', 5, if (status = 'banned', 6, 7))))))";
            } else {
                $orderby = $db->escape($order);
            }

        if (!X_SADMIN) {
            $srchip = '';
            $srchemail = '';
            $memberlist_template = 'memberlist';
            $where = array();
        } else {
            $where = array();
            $memberlist_template = 'memberlist_admin';
        }

        $ext = array(
            '&amp;order=' . urlencode(stripslashes($order))
        );

        if (!empty($srchemail)) {
            if (!X_SADMIN) {
                $where[] = " email LIKE '%" . $srchemail . "%'";
                $where[] = " showemail = 'yes'";
            } else {
                $where[] = " email LIKE '%" . $srchemail . "%'";
            }
            $ext[] = 'srchemail=' . urlencode(stripslashes($srchemail));
            $srchemail = htmlspecialchars(stripslashes($srchemail));
        } else {
            $srchemail = '';
        }

        if (!empty($srchip)) {
            $where[] = " regip LIKE '%" . $srchip . "%'";
            $ext[] = 'srchip=' . urlencode(stripslashes($srchip));
            $srchip = htmlspecialchars(stripslashes($srchip));
        } else {
            $srchip = '';
        }

        if (!empty($srchmem)) {
            $where[] = " username LIKE '%$srchmem%'";
            $ext[] = 'srchmem=' . urlencode(stripslashes($srchmem));
            $srchmem = htmlspecialchars(stripslashes($srchmem));
        } else {
            $srchmem = '';
        }

        if (!empty($where) && isset($where[0]) && !empty($where[0])) {
            $q = implode(' AND', $where);
            $num = $db->result($db->query("SELECT COUNT(uid) FROM " . X_PREFIX . "members WHERE $q"), 0);
            $qmem = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE $q ORDER BY $orderby $desc LIMIT $start_limit, $CONFIG[memberperpage]");
        } else {
            $num = $db->result($db->query("SELECT COUNT(uid) FROM " . X_PREFIX . "members $ltrqry"), 0);
            $qmem = $db->query("SELECT * FROM " . X_PREFIX . "members $ltrqry ORDER BY $orderby $desc LIMIT $start_limit, $CONFIG[memberperpage]");
        }

        $ext = implode('&amp;', $ext);

        $adjTime = ($self['timeoffset'] * 3600) + $self['daylightsavings'];

        $members = $oldst = '';
        if ($db->num_rows($qmem) == 0) {
            $db->free_result($qmem);
            eval('$members = "' . template('memberlist_results_none') . '";');
        } else {
            while (($member = $db->fetch_array($qmem)) != false) {
                $member['regdate'] = gmdate($self['dateformat'], $member['regdate'] + $adjTime);

                if (!($member['lastvisit'] > 0)) {
                    $lastmembervisittext = $lang['textpendinglogin'];
                } else {
                    $lastvisitdate = gmdate($self['dateformat'], $member['lastvisit'] + $adjTime);
                    $lastvisittime = gmdate($self['timecode'], $member['lastvisit'] + $adjTime);
                    $lastmembervisittext = $lastvisitdate . ' ' . $lang['textat'] . ' ' . $lastvisittime;
                }

                if (!empty($member['customstatus'])) {
                    $member['customstatus'] = censor($member['customstatus']);
                    $member['customstatus'] = stripslashes($member['customstatus']);
                } else {
                    $member['customstatus'] = $lang['profilenoinformation'];
                }

                if (!empty($member['firstname']) || !empty($member['lastname']) && $member['showname'] == 'yes') {
                    $member['firstname'] = censor($member['firstname']);
                    $member['firstname'] = stripslashes($member['firstname']);
                    $member['lastname'] = censor($member['lastname']);
                    $member['lastname'] = stripslashes($member['lastname']);
                } else
                    if (empty($member['firstname']) || empty($member['lastname']) && $member['showname'] == 'no') {
                        $member['firstname'] = $lang['profilenoinformation'];
                        $member['lastname'] = '';
                    }

                $icon = $pre = $suff = '';
                switch ($member['status']) {
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

                $memurl = $icon . '<a href="viewprofile.php?memberid=' . intval($member['uid']) . '">' . $pre . '' . trim($member['username']) . '' . $suff . '</a>';
                $mouseover = celloverfx('viewprofile.php?memberid=' . intval($member['uid']) . '');
                if ($order == 'status') {
                    if ($oldst != $member['status']) {
                        $oldst = $member['status'];
                        $separator_text = (trim($member['status']) == '' ? $lang['onlineother'] : $member['status']);
                        eval('$members .= "' . template('memberlist_separator') . '";');
                    }
                }
                eval('$members .= "' . template('memberlist_row') . '";');
            }
            $db->free_result($qmem);
        }

        if (!isset($CONFIG['memberperpage'])) {
            $CONFIG['memberperpage'] = $CONFIG['postperpage'];
        }

        $mpurl = 'memberlist.php?action=list' . $listsort . '&amp;desc=' . $desc . '' . $ext;
        $multipage = '';
        if (($multipage = multi($num, $CONFIG['memberperpage'], $page, $mpurl)) !== false) {
            eval('$multipage = "' . template('memberlist_multipage') . '";');
        }

        switch ($desc) {
            case 'desc':
                $init['ascdesc'] = 'asc';
                $ascdesc = $lang['asc'];
                break;
            default:
                $init['ascdesc'] = 'desc';
                $ascdesc = $lang['desc'];
                break;
        }
        eval('$memberlist = "' . template($memberlist_template) . '";');
        break;
    default:
        error($lang['textnoaction']);
        break;
}

$header = $footer = '';
eval('$header = "' . template('header') . '";');
loadtime();
eval('$footer = "' . template('footer') . '";');
echo stripslashes($header . $memberlist . $footer);
?>
