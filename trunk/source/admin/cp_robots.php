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
nav($lang['Robot_Settings']);

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (! X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken, $CONFIG, $cheHTML, $selHTML;
    ?>
<form method="post" action="cp_robots.php?action=search">
	<input type="hidden" name="token"
		value="<?php echo $oToken->get_new_token()?>" />
	<table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
		align="center">
		<tr>
			<td bgcolor="<?php echo $THEME['bordercolor']?>">
				<table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>"
					cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
					<tr class="category">
						<td class="title" colspan="2"><?php echo $lang['textrobots']?></td>
					</tr>
					<tr class="tablerow">
						<td bgcolor="<?php echo $THEME['altbg1']?>" width="22%"><?php echo $lang['textsrchbot']?></td>
						<td bgcolor="<?php echo $THEME['altbg2']?>"><input type="text"
							name="srchbot" /></td>
					</tr>
					<tr bgcolor="<?php echo $THEME['altbg2']?>" class="ctrtablerow">
						<td colspan="2"><input type="submit" class="submit"
							value="<?php echo $lang['textgo']?>" /></td>
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

function viewSearchPanel()
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken, $CONFIG, $cheHTML, $selHTML;
    
    $oToken->assert_token();
    ?>
<form method="post" action="cp_robots.php">
	<input type="hidden" name="token"
		value="<?php echo $oToken->get_new_token()?>" />
	<table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
		align="center">
		<tr>
			<td bgcolor="<?php echo $THEME['bordercolor']?>">
				<table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>"
					cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
					<tr class="category">
						<td class="title" align="center" width="3%"><?php echo $lang['textdeleteques']?></td>
						<td class="title"><?php echo $lang['textrobotname']?></td>
						<td class="title"><?php echo $lang['textrobotagent']?></td>
					</tr>
    <?php
    $srchbot = $db->escape(formVar('srchbot'));
    if (! empty($srchbot)) {
        $srchbot = "WHERE robot_fullname LIKE '%" . $srchbot . "%' ";
    }
    $query = $db->query("SELECT * FROM " . X_PREFIX . "robots $srchbot ORDER BY robot_fullname");
    while (($robot = $db->fetch_array($query)) != false) {
        ?>
        <tr bgcolor="<?php echo $THEME['altbg2']?>" class="tablerow">
						<td align="center"><input type="checkbox"
							name="delete<?php echo $robot['robot_id']?>"
							value="<?php echo $robot['robot_id']?>" /></td>
						<td><input type="text" size="20"
							name="robotname<?php echo $robot['robot_id']?>"
							value="<?php echo $robot['robot_fullname']?>" /></td>
						<td><input type="text" size="20"
							name="robotagent<?php echo $robot['robot_id']?>"
							value="<?php echo $robot['robot_string']?>" /></td>
					</tr>
        <?php
    }
    ?>
    <tr>
						<td bgcolor="<?php echo $THEME['altbg2']?>" colspan="4">&nbsp;</td>
					</tr>
					<tr bgcolor="<?php echo $THEME['altbg1']?>" class="tablerow">
						<td><?php echo $lang['textnewrobot']?></td>
						<td><input type="text" name="newrobotname" /></td>
						<td colspan="2"><input type="text" name="newrobotagent" /></td>
					</tr>
					<tr bgcolor="<?php echo $THEME['altbg2']?>" class="ctrtablerow">
						<td colspan="7"><input type="submit" class="submit"
							name="robotsubmit"
							value="<?php echo $lang['textsubmitchanges']?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<?php echo $shadow2?>
</td>
</tr>
<?php
}

function doPanel()
{
    global $shadow2, $lang, $db, $THEME;
    global $oToken;
    
    $oToken->assert_token();
    
    $srchbot = $db->escape(formVar('srchbot'));
    if (! empty($srchbot)) {
        $srchbot = "WHERE robot_fullname LIKE '%" . $srchbot . "%' ";
    }
    
    $query = $db->query("SELECT * FROM " . X_PREFIX . "robots $srchbot");
    while (($bot = $db->fetch_array($query)) != false) {
        $delete = formInt("delete" . $bot['robot_id']);
        if ($delete > 0) {
            $db->query("DELETE FROM " . X_PREFIX . "robots WHERE robot_id = '$delete'");
        }
        
        $robotname = $db->escape(formVar("robotname" . $bot['robot_id']));
        if (! empty($robotname)) {
            $robotagent = $db->escape(formVar("robotagent" . $bot['robot_id']));
            $db->query("UPDATE " . X_PREFIX . "robots SET robot_fullname = '$robotname', robot_string = '$robotagent' WHERE robot_id = '$bot[robot_id]'");
        }
    }
    $db->free_result($query);
    
    $newrobotagent = $db->escape(formVar('newrobotagent'));
    $newrobotname = $db->escape(formVar('newrobotname'));
    if ($newrobotagent != '' && $newrobotname != '') {
        if ($db->result($db->query("SELECT COUNT(robot_id) FROM " . X_PREFIX . "robots WHERE robot_string = '$newrobotagent'"), 0) > 0) {
            cp_error($lang['robotexists'], false, '', '</td></tr></table>');
        }
        $query = $db->query("INSERT INTO " . X_PREFIX . "robots (robot_string, robot_fullname) VALUES ('$newrobotagent', '$newrobotname')");
    }
    
    cp_message($lang['textrobotsupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('robotsubmit')) {
    switch ($action) {
        case 'search':
            viewSearchPanel();
            break;
        default:
            viewPanel();
    }
}

if (onSubmit('robotsubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer').'";');
?>
