<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB's installer (ajv)
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
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

require_once "convert.model.php";

class phpbb2 extends convert
{

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function disableBoards()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function isAuth()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function members()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function posts()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function polls()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function ranks()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function threads()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function forums()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    // phpBB 2.x does not have attachments. Nothing to do here
    function attachments()
    {
        return true;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function addresses()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function favorites()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function subscriptions()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function censors()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function banned()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function settings()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function messages()
    {
    }
}
