<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2014 The GaiaBB Group
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

require_once ('common.model.php');

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
function process_repair_config()
{
    $_SESSION['summary'] = formOnOff('summary');
    $_SESSION['resttemp'] = formOnOff('resttemp');
    $_SESSION['resetset'] = formOnOff('resetset');
    
    $_SESSION['config'] = "off";
    $_SESSION['createsa'] = "off";
    $_SESSION['disablesa'] = "off";
    if (file_exists('./emergency.php')) {
        $_SESSION['config'] = formOnOff('config');
        $_SESSION['createsa'] = formOnOff('createsa');
        $_SESSION['disablesa'] = formOnOff('disablesa');
    }
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
function repair_forum($path, $prgbar)
{
    $warn = false;
    
    setBar($prgbar, 0.05);
    
    if ($_SESSION['config'] == 'on' && file_exists('./emergency.php') && formVar('confMethod') !== 'skip') {
        process_config($path);
    }
    
    setBar($prgbar, 0.1);
    
    $version = phpversion();
    if (version_compare($version, "5.3.2", "<")) {
        setCol($prgbar, '#ff0000');
        print_error('Version warning', 'GaiaBB requires PHP 5.3.2 or later and prefers the latest version.');
    }
    
    if (version_compare($version, "5.3.9", "<")) {
        setCol($prgbar, '#ffff00');
        print_error('Version warning', 'GaiaBB prefers recent PHP releases. Strongly consider upgrading the version of PHP you are using.', false);
        $warn = true;
    }
    
    setBar($prgbar, 0.12);
    
    $error = check_folders();
    if ($error !== true) {
        setCol($prgbar, '#ff0000');
        print_error('Configuration error', "Cannot find $error. Please upload GaiaBB correctly and start again.");
    }
    
    setBar($prgbar, 0.15);
    
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
    
    require_once (ROOT . 'config.php');
    if ($database == 'DBTYPE' || ! file_exists(ROOT . "db/$database.php")) {
        setCol($prgbar, '#ff0000');
        print_error('Database connection', 'Please ensure that config.php has been successfully written prior to running this install.');
    }
    require_once (ROOT . "db/$database.php");
    
    setBar($prgbar, 0.3);
    
    if (! check_db_api($database)) {
        setCol($prgbar, '#ff0000');
        print_error('Database connection', 'GaiaBB does not support the configured type of database.');
    }
    
    setBar($prgbar, 0.35);
    
    define('X_PREFIX', $dbname . '.' . $tablepre);
    
    // TODO: Remove me when old DAL goes away
    if (! defined('X_DBCLASSNAME')) {
        define('X_DBCLASSNAME', 'dbstuff');
    }
    $dalname = X_DBCLASSNAME;
    $db = new $dalname();
    $db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true);
    
    setBar($prgbar, 0.4);
    
    $version = $db->getVersion();
    if (version_compare($version, "5.1.0", "<")) {
        setCol($prgbar, '#ffff00');
        print_error('Database warning', 'GaiaBB requires MariaDB 5.1 or later and prefers the latest version.', false);
        $warn = true;
    }
    
    setBar($prgbar, 0.45);
    
    if (is_priv_db_user($dbuser)) {
        setCol($prgbar, '#ffff00');
        print_error('Security notice', 'Connecting to the database as a highly privileged user is strongly discouraged.', false);
        $warn = true;
    }
    
    setBar($prgbar, 0.5);
    
    if ($_SESSION['createsa'] == 'on' && file_exists('./emergency.php')) {
        $warn = createsa($db, $tablepre);
        if ($warn) {
            setCol($prgbar, '#ffff00');
            print_error('Credentials warning', 'There is already a super admin with those provided credentials. Not reset.', false);
        }
    }
    
    setBar($prgbar, 0.55);
    
    $admin = is_admin($db, $tablepre);
    if (! $admin) {
        setCol($prgbar, '#ff0000');
        print_error('Security notice', 'The user specified is not a super administrator. The repair utility cannot continue.');
    }
    
    setBar($prgbar, 0.6);
    
    // Reset the settings row to sane values and turn off board
    if ($_SESSION['resetset'] == 'on') {
        reset_settings($db, $tablepre, get_boardurl());
    }
    
    setBar($prgbar, 0.65);
    disable_gbb($db, $tablepre); // we turn the board off for safety reasons
    
    setBar($prgbar, 0.7);
    
    if ($_SESSION['disablesa'] == 'on' && file_exists('./emergency.php')) {
        disablesa($db, $tablepre);
    }
    
    setBar($prgbar, 0.8);
    
    if ($_SESSION['resttemp'] == 'on') {
        reset_templates($db, $tablepre);
    }
    
    setBar($prgbar, 0.9);
    
    if ($_SESSION['summary'] == 'on') {
        view_admins($db, $tablepre);
    }
    
    setBar($prgbar, 1.0);
    
    return false;
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
function disablesa($db, $tablepre)
{
    $admin = $db->escape($_SESSION['admin']);
    $db->query("UPDATE `" . $tablepre . "members` SET status='Banned' WHERE username!='" . $admin . "' AND status IN ('Super Administrator', 'Administrator', 'Super Moderator', 'Moderator')");
}
?>