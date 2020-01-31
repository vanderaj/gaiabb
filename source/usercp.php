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

require_once 'header.php';
require_once 'include/usercp.inc.php';

if (X_GUEST) {
    redirect('login.php', 0);
}

loadtpl('usercp_profile', 'usercp_options', 'usercp_favs_row', 'usercp_favs_none', 'usercp_favs', 'usercp_favs_button', 'usercp_subscriptions_row', 'usercp_subscriptions_none', 'usercp_subscriptions', 'usercp_subscriptions_button', 'usercp_home_pm_row', 'usercp_home_pm_none', 'usercp_home', 'usercp_home_favs_none', 'usercp_home_favs_row', 'usercp_home_layout', 'usercp_home_subscriptions_row', 'usercp_home_subscriptions_none', 'usercp_outputmsg', 'usercp_email', 'usercp_avatar', 'usercp_avatarurl', 'usercp_avataruser', 'usercp_avatarhidden', 'usercp_avatarsubmit', 'usercp_avatarnone', 'usercp_password', 'usercp_signature', 'usercp_gallery', 'usercp_gallery_multipage', 'usercp_home_themes', 'usercp_custom', 'usercp_custom_none', 'usercp_options_aka', 'usercp_notepad', 'functions_smilieinsert', 'functions_smilieinsert_smilie', 'functions_bbcodeinsert', 'functions_bbcode', 'usercp_sig_preview', 'usercp_home_pm', 'usercp_photourl', 'usercp_photouser', 'usercp_photohidden', 'usercp_photosubmit', 'usercp_photonone', 'usercp_photo');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

smcwcache();

eval('$css = "' . template('css') . '";');

$favs = $menu = null;

$config_cache->expire('settings');
$config_cache->expire('theme');
$config_cache->expire('pluglinks');
$config_cache->expire('whosonline');
$config_cache->expire('forumjump');

$sel0 = $sel1 = $sel2 = $sel3 = $sel4 = $sel5 = $sel6 = '';
$sel7 = $sel8 = $sel9 = $sel10 = $sel11 = $sel12 = '';

switch ($action) {
    case 'profile':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['texteditpro']);
        btitle($lang['textusercp']);
        btitle($lang['texteditpro']);
        break;
    case 'options':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['Edit_Options']);
        btitle($lang['textusercp']);
        btitle($lang['Edit_Options']);
        break;
    case 'email':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['Edit_Email']);
        btitle($lang['textusercp']);
        btitle($lang['Edit_Email']);
        break;
    case 'avatar':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['Edit_Avatar']);
        btitle($lang['textusercp']);
        btitle($lang['Edit_Avatar']);
        break;
    case 'photo':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['edit_personal_photo']);
        btitle($lang['textusercp']);
        btitle($lang['edit_personal_photo']);
        break;
    case 'password':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['Edit_Password']);
        btitle($lang['textusercp']);
        btitle($lang['Edit_Password']);
        break;
    case 'signature':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['Edit_Signature']);
        btitle($lang['textusercp']);
        btitle($lang['Edit_Signature']);
        if ($bbcode_js != '') {
            $bbcode_js_sc = 'bbcodefns-' . $bbcode_js . '.js';
        } else {
            $bbcode_js_sc = 'bbcodefns.js';
        }
        eval('$bbcodescript = "' . template('functions_bbcode') . '";');
        break;
    case 'gallery':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        btitle($lang['textusercp']);
        $type = formVar('type');
        if (!isset($type)) {
            nav($lang['avatargallery']);
            btitle($lang['avatargallery']);
        } else {
            nav('<a href="usercp.php?action=gallery">' . $lang['avatargallery'] . '</a>');
            nav($type);
            btitle($lang['avatargallery']);
            btitle($type);
        }
        break;
    case 'subscriptions':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['textsubscriptions']);
        btitle($lang['textusercp']);
        btitle($lang['textsubscriptions']);
        break;
    case 'favorites':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['textfavorites']);
        btitle($lang['textusercp']);
        btitle($lang['textfavorites']);
        break;
    case 'notepad':
        nav('<a href="usercp.php">' . $lang['textusercp'] . '</a>');
        nav($lang['notepad']);
        btitle($lang['textusercp']);
        btitle($lang['notepad']);
        break;
    default:
        nav($lang['textusercp']);
        btitle($lang['textusercp']);
        break;
}

$userObj = new userObj();

