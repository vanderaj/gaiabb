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

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['raw_mysql']);
btitle($lang['textcp']);
btitle($lang['raw_mysql']);

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_SADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['superadminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML;
    ?>
    <form method="post" action="cp_rawsql.php" enctype="multipart/form-data">
        <input type="hidden" name="token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['textupgrade'] ?></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                            <td colspan="2"><?php echo $lang['upgrade'] ?></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td valign="top"><textarea style="width: 100%" rows="20" cols="40"
                                                       name="upgrade"></textarea></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td colspan="2"><input type="file" name="sql_file" size="40"
                                                   value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                            <td colspan="2"><?php echo $lang['upgradenote'] ?></td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input type="submit" class="submit"
                                                   name="upgradesubmit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/>&nbsp;<input
                                        type="reset" value="<?php echo $lang['Clear_Form'] ?>"/></td>
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

    $oToken->assertToken();

    $upgrade = formVar('upgrade');

    if (isset($_FILES['sql_file'])) {
        $add = get_attached_file($_FILES['sql_file'], 'on');
        if ($add !== false) {
            $upgrade .= $add;
        }
    }

    $upgrade = str_replace('$table_', X_PREFIX, $upgrade);
    $explode = explode(';', $upgrade);
    $count = count($explode);
    if (strlen(trim($explode[$count - 1])) == 0) {
        unset($explode[$count - 1]);
        $count--;
    }

    for ($num = 0; $num < $count; $num++) {
        $explode[$num] = stripslashes($explode[$num]);
        if ($CONFIG['specq'] == 'off') {
            if (strtoupper(substr(trim($explode[$num]), 0, 3)) == 'USE' || strtoupper(substr(trim($explode[$num]), 0, 14)) == 'SHOW DATABASES') {
                cp_error($lang['textillegalquery'], false, '', '</td></tr></table>');
            }
        }
        if ($explode[$num] != '') {
            $query = $db->query($explode[$num], true);
        }
        ?>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td colspan="<?php echo $db->numFields($query) ?>">
                                <strong><?php echo $lang['upgraderesults'] ?></strong>&nbsp;<?php echo $explode[$num] ?>
                            </td>
                        </tr>
                        <?php
                        $xn = strtoupper($explode[$num]);
                        if (strpos($xn, 'SELECT') !== false || strpos($xn, 'SHOW') !== false || strpos($xn, 'EXPLAIN') !== false || strpos($xn, 'DESCRIBE') !== false) {
                            dump_query($query, true);
                        } else {
                            $selq = false;
                        }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
        <?php echo $shadow2 ?>
        <br/>
        <?php
    }
    ?>
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
           align="center">
        <tr>
            <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                       cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                    <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                        <td><?php echo $lang['upgradesuccess'] ?></td>
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
}

displayAdminPanel();

if (noSubmit('upgradesubmit')) {
    viewPanel();
}

if (onSubmit('upgradesubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>