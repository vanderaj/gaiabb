<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2022 The GaiaBB Group
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
 * http://www.xmbforum.com
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

require_once ROOT . 'header.php';
require_once ROOTINC . 'admincp.inc.php';

loadtpl(
    'cp_header',
    'cp_footer',
    'cp_message',
    'cp_error'
);

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['smilies']);
btitle($lang['textcp']);
btitle($lang['smilies']);

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
    global $oToken, $CONFIG, $cheHTML, $selHTML;
    ?>
    <form method="post" action="cp_smilies.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
    <tr>
    <td class="category" colspan="4" align="left"><font color="<?php echo $THEME['cattext'] ?>"><strong><?php echo $lang['smilies'] ?></strong></font></td>
    </tr>
    <tr class="header">
    <td align="center"><?php echo $lang['textdeleteques'] ?></td>
    <td><?php echo $lang['textsmiliecode'] ?></td>
    <td><?php echo $lang['textsmiliefile'] ?></td>
    <td align="center"><?php echo $lang['smilies'] ?></td>
    </tr>
    <?php

    $query = $db->query("SELECT * FROM " . X_PREFIX . "smilies WHERE type = 'smiley' ORDER BY id ASC");
    while ($smilie = $db->fetch_array($query)) {
        ?>
        <tr>
        <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow"><input type="checkbox" name="smdelete[<?php echo $smilie['id'] ?>]" value="1" /></td>
        <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"><input type="text" name="smcode[<?php echo $smilie['id'] ?>]" value="<?php echo $smilie['code'] ?>" /></td>
        <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"><input type="text" name="smurl[<?php echo $smilie['id'] ?>]" value="<?php echo $smilie['url'] ?>" /></td>
        <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow"><img src="../<?php echo $THEME['smdir'] ?>/<?php echo $smilie['url'] ?>" alt="<?php echo $smilie['code'] ?>" title="<?php echo $smilie['code'] ?>" /></td>
        </tr>
        <?php
}
    $db->free_result($query);
    ?>
    <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
    <td><?php echo $lang['textnewsmilie'] ?></td>
    <td><input type="text" name="newcode" value="" /></td>
    <td colspan="2"><input type="text" name="newurl1" value="" /></td>
    </tr>
    <tr>
    <td bgcolor="<?php echo $THEME['altbg1'] ?>" class="ctrtablerow"><input type="checkbox" name="autoinsertsmilies" value="1" /></td>
    <td bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow" colspan="3"><?php echo $lang['autoinsertsmilies'] ?> (<?php echo $THEME['smdir'] ?>)?</td>
    </tr>
    <tr>
    <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="4"><input type="submit" class="submit" name="smiliesubmit" value="<?php echo $lang['textsubmitchanges'] ?>" /></td>
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

    $smdelete = formArray('smdelete');
    $smcode = formArray('smcode');
    $smurl = formArray('smurl');
    $newcode = formVar('newcode');
    $newurl1 = formVar('newurl1');
    $autoinsertsmilies = formInt('autoinsertsmilies');

    if (is_array($smcode)) {
        foreach ($smcode as $key => $val) {
            if (count(array_keys($smcode, $val)) > 1) {
                cp_error($lang['smilieexists'], false, '', '</td></tr></table>');
            }
        }
    }

    $querysmilie = $db->query("SELECT id FROM " . X_PREFIX . "smilies WHERE type = 'smiley'");
    while ($smilie = $db->fetch_array($querysmilie)) {
        $id = $smilie['id'];
        if (!empty($smdelete[$id]) && $smdelete[$id] == 1) {
            $query = $db->query("DELETE FROM " . X_PREFIX . "smilies WHERE id = '$id'");
            continue;
        }
        $code = $db->escape($smcode[$id]);
        if (!empty($code)) {
            $url = $db->escape($smurl[$id]);
            $query = $db->query("UPDATE " . X_PREFIX . "smilies SET code = '$code', url = '$url' WHERE id = '$id' AND type = 'smiley'");
        }
    }
    $db->free_result($querysmilie);

    $newcode = formVar('newcode');
    if (!empty($newcode)) {
        if ($db->result($db->query("SELECT count(id) FROM " . X_PREFIX . "smilies WHERE code = '$newcode'"), 0) > 0) {
            cp_error($lang['smilieexists'], false, '', '</td></tr></table>');
        }
        $newurl1 = formVar('newurl1');
        $query = $db->query("INSERT INTO " . X_PREFIX . "smilies (type, code, url) VALUES ('smiley', '$newcode', '$newurl1')");
    }

    if ($autoinsertsmilies == 1) {
        $smilies_count = $newsmilies_count = 0;
        $smiley_url = array();
        $smiley_code = array();

        $query = $db->query("SELECT * FROM " . X_PREFIX . "smilies WHERE type = 'smiley' ORDER BY id ASC");
        while ($smiley = $db->fetch_array($query)) {
            $smiley_url[] = $smiley['url'];
            $smiley_code[] = $smiley['code'];
        }
        $db->free_result($query);

        $dir = opendir(ROOT . $THEME['smdir']);
        while ($smiley = readdir($dir)) {
            if ($smiley != '.' && $smiley != '..' && (strpos($smiley, '.gif') || strpos($smiley, '.jpg') || strpos($smiley, '.bmp') || strpos($smiley, '.png'))) {
                $newsmiley_url = $smiley;
                $newsmiley_code = $smiley;
                $newsmiley_code = preg_replace('/.gif/i', '', $newsmiley_code);
                $newsmiley_code = preg_replace('/.jpg/i', '', $newsmiley_code);
                $newsmiley_code = preg_replace('/.bmp/i', '', $newsmiley_code);
                $newsmiley_code = preg_replace('/.png/i', '', $newsmiley_code);
                $newsmiley_code = preg_replace('/ /', '_', $newsmiley_code);
                $newsmiley_code = ':' . $newsmiley_code . ':';
                if (!in_array($newsmiley_url, $smiley_url) && !in_array($newsmiley_code, $smiley_code)) {
                    $query = $db->query("INSERT INTO " . X_PREFIX . "smilies (type, code, url) VALUES ('smiley', '$newsmiley_code', '$newsmiley_url')");
                    $newsmilies_count++;
                }
                $smilies_count++;
            }
        }
        closedir($dir);
        // cp_confirmmsg($newsmilies_count . ' / ' . $smilies_count . ' ' . $lang['smiliesadded'], 'cp_posticons.php');
        cp_message($newsmilies_count . ' / ' . $smilies_count . ' ' . $lang['smiliesadded'], false, '', '</td></tr></table>', 'cp_posticons.php', true, false, true);
        echo '<br />';
    }
    cp_message($lang['smilieupdate'], false, '', '</td></tr></table>', 'cp_smilies.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('smiliesubmit')) {
    viewPanel();
}

if (onSubmit('smiliesubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
