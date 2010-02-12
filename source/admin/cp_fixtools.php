<?php
/**
 * GaiaBB
 * Copyright (c) 2010 The GaiaBB Group
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
nav($lang['tools']);
btitle($lang['textcp']);
btitle($lang['tools']);
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
}
function fixorphanedposts()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;

    $oToken->assert_token();

    $query = $db->query("SELECT tid, pid FROM " . X_PREFIX . "posts WHERE 1 ORDER BY pid ASC");
    $count = $count2 = 0;
    while ($posts = $db->fetch_array($query))
    {
        $count2++;
        $query2 = $db->query("SELECT tid, subject FROM " . X_PREFIX . "threads WHERE tid = $posts[tid]");
        $thread = $db->fetch_array($query2);
        if (empty ($thread['tid']))
        {
            $count++;
            $db->query("DELETE FROM " . X_PREFIX . "posts WHERE pid = $posts[pid]");
        }
    }
    $db->free_result($query);
    if ($count == 0)
    {
        $percent = 0;
    } else
    {
        $percent = 100 / ($count2 / $count);
    }
    cp_message($count . ' ' . $lang['tool_of'] . ' ' . $count2 . ' (' . $percent . '%) ' . $lang['tool_tids'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

function fixorphanedfavorites()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    $oToken->assert_token();
    $query = $db->query("SELECT tid, username FROM " . X_PREFIX . "favorites WHERE 1 ORDER BY tid ASC");
    $count = $count2 = 0;
    while ($favs = $db->fetch_array($query))
    {
        $count2++;
        $query2 = $db->query("SELECT tid FROM " . X_PREFIX . "threads WHERE tid = $favs[tid]");
        $thread = $db->fetch_array($query2);
        if (empty ($thread['tid']))
        {
            $count++;
            $db->query("DELETE FROM " . X_PREFIX . "favorites WHERE tid = $favs[tid] AND username = '$favs[username]'");
            $db->query("DELETE FROM " . X_PREFIX . "favorites WHERE username = ''");
        }
    }
    $db->free_result($query);
    if ($count == 0)
    {
        $percent = 0;
    } else
    {
        $percent = 100 / ($count2 / $count);
    }
    cp_message($count . ' ' . $lang['tool_of'] . ' ' . $count2 . ' (' . $percent . '%) ' . $lang['tool_favorites'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

function fixorphanedsubscriptions()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;

    $oToken->assert_token();

    $query = $db->query("SELECT tid, username FROM " . X_PREFIX . "subscriptions WHERE 1 ORDER BY tid ASC");
    $count = $count2 = 0;
    while ($subs = $db->fetch_array($query))
    {
        $count2++;
        $query2 = $db->query("SELECT tid FROM " . X_PREFIX . "threads WHERE tid = $subs[tid]");
        $thread = $db->fetch_array($query2);
        if (empty ($thread['tid']))
        {
            $count++;
            $db->query("DELETE FROM " . X_PREFIX . "subscriptions WHERE tid = $subs[tid] AND username = '$subs[username]'");
            $db->query("DELETE FROM " . X_PREFIX . "subscriptions WHERE username = ''");
        }
    }
    $db->free_result($query);
    if ($count == 0)
    {
        $percent = 0;
    } else
    {
        $percent = 100 / ($count2 / $count);
    }
    cp_message($count . ' ' . $lang['tool_of'] . ' ' . $count2 . ' (' . $percent . '%) ' . $lang['tool_subscriptions'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

switch ($action)
{
    case 'fixorphanedposts':
        fixorphanedposts();
        break;
    case 'fixorphanedfavorites':
        fixorphanedfavorites();
        break;
    case 'fixorphanedsubscriptions':
        fixorphanedsubscriptions();
        break;
    default:
        redirect('index.php', 0);
    break;
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>
