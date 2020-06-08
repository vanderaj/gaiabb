<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
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

namespace GaiaBB;

class Address
{
    private $addresses;
    private $delete;

    /**
     * Address constructor.
     */
    public function __construct()
    {
        global $db;

        $this->addresses = formArray('addresses', true, false, 'string');
        $this->delete = formArray('delete', true, false, 'string');

        if (empty($this->addresses) && isset($_GET['addresses'])) {
            $address = $db->escape($_GET['addresses']);
            $this->addresses = array($address);
        }

        if (empty($this->delete) && isset($_GET['delete'])) {
            $del = $db->escape($_GET['delete']);
            $this->delete = array($del);
        }
    }

    /**
     * @param $message  message to display to user
     * @param string $redirect where to next
     * @param bool $exit true if need to stop
     */
    public function blistmsg($message, $redirect = '', $exit = false)
    {
        global $css, $THEME, $CONFIG, $lang, $bgcode, $versionpowered;
        global $charset, $redirectjs, $shadow, $lang_code, $lang_dir;

        if (isset($redirect) && !empty($redirect)) {
            redirect($redirect, 2.5, X_REDIRECT_JS);
        }
        eval('echo stripslashes("' . template('addresslist_message') . '");');
        if ($exit) {
            exit();
        }
    }

    /**
     * @param $addresses - add this address to the user's address book
     */
    public function add($addresses)
    {
        global $db, $lang, $self, $shadow, $THEME, $versionpowered;

        if (!is_array($addresses)) {
            $addresses = array(
                $addresses,
            );
        }

        if (count($addresses) > 10) {
            $addresses = array_slice($addresses, 0, 10);
        }

        foreach ($addresses as $key => $address) {
            if ($address == $self['username']) {
                $this->blistmsg($lang['addresswarnaddself']);
            } elseif (empty($address) || (strlen(trim($address)) == 0)) {
                $this->blistmsg($lang['noaddressselected'], '', true);
            } else {
                $address = addslashes($address);

                $q = $db->query("SELECT count(username) FROM " . X_PREFIX . "addresses WHERE username = '" . $self['username'] . "' AND addressname = '$address'");
                if ($db->result($q, 0) > 0) {
                    $this->blistmsg($address . ' ' . $lang['addressalreadyonlist']);
                } else {
                    $q = $db->query("SELECT count(username) FROM " . X_PREFIX . "members WHERE username = '$address'");
                    if ($db->result($q, 0) < 1) {
                        $this->blistmsg($lang['nomember']);
                    } else {
                        $db->query("INSERT INTO " . X_PREFIX . "addresses(addressname, username) VALUES('$address', '" . $self['username'] . "')");
                        $this->blistmsg($address . ' ' . $lang['addressaddedmsg'], 'address.php');
                    }
                }
                $db->freeResult($q);
            }
        }
    }

    public function edit()
    {
        global $db, $lang, $self, $shadow, $THEME, $CONFIG, $versionpowered;
        global $charset, $css, $bgcode, $oToken, $lang_code, $lang_dir;

        $addresses = array();
        $q = $db->query("SELECT addressname FROM " . X_PREFIX . "addresses WHERE username = '" . $self['username'] . "' ORDER BY addressname");
        while (($address = $db->fetchArray($q)) != false) {
            eval('$addresses[] = "' . template('addresslist_edit_address') . '";');
        }
        if (count($addresses) > 0) {
            $addresses = implode("\n", $addresses);
        } else {
            unset($addresses);
            $addresses = '';
        }
        $db->freeResult($q);
        eval('echo stripslashes("' . template('addresslist_edit') . '");');
    }

    public function delete($delete)
    {
        global $db, $lang, $self, $shadow, $THEME, $CONFIG;
        global $charset, $css, $bgcode, $versionpowered;

        if (!is_array($delete)) {
            $delete = array(
                $delete,
            );
        }

        foreach ($delete as $key => $address) {
            $address = addslashes($address);
            $db->query("DELETE FROM " . X_PREFIX . "addresses WHERE addressname = '$address' AND username = '" . $self['username'] . "'");
        }
        $this->blistmsg($lang['addresslistupdated'], 'address.php');
    }

    public function addpm()
    {
        global $db, $lang, $self, $shadow, $versionpowered;
        global $charset, $THEME, $CONFIG, $css, $bgcode, $oToken, $lang_code, $lang_dir;

        $users = array();
        $addresses = array();
        $addresses['offline'] = $addresses['online'] = '';

        $q = $db->query("SELECT a.addressname, w.invisible, w.username FROM " . X_PREFIX . "addresses a LEFT JOIN " . X_PREFIX . "whosonline w ON(a.addressname = w.username) WHERE a.username = '" . $self['username'] . "' ORDER BY a.addressname");
        while (($address = $db->fetchArray($q)) != false) {
            if ($address['invisible'] == 1) {
                if (!X_ADMIN) {
                    eval("\$addresses['offline'] .= \"" . template('address_pm_off') . "\";");
                } else {
                    eval("\$addresses['online'] .= \"" . template('address_pm_inv') . "\";");
                }
            } elseif ($address['username'] != '') {
                eval("\$addresses['online'] .= \"" . template('address_pm_on') . "\";");
            } else {
                eval("\$addresses['offline']   .= \"" . template('address_pm_off') . "\";");
            }
        }

        if (count($addresses) == 0) {
            $this->blistmsg($lang['no_addresses']);
        } else {
            eval('echo stripslashes("' . template('address_pm') . '");');
        }
        $db->freeResult($q);
    }

    public function display()
    {
        global $db, $lang, $self, $shadow, $versionpowered;
        global $charset, $THEME, $CONFIG, $css, $bgcode, $lang_code, $lang_dir;

        $q = $db->query("SELECT a.addressname, w.invisible, w.username FROM " . X_PREFIX . "addresses a LEFT JOIN " . X_PREFIX . "whosonline w ON(a.addressname = w.username) WHERE a.username = '" . $self['username'] . "' ORDER BY a.addressname");
        $addresses = array();
        $addresses['offline'] = $addresses['online'] = '';
        while (($address = $db->fetchArray($q)) != false) {
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
        $db->freeResult($q);
        eval('echo stripslashes("' . template('addresslist') . '");');
    }

    public function deleteByUid($uid = 0)
    {
        global $db;

        if ($uid === 0) {
            return false;
        }

        $owner = $db->escape(Member::findUsernameByUid($uid));
        $db->query("DELETE FROM " . X_PREFIX . "addresses WHERE username = '$owner'");
    }
}
