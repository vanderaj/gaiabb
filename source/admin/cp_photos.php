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
define('ROOTHELPER', '../helper/');

require_once '../header.php';
require_once '../include/admincp.inc.php';
require_once '../helper/FormHelper.php';

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['photo_main_settings']);
btitle($lang['textcp']);
btitle($lang['photo_main_settings']);

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
    global $shadow2, $lang, $db, $THEME, $oToken, $CONFIG, $cheHTML, $selHTML;

    $photoon = $photooff = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('photostatus', $photoon, $photooff);

    $avuchecked[0] = $avuchecked[1] = $avuchecked[2] = false;
    switch ($CONFIG['photo_whocanupload']) {
        case 'off':
            $avuchecked[0] = true;
            break;
        case 'all':
            $avuchecked[1] = true;
            break;
        default:
            $avuchecked[2] = true;
            break;
    }

    $max_photo_sizes = explode('x', $CONFIG['max_photo_size']);
    $CONFIG['photo_path'] = stripslashes($CONFIG['photo_path']);
    ?>
    <form method="post" action="cp_photos.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td colspan="2" class="title"><?php echo $lang['photo_main_settings'] ?></td>
                        </tr>
                        <?php
                        GaiaBB\FormHelper::formSelectOnOff($lang['photostatus'], 'photostatusnew', $photoon, $photooff);
                        GaiaBB\FormHelper::formSelectList($lang['photo_Whoupload'], 'photo_whocanuploadnew', array(
                                $lang['textoff'],
                                $lang['photo_Upall'],
                                $lang['photo_Upstaff'],
                            ), array(
                                'off',
                                'all',
                                'staff',
                            ), $avuchecked, false);
                            GaiaBB\FormHelper::formTextBox($lang['photo_Filesize'], 'photo_filesizenew', $CONFIG['photo_filesize'], 5);
                            GaiaBB\FormHelper::formTextBox($lang['photo_Wdimensions'], 'photo_max_widthnew', $CONFIG['photo_max_width'], 4);
                            GaiaBB\FormHelper::formTextBox($lang['photo_Hdimensions'], 'photo_max_heightnew', $CONFIG['photo_max_height'], 4);
                            GaiaBB\FormHelper::formTextBox($lang['photo_Newwresize'], 'photo_new_widthnew', $CONFIG['photo_new_width'], 4);
                            GaiaBB\FormHelper::formTextBox($lang['photo_Newhresize'], 'photo_new_heightnew', $CONFIG['photo_new_height'], 4);
                            GaiaBB\FormHelper::formTextBox($lang['photo_Path'], 'photo_pathnew', $CONFIG['photo_path'], 20);
                            GaiaBB\FormHelper::formTextBox($lang['max_photo_size_w'], 'max_photo_size_w_new', $max_photo_sizes[0], 4);
                            GaiaBB\FormHelper::formTextBox($lang['max_photo_size_h'], 'max_photo_size_h_new', $max_photo_sizes[1], 4);
                        ?>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input class="submit" type="submit"
                                                   name="photosubmit"
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

    $oToken->assertToken();

    $photostatusnew = formOnOff('photostatusnew');
    $photo_whocanuploadnew = formVar('photo_whocanuploadnew');
    $photo_whocanuploadnew = ($photo_whocanuploadnew == 'off') ? 'off' : ($photo_whocanuploadnew == 'all' ? 'all' : 'staff');

    $max_remote_w_new = formInt('max_photo_size_w_new');
    $max_remote_h_new = formInt('max_photo_size_h_new');

    $photo_upload_w_new = formInt('photo_max_widthnew');
    $photo_upload_h_new = formInt('photo_max_heightnew');

    $photo_pathnew = $db->escape(formVar('photo_pathnew')); // TODO make path safe
    $photo_filesizenew = formInt('photo_filesizenew');

    $photo_new_widthnew = formInt('photo_new_widthnew');
    $photo_new_heightnew = formInt('photo_new_heightnew');

    $max_photo_size = $max_remote_w_new . 'x' . $max_remote_h_new;

    $config_array = array(
        'photostatus' => $photostatusnew,
        'max_photo_size' => $max_photo_size,
        'photo_whocanupload' => $photo_whocanuploadnew,
        'photo_filesize' => $photo_filesizenew,
        'photo_max_width' => $photo_upload_w_new,
        'photo_max_height' => $photo_upload_h_new,
        'photo_path' => $photo_pathnew,
        'photo_new_width' => $photo_new_widthnew,
        'photo_new_height' => $photo_new_heightnew,
    );

    // execute query
    foreach ($config_array as $key => $value) {
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$value' WHERE config_name = '$key' LIMIT 1");
    }

    cp_message($lang['textsettingsupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('photosubmit')) {
    viewPanel();
}

if (onSubmit('photosubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>