<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2021 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2021 The XMB Development Team
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
 *
 **/
// phpcs:disable PSR1.Files.SideEffects
require_once 'header.php';
require_once ROOT . 'class/address.class.php';

loadtpl(
    'addresslist_edit_address',
    'addresslist_edit',
    'addresslist_address_online',
    'addresslist_address_offline',
    'addresslist',
    'address_pm_inv',
    'address_pm_on',
    'address_pm_off',
    'address_pm',
    'addresslist_message'
);

$shadow = shadowfx();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

nav($lang['textaddresslist']);
btitle($lang['textaddresslist']);

if (X_GUEST || isset($self['status']) && $self['status'] == 'Banned') {
    error($lang['pmnotloggedin']);
}

$addr = new GaiaBB\Address();

$addresses = getRequestVar('addresses');

switch ($action) {
    case 'add':
        $addr->add($addresses);
        break;
    case 'edit':
        $addr->edit();
        break;
    case 'delete':
        if (count($delete) > 0) {
            $addr->delete($delete);
        } else {
            $addr->blistmsg($lang['nomember']);
        }
        break;
    case 'add2pm':
        $addr->addpm();
        break;
    default:
        $addr->display();
        break;
}
