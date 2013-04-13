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

// Safe to use without global.inc.php
define('DEBUG_REG', true);
define('CACHECONTROL', 'nocache');
define('ROOT', './');

require_once(ROOT.'header.php');

loadtpl('lostpw');

$shadow = shadowfx();
$meta = metaTags();

smcwcache();

eval('$css = "'.template('css').'";');

nav($lang['textlostpw']);
btitle($lang['textlostpw']);

eval('echo "'.template('header').'";');

if (X_MEMBER)
{
    error($lang['plogtuf'], false, '', '', 'index.php', true);
}

if (noSubmit('lostpwsubmit'))
{
    eval('echo stripslashes("'.template('lostpw').'");');
}
else
{
    $username = $db->escape(formVar('username'));
    $email = formVar('email');

    $query = $db->query("SELECT username, email, pwdate FROM ".X_PREFIX."members WHERE (username = '$username' and status != 'Banned')");
    $member = $db->fetch_array($query);
    $rows = $db->num_rows($query);
    $db->free_result($query);

    if ($rows == 1 && strtolower($email) === strtolower(stripslashes($member['email'])))
    {
        $time = $onlinetime - 86400;
        if ($member['pwdate'] > $time)
        {
            error($lang['badinfo'], false, '', '', 'index.php', true);
        }
    }
    else
    {
        error($lang['badinfo'], false, '', '', 'lostpw.php', true);
    }

    $email = stripslashes($member['email']); // SMTP functions cannot handle database escaped e-mail addresses

    $chars = '23456789abcdefghjkmnpqrstuvwxyz';
    $newpass = '';
    mt_srand((double)microtime() * 1000000);
    $max = mt_rand(8, 12);
    for ($get = strlen($chars), $i = 0; $i < $max; $i++)
    {
        $newpass .= $chars[mt_rand(0, $get)];
    }
    $newmd5pass = md5(trim($newpass));

    $config_cache->expire('settings');
    $moderators_cache->expire('moderators');
    $config_cache->expire('theme');
    $config_cache->expire('pluglinks');
    $config_cache->expire('whosonline');
    $config_cache->expire('forumjump');

    $db->query("UPDATE ".X_PREFIX."members SET password = '$newmd5pass', pwdate = '$onlinetime' WHERE username = '$member[username]' AND email = '$member[email]'");

    $messagebody = $lang['textyourpwis']."\n\n".$member['username']."\n".$newpass;

    if (empty($CONFIG['adminemail'])) // The mail class can handle this error, but it'll describe it vaguely
    {
        error($lang['noadminemail'], false, '', '', 'admin/cp_board.php', true, false, true);
    }

    if (empty($CONFIG['bbname'])) // The mail class can handle this error, but it'll describe it vaguely
    {
        error($lang['nobbname'], false, '', '', 'admin/cp_board.php', true, false, true);
    }

    $mailsys->setTo($email);
    $mailsys->setFrom($CONFIG['adminemail'], $CONFIG['bbname']);
    $mailsys->setSubject($lang['textyourpw']);
    $mailsys->setMessage($messagebody);
    $mailsys->Send();
    
    message($lang['emailpw'], false, '', '', 'index.php', true, false, true);

}

loadtime();
eval('echo "'.template('footer').'";');
?>
