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
nav($lang['textuserranks']);

btitle($lang['textcp']);
btitle($lang['textuserranks']);

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
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML;
    ?>
    <form method="post" action="cp_ranks.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
    <tr class="category">
    <td class="title" align="center"><?php echo $lang['textdeleteques'] ?></td>
    <td class="title" align="center"><?php echo $lang['textcusstatus'] ?></td>
    <td class="title" align="center"><?php echo $lang['textposts'] ?></td>
    <td class="title" align="center"><?php echo $lang['textstars'] ?></td>
    <td class="title" align="center"><?php echo $lang['textallowavatars'] ?></td>
    <td class="title" align="center"><?php echo $lang['textavatar'] ?></td>
    </tr>
    <?php
    $avatarno = $avataryes = '';
    $query = $db->query("SELECT * FROM " . X_PREFIX . "ranks ORDER BY id");
    while ($rank = $db->fetch_array($query)) {
        $staff_disable = '';
        switch ($rank['title']) {
            case 'Super Administrator':
                $staff_disable = 'disabled="disabled"';
                break;
            case 'Administrator':
                $staff_disable = 'disabled="disabled"';
                break;
            case 'Super Moderator':
                $staff_disable = 'disabled="disabled"';
                break;
            case 'Moderator':
                $staff_disable = 'disabled="disabled"';
                break;
            default:
                $staff_disable = '';
                break;
        }

        $avataryes = $avatarno = '';
        switch ($rank['allowavatars']) {
            case 'yes':
                $avataryes = $selHTML;
                break;
            default:
                $avatarno = $selHTML;
                break;
        }
        ?>
        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
        <td><input type="checkbox" name="delete[<?php echo $rank['id'] ?>]" value="1" <?php echo $staff_disable ?> /></td>
        <td><input type="text" name="title[<?php echo $rank['id'] ?>]" value="<?php echo $rank['title'] ?>" <?php echo $staff_disable ?>/></td>
        <td><input type="text" name="posts[<?php echo $rank['id'] ?>]" value="<?php echo $rank['posts'] ?>" <?php echo $staff_disable ?> size="5" /></td>
        <td><input type="text" name="stars[<?php echo $rank['id'] ?>]" value="<?php echo $rank['stars'] ?>" size="4" /></td>
        <td>
        <select name="allowavatars[<?php echo $rank['id'] ?>]">
        <option value="yes" <?php echo $avataryes ?>><?php echo $lang['texton'] ?></option>
        <option value="no" <?php echo $avatarno ?>><?php echo $lang['textoff'] ?></option>
        </select>
        <input type="hidden" name="id[<?php echo $rank['id'] ?>]" value="<?php echo $rank['id'] ?>" />
        </td>
        <td><input type="text" name="avaurl[<?php echo $rank['id'] ?>]" value="<?php echo stripslashes($rank['avatarrank']); ?>" size="20" /></td>
        </tr>
        <?php
    }
    $db->free_result($query);
    ?>
    <tr class="tablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
    <td colspan="6">&nbsp;</td>
    </tr>
    <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
    <td><strong><?php echo $lang['textnewrank'] ?></strong></td>
    <td><input type="text" name="newtitle" value="" /></td>
    <td><input type="text" name="newposts" size="5" value="" /></td>
    <td><input type="text" name="newstars" size="4" value="" /></td>
    <td>
    <select name="newallowavatars">
    <option value="yes"><?php echo $lang['texton'] ?></option>
    <option value="no"><?php echo $lang['textoff'] ?></option></select></td>
    <td><input type="text" name="newavaurl" size="20" value="" /></td>
    </tr>
    <tr class="ctrtablerow">
    <td colspan="6" bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="submit" name="rankssubmit" class="submit" value="<?php echo $lang['textsubmitchanges'] ?>" /></td>
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

    $id = formArray('id', false, false, 'int');
    $title = formArray('title');
    $posts = formArray('posts', false, false, 'int');
    $stars = formArray('stars', false, false, 'int');
    $allowavatars = formArray('allowavatars');
    $avaurl = formArray('avaurl');

    // Load the existing ranks in
    $query = $db->query("SELECT * FROM " . X_PREFIX . "ranks ORDER BY id ASC");
    $staffranks = array();
    while ($ranks = $db->fetch_array($query)) {
        if ($ranks['title'] == 'Super Administrator' || $ranks['title'] == 'Administrator' || $ranks['title'] == 'Super Moderator' || $ranks['title'] == 'Moderator') {
            $title[$ranks['id']] = $ranks['title'];
            $staffranks[$ranks['id']] = $ranks['title'];
        }
    }
    $db->free_result($query);

    // Process existing ranks to be deleted
    $delete_keys = array();
    $delete = formArray('delete');
    foreach ($id as $key => $val) {
        if (isset($delete[$key]) && $delete[$key] == 1) {
            $delete_keys[] = (int) $key;
            continue;
        }
        if ($stars[$key] < 1 || $stars[$key] > 20) {
            $stars[$key] = 1;
        }
        $posts[$key] = (in_array($title[$key], $staffranks)) ? (int)  - 1 : $posts[$key];
        if ($posts[$key] < -1) {
            $posts[$key] = 0;
        }
        $db->query("UPDATE " . X_PREFIX . "ranks SET title = '" . $db->escape($title[$key]) . "', posts = '$posts[$key]', stars = '$stars[$key]', allowavatars = '" . $db->escape($allowavatars[$key]) . "', avatarrank = '" . $db->escape($avaurl[$key]) . "' WHERE id = '$key'");
    }
    $delete_keys = "'" . implode("', '", $delete_keys) . "'";
    $db->query("DELETE FROM " . X_PREFIX . "ranks WHERE id IN ($delete_keys)");

    $newtitle = $db->escape(formVar('newtitle'));
    $newposts = formInt('newposts');
    $newstars = formInt('newstars');
    $newavaurl = $db->escape(formVar('newavaurl'));
    $newallowavatars = $db->escape(formVar('newallowavatars'));

    if (!empty($newtitle)) {
        $db->query("INSERT INTO " . X_PREFIX . "ranks (title, posts, stars, allowavatars, avatarrank) VALUES ('$newtitle', '$newposts', '$newstars', '$newallowavatars', '$newavaurl')");
    }
    cp_message($lang['rankingsupdate'], false, '', '</td></tr></table>', 'cp_ranks.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('rankssubmit')) {
    viewPanel();
}
if (onSubmit('rankssubmit')) {
    doPanel();
}
loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
