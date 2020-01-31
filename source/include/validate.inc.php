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
 * Based off XMB and XMB Forum 2 (BBCode)
 * Copyright (c) 2001 - 2012 The XMB Development Team
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
class page_token
{

    /**
     *
     * @access private
     * @var mixed
     */
    var $pageToken;

    /**
     *
     * @access private
     * @var mixed
     */
    var $sessionToken;

    /**
     *
     * @access public
     * @var string
     */
    var $newToken;

    /**
     * Initialization of the class
     *
     * Sets all the class variables to their needed values
     */
    function init()
    {
        $this->pageToken = $this->get_page_token();
        $this->sessionToken = $this->get_session_token();
        $this->newToken = md5(sha1(uniqid(rand(), true)));
        $this->set_session_token($this->newToken);
    }

    /**
     * Retrieves the 'token' REQUEST variable
     *
     * @return string the token that was retrieved
     */
    function get_page_token()
    {
        return getRequestVar('token');
    }

    /**
     * Retrieves the 'token' SESSION variable
     *
     * @return mixed the token that was retrieved if it's set, false otherwise
     */
    function get_session_token()
    {
        return (isset($_SESSION['token'])) ? $_SESSION['token'] : false;
    }

    /**
     * Sets the 'token' SESSION variable
     *
     * @param string $token
     *            the token to set the 'token' variable as
     * @return string the token that was retrieved
     */
    function set_session_token($token)
    {
        $_SESSION['token'] = $token;
        return $token;
    }

    /**
     * Retrieves the a new token generated at initialization
     *
     * @return string the new token
     */
    function get_new_token()
    {
        return $this->newToken;
    }

    /**
     * Checks for valid token.
     * Error's if there is not one.
     *
     * @return boolean true no matter what
     */
    function assert_token()
    {
        global $lang;

        if ($this->sessionToken === false || $this->pageToken === false || $this->sessionToken !== $this->pageToken) {
            error($lang['textnoaction'], false);
        }
        // This old token has been used - prevent reuse
        $this->sessionToken = false;
        $this->pageToken = false;
        return true;
    }
}

/**
 * Make user input reasonably safe for display
 *
 * This function used to be used by XMB 1.9.1 and its use is now
 *
 * @param string $varname
 *            the variable to sanitize
 * @param boolean $striptags
 *            remove HTML and PHP tags from the input
 * @return string the "safe" string
 * @deprecated discouraged in favor of using the correct formXXX / getXXX functions from this file
 *
 */
function checkInput($varname, $striptags = true)
{
    $fullcode = array();
    $matches = array();
    $retval = '';
    if (isset($varname) && $varname !== '') {
        $retval = trim($varname);
        if ($striptags) {
            if (preg_match('#\[code\](.*?)\[/code\]#si', $retval)) {
                $retval = preg_replace_callback('#\[code\](.*?)\[/code\]#si', 'cleanHtml', $retval);
                preg_match_all('#\[code\](.*?)\[/code\]#si', $retval, $matches, PREG_OFFSET_CAPTURE);
                foreach ($matches[0] as $key => $value) {
                    $fullcode[] = array(
                        $value[0],
                        $value[1]
                    );
                }
                $retval = preg_replace('#\[code\](.*?)\[/code\]#si', '', $retval);
            }
        }
        $retval = ($striptags) ? strip_tags($retval) : $retval;
    }

    if ($striptags && isset($fullcode)) {
        foreach ($fullcode as $code) {
            $retval = substr_replace($retval, $code[0], $code[1], 0);
        }
    }
    return $retval;
}

/**
 * Make the input string safe for output
 *
 * @param string $output
 *            the string to make safe
 * @param string $word
 *            specific word to make safe, like "javascript"
 * @param boolean $stripslashes
 *            do slashes need stripping?
 * @return string the "safe" string
 *
 */
function checkOutput($output, $word = '', $stripslashes = true)
{
    if ($stripslashes) {
        $output = stripslashes($output);
    }

    $output = htmlentities(trim($output), ENT_QUOTES);

    if ($word != '') {
        $output = str_replace($word, "_" . $word, $output);
    }
    return $output;
}

if (!function_exists('htmlspecialchars_decode')) {

    /**
     * Decodes Html special characters
     *
     * @param string $string
     *            the string to be decoded
     * @param integer $type
     *            the quote style
     * @return string the decoded string
     */
    function htmlspecialchars_decode($string, $type = ENT_QUOTES)
    {
        $array = array_flip(get_html_translation_table(HTML_SPECIALCHARS, $type));
        return strtr($string, $array);
    }
}

