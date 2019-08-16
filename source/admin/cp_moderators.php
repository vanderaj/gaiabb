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

define('ROOT', '../');
define('ROOTINC', '../include/');
define('ROOTCLASS', '../class/');

require_once('../header.php');
require_once('../include/admincp.inc.php');

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken;

    ?>
    <form method="post" action="cp_moderators.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td><strong><font
                                            color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['textforum'] ?></font></strong>
                            </td>
                            <td><strong><font
                                            color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['textmoderator'] ?></font></strong>
                            </td>
                        </tr>
                        <?php
                        $oldfid = 0;
                        $query = $db->query("SELECT f.moderator, f.name, f.fid, c.name as cat_name, c.fid as cat_fid FROM " . X_PREFIX . "forums f LEFT JOIN " . X_PREFIX . "forums c ON (f.fup = c.fid) WHERE (c.type = 'group' AND f.type = 'forum') OR (f.type = 'forum' AND f.fup = '') ORDER BY c.displayorder, f.displayorder");
                        while (($forum = $db->fetch_array($query)) != false) {
                            if ($oldfid != $forum['cat_fid']) {
                                $oldfid = $forum['cat_fid'];
                                ?>
                                <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                                    <td colspan="2"><strong><?php echo stripslashes($forum['cat_name']) ?></strong></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo stripslashes($forum['name']) ?></td>
                                <td><input type="text" name="mod[<?php echo $forum['fid'] ?>]"
                                           value="<?php echo stripslashes($forum['moderator']) ?>"/></td>
                            </tr>
                            <?php
                            $querys = $db->query("SELECT name, fid, moderator FROM " . X_PREFIX . "forums WHERE fup = '$forum[fid]' AND type = 'sub'");
                            while (($sub = $db->fetch_array($querys)) != false) {
                                ?>
                                <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                    <td><?php echo $lang['4spaces'] ?><?php echo $lang['4spaces'] ?>
                                        <em><?php echo stripslashes($sub['name']) ?></em></td>
                                    <td><input type="text" name="mod[<?php echo $sub['fid'] ?>]"
                                               value="<?php echo stripslashes($sub['moderator']) ?>"/></td>
                                </tr>
                                <?php
                            }
                            $db->free_result($querys);
                        }
                        $db->free_result($query);
                        ?>
                        <tr>
                            <td colspan="2" class="tablerow"
                                bgcolor="<?php echo $THEME['altbg1'] ?>"><span
                                        class="smalltxt"><?php echo $lang['multmodnote'] ?></span></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="ctrtablerow"
                                bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="submit"
                                                                                class="submit" name="modsubmit"
                                                                                value="<?php echo $lang['textsubmitchanges'] ?>"/>
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

function doPanel()
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken;

    $oToken->assert_token();

    $mod = formArray('mod');
    if (is_array($mod) && !empty($mod)) {
        foreach ($mod as $fid => $mods) {
            $mods = $db->escape(trim($mods));
            $db->query("UPDATE " . X_PREFIX . "forums SET moderator = '$mods' WHERE fid = '$fid'");
        }
    }

    cp_message($lang['textmodupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('modsubmit')) {
    viewPanel();
}

if (onSubmit('modsubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>