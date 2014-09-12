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

eval('$css = "'.template('css').'";');

nav('<a href="index.php">'.$lang['textcp'].'</a>');
btitle($lang['textcp']);

eval('echo "'.template('cp_header').'";');

if (!X_SADMIN)
{
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['superadminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewModLogPanel($page)
{
    global $shadow2, $lang, $db, $THEME, $onlineip, $ips;
    global $oToken, $onlinetime, $self;

?>
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr class="category">
    <td><strong><font color="<?php echo $THEME['cattext']?>"><?php echo $lang['regusername']?></font></strong></td>
    <td><strong><font color="<?php echo $THEME['cattext']?>"><?php echo $lang['texttime']?></font></strong></td>
    <td><strong><font color="<?php echo $THEME['cattext']?>"><?php echo $lang['urltxt']?></font></strong></td>
    <td><strong><font color="<?php echo $THEME['cattext']?>"><?php echo $lang['actiontxt']?></font></strong></td>
    </tr>
    <?php
    $count = $db->result($db->query("SELECT count(fid) FROM ".X_PREFIX."modlogs WHERE NOT (fid = '0' AND tid = '0')"), 0);

    if (!isset($page) || $page < 1)
    {
        $page = 1;
    }

    $old = (($page-1)*25);
    $current = ($page*25);

    $firstpage = $prevpage = $nextpage = $random_var = '';
    $query = $db->query("SELECT l.*, t.subject FROM ".X_PREFIX."modlogs l LEFT JOIN ".X_PREFIX."threads t ON l.tid = t.tid WHERE NOT (l.fid = '0' AND l.tid = '0') ORDER BY date ASC LIMIT $old, 25");
    $url = '';
    while ($recordinfo = $db->fetch_array($query))
    {
        $date = gmdate($self['dateformat'], $recordinfo['date'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $time = gmdate($self['timecode'], $recordinfo['date'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        if ($recordinfo['tid'] > 0 && $recordinfo['action'] != 'delete' && trim($recordinfo['subject']) != '')
        {
            $url = '<a href="../viewtopic.php?tid='.$recordinfo['tid'].'" target="_blank">'.stripslashes($recordinfo['subject']).'</a>';
        }
        else if ($recordinfo['action'] == 'delete')
        {
            $recordinfo['action'] = '<strong>'.$recordinfo['action'].'</strong>';
            $url = '&nbsp;';
        }
        else
        {
            $url = 'tid='.$recordinfo['tid'].' - fid:'.$recordinfo['fid'];
        }
        ?>
        <tr>
        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1']?>"><a href="../viewprofile.php?memberid=<?php echo $recordinfo['uid']?>"><?php echo $recordinfo['username']?></a></td>
        <td class="tablerow" bgcolor="<?php echo $THEME['altbg2']?>"><?php echo $date?> <?php echo $lang['textat']?> <?php echo $time?></td>
        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1']?>"><?php echo $url?></td>
        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1']?>"><?php echo $recordinfo['action']?></td>
        </tr>
        <?php
    }
    $db->free_result($query);

    if ($count > $current)
    {
        $page = $current/25;
        if ($page > 1)
        {
            $prevpage = '<a href="./cp_logs.php?action=modlog&amp;page='.($page-1).'">&laquo; '.$lang['prevpage'].'</a>';
        }

        $nextpage = '<a href="./cp_logs.php?action=modlog&amp;page='.($page+1).'">'.$lang['nextpage'].' &raquo;</a>';

        if ($prevpage == '' || $nextpage == '')
        {
            $random_var = '';
        }
        else
        {
            $random_var = '-';
        }

        $last = ceil($count/25);
        if ($last > $page)
        {
            $lastpage = '<a href="cp_logs.php?action=modlog&amp;page='.$last.'">&nbsp;&raquo;&raquo;</a>';
        }

        $first = 1;
        if ($page > $first)
        {
            $firstpage = '<a href="cp_logs.php?action=modlog&amp;page='.$first.'">&nbsp;&laquo;&laquo;</a>';
        }
        ?>
        <tr class="header">
        <td colspan="4"><?php echo $firstpage?> <?php echo $prevpage?> <?php echo $random_var?> <?php echo $nextpage?> <?php echo $lastpage?></td>
        </tr>
        <?php
    }
    else
    {
        if ($page > 1)
        {
            $prevpage = '<a href="cp_logs.php?action=modlog&amp;page='.($page-1).'">&laquo; '.$lang['prevpage'].'</a>';
        }

        $first = 1;
        if ($page > $first)
        {
            $firstpage = '<a href="cp_logs.php?action=modlog&amp;page='.$first.'">&nbsp;&laquo;&laquo;</a>';
        }
        else
        {
            $firstpage = '';
        }

        if ($prevpage == '' || $nextpage == '')
        {
            $random_var = '';
        }
        else
        {
            $random_var = '-';
        }
        ?>
        <tr class="header">
        <td colspan="4"><?php echo $firstpage?> <?php echo $prevpage?> <?php echo $random_var?> <?php echo $nextpage?></td>
        </tr>
        <?php
    }

    if ($count == 0)
    {
        ?>
        <tr class="header">
        <td colspan="4"><?php echo $lang['nologspresent']?></td>
        </tr>
        <?php
    }
    ?>
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

function viewAdminLogPanel($page)
{
    global $shadow2, $lang, $db, $THEME, $onlineip, $ips;
    global $oToken, $onlinetime, $self;

    ?>
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr class="category">
    <td colspan="5"><strong><font color="<?php echo $THEME['cattext']?>"><?php echo $lang['textcplogs']?></font></strong></td>
    </tr>
    <tr class="header">
    <td><?php echo $lang['regusername']?></td>
    <td><?php echo $lang['texttime']?></td>
    <td><?php echo $lang['urltxt']?></td>
    <td><?php echo $lang['actiontxt']?></td>
    <td><?php echo $lang['textip']?>:</td>
    </tr>
    <?php
    $count = $db->result($db->query("SELECT count(fid) FROM ".X_PREFIX."adminlogs WHERE (fid = '0' AND tid = '0')"), 0);

    if (!isset($page) || $page < 1)
    {
        $page = 1;
    }

    $old = (($page-1)*25);
    $current = ($page*25);
    $firstpage = $prevpage = $nextpage = $random_var = '';

    $query = $db->query("SELECT l.*, t.subject FROM ".X_PREFIX."adminlogs l LEFT JOIN ".X_PREFIX."threads t ON l.tid = t.tid WHERE (l.fid = '0' AND l.tid = '0') ORDER BY date ASC LIMIT $old, 25");
    $url = '';
    while ($recordinfo = $db->fetch_array($query))
    {
        $date = gmdate($self['dateformat'], $recordinfo['date'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $time = gmdate($self['timecode'], $recordinfo['date'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $action = explode('|#|', $recordinfo['action']);
        if (strpos($action[1], '/') === false)
        {
            $recordinfo['action'] = $action[1];
            $url = '&nbsp';
        }
        else
        {
            $recordinfo['action'] = '&nbsp;';
            $url = $action[1];
        }
        ?>
        <tr>
        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1']?>"><a href="../viewprofile.php?memberid=<?php echo $recordinfo['uid']?>"><?php echo $recordinfo['username']?></a></td>
        <td class="tablerow" bgcolor="<?php echo $THEME['altbg2']?>"><?php echo $date?> at <?php echo $time?></td>
        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1']?>"><?php echo $url?></td>
        <td class="tablerow" bgcolor="<?php echo $THEME['altbg2']?>"><?php echo $recordinfo['action']?></td>
        <td class="tablerow" bgcolor="<?php echo $THEME['altbg1']?>"><?php echo $action[0]?></td>
        </tr>
        <?php
    }
    $db->free_result($query);

    if ($count > $current)
    {
        $page = $current/25;
        if ($page > 1)
        {
            $prevpage = '<a href="cp_logs.php?action=cplog&amp;page='.($page-1).'">&laquo; '.$lang['prevpage'].'</a>';
        }

        $nextpage = '<a href="cp_logs.php?action=cplog&amp;page='.($page+1).'">'.$lang['nextpage'].' &raquo;</a>';

        if ($prevpage == '' || $nextpage == '')
        {
            $random_var = '';
        }
        else
        {
            $random_var = '-';
        }

        $last = ceil($count/25);
        if ($last > $page)
        {
            $lastpage = '<a href="cp_logs.php?action=cplog&amp;page='.$last.'">&nbsp;&raquo;&raquo;</a>';
        }

        $first = 1;
        if ($page > $first)
        {
            $firstpage = '<a href="cp_logs.php?action=cplog&amp;page='.$first.'">&nbsp;&laquo;&laquo;</a>';
        }
        ?>
        <tr class="header">
        <td colspan="5"><?php echo $firstpage?> <?php echo $prevpage?> <?php echo $random_var?> <?php echo $nextpage?> <?php echo $lastpage?></td>
        </tr>
        <?php
    }
    else
    {
        if ($page == 1)
        {
            $prevpage = '';
        }
        else
        {
            $prevpage = '<a href="cp_logs.php?action=cplog&amp;page='.($page-1).'">&laquo; '.$lang['prevpage'].'</a>';
        }

        $first = 1;
        if ($page > $first)
        {
            $firstpage = '<a href="cp_logs.php?action=cplog&amp;page='.$first.'">&nbsp;&laquo;&laquo;</a>';
        }
        ?>
        <tr class="header">
        <td colspan="5"><?php echo $firstpage?> <?php echo $prevpage?> <?php echo $random_var?> <?php echo $nextpage?></td>
        </tr>
        <?php
    }

    if ($count == 0)
    {
        ?>
        <tr class="header">
        <td colspan="5"><?php echo $lang['nologspresent']?></td>
        </tr>
        <?php
    }
    ?>
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

// Validate user input
$page = getRequestInt('page');
if ($page < 1)
{
    $page = 1;
}

if ($action == 'modlog')
{
    viewModLogPanel($page);
}
else if ($action == 'cplog')
{
    viewAdminLogPanel($page);
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>
