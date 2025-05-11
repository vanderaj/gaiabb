<?php

/**
 * GaiaBB
 * Copyright (c) 2011-2022 The GaiaBB Group
 * https://github.com/vanderaj/gaiabb
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
 **/

// Production PHP error level, suppresses all warnings
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

if ((version_compare(phpversion(), "8.1.0")) < 0) {
    die("Unsupported PHP version");
}

define('IN_PROGRAM', true);

if (!defined('ROOT')) {
    define('ROOT', './');
}

if (!defined('ROOTINC')) {
    define('ROOTINC', './include/');
}

if (!defined('ROOTCLASS')) {
    define('ROOTCLASS', './class/');
}

$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];
$onlinetime = time();

// Include files used by all users of header.php
require ROOTINC . 'constants.inc.php';
require ROOTINC . 'validate.inc.php';
require ROOTINC . 'functions.inc.php';

// GZIP compression requires action to be set ... or it will not work

$user = isset($_REQUEST['user']) ? $_REQUEST['user'] : '';
$action = getVar('action');

// Enable Gzip compression for PHP that support it and boards that want it
// This must come AFTER constants.inc.php's inclusion, or it will not work
if (X_GZIP && $action != 'attachment') {
    if (($res = ini_get('zlib.output_compression')) === 1) {
        // leave it
    } elseif ($res === false) {
        // ini_get not supported. So let's just leave it
    } else {
        if (function_exists('gzopen')) {
            $r = ini_set('zlib.output_compression', 'Off');
            $r2 = ini_set('zlib.output_compression_level', '3');
            if (!$r || !$r2) {
                ob_start('ob_gzhandler');
            }
        } else {
            ob_start('ob_gzhandler');
        }
    }
}


if (!file_exists(ROOT . 'config.php')) {
    die('Error: Could not load configuration. Please try again.');
}

if (file_exists(ROOT . 'install/')) {
    die('Error: /install/ still exists in the root folder. Please remove it before continuing.');
}

require ROOT . 'config.php';

if (defined('DEBUG') && DEBUG == true) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
}

$useragent = $_SERVER['HTTP_USER_AGENT'];

$browser = 'generic';
$bbcode_js = '';
if (strpos($useragent, 'Opera') !== false) {
    $browser = 'opera';
    $bbcode_js = 'opera';
} elseif (strpos($useragent, 'MSIE') !== false && strpos($useragent, 'Opera') === false) {
    $browser = 'ie';
    $bbcode_js = 'ie';
} elseif (strpos($useragent, 'Gecko') !== false && strpos($useragent, 'Konqueror') === false) {
    if (strpos($useragent, 'Firefox') !== false) {
        $browser = 'firefox';
    } else {
        $browser = 'gecko';
    }
    $bbcode_js = 'mozilla';
} elseif (strpos($useragent, 'Safari') !== false) {
    $browser = 'safari';
    $bbcode_js = 'mozilla';
} elseif (strpos($useragent, 'Konqueror') !== false) {
    $browser = 'konqueror';
    $bbcode_js = 'mozilla';
}

// Resolve Server specific issues
$server = 'Apa'; // Pretend to be Apache by default
if (isset($_SERVER['SERVER_SOFTWARE'])) {
    $server = substr($_SERVER['SERVER_SOFTWARE'], 0, 3);
}

$url = '';
if (isset($_SERVER['REQUEST_URI'])) {
    $url = $_SERVER['REQUEST_URI'];
}

if (!file_exists(ROOT . 'db/' . $database . '.php')) {
    die('Error: Could not load database driver.');
}

require ROOT . 'db/' . $database . '.php';

$oToken = new page_token();
$oToken->init();

$contactLink = ROOT . 'contact.php?token=' . $oToken->get_new_token() . '';

// initialize navigation
nav();
btitle();

switch (CACHECONTROL) {
    // Use for pages where caching is problematic (possibly post?)
    // Use sparingly; for login pages, etc.
    case 'private':
    case 'nocache':
        header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Expires: -1");
        header("Pragma: no-cache");
        break;
    case 'IMAGE':
        header("Cache-Control: private");
        header("Pragma: public");
        break;
    case 'public':
    default:
        //        header("Cache-Control: private, no-cache=\"set-cookie\"");  // HTTP/1.1
        //        header("Expires: 0");
        //        header("Pragma: no-cache");
        break;
}

// Fix annoying bug in windows... *sigh*
if ($action != 'attachment' && !($action == 'templates' && onSubmit('download')) && !($action == 'themes' && onSubmit('download'))) {
    header("Content-type: text/html");
}

