<?php

/**
 * GaiaBB
 * Copyright (c) 2011-2025 The GaiaBB Group
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
    'cp_error',
    'functions_bbcode',
    'functions_bbcodeinsert'
);

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['indexnewscp']);
btitle($lang['textcp']);
btitle($lang['indexnewscp']);

eval('$css = "' . template('css') . '";');
if ($bbcode_js != '') {
    $bbcode_js_sc = 'bbcodefns-' . $bbcode_js . '.js';
} else {
    $bbcode_js_sc = 'bbcodefns.js';
}

eval('$bbcodescript = "' . template('functions_bbcode') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $shadow2, $lang, $db, $THEME, $oToken, $CONFIG;

    $bbcodeinsert = bbcodeinsert();

    $indexnewson = $indexnewsoff = '';
    settingHTML('indexnews', $indexnewson, $indexnewsoff);

    $CONFIG['indexnewstxt'] = stripslashes($CONFIG['indexnewstxt']);
    ?>
    <form method="post" action="cp_news.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
    <tr class="category">
    <td class="title" colspan="2"><?php echo $lang['indexnewscp'] ?></td>
    </tr>
    <?php
    printsetting1($lang['set_indexnews'], 'indexnewsnew', $indexnewson, $indexnewsoff);
    echo $bbcodeinsert;
    ?>
    <tr class="tablerow">
    <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top" width="50%"><?php echo $lang['set_indexnewstxt'] ?></td>
    <td bgcolor="<?php echo $THEME['altbg2'] ?>" width="50%"><textarea rows="12" name="indexnewstxtnew" cols="60" id="message" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo $CONFIG['indexnewstxt'] ?></textarea></td>
    </tr>
    <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
    <td colspan="2"><input class="submit" type="submit" name="newssubmit" value="<?php echo $lang['textsubmitchanges'] ?>" /></td>
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
    global $lang, $db, $oToken;

    $oToken->assert_token();

    $indexnewsnew = formOnOff('indexnewsnew');
    $indexnewstxtnew = $db->escape(formVar('indexnewstxtnew'));

    $config_array = array(
        'indexnews' => $indexnewsnew,
        'indexnewstxt' => $indexnewstxtnew,
    );

    // execute query
    foreach ($config_array as $key => $value) {
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$value' WHERE config_name = '$key' LIMIT 1");
    }

    cp_message($lang['textsettingsupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

$config_cache->expire('settings');
$moderators_cache->expire('moderators');
$config_cache->expire('theme');
$config_cache->expire('pluglinks');
$config_cache->expire('whosonline');
$config_cache->expire('forumjump');

if (noSubmit('newssubmit')) {
    viewPanel();
}

if (onSubmit('newssubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>