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

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['posticons']);
btitle($lang['textcp']);
btitle($lang['posticons']);
eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

function viewPanel()
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken, $CONFIG, $cheHTML, $selHTML;
    ?>
    <form method="post" action="cp_posticons.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td colspan="3" class="title"><?php echo $lang['picons'] ?></td>
                        </tr>
                        <tr class="header">
                            <td align="center"><?php echo $lang['textdeleteques'] ?></td>
                            <td><?php echo $lang['textsmiliefile'] ?></td>
                            <td align="center"><?php echo $lang['picons'] ?></td>
                        </tr>
                        <?php

    $query = $db->query("SELECT * FROM " . X_PREFIX . "smilies WHERE type = 'picon' ORDER BY id ASC");
    while (($smilie = $db->fetch_array($query)) != false) {
        ?>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td class="ctrtablerow"><input type="checkbox"
                                                               name="pidelete[<?php echo $smilie['id'] ?>]" value="1"/>
                                </td>
                                <td class="tablerow"><input type="text"
                                                            name="piurl[<?php echo $smilie['id'] ?>]"
                                                            value="<?php echo $smilie['url'] ?>"/></td>
                                <td class="ctrtablerow"><img
                                            src="../<?php echo $THEME['smdir'] ?>/<?php echo $smilie['url'] ?>"
                                            alt="<?php echo $smilie['url'] ?>"
                                            title="<?php echo $smilie['url'] ?>"/></td>
                            </tr>
                            <?php
}
    $db->free_result($query);
    ?>
                        <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                            <td><?php echo $lang['textnewpicon'] ?></td>
                            <td colspan="2"><input type="text" name="newurl2" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg1'] ?>">
                            <td class="ctrtablerow"><input type="checkbox"
                                                           name="autoinsertposticons" value="1"/></td>
                            <td class="tablerow" colspan="3"><?php echo $lang['autoinsertposticons'] ?>
                                (<?php echo $THEME['smdir'] ?>)?
                            </td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="3"><input type="submit" class="submit"
                                                   name="posticonsubmit"
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
    global $lang, $db, $oToken, $THEME, $shadow2;

    $oToken->assert_token();

    $pidelete = formArray('pidelete');
    $piurl = formArray('piurl');
    $newurl2 = formVar('newurl2');
    $autoinsertposticons = formVar('autoinsertposticons');
    if (is_array($piurl)) {
        foreach ($piurl as $key => $val) {
            if (count(array_keys($piurl, $val)) > 1) {
                cp_error($lang['piconexists'], false, false, '', '</td></tr></table>', 'cp_posticons.php', true, false, true);
            }
        }
    }
    $querysmilie = $db->query("SELECT id FROM " . X_PREFIX . "smilies WHERE type = 'picon' ORDER BY id ASC");
    while (($picon = $db->fetch_array($querysmilie)) != false) {
        $id = $picon['id'];
        if (isset($pidelete[$id]) && $pidelete[$id] == 1) {
            $query = $db->query("DELETE FROM " . X_PREFIX . "smilies WHERE id = '$picon[id]'");
            continue;
        }
        $query = $db->query("UPDATE " . X_PREFIX . "smilies SET url = '$piurl[$id]' WHERE id = '$picon[id]' AND type = 'picon'");
    }
    $db->free_result($querysmilie);
    if ($newurl2 != '') {
        if ($db->result($db->query("SELECT count(id) FROM " . X_PREFIX . "smilies WHERE url = '$newurl2' AND type = 'picon'"), 0) > 0) {
            cp_error($lang['piconexists'], false, '', '</td></tr></table>', 'cp_posticons.php', true, false, true);
        }
        $query = $db->query("INSERT INTO " . X_PREFIX . "smilies (type, code, url) VALUES ('picon', '', '$newurl2')");
    }
    if ($autoinsertposticons == 1) {
        $posticons_count = $newposticons_count = 0;
        $posticon_url = array();
        $query = $db->query("SELECT * FROM " . X_PREFIX . "smilies WHERE type = 'picon'");
        while (($picon = $db->fetch_array($query)) != false) {
            $posticon_url[] = $picon['url'];
        }
        $db->free_result($query);
        $dir = opendir(ROOT . $THEME['smdir']);
        while (($picon = readdir($dir)) != false) {
            if ($picon != '.' && $picon != '..' && (strpos($picon, '.gif') || strpos($picon, '.jpg') || strpos($picon, '.bmp') || strpos($picon, '.png'))) {
                $newposticon_url = $picon;
                $newposticon_url = urlencode($newposticon_url);
                if (!in_array($newposticon_url, $posticon_url)) {
                    $query = $db->query("INSERT INTO " . X_PREFIX . "smilies (type, url) VALUES ('picon', '$newposticon_url')");
                    $newposticons_count++;
                }
                $posticons_count++;
            }
        }
        closedir($dir);
        cp_message($newposticons_count . ' / ' . $posticons_count . ' ' . $lang['posticonsadded'], false, '', '</td></tr></table><br />', 'cp_posticons.php', true, false, true);
    }
    cp_message($lang['posticonsupdate'], false, '', '</td></tr></table>', 'cp_posticons.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('posticonsubmit')) {
    viewPanel();
}
if (onSubmit('posticonsubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
