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

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['textnewsletter']);
btitle($lang['textcp']);
btitle($lang['textnewsletter']);

eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $shadow2, $lang, $db, $THEME, $oToken, $CONFIG, $cheHTML, $selHTML;
    ?>
    <form method="post" action="cp_newsletter.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['textnewsletter'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                width="25%"><?php echo $lang['textsubject'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="newssubject" size="80"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top"
                                width="25%"><?php echo $lang['textmessage'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><textarea
                                        style="width: 100%" rows="20" cols="40" name="newsmessage"></textarea></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top"
                                width="25%"><?php echo $lang['textsendvia'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="radio"
                                                                                value="email"
                                                                                name="sendvia"/> <?php echo $lang['textemail'] ?>
                                <br/>
                                <input type="radio" value="pm" checked="checked"
                                       name="sendvia"/> <?php echo $lang['textpm'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top"
                                width="25%"><?php echo $lang['textsendto'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="radio"
                                                                                value="all" checked="checked"
                                                                                name="to"/> <?php echo $lang['textsendall'] ?>
                                <br/>
                                <input type="radio" value="staff" name="to"/> <?php echo $lang['textsendstaff'] ?><br/>
                                <input type="radio" value="superadmin" name="to"/> <?php echo $lang['superadmin'] ?>
                                <br/>
                                <input type="radio" value="admin" name="to"/> <?php echo $lang['textsendadmin'] ?><br/>
                                <input type="radio" value="supermod" name="to"/> <?php echo $lang['textsendsupermod'] ?>
                                <br/>
                                <input type="radio" value="mod" name="to"/> <?php echo $lang['textsendmod'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top"
                                width="25%"><?php echo $lang['textfaqextra'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="checkbox"
                                                                                value="yes" <?php echo $cheHTML ?>
                                                                                name="newscopy"/> <?php echo $lang['newsreccopy'] ?>
                                <br/>
                            </td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input type="submit" class="submit"
                                                   name="newslettersubmit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/></td>
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

function doPanel()
{
    global $lang, $db, $config_cache, $mailsys;
    global $oToken, $CONFIG, $self;

    if (!((bool) ini_get('safe_mode'))) {
        set_time_limit(0);
    }
    ignore_user_abort(1);
    ob_implicit_flush(1);

    $oToken->assertToken();

    $config_cache->expire('settings');
    $config_cache->expire('newpmmsg');

    $newssubject = $db->escape(formVar('newssubject'));
    $newsmessage = $db->escape(formVar('newsmessage'));
    $sendvia = $db->escape(formVar('sendvia'));
    $to = $db->escape(formVar('to'));
    $newscopy = $db->escape(formVar('newscopy'));

    $tome = '';
    if ($newscopy != 'yes') {
        $tome = "AND username != '" . $self['username'] . "'";
    }

    if ($to == 'all') {
        $query = $db->query("SELECT username, email FROM " . X_PREFIX . "members WHERE (status != 'Banned' AND newsletter='yes') $tome ORDER BY uid");
    } elseif ($to == 'staff') {
        $query = $db->query("SELECT username, email FROM " . X_PREFIX . "members WHERE (status = 'Super Administrator' OR status = 'Administrator' OR status = 'Super Moderator' OR status = 'Moderator') $tome ORDER BY uid");
    } elseif ($to == 'superadmin') {
        $query = $db->query("SELECT username, email FROM " . X_PREFIX . "members WHERE status = 'Super Administrator' $tome ORDER BY uid");
    } elseif ($to == 'admin') {
        $query = $db->query("SELECT username, email FROM " . X_PREFIX . "members WHERE status = 'Administrator' $tome ORDER BY uid");
    } elseif ($to == 'supermod') {
        $query = $db->query("SELECT username, email FROM " . X_PREFIX . "members WHERE status = 'Super moderator' $tome ORDER by uid");
    } elseif ($to == 'mod') {
        $query = $db->query("SELECT username, email FROM " . X_PREFIX . "members WHERE status = 'Moderator' $tome ORDER BY uid");
    }

    $_gbbuser = $db->escape(trim($self['username']));

    if ($sendvia == 'pm') {
        while (($memnews = $db->fetchArray($query)) != false) {
            $db->query("INSERT INTO " . X_PREFIX . "pm (msgto, msgfrom, type, owner, folder, subject, message, dateline, readstatus, sentstatus, usesig) VALUES ('" . $db->escape($memnews['username']) . "', '" . $_gbbuser . "', 'incoming', '" . $db->escape($memnews['username']) . "', 'Inbox', '$newssubject', '$newsmessage', '" . time() . "', 'no', 'yes', 'no')");
        }
        $db->freeResult($query);
    } else {
        $memcount = (int) $db->numRows($query);
        $i = 0;

        if (empty($CONFIG['adminemail'])) {
            error($lang['noadminemail'], false, '', '', 'cp_board.php', true, false, true);
        }

        if (empty($CONFIG['bbname'])) {
            error($lang['nobbname'], false, '', '', 'cp_board.php', true, false, true);
        }

        $mailsys->setTo($CONFIG['adminemail']);
        $mailsys->setFrom($CONFIG['adminemail'], $CONFIG['bbname']);
        $mailsys->setSubject('[' . $CONFIG['bbname'] . '] ' . stripslashes($newssubject));
        $mailsys->setMessage(stripslashes($newsmessage));

        while (($memnews = $db->fetchArray($query)) != false) {
            $mailsys->addBCC($memnews['email']);
            $i++;

            if ($i === 250 || $i === $memcount) {
                $mailsys->sendMail();
                if ($i === 250) {
                    sleep(3);
                }
                $i = 0;
            }
        }
        $db->freeResult($query);
    }
    cp_message($lang['newslettersubmit'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('newslettersubmit')) {
    viewPanel();
}

if (onSubmit('newslettersubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>