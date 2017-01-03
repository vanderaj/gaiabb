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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

class cacheable
{

    public $maxlife;

    public $prefix;

    public $expiry;

    function cacheable($prefix, $maxlife)
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

    function getWorkDir($reset = 'no')
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

    function setData($name, $object)
    {
        global $onlinetime;

        $_SESSION[$this->prefix . $name] = serialize($object);
        $this->expiry[$name] = $onlinetime + $this->maxlife;
        $_SESSION[$this->prefix . '__expiry'] = $this->expiry;
    }

    function refresh()
    {
    }

    function getData($name)
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

    function isStale($name)
    {
        global $onlinetime;

        if (isset($this->expiry[$name])) {
            return ($onlinetime > $this->expiry[$name]) ? true : false;
        }
        return true;
    }

    function expire($name)
    {
        if (isset($_SESSION[$this->prefix . $name])) {
            unset($_SESSION[$this->prefix . $name]);
            unset($this->expiry[$name]);
            $_SESSION[$this->prefix . '__expiry'] = $this->expiry;
        }
    }
}
