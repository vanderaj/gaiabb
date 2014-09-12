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

require_once(ROOT.'header.php');
require_once(ROOTINC.'admincp.inc.php');

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
nav($lang['Date_Format_Settings']);
btitle($lang['textcp']);
btitle($lang['Date_Format_Settings']);

eval('$css = "'.template('css').'";');
eval('echo "'.template('cp_header').'";');

if (!X_ADMIN)
{
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel($queryd)
{
    global $THEME, $lang, $shadow2, $oToken, $db;
    global $ubblva, $self;
    ?>
    <form method="post" action="cp_dateformats.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token()?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr class="category">
    <td class="title" align="center"><?php echo $lang['textdeleteques']?></td>
    <td class="title"><?php echo $lang['dateformats']?></td>
    <td class="title"><?php echo $lang['exdateformat']?></td>
    </tr>
    <?php
    while ($dformat = $db->fetch_array($queryd))
    {
        if ($dformat['did'] != 1)
        {
            ?>
            <tr bgcolor="<?php echo $THEME['altbg2']?>">
            <td class="ctrtablerow"><input type="checkbox" name="delete<?php echo $dformat['did']?>" value="<?php echo $dformat['did']?>" /></td>
            <td class="tablerow"><input type="text" size="20" name="find<?php echo $dformat['did']?>" value="<?php echo $dformat['dateformat']?>" /></td>
            <td class="tablerow"><?php echo gmdate(formatDate($dformat['dateformat']), $ubblva + ($self['timeoffset'] * 3600) + $self['daylightsavings'])?></td>
            </tr>
            <?php
        }
    }
    $db->free_result($queryd);
    ?>
    <tr bgcolor="<?php echo $THEME['altbg1']?>" class="ctrtablerow">
    <td colspan="3"><?php echo $lang['textnewcode']?>&nbsp;<input type="text" size="20" name="newfind" value="" /></td>
    </tr>
    <tr bgcolor="<?php echo $THEME['altbg2']?>" class="ctrtablerow">
    <td colspan="3"><input type="submit" class="submit" name="datesubmit" value="<?php echo $lang['textsubmitchanges']?>" />&nbsp;<input type="submit" class="submit" name="daterestore" value="<?php echo $lang['daterestore']?>" /></td>
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

function doPanel($querydate)
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken;

    $oToken->assert_token();

    while ($dformat = $db->fetch_array($querydate))
    {
        $find = "find" . $dformat['did'];
        $find = $db->escape(formVar($find));
        $delete = "delete" . $dformat['did'];
        $delete = formInt($delete);
        if ($delete > 0)
        {
            $db->query("DELETE FROM ".X_PREFIX."dateformats WHERE did='$delete'");
        }
        $db->query("UPDATE ".X_PREFIX."dateformats SET dateformat='$find' WHERE did='$dformat[did]'");
    }
    $db->free_result($querydate);
    if (isset ($newfind) && $newfind != '')
    {
        $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('$newfind')");
    }
    cp_message($lang['updatedates'], false, '', '</td></tr></table>', 'cp_dateformats.php', true, false, true);
}

function dateRestoreTable()
{
    global $db, $lang;

    $db->query("DROP TABLE IF EXISTS ".X_PREFIX."dateformats");
    $db->query("CREATE TABLE ".X_PREFIX."dateformats (`dateformat` varchar(10) NOT NULL default '', `did` int(3) NOT NULL auto_increment, PRIMARY KEY (`did`)) TYPE=MyISAM");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('dd-mm-yy');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('dd-mm-yyyy');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('mm-dd-yy');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('mm-dd-yyyy');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('dd/mm/yy');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('dd/mm/yyyy');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('mm/dd/yy');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('mm/dd/yyyy');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('F d, Y');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('M d, Y');");
    $db->query("INSERT INTO ".X_PREFIX."dateformats (`dateformat`) VALUES ('d F Y');");
    cp_message($lang['restoredates'], false, '', '</td></tr></table>', 'cp_dateformats.php', true, false, true);
}

function viewDateRestore()
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken;
    ?>
    <form method="post" action="cp_dateformats.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token()?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr class="category">
    <td class="title"><?php echo $lang['dateformats']?></td>
    </tr>
    <tr bgcolor="<?php echo $THEME['altbg2']?>" class="ctrtablerow">
    <td><?php echo $lang['daterestoreconfirm']?><br /><input type="submit" class="submit" name="daterestoresubmit" value="<?php echo $lang['textyes']?>" />&nbsp;-&nbsp;<input type="submit" class="submit" name="no" value="<?php echo $lang['textno']?>" /></td>
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

displayAdminPanel();

// Ensure the database is okay
$tables = $db->getTables();
$tablename = X_PREFIX . "dateformats";
if (!in_array($tablename, $tables))
{
    dateRestoreTable();
}

$query = $db->query("SELECT * FROM ".X_PREFIX."dateformats");
if ($db->num_rows($query) < 1)
{
    dateRestoreTable();
}

if (noSubmit('datesubmit') && noSubmit('daterestore') && noSubmit('daterestoresubmit'))
{
    viewPanel($query);
}
if (onSubmit('datesubmit'))
{
    doPanel($query);
}
if (onSubmit('daterestore'))
{
    viewDateRestore();
}
if (onSubmit('daterestoresubmit'))
{
    $oToken->assert_token();
    dateRestoreTable();
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>