<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2021 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2021 The XMB Development Team
 * https://forums.xmbforum2.com/
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
// phpcs:disable PSR1.Files.SideEffects
require_once 'header.php';

loadtpl('contactus');

$shadow = shadowfx();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

nav($lang['contactus']);
btitle($lang['contactus']);

eval('echo "' . template('header') . '";');

$oToken->assertToken(false);

if ($CONFIG['contactus'] == 'off') {
    error($lang['fnasorry'], false);
}

if (noSubmit('contactsubmit')) {
    $self['username'] = (isset($self['username']) ? $self['username'] : '');
    $self['email'] = (isset($self['email']) ? $self['email'] : '');
    eval('echo stripslashes("' . template('contactus') . '");');
}

if (onSubmit('contactsubmit')) {
    $name = stripslashes(formVar('name'));
    $email = stripslashes(formVar('email'));
    $subject = stripslashes(formVar('subject'));
    $message = stripslashes(formVar('message'));

    if (X_GUEST) {
        $name = $name . " (Guest)";
    }

    if (empty($name)) {
        error($lang['contactnonamefrom'], false, '', '', $contactLink, true, false, true);
    }

    if (empty($email)) {
        error($lang['contactnoemailfrom'], false, '', '', $contactLink, true, false, true);
    }

    if (empty($message)) {
        error($lang['contactnomessage'], false, '', '', $contactLink, true, false, true);
    }

    if (empty($subject)) {
        error($lang['contactnosubject'], false, '', '', $contactLink, true, false, true);
    }

    if (empty($CONFIG['adminemail'])) {
        error($lang['noadminemail'], false, '', '', 'cp_board.php', true, false, true);
    }

    // The mail class can handle this error, but it'll describe it vaguely
    if (empty($CONFIG['bbname'])) {
        error($lang['nobbname'], false, '', '', 'cp_board.php', true, false, true);
    }

    if (!empty($name) && !empty($email) && !empty($CONFIG['adminemail']) && !empty($CONFIG['bbname'])) {
        $mailSystem->setTo($CONFIG['adminemail']);
        $mailSystem->setFrom($email, $name);
        $mailSystem->setSubject('[' . $CONFIG['bbname'] . '] ' . $subject);
        $mailSystem->setMessage($message);
        $mailSystem->sendMail();

        message($lang['contactsubmitted'], false, '', '', 'index.php', true, false, true);
    }
}

loadtime();
eval('echo "' . template('footer') . '";'); // XXX