// Get visitors IP address (which is usually their transparent proxy)
// DO NOT USE HTTP_CLIENT_IP or HTTP_X_FORWARDED_FOR as these can (and are) forged by attackers. ajv
$onlineip = '';
if (isset($_SERVER['REMOTE_ADDR'])) {
    $onlineip = $_SERVER['REMOTE_ADDR'];
    // hack for IpV6

    if (strpos($onlineip, ':') == true) {
        // IPv6 found
        $onlineip = '127.0.0.1';    // any local addresses are IPv6 sourced
    }
}

// Load Objects, and such
$tables = array(
    'addresses',
    'adminlogs',
    'attachments',
    'banned',
    'dateformats',
    'faq',
    'favorites',
    'forums',
    'lastposts',
    'members',
    'modlogs',
    'plugins',
    'posts',
    'ranks',
    'restricted',
    'robots',
    'settings',
    'smilies',
    'subscriptions',
    'templates',
    'themes',
    'threads',
    'pm',
    'pm_attachments',
    'vote_desc',
    'vote_results',
    'vote_voters',
    'whosonline',
    'words'
);

foreach ($tables as $name) {
    $table[$name] = $tablepre . $name;
}

// create secure table prefix by John
define('X_PREFIX', $tablepre);

// TODO: Remove me when old DAL goes away
if (!defined('X_DBCLASSNAME')) {
    define('X_DBCLASSNAME', 'dbstuff');
}
$dalname = X_DBCLASSNAME;
$db = new $dalname();
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true);

// Make all settings global, and put them in the $CONFIG[] array
require ROOTCLASS . 'cache.class.php';
$config_cache = new cacheable('setcache', 60);

$CONFIG = $config_cache->getData('settings');
if ($CONFIG === null) {
    $sq = $db->query("SELECT * FROM " . X_PREFIX . "settings");
    while ($srow = $db->fetch_array($sq)) {
        $key = $srow['config_name'];
        $val = $srow['config_value'];

        $CONFIG[$key] = $val;
    }

    // Fixups

    if ($CONFIG['postperpage'] < 5) {
        $CONFIG['postperpage'] = 30;
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value='" . $CONFIG['postperpage'] . "' WHERE config_name='postperpage'");
    }

    if ($CONFIG['topicperpage'] < 5) {
        $CONFIG['topicperpage'] = 30;
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value='" . $CONFIG['topicperpage'] . "' WHERE config_name='topicperpage'");
    }

    // Add in inactive users if it doesn't already exist.

    if (!isset($CONFIG['inactiveusers'])) {
        $CONFIG['inactiveusers'] = 0;
        $db->query("INSERT INTO " . X_PREFIX . "settings (config_name, config_value) VALUES ('inactiveusers', '" . $CONFIG['inactiveusers'] . "')");
    }

    // Prune new users who have never logged in. Tempered by the number of days grace set in Admin > Settings.
    if ($CONFIG['inactiveusers'] > 0) {
        $inactivebefore = $onlinetime - (60 * 60 * 24 * $CONFIG['inactiveusers']);
        $db->query("DELETE FROM " . X_PREFIX . "members WHERE lastvisit = 0 AND regdate < $inactivebefore AND status = 'Member'");
    }

    $config_cache->setData('settings', $CONFIG);
}

// Get the moderators and cache them for later use
$moderators_cache = new cacheable('modcache', 3600);

$MODERATORS = $moderators_cache->getData('moderators');
if ($MODERATORS === null) {
    $MODERATORS = array();
    $modq = $db->query("SELECT moderator FROM " . X_PREFIX . "forums WHERE moderator != ''");
    while ($moda = $db->fetch_array($modq)) {
        $mods = explode(', ', $moda['moderator']);
        foreach ($mods as $mod_user) {
            $m_check = array_search(strtolower($mod_user), $MODERATORS);
            if ($m_check === false) {
                $modq2 = $db->query("SELECT DISTINCT uid FROM " . X_PREFIX . "members WHERE username = '$mod_user'");
                $mod_id = $db->result($modq2, 0);

                // To ensure that even if the admin makes a case-typo, the system still recognise each name
                $MODERATORS[$mod_id] = strtolower($mod_user);
            }
        }
    }
    $moderators_cache->setData('moderators', $MODERATORS);
}

// Create cookie-settings
$array = array();
if (!isset($CONFIG['boardurl']) || $CONFIG['boardurl'] == 'FULLURL' || $CONFIG['boardurl'] === '') {
    $cookiedomain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    $array['path'] = $_SERVER['PHP_SELF'];
    if ($array['path'] !== '/' && ROOT == './') {
        $array['path'] = substr($array['path'], 0, strrpos($array['path'], '/', -1)) . '/';
    } else {
        $array['path'] = substr($array['path'], 0, strrpos($array['path'], '/', -2)) . '/';
    }
} else {
    $cookiedomain = parse_url($CONFIG['boardurl'], PHP_URL_HOST);
}

