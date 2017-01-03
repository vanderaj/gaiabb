<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2017 The GaiaBB Group
 * http://www.GaiaBB.com
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

displayAdminPanel();

function viewPanel()
{
    global $shadow2, $lang, $db, $THEME, $oToken, $CONFIG, $cheHTML, $selHTML;

    $CONFIG['adminnotes'] = stripslashes($CONFIG['adminnotes']);
    ?>
    <form method="post" action="cp_notepad.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr>
                            <td class="category"><strong><font
                                            color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['Admin_Notes'] ?></font></strong>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow"><textarea
                                        name="adminnotes" rows="20" cols="40"
                                        style="width: 100%"><?php echo $CONFIG['adminnotes'] ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                class="tablerow"><?php echo $lang['Admin_Notes_Note'] ?></td>
                        </tr>
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow"><input
                                        class="submit" type="submit" name="adminnotesubmit"
                                        value="<?php echo $lang['Admin_Notes_Submit'] ?>"/>&nbsp;<input
                                        class="submit" type="submit" name="adminnoteclear"
                                        value="<?php echo $lang['Admin_Notes_Clear'] ?>"/></td>
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

function doAdminNoteUpdate()
{
    global $lang, $db, $oToken;

    $oToken->assert_token();

    $adminnotesnew = $db->escape(formVar('adminnotes'));
    if (empty($adminnotesnew)) {
        cp_error($lang['No_Adminnotes'], false, '', '</td></tr></table>');
    }

    $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$adminnotesnew' WHERE config_name = 'adminnotes' LIMIT 1");
    cp_message($lang['Admin_Notes_Update'], false, '', '</td></tr></table>', 'cp_notepad.php', true, false, true);
}

function doAdminNoteCLear()
{
    global $lang, $db, $oToken;

    $oToken->assert_token();

    $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '' WHERE config_name = 'adminnotes' LIMIT 1");
    cp_message($lang['Admin_Notes_Cleared'], false, '', '</td></tr></table>', 'cp_notepad.php', true, false, true);
}

if (noSubmit('adminnotesubmit') && noSubmit('adminnoteclear')) {
    viewPanel();
}

if (onSubmit('adminnotesubmit')) {
    doAdminNoteUpdate();
}

if (onSubmit('adminnoteclear')) {
    doAdminNoteClear();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>