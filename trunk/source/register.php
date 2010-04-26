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
define('ROOT', './');
define('CACHECONTROL', 'nocache');

require_once(ROOT.'header.php');
require_once(ROOTCLASS.'member.class.php');

loadtpl(
'register',
'register_coppa',
'register_password',
'register_rules',
'register_captcha',
'register_captchajs'
);

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function notifyViapm($username)
{
    global $db, $CONFIG, $lang, $onlinetime;
    if (!empty($CONFIG['usernamenotify']))
    {
        $member = explode(',', $CONFIG['usernamenotify']);
        for ($i = 0; $i < count($member); $i++)
        {
            $member[$i] = trim($member[$i]);
            $mailquery = $db->query("SELECT * FROM ".X_PREFIX."members WHERE username = '$member[$i]'");
            while ($admin = $db->fetch_array($mailquery))
            {
                $db->query("INSERT INTO ".X_PREFIX."pm (pmid, msgto, msgfrom, type, owner, folder, subject, message, dateline, readstatus, sentstatus, usesig) VALUES ('', '$admin[username]', '$admin[username]', 'incoming', '$admin[username]', 'Inbox', '$lang[newmember] ".$db->escape($CONFIG['bbname'])."', '$lang[newmember3]\n\n$username', '$onlinetime', 'no', 'yes', 'no')");
            }
            $db->free_result($mailquery);
        }
    }
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function notifyViaEmail($username)
{
    global $db, $mailsys, $CONFIG, $lang, $charset;

    if (!empty($CONFIG['usernamenotify']))
    {

        $member = explode(',', $CONFIG['usernamenotify']);
        for ($i = 0; $i < count($member); $i++)
        {
            $member[$i] = trim($member[$i]);
            $mailquery = $db->query("SELECT * FROM ".X_PREFIX."members WHERE username = '$member[$i]'");
            while ($notify = $db->fetch_array($mailquery))
            {
                $mailsys->setTo($notify['email']);
                $mailsys->setFrom($CONFIG['adminemail'], $CONFIG['bbname']);
                $mailsys->setSubject($lang['textnewmember']);
                $mailsys->setMessage($lang['textnewmember2']);
                $mailsys->Send();
            }
            $db->free_result($mailquery);
        }
    }
}

$shadow = shadowfx();
$meta = metaTags();

smcwcache();

eval('$css = "'.template('css').'";');

if (X_MEMBER)
{
    error($lang['plogtuf'], true);
}

if ($CONFIG['regstatus'] == 'off')
{
    error($lang['fnasorry']);
}

$time = $onlinetime - 86400;
$query = $db->query("SELECT COUNT(uid) FROM ".X_PREFIX."members WHERE regdate > '$time'");
if ($db->result($query, 0) > $CONFIG['max_reg_day'])
{
    $db->free_result($query);
    error($lang['max_regs'], true, '', '', 'index.php', true, false, true);
}

// Prune new users who have never logged in. Tempered by the number of days grace set in Admin > Settings.
if ($CONFIG['inactiveusers'] > 0)
{
    $inactivebefore = $onlinetime - (60 * 60 * 24 * $CONFIG['inactiveusers']);
    $db->query("DELETE FROM ".X_PREFIX."members WHERE lastvisit = 0 AND regdate < $inactivebefore AND status = 'Member'");
}

// Validation of user supplied data
$username = formVar('username');
$password = formVar('password');
$password2 = formVar('password2');
$email = formVar('email');
$langfilenew = formInt('langfilenew');
$captchaword = formVar('captchaword');
$thememem = formInt('thememem');
$psorting = formVar('psorting');
if ($psorting != 'ASC')
{
    $psorting = 'DESC';
}
$showemail = formYesNo('showemail');
$newsletter = formYesNo('newsletter');
$saveogpm = formYesNo('saveogpm');
$emailonpm = formYesNo('emailonpm');
$viewavatars = formYesNo('viewavatars');
$viewsigs = formYesNo('viewsigs');
$expview = formYesNo('expview');
$daylightsavings1 = form3600('daylightsavings1');
$timeformat1 = formVar('timeformat1');
$dateformat1 = formVar('dateformat1');
$timeoffset1 = formInt('timeoffset1');

$member = new member();

switch ($action)
{
    case 'captcha':
        nav($lang['textregister']);
        btitle($lang['textregister']);
        break;
    case 'reg':
        nav($lang['textregister']);
        btitle($lang['textregister']);
        break;
    case 'coppa':
        nav($lang['textcoppa']);
        btitle($lang['textcoppa']);
        break;
    default:
        nav($lang['error']);
        btitle($lang['error']);
        break;
}

switch ($action)
{
    case 'captcha':
        require_once(ROOTCLASS.'captcha.class.php');
        $captcha = new captcha();
        break;
    case 'coppa':
        if ($CONFIG['coppa'] == 'off')
        {
            redirect('register.php?action=reg', 0);
        }

        if (onSubmit('coppasubmit'))
        {
            redirect('register.php?action=reg', 0);
        }
        else
        {
            eval('echo "'.template('header').'";');
            eval('echo "'.template('register_coppa').'";');
        }
        break;
    case 'reg':
        if (noSubmit('regsubmit'))
        {
            eval('echo "'.template('header').'";');
            if ($CONFIG['bbrules'] == 'on' && noSubmit('rulesubmit'))
            {
                $CONFIG['bbrulestxt'] = postify($CONFIG['bbrulestxt']);
                eval('echo stripslashes("'.template('register_rules').'");');
            }
            else
            {
                $langfileselect = langSelect();

                $currdate = gmdate($self['timecode'], $onlinetime);
                eval($lang['evaloffset']);

                $dstyes = $dstno = '';
                if ($CONFIG['daylightsavings'] == 3600)
                {
                    $dstyes = $selHTML;
                }
                else
                {
                    $dstno = $selHTML;
                }

                if ($CONFIG['timeformat'] == 24)
                {
                    $timeFormat12Selected = '';
                    $timeFormat24Selected = $selHTML;
                }
                else
                {
                    $timeFormat12Selected = $selHTML;
                    $timeFormat24Selected = '';
                }

                $timezone1 = $timezone2 = $timezone3 = $timezone4 = $timezone5 = $timezone6 = '';
                $timezone7 = $timezone8 = $timezone9 = $timezone10 = $timezone11 = $timezone12 = '';
                $timezone13 = $timezone14 = $timezone15 = $timezone16 = $timezone17 = $timezone18 = '';
                $timezone19 = $timezone20 = $timezone21 = $timezone22 = $timezone23 = $timezone24 = '';
                $timezone25 = $timezone26 = $timezone27 = $timezone28 = $timezone29 = $timezone30 = '';
                $timezone31 = $timezone32 = $timezone33 = '';
                switch ($CONFIG['def_tz'])
                {
                    case '-12.00':
                        $timezone1 = $selHTML;
                        break;
                    case '-11.00':
                        $timezone2 = $selHTML;
                        break;
                    case '-10.00':
                        $timezone3 = $selHTML;
                        break;
                    case '-9.00':
                        $timezone4 = $selHTML;
                        break;
                    case '-8.00':
                        $timezone5 = $selHTML;
                        break;
                    case '-7.00':
                        $timezone6 = $selHTML;
                        break;
                    case '-6.00':
                        $timezone7 = $selHTML;
                        break;
                    case '-5.00':
                        $timezone8 = $selHTML;
                        break;
                    case '-4.00':
                        $timezone9 = $selHTML;
                        break;
                    case '-3.50':
                        $timezone10 = $selHTML;
                        break;
                    case '-3.00':
                        $timezone11 = $selHTML;
                        break;
                    case '-2.00':
                        $timezone12 = $selHTML;
                        break;
                    case '-1.00':
                        $timezone13 = $selHTML;
                        break;
                    case '1.00':
                        $timezone15 = $selHTML;
                        break;
                    case '2.00':
                        $timezone16 = $selHTML;
                        break;
                    case '3.00':
                        $timezone17 = $selHTML;
                        break;
                    case '3.50':
                        $timezone18 = $selHTML;
                        break;
                    case '4.00':
                        $timezone19 = $selHTML;
                        break;
                    case '4.50':
                        $timezone20 = $selHTML;
                        break;
                    case '5.00':
                        $timezone21 = $selHTML;
                        break;
                    case '5.50':
                        $timezone22 = $selHTML;
                        break;
                    case '5.75':
                        $timezone23 = $selHTML;
                        break;
                    case '6.00':
                        $timezone24 = $selHTML;
                        break;
                    case '6.50':
                        $timezone25 = $selHTML;
                        break;
                    case '7.00':
                        $timezone26 = $selHTML;
                        break;
                    case '8.00':
                        $timezone27 = $selHTML;
                        break;
                    case '9.00':
                        $timezone28 = $selHTML;
                        break;
                    case '9.50':
                        $timezone29 = $selHTML;
                        break;
                    case '10.00':
                        $timezone30 = $selHTML;
                        break;
                    case '11.00':
                        $timezone31 = $selHTML;
                        break;
                    case '12.00':
                        $timezone32 = $selHTML;
                        break;
                    case '13.00':
                        $timezone33 = $selHTML;
                        break;
                    case '0.00':
                    default:
                        $timezone14 = $selHTML;
                        break;
                }

                $themelist = array();
                $themelist[] = '<select name="thememem">';
                $themelist[] = '<option value="0">'.$lang['textusedefault'].'</option>';
                $query = $db->query("SELECT themeid, name FROM ".X_PREFIX."themes WHERE themestatus = 'on' ORDER BY name ASC");
                while ($themeinfo = $db->fetch_array($query))
                {
                    $themelist[] = '<option value="'.intval($themeinfo['themeid']).'">'.stripslashes($themeinfo['name']).'</option>';
                }
                $themelist[] = '</select>';
                $themelist = implode("\n", $themelist);
                $db->free_result($query);

                if ($CONFIG['predformat'] == 'on')
                {
                    $df = "<tr>\n\t<td bgcolor=\"$THEME[altbg1]\" class=\"tablerow\" width=\"22%\">$lang[dateformat1]</td>\n";
                }
                else
                {
                    $df = "<tr>\n\t<td bgcolor=\"$THEME[altbg1]\" class=\"tablerow\" width=\"22%\">$lang[dateformat2]</td>\n";
                }

                $df = $df."\t<td bgcolor=\"$THEME[altbg2]\" class=\"tablerow\"><select name=\"dateformat1\">\n";
                $querydf = $db->query("SELECT * FROM ".X_PREFIX."dateformats");
                while ($dformats = $db->fetch_array($querydf))
                {
                    if ($CONFIG['predformat'] == 'on')
                    {
                        $example = gmdate(formatDate($dformats['dateformat']), $ubblva + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
                    }
                    else
                    {
                        $example = $dformats['dateformat'];
                    }

                    if ($dformatorig == $dformats['dateformat'])
                    {
                        $df = $df."\t<option value=\"$dformats[dateformat]\" selected=\"selected\">$example</option>\n";
                    }
                    else
                    {
                        $df = $df."\t<option value=\"$dformats[dateformat]\">$example</option>\n";
                    }
                }
                $df = $df."\t</select>\n\t</td>\n</tr>";
                $db->free_result($querydf);

                $timeformatlist = array();
                $timeformatlist[] = '<select name="timeformat1">';
                $timeformatlist[] = '<option value="12" '.$timeFormat12Selected.'>'.gmdate("h:i A", $onlinetime + ($self['timeoffset'] * 3600) + $self['daylightsavings']).'</option>';
                $timeformatlist[] = '<option value="24" '.$timeFormat24Selected.'>'.gmdate("H:i", $onlinetime + ($self['timeoffset'] * 3600) + $self['daylightsavings']).'</option>';
                $timeformatlist[] = '</select>';
                $timeformatlist = implode("\n", $timeformatlist);

                $pwtd = '';
                if ($CONFIG['emailcheck'] == 'off')
                {
                    eval('$pwtd = "'.template('register_password').'";');
                }

                $captcha = $captcha_js = '';
                if ($CONFIG['captcha_status'] == 'on' && function_exists('ImageCreate'))
                {
                    eval('$captcha = "'.template('register_captcha').'";');
                    eval('$captcha_js = "'.template('register_captchajs').'";');
                }

                eval('echo stripslashes("'.template('register').'");');
            }
        }
        else
        {
            if (preg_match("/[\]\['".'",!@#~$%\^&*()+=\/\\\\:;?|.<>{}]/', $username))
            {
                error($lang['badusername'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            if (empty($username) || strlen($username) < 4 || strlen($username) > 25)
            {
                error($lang['usernamelimits'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            if ($CONFIG['ipreg'] == 'on')
            {
                $time = $onlinetime - 86400;
                $query = $db->query("SELECT uid FROM ".X_PREFIX."members WHERE regip = '$onlineip' AND regdate >= '$time'");
                if ($db->num_rows($query) >= 1)
                {
                    $db->free_result($query);
                    error($lang['reg_today'], true, '', '', 'register.php?action=reg', true, false, true);
                }
            }

            if ($member->exists($username))
            {
                error($lang['alreadyreg'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            if ($member->exists('', $email) && $CONFIG['doublee'] == 'off')
            {
                error($lang['alreadyreg'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            if ($CONFIG['emailcheck'] == 'on')
            {
                $password = '';
                $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnopqrstuvwxyz";
                mt_srand((double)microtime() * 1000000);
                $clen = strlen($chars)-1;
                for ($i = 0; $i < 8; $i++)
                {
                    $password .= $chars[mt_rand(0, $clen)];
                }
                $password2 = $password3 = $password;
            }

            $password = md5(trim($password));
            $password2 = md5(trim($password2));

            if ($password != $password2)
            {
                error($lang['pwnomatch'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            if (strlen($password) < 5)
            {
                error($lang['passwordlimits'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            $fail = $efail = false;
            $member->isRestricted($username, $email, $fail, $efail);

            if ($fail)
            {
                error($lang['restricted'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            if ($efail)
            {
                error($lang['emailrestricted'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            if (empty($email) || !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email))
            {
                error($lang['bademail'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            if (empty($password) || strpos($password, '"') !== false || strpos($password, "'") !== false)
            {
                error($lang['textpw1'], true, '', '', 'register.php?action=reg', true, false, true);
            }

            if ($CONFIG['captcha_status'] == 'on' && function_exists('ImageCreate'))
            {
                if (!empty($_SESSION['word_hash']) && !empty($captchaword) && md5(strtolower($captchaword)) == $_SESSION['word_hash'])
                {
                    $_SESSION['captcha_attempts'] = 0;
                    $_SESSION['word_hash'] = false;
                }
                else
                {
                    error($lang['captcha_wrong'], true, '', '', 'register.php?action=reg', true, false, true);
                }
            }

            if ($timeoffset1 < -12 || $timeoffset1 > 13)
            {
                $timeoffset1 = $CONFIG['def_tz'];
            }

            // Create the new user record
            $member->record['username'] = $username;
            $member->record['email'] = $email;
            $member->record['password'] = trim($password);
            $member->record['langfile'] = findLangName($langfilenew);
            $member->record['status'] = 'Member';
            $member->record['regdate'] = $onlinetime;
            $member->record['regip'] = $onlineip;
            $member->record['lastvisit'] = 0; // yet to log on
            $member->record['showemail'] = $showemail;
            $member->record['tpp'] = $tpp;
            $member->record['ppp'] = $ppp;
            $member->record['newsletter'] = $newsletter;
            $member->record['timeformat'] = $timeformat1;
            $member->record['dateformat'] = $dateformat1;
            $member->record['saveogpm'] = $saveogpm;
            $member->record['emailonpm'] = $emailonpm;
            $member->record['daylightsavings'] = $daylightsavings1;
            $member->record['psorting'] = $psorting;
            $member->record['viewsigs'] = $viewsigs;
            $member->record['expview'] = $expview;
            $member->record['viewavatars'] = $viewavatars;
            $member->record['timeoffset'] = $timeoffset1;

            $member->update();

            $config_cache->expire('settings');
            $moderators_cache->expire('moderators');
            $config_cache->expire('theme');
            $config_cache->expire('pluglinks');
            $config_cache->expire('whosonline');
            $config_cache->expire('forumjump');

            // If set, alert the admins that a new member has signed up
            switch ($CONFIG['notifyonreg'])
            {
                case 'pm':
                    notifyViapm($username);
                    break;
                case 'email':
                    notifyViaEmail($username);
                    break;
                default:
            }

            if ($CONFIG['pmwelcomestatus'] == 'on')
            {
                if (!empty($CONFIG['pmwelcomefrom']) && !empty($CONFIG['pmwelcomesubject']) && !empty($CONFIG['pmwelcomemessage']))
                {
                    $db->query("INSERT INTO ".X_PREFIX."pm (pmid, msgto, msgfrom, type, owner, folder, subject, message, dateline, readstatus, sentstatus, usesig) VALUES ('', '$username', '$CONFIG[pmwelcomefrom]', 'incoming', '$username', 'Inbox', '$CONFIG[pmwelcomesubject]', '$CONFIG[pmwelcomemessage]', '$onlinetime', 'no', 'yes', 'no')");
                }
            }

            if ($CONFIG['emailcheck'] == 'on')
            {
                eval('echo "'.template('header').'";');

                if (empty($CONFIG['adminemail'])) // The mail class can handle this error, but it'll describe it vaguely
                {
                    error($lang['noadminemail'], false, '', '', 'cp_board.php', true, false, true);
                }
                if (empty($CONFIG['bbname'])) // The mail class can handle this error, but it'll describe it vaguely
                {
                    error($lang['nobbname'], false, '', '', 'cp_board.php', true, false, true);
                }

                $messagebody = $lang['Thanks_You_Register']." \n$username\n$password3\n$CONFIG[boardurl]";

                $mailsys->setTo($email);
                $mailsys->setFrom($CONFIG['adminemail'], $CONFIG['bbname']);
                $mailsys->setSubject($lang['textyourpw']);
                $mailsys->setMessage($messagebody);
                $mailsys->Send();
            }
            else
            {
                $uid = $member->findUidByUsername($username);

                $currtime = $onlinetime + (86400*30);

                $authState->ubbuid = $uid;
                $authState->ubbuser = $username;
                $authState->ubbpw = $password;
                $authState->update();

                eval('echo "'.template('header').'";');
            }

            if ($CONFIG['emailcheck'] == 'on')
            {
                message($lang['Register_Thanks'], false, '', '', 'index.php', true, false, true);
            }
            else
            {
                message($lang['regged'], false, '', '', 'index.php', true, false, true);
            }
        }
        break;
    default:
        error($lang['textnoaction'], true, '', '', 'index.php', true, false, true);
        break;
}

loadtime();
eval('echo "'.template('footer').'";');
?>