if (strpos($cookiedomain, 'localhost') !== false || preg_match("/^([0-9]{1,3}\.){3}[0-9]{1,3}$/i", $cookiedomain)) {
    $cookiedomain  = '';
} else {
    $cookiedomain = str_replace('www', '', $cookiedomain);
}

$cookiepath = '';
if (isset($array['path'])) {
    $cookiepath = ($array['path'] == '/') ? '' : $array['path'];
}

    // Initialize Authentication

    require_once ROOTCLASS . 'authc.class.php';

    $authState = new AuthState();
    $authC = new AuthC();

    // Update last visit, old topics

    $authC->updateLastVisit();
    $authC->updateOldTopics();

    // Process login

    $authC->autoLogin();

if (empty($self['timeformat'])) {
    if ($CONFIG['timeformat'] == 24) {
        $self['timecode'] = "H:i";
    } else {
        $self['timecode'] = "h:i A";
    }
} else {
    if ($self['timeformat'] == 24) {
        $self['timecode'] = "H:i";
    } else {
        $self['timecode'] = "h:i A";
    }
}

// create static staff ranks for roles
// developed by vanderaj, John Briggs & Tularis
$role = array();
$role['sadmin'] = false;
$role['admin']  = false;
$role['smod']   = false;
$role['mod']    = false;
$role['staff']  = false;
if (X_MEMBER) {
    switch ($self['status']) {
        case 'Super Administrator':
            $role['sadmin'] = true;
            $role['admin']  = true;
            $role['smod']   = true;
            $role['mod']    = true;
            $role['staff']  = true;
            break;
        case 'Administrator':
            $role['sadmin'] = false;
            $role['admin']  = true;
            $role['smod']   = true;
            $role['mod']    = true;
            $role['staff']  = true;
            break;
        case 'Super Moderator':
            $role['sadmin'] = false;
            $role['admin']  = false;
            $role['smod']   = true;
            $role['mod']    = true;
            $role['staff']  = true;
            break;
        case 'Moderator':
            $role['sadmin'] = false;
            $role['admin']  = false;
            $role['smod']   = false;
            $role['mod']    = true;
            $role['staff']  = true;
            break;
        default:
            $role['sadmin'] = false;
            $role['admin']  = false;
            $role['smod']   = false;
            $role['mod']    = false;
            $role['staff']  = false;
            break;
    }
}
define('X_SADMIN', $role['sadmin']);
define('X_ADMIN', $role['admin']);
define('X_MOD', $role['mod']);
define('X_SMOD', $role['smod']);
define('X_STAFF', $role['staff']);

// Get the required language file
if (!file_exists(ROOT . 'lang/' . $self['langfile'] . '.lang.php')) {
    if (!file_exists(ROOT . 'lang/English.lang.php')) {
        die('Error: no languages available.');
    }
    $self['langfile'] = 'English';
}

// Just in case the language file is missing something important
$lang_code = 'EN';
$lang_dir = 'ltr';
$lang_align = 'left';
$lang_nalign = 'right';
$charset = 'ISO-8859-1';
require ROOT . 'lang/' . $self['langfile'] . '.lang.php';
header('Content-Type: text/html; charset=' . $charset);
header('Content-Language: ' . $lang_code);

// Prepare the mail system for use throughout the boards
// include(ROOTCLASS.'mail.class.php');
require ROOTCLASS . 'sendgrid.class.php';
$mailsys = new MailSys();

// Checks for the possibility to register
$reglink = '';
if ($CONFIG['regstatus'] == 'on' && X_GUEST) {
    $reglink = '- <a href="' . ROOT . 'register.php?action=coppa">' . $lang['textregister'] . '</a>';
}

// Creates login/logout links
if (X_MEMBER) {
    $loginout = '<a href="' . ROOT . 'logout.php?token=' . $oToken->get_new_token() . '">' . $lang['textlogout'] . '</a>';
    $usercp = '<a href="' . ROOT . 'usercp.php">' . $lang['textusercp'] . '</a>';
    $onlineuser = $self['username'];
    $robotname = $cplink = $pmlink = $modcplink = '';
    if (!($CONFIG['pmstatus'] == 'off' && isset($self['status']) && $self['status'] == 'Member')) {
        $pmlink = '<a href="' . ROOT . 'pm.php">' . $lang['banpm'] . '</a> - ';
    }

    if (X_ADMIN) {
        $cplink = ' - <a href="' . ROOT . 'admin/index.php">' . $lang['textcp'] . '</a>';
    }

    $notify = $lang['loggedin'] . ' <a href="' . ROOT . 'viewprofile.php?memberid=' . intval($self['uid']) . '"><strong>' . trim($self['username']) . '</strong></a> - [ ' . $loginout . ' - ' . $pmlink . '' . $usercp . '' . $modcplink . '' . $cplink . ' ]';
} else {
    $loginout = '<a href="' . ROOT . 'login.php">' . $lang['textlogin'] . '</a>';
    $onlineuser = 'xguest123';
    $self['status'] = '';
    $notify = '' . $lang['notloggedin'] . ' [ ' . $loginout . ' ' . $reglink . ' ]';
    $robotname = '';
    if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] != null) {
        $useragent = strtolower((string)$_SERVER['HTTP_USER_AGENT']);
        $rq = $db->query("SELECT LENGTH(robot_string) AS strlen, robot_string, robot_fullname FROM " . X_PREFIX . "robots ORDER BY strlen DESC");
        while ($result = $db->fetch_array($rq)) {
            if (strpos($useragent, $result['robot_string']) !== false) {
                $onlineuser = 'xrobot123';
                $robotname = $result['robot_fullname'];
                break;
            }
        }
        // $db->free_result($rq);
    }
}

