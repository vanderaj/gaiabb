<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2021 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2021 The XMB Development Team
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
if (!defined('ROOT')) {
    define('ROOT', '../');
}

require_once ROOT . 'header.php';
require_once ROOT . 'include/admincp.inc.php';

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
    global $oToken, $CONFIG, $cheHTML, $selHTML;
    $select = array();
    $select[] = '<select name="postword"><option value=""></option>';
    $query = $db->query("SELECT find FROM " . X_PREFIX . "words");
    while (($censors = $db->fetchArray($query)) != false) {
        if (!empty($censors['find'])) {
            $select[] = '<option value="' . $censors['find'] . '">' . $censors['find'] . '</option>';
        }
    }
    $select[] = '</select>';
    $select = implode("\n", $select);
    $db->freeResult($query);
    ?>
    <form method="post" action="cp_search.php">
        <input type="hidden" name="csrf_token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td colspan="2" class="title"><?php echo $lang['insertdata'] ?>:</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="55%"><?php echo $lang['userip'] ?>:</td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="userip" size="30"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="55%"><?php echo $lang['postip'] ?>:</td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="postip" size="30"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="55%"><?php echo $lang['profileword'] ?>
                                :
                            </td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="profileword" size="30"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="55%"><?php echo $lang['postword'] ?>:
                            </td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $select ?></td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input type="submit" class="submit"
                                                   name="searchsubmit" value="<?php echo $lang['searchsubmit'] ?>"/>
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

    $oToken->assertToken();

    $profileword = $db->escape(formVar('profileword'));
    $postword = $db->escape(formVar('postword'));
    $userip = $db->escape(formVar('userip'));
    $postip = $db->escape(formVar('postip'));

    $found = 0;
    $list = array();

    if (!empty($userip) && is_ip($userip)) {
        $query = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE regip LIKE '%$userip%'");
        while (($users = $db->fetchArray($query)) != false) {
            $link = '../viewprofile.php?member=' . $users['username'] . '';
            $list[] = '<a href="' . $link . '">' . stripslashes($users['username']) . '<br />';
            $found++;
        }
        $db->freeResult($query);
    }

    if (!empty($postip) && is_ip($postip)) {
        $query = $db->query("SELECT * FROM " . X_PREFIX . "posts WHERE useip LIKE '%$postip%'");
        while (($users = $db->fetchArray($query)) != false) {
            $link = '../viewtopic.php?tid=' . $users['tid'] . '#pid' . $users['pid'] . '';
            if (!empty($users['subject'])) {
                $list[] = '<a href="' . $link . '">' . stripslashes($users['subject']) . '<br />';
            } else {
                $list[] = '<a href="' . $link . '">' . $lang['textnosub'] . '<br />';
            }
            $found++;
        }
        $db->freeResult($query);
    }

    if (!empty($profileword)) {
        $query = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE bio LIKE '%$profileword%' OR sig LIKE '%$profileword%'");
        while (($users = $db->fetchArray($query)) != false) {
            $link = '../viewprofile.php?member=' . $users['username'] . '';
            $list[] = '<a href="' . $link . '">' . stripslashes($users['username']) . '<br />';
            $found++;
        }
        $db->freeResult($query);
    }

    if (!empty($postword)) {
        $query = $db->query("SELECT * FROM " . X_PREFIX . "posts WHERE subject LIKE '%$postword%' OR message LIKE '%$postword%'");
        while (($users = $db->fetchArray($query)) != false) {
            $link = '../viewtopic.php?tid=' . $users['tid'] . '#pid' . $users['pid'] . '';
            if (!empty($users['subject'])) {
                $list[] = '<a href="' . $link . '">' . stripslashes($users['subject']) . '<br />';
            } else {
                $list[] = '<a href="' . $link . '">' . $lang['textnosub'] . '<br />';
            }
            $found++;
        }
        $db->freeResult($query);
    }
    ?>
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
           align="center">
        <tr>
            <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                       cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                    <tr class="category">
                        <td colspan="2"><font
                                    color="<?php echo $THEME['cattext'] ?>"><strong><?php echo $found ?></strong> <?php echo $lang['beenfound'] ?>
                        </td>
                    </tr>
                    <?php
                    foreach ($list as $num => $val) {
                        ?>
                        <tr class="tablerow" width="5%">
                            <td align="left" bgcolor="<?php echo $THEME['altbg2'] ?>"><strong><?php echo ($num + 1) ?>
                                    .</strong>
                            </td>
                            <td align="left" width="95%"
                                bgcolor="<?php echo $THEME['altbg1'] ?>">
                                <?php echo $val ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
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

if (noSubmit('searchsubmit')) {
    viewPanel();
}

if (onSubmit('searchsubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
