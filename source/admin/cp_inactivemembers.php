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

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['inactive']);
btitle($lang['textcp']);
btitle($lang['inactive']);

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
    global $oToken, $onlinetime;

    ?>
    <form method="post" action="cp_inactivemembers.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['inactive'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                width="22%"><?php echo $lang['num_days_expl'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="num_days" size="4"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                width="22%"><?php echo $lang['num_posts_expl'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="num_posts" size="4"/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                            <td colspan="2"><input class="submit" type="submit"
                                                   name="yessubmit" value="<?php echo $lang['textsubmitchanges'] ?>"/>
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
    global $oToken, $onlinetime;

    $oToken->assert_token();

    $num_days = formInt('num_days');
    $num_posts = formInt('num_posts');

    if ($num_days < 1) {
        cp_error($lang['num_days_not_there'], false, '', '</td></tr></table>');
    }

    if ($num_posts < 1) {
        cp_error($lang['num_posts_not_there'], false, '', '</td></tr></table>');
    }

    $old = $onlinetime - (60 * 60 * 24 * $num_days);
    $db->query("DELETE FROM " . X_PREFIX . "members WHERE lastvisit < $old AND postnum <= $num_posts AND status = 'Member'");
    cp_message($lang['tool_inactive'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('yessubmit')) {
    viewPanel();
}

if (onSubmit('yessubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
