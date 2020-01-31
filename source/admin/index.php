<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2020 The GaiaBB Project
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
define('ROOT', '../');
define('ROOTINC', '../include/');
define('ROOTCLASS', '../class/');

require_once('../header.php');
require_once('../include/admincp.inc.php');

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav($lang['textcp']);
btitle($lang['textcp']);

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

displayAdminPanel();

$query = $db->query("SELECT COUNT(uid) FROM " . X_PREFIX . "members UNION ALL SELECT COUNT(tid) FROM " . X_PREFIX . "threads UNION ALL SELECT COUNT(pid) FROM " . X_PREFIX . "posts UNION ALL SELECT COUNT(pmid) FROM " . X_PREFIX . "pm");
$members = $db->result($query, 0);
if ($members == false) {
    $members = 0;
}

$threads = $db->result($query, 1);
if ($threads == false) {
    $threads = 0;
}

$posts = $db->result($query, 2);
if ($posts == false) {
    $posts = 0;
}
$pms = $db->result($query, 3);
if ($pms == false) {
    $pms = 0;
}
$db->free_result($query);

$query = $db->query("SELECT COUNT(username) FROM " . X_PREFIX . "whosonline WHERE username LIKE 'xguest123'");
$gonline = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(username) FROM " . X_PREFIX . "whosonline WHERE username LIKE 'xrobot123'");
$ronline = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(username) FROM " . X_PREFIX . "whosonline WHERE username NOT LIKE 'xguest123'");
$monline = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(status) FROM " . X_PREFIX . "members WHERE status = 'Moderator'");
$mods = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(status) FROM " . X_PREFIX . "members WHERE status = 'Administrator'");
$admins = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(fid) FROM " . X_PREFIX . "forums WHERE type = 'forum'");
$forums = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(fid) FROM " . X_PREFIX . "forums WHERE type = 'forum' AND status = 'on'");
$forumsa = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(postnum) FROM " . X_PREFIX . "members WHERE postnum = '0'");
$inactive = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(readstatus) FROM " . X_PREFIX . "pm WHERE readstatus = 'yes'");
$readpms = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(status) FROM " . X_PREFIX . "members WHERE status = 'Super Moderator'");
$supmods = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(status) FROM " . X_PREFIX . "members WHERE status = 'Super Administrator'");
$supadmins = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(aid) FROM " . X_PREFIX . "attachments");
$attacht = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(aid) FROM " . X_PREFIX . "pm_attachments");
$attachu = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(type) FROM " . X_PREFIX . "favorites WHERE type = 'favorite'");
$favt = $db->result($query, 0);
$db->free_result($query);

$query = $db->query("SELECT COUNT(type) FROM " . X_PREFIX . "subscriptions WHERE type = 'subscription'");
$subt = $db->result($query, 0);
$db->free_result($query);

$mysqlver = $db->getVersion();
$phpver = phpversion();
$zendver = zend_version();
?>
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
           align="center">
        <tr>
            <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                       cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                    <tr class="category">
                        <td class="title"><?php echo $lang['textcp'] ?></td>
                    </tr>
                    <tr class="ctrtablerow">
                        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $lang['adminwelcome'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php echo $shadow2 ?>
    <br/>
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
           align="center">
        <tr>
            <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                       cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                    <tr class="category">
                        <td colspan="8" class="title"><?php echo $lang['finfo_foruminfo'] ?></td>
                    </tr>
                    <tr>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_boardvers'] ?></strong></td>
                        <td class="ctrtablerow"
                            bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $versiongeneral ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_boardbld'] ?></strong></td>
                        <td class="ctrtablerow"
                            bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $versionbuild ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totalforums'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $forums ?></td>
                    </tr>
                    <tr>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totalmems'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $members ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totalthreads'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $threads ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totalposts'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $posts ?></td>
                    </tr>
                    <tr>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_guestsonline'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $gonline ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_membersonline'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $monline ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_admins'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $admins ?></td>
                    </tr>
                    <tr>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_inactive'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $inactive ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_readpms'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $readpms ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_supadmins'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $supadmins ?></td>
                    </tr>
                    <tr>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totattach'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $attacht ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_mysql'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $mysqlver ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_phpver'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $phpver ?></td>
                    </tr>
                    <tr>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totuattach'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $attachu ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totbots'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $ronline ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totfav'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $favt ?></td>
                    </tr>
                    <tr>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_activeforums'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $forumsa ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totalpms'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $pms ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_mods'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $mods ?></td>
                    </tr>
                    <tr>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_supmods'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $supmods ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_zend'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $zendver ?></td>
                        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <strong><?php echo $lang['finfo_totsub'] ?></strong></td>
                        <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $subt ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php echo $shadow2 ?>
    </td>
    </tr>
    </table>
<?php

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>