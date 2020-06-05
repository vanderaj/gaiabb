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

define('ROOT', '../');
define('ROOTINC', '../include/');
define('ROOTCLASS', '../class/');

require_once '../header.php';
require_once '../include/admincp.inc.php';
require_once '../class/member.class.php';

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error', 'popup_header', 'popup_footer', 'memberlist_multipage');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['textmembers']);
btitle($lang['textcp']);
btitle($lang['textmembers']);

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function validateSpecialRank($inRank)
{
    $specialRanks = array(
        "0",
        "Member",
        "Moderator",
        "Super Moderator",
        "Administrator",
        "Super Administrator",
        "Banned",
    );
    if (!in_array($inRank, $specialRanks)) {
        // Tamper attack, stop now
        return false;
    }
    return true;
}

function viewMemberCPForm()
{
    global $srchmem, $srchemail, $srchrank, $page, $CONFIG;
    global $shadow2, $THEME, $lang;
    global $oToken;
    ?>
    <form method="post" action="cp_members.php?action=search">
        <input type="hidden" name="token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['textmembers'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                width="22%"><?php echo $lang['textsrchusr'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="srchmem" value="" size="32"/></td>
                        </tr class="tablerow">
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow"
                                width="22%"><?php echo $lang['textsrchemail'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"><input
                                        type="text" name="srchemail" size="32"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                width="22%"><?php echo $lang['textwithstatus'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select
                                        name="srchrank">
                                    <option value="0"><?php echo $lang['anystatus'] ?></option>
                                    <option value="Super Administrator"><?php echo $lang['superadmin'] ?></option>
                                    <option value="Administrator"><?php echo $lang['textadmin'] ?></option>
                                    <option value="Super Moderator"><?php echo $lang['textsupermod'] ?></option>
                                    <option value="Moderator"><?php echo $lang['textmod'] ?></option>
                                    <option value="Member"><?php echo $lang['textmem'] ?></option>
                                    <option value="Banned"><?php echo $lang['textbanned'] ?></option>
                                </select></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                            <td colspan="2"><input type="submit" class="submit"
                                                   value="<?php echo $lang['textgo'] ?>"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php echo $shadow2 ?>
    </form>
    </td>
    </tr>
    </table>
    <?php
}

function viewMembers()
{
    global $srchmem, $srchemail, $srchrank, $page, $CONFIG;
    global $shadow2, $THEME, $lang, $lang_align;
    global $oToken, $selHTML;
    global $db;

    $sql = " FROM " . X_PREFIX . "members WHERE (";

    $srchmemtxt = '';
    if (!empty($srchmem)) {
        $sql .= "username LIKE '%$srchmem%' ";
        $srchmemtxt = "&amp;srchmem=" . urlencode($srchmem);
    } else {
        $sql .= "username LIKE '%%' ";
    }

    $srchemailtxt = '';
    if (!empty($srchemail)) {
        $sql .= "AND email LIKE '%$srchemail%' ";
        $srchemailtxt = "&amp;srchemail=" . rawurlencode($srchemail);
    }

    $srchranktxt = '&amp;srchrank=0';
    if ($srchrank != "0") {
        $sql .= "AND status='$srchrank' ";
        $srchranktxt = "&amp;srchrank=" . urlencode($srchrank);
    }

    $start = ($page - 1) * $CONFIG['memberperpage'];

    $sql .= ") ";

    $q1 = $db->query("SELECT uid " . $sql);
    $num = $db->num_rows($q1);
    $db->free_result($q1);

    $mpurl = 'cp_members.php?action=search' . $srchmemtxt . $srchemailtxt . $srchranktxt;

    $multipage = multi($num, $CONFIG['memberperpage'], $page, $mpurl);
    if ($multipage !== false) {
        eval('$multipage = "' . template('memberlist_multipage') . '";');
    }

    $q1 = $db->query("SELECT * " . $sql . " ORDER BY username LIMIT $start, $CONFIG[memberperpage]");
    $rowsFound = $db->num_rows($q1);
    ?>
    <form method="post" action="cp_members.php?action=members">
        <input type="hidden" name="token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td align="center" class="title"><?php echo $lang['textdeleteques'] ?></td>
                            <td align="center" class="title"><?php echo $lang['textusername'] ?></td>
                            <td align="center" class="title"><?php echo $lang['textnewpassword'] ?></td>
                            <td align="center" class="title"><?php echo $lang['textposts'] ?></td>
                            <td align="center" class="title"><?php echo $lang['textthreads'] ?></td>
                            <td align="center" class="title"><?php echo $lang['reagreerules'] ?></td>
                            <td align="center" class="title"><?php echo $lang['textstatus'] ?></td>
                            <td align="center" class="title"><?php echo $lang['textcusstatus'] ?></td>
                            <td align="center" class="title"><?php echo $lang['textbanfrom'] ?></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                            <td colspan="9"><?php echo $multipage ?></td>
                        </tr>
                        <?php
while (($member = $db->fetch_array($q1)) != false) {
        $readrulesyes = $readrulesno = '';
        switch ($member['readrules']) {
            case 'yes':
                $readrulesyes = $selHTML;
                break;
            default:
                $readrulesno = $selHTML;
                break;
        }

        $staff_disable = '';
        switch ($member['status']) {
            case 'Super Administrator':
                $staff_disable = 'disabled="disabled"';
                break;
            case 'Administrator':
                $staff_disable = 'disabled="disabled"';
                break;
            case 'Super Moderator':
                $staff_disable = 'disabled="disabled"';
                break;
            case 'Moderator':
                $staff_disable = 'disabled="disabled"';
                break;
            default:
                $staff_disable = '';
                break;
        }

        $sadminselect = $adminselect = $smodselect = '';
        $modselect = $memselect = $banselect = '';
        switch ($member['status']) {
            case 'Super Administrator':
                $sadminselect = $selHTML;
                break;
            case 'Administrator':
                $adminselect = $selHTML;
                break;
            case 'Super Moderator':
                $smodselect = $selHTML;
                break;
            case 'Moderator':
                $modselect = $selHTML;
                break;
            case 'Member':
                $memselect = $selHTML;
                break;
            case 'Banned':
                $banselect = $selHTML;
                break;
            default:
                $memselect = $selHTML;
                break;
        }

        $pmban = $postban = $bothban = $noban = '';
        switch ($member['ban']) {
            case 'pm':
                $pmban = $selHTML;
                break;
            case 'posts':
                $postban = $selHTML;
                break;
            case 'both':
                $bothban = $selHTML;
                break;
            default:
                $noban = $selHTML;
                break;
        }
        ?>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td align="center"><input type="checkbox"
                                                          name="delete<?php echo $member['uid'] ?>"
                                                          value="<?php echo $member['uid'] ?>" <?php echo $staff_disable ?> />
                                </td>
                                <td><a
                                            href="../viewprofile.php?memberid=<?php echo intval($member['uid']) ?>"><?php echo $member['username'] ?></a>
                                    <br/>
                                    <a
                                            href="cp_members.php?action=deleteposts&amp;member=<?php echo $member['uid'] ?>"><strong><?php echo $lang['cp_deleteposts'] ?></strong></a>
                                </td>
                                <td align="center"><input type="text" size="12"
                                                          name="pw<?php echo $member['uid'] ?>"/></td>
                                <td align="center"><input type="text" size="3"
                                                          name="postnum<?php echo $member['uid'] ?>"
                                                          value="<?php echo $member['postnum'] ?>"/></td>
                                <td align="center"><input type="text" size="3"
                                                          name="threadnum<?php echo $member['uid'] ?>"
                                                          value="<?php echo $member['threadnum'] ?>"/></td>
                                <td align="center"><select
                                            name="readrules<?php echo $member['uid'] ?>">
                                        <option value="yes" <?php echo $readrulesyes ?>
                                            <?php echo $staff_disable ?>><?php echo $lang['textyes'] ?></option>
                                        <option value="no" <?php echo $readrulesno ?>
                                            <?php echo $staff_disable ?>><?php echo $lang['textno'] ?></option>
                                    </select></td>
                                <td align="center"><select
                                            name="status<?php echo $member['uid'] ?>">
                                        <option value="Super Administrator" <?php echo $sadminselect ?>><?php echo $lang['superadmin'] ?></option>
                                        <option value="Administrator" <?php echo $adminselect ?>><?php echo $lang['textadmin'] ?></option>
                                        <option value="Super Moderator" <?php echo $smodselect ?>><?php echo $lang['textsupermod'] ?></option>
                                        <option value="Moderator" <?php echo $modselect ?>><?php echo $lang['textmod'] ?></option>
                                        <option value="Member" <?php echo $memselect ?>><?php echo $lang['textmem'] ?></option>
                                        <option value="Banned" <?php echo $banselect ?>><?php echo $lang['textbanned'] ?></option>
                                    </select></td>
                                <td align="center"><input type="text" size="16"
                                                          name="cusstatus<?php echo $member['uid'] ?>"
                                                          value="<?php echo htmlspecialchars(stripslashes($member['customstatus'])) ?>"/>
                                </td>
                                <td align="center"><select
                                            name="banstatus<?php echo $member['uid'] ?>">
                                        <option value="" <?php echo $noban ?>><?php echo $lang['noban'] ?></option>
                                        <option value="pm" <?php echo $pmban ?>><?php echo $lang['banpm'] ?></option>
                                        <option value="posts" <?php echo $postban ?>><?php echo $lang['banpost'] ?></option>
                                        <option value="both" <?php echo $bothban ?>><?php echo $lang['banboth'] ?></option>
                                    </select></td>
                            </tr>
                            <?php
$readrulesyes = $readrulesno = $staff_disable = '';
        $sadminselect = $adminselect = $smodselect = '';
        $modselect = $memselect = $banselect = '';
        $pmban = $postban = $bothban = $noban = '';
    }
    $db->free_result($q1);
    if ($rowsFound < 1) {
        ?>
                            <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="ctrtablerow">
                                <td colspan="9"><?php echo $lang['nouserfound'] ?></td>
                            </tr>
                            <?php
}
    ?>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                            <td colspan="9"><?php echo $multipage ?></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                            <td colspan="9"><input type="submit" class="submit"
                                                   name="membersubmit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/> <input
                                        type="hidden" name="srchmem" value="<?php echo $srchmem ?>"/> <input
                                        type="hidden" name="srchrank" value="<?php echo $srchrank ?>"/> <input
                                        type="hidden" name="page" value="<?php echo $page ?>"/> <input
                                        type="hidden" name="srchemail" value="<?php echo $srchemail ?>"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php echo $shadow2 ?>
    </form>
    </td>
    </tr>
    </table>
    <?php
}

function processMembers()
{
    global $srchmem, $srchemail, $srchrank, $page, $CONFIG;
    global $shadow2, $THEME, $lang, $lang_align;
    global $oToken, $selHTML;
    global $db, $self;

    $query = $db->query("SELECT MIN(uid) FROM " . X_PREFIX . "members WHERE status = 'Super Administrator'");
    $sa_uid = $db->result($query, 0);
    $db->free_result($query);

    $start = ($page - 1) * $CONFIG['memberperpage'];

    $sql = "SELECT uid, username, password, status FROM " . X_PREFIX . "members ";

    $where = array();
    if (!empty($srchmem)) {
        $where[] = "WHERE username LIKE '%$srchmem%' ";
    }

    if (!empty($srchemail)) {
        $where[] = "WHERE email LIKE '%$srchemail%' ";
    }

    if (!empty($srchrank)) {
        $where[] = "WHERE status='" . $srchrank . "' ";
    }

    if (!empty($where)) {
        $sql .= implode(' AND ', $where);
        $sql = str_replace('AND WHERE', 'AND', $sql);
    }

    $q2 = $db->query($sql . " ORDER BY username LIMIT $start, $CONFIG[memberperpage]");
    while (($mem = $db->fetch_array($q2)) != false) {
        $to['status'] = formVar("status" . $mem['uid']);
        if ($to['status'] == '') {
            $to['status'] = 'Member';
        }

        $origstatus = $mem['status'];
        $banstatus = formVar("banstatus" . $mem['uid']);
        $cusstatus = formVar("cusstatus" . $mem['uid']);
        $pw = formVar("pw" . $mem['uid']);
        $postnum = formVar('postnum' . $mem['uid']);
        $threadnum = formVar('threadnum' . $mem['uid']);
        $delete = formVar('delete' . $mem['uid']);
        $readrules = formVar('readrules' . $mem['uid']);

        // print_r($delete);

        if ($pw != '') {
            $newpw = md5($pw);
            $queryadd = " , password='$newpw'";
        } else {
            $newpw = $mem['password'];
            $queryadd = " , password='$newpw'";
        }

        if (!X_SADMIN && ($origstatus == 'Super Administrator' || $to['status'] == 'Super Administrator')) {
            continue;
        }

        if ($origstatus == 'Super Administrator' && $to['status'] != 'Super Administrator') {
            if ($db->result($db->query("SELECT COUNT(uid) FROM " . X_PREFIX . "members WHERE status = 'Super Administrator'"), 0) == 1) {
                cp_error($lang['lastsadmin'], false, '', '</td></tr></table>');
            }
        }

        if ($delete != '' && $delete != $self['uid'] && $delete != $sa_uid) {
            $rem = array();
            $un = $db->result($db->query("SELECT username FROM " . X_PREFIX . "members WHERE uid = '$delete'"), 0);
            $db->query("DELETE FROM " . X_PREFIX . "members WHERE uid = '$delete'");
            $queryr = $db->query("SELECT t.tid as ttid, count(p.pid) as postcount FROM " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "posts p ON p.tid = t.tid WHERE t.author = '$un' GROUP BY t.tid");
            while (($row = $db->fetch_array($queryr)) != false) {
                $q2 = $db->query("SELECT count(pid) FROM " . X_PREFIX . "posts WHERE author = '$un' AND tid = '$row[ttid]'");
                if ($row['postcount'] == $db->result($q2, 0)) {
                    $rem[] = $row['ttid'];
                }
            }
            $db->free_result($queryr);

            if (!empty($rem)) {
                $rem = implode(',', $rem);
                $db->query("DELETE FROM " . X_PREFIX . "threads WHERE tid IN (" . $rem . ")");
                $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE tid IN (" . $rem . ")");
                $db->query("DELETE FROM " . X_PREFIX . "favorites WHERE username = '$un' OR tid IN (" . $rem . ")");
                $db->query("DELETE FROM " . X_PREFIX . "subscriptions WHERE username = '$un' OR tid IN (" . $rem . ")");
            }

            unset($rem);

            $rem = array();
            $queryp = $db->query("SELECT pid FROM " . X_PREFIX . "posts WHERE author = '$un'");
            while (($row = $db->fetch_array($queryp)) != false) {
                $rem[] = $row['pid'];
            }
            $db->free_result($queryp);

            if (!empty($rem)) {
                $rem = implode(',', $rem);
                $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE pid IN (" . $rem . ")");
                $db->query("DELETE FROM " . X_PREFIX . "posts WHERE pid IN (" . $rem . ")");
            }

            unset($rem);

            $db->query("DELETE FROM " . X_PREFIX . "addresses WHERE username = '$un' OR addressname = '$un'");
            $db->query("DELETE FROM " . X_PREFIX . "pm WHERE msgfrom = '$un' OR msgto = '$un' OR owner='$un'");
            $db->query("DELETE FROM " . X_PREFIX . "pm_attachments WHERE owner = '$un'");
            $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE username = '$un'");
        } else {
            if (strpos($pw, '"') !== false || strpos($pw, "'") !== false) {
                $lang['textmembersupdate'] = $mem['username'] . ': ' . $lang['textpwincorrect'];
            } else {
                $newcustom = addslashes(trim($cusstatus));
                $db->query("UPDATE " . X_PREFIX . "members SET ban = '$banstatus', status = '$to[status]', postnum = '$postnum', customstatus = '$newcustom', threadnum='$threadnum', readrules='$readrules'$queryadd WHERE uid = '$mem[uid]'");
                $newpw = '';
            }
        }
    }
    $db->free_result($q2);
    cp_message($lang['textmembersupdate'], false, '', '</td></tr></table>', 'cp_members.php?action=members', true, false, true);
}

function processDeletePosts()
{
    global $lang;

    $member = getInt('member');
    if ($member > 0) {
        $memObj = new member($member);
        $retval = $memObj->deletePosts($member);
        if ($retval === true) {
            cp_message($lang['postsDeleted'], false, '', '</td></tr></table>', 'cp_members.php?action=members', true, false, true);
        }
    }
}

$srchmem = $db->escape(getRequestVar('srchmem'), 32, true);
$srchemail = $db->escape(getRequestVar('srchemail'), 75, true);
$srchrank = $db->escape(getRequestVar('srchrank'));
$members = getRequestVar('members');
$page = getRequestInt('page');
if ($page < 1) {
    $page = 1;
}

switch ($action) {
    case 'members':
        displayAdminPanel();
        if (noSubmit('membersubmit')) {
            viewMemberCPForm();
        }

        if (onSubmit('membersubmit')) {
            processMembers();
        }
        break;

    case 'search':
        displayAdminPanel();
        viewMembers();
        break;

    case 'deleteposts':
        processDeletePosts();
        break;

    default:
        displayAdminPanel();
        viewMemberCPForm();
        break;
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
