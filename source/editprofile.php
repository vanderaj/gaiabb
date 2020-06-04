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

loadtpl(
    'error_nologinsession',
    'usercp_avatarurl',
    'admintool_editprofile',
    'usercp_photourl',
    'usercp_avatarhidden',
    'usercp_useravatar',
    'editprofile_avatarurl',
    'editprofile_avatarhidden',
    'editprofile_useravatar',
    'editprofile_photourl',
    'editprofile_photohidden',
    'editprofile_userphoto'
);

$shadow = shadowfx();
$meta = metaTags();

nav($lang['texteditpro']);
btitle($lang['texteditpro']);

eval('$css = "' . template('css') . '";');

eval('echo "' . template('header') . '";');

if (!X_SADMIN) {
    error($lang['superadminonly'], false);
}

$config_cache->expire('settings');
$moderators_cache->expire('moderators');
$config_cache->expire('theme');
$config_cache->expire('pluglinks');
$config_cache->expire('whosonline');
$config_cache->expire('forumjump');

$auditaction = $_SERVER['REQUEST_URI'];
$aapos = strpos($auditaction, "?");
if ($aapos !== false) {
    $auditaction = substr($auditaction, $aapos + 1);
}

$auditaction = addslashes("$onlineip|#|$auditaction");
adminaudit($self['username'], $auditaction, 0, 0);

$memberid = getInt('memberid');
$userid = $db->fetch_array($db->query("SELECT username FROM " . X_PREFIX . "members WHERE uid = '$memberid'"));
if (empty($userid['username'])) {
    error($lang['nomember'], false);
} else {
    $user = $userid['username'];
}

