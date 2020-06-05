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

eval('$css = "' . template('css') . '";');

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['pluglinkadmin']);
btitle($lang['textcp']);
btitle($lang['pluglinkadmin']);

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
    <form method="post" action="cp_pluglinks.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" align="center"><?php echo $lang['textdeleteques'] ?></td>
                            <td class="title" align="center"><?php echo $lang['pluglinkorder'] ?></td>
                            <td class="title" align="center"><?php echo $lang['pluglinkname'] ?></td>
                            <td class="title" align="center"><?php echo $lang['pluglinkimg'] ?></td>
                            <td class="title" align="center"><?php echo $lang['pluglinkurl'] ?></td>
                            <td class="title" align="center"><?php echo $lang['pluglinkstatus'] ?></td>
                        </tr>
                        <?php
$plugs = $db->query("SELECT * FROM " . X_PREFIX . "pluglinks ORDER BY displayorder ASC");
    $rowsFound = $db->num_rows($plugs);
    while (($pluginfo = $db->fetch_array($plugs)) != false) {
        $on = $off = '';
        switch ($pluginfo['status']) {
            case 'on':
                $on = $selHTML;
                break;
            default:
                $off = $selHTML;
                break;
        }
        ?>
                            <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><input type="checkbox"
                                           name="delete<?php echo $pluginfo['id'] ?>"
                                           value="<?php echo $pluginfo['id'] ?>"/></td>
                                <td><input type="text"
                                           name="displayorder<?php echo $pluginfo['id'] ?>"
                                           value="<?php echo $pluginfo['displayorder'] ?>" size="2"/></td>
                                <td><input type="text" name="name<?php echo $pluginfo['id'] ?>"
                                           value="<?php echo stripslashes($pluginfo['name']) ?>" size="15"/></td>
                                <td><input type="text" name="img<?php echo $pluginfo['id'] ?>"
                                           value="<?php echo stripslashes($pluginfo['img']) ?>" size="15"/></td>
                                <td><input type="text" name="url<?php echo $pluginfo['id'] ?>"
                                           value="<?php echo stripslashes($pluginfo['url']) ?>" size="15"/></td>
                                <td><select name="status<?php echo $pluginfo['id'] ?>">
                                        <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                        <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                    </select></td>
                            </tr>
                            <?php
}
    $db->free_result($plugs);
    if ($rowsFound < 1) {
        ?>
                            <tr>
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>" class="ctrtablerow"
                                    colspan="6"><?php echo $lang['pluglinknone'] ?></td>
                            </tr>
                            <?php
}
    ?>
                        <tr class="category">
                            <td class="title" colspan="6"><?php echo $lang['pluglinkcreate'] ?></td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td>&nbsp;</td>
                            <td><input type="text" name="displayordernew" value="" size="2"/></td>
                            <td><input type="text" name="namenew" value="" size="15"/></td>
                            <td><input type="text" name="imgnew" value="" size="15"/></td>
                            <td><input type="text" name="urlnew" value="" size="15"/></td>
                            <td><select name="statusnew">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="6"><input type="submit" class="submit" name="submit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
    <?php echo $shadow2 ?>
    </td>
    </tr>
    </table>
    <?php
}

function doPanel()
{
    global $lang, $db, $oToken, $THEME, $shadow2;

    $oToken->assertToken();

    $plugs = $db->query("SELECT * FROM " . X_PREFIX . "pluglinks");
    while (($pluginfo = $db->fetch_array($plugs)) != false) {
        $name = "name" . $pluginfo['id'];
        $name = $db->escape(formVar($name));
        $url = "url" . $pluginfo['id'];
        $url = $db->escape(formVar($url));
        $img = "img" . $pluginfo['id'];
        $img = $db->escape(formVar($img));
        $displayorder = "displayorder" . $pluginfo['id'];
        $displayorder = formInt($displayorder);
        $status = "status" . $pluginfo['id'];
        $status = $db->escape(formVar($status));

        $delete = "delete" . $pluginfo['id'];
        $delete = formInt($delete);
        if ($delete > 0) {
            $db->query("DELETE FROM " . X_PREFIX . "pluglinks WHERE id = '$delete'");
        }

        $db->query("UPDATE " . X_PREFIX . "pluglinks SET
            name = '$name',
            url = '$url',
            img = '$img',
            displayorder = '$displayorder',
            status = '$status'
            WHERE id = '$pluginfo[id]'
        ");
    }
    $db->free_result($plugs);

    $namenew = $db->escape(formVar('namenew'));

    if (!empty($namenew)) {
        $urlnew = $db->escape(formVar('urlnew'));
        $imgnew = $db->escape(formVar('imgnew'));
        $displayordernew = formInt('displayordernew');
        $statusnew = formOnOff('statusnew');
        $db->query("INSERT INTO " . X_PREFIX . "pluglinks (name, url, img, displayorder, status) VALUES ('$namenew', '$urlnew', '$imgnew', '$displayordernew', '$statusnew')");
    }

    cp_message($lang['pluglinkupdate'], false, '', '</td></tr></table>', 'cp_pluglinks.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('submit')) {
    viewPanel();
}

if (onSubmit('submit')) {
    $config_cache->expire('settings');
    $moderators_cache->expire('moderators');
    $config_cache->expire('theme');
    $config_cache->expire('pluglinks');
    $config_cache->expire('whosonline');
    $config_cache->expire('forumjump');

    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
