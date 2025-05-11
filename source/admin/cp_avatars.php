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
require_once ROOTINC . 'settings.inc.php';

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
nav($lang['Avatar_Settings']);
btitle($lang['textcp']);
btitle($lang['Avatar_Settings']);

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
 * Long description of function
 *
 * @param  $varname type, what it does
 * @return type, what the return does
 */
function viewPanel()
{
    global $CONFIG, $lang, $THEME, $shadow2, $oToken;

    $avataron = $avataroff = '';
    settingHTML('avastatus', $avataron, $avataroff);

    $avuchecked[0] = $avuchecked[1] = $avuchecked[2] = false;
    switch ($CONFIG['avatar_whocanupload']) {
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

    $avatars_status_on = $avatars_status_off = '';
    settingHTML('avatars_status', $avatars_status_on, $avatars_status_off);

    $max_avatar_sizes = explode('x', $CONFIG['max_avatar_size']);
    $CONFIG['avatar_path'] = stripslashes($CONFIG['avatar_path']);
    $CONFIG['avgalpath'] = stripslashes($CONFIG['avgalpath']);
    ?>
    <form method="post" action="cp_avatars.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
    <tr class="category">
    <td colspan="2" class="title"><?php echo $lang['Avatar_Settings'] ?></td>
    </tr>
    <?php
    if (!is_writable($CONFIG['avatar_path'])) {
        error($lang['avatar_nowrite'], false);
    }
    printsetting1($lang['textavastatus'], 'avastatusnew', $avataron, $avataroff);
    printsetting1($lang['avatars_status'], 'avatars_statusnew', $avatars_status_on, $avatars_status_off);
    printsetting2($lang['avatarsperpage'], 'avatars_perpagenew', $CONFIG['avatars_perpage'], 2);
    printsetting2($lang['avatarsperrow'], "avatars_perrownew", $CONFIG['avatars_perrow'], 2);
    printsetting3($lang['Avatar_Whoupload'], 'avatar_whocanuploadnew', array($lang['textoff'], $lang['Avatar_Upall'], $lang['Avatar_Upstaff']), array('off', 'all', 'staff'), $avuchecked, false);
    printsetting2($lang['Avatar_Filesize'], 'avatar_filesizenew', $CONFIG['avatar_filesize'], 5);
    printsetting2($lang['Avatar_Wdimensions'], 'avatar_max_widthnew', $CONFIG['avatar_max_width'], 4);
    printsetting2($lang['Avatar_Hdimensions'], 'avatar_max_heightnew', $CONFIG['avatar_max_height'], 4);
    printsetting2($lang['Avatar_Newwresize'], 'avatar_new_widthnew', $CONFIG['avatar_new_width'], 4);
    printsetting2($lang['Avatar_Newhresize'], 'avatar_new_heightnew', $CONFIG['avatar_new_height'], 4);
    printsetting2($lang['Avatar_Path'], 'avatar_pathnew', $CONFIG['avatar_path'], 20);
    printsetting2($lang['avgalpath'], 'avgalpathnew', $CONFIG['avgalpath'], 20);
    printsetting2($lang['max_avatar_size_w'], 'max_avatar_size_w_new', $max_avatar_sizes[0], 4);
    printsetting2($lang['max_avatar_size_h'], 'max_avatar_size_h_new', $max_avatar_sizes[1], 4);
    ?>
    <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
    <td colspan="2"><input class="submit" type="submit" name="avatarsubmit" value="<?php echo $lang['textsubmitchanges'] ?>" /></td>
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
 * Long description of function
 *
 * @param  $varname type, what it does
 * @return type, what the return does
 */
function doPanel()
{
    global $lang, $db, $THEME, $oToken;

    $oToken->assert_token();

    $avastatusnew = formOnOff('avastatusnew');
    $avatars_statusnew = formOnOff('avatars_statusnew');

    $avatar_whocanuploadnew = formVar('avatar_whocanuploadnew');
    switch ($avatar_whocanuploadnew) {
        case 'all':
        case 'off':
        case 'staff':
            // positive validation = no action needs to be taken
            break;
        default:
            // all other choices, make a safe choice
            $avatar_whocanuploadnew = 'off';
    }

    $max_remote_w_new = formInt('max_avatar_size_w_new');
    $max_remote_h_new = formInt('max_avatar_size_h_new');
    $max_upload_w_new = formInt('avatar_max_widthnew');
    $max_upload_h_new = formInt('avatar_max_heightnew');
    $avatar_new_widthnew = formInt('avatar_new_widthnew');
    $avatar_new_heightnew = formInt('avatar_new_heightnew');
    $avatars_perpagenew = formInt('avatars_perpagenew');
    $avatars_perrownew = formInt('avatars_perrownew');
    $avatar_pathnew = $db->escape(formVar('avatar_pathnew')); // TODO make path safe
    $avgalpathnew = $db->escape(formVar('avgalpathnew')); // TODO make path safe
    $avatar_filesizenew = formInt('avatar_filesizenew');
    $max_avatar_size = $max_remote_w_new . 'x' . $max_remote_h_new;

    $config_array = array(
        'avastatus' => $avastatusnew,
        'max_avatar_size' => $max_avatar_size,
        'avatar_whocanupload' => $avatar_whocanuploadnew,
        'avatar_filesize' => $avatar_filesizenew,
        'avatar_max_width' => $max_upload_w_new,
        'avatar_max_height' => $max_upload_h_new,
        'avatar_path' => $avatar_pathnew,
        'avatar_new_width' => $avatar_new_widthnew,
        'avatar_new_height' => $avatar_new_heightnew,
        'avatars_status' => $avatars_statusnew,
        'avatars_perpage' => $avatars_perpagenew,
        'avatars_perrow' => $avatars_perrownew,
        'avgalpath' => $avgalpathnew,
    );

    // execute query
    foreach ($config_array as $key => $value) {
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$value' WHERE config_name = '$key' LIMIT 1");
    }

    cp_message($lang['textsettingsupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('avatarsubmit')) {
    viewPanel();
}

if (onSubmit('avatarsubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>