switch ($action) {
    case 'quicktheme':
        $newtheme = getInt('newtheme');
        $db->query("UPDATE " . X_PREFIX . "members SET theme = '$newtheme' WHERE username = '" . $self['username'] . "'");
        redirect('usercp.php', 0);
        break;
    case 'notepad':
        eval('echo "' . template('header') . '";');
        makenav($action);

        if ($CONFIG['notepadstatus'] == 'off') {
            error($lang['fnasorry'], false);
        }

        if (noSubmit('savesubmit') && noSubmit('clearsubmit')) {
            $notes = htmlentities(stripslashes($self['notepad'])); // Output is htmlentities, not addslashes
            eval('$output = "' . template('usercp_notepad') . '";');
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        }

        if (onSubmit('savesubmit')) {
            $db->query("UPDATE " . X_PREFIX . "members SET notepad = '" . $db->escape(formVar('notes')) . "' WHERE username = '" . $self['username'] . "'");
            $output = table_msg($lang['notepadsuccess']);
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
            redirect('usercp.php?action=notepad', 2.5, X_REDIRECT_JS);
        }

        if (onSubmit('clearsubmit')) {
            $db->query("UPDATE " . X_PREFIX . "members SET notepad = '' WHERE username = '" . $self['username'] . "'");
            $output = table_msg($lang['notepadcleared']);
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
            redirect('usercp.php?action=notepad', 2.5, X_REDIRECT_JS);
        }
        break;
    case 'profile':
        eval('echo "' . template('header') . '";');
        makenav($action);

        if (noSubmit('profilesubmit')) {
            $userObj->viewProfile();
        }

        if (onSubmit('profilesubmit')) {
            $userObj->submitProfile();
        }
        break;
    case 'options':
        eval('echo "' . template('header') . '";');
        makenav($action);

        if (noSubmit('optionsubmit')) {
            $userObj->viewOption();
        }

        if (onSubmit('optionsubmit')) {
            $userObj->submitOption();
        }
        break;
    case 'email':
        eval('echo "' . template('header') . '";');
        makenav($action);

        if (noSubmit('emailsubmit')) {
            $member = $self;
            eval('$output = "' . template('usercp_email') . '";');
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        }

        if (onSubmit('emailsubmit')) {
            $userObj->submitEmail();
        }
        break;
    case 'avatar':
        eval('echo "' . template('header') . '";');
        makenav($action);

        if ($CONFIG['avastatus'] == 'off' && $CONFIG['avatar_whocanupload'] == 'off') {
            error($lang['fnasorry'], false);
        }

        if (noSubmit('avatarsubmit')) {
            $userObj->viewAvatar();
        }

        if (onSubmit('avatarsubmit')) {
            $userObj->submitAvatar();
        }
        break;
    case 'photo':
        eval('echo "' . template('header') . '";');
        makenav($action);

        if ($CONFIG['photostatus'] == 'off' && $CONFIG['photo_whocanupload'] == 'off') {
            error($lang['fnasorry'], false);
        }

        if (noSubmit('photosubmit')) {
            $userObj->viewPhoto();
        }

        if (onSubmit('photosubmit')) {
            $userObj->submitPhoto();
        }
        break;
    case 'password':
        eval('echo "' . template('header') . '";');
        makenav($action);

        if (noSubmit('passwordsubmit')) {
            $member = $self;
            eval('$output = "' . template('usercp_password') . '";');
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        }

        if (onSubmit('passwordsubmit')) {
            $userObj->submitPassword();
        }
        break;
    case 'signature':
        eval('echo "' . template('header') . '";');
        makenav($action);

        if (noSubmit('sigsubmit')) {
            $userObj->viewSignature();
        }

        if (onSubmit('sigsubmit')) {
            $userObj->submitSignature();
        }
        break;
    case 'gallery':
        eval('echo "' . template('header') . '";');
        makenav($action);

        if ($CONFIG['avatars_status'] == 'off') {
            error($lang['avatarfeaturedisabled'], false);
        }

        if (noSubmit('avatarsubmit')) {
            $userObj->viewAvatarGallery();
        }

        if (onSubmit('avatarsubmit')) {
            $userObj->submitAvatarGallery();
        }
        break;
    case 'favorites':
        eval('echo "' . template('header') . '";');
        makenav($action);

        $favadd = intval(getRequestVar('favadd'));

        if (noSubmit('favsubmit') && !empty($favadd) && is_numeric($favadd)) {
            $userObj->submitAddFavorite($favadd);
        }

        if (empty($favadd) && noSubmit('favsubmit')) {
            $userObj->viewFavorites();
        }

        if (empty($favadd) && onSubmit('favsubmit')) {
            $userObj->submitManageFavorites();
        }
        break;
    case 'subscriptions':
        eval('echo "' . template('header') . '";');
        makenav($action);

        $subadd = intval(getRequestVar('subadd'));

        if (empty($subadd) && noSubmit('subsubmit')) {
            $userObj->viewSubscriptions();
        } else
        if (!empty($subadd) && noSubmit('subsubmit')) {
            $userObj->submitAddSubscription($subadd);
        } else
        if (empty($subadd) && onSubmit('subsubmit')) {
            $userObj->submitManageSubscriptions();
        }
        break;
    default:
        eval('echo "' . template('header') . '";');
        makenav($action);

        $userObj->viewUserCP();
        break;
}

loadtime();
eval('echo "' . template('footer') . '";');