// Checks if the timeformat has been set, if not, use default
if (empty($self['dateformat'])) {
    $self['dateformat'] = $CONFIG['dateformat'];
}

$dformatorig = $self['dateformat'];
$self['dateformat'] = str_replace(array('mm','dd','yyyy','yy'), array('n','j','Y','y'), $self['dateformat']);

$tid = getRequestInt('tid');
$fid = getRequestInt('fid');

// Get themes, [fid, [tid]]
// make sure that tid doesn't show as thread subject
// if templates are in use regarding editing etc actions
// special thanks to jamieC at XMB for fix.
if ($tid !== 0 && $action != 'templates') {
    $fid = $config_cache->getData('fid');
    $forumtheme = null;
    if ($fid !== null) {
        $fid = intval('fid');
        $forumtheme = $config_cache->getData('forumtheme' . $fid);
    }

    if ($forumtheme === null || $fid === null) {
        $q = $db->query("SELECT f.fid, f.theme FROM " . X_PREFIX . "forums f, " . X_PREFIX . "threads t WHERE f.fid = t.fid AND t.tid = '$tid'");
        while ($locate = $db->fetch_array($q)) {
            $fid = $locate['fid'];
            $forumtheme = $locate['theme'];
        }
        $db->free_result($q);
        $config_cache->setData('fid', '' . $fid . '');
        $config_cache->setData('forumtheme' . $fid, '' . $forumtheme . '');
    }
} elseif ($fid !== 0) {
    $config_cache->setData('fid', '' . $fid . '');
    $forumtheme = $config_cache->getData('forumtheme' . $fid);
    if ($forumtheme === null) {
        $q = $db->query("SELECT theme FROM " . X_PREFIX . "forums WHERE fid = '$fid'");
        if ($db->num_rows($q) === 1) {
            $forumtheme = $db->result($q, 0);
        } else {
            $forumtheme = 0;
        }
        $db->free_result($q);
        $config_cache->setData('forumtheme' . $fid, '' . $forumtheme . '');
    }
}

