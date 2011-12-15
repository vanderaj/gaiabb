<?php
/**
 * GaiaBB
 * Copyright (c) 2011 The GaiaBB Group
 * http://www.GaiaBB.com
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group 
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
 * http://www.xmbforum.com
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

// Safe to use without global.inc.php
define('DEBUG_REG', true);
define('ROOT', './');

require_once(ROOT.'header.php');
require_once(ROOTINC.'address.inc.php');

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

eval('$css = "'.template('css').'";');

nav($lang['textaddresslist']);
btitle($lang['textaddresslist']);

if (X_GUEST || isset($self['status']) && $self['status'] == 'Banned')
{
    error($lang['pmnotloggedin']);
}

$addresses = formArray('addresses', true , false, 'string');
$delete = formArray('delete', true, false, 'string');

if (empty($addresses) && isset($_GET['addresses']))
{
    $addresses = $db->escape($_GET['addresses']);
    $addresses = array($addresses);
}

if (empty($delete) && isset($_GET['delete']))
{
    $addresses = $db->escape($_GET['delete']);
    $addresses = array($delete);
}

switch($action)
{
    case 'add':
        address_add($addresses);
        break;
    case 'edit':
        address_edit();
        break;
    case 'delete':
        if (count($delete) > 0)
        {
            address_delete($delete);
        }
        else
        {
            blistmsg($lang['nomember']);
        }
        break;
    case 'add2pm':
        address_addpm();
        break;
    default:
        address_display();
        break;
}
?>