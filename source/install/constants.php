<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB's installer (ajv)
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

define('ROOT', '../');

define('X_INST_ERR', 0);
define('X_INST_WARN', 1);
define('X_INST_OK', 2);
define('X_INST_SKIP', 3);

define('INSTALLVER', 'GaiaBB 1.0 M1');

define('INSTALLER', true);
define('X_REDIRECT_HEADER', 1);
define('X_REDIRECT_JS', 2);

define('SCHEMAVER', 41);

require_once '../include/validate.inc.php';
require_once '../include/functions.inc.php';

// DEBUG flag, useful for tracking down serious technical issues.

// To enable, change define('DEBUG', false) to define('DEBUG', true)

// This debug mode can help if you are having difficulty during
// *installation/upgrade/conversion* only.

// If you need to see what is happening after installation, you need to
// change the same flag in the include/constants.inc.php file, not this
// file.

// Installation DEBUG mode grants installation users super admin rights.
// This should be reasonably safe as you must know a super admin password
// to do anything, but just in case, here are the warnings:

// 1. DO NOT LEAVE DEBUG MODE ENABLED
// 2. DELETE THESE FILES AS SOON AS YOU'RE DONE WITH THEM
// 3. Batteries not included. Void where applicable. Not suitable for kittens.

define('DEBUG', false);

if (DEBUG) {
    error_reporting(E_ALL || E_STRICT || E_DEPRECATED);
    define('X_SADMIN', 1); // Danger Will Robinson!
    $debug_log = array();
} else {
    // Production
    error_reporting(E_ALL & ~E_NOTICE);
}
