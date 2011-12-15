<?php
/**
 * GaiaBB
 * Copyright (c) 2011 The GaiaBB Group
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
define('ROOT', './');

require_once(ROOT.'header.php');
require_once(ROOT.'include/validate.inc.php');

loadtpl('contactus');

$shadow = shadowfx();
$meta = metaTags();

eval('$css = "'.template('css').'";');

nav($lang['contactus']);
btitle($lang['contactus']);

eval('echo "'.template('header').'";');

$oToken->assert_token(false);

if ($CONFIG['contactus'] == 'off')
{
    error($lang['fnasorry'], false);
}

if (noSubmit('contactsubmit'))
{
    $self['username'] = (isset($self['username']) ? $self['username'] : '');
    $self['email'] = (isset($self['email']) ? $self['email'] : '');
    eval('echo stripslashes("'.template('contactus').'");');
}

if (onSubmit('contactsubmit'))
{
    $name = stripslashes(formVar('name'));
    $email = stripslashes(formVar('email'));
    $subject = stripslashes(formVar('subject'));
    $message = stripslashes(formVar('message'));

    if (X_GUEST)
    {
        $name = $name . " (Guest)";
    }

    if ( empty($name) )
    {
        error($lang['contactnonamefrom'], false, '', '', $contactLink, true, false, true);
    }
    
    if( empty($email) )
    {
        error($lang['contactnoemailfrom'], false, '', '', $contactLink, true, false, true);
    }
    
    if( empty($message) )
    {
        error($lang['contactnomessage'], false, '', '', $contactLink, true, false, true);
    }
    
    if( empty ($subject) )
    {
        error($lang['contactnosubject'], false, '', '', $contactLink, true, false, true);
    }

    if ( empty($CONFIG['adminemail']) ) // The mail class can handle this error, but it'll describe it vaguely
    {
        error($lang['noadminemail'], false, '', '', 'cp_board.php', true, false, true);
    }

    if ( empty($CONFIG['bbname']) ) // The mail class can handle this error, but it'll describe it vaguely
    {
        error($lang['nobbname'], false, '', '', 'cp_board.php', true, false, true);
    }
    
    if( !empty($name) && !empty($email) && !empty($CONFIG['adminemail']) && !empty($CONFIG['bbname']) )
    {
        $mailsys->setTo($CONFIG['adminemail']);
        $mailsys->setFrom($email, $name);
        $mailsys->setSubject('['.$CONFIG['bbname'].'] '.$subject);
        $mailsys->setMessage($message);
        $mailsys->Send();
    
        message($lang['contactsubmitted'], false, '', '', 'index.php', true, false, true);
    }
}

loadtime();
eval('echo "'.template('footer').'";');
?>
