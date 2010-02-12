<?php
/**
 * GaiaBB
 * Copyright (c) 2010 The GaiaBB Group
 * http://www.GaiaBB.com
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

define('DEBUG_REG', true);
define('ROOT', '../');
define('ROOTINC', '../include/');
define('ROOTCLASS', '../class/');

require_once(ROOT.'header.php');
require_once(ROOTINC.'admincp.inc.php');
require_once(ROOTINC.'settings.inc.php');

loadtpl(
'cp_header',
'cp_footer',
'cp_message',
'cp_error'
);

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">'.$lang['textcp'].'</a>');
nav($lang['captcha_settings']);
btitle($lang['textcp']);
btitle($lang['captcha_settings']);

eval('$css = "'.template('css').'";');
eval('echo "'.template('cp_header').'";');

if (!X_ADMIN)
{
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
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function viewPanel()
{
    global $CONFIG, $lang, $THEME, $shadow2, $oToken;

    $captcha_on = $captcha_off = '';
    settingHTML('captcha_status', $captcha_on, $captcha_off);

    $colmanage[0] = $colmanage[1] = false;
    switch($CONFIG['captcha_colortype'])
    {
        case 'single':
            $colmanage[0] = true;
            break;
        case 'multiple':
            $colmanage[1] = true;
            break;
    }

    $CONFIG['captcha_fontpath'] = stripslashes($CONFIG['captcha_fontpath']);
    ?>
    <form method="post" action="cp_captcha.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token()?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr class="category">
    <td colspan="2" class="title"><?php echo $lang['captcha_settings']?></td>
    </tr>
    <?php
    printsetting1($lang['text_captcha_status'], 'new_status', $captcha_on, $captcha_off);
    printsetting2($lang['text_captcha_attempts_max'], 'new_maxattempts', $CONFIG['captcha_maxattempts'], 2);
    printsetting2($lang['text_captcha_chars_min'], "new_minchars", $CONFIG['captcha_minchars'], 2);
    printsetting2($lang['text_captcha_chars_max'], "new_maxchars", $CONFIG['captcha_maxchars'], 2);
    printsetting3($lang['text_captcha_color_type'], 'new_colortype', array($lang['text_captcha_color_type_single'], $lang['text_captcha_color_type_multiple']), array('single', 'multiple'), $colmanage, false);
    printsetting2($lang['text_captcha_font_path'], 'new_fontpath', $CONFIG['captcha_fontpath'], 40);
    ?>
    <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2']?>">
    <td colspan="2"><input class="submit" type="submit" name="captchasubmit" value="<?php echo $lang['textsubmitchanges']?>" /></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    <?php echo $shadow2?>
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
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function doPanel()
{
    global $lang, $db, $THEME, $oToken;

    $oToken->assert_token();

    $new_status = $db->escape(formVar('new_status'));
    $new_maxattempts = $db->escape(formInt('new_maxattempts'));
    $new_minchars = $db->escape(formInt('new_minchars'));
    if ($new_minchars < 4)
    {
        $new_minchars = 4;
    }
    $new_maxchars = $db->escape(formInt('new_maxchars'));
    if ($new_maxchars > 8)
    {
        $new_maxchars = 8;
    }
    $new_colortype = $db->escape(formVar('new_colortype'));
    $new_fontpath = $db->escape(formVar('new_fontpath'));
    if (empty($new_fontpath))
    {
        $new_fontpath = './include/captcha';
    }

    $config_array = array (
        'captcha_status' => $new_status,
        'captcha_maxattempts' => $new_maxattempts,
        'captcha_minchars' => $new_minchars,
        'captcha_maxchars' => $new_maxchars,
        'captcha_colortype' => $new_colortype,
        'captcha_fontpath' => $new_fontpath
    );

    // execute query
    foreach ($config_array as $key => $value)
    {
        $db->query("UPDATE ".X_PREFIX."settings SET config_value = '$value' WHERE config_name = '$key' LIMIT 1");
    }

    cp_message($lang['textsettingsupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('captchasubmit'))
{
    viewPanel();
}

if (onSubmit('captchasubmit'))
{
    doPanel();
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>