if (!function_exists('htmlentities_decode')) {

    /**
     * Decodes Html entities
     *
     * @param string $string
     *            the string to be decoded
     * @param integer $type
     *            the quote style
     * @return string the decoded string
     */
    function htmlentities_decode($string, $type = ENT_QUOTES)
    {
        $array = array_flip(get_html_translation_table(HTML_ENTITIES, $type));
        return strtr($string, $array);
    }
}

/**
 * Validate posts per page
 *
 * Validate the global $ppp variable and ensure it's sane
 */
function validatePpp()
{
    global $self, $CONFIG;

    if (!isset($self['ppp']) || $self['ppp'] == '') {
        $self['ppp'] = $CONFIG['postperpage'];
    } else {
        $self['ppp'] = is_numeric($self['ppp']) ? (int)$self['ppp'] : $CONFIG['postperpage'];
    }

    if ($self['ppp'] < 5) {
        $self['ppp'] = 30;
    }
}

/**
 * Validate threads per page
 *
 * Validate the global $self['tpp'] variable and ensure it's sane
 */
function validateTpp()
{
    global $self, $CONFIG;

    if (!isset($self['tpp']) || $self['tpp'] == '') {
        $self['tpp'] = $CONFIG['topicperpage'];
    } else {
        $self['tpp'] = is_numeric($self['tpp']) ? (int)$self['tpp'] : $CONFIG['topicperpage'];
    }

    if ($self['tpp'] < 5) {
        $self['tpp'] = 30;
    }
}

/**
 * Checks if the supplied filename is valid
 *
 * @return boolean true if the filename is valid, false otherwise
 *
 */
function isValidFilename($filename)
{
    return preg_match('#^[^:\\/?*<>|]+$#', trim($filename));
}

/**
 * Checks if the supplied email address is valid
 *
 * @return boolean true if the e-mail address is valid, false otherwise
 *
 */
function isValidEmail($addr)
{
    $emailPattern = '/^([_a-z0-9-]+)(.[_a-z0-9-]+)*@([a-z0-9-]+)(.[a-z0-9-]+)*(.[a-z]{2,4})$/i';
    $emailValid = false;

    if (preg_match($emailPattern, $addr) > 0) {
        $user = '';
        $domain = '';
        list ($user, $domain) = explode('@', $addr);

        // Check if the site has an MX record. We can't send unless there is.
        $mxrecords = array();
        $weights = array();
        $found = getmxrr($domain, $mxrecords, $weights);
        if ($found) {
            $emailValid = true;
        }
    }
    return $emailValid;
}

/**
 * Has the named submit button been invoked?
 *
 * Looks in the form post data for a named submit
 *
 * @return boolean true if the submit is present, false otherwise
 *
 */
function onSubmit($submitname)
{
    $retval = (isset($_POST[$submitname]) && !empty($_POST[$submitname]));

    if (!$retval) {
        $retval = (isset($_GET[$submitname]) && !empty($_GET[$submitname]));
    }

    return ($retval);
}

/**
 * Is the forum being viewed?
 *
 * Looks for pre-form post data for a named submit
 *
 * @return boolean true if the no submit is present, false otherwise
 *
 */
function noSubmit($submitname)
{
    return (!onSubmit($submitname));
}

/**
 * Retrieve a POST variable and sanitizes it
 *
 * @param string $varname
 *            name of the variable in $_POST
 * @param boolean $striptags
 *            do a striptags to remove HTML tags
 * @param boolean $quotes
 *            do a htmlspecialchars to sanitize input for XSS
 * @return string the "safe" string if the variable is available, empty otherwise
 *
 */
function formVar($varname, $striptags = true, $quotes = false)
{
    $retval = '';
    if (isset($_POST[$varname]) && $_POST[$varname] !== '') {
        $retval = trim($_POST[$varname]);
        if ($striptags) {
            $retval = strip_tags($retval);
        }

        if ($quotes) {
            $retval = htmlspecialchars($retval, ENT_QUOTES);
        } else {
            $retval = htmlspecialchars($retval, ENT_NOQUOTES);
        }
    }
    return $retval;
}

/**
 * Retrieve the contents of an array from a POST
 *
 * This function will attempt to retrieve a named array($varname)
 * and sanitize it based upon type($type). If a string, $striptags indicates
 * if striptags should be used, and $quotes is only enabled when you want it
 *
 * This function always returns an array. It will be an empty array if there's
 * no data or variable to be returned.
 *
 * @param string $varname
 *            name of the variable in $_POST
 * @param boolean $striptags
 *            strings only: do a striptags to remove HTML tags
 * @param boolean $quotes
 *            strings only: do a htmlspecialchars to sanitize input for XSS
 * @param string $type
 *            'string' or 'int' to specify what needs to be done to the values
 * @return array the array found for $varname, empty otherwise
 *
 */
