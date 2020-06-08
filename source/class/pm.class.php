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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

/**
 * Class Pm
 *
 * Private messages
 *
 * @package GaiaBB
 *
 */

namespace GaiaBB;

class Pm
{

    public $uid;

    public function __construct()
    {
        $this->uid = 0;
    }

    public function init()
    {
    }

    public function findById()
    {
    }

    public function save()
    {
    }

    public function exists()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }

    public function deleteByUid($uid = 0)
    {
        global $db;

        if ($uid === 0) {
            return false;
        }

        $owner = $db->escape(member::findUsernameByUid($uid));

        $db->query("DELETE FROM " . X_PREFIX . "pm_attachments WHERE owner = '$owner'");
        $db->query("DELETE FROM " . X_PREFIX . "pm WHERE owner = '$owner'");
    }
}
