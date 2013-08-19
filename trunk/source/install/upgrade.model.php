<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2013 The GaiaBB Group
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

if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false))
{
    exit('This file is not designed to be called directly');
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function process_upgrade_config()
{
    $_SESSION['resetset'] = formOnOff('resetset');
}

class Upgrade
{
    private $db;
    private $prgbar;
    private $schemaver;

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function Upgrade($indb, $in_prgbar)
    {
        $this->db = $indb;
        $this->prgbar = $in_prgbar;

        if ($this->column_exists('settings', 'schemaver'))
        {
            $query = $this->db->query("SELECT schemaver FROM `".X_PREFIX."settings`");
            $this->schemaver = $this->db->result($query, 0);
            $this->db->free_result($query);
        }
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function table_exists($table)
    {
        $query = $this->db->query("SHOW TABLES LIKE '". X_PREFIX .$table. "'");
        $rows = $this->db->num_rows($query);
        return ($rows > 0) ? true : false;
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function column_exists($table, $column)
    {
        $query = $this->db->query("SHOW COLUMNS FROM `". X_PREFIX .$table. "` LIKE '". $column. "'");
        $rows = $this->db->num_rows($query);
        return ($rows > 0) ? true : false;
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function rename_tables()
    {
        return true;
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function add_tables()
    {
        return true;
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function delete_tables()
    {
        return true;
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function alter_tables()
    {
        return true;
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function migrate_data()
    {
        return true;
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function migrate_settings()
    {
        return true;
    }
}

require_once "upgrade.ultimabb.php";

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function upgrade_forum($path, $prgbar)
{
    $warn = false;
    setBar($prgbar, 0.01);

    $version = phpversion();
    if (version_compare($version, "5.3.2") < 0)
    {
        setCol($prgbar, '#ff0000');
        print_error('Version error', 'GaiaBB requires PHP 4.3.2 or later and prefers the latest version.');
    }

    if (version_compare($version, "5.3.9") < 0)
    {
        setCol($prgbar, '#ffff00');
        print_error('Version warning', 'GaiaBB prefers recent PHP releases. Strongly consider upgrading the version of PHP you are using.', false);
        $warn = true;
    }

    setBar($prgbar, 0.02);

    $error = check_folders();
    if ($error !== true)
    {
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

    $database = ''; $pconnect = ''; $dbname = ''; $dbhost = '';
    $tablepre = ''; $dbuser = ''; $dbpw = '';

    require_once (ROOT.'config.php');
    if ($database == 'DBTYPE' || !file_exists(ROOT."db/$database.php"))
    {
        setCol($prgbar, '#ff0000');
        print_error('Database connection', 'Please ensure that config.php has been successfully written prior to running this install.');
    }
    require_once(ROOT."db/$database.php");

    setBar($prgbar, 0.05);

    if (!check_db_api($database))
    {
        setCol($prgbar, '#ff0000');
        print_error('Database connection', 'GaiaBB does not support the configured type of database.');
    }

    setBar($prgbar, 0.06);

    define('X_PREFIX', $tablepre);

    // TODO: Remove me when old DAL goes away
    if (!defined('X_DBCLASSNAME'))
    {
        define('X_DBCLASSNAME', 'dbstuff');
    }
    $dalname = X_DBCLASSNAME;
    $db = new $dalname;
    $db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true);

    setBar($prgbar, 0.07);

    $version = $db->getVersion();
    if (version_compare($version, "5.1.0", "<"))
    {
        setCol($prgbar, '#ffff00');
        print_error('Database warning', 'GaiaBB requires MariaDB 5.1 or later and prefers the latest version.', false);
        $warn = true;
    }

    setBar($prgbar, 0.08);

    if (is_priv_db_user($dbuser))
    {
        setCol($prgbar, '#ffff00');
        print_error('Security notice', 'Connecting to the database as a highly privileged user is strongly discouraged.', false);
        $warn = true;
    }

    setBar($prgbar, 0.09);

    $admin = is_admin($db, $tablepre);
    if (!$admin)
    {
        setCol($prgbar, '#ff0000');
        print_error('Security notice', 'The user specified is not a super administrator. The upgrade utility cannot continue.');
    }

    setBar($prgbar, 0.1);

    $upgrade = new upgrade_ultimaBB($db, $prgbar);

    // Reset the settings row to sane values and turn off board
    if ($_SESSION['resetset'] == 'on')
    {
        schema_create_settings($db, $tablepre);
        reset_settings($db, $tablepre);
    }
    else
    {
        $upgrade->migrate_settings();
    }
    disable_gbb($db, $tablepre);   // we turn the board off for safety reasons

    // Some operations take time. If your script fails, set this higher
    set_time_limit(300);

    $upgrade->add_tables(0.15);
    $upgrade->rename_tables(0.2);
    $upgrade->alter_tables(0.3);
    $upgrade->migrate_data(0.9);

    setBar($prgbar, 1.0);
    return $warn;
}
?>