function formArray($varname, $striptags = true, $quotes = false, $type = 'string')
{
    $arrayItems = array();
    // Convert a single or comma delimited list to an array
    if (isset($_POST[$varname]) && !is_array($_POST[$varname])) {
        if (strpos($_POST[$varname], ',') !== false) {
            $_POST[$varname] = explode(',', $_POST[$varname]);
        } else {
            $_POST[$varname] = array(
                $_POST[$varname]
            );
        }
    }

    if (isset($_POST[$varname]) && is_array($_POST[$varname]) && count($_POST[$varname]) > 0) {
        $arrayItems = $_POST[$varname];
        foreach ($arrayItems as $item => $theObject) {
            $theObject = &$arrayItems[$item];
            switch ($type) {
                case 'int':
                    $theObject = intval($theObject);
                    break;
                case 'string':
                default:
                    if ($striptags) {
                        $theObject = strip_tags($theObject);
                    }
                    if ($quotes) {
                        $theObject = htmlspecialchars($theObject, ENT_QUOTES);
                    } else {
                        $theObject = htmlspecialchars($theObject, ENT_NOQUOTES);
                    }
                    break;
            }
            unset($theObject);
        }
    }
    return $arrayItems;
}

/**
 * Retrieve a GET variable and sanitize it
 *
 * @param string $varname
 *            name of the variable in $_GET
 * @param boolean $striptags
 *            do a striptags to remove HTML tags
 * @param boolean $quotes
 *            do a htmlspecialchars to sanitize input for XSS
 * @return string the "safe" string if the variable is available, empty otherwise
 *
 */
function getVar($varname, $striptags = true, $quotes = true)
{
    $retval = '';
    if (isset($_GET[$varname]) && $_GET[$varname] !== '') {
        $retval = urldecode(trim($_GET[$varname]));
        if ($striptags) {
            $retval = strip_tags($retval);
        }

        if ($quotes) {
            $retval = htmlspecialchars($retval, ENT_QUOTES);
        }
    }
    return $retval;
}

/**
 * Retrieve a GET integer and sanitize it
 *
 * @param string $varname
 *            name of the variable in $_GET
 * @return integer the "safe" integer if the variable is available, zero otherwise
 *
 */
function getInt($varname)
{
    $retval = 0;
    if (isset($_GET[$varname]) && is_numeric($_GET[$varname])) {
        $retval = (int)$_GET[$varname];
    }
    return $retval;
}

/**
 * Retrieve a REQUEST integer and sanitize it
 *
 * @param string $varname
 *            name of the variable in $_REQUEST
 * @return integer the "safe" integer if the variable is available, zero otherwise
 *
 */
function getRequestInt($varname)
{
    $retval = 0;
    if (isset($_REQUEST[$varname]) && is_numeric($_REQUEST[$varname])) {
        $retval = intval($_REQUEST[$varname]);
    }
    return $retval;
}

/**
 * Retrieve a REQUEST variable
 *
 * @param string $varname
 *            name of the variable in $_REQUEST
 * @return mixed the value of $varname, false otherwise
 *
 */
function getRequestVar($varname)
{
    return (isset($_REQUEST[$varname])) ? $_REQUEST[$varname] : false;
}

/**
 * Retrieve a POST integer and sanitize it
 *
 * @param string $varname
 *            name of the variable in $_POST
 * @param boolean $setZero
 *            should the return be set to zero if the variable doesnt exist?
 * @return mixed the "safe" integer or zero, empty string otherwise
 *
 */
function formInt($varname, $setZero = true)
{
    if ($setZero) {
        $retval = 0;
    } else {
        $retval = '';
    }

    if (isset($_POST[$varname]) && is_numeric($_POST[$varname])) {
        $retval = (int)$_POST[$varname];
    }
    return $retval;
}

/**
 * Return the array associated with varname
 *
 * This function interrogates the POST variable(form) for an
 * array of inputs submitted by the user. It checks that it exists
 * and returns false if no elements or not existent, and an array of
 * one or more integers if it does exist.
 *
 * @param string $varname
 *            the form field to find and sanitize
 * @return mixed false if not set or no elements, an array() of integers otherwise
 *
 */
function getFormArrayInt($varname, $doCount = true)
{
    if (!isset($_POST[$varname]) || empty($_POST[$varname])) {
        return false;
    }
    $retval = $_POST[$varname];

    if ($doCount) {
        if (count($retval) == 1) {
            $retval = array(
                $retval
            );
        }
    }

    foreach ($retval as $bits => $value) {
        $value = intval($value);
    }
    return $retval;
}

