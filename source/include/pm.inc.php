<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2017 The GaiaBB Group
 * http://www.GaiaBB.com
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
 * http://forums.xmbforum2.com/
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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

require_once('mimetypes.inc.php');

class pmDAO
{

    function pmDAO()
    {
    }

    function insert_pm($to, $from, $to_uid, $from_uid, $type, $owner, $folder, $subject, $message, $isRead, $isSent, $usepmsig)
    {
        global $db, $onlinetime;

        $to = $db->escape($to, -1, true);
        $from = $db->escape($from, -1, true);
        $to_uid = $db->escape($to_uid);
        $from_uid = $db->escape($from_uid);
        $type = $db->escape($type);
        $owner = $db->escape($owner, -1, true);
        $folder = $db->escape($folder);
        $subject = $db->escape($subject);
        $message = $db->escape($message);
        $isRead = $db->escape($isRead);
        $isSent = $db->escape($isSent);
        $usepmsig = $db->escape($usepmsig);

        $result = $db->query("INSERT INTO " . X_PREFIX . "pm (pmid, msgto, msgfrom, msgto_uid, msgfrom_uid, type, owner, folder, subject, message, dateline, readstatus, sentstatus, usesig) VALUES ('', '$to', '$from', '$to_uid', '$from_uid', '$type', '$owner', '$folder', '$subject', '$message', '$onlinetime', '$isRead', '$isSent', '$usepmsig')");
        if ($result == false) {
            return false;
        }
        return $db->insert_id();
    }
}

class pmModel
{

    function pmModel()
    {
    }

    function send($pmid, $msgto, $subject, $message, $pmpreview)
    {
        global $db, $self, $lang, $CONFIG, $THEME, $username, $pmcount, $cheHTML;
        global $shadow, $shadow2, $onlinetime, $fileheight, $filewidth;
        global $attachedfile, $filetype, $filesize, $filename;
        global $usesig, $usesigcheck, $selHTML;
        global $oToken;

        if (isset($self['ban']) && $self['ban'] == 'pm' || $self['ban'] == 'both') {
            error($lang['textbanfrompm'], false, '', '', false, true, false, true);
        }

        // do pm quota
        if (!X_STAFF && $pmcount >= $CONFIG['pmquota'] && $CONFIG['pmquota'] > 0) {
            error($lang['pmreachedquota'], false, '', '', false, true, false, true);
        }

        // show signature check box
        $usesig = (isset($usesig) && $usesig == 'yes') ? 'yes' : 'no';
        if ($usesig == 'yes') {
            $usesigcheck = $cheHTML;
            $pmcheckhtml = '<br /><input type="checkbox" name="usesig" value="yes" ' . $usesigcheck . ' /> ' . $lang['textusesig'];
        } else
            if (isset($self['sig']) && !empty($self['sig'])) {
                $usesigcheck = $cheHTML;
                $pmcheckhtml = '<br /><input type="checkbox" name="usesig" value="yes" ' . $usesigcheck . ' /> ' . $lang['textusesig'];
            } else {
                $usesigcheck = $pmcheckhtml = '';
            }

        if (onSubmit('savesubmit')) {
            if (empty($message) || empty($subject)) {
                error($lang['pmempty'], false, '', '', 'pm.php', true, false, true);
            }
            $pm_dao = new pmDAO();
            $pm_dao->insert_pm('', '', '', '', 'draft', $self['username'], 'Drafts', $subject, $message, 'yes', 'no', $usesig);
            message($lang['imsavedmsg'], false, '', '', 'pm.php?folder=Drafts', true, false, true);
        }

        if (onSubmit('sendsubmit')) {
            $errors = '';
            if (empty($message) || empty($subject)) {
                error($lang['pmempty'], false, '', '', 'pm.php', true, false, true);
            }
            if (!X_ADMIN) {
                if ($db->result($db->query("SELECT COUNT(pmid) FROM " . X_PREFIX . "pm WHERE msgfrom = '$self[username]' AND dateline > " . ($onlinetime - $CONFIG['floodctrl'])), 0) > 0) {
                    error($lang['floodprotect_pm'], false, '', '', 'pm.php', true, false, true);
                }
            }

            if (strstr($msgto, ',') && X_STAFF) {
                $errors = $this->send_multi_recp($msgto, $subject, $message, $usesig);
            } else {
                $errors = $this->send_recp($msgto, $subject, $message, $usesig);
            }

            if (empty($errors)) {
                message($lang['imsentmsg'], false, '', '', 'pm.php', true, false, true);
            } else {
                message(substr($errors, 6), false, '', '', 'pm.php', true, false, true);
            }
        } else {
            // create address book drop down
            $addresses = array();
            $query = $db->query("SELECT * FROM " . X_PREFIX . "addresses WHERE username = '$self[username]' ORDER BY addressname ASC");
            while (($address = $db->fetch_array($query)) != false) {
                $addresses[] = '<option value="' . $address['addressname'] . '">' . stripslashes($address['addressname']) . '</option>';
            }
            $addresses = implode("\n", $addresses);
        }

        // show attachment upload box if set to on
        $attachfile = '';
        if ($CONFIG['pmattachstatus'] == 'on') {
            eval('$attachfile = "' . template('pm_attachmentbox') . '";');
        }

        if ($pmid > 0 && noSubmit('previewsubmit')) {
            $query = $db->query("SELECT subject, msgfrom, message FROM " . X_PREFIX . "pm WHERE pmid = '$pmid' AND owner = '$self[username]'");
            $quote = $db->fetch_array($query);

            $reply = getVar('reply');
            $forward = getVar('forward');

            if ($quote) {
                $prefixes = array(
                    $lang['textre'],
                    $lang['textfwd']
                );
                $subject = checkOutput(str_replace($prefixes, '', $quote['subject']));
                $message = checkOutput($quote['message']);
                if ($forward == 'yes') {
                    $subject = $lang['textfwd'] . ' ' . $subject;
                    $message = '[quote][i]' . $lang['origpostedby'] . ' ' . $quote['msgfrom'] . "[/i]\n" . $message . '[/quote]';
                } else
                    if ($reply == 'yes') {
                        $subject = $lang['textre'] . ' ' . $subject;
                        $message = '[quote]' . $message . '[/quote]';
                        $username = $quote['msgfrom'];
                    }
            }
            $db->free_result($query);
        } else
            if (onSubmit('previewsubmit')) {
                if (empty($message)) {
                    error($lang['pmempty'], false, '', '', false, true, false, true);
                }
                $pmsubject = checkOutput(censor(checkInput($subject)));
                $pmmessage = postify(checkInput($message));
                $username = checkOutput(checkInput($msgto));
                // show signature preview
                if ($usesig != 'no') {
                    eval('$pmmessage .= "' . template('pm_send_preview_sig') . '";');
                }
                eval('$pmpreview = "' . template('pm_send_preview') . '";');
            }
        $smilieinsert = smilieinsert();
        $bbcodeinsert = bbcodeinsert();
        $leftpane = '';
        eval('$leftpane = "' . template('pm_send') . '";');
        return $leftpane;
    }

