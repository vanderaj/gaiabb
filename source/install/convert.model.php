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

class dbHost
{

    private $hostname;

    private $dbtype;

    private $username;

    private $password;

    private $tablepre;
}

class convert
{

    private $fromDbHost;

    private $toDbHost;

    private $prgBar;

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function convert(&$prgbar, $fromDb, $toDb)
    {
        $this->prgbar = $prgbar;
        $this->toDbHost = $toDb;
        $this->fromDbHost = $fromDb;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function close()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function init()
    {
        $this->isAuth();
        $this->disableBoards();
        $this->settings();
        $this->members();
        $this->forums();
        $this->threads();
        $this->posts();
        $this->polls();
        $this->ranks();
        $this->attachments();
        $this->addresses();
        $this->favorites();
        $this->subscriptions();
        $this->censors();
        $this->banned();
        $this->messages();
        $this->finish();
        return true;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function isAuth()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function disableBoards()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function settings()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function members()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function forums()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function threads()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function posts()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function polls()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function ranks()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function attachments()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function addresses()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function favorites()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function subscriptions()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function censors()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function banned()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function messages()
    {
    }
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function process_convert_index($path)
{
    if (formVar('gbbinstall') == '') {
        view_header('Must Install First', $path);
        print_error('Cannot continue', 'You must have GaiaBB installed already to continue with the convertion');
    }
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function process_convert_details($path)
{
    $_SESSION['dbhost'] = formVar('db_host');
    $_SESSION['dbname'] = formVar('db_name');
    $_SESSION['dbuser'] = formVar('db_user');
    $_SESSION['dbpw'] = formVar('db_pw');
    $_SESSION['tablepre'] = formVar('table_pre');
    $_SESSION['admin_user'] = formVar('admin_user');
    $_SESSION['admin_pass'] = formVar('admin_pass');
    $_SESSION['admin'] = formVar('gbb_admin_user');
    $_SESSION['adminpw'] = md5(formVar('gbb_admin_pass'));
}

function convert_forum($path, $prgbar)
{
    set_time_limit(0); // Need more time for big boards
    ini_set('memory_limit', '64M'); // Need lots of memory
    $warn = false;
    setBar($prgbar, 0.01);

    $database = $pconnect = $dbname = $dbhost = '';
    $tablepre = $dbuser = $dbpw = '';

    require_once 'config.php';
    if ($database == 'DBTYPE' || !file_exists(ROOT . "db/$database.php")) {
        setCol($prgbar, '#ff0000');
        print_error('Database connection', 'Please ensure that you have successfully installed GaiaBB prior to running this convertion.');
    }
    require_once "../db/mysql5php.php";

    setBar($prgbar, 0.05);

    define('X_PREFIX', $tablepre);

    $db = new mysql5Php5();
    $db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, true);

    setBar($prgbar, 0.07);

    if (!isInstalled($db)) {
        setCol($prgbar, '#ff0000');
        print_error('Installation error', 'Please ensure that you have successfully installed GaiaBB prior to running this convertion.', true);
        exit();
    }

    if (is_priv_db_user($_SESSION['dbuser'])) {
        setCol($prgbar, '#ffff00');
        print_error('Security notice', 'Connecting to the database as a highly privileged user is strongly discouraged.', false);
        $warn = true;
    }

    setBar($prgbar, 0.1);

    define('X_PREFIX2', $_SESSION['tablepre']);

    $db2 = new mysql5Php5();
    $db2->connect($_SESSION['dbhost'], $_SESSION['dbuser'], $_SESSION['dbpw'], $_SESSION['dbname'], 0, true, true, X_PREFIX2);

    setBar($prgbar, 0.11);

    if (!is_admin($db, $tablepre)) {
        setCol($prgbar, '#ff0000');
        print_error('Convertion error', 'You are not a Super Administrator at your GaiaBB forum. The convertion can not continue.', true);
        exit();
    }
    setBar($prgbar, 0.12);

    switch ($path) {
        case 'convertxmb':
        default:
            include 'convert.xmb19x.php';
            break;
    }
    setBar($prgbar, 0.15);

    $convertclass = X_CONVERT;
    $convert = new $convertclass($prgbar, $db2, $db);
    $convert->init();
    return $warn;
}