if ($CONFIG['whosoptomized'] == 'on') {
    $wollocation = addslashes(trim($url));
    $newtime = $onlinetime - 600;
    $username = isset($username) ? $username : '';

    $whosonlineDone = $config_cache->getData('whosonline');

    if ($whosonlineDone === null) {
        // clear out old entries and guests/robots
        $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE ip = '$onlineip' && (username = 'xguest123' OR username = 'xrobot123' OR username = '$self[username]') OR time < '$newtime'");
        $db->query("INSERT INTO " . X_PREFIX . "whosonline (username, ip, time, location, invisible, robotname) VALUES ('$onlineuser', '$onlineip', " . $db->time($onlinetime) . ", '$wollocation', '$self[invisible]', '$robotname')");

        $online24 = $onlinetime - (60 * 60 * 24);
        $db->query("DELETE FROM " . X_PREFIX . "guestcount WHERE ((ipaddress = '$onlineip') OR (onlinetime < '$online24'))");
        $db->query("DELETE FROM " . X_PREFIX . "robotcount WHERE ((ipaddress = '$onlineip') OR (onlinetime < '$online24'))");

        if ($onlineuser == 'xguest123') {
            $db->query("INSERT INTO " . X_PREFIX . "guestcount (ipaddress, onlinetime) VALUES ('$onlineip', '$onlinetime')");
        } elseif ($onlineuser == 'xrobot123') {
            $db->query("INSERT INTO " . X_PREFIX . "robotcount (ipaddress, onlinetime) VALUES ('$onlineip', '$onlinetime')");
        }

        if (X_MEMBER) {
            $result = $db->query("SELECT COUNT(username) FROM " . X_PREFIX . "whosonline WHERE (username = '$self[username]')");
            $usercount = $db->result($result, 0);
            if ($usercount > 1) {
                $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE (username = '$self[username]')");
                $db->query("INSERT INTO " . X_PREFIX . "whosonline (username, ip, time, location, invisible, robotname) VALUES ('$onlineuser', '$onlineip', " . $db->time($onlinetime) . ", '$wollocation', '$self[invisible]', '$robotname')");
            }
            $db->free_result($result);
        }
        $config_cache->setData('whosonline', 'true');
    }
} else {
    $wollocation = addslashes(trim($url));
    $newtime = $onlinetime - 600;
    $username = isset($username) ? $username : '';

    // clear out old entries and guests/robots
    $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE ((ip = '$onlineip' && (username = 'xguest123' OR username = 'xrobot123')) OR (username = '$self[username]') OR (time < '$newtime'))");
    $db->query("INSERT INTO " . X_PREFIX . "whosonline (username, ip, time, location, invisible, robotname) VALUES ('$onlineuser', '$onlineip', " . $db->time($onlinetime) . ", '$wollocation', '$self[invisible]', '$robotname')");

    $online24 = $onlinetime - (60 * 60 * 24);
    $db->query("DELETE FROM " . X_PREFIX . "guestcount WHERE ((ipaddress = '$onlineip') OR (onlinetime < '$online24'))");
    $db->query("DELETE FROM " . X_PREFIX . "robotcount WHERE ((ipaddress = '$onlineip') OR (onlinetime < '$online24'))");

    if ($onlineuser == 'xguest123') {
        $db->query("INSERT INTO " . X_PREFIX . "guestcount (ipaddress, onlinetime) VALUES ('$onlineip', '$onlinetime')");
    } elseif ($onlineuser == 'xrobot123') {
        $db->query("INSERT INTO " . X_PREFIX . "robotcount (ipaddress, onlinetime) VALUES ('$onlineip', '$onlinetime')");
    }

    if (X_MEMBER) {
        $result = $db->query("SELECT COUNT(username) FROM " . X_PREFIX . "whosonline WHERE (username = '$self[username]')");
        $usercount = $db->result($result, 0);
        if ($usercount > 1) {
            $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE (username = '$self[username]')");
            $db->query("INSERT INTO " . X_PREFIX . "whosonline (username, ip, time, location, invisible, robotname) VALUES ('$onlineuser', '$onlineip', " . $db->time($onlinetime) . ", '$wollocation', '$self[invisible]', '$robotname')");
        }
        $db->free_result($result);
    }
}

// Check what theme to use
if (!empty($forumtheme) && (int) $forumtheme > 0) {
    $theme = (int) $forumtheme;
} elseif (!empty($self['theme']) && (int) $self['theme'] > 0) {
    $theme = (int) $self['theme'];
} else {
    $theme = (int) $CONFIG['theme'];
}

// Make theme-vars semi-global
$THEME = $config_cache->getData('theme');
if ($THEME === null) {
    $tquery = $db->query("SELECT * FROM " . X_PREFIX . "themes WHERE themeid = '$theme'");
    foreach ($db->fetch_array($tquery) as $key => $val) {
        if ($key != 'name') {
            $$key = $val;
        } else {
            // make themes with apostrophes safe to display
            $val = stripslashes($val);
        }
        $THEME[$key] = $val;
    }
    $db->free_result($tquery);
    $config_cache->setData('theme', $THEME);
}

// Alters certain visibility-variables
$THEME['imgdir'] = ROOT . $THEME['imgdir'];

// CSS file load for addons
if (file_exists($THEME['imgdir'] . '/theme.css')) {
    $cssadd = '<style type="text/css">' . "\n" . "@import url('" . $THEME['imgdir'] . "/theme.css');" . "\n" . '</style>';
} else {
    $cssadd = '';
}

if (false === strpos($THEME['bgcolor'], '.')) {
    $bgcode = 'bgcolor="' . $THEME['bgcolor'] . '"';
} else {
    $bgcode = 'background="' . $THEME['imgdir'] . '/' . $THEME['bgcolor'] . '"';
}

if (false === strpos($THEME['outerbgcolor'], '.')) {
    $outerbgcode = 'bgcolor="' . $THEME['outerbgcolor'] . '"';
} else {
    $outerbgcode = 'background="' . $THEME['imgdir'] . '/' . $THEME['outerbgcolor'] . '"';
}

