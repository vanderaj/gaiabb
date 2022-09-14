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
require_once ROOTINC . 'settings.inc.php';

loadtpl(
    'cp_header',
    'cp_footer',
    'cp_message',
    'cp_error',
    'functions_bbcode',
    'functions_bbcodeinsert'
);

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['textfaq'] . ' ' . $lang['textbbrules']);
btitle($lang['textcp']);
btitle($lang['textfaq'] . ' ' . $lang['textbbrules']);

eval('$css = "' . template('css') . '";');
if ($bbcode_js != '') {
    $bbcode_js_sc = 'bbcodefns-' . $bbcode_js . '.js';
} else {
    $bbcode_js_sc = 'bbcodefns.js';
}
eval('$bbcodescript = "' . template('functions_bbcode') . '";');
$bbcodeinsert = bbcodeinsert();
eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $shadow2, $lang, $db, $THEME, $oToken, $CONFIG, $cheHTML, $selHTML, $bbcodeinsert;
    ?>
    <form method="post" action="cp_rules.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
    <tr class="category">
    <td class="title"><?php echo $lang['Rules_Management_System'] ?></td>
    </tr>
    <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
    <td><?php echo $lang['rules_A'] ?></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    <?php echo $shadow2 ?>
    <br />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
    <tr class="category">
    <td colspan="2" class="title"><?php echo $lang['textbbrules'] ?></td>
    </tr>
    <?php
$ruleson = $rulesoff = '';
    settingHTML('bbrules', $ruleson, $rulesoff);

    $queryg = $db->query("SELECT * FROM " . X_PREFIX . "faq WHERE type = 'rulesset'");
    $frules = $db->fetch_array($queryg);
    $db->free_result($queryg);

    if ($frules['allowsmilies'] == 'yes') {
        $checked1 = $cheHTML;
    } else if ($frules['allowsmilies'] == 'no') {
        $checked1 = '';
    }

    if ($frules['allowbbcode'] == 'yes') {
        $checked2 = $cheHTML;
    } else if ($frules['allowbbcode'] == 'no') {
        $checked2 = '';
    }

    if ($frules['allowimgcode'] == 'yes') {
        $checked3 = $cheHTML;
    } else if ($frules['allowimgcode'] == 'no') {
        $checked3 = '';
    }

    $nameo = stripslashes($frules['name']);
    $CONFIG['bbrulestxt'] = stripslashes($CONFIG['bbrulestxt']);

    printsetting1($lang['board_rules_status'], 'bbrulesnew', $ruleson, $rulesoff);
    echo $bbcodeinsert;
    ?>
    <tr class="tablerow">
    <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top" width="50%"><?php echo $lang['textbbrulestxt'] ?></td>
    <td bgcolor="<?php echo $THEME['altbg2'] ?>" width="50%"><textarea rows="12" name="bbrulestxtnew" cols="50" id="message" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo $CONFIG['bbrulestxt'] ?></textarea></td>
    </tr>
    <tr class="tablerow">
    <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top"><?php echo $lang['textallow'] ?></td>
    <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="smalltxt">
    <input type="checkbox" name="allowsmiliesnew" value="yes" <?php echo $checked1 ?> /><?php echo $lang['textsmilies'] ?><br />
    <input type="checkbox" name="allowbbcodenew" value="yes" <?php echo $checked3 ?> /><?php echo $lang['textbbcode'] ?><br />
    <input type="checkbox" name="allowimgcodenew" value="yes" <?php echo $checked3 ?> /><?php echo $lang['textimgcode'] ?><br />
    </td>
    </tr>
    <tr class="tablerow">
    <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top"><?php echo $lang['rules_C'] ?></td>
    <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="checkbox" name="allmembers" value="on" /></td>
    </tr>
    <tr class="tablerow">
    <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top"><?php echo $lang['rules_D'] ?></td>
    <td bgcolor="<?php echo $THEME['altbg2'] ?>"><textarea rows="5" cols="50" name="namenew"><?php echo $nameo ?></textarea></td>
    </tr>
    <tr>
    <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow" colspan="2"><input class="submit" type="submit" name="submit" value="<?php echo $lang['textsubmitchanges'] ?>" /></td>
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
    global $shadow2, $lang, $db, $THEME;
    global $oToken, $config_cache, $moderators_cache;

    $oToken->assert_token();

    $config_cache->expire('settings');
    $moderators_cache->expire('moderators');
    $config_cache->expire('theme');
    $config_cache->expire('pluglinks');
    $config_cache->expire('whosonline');
    $config_cache->expire('forumjump');

    $allowsmiliesnew = formYesNo('allowsmiliesnew');
    $allowbbcodenew = formYesNo('allowbbcodenew');
    $allowimgcodenew = formYesNo('allowimgcodenew');
    $namenew = $db->escape(formVar('namenew'));
    $bbrulesnew = formOnOff('bbrulesnew');
    $bbrulestxtnew = $db->escape(formVar('bbrulestxtnew'));

    $db->query("UPDATE " . X_PREFIX . "faq SET
        name = '$namenew',
        allowsmilies = '$allowsmiliesnew',
        allowbbcode = '$allowbbcodenew',
        allowimgcode = '$allowimgcodenew'
        WHERE type = 'rulesset'
    ");

    $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$bbrulesnew' WHERE config_name = 'bbrules' LIMIT 1");
    $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$bbrulestxtnew' WHERE config_name = 'bbrulestxt' LIMIT 1");

    $allmembers = formOnOff('allmembers');
    if ($allmembers == 'on') {
        $db->query("UPDATE " . X_PREFIX . "members SET readrules = 'yes' WHERE status = 'Member'");
    }

    cp_message($lang['rules_B'], false, '', '</td></tr></table>', 'cp_rules.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('submit')) {
    viewPanel();
}

if (onSubmit('submit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>