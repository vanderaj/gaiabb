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

eval('$css = "' . template('css') . '";');

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['textfixothreads']);
btitle($lang['textcp']);
btitle($lang['textfixothreads']);

eval('echo "' . template('cp_header') . '";');

if (! X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    ?>
<form method="post" action="cp_fixthreads.php">
	<input type="hidden" name="token"
		value="<?php echo $oToken->get_new_token()?>" />
	<table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
		align="center">
		<tr>
			<td bgcolor="<?php echo $THEME['bordercolor']?>">
				<table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>"
					cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
					<tr class="category">
						<td colspan="2" class="title"><?php echo $lang['textfixothreads']?></td>
					</tr>
					<tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg1']?>">
						<td colspan="2"><?php echo $lang['fixorphanedthreads']?></td>
					</tr>
					<tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2']?>">
						<td colspan="2"><input class="submit" type="submit"
							name="yessubmit" value="<?php echo $lang['textyes']?>" />&nbsp;-&nbsp;<input
							class="submit" type="submit" name="nosubmit"
							value="<?php echo $lang['textno']?>" /></td>
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
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    
    $oToken->assert_token();
    
    $query = $db->query("SELECT fid, tid FROM " . X_PREFIX . "threads WHERE 1 ORDER BY tid ASC");
    $count = $count2 = 0;
    while (($threads = $db->fetch_array($query)) != false) {
        $count2 ++;
        $query2 = $db->query("SELECT fid, name FROM " . X_PREFIX . "forums WHERE fid = $threads[fid]");
        $forum = $db->fetch_array($query2);
        if (empty($forum['fid'])) {
            $count ++;
            $db->query("DELETE FROM " . X_PREFIX . "threads WHERE tid = $threads[tid]");
        }
    }
    $db->free_result($query);
    if ($count == 0) {
        $percent = 0;
    } else {
        $percent = 100 / ($count2 / $count);
    }
    cp_message($count . ' ' . $lang['tool_of'] . ' ' . $count2 . ' (' . $percent . '%) ' . $lang['tool_fids'], false, '', '</td></tr></table>', 'index.php', true, false, true);
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
