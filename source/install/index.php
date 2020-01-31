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
define('IN_PROGRAM', true);

session_start();

require_once('constants.php');
require_once('common.view.php');
require_once('common.model.php');
require_once('schema.php');
require_once('install.model.php');
require_once('install.view.php');
require_once('upgrade.model.php');
require_once('upgrade.view.php');
require_once('repair.model.php');
require_once('repair.view.php');
require_once('convert.model.php');
require_once('convert.view.php');

$path = $step = '';
$action = getVar('action');
if ($action != '') {
    $path = $action;
    $step = 'preflight';
} else {
    $path = formVar('path');
    $step = formVar('step');
}

switch ($path) {
    case 'install':
        switch ($step) {
            case 'preflight':
                $_SESSION = array(); // clear the session variables out
                if (isInstalled()) {
                    view_header('Previous installation detected', $path);
                    print_error('Previous installation', 'Install cannot continue as there is an existing board. Please review the installation notes for more details.');
                    exit();
                }
                view_header('License Agreement', $path);
                view_eula($path);
                break;
            case 'eula': // no reason to backup a new installation
            case 'backup':
                view_header('Administrator Credentials', $path);
                view_admin($path);
                break;
            case 'admin':
                process_admin_creds($path);
                view_header('Database Details', $path);
                view_database($path);
                break;
            case 'db':
                process_db($path);
                view_header('Configuration File', $path);
                view_config($path);
                break;
            case 'conf':
                if (formVar('confMethod') !== 'skip') {
                    process_config($path);
                }
                view_header('Installing GaiaBB', $path);
                $prgbar = view_install_index();
                $warn = install_forum($prgbar);
                if (!$warn) {
                    setCol($prgbar, '#00ff00');
                    view_install_complete();
                } else {
                    view_install_warncomplete();
                }
                view_footer();
                exit();
                break;
            default:
                redirect('index.php', 0, X_REDIRECT_HEADER);
                break;
        }
        break;

    case 'upgrade':
        switch ($step) {
            case 'preflight':
            case 'eula':
                $_SESSION = array(); // clear the session variables out
                view_header('Have you backed up your files?', $path);
                view_backup($path);
                break;
            case 'backup':
                process_backup($path);
                view_header('Upgrade options', $path);
                view_upgrade_index($path);
                break;
            case 'upgrade':
                process_upgrade_config();
                view_header('Administrator Credentials', $path);
                view_admin($path);
                break;
            case 'admin':
                process_admin_creds($path);
                view_header('Upgrading GaiaBB', $path);
                $prgbar = view_upgrade_action();
                $warn = upgrade_forum($path, $prgbar);
                if (!$warn) {
                    setCol($prgbar, '#00ff00');
                    view_upgrade_complete();
                } else {
                    view_upgrade_warncomplete();
                }
                view_footer();
                exit();
                break;
            default:
                redirect('index.php', 0, X_REDIRECT_HEADER);
                break;
        }
        break;
    case 'repair':
        switch ($step) {
            case 'preflight':
            case 'eula':
                $_SESSION = array(); // clear the session variables out
                view_header('Have you backed up your files?', $path);
                view_backup($path);
                break;
            case 'backup':
                process_backup($path);
                view_header('Repair options', $path);
                view_repair_index($path);
                break;
            case 'repair':
                process_repair_config();
                view_header('Administrator Credentials', $path);
                view_admin($path);
                break;
            case 'db':
                process_db($path);
                view_header('Configuration File', $path);
                view_config($path);
                break;
            case 'admin':
                process_admin_creds($path);
                if ($_SESSION['config'] == 'on') {
                    view_header('Database Details', $path);
                    view_database($path);
                    view_footer();
                    exit();
                }
            // deliberate fall-through - DO NOT CHANGE
            case 'conf':
                view_header('Repairing GaiaBB', $path);
                $prgbar = view_repair_action();
                $warn = repair_forum($path, $prgbar);
                if (!$warn) {
                    setCol($prgbar, '#00ff00');
                    view_repair_complete();
                } else {
                    view_repair_warncomplete();
                }
                view_footer();
                exit();
                break;
            default:
                redirect('index.php', 0, X_REDIRECT_HEADER);
                break;
        }
        break;
    case 'convertxmb':
        $boardtype = 'XMB';
        switch ($step) {
            case 'preflight':
            case 'eula':
                $_SESSION = array(); // clear the session variables out
                view_header('Have you backed up your files?', $path);
                view_backup($path, $boardtype);
                break;
            case 'backup':
                process_backup($path);
                view_header('Have you installed GaiaBB?', $path);
                view_convert_index($path);
                break;
            case 'preconvert':
                process_convert_index($path);
                view_header('Forum Database Details', $path);
                view_convert_details($path, $boardtype);
                break;
            case 'convert':
                process_convert_details($path);
                view_header('Converting XMB', $path);
                $prgbar = view_convert_action($boardtype);
                $warn = convert_forum($path, $prgbar);
                if (!$warn) {
                    setCol($prgbar, '#00ff00');
                    view_convert_complete($boardtype);
                } else {
                    view_convert_warncomplete($boardtype);
                }
                view_footer();
                exit();
                break;
            default:
                redirect('index.php', 0, X_REDIRECT_HEADER);
                break;
        }
        break;
    case 'convertphpbb':
        view_header('phpBB Converter', $path);
        print_error('Not implemented', 'Sorry, this feature is not yet implemented.');
        exit();
        break;
    default:
        view_header('Welcome to GaiaBB', $path);
        view_default_index();
        break;
}
view_footer();
?>