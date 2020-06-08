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
// phpcs:disable PSR1.Files.SideEffects
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
nav($lang['inactivethreads']);
btitle($lang['textcp']);
btitle($lang['inactivethreads']);

eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $oToken, $CONFIG, $THEME, $lang, $shadow2;

    ?>
    <form method="post" action="cp_closethreads.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['inactivethreads'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"
                                colspan="2"><?php echo $lang['fid_to_close_note'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                width="40%"><?php echo $lang['fid_to_close'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="fid" size="10"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                width="40%"><?php echo $lang['num_days_forthreads'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="num_days" size="10"/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                            <td colspan="2"><input class="submit" type="submit"
                                                   name="closesubmit"
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
    global $onlinetime, $oToken, $CONFIG, $THEME, $lang, $shadow2, $db;

    $oToken->assertToken();

    $fid = formVar('fid');
    if (empty($fid)) {
        cp_error($lang['fid_forthreads_not_there'], false, '', '</td></tr></table>');
    }

    $num_days = formInt('num_days');
    if ($num_days < 1) {
        cp_error($lang['num_days_forthreads_not_there'], false, '', '</td></tr></table>');
    }

    $old = $onlinetime - (60 * 60 * 24 * $num_days);

    $fid_array = explode(',', $fid);
    $clean_array = array();
    foreach ($fid_array as $key => $val) {
        if (is_numeric($val) && $val > 0) {
            $clean_array[] = intval($val);
        }
    }
    $clean_str = implode(',', $clean_array);

    if (!empty($clean_str)) {
        $db->query("UPDATE " . X_PREFIX . "threads t, " . X_PREFIX . "lastposts l SET t.closed = 'yes' WHERE t.fid IN (" . $clean_str . ") AND t.tid=l.tid AND l.dateline < " . $old);
    }
    cp_message($lang['tool_inactivethreads'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('closesubmit')) {
    viewPanel();
}

if (onSubmit('closesubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