if (noSubmit('editsubmit')) {
    $query = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE uid = '$memberid'");
    $member = $db->fetch_array($query);
    $db->free_result($query);

    $showemailyes = $showemailno = '';
    memberYesNo('showemail', $showemailyes, $showemailno);

    $newsletteryes = $newsletterno = '';
    memberYesNo('newsletter', $newsletteryes, $newsletterno);

    $saveogpmyes = $saveogpmno = '';
    memberYesNo('saveogpm', $saveogpmyes, $saveogpmno);

    $emailonpmyes = $emailonpmno = '';
    memberYesNo('emailonpm', $emailonpmyes, $emailonpmno);

    $viewavatarsyes = $viewavatarsno = '';
    memberYesNo('viewavatars', $viewavatarsyes, $viewavatarsno);

    $viewsigsyes = $viewsigsno = '';
    memberYesNo('viewsigs', $viewsigsyes, $viewsigsno);

    $shownameyes = $shownameno = '';
    memberYesNo('showname', $shownameyes, $shownameno);

    $expviewyes = $expviewno = '';
    memberYesNo('expview', $expviewyes, $expviewno);

    $invisibleyes = $invisibleno = '';
    switch ($member['invisible']) {
        case '1':
            $invisibleyes = $selHTML;
            break;
        default:
            $invisibleno = $selHTML;
            break;
    }

    $dstyes = $dstno = '';
    switch ($member['daylightsavings']) {
        case '3600':
            $dstyes = $selHTML;
            break;
        default:
            $dstno = $selHTML;
            break;
    }

    $selectasc = $selectdesc = '';
    switch ($member['psorting']) {
        case 'ASC':
            $selectasc = $selHTML;
            break;
        default:
            $selectdesc = $selHTML;
            break;
    }

    $registerdate = gmdate($self['dateformat'], $member['regdate'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
    $lastlogdate = gmdate($self['dateformat'], $member['lastvisit'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);

    $currdate = gmdate($self['timecode'], $onlinetime);
    eval($lang['evaloffset']);

    TimeOffsetDisplay();

    $themelist = array();
    $themelist[] = '<select name="thememem">';
    $themelist[] = '<option value="0">' . $lang['textusedefault'] . '</option>';
    $query = $db->query("SELECT themeid, name FROM " . X_PREFIX . "themes WHERE themestatus = 'on' ORDER BY name ASC");
    while (($themeinfo = $db->fetch_array($query)) != false) {
        if ($themeinfo['themeid'] == $member['theme']) {
            $themelist[] = '<option value="' . $themeinfo['themeid'] . '" ' . $selHTML . '>' . stripslashes($themeinfo['name']) . '</option>';
        } else {
            $themelist[] = '<option value="' . $themeinfo['themeid'] . '">' . stripslashes($themeinfo['name']) . '</option>';
        }
    }
    $themelist[] = '</select>';
    $themelist = implode("\n", $themelist);
    $db->free_result($query);

    $langfileselect = langSelect();

    BDayDisplay();

    $check12 = $check24 = '';
    switch ($member['timeformat']) {
        case '24':
            $check24 = $selHTML;
            break;
        default:
            $check12 = $selHTML;
            break;
    }

    $timeformatlist = array();
    $timeformatlist[] = '<select name="timeformatnew">';
    $timeformatlist[] = '<option value="24"' . $check24 . '>' . gmdate("H:i", $onlinetime + ($self['timeoffset'] * 3600) + $self['daylightsavings']) . '</option>';
    $timeformatlist[] = '<option value="12"' . $check12 . '>' . gmdate("h:i A", $onlinetime + ($self['timeoffset'] * 3600) + $self['daylightsavings']) . '</option>';
    $timeformatlist[] = '</select>';
    $timeformatlist = implode("\n", $timeformatlist);

    if ($CONFIG['sigbbcode'] == 'on') {
        $bbcodeis = $lang['texton'];
    } else {
        $bbcodeis = $lang['textoff'];
    }

    eval('$avatar = "' . template('editprofile_avatarurl') . '";');

    $useravatar = $avdeletebutton = '';
    if (!empty($member['avatar'])) {
        eval('$useravatar = "' . template('editprofile_useravatar') . '";');
        $avdeletebutton = '<br /><input type="checkbox" name="avatardel" value="1" />' . $lang['Avatar_Delete'] . '';
    }

    eval('$avatarhidden = "' . template('editprofile_avatarhidden') . '";');

    $photo = '';
    eval('$photo = "' . template('editprofile_photourl') . '";');

    $userphoto = $photodeletebutton = '';
    if (!empty($member['photo'])) {
        eval('$userphoto = "' . template('editprofile_userphoto') . '";');
        $photodeletebutton = '<br /><input type="checkbox" name="photodel" value="1" />' . $lang['photo_Delete'] . '';
    }

    eval('$photohidden = "' . template('editprofile_photohidden') . '";');

    if ($CONFIG['predformat'] == 'on') {
        $df = "<tr>\n\t<td bgcolor=\"$THEME[altbg1]\" class=\"tablerow\" width=\"22%\">$lang[dateformat1]</td>\n";
    } else {
        $df = "<tr>\n\t<td bgcolor=\"$THEME[altbg1]\" class=\"tablerow\" width=\"22%\">$lang[dateformat2]</td>\n";
    }

    $df = $df . "\t<td bgcolor=\"$THEME[altbg2]\" class=\"tablerow\"><select name=\"dateformatnew\">\n";
    $querydf = $db->query("SELECT * FROM " . X_PREFIX . "dateformats");
    while (($dformats = $db->fetch_array($querydf)) != false) {
        if ($CONFIG['predformat'] == 'on') {
            $example = gmdate(formatDate($dformats['dateformat']), $gbblva + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        } else {
            $example = $dformats['dateformat'];
        }

        if ($member['dateformat'] == $dformats['dateformat']) {
            $df = $df . "\t<option value=\"$dformats[dateformat]\" selected=\"selected\">$example</option>\n";
        } else {
            $df = $df . "\t<option value=\"$dformats[dateformat]\">$example</option>\n";
        }
    }
    $df = $df . "\t</select>\n\t</td>\n</tr>";
    $db->free_result($querydf);

    $lang['searchusermsg'] = str_replace('*USER*', $user, $lang['searchusermsg']);

    eval('echo stripslashes("' . template('admintool_editprofile') . '");');
}

if (onSubmit('editsubmit')) {
    $query = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE uid = '$memberid'");
    $member = $db->fetch_array($query);
    $db->free_result($query);

    if (empty($member['username'])) {
        error($lang['badname'], false);
    }

    $showemail = formYesNo('newshowemail');
    $newsletter = formYesNo('newnewsletter');
    $saveogpm = formYesNo('saveogpm');
    $emailonpm = formYesNo('emailonpm');
    $viewavatars = formYesNo('viewavatars');
    $viewsigs = formYesNo('viewsigs');
    $showname = formYesNo('showname');
    $expview = formYesNo('expview');
    $invisible = form10('newinv');
    $daylightsavings1 = form3600('daylightsavings1');
    $thememem = formInt('thememem');
    $psorting = formVar('psorting');
    if ($psorting != 'ASC') {
        $psorting = 'DESC';
    }
    $tppnew = formInt('tppnew');
    if ($tppnew < 5) {
        $tppnew = $CONFIG['topicperpage'];
    }
    $pppnew = formInt('pppnew');
    if ($pppnew < 5) {
        $pppnew = $CONFIG['postperpage'];
    }

    $month = addslashes(formVar('month'));
    $day = formInt('day', false);
    $year = formInt('year', false);
    if ($year == '' || $year == 0) {
        $comma = '';
    } else {
        $comma = ', ';
    }
    $bday = $month . ' ' . $day . $comma . $year;

    if (isset($_POST['newavatar'])) {
        if ('http' == substr($_POST['newavatar'], 0, 4)) {
            $_POST['newavatar'] = preg_replace('/ /', '%20', $_POST['newavatar']);
        }
    } else {
        $_POST['newavatar'] = '';
    }

    if (isset($_POST['newphoto'])) {
        if ('http' == substr($_POST['newphoto'], 0, 4)) {
            $_POST['newphoto'] = preg_replace('/ /', '%20', $_POST['newphoto']);
        }
    } else {
        $_POST['newphoto'] = '';
    }

    $avatar = $db->escape(formVar('newavatar'), -1, true);
    $location = addslashes(formVar('newlocation'));
    $icq = addslashes(formVar('newicq'));
    $yahoo = addslashes(formVar('newyahoo'));
    $aim = addslashes(formVar('newaim'));
    $msn = addslashes(formVar('newmsn'));
    $email = addslashes(formVar('newemail'));
    $site = addslashes(formVar('newsite'));
    $bio = addslashes(formVar('newbio'));
    $mood = addslashes(formVar('newmood'));
    $sig = addslashes(formVar('newsig'));
    $photo = $db->escape(formVar('newphoto'), -1, true);
    $firstname = addslashes(formVar('firstname'));
    $lastname = addslashes(formVar('lastname'));
    $customstatus = addslashes(formVar('newcustomstatus'));
    $occupation = addslashes(formVar('newoccupation'));
    $blog = addslashes(formVar('newblog'));
    $timeoffset1 = formInt('timeoffset1');
    if ($timeoffset1 < -12 || $timeoffset1 > 13) {
        $timeoffset1 = $CONFIG['def_tz'];
    }
    $timeformatnew = addslashes(formVar('timeformatnew'));
    $dateformatnew = addslashes(formVar('dateformatnew'));
    $langfilenew = $db->escape(findLangName(formInt('langfilenew')));

    $max_size = explode('x', $CONFIG['max_avatar_size']);
    if ($max_size[0] > 0 && $max_size[1] > 0 && substr_count($avatar, ',') < 2) {
        $size = getimagesize($avatar);
        if ($size === false) {
            $avatar = '';
        } else
        if (($size[0] > $max_size[0] && $max_size[0] > 0) || ($size[1] > $max_size[1] && $max_size[1] > 0) && !X_ADMIN) {
            error($lang['avatar_too_big'] . $CONFIG['max_avatar_size'] . $lang['Avatar_Pixels'], false);
        }
    }

    if (isset($_COOKIE['avatarfile']) || isset($_POST['avatarfile']) || isset($_GET['avatarfile'])) {
        die('Action Halted Due To Illegal Acivity!!');
        exit();
    }

    if (isset($_FILES['avatarfile']['name']) && $_FILES['avatarfile']['tmp_name'] && !empty($_FILES['avatarfile']['name'])) {
        $avatarext = substr($_FILES['avatarfile']['name'], strlen($_FILES['avatarfile']['name']) - 3, 3);
        $newavatarname = $member['uid'] . '.' . $onlinetime . '.' . $avatarext;
        $check = $_FILES['avatarfile'];

        $CONFIG['avatar_filesize'] = (int) $CONFIG['avatar_filesize'];
        if (($check['size'] > $CONFIG['avatar_filesize']) && !X_ADMIN) {
            error($lang['avatar_too_big'] . $CONFIG['avatar_filesize'] . $lang['Avatar_Bytes'], false);
        }

        $avatarpath = $CONFIG['avatar_path'] . '/' . $newavatarname;
        $tmppath = $check['tmp_name'];

        if (!preg_match('/gif|jpeg|png|jpg|bmp/i', $avatarext)) {
            error($lang['avatar_invalid_ext'], false);
        }

        if (!is_writable($CONFIG['avatar_path'])) {
            error($lang['avatar_nowrite'], false);
        }

        $size = getimagesize($tmppath);
        $width = $size[0];
        $height = $size[1];
        $type = $size[2];

        if (!((bool) ini_get('safe_mode'))) {
            set_time_limit(30);
        }
        $imginfo = getimagesize($tmppath);
        $type = $imginfo[2];

        switch ($type) {
            case IMAGETYPE_GIF:
                if (!function_exists('imagecreatefromgif')) {
                    return $tmppath;
                }
                $srcImage = imagecreatefromgif($tmppath);
                break;
            case IMAGETYPE_JPEG:
                if (!function_exists('imagecreatefromjpeg')) {
                    return $tmppath;
                }
                $srcImage = imagecreatefromjpeg($tmppath);
                break;
            case IMAGETYPE_PNG:
                if (!function_exists('imagecreatefrompng')) {
                    return $tmppath;
                }
                $srcImage = imagecreatefrompng($tmppath);
                break;
            case IMAGETYPE_WBMP:
                if (!function_exists('imagecreatefromwbmp')) {
                    return $tmppath;
                }
                $srcImage = imagecreatefromwbmp($tmppath);
                break;
            default:
                return $tmppath;
        }

        if ($width > $CONFIG['avatar_max_width']) {
            $newwidth = $CONFIG['avatar_new_width'];
            $newheight = ($newwidth / $width) * $height;
        } else
        if ($height > $CONFIG['avatar_max_height']) {
            $newheight = $CONFIG['avatar_new_height'];
            $newwidth = ($newheight / $height) * $width;
        }

        if (isset($newwidth)) {
            $destImage = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            switch ($type) {
                case IMAGETYPE_GIF:
                    imagegif($destImage, $tmppath);
                    break;
                case IMAGETYPE_JPEG:
                    imagejpeg($destImage, $tmppath);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($destImage, $tmppath);
                    break;
                case IMAGETYPE_WBMP:
                    imagewbmp($destImage, $tmppath);
                    break;
                    imagedestroy($srcImage);
                    imagedestroy($destImage);
            }
        }

        copy($tmppath, $avatarpath);
        $db->query("UPDATE " . X_PREFIX . "members SET avatar = '$avatarpath' WHERE uid = '$memberid'");
    }

    if (isset($_POST['newavatar']) && empty($_FILES['avatarfile']['name'])) {
        $db->query("UPDATE " . X_PREFIX . "members SET avatar = '$avatar' WHERE uid = '$memberid'");
    }

    if (onSubmit('editsubmit') && isset($_POST['avatardel']) != 1 && !empty($member['avatar']) && empty($_POST['newavatar']) && empty($_FILES['avatarfile']['name'])) {
        $db->query("UPDATE " . X_PREFIX . "members SET avatar = '$member[avatar]' WHERE uid = '$memberid'");
    }

    if (isset($_POST['avatardel']) && isset($_POST['avatardel']) == 1 && empty($_FILES['avatarfile']['name'])) {
        if (file_exists($member['avatar'])) {
            unlink($member['avatar']);
        }
        $db->query("UPDATE " . X_PREFIX . "members SET avatar = '' WHERE uid = '$memberid'");
    }

    $max_size = explode('x', $CONFIG['max_photo_size']);
    if ($max_size[0] > 0 && $max_size[1] > 0 && substr_count($photo, ',') < 2) {
        $size = getimagesize($photo);
        if ($size === false) {
            $photo = '';
        } else
        if (($size[0] > $max_size[0] && $max_size[0] > 0) || ($size[1] > $max_size[1] && $max_size[1] > 0) && !X_ADMIN) {
            error($lang['photo_too_big'] . $CONFIG['max_photo_size'] . $lang['photo_Pixels'], false);
        }
    }

    if (isset($_COOKIE['photofile']) || isset($_POST['photofile']) || isset($_GET['photofile'])) {
        die('Action Halted Due To Illegal Acivity!!');
        exit();
    }

    if (isset($_FILES['photofile']['name']) && $_FILES['photofile']['tmp_name'] && !empty($_FILES['photofile']['name'])) {
        $photoext = substr($_FILES['photofile']['name'], strlen($_FILES['photofile']['name']) - 3, 3);
        $newphotoname = $member['uid'] . '.' . $onlinetime . '.' . $photoext;
        $check = $_FILES['photofile'];

        $CONFIG['photo_filesize'] = (int) $CONFIG['photo_filesize'];
        if (($check['size'] > $CONFIG['photo_filesize']) && !X_ADMIN) {
            error($lang['photo_too_big'] . $CONFIG['photo_filesize'] . $lang['photo_Bytes'], false);
        }

        $photopath = $CONFIG['photo_path'] . '/' . $newphotoname;
        $tmppath = $check['tmp_name'];

        if (!preg_match('/gif|jpeg|png|jpg|bmp/i', $photoext)) {
            error($lang['photo_invalid_ext'], false);
        }

        if (!is_writable($CONFIG['photo_path'])) {
            error($lang['photo_nowrite'], false);
        }

        $size = getimagesize($tmppath);
        $width = $size[0];
        $height = $size[1];
        $type = $size[2];

        if (!((bool) ini_get('safe_mode'))) {
            set_time_limit(30);
        }

        $imginfo = getimagesize($tmppath);
        $type = $imginfo[2];

        switch ($type) {
            case IMAGETYPE_GIF:
                if (!function_exists('imagecreatefromgif')) {
                    return $tmppath;
                }
                $srcImage = imagecreatefromgif($tmppath);
                break;
            case IMAGETYPE_JPEG:
                if (!function_exists('imagecreatefromjpeg')) {
                    return $tmppath;
                }
                $srcImage = imagecreatefromjpeg($tmppath);
                break;
            case IMAGETYPE_PNG:
                if (!function_exists('imagecreatefrompng')) {
                    return $tmppath;
                }
                $srcImage = imagecreatefrompng($tmppath);
                break;
            case IMAGETYPE_WBMP:
                if (!function_exists('imagecreatefromwbmp')) {
                    return $tmppath;
                }
                $srcImage = imagecreatefromwbmp($tmppath);
                break;
            default:
                return $tmppath;
        }

        if ($width > $CONFIG['photo_max_width']) {
            $newwidth = $CONFIG['photo_new_width'];
            $newheight = ($newwidth / $width) * $height;
        } else
        if ($height > $CONFIG['photo_max_height']) {
            $newheight = $CONFIG['photo_new_height'];
            $newwidth = ($newheight / $height) * $width;
        }

        if (isset($newwidth)) {
            $destImage = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            switch ($type) {
                case IMAGETYPE_GIF:
                    imagegif($destImage, $tmppath);
                    break;
                case IMAGETYPE_JPEG:
                    imagejpeg($destImage, $tmppath);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($destImage, $tmppath);
                    break;
                case IMAGETYPE_WBMP:
                    imagewbmp($destImage, $tmppath);
                    break;
                    imagedestroy($srcImage);
                    imagedestroy($destImage);
            }
        }

        copy($tmppath, $photopath);
        $db->query("UPDATE " . X_PREFIX . "members SET photo = '$photopath' WHERE uid = '$memberid'");
    }

    if (isset($_POST['newphoto']) && empty($_FILES['photofile']['name'])) {
        $db->query("UPDATE " . X_PREFIX . "members SET photo = '$photo' WHERE uid = '$memberid'");
    }

    if (onSubmit('editsubmit') && isset($_POST['photodel']) != 1 && !empty($member['photo']) && empty($_POST['newphoto']) && empty($_FILES['photofile']['name'])) {
        $db->query("UPDATE " . X_PREFIX . "members SET photo = '$member[photo]' WHERE uid = '$memberid'");
    }

    if (isset($_POST['photodel']) && isset($_POST['photodel']) == 1 && empty($_FILES['photofile']['name'])) {
        if (file_exists($member['photo'])) {
            unlink($member['photo']);
        }
        $db->query("UPDATE " . X_PREFIX . "members SET photo = '' WHERE uid = '$memberid'");
    }

    $db->query("UPDATE " . X_PREFIX . "members SET
        email = '$email',
        site = '$site',
        aim = '$aim',
        location = '$location',
        bio = '$bio',
        sig = '$sig',
        showemail = '$showemail',
        timeoffset = '$timeoffset1',
        icq = '$icq',
        yahoo = '$yahoo',
        theme = '$thememem',
        bday = '$bday',
        langfile = '$langfilenew',
        tpp = '$tppnew',
        ppp = '$pppnew',
        newsletter = '$newsletter',
        timeformat = '$timeformatnew',
        msn = '$msn',
        dateformat = '$dateformatnew',
        mood = '$mood',
        invisible = '$invisible',
        saveogpm = '$saveogpm',
        emailonpm = '$emailonpm',
        daylightsavings = '$daylightsavings1',
        viewavatars = '$viewavatars',
        psorting = '$psorting',
        viewsigs = '$viewsigs',
        firstname = '$firstname',
        lastname = '$lastname',
        showname = '$showname',
        customstatus = '$customstatus',
        occupation = '$occupation',
        blog = '$blog',
        expview = '$expview'
        WHERE uid = '$memberid'
    ");

    $newpassword = trim(formVar('newpassword'));
    $newpasswordcf = trim(formVar('newpasswordcf'));
    if (!empty($newpassword) != '' && !empty($newpasswordcf)) {
        if ($newpassword != $newpasswordcf) {
            error($lang['pwnomatch'], false);
        }
        $newpassword = md5(trim($newpassword));

        $db->query("UPDATE " . X_PREFIX . "members SET password = '$newpassword' WHERE uid = '$memberid'");
    }
    message($lang['textsettingsupdate'], false, '', '', '' . 'admin/index.php', true, false, true);
}

loadtime();
eval('echo "' . template('footer') . '";');