    function send_multi_recp($msgto, $subject, $message, $usepmsig)
    {
        $errors = '';
        $recipients = array_unique(array_map('trim', explode(',', $msgto)));
        foreach ($recipients as $recp) {
            $errors .= $this->send_recp($recp, $subject, $message, $usepmsig);
        }
        return $errors;
    }

    function send_recp($msgto, $subject, $message, $usepmsig)
    {
        global $db, $mailsys, $self, $CONFIG, $lang, $onlinetime, $username;
        global $attachedfile, $filetype, $filesize, $filename;
        global $usesig, $usesigcheck, $fileheight, $filewidth;

        $errors = '';

        $pm_dao = new pmDao();

        $msgto = $db->escape(checkInput($msgto), -1, true);
        $query = $db->query("SELECT username, uid, email, ignorepm, emailonpm, langfile, status FROM " . X_PREFIX . "members WHERE username = '$msgto'");
        if (($rcpt = $db->fetch_array($query)) != false) {
            $ilist = array_map('trim', explode(',', $rcpt['ignorepm']));
            if (!in_array($self['username'], $ilist) || X_ADMIN) {
                $username = $rcpt['username'];
                $usr_uid = $rcpt['uid'];
                $thislangfile = $rcpt['langfile'];

                if (isset($_FILES['attach']['name'])) {
                    // The external check alone will fail if exceeds the size. ~martijn
                    if ($_FILES['attach']['error'] === 1 || $_FILES['attach']['error'] === 2) {
                        error($lang['attachtoobig'], false, '', '', false, true, false, true);
                    }

                    if (isset($_FILES['attach']) && ($attachedfile = $this->getAttachment($_FILES['attach'], $CONFIG['pmattachstatus'], $CONFIG['max_attach_size'])) !== false) {
                        $next_pmid = $db->result($db->query("SELECT last_insert_id(pmid+1) FROM " . X_PREFIX . "pm ORDER BY pmid DESC LIMIT 0,1"), 0);
                        $db->query("INSERT INTO " . X_PREFIX . "pm_attachments (aid, pmid, filename, filetype, filesize, fileheight, filewidth, attachment, owner) VALUES " . "('', '" . $next_pmid . "', " . "'" . $db->escape($filename) . "', " . "'" . $db->escape($filetype) . "', " . "'" . intval($filesize) . "', " . "'" . intval($fileheight) . "', " . "'" . intval($filewidth) . "', " . "'" . $db->escape($attachedfile) . "', " . "'" . $db->escape($username) . "')");
                    }
                }

                $pm_dao->insert_pm($username, $self['username'], $usr_uid, $self['uid'], 'incoming', $username, 'Inbox', $subject, $message, 'no', 'yes', $usepmsig);

                if (isset($self['saveogpm']) && $self['saveogpm'] == 'yes') {
                    $pm1 = $pm_dao->insert_pm($username, $self['username'], $usr_uid, $self['uid'], 'outgoing', $self['username'], 'Outbox', $subject, $message, 'no', 'yes', $usepmsig);
                    if (isset($_FILES['attach']) && ($attachedfile = $this->getAttachment($_FILES['attach'], $CONFIG['pmattachstatus'], $CONFIG['max_attach_size'])) !== false) {
                        $db->query("INSERT INTO " . X_PREFIX . "pm_attachments (aid, pmid, filename, filetype, filesize, fileheight, filewidth, attachment, owner) VALUES " . "(''," . "'" . $pm1 . "'", "'" . $db->escape($filename) . "', " . "'" . $db->escape($filetype) . "', " . "'" . intval($filesize) . "', " . "'" . intval($fileheight) . "', " . "'" . intval($filewidth) . "', " . "'" . $db->escape($attachedfile) . "', " . "'" . $db->escape($self[username]) . "')");
                    }
                }

                if ($rcpt['emailonpm'] == 'yes' && $rcpt['status'] != 'Banned') {
                    // Force a langswitch (1)
                    $langfile = langswitch('no', $thislangfile);
                    include('lang/' . $langfile . '.lang.php');

                    $pmurl = $CONFIG['boardurl'] . 'pm.php';

                    $tpl_keys = array(
                        '{TO}',
                        '{FROM}',
                        '{LINK}'
                    );
                    $tpl_values = array(
                        $rcpt['username'],
                        $self['username'],
                        $pmurl
                    );
                    $msgbody = str_replace($tpl_keys, $tpl_values, $lang['textnewpmbody']);

                    $mailsys->setTo($rcpt['email']);
                    $mailsys->setFrom($CONFIG['adminemail'], $CONFIG['bbname']);
                    $mailsys->setSubject('[' . $CONFIG['bbname'] . '] ' . $lang['textnewpmemail']);
                    $mailsys->setMessage($msgbody);
                    $mailsys->Send();

                    // Force a revert langswitch (1)
                    $langfile = langswitch('yes', '');
                    include('lang/' . $langfile . '.lang.php');
                }
            } else {
                $errors = '<br />' . $lang['pmblocked'];
            }
        } else {
            $errors = '<br />' . $lang['badrcpt'];
        }
        $db->free_result($query);
        return $errors;
    }

