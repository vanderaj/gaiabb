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
require_once 'include/pm.inc.php';

loadtpl('pm_nav', 'pm', 'pm_folderlink', 'pm_inbox', 'pm_outbox', 'pm_drafts', 'pm_row', 'pm_row_none', 'pm_view', 'pm_ignore', 'pm_send', 'pm_send_preview', 'pm_folders', 'pm_main', 'pm_multipage', 'pm_quotabar', 'pm_printable', 'pm_attachmentbox', 'pm_attachment', 'pm_sig', 'pm_trash', 'pm_send_preview_sig', 'pm_attachmentimage', 'functions_smilieinsert', 'functions_smilieinsert_smilie', 'functions_bbcodeinsert', 'functions_bbcode');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();
smcwcache();

eval('$css = "' . template('css') . '";');

switch ($action) {
    case 'send':
        nav('<a href="pm.php">' . $lang['textpmmessenger'] . '</a>');
        nav($lang['textsendpm']);
        btitle($lang['textpmmessenger']);
        btitle($lang['textsendpm']);
        break;
    case 'ignore':
        nav('<a href="pm.php">' . $lang['textpmmessenger'] . '</a>');
        nav($lang['ignorelist']);
        btitle($lang['textpmmessenger']);
        btitle($lang['ignorelist']);
        break;
    case 'view':
        nav('<a href="pm.php">' . $lang['textpmmessenger'] . '</a>');
        nav($lang['viewpminbox']);
        btitle($lang['textpmmessenger']);
        btitle($lang['viewpminbox']);
        break;
    case 'folders':
        nav('<a href="pm.php">' . $lang['textpmmessenger'] . '</a>');
        nav($lang['folderlist']);
        btitle($lang['textpmmessenger']);
        btitle($lang['folderlist']);
        break;
    default:
        nav($lang['textpmmessenger']);
        btitle($lang['textpmmessenger']);
        break;
}

$sendmode = (isset($action) && $action == 'send') ? true : false;

if ($bbcode_js != '') {
    $bbcode_js_sc = 'bbcodefns-' . $bbcode_js . '.js';
} else {
    $bbcode_js_sc = 'bbcodefns.js';
}

eval('$bbcodescript = "' . template('functions_bbcode') . '";');

if ($action != 'attachment' && $action != 'printable') {
    eval('echo "' . template('header') . '";');
    eval('echo "' . template('pm_nav') . '";');
}

if (X_GUEST) {
    error($lang['pmnotloggedin'], false, '', '', 'index.php', true, false, true);
}

if ($CONFIG['pmstatus'] == 'off' && isset($self['status']) && $self['status'] == 'Member') {
    error($lang['pmstatusdisabled'], false, '', '', 'index.php', true, false, true);
}

$pmCommand = new pmModel();

$page = getInt('page');
if (!$page) {
    $page = 1;
}

// If there's a new folder coming in from the URL, let's parse it.
$folder = getRequestVar('folder');
if (!empty($folder)) {
    $folder = checkInput($folder);
    $_SESSION['folder'] = $folder;
} else {
    if (empty($folder) || !isset($folder)) {
        if ($action == 'view' || $action == 'modif') {
            $folder = '';
        }
        if ($action == '' || !isset($action)) {
            $folder = 'Inbox';
            $_SESSION['folder'] = $folder;
        }
    } else {
        if (isset($_SESSION['folder'])) {
            $folder = $_SESSION['folder'];
        } else {
            $folder = 'Inbox';
            $_SESSION['folder'] = $folder;
        }
    }
}

// Fill in the folder list, folders and farray.
$folderlist = $folders = '';
$farray = array();
$pmcount = $pmCommand->viewFolderList();

// Get sanitized user data
$pmid = getRequestInt('pmid');

$pm_select = formArray('pm_select');
$aid = getInt('aid');

$mod = formVar('mod');
$modaction = formVar('modaction');

$tofolder = formVar('tofolder');
$type = formVar('type');

$msgto = formVar('msgto');
$subject = formVar('subject');
$message = formVar('message');
$usesig = formVar('usesig');

$pmpreview = formVar('pmpreview');
$pmfolders = formVar('pmfolders');

// No matter what happens here, something will change, so let's expire the cache
$config_cache->expire('newpmmsg');

