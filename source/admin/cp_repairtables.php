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

eval('$css = "' . template('css') . '";');

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['repair']);
btitle($lang['textcp']);
btitle($lang['repair']);

eval('echo "' . template('cp_header') . '";');

if (!X_SADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['superadminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML;

    ?>
    <form method="post" action="cp_repairtables.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
    <tr class="category">
    <td colspan="2" class="title"><?php echo $lang['repair'] ?></td>
    </tr>
    <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg1'] ?>">
    <td colspan="2"><?php echo $lang['confirm_repair'] ?></td>
    </tr>
    <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
    <td colspan="2"><input class="submit" type="submit" name="yessubmit" value="<?php echo $lang['textyes'] ?>" />&nbsp;-&nbsp;<input class="submit" type="submit" name="nosubmit" value="<?php echo $lang['textno'] ?>" /></td>
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

    $start = true;
    set_time_limit(180);
    $tables = $db->getTables();
    $q = array();
    ?>
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
    <?php
foreach ($tables as $key => $val) {
        if ($start) {
            dump_query($db->query('REPAIR TABLE `' . $val . '`'));
            $start = false;
        } else {
            dump_query($db->query('REPAIR TABLE `' . $val . '`'), false);
        }
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

if (noSubmit('yessubmit') && noSubmit('nosubmit')) {
    viewPanel();
}

if (onSubmit('yessubmit') && noSubmit('nosubmit')) {
    doPanel();
}

if (onSubmit('nosubmit') && noSubmit('yessubmit')) {
    redirect('index.php', 0);
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
