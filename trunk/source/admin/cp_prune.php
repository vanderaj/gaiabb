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

require_once (ROOT . 'header.php');
require_once (ROOTINC . 'admincp.inc.php');

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['textprune']);
btitle($lang['textcp']);
btitle($lang['textprune']);

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
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML;
    
    $forumselect = forumList('pruneFromList[]', true, false);
    ?>
<form method="post" action="cp_prune.php">
	<input type="hidden" name="token"
		value="<?php echo $oToken->get_new_token()?>" />
	<table cellspacing="0px" cellpadding="0px" border="0px" width="100%">
		<tr>
			<td bgcolor="<?php echo $THEME['bordercolor']?>">
				<table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>"
					cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
					<tr class="category">
						<td class="title" colspan="2"><?php echo $lang['textprune']?></td>
					</tr>
					<tr>
						<td class="tablerow" bgcolor="<?php echo $THEME['altbg1']?>">
    <?php echo $lang['pruneby']?>
    </td>
						<td class="tablerow" bgcolor="<?php echo $THEME['altbg2']?>">
							<table>
								<tr>
									<td><input type="checkbox" name="pruneBy[]" value="date" /></td>
									<td class="tablerow"><select name="pruneByDateType">
											<option value="more"><?php echo $lang['prunemorethan']?></option>
											<option value="is"><?php echo $lang['pruneexactly']?></option>
											<option value="less"><?php echo $lang['prunelessthan']?></option>
									</select> <input type="text" name="pruneByDate" value="10" /> <?php echo $lang['daysold']?>
    </td>
								</tr>
								<tr>
									<td class="tablerow"><input type="checkbox" name="pruneBy[]"
										value="posts" /></td>
									<td class="tablerow"><select name="pruneByPostsType">
											<option value="more"><?php echo $lang['prunemorethan']?></option>
											<option value="is"><?php echo $lang['pruneexactly']?></option>
											<option value="less"><?php echo $lang['prunelessthan']?></option>
									</select> <input type="text" name="pruneByPosts" value="10" /> <?php echo $lang['memposts']?>
    </td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="tablerow" bgcolor="<?php echo $THEME['altbg1']?>"><?php echo $lang['prunefrom']?></td>
						<td class="tablerow" bgcolor="<?php echo $THEME['altbg2']?>">
							<table>
								<tr>
									<td class="tablerow"><input type="radio" name="pruneFrom"
										value="all" <?php echo $cheHTML?> /></td>
									<td class="tablerow"><?php echo $lang['textallforumsandsubs']?></td>
								</tr>
								<tr>
									<td class="tablerow"><input type="radio" name="pruneFrom"
										value="list" /></td>
									<td class="tablerow"><?php echo $forumselect?></td>
								</tr>
								<tr>
									<td class="tablerow"><input type="radio" name="pruneFrom"
										value="fid" /></td>
									<td class="tablerow"><?php echo $lang['prunefids']?> <input
										type="text" name="pruneFromFid" value="" /> <span
										class="smalltxt">(<?php echo $lang['seperatebycomma']?>)</span></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr class="tablerow">
						<td bgcolor="<?php echo $THEME['altbg1']?>"><?php echo $lang['pruneposttypes']?></td>
						<td bgcolor="<?php echo $THEME['altbg2']?>"><input type="checkbox"
							name="pruneType[]" value="normal" <?php echo $cheHTML?> /> <?php echo $lang['prunenormal']?><br />
							<input type="checkbox" name="pruneType[]" value="closed"
							<?php echo $cheHTML?> /> <?php echo $lang['pruneclosed']?><br />
							<input type="checkbox" name="pruneType[]" value="topped" /> <?php echo $lang['prunetopped']?><br />
						</td>
					</tr>
					<tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2']?>">
						<td colspan="2"><input type="submit" name="pruneSubmit"
							value="<?php echo $lang['textprune']?>" /></td>
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
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG, $onlinetime;
    
    $oToken->assert_token();
    
    $queryWhere = array();
    
    $pruneBy = formArray('pruneBy');
    $pruneByPosts = formInt('pruneByPosts');
    $pruneByPostType = formVar('pruneByPostType');
    $pruneByDate = formInt('pruneByDate');
    $pruneByDateType = formVar('pruneByDateType');
    
    $pruneFrom = formVar('pruneFrom');
    $pruneFromFid = formVar('pruneFromFid');
    
    $pruneType = formArray('pruneType');
    
    $pruneFromList = formArray('pruneFromList');
    
    switch ($pruneFrom) {
        case 'all':
            break;
        case 'list':
            $fs = array();
            
            foreach ($pruneFromList as $fid) {
                $fs[] = intval($fid);
            }
            
            $fs = array_unique($fs);
            
            if (count($fs) < 1) {
                cp_error($lang['nopruneforums'], false, '', '</td></tr></table>');
            }
            
            $queryWhere[] = 't.fid IN (' . implode(',', $fs) . ')';
            break;
        
        case 'fid':
            $fs = array();
            
            $fids = explode(',', $pruneFromFid);
            
            foreach ($fids as $fid) {
                $fs[] = intval($fid);
            }
            
            $fs = array_unique($fs);
            
            if (count($fs) < 1) {
                cp_error($lang['nopruneforums'], false, '', '</td></tr></table>');
            }
            
            $queryWhere[] = 't.fid IN (' . implode(',', $fs) . ')';
            break;
        
        default:
            cp_error($lang['nopruneforums'], false, '', '</td></tr></table>');
            break;
    }
    
    if (in_array('posts', $pruneBy)) {
        $sign = '';
        switch ($pruneByPostType) {
            case 'less':
                $sign = '<';
                break;
            
            case 'is':
                $sign = '=';
                break;
            
            case 'more':
            default:
                $sign = '>';
                break;
        }
        
        $queryWhere[] = 't.replies ' . $sign . ' ' . ($pruneByPosts - 1);
    }
    
    if (in_array('date', $pruneBy)) {
        $sign = '';
        switch ($pruneByDateType) {
            case 'less':
                $queryWhere[] = 't.tid=l.tid AND l.dateline >= ' . ($onlinetime - (24 * 3600 * $pruneByDate));
                break;
            case 'is':
                $queryWhere[] = 't.tid=l.tid AND l.dateline >= ' . ($onlinetime - (24 * 3600 * ($pruneByDate - 1))) . ' AND l.dateline <= ' . ($onlinetime - (24 * 3600 * ($pruneByDate)));
                break;
            case 'more':
            default:
                $queryWhere[] = 't.tid=l.tid AND l.dateline <= ' . ($onlinetime - (24 * 3600 * $pruneByDate));
                break;
        }
    }
    
    if (! in_array('closed', $pruneType)) {
        $queryWhere[] = "t.closed != 'yes'";
    }
    
    if (! in_array('topped', $pruneType)) {
        $queryWhere[] = "t.topped != '1'";
    }
    
    if (! in_array('normal', $pruneType)) {
        $queryWhere[] = "(t.topped = '1' OR t.closed = 'yes')";
    }
    
    if (count($queryWhere) > 0) {
        $tids = array();
        $queryWhere = implode(' AND ', $queryWhere);
        $q = $db->query("SELECT t.tid FROM " . X_PREFIX . "threads t, " . X_PREFIX . "lastposts l WHERE " . $queryWhere);
        if ($db->num_rows($q) > 0) {
            while ($t = $db->fetch_array($q)) {
                $tids[] = $t['tid'];
            }
            $db->free_result($q);
            $tids = implode(',', $tids);
            $db->query("DELETE FROM " . X_PREFIX . "threads WHERE tid IN (" . $tids . ")");
            $db->query("DELETE FROM " . X_PREFIX . "posts WHERE tid IN (" . $tids . ")");
            $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE tid IN (" . $tids . ")");
            $db->query("DELETE FROM " . X_PREFIX . "subscriptions WHERE tid IN (" . $tids . ")");
            updatelastposts();
        }
    } else {
        $db->query("TRUNCATE TABLE " . X_PREFIX . "threads");
        $db->query("TRUNCATE TABLE " . X_PREFIX . "attachments");
        $db->query("TRUNCATE TABLE " . X_PREFIX . "posts");
        $db->query("TRUNCATE TABLE " . X_PREFIX . "subscriptions");
        $db->query("UPDATE " . X_PREFIX . "members SET postnum = 0");
        updatelastposts();
    }
    
    cp_message($lang['forumpruned'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('pruneSubmit'))
{
    viewPanel();
}

if (onSubmit('pruneSubmit'))
{
    doPanel();
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>
