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

namespace GaiaBB;

class Cacheable
{

    public $maxlife;

    public $prefix;

    public $expiry;

    public function __construct($prefix, $maxlife)
    {
        global $onlinetime;

        $this->maxlife = $maxlife;
        $this->prefix = $prefix;

        if (isset($_SESSION[$prefix . '__expiry'])) {
            $this->expiry = $_SESSION[$prefix . '__expiry'];
        } else {
            $this->expiry = array();
            $_SESSION[$prefix . '__expiry'] = $this->expiry;
        }
    }

    public function getWorkDir($reset = 'no')
    {
        $full = dirname($_SERVER['PHP_SELF']);
        $pos = strrpos($full, '/');
        $pos++;
        $workdir = substr($full, $pos);
        if (isset($reset) && $reset == 'yes') {
            $this->setData('workdir', $workdir);
        }
        return $workdir;
    }

    public function setData($name, $object)
    {
        global $onlinetime;

        $_SESSION[$this->prefix . $name] = serialize($object);
        $this->expiry[$name] = $onlinetime + $this->maxlife;
        $_SESSION[$this->prefix . '__expiry'] = $this->expiry;
    }

    public function refresh()
    {
    }

    public function getData($name)
    {
        if ($this->isStale($name)) {
            $this->expire($name);
            return false;
        }

        if (isset($_SESSION[$this->prefix . $name])) {
            return unserialize($_SESSION[$this->prefix . $name]);
        }
        return false;
    }

    public function isStale($name)
    {
        global $onlinetime;

        if (isset($this->expiry[$name])) {
            return ($onlinetime > $this->expiry[$name]) ? true : false;
        }
        return true;
    }

    public function expire($name)
    {
        if (isset($_SESSION[$this->prefix . $name])) {
            unset($_SESSION[$this->prefix . $name]);
            unset($this->expiry[$name]);
            $_SESSION[$this->prefix . '__expiry'] = $this->expiry;
        }
    }
}