// create theme based ourtables and corners
switch ($THEME['outertable']) {
    case 'round':
        $background = $outerbgcode;
        $topcorners = '<table cellspacing="0px" cellpadding="0px" border="0px" width="' . $THEME['outertablewidth'] . '" align="center"><tr><td align="left"><img src="' . $THEME['imgdir'] . '/top_left.gif" alt="" title="" border="0px" /></td><td align="center" width="100%" ' . $bgcode . '><img src="' . $THEME['imgdir'] . '/pixel.gif" alt="" title="" border="0px" /></td><td align="right"><img src="' . $THEME['imgdir'] . '/top_right.gif" alt="" title="" border="0px" /></td></tr><tr><td ' . $bgcode . ' colspan="3">';
        $bottomcorners = '</td></tr><tr><td align="left"><img src="' . $THEME['imgdir'] . '/bottom_left.gif" alt="" title="" border="0px" /></td><td align="center" width="100%" ' . $bgcode . '><img src="' . $THEME['imgdir'] . '/pixel.gif" alt="" title="" border="0px" /></td><td align="right"><img src="' . $THEME['imgdir'] . '/bottom_right.gif" alt="" title="" border="0px" /></td></tr></table>';
        break;
    case 'square':
        $background = $outerbgcode;
        $topcorners = '<table cellspacing="0px" cellpadding="0px" border="0px" width="' . $THEME['outertablewidth'] . '" bgcolor="' . $THEME['outerbordercolor'] . '" align="center"><tr><td><table border="0px" cellspacing="' . $THEME['outerborderwidth'] . '" cellpadding="' . $THEME['tablespace'] . '" width="100%" align="center"><tr><td ' . $bgcode . '><br />';
        $bottomcorners = '<br /></td></tr></table></td></tr></table>';
        break;
    default:
        $background = $bgcode;
        $topcorners = $bottomcorners = '';
        break;
}

if (false === strpos($THEME['catcolor'], '.')) {
    $catbgcode = 'bgcolor="' . $THEME['catcolor'] . '"';
    $catcss = 'background-color: ' . $THEME['catcolor'] . ';';
} else {
    $catbgcode = 'style="background-image: url(' . $THEME['imgdir'] . '/' . $THEME['catcolor'] . ')"';
    $catcss = 'background-image: url(' . $THEME['imgdir'] . '/' . $THEME['catcolor'] . ');';
}

if (false === strpos($THEME['top'], '.')) {
    $topbgcode = 'bgcolor="' . $THEME['top'] . '"';
} else {
    $topbgcode = 'style="background-image: url(' . $THEME['imgdir'] . '/' . $THEME['top'] . ')"';
}

// create navigation symbol
$THEME['navsymbol'] = (isset($THEME['navsymbol']) ? $THEME['navsymbol'] : '&raquo;');
if (stristr($THEME['navsymbol'], '.bmp') || stristr($THEME['navsymbol'], '.gif') || stristr($THEME['navsymbol'], '.jpg') || stristr($THEME['navsymbol'], '.png')) {
    $THEME['navsymbol'] = '<img src="' . $THEME['imgdir'] . '/' . $THEME['navsymbol'] . '" border="0px" alt="' . $lang['navsymbolalt'] . '" title="' . $lang['navsymbolalt'] . '" />';
}

// check if it's an URL or just a image
if (strlen($THEME['boardimg'] = trim($THEME['boardimg'])) > 0) {
    $l = array();
    $l = parse_url($THEME['boardimg']);
    if (!isset($l['scheme']) || !isset($l['host'])) {
        $THEME['boardimg'] = $THEME['imgdir'] . '/' . $THEME['boardimg'];
    }
    $logo = '<a href="' . ROOT . 'index.php"><img src="' . $THEME['boardimg'] . '" alt="' . $lang['altboardlogo'] . '" title="' . $lang['altboardlogo'] . '" border="0px" /></a>';
} else {
    $logo = '<a href="' . ROOT . 'index.php">' . stripslashes($CONFIG['bbname']) . '</a>';
}

// Font stuff...
$fontedit = preg_replace('#(\D)#', '', $THEME['fontsize']);
$fontsuf = preg_replace('#(\d)#', '', $THEME['fontsize']);

$THEME['font1'] = $fontedit - 1 . $fontsuf;
$THEME['font2'] = $THEME['fontsize'];
$THEME['font3'] = $fontedit + 2 . $fontsuf;

// Update lastvisit in the header shown
if (isset($lastvisit) && X_MEMBER) {
    $theTime = $ubblva + ($self['timeoffset'] * 3600) + $self['daylightsavings'];
    $lastdate = gmdate($self['dateformat'], $theTime);
    $lasttime = gmdate($self['timecode'], $theTime);
    $lastvisittext = $lang['lastactive'] . ' ' . $lastdate . ' ' . $lang['textat'] . ' ' . $lasttime;
} else {
    $lastvisittext = $lang['lastactive'] . ' ' . $lang['textnever'];
}

