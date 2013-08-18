<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2013 The GaiaBB Group
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

define('DEBUG_REG',true);
define('ROOT', '../');
define('ROOTINC', '../include/');
define('ROOTCLASS', '../class/');
define('ROOTHELPER', '../helper/');

require_once(ROOT.'header.php');
require_once(ROOTINC.'admincp.inc.php');
require_once(ROOTHELPER.'formHelper.php');

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
nav($lang['Smtp_Settings']);

eval('$css = "'.template('css').'";');

eval('echo "'.template('cp_header').'";');

if (!X_ADMIN)
{
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken, $CONFIG, $cheHTML, $selHTML;

    $smtp_statuson = $smtp_statusoff = '';
    formHelper::getSettingOnOffHtml('smtp_status', $smtp_statuson, $smtp_statusoff);

    $CONFIG['smtpServer'] = stripslashes($CONFIG['smtpServer']);
    $CONFIG['smtpusername'] = stripslashes($CONFIG['smtpusername']);
    $CONFIG['smtppassword'] = stripslashes($CONFIG['smtppassword']);
    $CONFIG['smtphost'] = stripslashes($CONFIG['smtphost']);
    $CONFIG['smtpport'] = (int) ($CONFIG['smtpport']);
    $CONFIG['smtptimeout'] = (int) ($CONFIG['smtptimeout']);
    ?>
    <form method="post" action="cp_smtp.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token()?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr>
    <td colspan="2" class="category"><strong><font color="<?php echo $THEME['cattext']?>"><?php echo $lang['Smtp_Settings']?></font></strong></td>
    </tr>
    <?php
    formHelper::formSelectOnOff($lang['Smtp_Status'], 'smtp_statusnew', $smtp_statuson, $smtp_statusoff);
    formHelper::formTextBox($lang['Smtp_Server'], 'smtpServernew', $CONFIG['smtpServer'], 50);
    formHelper::formTextBox($lang['Smtp_Port_Number'], 'smtpportnew', $CONFIG['smtpport'], 4);
    formHelper::formTextBox($lang['Smtp_Timeout'], 'smtptimeoutnew', $CONFIG['smtptimeout'], 3);
    formHelper::formTextBox($lang['Smtp_Username'], 'smtpusernamenew', $CONFIG['smtpusername'], 50);
    formHelper::formTextPassBox($lang['Smtp_Password'], 'smtppasswordnew', $CONFIG['smtppassword'], 50, true);
    formHelper::formTextBox($lang['Smtp_Host'], 'smtphostnew', $CONFIG['smtphost'], 50);
    ?>
    <tr>
    <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2']?>" colspan="2">
    <input class="submit" type="submit" name="smtpsubmit" value="<?php echo $lang['textsubmitchanges']?>" /></td>
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

function doPanel()
{
    global $shadow2, $lang, $db, $THEME, $oToken;

    $oToken->assert_token();

    $smtpServernew = $db->escape(formVar('smtpServernew'));
    $smtpusernamenew = $db->escape(formVar('smtpusernamenew'));
    $smtppasswordnew = $db->escape(formVar('smtppasswordnew'));
    $smtphostnew = $db->escape(formVar('smtphostnew'));
    $smtpportnew = formInt('smtpportnew');
    $smtptimeoutnew = formInt('smtptimeoutnew');
    $smtp_statusnew = formOnOff('smtp_statusnew');

    if ($smtpportnew < 1)
    {
        $smtpportnew = 25;
    }

    if ($smtptimeoutnew < 1)
    {
        $smtptimeoutnew = 30;
    }

    $config_array = array (
        'smtpServer' => $smtpServernew,
        'smtpport' => $smtpportnew,
        'smtptimeout' => $smtptimeoutnew,
        'smtpusername' => $smtpusernamenew,
        'smtppassword' => $smtppasswordnew,
        'smtphost' => $smtphostnew,
        'smtp_status' => $smtp_statusnew
    );

    // execute query
    foreach ($config_array as $key => $value)
    {
        $db->query("UPDATE ".X_PREFIX."settings SET config_value = '$value' WHERE config_name = '$key' LIMIT 1");
    }

    cp_message($lang['textsettingsupdate'], false, '', '</td></tr></table>', 'cp_smtp.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('smtpsubmit'))
{
    viewPanel();
}

if (onSubmit('smtpsubmit')) {
    doPanel();
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>