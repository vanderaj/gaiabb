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

nav('<a href="index.php">'.$lang['textcp'].'</a>');
nav($lang['textipban']);
btitle($lang['textcp']);
btitle($lang['textipban']);

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
    global $shadow2, $lang, $db, $THEME, $onlineip, $ips;
    global $oToken, $onlinetime, $self;
    ?>
    <form method="post" action="cp_ipban.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token()?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr><td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr class="category">
    <td class="title" align="center"><?php echo $lang['textdeleteques']?></td>
    <td class="title" align="center"><?php echo $lang['textip']?>:</td>
    <td class="title" align="center"><?php echo $lang['textipresolve']?>:</td>
    <td class="title" align="center"><?php echo $lang['textadded']?></td>
    </tr>
    <?php
    $query = $db->query("SELECT * FROM ".X_PREFIX."banned ORDER BY dateline");
    $rowsFound = $db->num_rows($query);
    while ($ipaddress = $db->fetch_array($query))
    {
        for($i = 1; $i <=4; ++$i)
        {
            $j = "ip" . $i;
            if ($ipaddress[$j] == -1)
            {
                $ipaddress[$j] = "*";
            }
        }
        $ipdate = gmdate($self['dateformat'], $ipaddress['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']).' '.$lang['textat'].' '.gmdate($self['timecode'], $ipaddress['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $theip = "$ipaddress[ip1].$ipaddress[ip2].$ipaddress[ip3].$ipaddress[ip4]";
        ?>
        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2']?>">
        <td><input type="checkbox" name="delete<?php echo $ipaddress['id']?>" value="on" /></td>
        <td><?php echo $theip?></td>
        <td><?php echo @gethostbyaddr($theip)?></td>
        <td><?php echo $ipdate?></td>
        </tr>
        <?php
    }
    $db->free_result($query);

    $query = $db->query("SELECT id FROM ".X_PREFIX."banned WHERE (ip1 = '$ips[0]' OR ip1 = '-1') AND (ip2 = '$ips[1]' OR ip2 = '-1') AND (ip3 = '$ips[2]' OR ip3 = '-1') AND (ip4 = '$ips[3]' OR ip4 = '-1')");
    $result = $db->fetch_array($query);

    if ($result)
    {
        $warning = $lang['ipwarning'];
    }
    else
    {
        $warning = '';
    }

    if ($rowsFound < 1)
    {
        ?>
        <tr bgcolor="<?php echo $THEME['altbg2']?>" class="ctrtablerow">
        <td colspan="4"><?php echo $lang['textnone']?></td>
        </tr>
        <?php
    }
    ?>
    <tr class="tablerow">
    <td bgcolor="<?php echo $THEME['altbg1']?>" colspan="4"><span class="smalltxt"><?php echo $lang['currentip']?> <strong><?php echo $onlineip?></strong><?php echo $warning?><br /><?php echo $lang['multipnote']?></span></td>
    </tr>
    <tr bgcolor="<?php echo $THEME['altbg2']?>">
    <td colspan="4" class="tablerow"><?php echo $lang['textnewip']?>&nbsp;<input type="text" name="newip1" size="3" maxlength="3" />.<input type="text" name="newip2" size="3" maxlength="3" />.<input type="text" name="newip3" size="3" maxlength="3" />.<input type="text" name="newip4" size="3" maxlength="3" /></td>
    </tr>
    <tr>
    <td class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2']?>" colspan="4"><input type="submit" class="submit" name="ipbansubmit" value="<?php echo $lang['textsubmitchanges']?>" /></td>
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
    global $shadow2, $lang, $db, $THEME;
    global $oToken, $onlinetime;

    $oToken->assert_token();

    $query = $db->query("SELECT id FROM ".X_PREFIX."banned");
    while ($ip = $db->fetch_array($query))
    {
        $delete = "delete" . $ip['id'];
        if (formOnOff($delete) == 'on')
        {
            $db->query("DELETE FROM ".X_PREFIX."banned WHERE id = '".$ip['id']."'");
        }
    }
    $db->free_result($query);

    $msg = $lang['textipupdate'];

    $ip = array();

    $ip[1] = formVar('newip1');
    $ip[2] = formVar('newip2');
    $ip[3] = formVar('newip3');
    $ip[4] = formVar('newip4');

    for ($i = 1; $i < 5; $i++)
    {
        if ($ip[$i] == '*')
        {
            $ip[$i] = -1;
        }

        $ip[$i] = (int) $ip[$i];
        if ($ip[$i] < -1 || $ip[$i] > 255)
        {
            $msg = $lang['invalidip'];
            break;
        }
    }

    if ($ip[1] == '-1' && $ip[2] == '-1' && $ip[3] == '-1' && $ip[4] == '-1')
    {
        $msg = $lang['impossiblebanall'];
    }

    if ($msg === $lang['textipupdate'])
    {
        $query = $db->query("SELECT id FROM ".X_PREFIX."banned WHERE (ip1 = '$ip[1]' OR ip1 = '-1') AND (ip2 = '$ip[2]' OR ip2 = '-1') AND (ip3 = '$ip[3]' OR ip3 = '-1') AND (ip4 = '$ip[4]' OR ip4 = '-1')");
        $result = $db->fetch_array($query);
        if ($result)
        {
            $msg = $lang['existingip'];
        }
        else
        {
                if (!($ip[1] == 0 && $ip[2] == 0 && $ip[3] == 0 && $ip[4] == 0))
                {
                $query = $db->query("INSERT INTO ".X_PREFIX."banned (ip1, ip2, ip3, ip4, dateline) VALUES ('$ip[1]', '$ip[2]', '$ip[3]', '$ip[4]', '$onlinetime')");
                }
        }
    }

    cp_message($msg, false, '', '</td></tr></table>', 'cp_ipban.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('ipbansubmit'))
{
    viewPanel();
}

if (onSubmit('ipbansubmit'))
{
    doPanel();
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>
