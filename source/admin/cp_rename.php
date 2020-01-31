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

require_once '../header.php';
require_once '../include/admincp.inc.php';
require_once '../class/member.class.php';

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_SADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['superadminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPaneL()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML;
    ?>
    <form method="post" action="cp_rename.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td colspan="2"><strong><font
                                            color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['admin_rename_txt'] ?></font></strong>
                            </td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                width="22%"><?php echo $lang['admin_rename_userfrom'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="frmUserFrom" size="25"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                width="22%"><?php echo $lang['admin_rename_userto'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="frmUserTo" size="25"/></td>
                        </tr>
                        <tr class="ctrtablerow">
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="submit" class="submit" name="renamesubmit"
                                        value="<?php echo $lang['admin_rename_txt'] ?>"/></td>
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
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG, $onlinetime;

    $oToken->assert_token();

    $frmUserForm = formVar('frmUserFrom');
    $frmUserTo = formVar('frmUserTo');

    $adm = new member();
    $myErr = $adm->rename($frmUserForm, $frmUserTo);
    cp_message($myErr, false, '', '</td></tr></table>', 'cp_rename.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('renamesubmit')) {
    viewPanel();
}

if (onSubmit('renamesubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
