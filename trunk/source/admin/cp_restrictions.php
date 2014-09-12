<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2014 The GaiaBB Group
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

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['cprestricted']);

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
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML;
    ?>
    <form method="post" action="cp_restrictions.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token()?>" />
    <table align="center" border="0px" cellspacing="0px" cellpadding="0px" width="100%">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr class="category">
    <td class="title" align="center"><?php echo $lang['textdeleteques']?></td>
    <td class="title" align="center"><?php echo $lang['restrictedname']?></td>
    <td class="title" align="center"><?php echo $lang['restrictcasesensitive']?></td>
    <td class="title" align="center"><?php echo $lang['restrictpartialmatch']?></td>
    </tr>
    <?php
    $query = $db->query("SELECT * FROM " . X_PREFIX . "restricted ORDER BY id");
    $rowsFound = $db->num_rows($query);
    while ($restricted = $db->fetch_array($query))
    {

        $case_check = $partial_check = '';
        if ($restricted['case_sensitivity'] == 1)
        {
            $case_check = $cheHTML;
        }

        if ($restricted['partial'] == 1)
        {
            $partial_check = $cheHTML;
        }
        $restricted['name'] = htmlspecialchars($restricted['name']);
        ?>
        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2']?>">
        <td>
        <?php
        formHelper::formCheckBox("delete" . $restricted['id'], $restricted['id'], '', '');
        ?>
        </td>
        <td>
        <input type="text" size="30" name="name<?php echo $restricted['id']?>" value="<?php echo $restricted['name']?>" />
        </td>
        <td>
        <?php
        formHelper::formCheckBox("case" . $restricted['id'], 'on', $case_check, '');
        ?>
        </td>
        <td>
        <?php
        formHelper::formCheckBox("partial" . $restricted['id'], 'on', $partial_check, '');
        ?>
        </td>
        </tr>
        <?php
    }
    $db->free_result($query);
    if ($rowsFound < 1)
    {
        ?>
        <tr bgcolor="<?php echo $THEME['altbg1']?>" class="ctrtablerow">
        <td colspan="4"><?php echo $lang['pluglinknone']?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
    <td bgcolor="<?php echo $THEME['altbg2']?>" colspan="4"><span class="smalltxt"><?php echo $lang['newrestrictionwhy']?><br /><?php echo $lang['newrestriction']?></span></td>
    </tr>
    <tr class="category">
    <td class="title" colspan="4"><?php echo $lang['textnewcode']?></td>
    </tr>
    <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2']?>">
    <td>&nbsp;</td>
    <td><input type="text" size="30" name="newname" value="" /></td>
    <td>
    <?php
    formHelper::formCheckBox('newcase', 'on', $cheHTML, '');
    ?>
    </td>
    <td>
    <?php
    formHelper::formCheckBox('newpartial', 'on', $cheHTML, '');
    ?>
    </td>
    </tr>
    <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2']?>">
    <td colspan="4">
    <input class="submit" type="submit" name="restrictedsubmit" value="<?php echo $lang['textsubmitchanges']?>" /></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    </form>
    <?php echo $shadow2?>
    </td>
    </tr>
    </table>
    <?php
}

function doPanel()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG, $onlinetime;

    $oToken->assert_token();

    $queryrestricted = $db->query("SELECT id FROM " . X_PREFIX . "restricted");
    while ($restricted = $db->fetch_array($queryrestricted))
    {
        $name = $delete = $case = $partial = '';

        $name = $db->escape(formVar('name' . $restricted['id']));

        $partial = formOnOff('partial' . $restricted['id']);
        $partial = ($partial == "on") ? 1 : 0;

        $case = formOnOff('case' . $restricted['id']);
        $case = ($case == "on") ? 1 : 0;

        $delete = formInt('delete' . $restricted['id']);
        if ($delete > 0)
        {
            $db->query("DELETE FROM " . X_PREFIX . "restricted WHERE id = '$delete'");
            continue;
        }
        $db->query("UPDATE `" . X_PREFIX . "restricted` SET `name` = '$name', `case_sensitivity` = '$case', `partial` = '$partial' WHERE `id` = '$restricted[id]'");
    }
    $db->free_result($queryrestricted);

    $newname = $db->escape(formVar('newname'));
    if (!empty ($newname))
    {
        $newpartial = formOnOff('newpartial');
        $newpartial = ($newpartial == "on") ? 1 : 0;
        $newcase = formOnOff('newcase');
        $newcase = ($newcase == "on") ? 1 : 0;
        $db->query("INSERT INTO " . X_PREFIX . "restricted (`name`, `case_sensitivity`, `partial`) VALUES ('$newname', '$newcase', '$newpartial')");
    }
    cp_message($lang['restrictedupdate'], false, '', '</td></tr></table>', 'cp_restrictions.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('restrictedsubmit'))
{
    viewPanel();
}

if (onSubmit('restrictedsubmit'))
{
    doPanel();
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>
