<?php

/**
 * GaiaBB
 * Copyright (c) 2011-2025 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB's installer (ajv)
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
 **/

// phpcs:disable PSR1.Files.SideEffects
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param  $varname type,
 *                  what it does
 * @return type, what the return does
 */
function process_upgrade_config()
{
    $_SESSION['resetset'] = formOnOff('resetset');
}

require_once "upgrade.ultimabb.php";

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param  $varname type,
 *                  what it does
 * @return type, what the return does
 */
function upgrade_forum($path, $prgbar)
{
    $warn = false;
    setBar($prgbar, 0.01);

    $version = phpversion();
    if (version_compare($version, "5.6.0", "<")) {
        setCol($prgbar, '#ff0000');
        print_error(
            'Version error',
            'GaiaBB requires PHP 5.6.0 or later and prefers the latest version.'
        );
    }

    if (version_compare($version, "7.2.31", "<")) {
        setCol($prgbar, '#ffff00');
        print_error(
            'Version warning',
            'GaiaBB prefers recent PHP releases. Strongly consider upgrading the version of PHP you are using.',
            false
        );
        $warn = true;
    }

    setBar($prgbar, 0.02);

    $error = check_folders();
    if ($error !== true) {
        setCol($prgbar, '#ff0000');
        print_error('Configuration error', "Cannot find $error. Please upload GaiaBB correctly and start again.");
    }

    setBar($prgbar, 0.03);

    $error = check_files();
    if ($error !== true) {
        setCol($prgbar, '#ff0000');
        print_error('Configuration error', "Cannot find $error. Please upload GaiaBB correctly and start again.");
    }

    setBar($prgbar, 0.04);

    $database = '';
    $pconnect = '';
    $dbname = '';
    $dbhost = '';
    $tablepre = '';
    $dbuser = '';
    $dbpw = '';

    include_once 'config.php';
    if ($database == 'DBTYPE' || !file_exists(ROOT . "db/$database.php")) {
        setCol($prgbar, '#ff0000');
        print_error('Database connection', 'Please ensure that config.php has been successfully written prior to running this install.');
    }
    include_once "../db/mariadb.class.php";

    setBar($prgbar, 0.05);

    if (!check_db_api($database)) {
        setCol($prgbar, '#ff0000');
        print_error('Database connection', 'GaiaBB does not support the configured type of database.');
    }

    setBar($prgbar, 0.06);

    define('X_PREFIX', $tablepre);

    $db = new GaiaBB\MariaDB();
    $db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true);

    setBar($prgbar, 0.07);

    $version = $db->getVersion();
    if (version_compare($version, "5.7.0", "<")) {
        setCol($prgbar, '#ffff00');
        print_error('Database warning', 'GaiaBB requires MariaDB 10.1 (or MySQL 5.7) or later. In all cases, GaiaBB prefers the latest version.', false);
        $warn = true;
    }

    setBar($prgbar, 0.08);

    if (is_priv_db_user($dbuser)) {
        setCol($prgbar, '#ffff00');
        print_error('Security notice', 'Connecting to the database as a highly privileged user is strongly discouraged.', false);
        $warn = true;
    }

    setBar($prgbar, 0.09);

    $admin = is_admin($db, $tablepre);
    if (!$admin) {
        setCol($prgbar, '#ff0000');
        print_error(
            'Security notice',
            'The user specified is not a super administrator. The upgrade utility cannot continue.'
        );
    }

    setBar($prgbar, 0.1);

    $upgrade = new GaiaBB\UpgradeUltimaBB($db, $prgbar);

    // Reset the settings row to sane values and turn off board
    if ($_SESSION['resetset'] == 'on') {
        schema_create_settings($db, $tablepre);
        reset_settings($db, $tablepre);
    } else {
        $upgrade->migrateSettings($prgbar);
    }
    disable_gbb($db, $tablepre); // we turn the board off for safety reasons

    // Some operations take time. If your script fails, set this higher
    set_time_limit(300);

    $upgrade->addTables(0.15);
    $upgrade->renameTables(0.2);
    $upgrade->alterTables(0.3);
    $upgrade->migrateData(0.9);

    setBar($prgbar, 1.0);
    return $warn;
}
