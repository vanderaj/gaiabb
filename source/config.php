<?php

/**
 * GaiaBB
 * Copyright (c) 2011-2025 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
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
 **/

// phpcs:disable PSR1.Files.SideEffects
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

// Database connection settings
$dbname = 'DBNAME'; // Name of your database
$dbuser = 'DBUSER'; // Username used to access it
$dbpw = 'DBPW'; // Password used to access it
$dbhost = 'localhost'; // Database host, usually 'localhost'
$database = 'mariadb.class'; // Database type. mariadb.class is the only one supported
$pconnect = 0; // Persistent connection, 1 = on, 0 = off, use if 'too many connections'-errors appear
$tablepre = 'gaiabb_'; // Used in case you want to host multiple forums in the one database

// If you want to use SendGrid to send your mails, add an API key
$sendgridAPIkey = '';

// Change this if you want additional security in your installation
// Each installation should choose a random password of at least 8 characters
// If you change it, autologin will fail
define('GAIABB_MASTERKEY', '');
