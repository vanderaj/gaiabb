<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2015 The GaiaBB Group
 * http://www.GaiaBB.com
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
if (! defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

/**
 * function() - short description of function
 *
 * Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *        
 */
function install_forum($prgbar)
{
    $warn = false;
    
    setBar($prgbar, 0.05);
    
    $version = phpversion();
    if (version_compare($version, "5.4.0") < 0) {
        setCol($prgbar, '#ff0000');
        print_error('Version warning', 'GaiaBB requires PHP 5.4.0 or later and prefers the latest version.');
    }
    
    if (version_compare($version, "5.5.13") < 0) {
        setCol($prgbar, '#ffff00');
        print_error('Version warning', 'GaiaBB prefers recent PHP releases. Strongly consider upgrading the version of PHP you are using.', false);
        $warn = true;
    }
    
    setBar($prgbar, 0.08);
    
    $error = check_folders();
    if ($error !== true) {
        setCol($prgbar, '#ff0000');
        print_error('Configuration error', "Cannot find $error. Please upload GaiaBB correctly and start again.");
    }
    
    $badFolders = find_nonwritable_folders();
    if (! empty($badFolders)) {
        setCol($prgbar, '#ffff00');
        
        print_error('Permissions error', "Set the following folders to be writable by the web server: " . implode(", ", $badFolders));
    }
    
    setBar($prgbar, 0.1);
    
    $error = check_files();
    if ($error !== true) {
        setCol($prgbar, '#ff0000');
        print_error('Configuration error', "Cannot find $error. Please upload GaiaBB correctly and start again.");
    }
    
    setBar($prgbar, 0.2);
    
    $database = '';
    $pconnect = '';
    $dbname = '';
    $dbhost = '';
    $tablepre = '';
    $dbuser = '';
    $dbpw = '';
    
    if (! file_exists('../config.php')) {
        setCol($prgbar, '#ff0000');
        print_error('Configuration file', 'Please ensure that config.php has been successfully written prior to running this install.');
    }
    
    require '../config.php';
    
    if ($dbname == 'DBNAME') {
        setCol($prgbar, '#ff0000');
        
        if (DEBUG) {
            echo "<pre>" . var_export(array(
                $dbname,
                $dbpw,
                $dbhost,
                $tablepre,
                $dbuser
            )) . "</pre>";
        }
        
        print_error('Database connection', 'The config file seems to be the default. Have you configured it correctly?');
    }
    
    if ($database == 'DBTYPE' || ! file_exists(ROOT . "db/$database.php")) {
        setCol($prgbar, '#ff0000');
        
        if (DEBUG) {
            echo "<pre>" . var_export(array(
                $dbname,
                $dbpw,
                $dbhost,
                $tablepre,
                $dbuser
            )) . "</pre>";
        }
        
        print_error('Database connection', 'The database file ' . 'db/' . $database . '.php does not exist. Please try again');
    }
    require_once ('../db/mysql5php5.class.php');
    
    setBar($prgbar, 0.3);
    
    if (! check_db_api($database)) {
        setCol($prgbar, '#ff0000');
        print_error('Database connection', 'GaiaBB does not support the configured type of database.');
    }
    
    setBar($prgbar, 0.35);
    
    if (! defined('X_PREFIX')) {
        define('X_PREFIX', $dbname . '.' . $tablepre);
    }
    
    $db = new mysql5Php5();
    $db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, false); // don't force panics
    
    setBar($prgbar, 0.4);
    
    $version = $db->getVersion();
    if (version_compare($version, "5.1.0") < 0) {
        setCol($prgbar, '#ffff00');
        print_error('Database warning', 'GaiaBB requires MariaDB 5.1 or later and prefers the latest version.', false);
        $warn = true;
    }
    
    setBar($prgbar, 0.45);
    
    if (isInstalled($db)) {
        setCol($prgbar, '#ff0000');
        print_error('Installation error', 'The installer has detected a previously installed version. Please remove the members table to continue', true);
        exit();
    }
    
    if (is_priv_db_user($dbuser)) {
        setCol($prgbar, '#ffff00');
        print_error('Security notice', 'Connecting to the database as a highly privileged user is strongly discouraged.', false);
        $warn = true;
    }
    
    setBar($prgbar, 0.47);
    
    $error = create_tables($db, $tablepre, $prgbar, 0.5, 0.01);
    if ($error !== 0) {
        setCol($prgbar, '#ff0000');
        print_error('Database creation error', $error);
    }
    
    $error = insert_data($db, $tablepre, $prgbar, 0.8, 0.01);
    if ($error !== 0) {
        setCol($prgbar, '#ff0000');
        print_error('Data insertion error', $error);
    }
    
    setBar($prgbar, 1);
    
    return $warn;
}
?>