    function getAttachment($file, $pmattachstatus)
    {
        global $db, $lang, $filename, $filetype, $filesize, $CONFIG, $fileheight, $filewidth;
        global $attachedfile;

        $filename = $filetype = $fileheight = $filewidth = '';
        $filesize = 0;

        if (is_array($file) && $file['name'] != 'none' && !empty($file['name']) && $CONFIG['pmattachstatus'] != 'off' && is_uploaded_file($file['tmp_name'])) {
            if (!isValidFilename($file['name'])) {
                error($lang['invalidFilename'], false, '', '', false, true, false, true);
                return false;
            }
            $filesize = intval(filesize($file['tmp_name']));
            if ($filesize > ($CONFIG['max_attach_size'])) {
                error($lang['attachtoobig'], false, '', '', false, true, false, true);
                return false;
            }
            $attachment = addslashes(fread(fopen($file['tmp_name'], 'rb'), $filesize));
            $filename = checkInput($file['name']);
            $filetype = checkInput($file['type']);
            $extension = strtolower(substr(strrchr($file['name'], '.'), 1));
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png' || $extension == 'bmp') {
                $exsize = getimagesize($file['tmp_name']);
                $fileheight = $exsize[1];
                $filewidth = $exsize[0];
            }
            if ($filesize !== 0) {
                return $attachment;
            }
        }
        return false;
    }

    function view($pmid, $folders)
    {
        global $db, $THEME, $lang, $self;
        global $cheHTML, $sendoptions, $shadow, $shadow2, $CONFIG, $pmsig, $fileheight, $filewidth;
        global $attachedfile, $filetype, $filesize, $filename, $lang_align, $lang_nalign;
        global $usesig, $usesigcheck, $n_height, $n_width, $oToken;

        $delchecked = '';

        if (!($pmid > 0)) {
            error($lang['textnonechosen'], false, '', '', 'pm.php', true, false, true);
        }

        $query = $db->query("SELECT * FROM " . X_PREFIX . "pm WHERE pmid = '$pmid' AND owner = '$self[username]'");
        $pm = $db->fetch_array($query);

        if ($pm) {
            $query = $db->query("SELECT * FROM " . X_PREFIX . "pm_attachments WHERE pmid = '$pmid' AND owner = '$self[username]'");
            if ($db->num_rows($query) > 0) {
                $pm = array_merge($pm, $db->fetch_array($query));
            }

            if ($pm['type'] == 'incoming') {
                $db->query("UPDATE " . X_PREFIX . "pm SET readstatus = 'yes' WHERE pmid = $pm[pmid] OR (pmid = $pm[pmid]+1 AND type = 'outgoing' AND msgto = '$self[username]')");
            } else
                if ($pm['type'] == 'draft') {
                    $db->query("UPDATE " . X_PREFIX . "pm SET readstatus = 'yes' WHERE pmid = $pm[pmid]");
                }

            if (empty($pm['subject'])) {
                $pm['subject'] = $lang['textnosub'];
            }

            $adjTime = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
            $pmdate = gmdate($self['dateformat'], $pm['dateline'] + $adjTime);
            $pmtime = gmdate($self['timecode'], $pm['dateline'] + $adjTime);
            $pmdateline = $pmdate . ' ' . $lang['textat'] . ' ' . $pmtime;
            $pmsubject = checkOutput(censor($pm['subject']));
            $pmmessage = pmTempAmp(postify(checkOutput($pm['message'])));
            $pmfolder = $pm['folder'];
            $pmfrom = '<a href="viewprofile.php?memberid=' . rawurlencode($pm['msgfrom_uid']) . '" target="mainwindow">' . $pm['msgfrom'] . '</a>';
            $pmto = ($pm['type'] == 'draft') ? $lang['textpmnotsent'] : '<a href="viewprofile.php?memberid=' . rawurlencode($pm['msgto_uid']) . '" target="mainwindow">' . $pm['msgto'] . '</a>';

            if ($pm['type'] == 'draft') {
                $sendoptions = '<input type="radio" name="mod" value="send" /> ' . $lang['textpm'] . '<br />';
                $delchecked = $cheHTML;
            } else
                if ($pm['msgfrom'] != $self['username']) {
                    $sendoptions = '<input type="radio" name="mod" value="reply" ' . $cheHTML . ' /> ' . $lang['textreply'] . '<br /><input type="radio" name="mod" value="forward" /> ' . $lang['textforward'] . '<br />';
                } else {
                    $delchecked = $cheHTML;
                }

            // make the attachment output clean and understandable here.
            if (!empty($pm['filename']) && $CONFIG['pmattachstatus'] == 'on') {
                $attachsize = $pm['filesize'];
                if ($attachsize >= 1073741824) {
                    $attachsize = round($attachsize / 1073741824 * 100) / 100 . 'gb';
                } else
                    if ($attachsize >= 1048576) {
                        $attachsize = round($attachsize / 1048576 * 100) / 100 . 'mb';
                    } else
                        if ($attachsize >= 1024) {
                            $attachsize = round($attachsize / 1024 * 100) / 100 . 'kb';
                        } else {
                            $attachsize = $attachsize . 'b';
                        }

                $pm['filename'] = htmlspecialchars($pm['filename']);
                $extension = strtolower(substr(strrchr($pm['filename'], '.'), 1));
                if ($CONFIG['attachimgpost'] == 'on' && ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png' || $extension == 'bmp')) {
                    if ($pm['fileheight'] != '' && $pm['filewidth'] != '') {
                        $CONFIG['max_attheight'] = (int)$CONFIG['max_attheight'];
                        $CONFIG['max_attwidth'] = (int)$CONFIG['max_attwidth'];
                        $h_ratio = $CONFIG['max_attheight'] / $pm['fileheight'];
                        $w_ratio = $CONFIG['max_attwidth'] / $pm['filewidth'];
                        if (($pm['fileheight'] <= $CONFIG['max_attheight']) && ($pm['filewidth'] <= $CONFIG['max_attwidth'])) {
                            $n_height = $pm['fileheight'];
                            $n_width = $pm['filewidth'];
                        } else
                            if (($w_ratio * $pm['fileheight']) < $CONFIG['max_attheight']) {
                                $n_height = ceil($w_ratio * $pm['fileheight']);
                                $n_width = $CONFIG['max_attwidth'];
                            } else {
                                $n_height = $CONFIG['max_attheight'];
                                $n_width = ceil($h_ratio * $pm['filewidth']);
                            }
                    }

                    // create attachment icon if any
                    $attachicon = '';
                    if ($CONFIG['attachicon_status'] == 'on') {
                        $attachicon = getMimeType($extension);
                    }
                    eval('$pmmessage .= "' . template('pm_attachmentimage') . '";');
                } else {
                    // create attachment icon if any
                    $attachicon = '';
                    if ($CONFIG['attachicon_status'] == 'on') {
                        $attachicon = getMimeType($extension);
                    }
                    eval('$pmmessage .= "' . template('pm_attachment') . '";');
                }
            }

            // project signature or pms
            if ($pm['usesig'] == 'yes') {
                $tquery = $db->query("SELECT sig FROM " . X_PREFIX . "members WHERE username = '$pm[msgfrom]'");
                $pmsig = $db->fetch_array($tquery);
                $db->free_result($tquery);
                $pmsig['sig'] = censor($pmsig['sig']);
                $pmsig['sig'] = stripslashes($pmsig['sig']);
                $pmsig['sig'] = postify($pmsig['sig'], 'no', 'no', 'yes', $CONFIG['sigbbcode']);
                eval('$pmmessage .= "' . template('pm_sig') . '";');
            }

            $mtofolder = array();
            $mtofolder[] = '<select name="tofolder"><option value="">' . $lang['textpickfolder'] . '</option>';
            foreach ($folders as $key => $value) {
                if (is_numeric($key)) {
                    $key = $value;
                }
                $mtofolder[] = '<option value="' . $key . '">' . $value . '</option>';
            }
            $mtofolder[] = '</select>';
            $mtofolder = implode("\n", $mtofolder);
        } else {
            error($lang['pmadmin_noperm'], false, '', '', false, true, false, true);
        }
        $db->free_result($query);

        $leftpane = '';
        eval('$leftpane = "' . template('pm_view') . '";');
        return $leftpane;
    }

    function pm_print($pmid, $eMail = false)
    {
        global $mailsys, $db, $self, $lang_code, $lang_dir, $versionpowered, $lang, $charset, $THEME, $CONFIG, $logo;

        if (!($pmid > 0)) {
            error($lang['textnonechosen'], false, '', '', 'pm.php', true, false, true);
        }

        $query = $db->query("SELECT * FROM " . X_PREFIX . "pm WHERE pmid = '$pmid' AND owner = '$self[username]'");
        $pm = $db->fetch_array($query);
        $db->free_result($query);
        if ($pm) {
            $adjTime = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
            $pmdate = gmdate($self['dateformat'], $pm['dateline'] + $adjTime);
            $pmtime = gmdate($self['timecode'], $pm['dateline'] + $adjTime);
            $pmdateline = $pmdate . ' ' . $lang['textat'] . ' ' . $pmtime;
            $pmsubject = checkOutput(censor($pm['subject']));
            $pmmessage = stripslashes(trim($pm['message']));
            $pmmessage = str_replace('[quote]', "\n---- QUOTE ----\n", $pmmessage);
            $pmmessage = str_replace('[/quote]', "\n---- END QUOTE ----\n", $pmmessage);
            $pmfolder = $pm['folder'];
            $pmfrom = $pm['msgfrom'];
            $pmto = ($pm['type'] == 'draft') ? $lang['textpmnotsent'] : $pm['msgto'];

            if ($eMail) {
                $tpl_keys = array(
                    '{TO}',
                    '{FROM}',
                    '{MSG}',
                    '{SENT}',
                    '{FOLDER}',
                    '{SUBJECT}'
                );
                $tpl_values = array(
                    $pmto,
                    $pmfrom,
                    $pmmessage,
                    $pmdateline,
                    $pmfolder,
                    $pmsubject
                );
                $msgbody = str_replace($tpl_keys, $tpl_values, $lang['textpmtoemailmsg']);

                $mailsys->setTo($self['email']);
                $mailsys->setFrom($CONFIG['adminemail'], $CONFIG['bbname']);
                $mailsys->setSubject($lang['textpmtoemail'] . ' ' . $pmsubject);
                $mailsys->setMessage($msgbody);
                $mailsys->Send();

                message($lang['contactsubmitted'], false, '', '', 'index.php', true, false, true);
            } else {
                eval('echo stripslashes("' . template('pm_printable') . '");');
                exit();
            }
        } else {
            error($lang['pmadmin_noperm'], false, '', '', false, true, false, true);
        }
    }

    function delete($pmid)
    {
        global $db, $self, $lang, $THEME;

        $folder = $_SESSION['folder'];

        if (!($pmid > 0)) {
            error($lang['textnonechosen'], false, '', '', 'pm.php', true, false, true);
        }

        if ($folder == 'Trash') {
            $db->query("DELETE FROM " . X_PREFIX . "pm WHERE pmid = '$pmid' AND owner = '$self[username]'");
            $db->query("DELETE FROM " . X_PREFIX . "pm_attachments WHERE pmid = '$pmid' AND owner = '$self[username]'");
        } else {
            $db->query("UPDATE " . X_PREFIX . "pm SET folder = 'Trash' WHERE pmid = '$pmid' AND owner = '$self[username]'");
        }

        message($lang['imdeletedmsg'], false, '', '', 'pm.php?folder=' . $folder, true, false, true);
    }

    function mod_delete($pm_select)
    {
        global $db, $self, $lang, $CONFIG, $THEME;
        global $attachedfile, $filetype, $filesize, $filename;
        global $usesig, $usesigcheck, $fileheight, $filewidth;

        $folder = $_SESSION['folder'];

        $in = '';
        foreach ($pm_select as $key => $value) {
            $value = valInt($value);
            $in .= (empty($in)) ? "$value" : ", $value";
        }

        if ($folder == 'Trash') {
            $db->query("DELETE FROM " . X_PREFIX . "pm WHERE pmid IN($in) AND owner = '$self[username]'");
            $db->query("DELETE FROM " . X_PREFIX . "pm_attachments WHERE pmid IN($in) AND owner = '$self[username]'");
        } else {
            $db->query("UPDATE " . X_PREFIX . "pm SET folder = 'Trash' WHERE pmid IN($in) AND owner = '$self[username]'");
        }

        message($lang['imdeletedmsg'], false, '', '', 'pm.php?folder=' . $folder, true, false, true);
    }

    function move($pmid, $tofolder)
    {
        global $db, $self, $lang, $folders, $type, $CONFIG, $THEME;
        global $attachedfile, $filetype, $filesize, $filename;
        global $usesig, $usesigcheck, $fileheight, $filewidth;

        $folder = $_SESSION['folder'];

        if (!($pmid > 0)) {
            error($lang['textnonechosen'], false, '', '', "pm.php", true, false, true);
        }

        if (empty($tofolder)) {
            error($lang['textnofolder'], false, '', '', "pm.php?action=view&amp;pmid=$pmid", true, false, true);
        } else {
            if (!(in_array($tofolder, $folders) || $tofolder == 'Inbox' || $tofolder == 'Outbox' || $tofolder == 'Drafts') || ($tofolder == 'Inbox' && ($type == 'draft' || $type == 'outgoing')) || ($tofolder == 'Outbox' && ($type == 'incoming' || $type == 'draft')) || ($tofolder == 'Drafts' && ($type == 'incoming' || $type == 'outgoing'))) {
                error($lang['textcantmove'], false, '', '', 'pm.php?action=view&amp;pmid=' . $pmid . '', true, false, true);
            }

            $db->query("UPDATE " . X_PREFIX . "pm SET folder = '$tofolder' WHERE pmid = '$pmid' AND owner = '$self[username]'");

            message($lang['textmovesucc'], false, '', '', 'pm.php?folder=' . $folder, true, false, true);
        }
    }

    function mod_move($tofolder, $pm_select)
    {
        global $db, $self, $lang, $folders, $CONFIG, $THEME;
        global $attachedfile, $filetype, $filesize, $filename;
        global $usesig, $usesigcheck, $fileheight, $filewidth;

        $folder = $_SESSION['folder'];

        $in = '';
        foreach ($pm_select as $value) {
            $value = valInt($value);
            $type = formVar('type' . $value);
            if ((in_array($tofolder, $folders) || $tofolder == 'Inbox' || $tofolder == 'Outbox' || $tofolder == 'Drafts') && !($tofolder == 'Inbox' && ($type == 'draft' || $type == 'outgoing')) && !($tofolder == 'Outbox' && ($type == 'incoming' || $type == 'draft')) && !($tofolder == 'Drafts' && ($type == 'incoming' || $type == 'outgoing'))) {
                $in .= (empty($in)) ? "$value" : ",$value";
            }
        }

        if (empty($in)) {
            error($lang['textcantmove'], false, '', '', 'pm.php', true, false, true);
        }

        $db->query("UPDATE " . X_PREFIX . "pm SET folder = '$tofolder' WHERE pmid IN($in) AND owner = '$self[username]'");

        message($lang['textmovesucc'], false, '', '', 'pm.php?folder=' . $folder, true, false, true);
    }

    function markUnread($pmid, $type)
    {
        global $db, $self, $lang, $CONFIG, $THEME;

        $folder = $_SESSION['folder'];

        if (!($pmid > 0)) {
            error($lang['textnonechosen'], false, '', '', 'pm.php', true, false, true);
        }

        if ($type == 'outgoing') {
            error($lang['textnomur'], false, '', '', 'pm.php', true, false, true);
        }

        $db->query("UPDATE " . X_PREFIX . "pm SET readstatus = 'no' WHERE pmid = $pmid AND owner = '$self[username]'");

        message($lang['textmarkedunread'], false, '', '', 'pm.php?folder=' . $folder, true, false, true);
    }

    function mod_markUnread($pm_select)
    {
        global $db, $lang, $self, $CONFIG, $THEME;
        global $attachedfile, $filetype, $filesize, $filename;
        global $usesig, $usesigcheck, $fileheight, $filewidth, $pmid;

        $pmid = intval($pmid);
        $folder = $_SESSION['folder'];

        if (empty($folder)) {
            error($lang['textnofolder'], false, '', '', 'pm.php?action=view&amp;pmid=' . $pmid . '', true, false, true);
        }

        if ($pm_select == '') {
            error($lang['textnonechosen'], false, '', '', 'pm.php', true, false, true);
        }

        $in = '';

        foreach ($pm_select as $value) {
            if (formVar('type' . $value) != 'outgoing') {
                $value = valInt($value);
                $in .= (empty($in)) ? "$value" : ",$value";
            }
        }

        if (empty($in)) {
            error($lang['textnonechosen'], false, '', '', 'pm.php', true, false, true);
        }

        $db->query("UPDATE " . X_PREFIX . "pm SET readstatus = 'no' WHERE pmid IN($in) AND owner = '$self[username]'");

        message($lang['textmarkedunread'], false, '', '', 'pm.php?folder=' . $folder, true, false, true);
    }

    function updateFolders($pmfolders, $folders)
    {
        global $db, $lang, $self, $farray, $CONFIG, $THEME;
        global $attachedfile, $filetype, $filesize, $filename;
        global $usesig, $usesigcheck, $fileheight, $filewidth;

        $error = '';
        $newfolders = explode(',', $pmfolders);
        foreach ($newfolders as $key => $value) {
            $newfolders[$key] = checkInput($value);
            if (empty($newfolders[$key])) {
                unset($newfolders[$key]);
            }
        }

        foreach ($folders as $value) {
            if (isset($farray[$value]) && $farray[$value] != 0 && !in_array($value, $newfolders) && !in_array($value, array(
                    'Inbox',
                    'Outbox',
                    'Drafts',
                    'Trash'
                ))
            ) {
                $newfolders[] = checkInput($value);
                $error .= (empty($error)) ? '<br />' . $lang['foldersupdateerror'] . ' ' . $value : ', ' . $value;
            }
        }

        $pmfolders = $db->escape(implode(', ', $newfolders));

        $db->query("UPDATE " . X_PREFIX . "members SET pmfolders = '$pmfolders' WHERE username = '$self[username]'");

        message($lang['foldersupdate'] . $error, false, '', '', 'pm.php?folder=Inbox', true, false, true);
    }

    function viewIgnoreList()
    {
        global $self, $lang, $db;
        global $THEME, $shadow2, $oToken;

        $leftpane = '';
        $ignorelist = formVar('ignorelist');
        if (onSubmit('ignoresubmit')) {
            $self['ignorepm'] = $db->escape(checkInput($ignorelist));
            $db->query("UPDATE " . X_PREFIX . "members SET ignorepm = '" . $self['ignorepm'] . "' WHERE username = '$self[username]'");
            message($lang['ignoreupdate'], false, '', '', 'pm.php?action=ignore', true, false, true);
        } else {
            $self['ignorepm'] = checkOutput($self['ignorepm']);
            eval('$leftpane = "' . template('pm_ignore') . '";');
        }

        return $leftpane;
    }

    function viewFolders($folders)
    {
        global $db, $self, $lang, $CONFIG, $THEME;
        global $shadow, $shadow2, $mouseover;
        global $attachedfile, $filetype, $filesize, $filename, $lang_align, $lang_nalign;
        global $usesig, $usesigcheck, $oToken, $fileheight, $filewidth, $page, $farray;

        $pmsin = $pmsout = $pmsdraft = $pmsent = '';

        $folder = $_SESSION['folder'];

        if (empty($folder)) {
            $folder = "Inbox";
        }
        $folder = $db->escape($folder);

        $start_limit = ($page > 1) ? (($page - 1) * $self['tpp']) : 0;
        $query = $db->query("SELECT u.*, w.username, w.invisible FROM " . X_PREFIX . "pm u LEFT JOIN " . X_PREFIX . "whosonline w ON (u.msgto = w.username OR u.msgfrom = w.username) AND w.username != '$self[username]' WHERE u.folder = '$folder' AND u.owner = '$self[username]' ORDER BY dateline DESC LIMIT $start_limit, " . $self['tpp']);
        while (($pm = $db->fetch_array($query)) != false) {
            if ($pm['readstatus'] == 'yes') {
                $pmreadstatus = $lang['textread'];
            } else {
                $pmreadstatus = '<strong>' . $lang['textunread'] . '</strong>';
            }

            if (empty($pm['subject'])) {
                $pm['subject'] = $lang['textnosub'];
            }

            $pmsubject = checkOutput(censor($pm['subject']));

            if ($pm['type'] == 'incoming') {
                if ($pm['msgfrom'] == $pm['username'] || $pm['msgfrom'] == $self['username']) {
                    if ($pm['invisible'] == 1) {
                        if (X_ADMIN) {
                            $online = $lang['hidden'];
                        } else {
                            $online = $lang['textoffline'];
                        }
                    } else {
                        $online = $lang['textonline'];
                    }
                } else {
                    $online = $lang['textoffline'];
                }
                $pmsent = '<a href="viewprofile.php?memberid=' . rawurlencode($pm['msgfrom_uid']) . '" target="_blank">' . $pm['msgfrom'] . '</a> (' . $online . ')';
            } else
                if ($pm['type'] == 'outgoing') {
                    if ($pm['msgto'] == $pm['username'] || $pm['msgto'] == $self['username']) {
                        if ($pm['invisible'] == 1) {
                            if (X_ADMIN) {
                                $online = $lang['hidden'];
                            } else {
                                $online = $lang['textoffline'];
                            }
                        } else {
                            $online = $lang['textonline'];
                        }
                    } else {
                        $online = $lang['textoffline'];
                    }
                    $pmsent = '<a href="viewprofile.php?memberid=' . rawurlencode($pm['msgto_uid']) . '" target="_blank">' . $pm['msgto'] . '</a> (' . $online . ')';
                } else
                    if ($pm['type'] == 'draft') {
                        $pmsent = $lang['textpmnotsent'];
                    }

            $adjTime = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
            $pmdate = gmdate($self['dateformat'], $pm['dateline'] + $adjTime);
            $pmtime = gmdate($self['timecode'], $pm['dateline'] + $adjTime);
            $pmdateline = $lang['lastreply1'] . ' ' . $pmdate . ' ' . $lang['textat'] . ' ' . $pmtime;

            switch ($pm['type']) {
                case 'outgoing':
                    $pms = 'pmsout';
                    break;
                case 'draft':
                    $pms = 'pmsdraft';
                    break;
                case 'incoming':
                default:
                    $pms = 'pmsin';
                    break;
            }

            $mouseover = celloverfx('pm.php?action=view&amp;pmid=' . $pm['pmid'] . '');

            eval('$$pms .= "' . template('pm_row') . '";');
        }
        $db->free_result($query);

        $pmtrash = '';
        if ($pmsin == '') {
            eval('$pmsin = "' . template('pm_row_none') . '";');
        } else {
            if ($folder == 'Trash') {
                $pmtrash .= $pmsin;
            }
        }

        if ($pmsout == '') {
            eval('$pmsout = "' . template('pm_row_none') . '";');
        } else {
            if ($folder == 'Trash') {
                $pmtrash .= $pmsout;
            }
        }

        if ($pmsdraft == '') {
            eval('$pmsdraft = "' . template('pm_row_none') . '";');
        } else {
            if ($folder == 'Trash') {
                $pmtrash .= $pmsdraft;
            }
        }

        if ($pmtrash == '') {
            eval('$pmtrash = "' . template('pm_row_none') . '";');
        }

        $mpurl = 'pm.php?folder=' . $folder;

        $total = 0;
        if (isset($farray[$folder])) {
            $total = $farray[$folder];
        }

        if (($multipage = multi($total, $self['tpp'], $page, $mpurl)) === false) {
            $multipage = '';
        } else {
            eval('$multipage = "' . template('pm_multipage') . '";');
        }

        switch ($folder) {
            case 'Outbox':
                eval('$pmlist = "' . template('pm_outbox') . '";');
                break;
            case 'Drafts':
                eval('$pmlist = "' . template('pm_drafts') . '";');
                break;
            case 'Trash':
                eval('$pmlist = "' . template('pm_trash') . '";');
                break;
            case 'Inbox':
            default:
                eval('$pmlist = "' . template('pm_inbox') . '";');
                break;
        }

        $mtofolder = array();
        $mtofolder[] = '<select name="tofolder"><option value="">' . $lang['textpickfolder'] . '</option>';
        foreach ($folders as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
            }
            $mtofolder[] = '<option value="' . $key . '">' . $value . '</option>';
        }
        $mtofolder[] = '</select>';
        $mtofolder = implode("\n", $mtofolder);

        $leftpane = '';
        eval('$leftpane = "' . template('pm_main') . '";');
        return $leftpane;
    }

    function viewFolderList()
    {
        global $db, $self, $lang, $CONFIG, $THEME, $pmid;
        global $folderlist, $folders, $farray, $shadow, $shadow2;

        if (isset($_SESSION['folder'])) {
            $folder = $_SESSION['folder'];
        } else {
            $folder = '';
        }

        $pmcount = 0;
        $folders = (empty($self['pmfolders'])) ? array() : explode(',', $self['pmfolders']);
        foreach ($folders as $key => $value) {
            $folders[$key] = checkInput($value);
        }

        sort($folders);
        $folders = array_merge(array(
            'Inbox' => $lang['textpminbox'],
            'Outbox' => $lang['textpmoutbox']
        ), $folders, array(
            'Drafts' => $lang['textpmdrafts'],
            'Trash' => $lang['textpmtrash']
        ));

        $query = $db->query("SELECT folder, count(pmid) as count FROM " . X_PREFIX . "pm WHERE owner = '$self[username]' GROUP BY folder ORDER BY folder ASC");
        $farray = array();
        while (($flist = $db->fetch_array($query)) != false) {
            $farray[$flist['folder']] = $flist['count'];
            $pmcount += $flist['count'];
        }
        $db->free_result($query);

        $emptytrash = $folderlist = '';

        foreach ($folders as $link => $value) {
            if (is_numeric($link)) {
                $link = $value;
            }
            if (empty($folder) && isset($pmid)) {
                $query = $db->query("SELECT folder FROM " . X_PREFIX . "pm WHERE owner = '$self[username]' AND pmid = '$pmid'");
                $viewfolder = $db->result($query);
                $db->free_result($query);

                if ($link == $viewfolder) {
                    $value = '<strong>' . $value . '</strong>';
                }
            }

            if ($link == $folder) {
                $value = '<strong>' . $value . '</strong>';
            }

            $count = (empty($farray[$link])) ? 0 : $farray[$link];
            if ($link == 'Trash') {
                if ($count != 0) {
                    $emptytrash = ' (<a href="pm.php?action=emptytrash">' . $lang['textemptytrash'] . '</a>)';
                }
            }
            $link = rawurlencode($link);
            eval('$folderlist .= "' . template('pm_folderlink') . '";');
        }

        return $pmcount;
    }
}
