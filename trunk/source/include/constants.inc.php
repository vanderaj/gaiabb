<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2013 The GaiaBB Group
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

// check to ensure no direct viewing of page
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false))
{
    exit('This file is not designed to be called directly');
}

/*
 * DEBUG Mode
 *
 * DO NOT CHANGE THIS UNLESS YOU KNOW WHAT YOU ARE DOING
 *
 * Debug mode has several effects:
 *
 * - Enables SQL queries for Super Administrators at the bottom of the screen
 * - Detailed error messages in SQL classes and other areas for super admins
 * - Any warning or error messages which are normally suppressed by PHP for all users
 * - Performs a few more checks(not unit tests) for all users
 * - Slows performance by about 5% for all users
 *
 * Turn DEBUG on by changing define('DEBUG', false); to define('DEBUG', true);
*/

// Development
define('DEBUG', false);

// Production
if (!defined('DEBUG')) {
    define('DEBUG', false);
}

// 0 = completely off
// 1 = normal, debug is enabled for X_SADMIN and above
// 2 = more, debug is enabled for X_MEMBER and above (so no banned or guest debug messages)
// 3 = all, debug is enabled for everyone (including banned and guests)
define('DEBUGLEVEL', 1);

// Production
if (!defined('DEBUGLEVEL')) {
    define('DEBUGLEVEL', 0);
}

// Change this if you want additional security in your installation
define('GAIABB_MASTERKEY', 'sq^%L4Ld/<*C~WG)');

/* Product name and version
 *
 * Change these as necessary.
*/
$versionpowered = ' - Powered by GaiaBB';
$versioncompany = 'The GaiaBB Group';
$versionshort = 'GaiaBB';
$versiongeneral = 'GaiaBB 1.0-HEAD';
$versioncopyright = 'GaiaBB 1.0-HEAD, &copy; 2011-2013 The GaiaBB Group';
$versionbuild = '2013041301';
$alpha = '';
$beta = '';
$gamma = '1.0-HEAD dev sprint 2';
$sp = '';

// No user serviceable items below
define('X_CACHE_GET', 1);
define('X_CACHE_PUT', 2);
define('X_SET_HEADER', 1);
define('X_SET_JS', 2);
define('X_SET_CHOOSE', 4);
define('X_REDIRECT_HEADER', 1);
define('X_REDIRECT_JS', 2);
define('X_SHORTEN_SOFT', 1);
define('X_SHORTEN_HARD', 2);

$cookiepath = '';
$cookiedomain = '';
$ubblva = 0;
$ubblvb = 0;
$bbcodescript = '';
$attachscript = '';
$navigation = '';
$btitle = '';
$filename = '';
$filetype = '';
$fileheight = '';
$filewidth = '';
$shadow = '';
$shadow2 = '';
$newpmmsg = '';
$meta = '';
$quickjump = '';
$cssadd = '';

$tpp = 0;
$ppp = 0;
$filesize = 0;
$forumtheme = 0;

$self = array();
$footerstuff = array();
$CONFIG = array();
$THEME = array();
$links = array();
$lang = array();
$member = array();
$robot = array();

$selHTML = 'selected="selected"';
$cheHTML = 'checked="checked"';

// Initialise pre-set Variables
// These strings can be pulled for use on any page as header is required by all GaiaBB pages
define('GAIABB_VERSION', $versiongeneral);
define('GAIABB_BUILD', $versionbuild);
define('GAIABB_COPYRIGHT', $versioncopyright);

// Cache-control
if (!defined('CACHECONTROL'))
{
    define('CACHECONTROL', 'public');
}

// Use Gzip Page Compression?
// set to false to turn off
// unlikely scenario though.
// Requires PHP 4.0.4 or higher
define('X_GZIP', true);

// leave this alone unless you know what you are doing
if (!defined('X_GZIP'))
{
    define('X_GZIP', false);
}
?>
