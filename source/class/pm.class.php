<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2017 The GaiaBB Group
 * http://www.GaiaBB.com
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
 * http://forums.xmbforum2.com/
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

// check to ensure no direct viewing of page
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

/**
 * Class thread - provides generic business logic for thread management
 *
 * thread routines
 * Static initialization allowed, does not require instantiation before first use.
 *
 * @package GaiaBB
 *
 */
class pm
{

    public $uid;

    function __construct()
    {
        $this->uid = 0;
    }

    function init()
    {
    }

    function findById()
    {
    }

    function save()
    {
    }

    function exists()
    {
    }

    function update()
    {
    }

    function delete()
    {
    }

    function deleteByUid($uid = 0)
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
