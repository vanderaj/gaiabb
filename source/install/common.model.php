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

/**
 * get_boardurl() - Try to fill in $boardurl for the user
 *
 * This will work as long as the DNS is good.
 *
 * @return type, the board's URL from headers
 *
 */
function get_boardurl()
{
    $boardurl = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    $self = $_SERVER['PHP_SELF']; // which we need to strip off /install from the back
    $i = strpos($self, "install/index.php");
    if ($i === false) {
        $boardurl = "http://www.example.com/forums/";
    } else {
        $self = substr($self, 0, $i);
        switch ($_SERVER['SERVER_PORT']) {
            case 80:
                $boardurl = "http://" . $boardurl . $self;
                break;
            case 443:
                $boardurl = "https://" . $boardurl . $self;
                break;
            default:

                // for the purposes of $boardurl, it is not really that important to get the
                // protocol right
                $boardurl = "http://" . $boardurl . ':' . $_SERVER['SERVER_PORT'] . $self;
                break;
        }
    }
    return $boardurl;
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
function get_config()
{
    // From db view
    $dbname = $_SESSION['dbname'];
    $dbuser = $_SESSION['dbuser'];
    $dbpw = $_SESSION['dbpw'];
    $dbhost = $_SESSION['dbhost'];
    $dbtype = $_SESSION['dbtype'];
    $tablepre = $_SESSION['tablepre'];

    $config = "<?php\n" . "if (!defined('IN_PROGRAM')){ exit(\"This file is not designed to be called directly\"); }\n" . "\$dbname = '" . $dbname . "';\n" . "\$dbuser = '" . $dbuser . "';\n" . "\$dbpw = '" . $dbpw . "';\n" . "\$dbhost = '" . $dbhost . "';\n" . "\$database = '" . $dbtype . "';\n" . "\$pconnect = 0;\n" . "\$tablepre = '" . $tablepre . "';\n?>";
    return $config;
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
function create_config($config)
{
    $retval = false;
    $handle = @fopen('config.php', "w");
    if (!$handle) {
        return false;
    }

    $retval = @fwrite($handle, $config);
    if ($retval !== false) {
        $retval = true;
    }
    fflush($handle);
    fclose($handle);

    // set the file read-only and not world readable (dangerous on shared hosts)
    @chmod('config.php', 0440);
    return $retval;
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
// Create the select list for the database
function get_db_array()
{
    $dbs[] = array(
        'name' => "MariaDB 5.1 or later (also compatible with Oracle MySQL)",
        'classname' => "mysql5Php5",
        'file' => 'mysql5php5.class'
    );
    return $dbs;
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
function get_databases()
{
    $dbs = get_db_array();
    array_multisort($dbs, SORT_ASC, SORT_STRING);
    $types = array();
    foreach ($dbs as $db) {
        if (!empty($db)) {
            $types[] = "<option name=\"" . $db['file'] . "\">" . $db['name'] . "</option>";
        }
    }

    return '<select name="db_type">' . implode("\n", $types) . '</select>';
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
function check_folders()
{
    $dirs = array(
        'admin',
        'admin/templates',
        'class',
        'db',
        'helper',
        'include',
        'include/captcha',
        'js',
        'lang',
        'images',
        'images/avatars',
        'images/mimetypes',
        'images/photos',
        'images/problue',
        'images/prored',
        'images/ranks',
        'images/ricons',
        'images/smilies'
    );

    $retval = true;
    foreach ($dirs as $dir) {
        if (!file_exists(ROOT . $dir)) {
            $retval = $dir;
            break;
        }
    }

    return $retval;
}

/*
 * Check if the (optionally) writable folders are writable
 *
 * @return	an array containing the folders that aren't writable
 */
function find_nonwritable_folders()
{
    $dirs = array(
        'admin/templates/',
        'include/captcha/',
        'images/avatars/',
        'images/photos/'
    );

    $retval = array();
    foreach ($dirs as $dir) {
        if (!is_writable(ROOT . $dir)) {
            $retval[] = ROOT . $dir;
        }
    }

    return $retval;
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
function check_files()
{
    $files = array(
        'activity.php',
        'address.php',
        'config.php',
        'contact.php',
        'editprofile.php',
        'email.php',
        'faq.php',
        'header.php',
        'index.php',
        'login.php',
        'logout.php',
        'lostpw.php',
        'markread.php',
        'memberlist.php',
        'pm.php',
        'post.php',
        'register.php',
        'search.php',
        'smilies.php',
        'stats.php',
        'topicadmin.php',
        'usercp.php',
        'viewforum.php',
        'viewonline.php',
        'viewprofile.php',
        'viewtopic.php',
        'admin/cp_analyzetables.php',
        'admin/cp_attachments.php',
        'admin/cp_avatars.php',
        'admin/cp_board.php',
        'admin/cp_captcha.php',
        'admin/cp_censors.php',
        'admin/cp_checktables.php',
        'admin/cp_closethreads.php',
        'admin/cp_dateformats.php',
        'admin/cp_dbinfo.php',
        'admin/cp_default.php',
        'admin/cp_deleteoldpms.php',
        'admin/cp_emptyadminlogs.php',
        'admin/cp_emptymodlogs.php',
        'admin/cp_faq.php',
        'admin/cp_fixattachments.php',
        'admin/cp_fixfavorites.php',
        'admin/cp_fixftotals.php',
        'admin/cp_fixlastposts.php',
        'admin/cp_fixmposts.php',
        'admin/cp_fixmthreads.php',
        'admin/cp_fixorphanedposts.php',
        'admin/cp_fixsmilies.php',
        'admin/cp_fixsubscriptions.php',
        'admin/cp_fixthreads.php',
        'admin/cp_fixttotals.php',
        'admin/cp_forcelogout.php',
        'admin/cp_forums.php',
        'admin/cp_general.php',
        'admin/cp_inactivemembers.php',
        'admin/cp_ipban.php',
        'admin/cp_logs.php',
        'admin/cp_members.php',
        'admin/cp_moderators.php',
        'admin/cp_news.php',
        'admin/cp_newsletter.php',
        'admin/cp_notepad.php',
        'admin/cp_onlinedump.php',
        'admin/cp_optimizetables.php',
        'admin/cp_photos.php',
        'admin/cp_pluglinks.php',
        'admin/cp_pmdump.php',
        'admin/cp_posticons.php',
        'admin/cp_prune.php',
        'admin/cp_ranks.php',
        'admin/cp_rawsql.php',
        'admin/cp_reguser.php',
        'admin/cp_rename.php',
        'admin/cp_repairtables.php',
        'admin/cp_restrictions.php',
        'admin/cp_robots.php',
        'admin/cp_rules.php',
        'admin/cp_search.php',
        'admin/cp_smilies.php',
        'admin/cp_smtp.php',
        'admin/cp_templates.php',
        'admin/cp_themes.php',
        'admin/cp_updatemoods.php',
        'admin/index.php',
        'admin/templates.gbb',
        'class/address.class.php',
        'class/attachments.class.php',
        'class/authc.class.php',
        'class/cache.class.php',
        'class/captcha.class.php',
        'class/favorite.class.php',
        'class/forum.class.php',
        'class/index.html',
        'class/mail.class.php',
        'class/member.class.php',
        'class/pm.class.php',
        'class/post.class.php',
        'class/subscription.class.php',
        'class/thread.class.php',
        'db/mysql5php5.class.php',
        'js/address.js',
        'js/addresslistedit.js',
        'js/admin.js',
        'js/admin_menu.js',
        'js/bbcodefns-ie.js',
        'js/bbcodefns-mozilla.js',
        'js/bbcodefns-opera.js',
        'js/bbcodefns.js',
        'js/header.js',
        'js/index.html',
        'js/popup.js',
        'js/progressbar.js',
        'include/admincp.inc.php',
        'include/captcha/mpl1.gdf',
        'include/captcha/mpl2.gdf',
        'include/captcha/mpl3.gdf',
        'include/constants.inc.php',
        'include/functions.inc.php',
        'include/index.html',
        'include/mass_mod.inc.php',
        'include/mimetypes.inc.php',
        'include/modcp.inc.php',
        'include/online.inc.php',
        'include/pm.inc.php',
        'include/theme.inc.php',
        'include/topicadmin.inc.php',
        'include/usercp.inc.php',
        'include/validate.inc.php',
        'lang/English.lang.php'
    );

    $retval = true;
    foreach ($files as $file) {
        if (!file_exists(ROOT . $file)) {
            $retval = $file;
            break;
        }
    }
    return $retval;
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
// Check to see if there is an existing board already configured
function isInstalled($db = false)
{
    if ($db === false) {
        $database = 'null';
        $tablepre = '';
        $dbhost = '';
        $dbuser = '';
        $dbname = 'DBNAME';
        $dbpw = '';
        $pconnect = false;
        include_once('../config.php');

        if ($dbname !== "DBNAME" && file_exists("../db/mysql5php5.php")) {
            // Okay, it's safe to check the database as per config.php
            define('X_PREFIX', $tablepre);
            include_once("../db/mysql5php5.php");

            $db = new mysql5Php5();
            $db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, false);

            if (@in_array(X_PREFIX . 'settings', $db->getTables())) {
                $db->close();
                return true;
            }
        } else {
            return false;
        }
    }

    if (@in_array(X_PREFIX . 'settings', $db->getTables())) {
        return true;
    }

    return false;
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
function is_admin($db, $tablepre)
{
    $admin = $db->escape($_SESSION['admin']);
    $adminpw = $_SESSION['adminpw'];

    $query = $db->query("SELECT username, password, status FROM " . X_PREFIX . "members WHERE username = '$admin'");
    if ($query === false) {
        return false;
    }
    $user = $db->fetch_array($query);
    $rows = $db->num_rows($query);
    $db->free_result($query);
    if ($rows === 1 && $admin === $user['username'] && $adminpw === $user['password'] && $user['status'] === 'Super Administrator') {
        return true;
    }
    return false;
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
function is_admin_pw_same()
{
    $path = formVar('path');
    if ($path == 'repair' || $path == 'upgrade') {
        return true;
    }

    $adminpw = formVar('frmPassword');
    $adminpwcfg = formVar('frmPasswordCfm');

    if ($adminpw == '' || $adminpwcfg == '') {
        return false;
    }

    return ($adminpw === $adminpwcfg);
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
function process_admin_creds($path)
{
    if (!is_admin_pw_same()) {
        view_header('Error: Administrator Credentials', $path);
        print_error('Error', 'Administration passwords do not match or are blank. Please go back and try again.');
    }

    $_SESSION['admin'] = formVar('frmUsername');
    $_SESSION['adminpw'] = md5(formVar('frmPassword'));
    $_SESSION['adminemail'] = formVar('frmEmail');
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
function process_db($path)
{
    $dbtype = formVar('db_type');
    $dbs = get_db_array();

    $found = false;
    foreach ($dbs as $db) {
        if (strpos($db['name'], $dbtype) !== false) {
            $file = $db['file'];
            $found = true;
            break;
        }
    }

    if (!$found) {
        view_header('Error: Database Configuration', $path);
        print_error('Error', 'The supplied database type is not available on this host.');
    }

    $_SESSION['dbtype'] = $file;
    $_SESSION['dbhost'] = formVar('db_host');
    $_SESSION['dbname'] = formVar('db_name');
    $_SESSION['dbuser'] = formVar('db_user');
    $_SESSION['dbpw'] = formVar('db_pw');
    $_SESSION['tablepre'] = formVar('table_pre');
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
function process_config($path)
{
    $confMethod = formVar('confMethod');
    if ($confMethod == '') {
        view_header('Configuration', $path);
        print_error('Configuration Error', 'Invalid configuration option supplied:' . $confMethod);
    }

    if ($confMethod == 'skip') {
        return;
    }

    if ($path == 'repair' && !file_exists('./emergency.php')) {
        view_header('Configuration', $path);
        print_error('Configuration Warning', 'Cannot process repair configuration as emergency.php does not exist.');
        return;
    }

    if ($path == 'install' && isInstalled()) {
        view_header('Configuration', $path);
        print_error('Configuration Error', 'Forum is already installed, cannot process configuration.');
        exit();
    }

    $_SESSION['fullurl'] = formVar('fullurl');

    $config = get_config();

    switch ($confMethod) {
        case 'create':
            if (!create_config($config)) {
                view_header('Configuration', $path);
                print_error('File system permissions', 'Could not write out config.php. Please check permissions or write manually', false);
                view_footer();
                exit();
            }
            break;
        case 'download':
            view_config_download($config);
            exit();
            break;
        case 'view':
            view_header('Configuration', $path);
            view_config_screen($path, $config);
            view_footer();
            exit();
            break;
        default:
            view_header('Configuration', $path);
            print_error('Configuration Error', 'Invalid configuration option supplied: ' . $confMethod);
            break;
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
function process_backup($path)
{
    if (formVar('fbackup') == '' || formVar('dbackup') == '') {
        view_header('Unsafe', $path);
        print_error('Cannot continue', 'GaiaBB cannot support you if you do not have a backup. Please make a backup and try again');
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
function createsa($db, $tablepre)
{
    if (is_admin($db, $tablepre)) {
        return true;
    }
    $admin = $db->escape($_SESSION['admin']);
    $adminpw = $db->escape($_SESSION['adminpw']);
    $adminEmail = $db->escape($_SESSION['adminemail']);
    $adminRegdate = $db->time(time());
    $db->query("INSERT INTO " . $tablepre . "members (username, password, regdate, email, status, langfile) VALUES ('" . $admin . "','" . $adminpw . "',$adminRegdate,'" . $adminEmail . "','Super Administrator', 'English')");
    $db->query("UPDATE " . $tablepre . "settings SET config_value = '$adminEmail' WHERE config_name = 'adminemail'");

    return false;
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
function disable_gbb($db, $tablepre)
{
    $db->query("UPDATE " . $tablepre . "settings SET config_value = 'off' WHERE config_name = 'bbstatus'");
    $db->query("UPDATE " . $tablepre . "settings SET config_value = 'The forum is disabled for maintenance. Please come back soon.' WHERE config_name = 'bboffreason'");
}
