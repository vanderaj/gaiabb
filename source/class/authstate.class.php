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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

namespace GaiaBB;

class AuthState
{

    public $gbbuid;
    public $gbbpw;
    private $state;

    public function __construct()
    {
        $this->state = array();
        $this->gbbuid = '';
        $this->gbbpw = '';

        $this->get();
    }

    public function get()
    {
        if (isset($_COOKIE['gbbstate'])) {
            try {
                $tmpState = $_COOKIE['gbbstate'];

                $tmpState = base64_decode($tmpState, true);

                if ($tmpState === false) {
                    throw new \Exception("Invalid decode of state string");
                }

                // $this->state = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, GAIABB_MASTERKEY, $tmpState, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
                // if ($this->state === false)
                // {
                // throw new Exception("Invalid decryption");
                // }
                $this->state = unserialize($tmpState);

                if (isset($this->state['version']) && $this->state['version'] != 1) {
                    throw new \Exception("Invalid state version, or state is not valid.");
                }

                $this->gbbuid = $this->state['gbbuid'];
                $this->gbbpw = $this->state['gbbpw'];
            } catch (\Exception $e) {
                global $db;

                $db->panic("authState :: get() - Failed to decrypt authentication state", $e);
            }
        }
    }

    public function convert()
    {
        if (isset($_COOKIE['gbbuid'])) {
            $this->gbbuid = $_COOKIE['gbbuid'];
        }

        if (isset($_COOKIE['gbbpw'])) {
            $this->gbbpw = $_COOKIE['gbbpw'];
        }

        $this->update();
    }

    public function update()
    {
        global $onlinetime, $cookiepath, $cookiedomain;

        try {
            $this->state['version'] = 1;
            $this->state['gbbuid'] = $this->gbbuid;
            $this->state['gbbpw'] = $this->gbbpw;

            $tmpState = serialize($this->state);

            // $tmpState = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, GAIABB_MASTERKEY, $tmpState, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
            // if ($this->state === false)
            // {
            // throw new Exception("Invalid encryption");
            // }
            $tmpState = base64_encode($tmpState);
            $currtime = $onlinetime + (86400 * 30);
            setcookie('gbbstate', $tmpState, $currtime, $cookiepath, $cookiedomain);
        } catch (\Exception $e) {
            global $db;

            $db->panic("authState :: update() - Failed to update authentication state", $e);
        }
    }
}
