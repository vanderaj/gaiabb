<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2014 The GaiaBB Group
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

// check to ensure no direct viewing of page
if (! defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

/**
 * Get rid of registered globals if they are set
 */
function disposeGlobals()
{
    if (ini_get('register_globals')) {
        if (isset($_REQUEST['GLOBALS'])) {
            die('Attack attempt logged.');
        }
        // Variables that shouldn't be unset
        $noUnset = array(
            'GLOBALS',
            '_GET',
            '_POST',
            '_COOKIE',
            '_REQUEST',
            '_SERVER',
            '_ENV',
            '_FILES'
        );
        $input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) ? (array) $_SESSION : array());
        foreach ($input as $k => $v) {
            if (! in_array($k, $noUnset) and isset($GLOBALS[$k])) {
                unset($GLOBALS[$k]);
            }
        }
    }
}

if (! defined('DEBUG_REG')) {
    // TODO REMOVE Here be olde code
    //
    // Olde code is crap and makes poor security assumptions, but boy is there a lot of it
    //
    // This path is inherited from XMB, and does two things:
    //
    // 1. addslashes() to all strings as the old code assumes magic_gpc is on
    // 2. extracts all variables from $_POST etc to globals just as per the ye olden days
    //
    // Of course, this slows us down and is insecure. This code has limited lifespan
    $global = @array(
        0 => $_GET,
        1 => $_POST,
        2 => $_ENV,
        3 => $_COOKIE,
        4 => $_SERVER,
        5 => $_FILES
    );
    if (get_magic_quotes_gpc() === 0) {
        foreach ($global as $keyg => $valg) {
            if (is_array($valg)) {
                foreach ($valg as $keya => $vala) {
                    if (is_array($vala)) {
                        foreach ($vala as $keyv => $valv) {
                            if (gettype($valv) == "string") {
                                $global[$keyg][$keya][$keyv] = addslashes($valv);
                            }
                        }
                    } else 
                        if (gettype($vala) == "string") {
                            $global[$keyg][$keya] = addslashes($vala);
                        }
                }
            }
        }
        foreach ($global as $num => $array) {
            if (is_array($array)) {
                extract($array, EXTR_SKIP);
            }
        }
    } else {
        foreach ($global as $num => $array) {
            if (is_array($array)) {
                extract($array, EXTR_SKIP);
            }
        }
    }
}

// magic_gpc_quotes is evil, and it must be stamped out
if (defined('DEBUG_REG')) {
    $global = @array(
        0 => $_GET,
        1 => $_POST,
        2 => $_ENV,
        3 => $_COOKIE,
        4 => $_SERVER,
        5 => $_FILES
    );
    if (get_magic_quotes_gpc() === 1) {
        foreach ($global as $keyg => $valg) {
            if (is_array($valg)) {
                foreach ($valg as $keya => $vala) {
                    if (is_array($vala)) {
                        foreach ($vala as $keyv => $valv) {
                            if (gettype($valv) == "string") {
                                $global[$keyg][$keya][$keyv] = stripslashes($valv);
                            }
                        }
                    } else 
                        if (gettype($vala) == "string") {
                            $global[$keyg][$keya] = stripslashes($vala);
                        }
                } // end for
            } // end if
        } // end for
    }
}
?>