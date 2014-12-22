<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2015 The GaiaBB Group
 * http://www.GaiaBB.com
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group 
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
 * http://forums.xmbforum2.com/
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

require_once ('../header.php');
require_once ('../include/admincp.inc.php');

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['dbinfo']);
btitle($lang['textcp']);
btitle($lang['dbinfo']);

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (! X_SADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['superadminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $dbname;
    
    $tables = $db->getTables();
    if (empty($tables)) {
        cp_error($lang['listfieldserror'], false, '', '</td></tr></table>');
    }
    $count = 1;
    $showtables = '';
    foreach ($tables as $tablename) {
        $showtables .= '<tr><td class="ctrtablerow" bgcolor="' . $THEME['altbg1'] . '" width="10%"><strong>' . $count . '</strong></td><td class="tablerow" bgcolor="' . $THEME['altbg2'] . '"><a href="cp_dbinfo.php?list=fields&amp;tablename=' . $tablename . '&amp;token=' . $oToken->get_new_token() . '">' . $tablename . '</a></td></tr>';
        $showtables .= "\n";
        $count ++;
    }
    ?>
<table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
	align="center">
	<tr>
		<td bgcolor="<?php echo $THEME['bordercolor']?>">
			<table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>"
				cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
				<tr class="category">
					<td class="title" colspan="2"><?php echo $lang['tableinformation']?> For <?php echo $dbname?></td>
				</tr>
				<tr class="tablerow">
					<td bgcolor="<?php echo $THEME['altbg1']?>" colspan="2"><?php echo $lang['tabledir']?></td>
				</tr>
				<tr bgcolor="<?php echo $THEME['altbg1']?>">
					<td class="ctrtablerow"><strong><?php echo $lang['tablenumber']?></strong></td>
					<td class="tablerow"><strong><?php echo $lang['tablename']?></strong></td>
				</tr>
    <?php echo $showtables?>
    </table>
		</td>
	</tr>
</table>
<?php echo $shadow2?>
</td>
</tr>
</table>
<?php
}

function listFields()
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken;
    
    $oToken->assert_token();
    
    $tablename = stripslashes(getRequestVar('tablename'));
    
    $result = $db->query("SELECT * FROM $tablename");
    $columns = $db->num_fields($result);
    $records = $db->num_rows($result);
    $table = $db->field_table($result, 0);
    $count = 1;
    $comma = '';
    if (! $table) {
        cp_error($lang['listfieldserror'], false, '', '</td></tr></table>');
    }
    $showfields = '';
    for ($i = 0; $i < $columns; $i ++) {
        $type = $db->field_type($result, $i);
        $name = $db->field_name($result, $i);
        $length = $db->field_len($result, $i);
        $flags = $db->field_flags($result, $i);
        $comma = ', ';
        
        $showfields .= '<tr><td class="ctrtablerow" bgcolor="' . $THEME['altbg1'] . '" width="10%"><strong>' . $count . '</strong></td><td class="tablerow" bgcolor="' . $THEME['altbg2'] . '">' . $name . '</td><td class="tablerow" bgcolor="' . $THEME['altbg1'] . '">' . $type . '</td><td class="tablerow" bgcolor="' . $THEME['altbg2'] . '">' . $length . '</td><td class="tablerow" bgcolor="' . $THEME['altbg1'] . '">' . $flags . '</td></tr>';
        $showfields .= "\n";
        $count ++;
    }
    $db->free_result($result);
    ?>
<table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
	align="center">
	<tr>
		<td bgcolor="<?php echo $THEME['bordercolor']?>">
			<table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>"
				cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
				<tr class="category">
					<td class="title" colspan="5"><?php echo $lang['fieldinformation']?> For <?php echo $table?></td>
				</tr>
				<tr class="tablerow" bgcolor="<?php echo $THEME['altbg1']?>">
					<td colspan="5"><a href="cp_dbinfo.php"><?php echo $lang['returndblist']?></a></td>
				</tr>
				<tr bgcolor="<?php echo $THEME['altbg1']?>">
					<td class="ctrtablerow"><strong><?php echo $lang['fieldnumber']?></strong></td>
					<td class="tablerow"><strong><?php echo $lang['fieldname']?></strong></td>
					<td class="tablerow"><strong><?php echo $lang['fieldtype']?></strong></td>
					<td class="tablerow"><strong><?php echo $lang['fieldlength']?></strong></td>
					<td class="tablerow"><strong><?php echo $lang['fieldflags']?></strong></td>
				</tr>
    <?php echo $showfields?>
    </table>
		</td>
	</tr>
</table>
<?php echo $shadow2?>
</td>
</tr>
</table>
<?php
}

displayAdminPanel();

$list = getRequestVar('list');
switch ($list) {
    case 'fields':
        listFields();
        break;
    default:
        viewPanel();
        break;
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
