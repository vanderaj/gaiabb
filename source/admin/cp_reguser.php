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

nav('<a href="index.php">'.$lang['textcp'].'</a>');

eval('$css = "'.template('css').'";');

eval('echo "'.template('cp_header').'";');

if (!X_SADMIN)
{
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['superadminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML;
    ?>
    <form method="post" action="cp_reguser.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token()?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr>
    <td class="category" colspan="2"><strong><font color="<?php echo $THEME['cattext']?>"><?php echo $lang['reguser']?></font></strong></td>
    </tr>
    <tr>
    <td bgcolor="<?php echo $THEME['altbg1']?>" class="tablerow" width="22%"><?php echo $lang['regusername']?></td>
    <td bgcolor="<?php echo $THEME['altbg2']?>" class="tablerow"><input type="text" name="regusername" size="25" maxlength="25" /></td>
    </tr>
    <tr>
    <td bgcolor="<?php echo $THEME['altbg1']?>" class="tablerow" width="22%"><?php echo $lang['regemail']?></td>
    <td bgcolor="<?php echo $THEME['altbg2']?>" class="tablerow"><input type="text" name="regemail" size="25" /></td>
    </tr>
    <tr>
    <td bgcolor="<?php echo $THEME['altbg2']?>" class="tablerow" colspan="2" align="center"><input type="submit" class="submit" name="regusersubmit" value="<?php echo $lang['regsubmit']?>" />&nbsp;<input type="reset" value="<?php echo $lang['regclear']?>" /></td>
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
    global $THEME, $mailsys, $lang, $shadow2, $oToken, $db, $CONFIG, $onlinetime;

    $oToken->assert_token();

    $regusername = $db->escape(formVar('regusername'), -1, true);
    $regemail = $db->escape(formVar('regemail'), -1, true);

    $regdate = $db->time($onlinetime);

    if (empty($regusername))
    {
        cp_error($lang['regempty'], false, '', '</td></tr></table>');
    }

    if (empty($CONFIG['adminemail'])) // The mail class can handle this error, but it'll describe it vaguely
    {
        error($lang['noadminemail'], false, '', '', 'cp_reguser.php', true, false, true);
    }

    if (empty($CONFIG['bbname'])) // The mail class can handle this error, but it'll describe it vaguely
    {
        error($lang['nobbname'], false, '', '', 'cp_reguser.php', true, false, true);
    }

    if ($CONFIG['doublee'] == 'off' && false !== strpos($regemail, "@"))
    {
        $email1 = ", email";
        $email2 = "OR email='$regemail'";
    }
    else
    {
        $email1 = $email2 = '';
    }

    if (preg_match('#^[-a-z0-9_]*$#i', $regusername) == false)
    {
        cp_error($lang['badusername'], false, '', '</td></tr></table>');
    }

    $query = $db->query("SELECT username$email1 FROM ".X_PREFIX."members WHERE username = '$regusername' $email2");
    $usercheck = $db->num_rows($query);
    $db->free_result($query);

    if (!($usercheck == 0))
    {
        cp_error($lang['regcheck'], false, '', '</td></tr></table>');
    }

    $fail = $efail = false;
    $query = $db->query("SELECT * FROM ".X_PREFIX."restricted");
    while ($restriction = $db->fetch_array($query))
    {
        if ($restriction['case_sensitivity'] == 1)
        {
            if ($restriction['partial'] == 1)
            {
                if (strpos($regusername, $restriction['name']) !== false)
                {
                    $fail = true;
                }

                if (strpos($regemail, $restriction['name']) !== false)
                {
                    $efail = true;
                }
            }
            else
            {
                if ($regusername == $restriction['name'])
                {
                    $fail = true;
                }

                if ($regemail == $restriction['name'])
                {
                    $efail = true;
                }
            }
        }
        else
        {
            $t_username = strtolower($regusername);
            $t_email = strtolower($regemail);
            $restriction['name'] = strtolower($restriction['name']);

            if ($restriction['partial'] == 1)
            {
                if (strpos($t_username, $restriction['name']) !== false)
                {
                    $fail = true;
                }

                if (strpos($t_email, $restriction['name']) !== false)
                {
                    $efail = true;
                }
            }
            else
            {
                if ($t_username == $restriction['name'])
                {
                    $fail = true;
                }

                if ($t_email == $restriction['name'])
                {
                    $efail = true;
                }
            }
        }
    }
    $db->free_result($query);

    if ($efail || $fail)
    {
        cp_error($lang['regerestricted'], false, '', '</td></tr></table>');
    }

    if (empty($regemail) || isValidEmail($regemail) == false)
    {
        cp_error($lang['regbademail'], false, '', '</td></tr></table>');
    }

    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789abcdefghjkmnpqrstuvwxyz';
    $regpassword = '';
    mt_srand((double)microtime() * 1000000);
    $max = mt_rand(8, 12);
    for ($get = strlen($chars), $i=0; $i < $max; $i++)
    {
        $regpassword .= $chars[mt_rand(0, $get)];
    }
    $newmd5pass = md5(trim($regpassword));

    $db->query("INSERT INTO ".X_PREFIX."members (username, password, regdate, email, status, showemail, theme, langfile, timeformat, dateformat, mood, pwdate, tpp, ppp, saveogpm, emailonpm) VALUES ('$regusername', '$newmd5pass', $regdate, '$regemail', 'Member', 'no', '0', 'English', 24, 'dd-mm-yyyy', '', $regdate, 30, 30, 'yes', 'no');");

    $mailsys->setTo($regemail);
    $mailsys->setFrom($CONFIG['adminemail'], $CONFIG['bbname']);
    $mailsys->setSubject('['.$CONFIG['bbname'].'] '.$lang['textyourpw']);
    $mailsys->setMessage($lang['textyourpwis']."\n\n".$regusername."\n".$regpassword);

    if (!$mailsys->Send())
    {
        $uid = $db->insert_id();
        if ($uid > 0)
        {
            $db->query("DELETE FROM ".X_PREFIX."members WHERE uid = ".$uid);
        }
        cp_message($lang['reguserfail'], false, '', '</td></tr></table>', 'cp_reguser.php', true, false, true);
    }

    cp_message($lang['regusersuccess'], false, '', '</td></tr></table>', 'cp_reguser.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('regusersubmit'))
{
   viewPanel();
}

if (onSubmit('regusersubmit'))
{
    doPanel();
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>