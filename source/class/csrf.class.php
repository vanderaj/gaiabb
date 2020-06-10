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
 * Forked from XMB and XMB Forum 2 (BBCode)
 * Copyright (c) 2001 - 2012 The XMB Development Team
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

/**
 * CSRF protection class.
 * Call this to obtain and test a page token.
 *
 * In GaiaBB 1.0, each user has a single token per page no matter which destination
 * action. These should be used for all actions. GaiaBB 1.1 will extend this to include
 * unique tokens per action, making it much harder for attackers to spoof any particular
 * action.
 *
 * As each page has many old and new, and only one token slot in the session,
 * there is a way to re-seed the session.
 *
 * @author ajv
 * @package GaiaBB
 * @license GPL
 *
 */

class CsrfToken
{

    /**
     *
     * @access private
     * @var mixed
     */
    public $csrfToken;

    /**
     *
     * @access private
     * @var mixed
     */
    public $sessionToken;

    /**
     *
     * @access public
     * @var string
     */
    public $newToken;

    public function __construct()
    {
        $this->csrfToken = $this->getCsrfToken();
        $this->sessionToken = $this->getSessionToken();
        $this->newToken = md5(sha1(uniqid(rand(), true)));
        $this->setSessionToken($this->newToken);
    }

    /**
     * Retrieves the "csrf_token" REQUEST variable
     *
     * @return string the token that was retrieved
     */
    public function getCsrfToken()
    {
        // This name is known to scanners and should allow them to identify that GaiaBB has CSRF protection
        // And yes, although this should be only in the form, there might be scenarios where providing the
        // token in the URL or a header might make sense
        return getRequestVar('csrf_token');
    }

    /**
     * Retrieves the "csrf_token" SESSION variable
     *
     * @return mixed the token that was retrieved if it's set, false otherwise
     */
    public function getSessionToken()
    {
        return (isset($_SESSION['csrf_token'])) ? $_SESSION['csrf_token'] : false;
    }

    /**
     * Sets the "csrf_token" SESSION variable
     *
     * @param string $token
     *            the token to set the "csrf_token" variable as
     * @return string the token that was retrieved
     */
    public function setSessionToken($token)
    {
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    /**
     * Retrieves the a new token generated at initialization
     *
     * @return string the new token
     */
    public function createToken()
    {
        return $this->newToken;
    }

    /**
     * assertToken() - asserts CSRF token is valid
     *
     * This function compares that the session and request CSRF token are present and the same.
     * It will display an error if not present or incorrect
     *
     * @return boolean true no matter what
     */
    public function assertToken()
    {
        global $lang;

        if ($this->sessionToken === false || $this->csrfToken === false || $this->sessionToken !== $this->csrfToken) {
            error("CSRF: " . $lang['textnoaction'], false);
        }
        // This old token has been used - prevent reuse
        $this->sessionToken = false;
        $this->csrfToken = false;
        return true;
    }
}
