<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2022 The GaiaBB Group
 * https://github.com/vanderaj/gaiabb
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

if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

function blistmsg($message, $redirect = '', $exit = false)
{
    global $css, $THEME, $CONFIG, $lang, $bgcode, $versionpowered;
    global $charset, $redirectjs, $shadow, $lang_code, $lang_dir;

    if (isset($redirect) && !empty($redirect)) {
        redirect($redirect, 2.5, X_REDIRECT_JS);
    }
    eval('echo stripslashes("' . template('addresslist_message') . '");');
    if ($exit) {
        exit;
    }
}

function address_add($addresses)
{
    global $db, $lang, $self, $shadow, $THEME, $versionpowered;

    if (!is_array($addresses)) {
        $addresses = array($addresses);
    }

    if (count($addresses) > 10) {
        $addresses = array_slice($addresses, 0, 10);
    }

    foreach ($addresses as $key => $address) {
        if ($address == $self['username']) {
            blistmsg($lang['addresswarnaddself']);
        } else if (empty($address) || (strlen(trim($address)) == 0)) {
            blistmsg($lang['noaddressselected'], '', true);
        } else {
            $address = addslashes($address);

            $q = $db->query("SELECT count(username) FROM " . X_PREFIX . "addresses WHERE username = '" . $self['username'] . "' AND addressname = '$address'");
            if ($db->result($q, 0) > 0) {
                blistmsg($address . ' ' . $lang['addressalreadyonlist']);
            } else {
                $q = $db->query("SELECT count(username) FROM " . X_PREFIX . "members WHERE username = '$address'");
                if ($db->result($q, 0) < 1) {
                    blistmsg($lang['nomember']);
                } else {
                    $db->query("INSERT INTO " . X_PREFIX . "addresses(addressname, username) VALUES('$address', '" . $self['username'] . "')");
                    blistmsg($address . ' ' . $lang['addressaddedmsg'], 'address.php');
                }
            }
            $db->free_result($q);
        }
    }
}

function address_edit()
{
    global $db, $lang, $self, $shadow, $THEME, $CONFIG, $versionpowered;
    global $charset, $css, $bgcode, $oToken, $lang_code, $lang_dir;

    $addresses = array();
    $q = $db->query("SELECT addressname FROM " . X_PREFIX . "addresses WHERE username = '" . $self['username'] . "' ORDER BY addressname") or die($db->error());
    while ($address = $db->fetch_array($q)) {
        eval('$addresses[] = "' . template('addresslist_edit_address') . '";');
    }
    if (count($addresses) > 0) {
        $addresses = implode("\n", $addresses);
    } else {
        unset($addresses);
        $addresses = '';
    }
    $db->free_result($q);
    eval('echo stripslashes("' . template('addresslist_edit') . '");');
}

function address_delete($delete)
{
    global $db, $lang, $self, $shadow, $THEME, $CONFIG;
    global $charset, $css, $bgcode, $versionpowered;

    if (!is_array($delete)) {
        $delete = array($delete);
    }

    foreach ($delete as $key => $address) {
        $address = addslashes($address);
        $db->query("DELETE FROM " . X_PREFIX . "addresses WHERE addressname = '$address' AND username = '" . $self['username'] . "'");
    }
    blistmsg($lang['addresslistupdated'], 'address.php');
}

function address_addpm()
{
    global $db, $lang, $self, $shadow, $versionpowered;
    global $charset, $THEME, $CONFIG, $css, $bgcode, $oToken, $lang_code, $lang_dir;

    $users = array();
    $addresses = array();
    $addresses['offline'] = $addresses['online'] = '';

    $q = $db->query("SELECT a.addressname, w.invisible, w.username FROM " . X_PREFIX . "addresses a LEFT JOIN " . X_PREFIX . "whosonline w ON(a.addressname = w.username) WHERE a.username = '" . $self['username'] . "' ORDER BY a.addressname");
    while ($address = $db->fetch_array($q)) {
        if ($address['invisible'] == 1) {
            if (!X_ADMIN) {
                eval("\$addresses['offline'] .= \"" . template('address_pm_off') . "\";");
            } else {
                eval("\$addresses['online'] .= \"" . template('address_pm_inv') . "\";");
            }
        } else if ($address['username'] != '') {
            eval("\$addresses['online'] .= \"" . template('address_pm_on') . "\";");
        } else {
            eval("\$addresses['offline']   .= \"" . template('address_pm_off') . "\";");
        }
    }

    if (count($addresses) == 0) {
        blistmsg($lang['no_addresses']);
    } else {
        eval('echo stripslashes("' . template('address_pm') . '");');
    }
    $db->free_result($q);
}

function address_display()
{
    global $db, $lang, $self, $shadow, $versionpowered;
    global $charset, $THEME, $CONFIG, $css, $bgcode, $lang_code, $lang_dir;

    $q = $db->query("SELECT a.addressname, w.invisible, w.username FROM " . X_PREFIX . "addresses a LEFT JOIN " . X_PREFIX . "whosonline w ON(a.addressname = w.username) WHERE a.username = '" . $self['username'] . "' ORDER BY a.addressname");
    $addresses = array();
    $addresses['offline'] = $addresses['online'] = '';
    while ($address = $db->fetch_array($q)) {
        if (!empty($address['username'])) {
            if ($address['invisible'] == 1) {
                if (!X_ADMIN) {
                    eval("\$addresses['offline'] .= \"" . template('addresslist_address_offline') . "\";");
                    continue;
                } else {
                    $addressstatus = $lang['hidden'];
                }
            } else {
                $addressstatus = $lang['textonline'];
            }
            eval("\$addresses['online'] .= \"" . template('addresslist_address_online') . "\";");
        } else {
            eval("\$addresses['offline'] .= \"" . template('addresslist_address_offline') . "\";");
        }
    }
    $db->free_result($q);
    eval('echo stripslashes("' . template('addresslist') . '");');
}