/**
 * Sanitizes a integer
 *
 * @param string $varname
 *            name of the variable
 * @return integer the "safe" integer if available, zero otherwise
 *
 */
function valInt($varname)
{
    $retval = 0;
    if (isset($varname) && is_numeric($varname)) {
        $retval = (int)$varname;
    }
    return $retval;
}

/**
 * Retrieve a POST variable and check it for on value
 *
 * @param string $varname
 *            name of the variable in $_POST
 * @return string on if set to on, off otherwise
 *
 */
function formOnOff($varname)
{
    if (isset($_POST[$varname]) && strtolower($_POST[$varname]) == 'on') {
        return 'on';
    }
    return 'off';
}

/**
 * Retrieve a POST variable and check it for yes value
 *
 * @param string $varname
 *            name of the variable in $_POST
 * @return string yes if set to yes, no otherwise
 *
 */
function formYesNo($varname)
{
    if (isset($_POST[$varname]) && strtolower($_POST[$varname]) == 'yes') {
        return 'yes';
    }
    return 'no';
}

/**
 * Retrieve a POST variable and check it for yes value
 *
 * @param string $varname
 *            name of the variable
 * @param boolean $glob
 *            is this variable also a global?
 * @return string yes if set to yes, no otherwise
 *
 */
function valYesNo($varname, $glob = true)
{
    if ($glob) {
        global $varname;
    }

    if (isset($varname) && strtolower($varname) == 'yes') {
        return 'yes';
    }
    return 'no';
}

/**
 * Sanitizes a POST integer and checks it for 1 value
 *
 * @param string $varname
 *            name of the variable in $_POST
 * @return integer 1 if set to 1, 0 otherwise
 *
 */
function form10($varname)
{
    return (formInt($varname) == 1) ? 1 : 0;
}

/**
 * Sanitizes a POST integer and checks it for 3600 value
 *
 * @param string $varname
 *            name of the variable in $_POST
 * @return integer 3600 if set to 3600, 0 otherwise
 *
 */
function form3600($varname)
{
    return (formInt($varname) == 3600) ? 3600 : 0;
}

/**
 * Retrieve a POST boolean variable and check it for true value
 *
 * @param string $varname
 *            name of the variable in $_POST
 * @return boolean true if set to true, false otherwise
 *
 */
function formBool($varname)
{
    if (isset($_POST[$varname]) && strtolower($_POST[$varname]) == "true") {
        return 'true';
    }
    return 'false';
}

/**
 * Check if a variable is checked
 *
 * @param string $varname
 *            name of the variable
 * @param string $compare
 *            is $compare equal to $varname?
 * @return string checked html if $compare is equal to $varname, empty otherwise
 *
 */
function isChecked($varname, $compare = 'yes')
{
    return (($varname == $compare) ? 'checked="checked"' : '');
}

/**
 * Clean up some HTML
 *
 * @param array $matches
 *            matches from preg_replace_callback in checkInput()
 * @return string the "safe" string
 *
 */
function cleanHtml($matches)
{
    $string = htmlentities($matches[0], ENT_QUOTES);
    return $string;
}

/**
 * Replace easier date formats into PHP date formats
 *
 * @param string $format
 *            the easier date format
 * @return string the PHP date format
 *
 */
function formatDate($format)
{
    $format = str_replace(array(
        'mm',
        'dd',
        'yyyy',
        'yy'
    ), array(
        'n',
        'j',
        'Y',
        'y'
    ), $format);
    return ($format);
}

function pmTempAmp($message)
{
    $message = str_replace('&amp;', '&', $message);
    $message = str_replace('&amp;', '&', $message);
    return $message;
}

function rawHTMLmessage($rawstring, $allowhtml = 'no')
{
    if ($allowhtml == 'yes') {
        return censor(htmlspecialchars_decode($rawstring, ENT_NOQUOTES));
    } else {
        return censor(decimalEntityDecode($rawstring));
    }
}

function decimalEntityDecode($rawstring)
{
    $currPos = 0;
    while (($currPos = strpos($rawstring, '&amp;#', $currPos)) !== FALSE) {
        $tempPos = strpos($rawstring, ';', $currPos + 6);
        $entLen = $tempPos - ($currPos + 6);
        if ($entLen >= 3 And $entLen <= 5) {
            $entNum = substr($rawstring, $currPos + 6, $entLen);
            if (is_numeric($entNum)) {
                if (intval($entNum) >= 160 And intval($entNum) <= 65533) {
                    $rawstring = str_replace("&amp;#$entNum;", "&#$entNum;", $rawstring);
                }
            }
        }
        $currPos++;
    }

    return $rawstring;
}
