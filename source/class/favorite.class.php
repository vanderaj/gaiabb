<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2020 The XMB Development Team
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

class Favorite
{

    public $tid;

    public $dirty;

    public function __construct($tid = 0)
    {
        if ($tid === 0) {
            $this->dirty = false;
            $this->tid = 0;
            return;
        }
        $this->findById($tid);
    }

    public function findById($tid)
    {
        if ($this->exists($tid)) {
            $this->tid = $tid;
            $this->dirty = true;
            return true;
        }
        return false;
    }

    public function exists($tid)
    {
        global $db, $self;

        $retval = false;

        if ($tid == 0) {
            return false;
        }

        $query = $db->query("SELECT tid FROM " . X_PREFIX . "favorites WHERE tid = '" . intval($tid) . "' AND username = '" . $db->escape($self['username']) . "' AND type = 'favorite'");
        if ($query && $db->numRows($query) == 1) {
            $this->tid = $tid;
            $retval = true;
        }
        $db->freeResult($query);

        return $retval;
    }

    public function update()
    {
        global $db, $self;

        if ($this->dirty) {
            $db->query("INSERT INTO " . X_PREFIX . "favorites (tid, username, type) VALUES ('" . intval($this->tid) . "', '" . $db->escape($self['username']) . "', 'favorite')");
            $this->dirty = false;

            return true;
        }
        return false;
    }

    public function delete()
    {
        if ($this->dirty && $this->tid > 0) {
            $this->deleteByTid($this->tid);
            $this->tid = 0;
            $this->dirty = false;
            return true;
        }
        return false;
    }

    public function deleteByTid($tid)
    {
        global $db, $self;

        if ($tid === 0) {
            return false;
        }
        return $db->query("DELETE FROM " . X_PREFIX . "favorites WHERE username = '" . $self['username'] . "' AND type='favorite' AND tid = '" . intval($tid) . "'");
    }

    public function deleteByUid($uid)
    {
        global $db;

        if ($uid === 0) {
            return false;
        }

        $owner = $db->escape(Member::findUsernameByUid($uid));

        $db->query("DELETE FROM " . X_PREFIX . "favorites WHERE username = '$owner'");
    }

    public function deleteByFormTids()
    {
        global $db, $self;

        $toDelete = array();

        $query = $db->query("SELECT tid FROM " . X_PREFIX . "favorites WHERE username = '" . $self['username'] . "' AND type='favorite'");
        while (($sub = $db->fetchArray($query)) != false) {
            $delete = formInt("delete" . $sub['tid'] . "");
            if (is_numeric($delete)) {
                $toDelete[] = $delete;
            }
        }
        $db->freeResult($query);

        if (!empty($toDelete)) {
            $in = implode(' ,', $toDelete);
            $db->query("DELETE FROM " . X_PREFIX . "favorites WHERE username = '" . $db->escape($self['username']) . "' AND type='favorite' AND tid in (" . $in . ")");
        }
    }
}