// begin naviagtion header links
// Search-link
if (isset($CONFIG['siteurl']) && !empty($CONFIG['siteurl'])) {
    $links[] = '<img src="' . $THEME['imgdir'] . '/home.gif" alt="' . $lang['texthome'] . '" title="' . $lang['texthome'] . '" border="0px" />&nbsp;<a href="' . $CONFIG['siteurl'] . '"><font class="navtd">' . $lang['texthome'] . '</font></a>';
}

// Search-link
if (X_MEMBER && $CONFIG['searchstatus'] == 'on') {
    $links[] = '<img src="' . $THEME['imgdir'] . '/search.gif" alt="' . $lang['altsearch'] . '" title="' . $lang['altsearch'] . '" border="0px" />&nbsp;<a href="' . ROOT . 'search.php"><font class="navtd">' . $lang['textsearch'] . '</font></a>';
}

// Faq-link
if ($CONFIG['faqstatus'] == 'on') {
    $links[] = '<img src="' . $THEME['imgdir'] . '/faq.gif" alt="' . $lang['altfaq'] . '" title="' . $lang['altfaq'] . '" border="0px" />&nbsp;<a href="' . ROOT . 'faq.php"><font class="navtd">' . $lang['textfaq'] . '</font></a>';
}

// Member List-link
if (X_MEMBER && $CONFIG['memliststatus'] == 'on') {
    $links[] = '<img src="' . $THEME['imgdir'] . '/members_list.gif" alt="' . $lang['altmemberlist'] . '" title="' . $lang['altmemberlist'] . '" border="0px" />&nbsp;<a href="' . ROOT . 'memberlist.php?action=list"><font class="navtd">' . $lang['textmemberlist'] . '</font></a>';
}

// Topic Activity-link
if ($CONFIG['topicactivity_status'] == 'on') {
    $links[] = '<img src="' . $THEME['imgdir'] . '/todays_posts.gif" alt="' . $lang['topicactivityalt'] . '" title="' . $lang['topicactivityalt'] . '" border="0px" />&nbsp;<a href="' . ROOT . 'activity.php?days=7"><font class="navtd">' . $lang['topicactivity'] . '</font></a>';
}

// Stats-link
if ($CONFIG['stats'] == 'on') {
    $links[] = '<img src="' . $THEME['imgdir'] . '/stats.gif" alt="' . $lang['altstats'] . '" title="' . $lang['altstats'] . '" border="0px" />&nbsp;<a href="' . ROOT . 'stats.php"><font class="navtd">' . $lang['navstats'] . '</font></a>';
}

// Contact Us-link
if ($CONFIG['contactus'] == 'on') {
    $links[] = '<img src="' . $THEME['imgdir'] . '/contact.gif" alt="' . $lang['contactus'] . '" title="' . $lang['contactus'] . '" border="0px" />&nbsp;<a href="' . $contactLink . '"><font class="navtd">' . $lang['contactus'] . '</font></a>';
}

// Board Rules-link
if ($CONFIG['bbrules'] == 'on') {
    $links[] = '<img src="' . $THEME['imgdir'] . '/bbrules.gif" alt="' . $lang['altrules'] . '" title="' . $lang['altrules'] . '" border="0px" />&nbsp;<a href="' . ROOT . 'faq.php?page=forumrules"><font class="navtd">' . $lang['textbbrules'] . '</font></a>';
}

$links = implode('&nbsp;&nbsp;', $links);

// Fix for pluglinks to expire cache on dir change
$currentdir = $config_cache->getWorkDir();
$workdir = $config_cache->getData('workdir');
if ($workdir === null || $workdir != $currentdir) {
    $workdir = $config_cache->getWorkDir('yes');
    $config_cache->expire('pluglinks');
}

// pluglink system for adding new links in header nav
$pluglinks = $config_cache->getData('pluglinks');
if ($pluglinks === null) {
    $pluglinks = getPlugLinks();
}
$pluglink = implode('&nbsp;', $pluglinks);

// sanity check maximum registrations
if (!isset($CONFIG['max_reg_day']) || $CONFIG['max_reg_day'] < 1 || $CONFIG['max_reg_day'] > 100) {
    $CONFIG['max_reg_day'] = 25;
}

// display version build (John)
if ($CONFIG['show_full_info'] == 'on') {
    $versionlong = '<br />Powered by <a href="http://www.GaiaBB.com" target="_blank"><strong>' . $versionshort . '</strong></a> (' . $alpha . '' . $beta . '' . $gamma . '' . $sp . '), &copy; 2011 The GaiaBB Group';
} else {
    $versionlong = '<br />Powered by <a href="http://www.GaiaBB.com" target="_blank"><strong>' . $versionshort . '</strong></a>, &copy; 2011 The GaiaBB Group';
}

