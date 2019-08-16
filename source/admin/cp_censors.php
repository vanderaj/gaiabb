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
nav($lang['textcensors']);
btitle($lang['textcp']);
btitle($lang['textcensors']);

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function viewPanel()
{
    global $oToken, $CONFIG, $THEME, $lang, $shadow2, $db;
    ?>
    <form method="post" action="cp_censors.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td align="center" class="title"><?php echo $lang['textdeleteques'] ?></td>
                            <td align="center" class="title"><?php echo $lang['textcensorfind'] ?></td>
                            <td align="center" class="title"><?php echo $lang['textcensorreplace'] ?></td>
                        </tr>
                        <?php
                        $query = $db->query("SELECT * FROM " . X_PREFIX . "words ORDER BY id");
                        $rowsFound = $db->num_rows($query);
                        while (($censor = $db->fetch_array($query)) != false) {
                            ?>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                                <td><input type="checkbox" name="delete<?php echo $censor['id'] ?>"
                                           value="<?php echo $censor['id'] ?>"/></td>
                                <td><input type="text" size="20"
                                           name="find<?php echo $censor['id'] ?>"
                                           value="<?php echo $censor['find'] ?>"/></td>
                                <td><input type="text" size="20"
                                           name="replace<?php echo $censor['id'] ?>"
                                           value="<?php echo $censor['replace1'] ?>"/></td>
                            </tr>
                            <?php
                        }
                        $db->free_result($query);

                        if ($rowsFound < 1) {
                            ?>
                            <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="ctrtablerow">
                                <td colspan="3"><?php echo $lang['textnone'] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                            <td><strong><?php echo $lang['textnewcode'] ?></strong></td>
                            <td><input type="text" size="20" name="newfind"/></td>
                            <td><input type="text" size="20" name="newreplace"/></td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="3"><input type="submit" class="submit"
                                                   name="censorsubmit"
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

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function doPanel()
{
    global $db, $lang, $oToken;

    $oToken->assert_token();

    $querycensor = $db->query("SELECT id FROM " . X_PREFIX . "words");
    while (($censor = $db->fetch_array($querycensor)) != false) {
        $delete = "delete" . $censor['id'];
        $delete = formInt($delete);
        if ($delete > 0) {
            $db->query("DELETE FROM " . X_PREFIX . "words WHERE id = '$delete'");
        }

        $find = "find" . $censor['id'];
        $find = $db->escape(formVar($find));

        $replace = "replace" . $censor['id'];
        $replace = $db->escape(formVar($replace));
        $db->query("UPDATE " . X_PREFIX . "words SET find = '$find', replace1 = '$replace' WHERE id = '$censor[id]'");
    }
    $db->free_result($querycensor);

    $newfind = $db->escape(formVar('newfind'));
    $newreplace = $db->escape(formVar('newreplace'));
    if (!empty($newfind) && !empty($newreplace)) {
        $db->query("INSERT INTO " . X_PREFIX . "words (`find`, `replace1`) VALUES ('$newfind', '$newreplace')");
    }

    cp_message($lang['censorupdate'], false, '', '</td></tr></table>', 'cp_censors.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('censorsubmit')) {
    viewPanel();
}

if (onSubmit('censorsubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>