$pmpreview = $leftpane = '';
switch ($action) {
    case 'modif':
        switch ($mod) {
            case 'send':
                if ($pmid > 0) {
                    redirect('pm.php?action=send&pmid=' . $pmid, 0);
                } else {
                    redirect('pm.php?action=send', 0);
                }
                break;
            case 'reply':
                if ($pmid > 0) {
                    redirect('pm.php?action=send&pmid=' . $pmid . '&reply=yes', 0);
                } else {
                    redirect('pm.php?action=send&reply=yes', 0);
                }
                break;
            case 'forward':
                if ($pmid > 0) {
                    redirect('pm.php?action=send&pmid=' . $pmid . '&forward=yes', 0);
                } else {
                    redirect('pm.php?action=send&forward=yes', 0);
                }
                break;
            case 'sendtoemail':
                $pmCommand->pm_print($pmid, true);
                break;
            case 'delete':
                $pmCommand->delete($pmid);
                break;
            case 'move':
                $pmCommand->move($pmid, $tofolder);
                break;
            case 'markunread':
                $pmCommand->markUnread($pmid, $type);
                break;
            default:
                $leftpane = $pmCommand->view($pmid, $folders);
                break;
        }
        break;
    case 'mod':
        switch ($modaction) {
            case 'delete':
                if (empty($pm_select)) {
                    error($lang['textnonechosen'], false, '', '', 'pm.php', true, false, true);
                }
                $pmCommand->mod_delete($pm_select);
                break;

            case 'move':
                if (empty($tofolder)) {
                    error($lang['textnofolder'], false, '', '', 'pm.php', true, false, true);
                }

                if ($pm_select == '') {
                    error($lang['textnonechosen'], false, '', '', 'pm.php', true, false, true);
                }
                $pmCommand->mod_move($tofolder, $pm_select);
                break;

            case 'markunread':
                $pmCommand->mod_markUnread($pm_select);
                break;

            default:
                error($lang['testnothingchos'], false, '', '', 'pm.php', true, false, true);
                break;
        }
        break;
    case 'send':
        if (!X_STAFF && isset($self['postnum']) && $self['postnum'] < $CONFIG['pmposts']) {
            error($lang['pminsufficentposts'], false, '', '', 'pm.php', true, false, true);
        }

        if (isset($_GET['memberid'])) {
            $memberid = getInt('memberid');
            $gmem_query = $db->query("SELECT username FROM " . X_PREFIX . "members WHERE uid = '$memberid' LIMIT 1");
            $gmem_array = $db->fetch_array($gmem_query);
            $username = $gmem_array['username'];
        }

        $leftpane = $pmCommand->send($pmid, $msgto, $subject, $message, $pmpreview);
        break;
    case 'view':
        $leftpane = $pmCommand->view($pmid, $folders);
        break;
    case 'printable':
        $pmCommand->pm_print($pmid, false);
        break;
    case 'folders':
        if (onSubmit('folderssubmit')) {
            $pmCommand->updateFolders($pmfolders, $folders);
        } else {
            $self['pmfolders'] = checkOutput($self['pmfolders']);
            eval('$leftpane = "' . template('pm_folders') . '";');
        }
        break;
    case 'attachment':
        if (!($pmid > 0)) {
            error($lang['textnonechosen'], false, '', '', 'pm.php', true, false, true);
        }

        $query = $db->query("SELECT * FROM " . X_PREFIX . "pm_attachments WHERE pmid = '$pmid' AND aid = '$aid' AND owner = '$self[username]'");
        $file = $db->fetch_array($query);
        $db->free_result($query);

        if ($file['filesize'] != strlen($file['attachment'])) {
            error($lang['File_Corrupt'], false, '', '', false, true, false, true);
        }

        $type = $file['filetype'];
        $name = str_replace(' ', '_', $file['filename']);
        $size = (int) $file['filesize'];
        $type = (strtolower($type) == 'text/html') ? 'text/plain' : $type;

        header("Content-type: $type");
        header("Content-length: $size");
        header("Content-Disposition: inline; filename = $name");
        header("Content-Description: PHP Generated Attachments");
        header("Cache-Control: public; max-age=604800");
        header("Expires: 604800");

        echo $file['attachment'];

        exit();
        break;
    case 'ignore':
        $leftpane = $pmCommand->viewIgnoreList();
        break;
    case 'emptytrash':
        $in = '';
        $iquery = $db->query("SELECT pmid FROM " . X_PREFIX . "pm WHERE folder = 'Trash' AND owner = '$self[username]'");
        while (($ids = $db->fetch_array($iquery)) != false) {
            $in .= (empty($in)) ? $ids['pmid'] : "," . $ids['pmid'];
        }
        $db->free_result($iquery);
        $db->query("DELETE FROM " . X_PREFIX . "pm WHERE pmid IN($in)");
        $db->query("DELETE FROM " . X_PREFIX . "pm_attachments WHERE pmid IN($in)");
        message($lang['texttrashemptied'], false, '', '', 'pm.php', true, false, true);
        break;
    default:
        $leftpane = $pmCommand->viewFolders($folders);
        break;
}

if (!X_STAFF) {
    $percentage = (0 == $CONFIG['pmquota']) ? 0 : (float) (($pmcount / $CONFIG['pmquota']) * 100);
    if (100 < $percentage) {
        $barwidth = 100;
        eval($lang['evaluqinfo_over']);
    } else {
        $percent = number_format($percentage, 2);
        $barwidth = number_format($percentage, 0);
        eval($lang['evaluqinfo']);
    }
} else {
    $barwidth = $percentage = 0;
    eval($lang['evalpmstaffquota']);
}

eval('$pmquotabar = "' . template('pm_quotabar') . '";');
eval('echo stripslashes("' . template('pm') . '");');

loadtime();
eval('echo "' . template('footer') . '";');
