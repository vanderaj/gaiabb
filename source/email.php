<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
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

require_once 'header.php';

loadtpl('email_member');

$shadow = shadowfx();
$meta = metaTags();

smcwcache();

eval('$css = "' . template('css') . '";');

nav($lang['emailmemnav']);
btitle($lang['emailmemnav']);

eval('echo "' . template('header') . '";');

if (X_GUEST) {
    error($lang['emailmemerror'], false);
}

$memberid = getInt('memberid');

if (noSubmit('emailsubmit')) {
    if ($memberid == '') {
        error($lang['emailmemnomem'], false);
    }

    $query = $db->query("SELECT uid, email, username, showemail FROM " .
        X_PREFIX . "members WHERE uid = '$memberid' AND status != 'Banned'");
    $sendto = $db->fetchArray($query);
    $db->freeResult($query);

    if (empty($sendto)) {
        error($lang['emailmemnoexist'], false);
    }

    if ($sendto['showemail'] != 'yes') {
        error($lang['emailmemerror'], false);
    }

    $member = trim($sendto['username']);

    eval('echo stripslashes("' . template('email_member') . '");');
}

if (onSubmit('emailsubmit')) {
    $query = $db->query("SELECT uid, email, username, showemail FROM " .
        X_PREFIX . "members WHERE uid = '$memberid' AND status != 'Banned'");
    $sendto = $db->fetchArray($query);
    $db->freeResult($query);

    if (empty($sendto)) {
        error($lang['emailmemnoexist'], false);
    }

    if ($sendto['showemail'] != 'yes') {
        error($lang['emailmemerror'], false);
    }

    $name = stripslashes(formVar('name'));
    $email = stripslashes(formVar('email'));
    $subject = stripslashes(formVar('subject'));
    $message = stripslashes(formVar('message'));

    if ($name == '') {
        error($lang['emailmemnoname'], false);
    }
    if ($email == '') {
        error($lang['emailmemnoemail'], false);
    }
    if ($subject == '') {
        error($lang['emailmemnosubject'], false);
    }
    if ($message == '') {
        error($lang['emailmemnomessage'], false);
    }

    $emailmsgurl = $CONFIG['boardurl'] . 'index.php';

    $tpl_keys = array(
        '{TO}',
        '{FROM}',
        '{MSG}',
    );
    $tpl_values = array(
        $sendto['username'],
        $name,
        $message,
    );
    $msgbody = str_replace($tpl_keys, $tpl_values, $lang['emailmemmsg']);

    $mailsys->setTo($sendto['email']);
    $mailsys->setFrom($email, $name);
    $mailsys->setSubject('[' . $CONFIG['bbname'] . '] ' . $subject);
    $mailsys->setMessage($msgbody);
    $mailsys->sendMail();

    message(
        $lang['emailmemsubmitted'],
        false,
        '',
        '',
        'viewprofile.php?memberid=' . intval($sendto['uid']),
        true,
        false,
        true
    );
}

loadtime();
eval('echo "' . template('footer') . '";');
