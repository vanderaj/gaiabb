<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2013 The GaiaBB Group
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

// check to ensure no direct viewing of page
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false))
{
    exit('This file is not designed to be called directly');
}

/**
* Displays the mod panel in the moderator control panel
*/
function displayModPanel()
{
    global $THEME, $CONFIG, $lang, $shadow2;
    ?>
    <script language="JavaScript" type="text/javascript" src="../js/admin.js"></script>
    <table cellspacing="0" cellpadding="1" border="0" width="<?php echo $THEME['tablewidth']?>" align="center">
    <tr>
    <td nowrap="nowrap" width="180px" valign="top" class="tablerow">
    <table cellspacing="0" cellpadding="0" border="0" style="width:99%;">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table cellspacing="<?php echo $THEME['borderwidth']?>" border="0" cellpadding="<?php echo $THEME['tablespace']?>" style="width:100%;">
    <tr class="category">
    <td style="text-align:center;"><a href="javascript:viewset('<?php echo $lang['general']?>')"><strong><font color="<?php echo $THEME['cattext']?>"><?php echo $lang['general']?></font></strong></a></td>
    </tr>
    <tr class="hidden" id="<?php echo $lang['general']?>">
    <td>
    <ul>
    <li><a href="mod_members.php?action=members"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textmembers']?></a></li>
    <li><a href="mod_censors.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textcensors']?></a></li>
    <li><a href="mod_newsletter.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textnewsletter']?></a></li>
    <li><a href="mod_search.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['cpsearch']?></a></li>
    </ul>
    </td>
    </tr>
    <tr class="category">
    <td style="text-align:center;"><a href="javascript:viewset('<?php echo $lang['tools']?>')"><strong><font color="<?php echo $THEME['cattext']?>"><?php echo $lang['tools']?></font></strong></a></td>
    </tr>
    <tr class="hidden" id="<?php echo $lang['tools']?>">
    <td>
    <ul>
    <li><a href="mod_fixftotals.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textfixposts']?></a></li>
    <li><a href="mod_fixlastposts.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textfixlastposts']?></a></li>
    <li><a href="mod_fixmposts.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textfixmemposts']?></a></li>
    <li><a href="mod_fixttotals.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textfixthread']?></a></li>
    <li><a href="mod_fixorphanedthreads.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textfixothreads']?></a></li>
    <li><a href="mod_fixorphanedattachments.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textfixoattachments']?></a></li>
    <li><a href="mod_fixorphanedposts.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['tool_fixtids']?></a></li>
    <li><a href="mod_fixorphanedfavorites.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['tool_fixfavtids']?></a></li>
    <li><a href="mod_whosonlinedump.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['cpwodump']?></a></li>
    <li><a href="mod_updatemoods.php"><?php echo $THEME['navsymbol']?>&nbsp;<?php echo $lang['textfixmoods']?></a></li>
    </ul>
    </td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    <?php echo $shadow2?>
    <script language="JavaScript" type="text/javascript" src="../js/admin_menu.js"></script>
    </td>
    <td valign="top">
    <?php
}
?>