<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2017 The GaiaBB Group
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

eval('$css = "' . template('css') . '";');

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['pmdump']);
btitle($lang['textcp']);
btitle($lang['pmdump']);

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
<form method="post" action="cp_pmdump.php">
	<input type="hidden" name="token"
		value="<?php echo $oToken->get_new_token()?>" />
	<table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
		align="center">
		<tr>
			<td bgcolor="<?php echo $THEME['bordercolor']?>">
				<table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>"
					cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
					<tr>
						<td colspan="2" class="category"><strong><font
								color="<?php echo $THEME['cattext']?>"><?php echo $lang['pmdump']?></font></strong></td>
					</tr>
					<tr>
						<td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2']?>"
							colspan="2"><?php echo $lang['pmdump_confirm']?><br />
						<input class="submit" type="submit" name="yessubmit"
							value="<?php echo $lang['textyes']?>" /></td>
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
    global $shadow2, $lang, $db, $THEME;
    global $oToken;
    
    $oToken->assert_token();
    
    $db->query("TRUNCATE " . X_PREFIX . "pm");
    cp_message($lang['tool_pm'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('yessubmit')) {
    viewPanel();
}

if (onSubmit('yessubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer').'";');
?>