// If the board is offline, display an appropriate message
if ($CONFIG['bbstatus'] == 'off' && !(X_ADMIN) && false === strpos($url, "login.php") && false === strpos($url, "logout.php") && false === strpos($url, "contact.php")) {
    eval('$css = "' . template('css') . '";');
    $CONFIG['bboffreason'] = postify($CONFIG['bboffreason']);
    $shadow = shadowfx();
    $meta = metaTags();
    message(stripslashes($CONFIG['bboffreason']));
}

// If the board is set to 'reg-only' use, check if someone is logged in, and if not display a message
if ($CONFIG['regviewonly'] == 'on') {
    if (X_GUEST && $action != 'reg' && $action != 'coppa' && $action != 'captcha' && false === strpos($url, "lostpw.php") && false === strpos($url, "login.php") && false === strpos($url, "logout.php")) {
        if ($CONFIG['coppa'] == 'on') {
            $message = $lang['reggedonly'] . ' <a href="' . ROOT . 'register.php?action=coppa">' . $lang['textregister'] . '</a> ' . $lang['textor'] . ' <a href="' . ROOT . 'login.php">' . $lang['textlogin'] . '</a>';
        } else {
            $message = $lang['reggedonly'] . ' <a href="' . ROOT . 'register.php?action=reg">' . $lang['textregister'] . '</a> ' . $lang['textor'] . ' <a href="login.php">' . $lang['textlogin'] . '</a>';
        }
        eval('$css = "' . template('css') . '";');
        $shadow = shadowfx();
        $meta = metaTags();
        message($message);
    }
}

// define path to javascript files
if (strpos($url, '/admin/') !== false) {
    $js_path = '..';
} else {
    $js_path = '.';
}

// Check if the user is ip-banned
$ips = explode('.', $onlineip);
// also disable 'ban all'-possibility
$qre = $db->query("SELECT id FROM " . X_PREFIX . "banned WHERE ((ip1 = '$ips[0]' OR ip1 = '-1') AND (ip2 = '$ips[1]' OR ip2 = '-1') AND (ip3 = '$ips[2]' OR ip3 = '-1') AND (ip4 = '$ips[3]' OR ip4 = '-1')) AND NOT (ip1 = '-1' AND ip2 = '-1' AND ip3 = '-1' AND ip4 = '-1')");
$result = $db->fetch_array($qre);
$db->free_result($qre);

// don't *ever* ban a (super-)admin!
if (!X_ADMIN && (isset($self['status']) && $self['status'] == 'Banned' || $result)) {
    eval('$css = "' . template('css') . '";');
    error($lang['bannedmessage']);
}

// check if user needs to be forced to logout
$authC->checkForceLogout();

// check if user needs to be forced to read board rules
// credited to FunForum
if (isset($self['status']) && $self['status'] == 'Member' && $CONFIG['bbrules'] == 'on' && isset($self['status']) && $self['readrules'] == 'yes') {
    if (!strstr($url, 'faq.php?page=agreerules')) {
        $fly = explode('/', $_SERVER['REQUEST_URI']); // Only want filename
        redirect('faq.php?page=agreerules&flyto=' . $fly[count($fly) - 1], 0);
    }
}

// if the user is logged in, check for new pm's
$newpmmsg = $config_cache->getData('newpmmsg');
if (X_MEMBER && $newpmmsg === null && !($CONFIG['pmstatus'] == 'off' && isset($self['status']) && $self['status'] == 'Member')) {
    $qpm = $db->query("SELECT COUNT(readstatus) FROM " . X_PREFIX . "pm WHERE owner = '$self[username]' AND folder = 'Inbox' AND readstatus = 'no'");
    $newpmnum = $db->result($qpm, 0);
    $db->free_result($qpm);
    if ($newpmnum > 0) {
        $newpmmsg = '<a href="' . ROOT . 'pm.php">' . $lang['newpm1'] . ' <strong>' . $newpmnum . '</strong> ' . $lang['newpm2'] . '</a>';
    }
    $config_cache->setData('newpmmsg', base64_encode($newpmmsg));
} else {
    $newpmmsg = base64_decode($newpmmsg);
}

// create forumjump
$quickjump = $config_cache->getData('forumjump');
if ($CONFIG['forumjump'] == 'on' && $quickjump === null) {
    $quickjump = forumJump();
    $config_cache->setData('forumjump', base64_encode($quickjump));
} else {
    $quickjump = base64_decode($quickjump);
}

// do security checks for certain scripts and actions
securityChecks();

// stripslashes for common output
$CONFIG['copyright'] = stripslashes($CONFIG['copyright']);
$CONFIG['bbname'] = stripslashes($CONFIG['bbname']);
$CONFIG['sitename'] = stripslashes($CONFIG['sitename']);
$CONFIG['siteurl'] = stripslashes($CONFIG['siteurl']);
$CONFIG['indexnewstxt'] = stripslashes($CONFIG['indexnewstxt']);

validateTpp();
validatePpp();
