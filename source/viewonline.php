<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2013 The GaiaBB Group
 * http://www.GaiaBB.com
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

// Safe to use without global.inc.php
define('DEBUG_REG', true);
define('ROOT', './');

require_once (ROOT . 'header.php');
require_once (ROOTINC . 'online.inc.php');

loadtpl('online_row_admin', 'online_row', 'online_admin', 'online');

$shadow = shadowfx();
$meta = metaTags();

smcwcache();

eval('$css = "' . template('css') . '";');

nav($lang['whosonline']);
btitle($lang['whosonline']);

eval('echo "' . template('header') . '";');

if (X_GUEST) {
    error($lang['textnoaction'], false);
}

if ($CONFIG['whosonlinestatus'] == 'off') {
    error($lang['fnasorry'], false);
}

if (X_ADMIN) {
    $q = $db->query("SELECT m.uid, m.status, m.username, w.* FROM " . X_PREFIX . "whosonline w LEFT JOIN " . X_PREFIX . "members m ON m.username=w.username ORDER BY w.username ASC");
} else {
    $q = $db->query("SELECT m.uid, m.status, m.username, w.* FROM " . X_PREFIX . "whosonline w LEFT JOIN " . X_PREFIX . "members m ON m.username=w.username WHERE w.invisible='0' OR (w.invisible='1' AND w.username='" . $self['username'] . "') ORDER BY w.username ASC");
}

$onlineusers = '';
while (($online = $db->fetch_array($q)) != false) {
    $array = url_to_text($online['location']);
    $onlinetime = gmdate($self['timecode'], $online['time'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
    $username = str_replace('xguest123', $lang['textguest1'], $online['username']);
    
    if ($online['username'] == 'xrobot123') {
        $username = $online['robotname'];
    }
    
    $online['location'] = $array['text'];
    if (X_STAFF) {
        $online['location'] = '<a href="' . $array['url'] . '">' . $array['text'] . '</a>';
        $online['location'] = stripslashes($online['location']);
    }
    
    if ($online['invisible'] == 1 && (X_ADMIN || $online['username'] == $self['username'])) {
        $hidepre = '<strike>';
        $hidesuff = '</strike>';
    } else {
        $hidepre = $hidesuff = '';
    }
    
    if ($online['username'] != 'xguest123' && $online['username'] != $lang['textguest1'] && $online['username'] != 'xrobot123' && $online['username'] != $lang['textrobot1']) {
        $icon = $pre = $suff = '';
        switch ($online['status']) {
            case 'Super Administrator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_supadmin.gif" alt="' . $lang['ranksupadmin'] . '" title="' . $lang['ranksupadmin'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                    $suff = '</em></u></strong></span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                    $suff = '</em></u></strong></span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                }
                break;
            case 'Administrator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_admin.gif" alt="' . $lang['rankadmin'] . '" title="' . $lang['rankadmin'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                    $suff = '</u></strong></span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                    $suff = '</u></strong></span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                }
                break;
            case 'Super Moderator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_supmod.gif" alt="' . $lang['ranksupmod'] . '" title="' . $lang['ranksupmod'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                    $suff = '</strong></em></span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                    $suff = '</strong></em></span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                }
                break;
            case 'Moderator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_mod.gif" alt="' . $lang['rankmod'] . '" title="' . $lang['rankmod'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                    $suff = '</strong></span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                    $suff = '</strong></span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                }
                break;
            default:
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_mem.gif" alt="' . $lang['rankmem'] . '" title="' . $lang['rankmem'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                    $suff = '</span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                    $suff = '</span>';
                    $online['username'] = $icon . '<a href="viewprofile.php?memberid=' . intval($online['uid']) . '">' . $pre . '' . $hidepre . '' . $username . '' . $hidesuff . '' . $suff . '</a>';
                }
                break;
        }
    } else 
        if ($online['username'] == 'xrobot123') {
            if ($THEME['riconstatus'] == 'on') {
                $icon = '<img src="' . $THEME['ricondir'] . '/online_robot.gif" alt="' . $lang['textrobot1'] . '" title="' . $lang['textrobot1'] . '" border="0px" />';
                $online['username'] = $icon . '' . $username;
            } else {
                $icon = '';
                $online['username'] = $icon . '' . $username;
            }
        } else {
            if ($THEME['riconstatus'] == 'on') {
                $icon = '<img src="' . $THEME['ricondir'] . '/online_guest.gif" alt="' . $lang['textguest1'] . '" title="' . $lang['textguest1'] . '" border="0px" />';
                $online['username'] = $icon . '' . $username;
            } else {
                $icon = '';
                $online['username'] = $icon . '' . $username;
            }
        }
    
    if (X_ADMIN) {
        eval('$onlineusers .= "' . template('online_row_admin') . '";');
    } else {
        eval('$onlineusers .= "' . template('online_row') . '";');
    }
}
$db->free_result($q);

if (X_ADMIN) {
    eval('echo "' . template('online_admin') . '";');
} else {
    eval('echo "' . template('online') . '";');
}

loadtime();
eval('echo "' . template('footer') . '";');
?>
