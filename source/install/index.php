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

// phpcs:disable PSR1.Files.SideEffects
define('IN_PROGRAM', true);

session_start();

require_once 'constants.php';
require_once 'common.view.php';
require_once 'common.model.php';
require_once 'schema.php';
require_once 'install.model.php';
require_once 'install.view.php';
require_once 'upgrade.model.php';
require_once 'upgrade.view.php';
require_once 'repair.model.php';
require_once 'repair.view.php';
require_once 'convert.model.php';
require_once 'convert.view.php';

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
                    viewHeader('Previous installation detected', $path);
                    print_error('Previous installation', 'Install cannot continue as there is an existing board. Please review the installation notes for more details.');
                    exit();
                }
                viewHeader('License Agreement', $path);
                view_eula($path);
                break;
            case 'eula': // no reason to backup a new installation
            case 'backup':
                viewHeader('Administrator Credentials', $path);
                view_admin($path);
                break;
            case 'admin':
                process_admin_creds($path);
                viewHeader('Database Details', $path);
                view_database($path);
                break;
            case 'db':
                process_db($path);
                viewHeader('Configuration File', $path);
                view_config($path);
                break;
            case 'conf':
                if (formVar('confMethod') !== 'skip') {
                    process_config($path);
                }
                viewHeader('Installing GaiaBB', $path);
                $prgbar = view_install_index();
                $warn = install_forum($prgbar);
                if (!$warn) {
                    setCol($prgbar, '#00ff00');
                    view_install_complete();
                } else {
                    view_install_warncomplete();
                }
                viewFooter();
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
                viewHeader('Have you backed up your files?', $path);
                view_backup($path);
                break;
            case 'backup':
                process_backup($path);
                viewHeader('Upgrade options', $path);
                view_upgrade_index($path);
                break;
            case 'upgrade':
                process_upgrade_config();
                viewHeader('Administrator Credentials', $path);
                view_admin($path);
                break;
            case 'admin':
                process_admin_creds($path);
                viewHeader('Upgrading GaiaBB', $path);
                $prgbar = view_upgrade_action();
                $warn = upgrade_forum($path, $prgbar);
                if (!$warn) {
                    setCol($prgbar, '#00ff00');
                    view_upgrade_complete();
                } else {
                    view_upgrade_warncomplete();
                }
                viewFooter();
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
                viewHeader('Have you backed up your files?', $path);
                view_backup($path);
                break;
            case 'backup':
                process_backup($path);
                viewHeader('Repair options', $path);
                view_repair_index($path);
                break;
            case 'repair':
                process_repair_config();
                viewHeader('Administrator Credentials', $path);
                view_admin($path);
                break;
            case 'db':
                process_db($path);
                viewHeader('Configuration File', $path);
                view_config($path);
                break;
            case 'admin':
                process_admin_creds($path);
                if ($_SESSION['config'] == 'on') {
                    viewHeader('Database Details', $path);
                    view_database($path);
                    viewFooter();
                    exit();
                }
            // deliberate fall-through - DO NOT CHANGE
            case 'conf':
                viewHeader('Repairing GaiaBB', $path);
                $prgbar = view_repair_action();
                $warn = repair_forum($path, $prgbar);
                if (!$warn) {
                    setCol($prgbar, '#00ff00');
                    view_repair_complete();
                } else {
                    view_repair_warncomplete();
                }
                viewFooter();
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
                viewHeader('Have you backed up your files?', $path);
                view_backup($path, $boardtype);
                break;
            case 'backup':
                process_backup($path);
                viewHeader('Have you installed GaiaBB?', $path);
                view_convert_index($path);
                break;
            case 'preconvert':
                process_convert_index($path);
                viewHeader('Forum Database Details', $path);
                view_convert_details($path, $boardtype);
                break;
            case 'convert':
                process_convert_details($path);
                viewHeader('Converting XMB', $path);
                $prgbar = view_convert_action($boardtype);
                $warn = convert_forum($path, $prgbar);
                if (!$warn) {
                    setCol($prgbar, '#00ff00');
                    view_convert_complete($boardtype);
                } else {
                    view_convert_warncomplete($boardtype);
                }
                viewFooter();
                exit();
                break;
            default:
                redirect('index.php', 0, X_REDIRECT_HEADER);
                break;
        }
        break;
    case 'convertphpbb':
        viewHeader('phpBB Converter', $path);
        print_error('Not implemented', 'Sorry, this feature is not yet implemented.');
        exit();
        break;
    default:
        viewHeader('Welcome to GaiaBB', $path);
        view_default_index();
        break;
}
viewFooter();
