<?php
/**
 * GaiaBB
 * Copyright (c) 2010 The GaiaBB Group
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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false))
{
    exit('This file is not designed to be called directly');
}

class DataAccessObject {

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function DataAccessObject() {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function connect($dbhost = "localhost", $dbuser, $dbpw, $dbname, $pconnect = 0, $force_db = false)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function panic($head, $msg)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function view_header($title = "Data Abstraction Layer")
    {
        $logo = ROOT.'images/problue/logo.gif';
        $navtext = "Data Abstraction Layer";
        $css = ROOT.'install/install.css';
        $imgpath = ROOT.'images/problue/';
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>
        <?php echo $title ?>
        </title>
        <link rel=stylesheet type="text/css" href="<?php echo $css?>" title="Stylesheet">
        <style type="text/css">@import url("<?php echo $css ?>");</style>
        </head>
        <body bgcolor="#97A6BF" text="#000000">
        <a name="top"></a>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" bgcolor="#5176B5" align="center"><tr><td><table border="0" cellspacing="1px" cellpadding="5px" width="100%" align="center"><tr><td bgcolor="#FFFFFF" align="center"><br />
        <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center">
        <tr>
        <td bgcolor="#5176B5"><table border="0" cellspacing="1" cellpadding="6" width="100%">
        <tr>
        <td width="74%" style="background-image: url(<?php echo $imgpath?>topbg.gif); text-align: left"><table border="0" width="100%" cellpadding="0" cellspacing="0">
        <tr>
        <td valign="top" rowspan="2"><a href="index.php"><img src="<?php echo $imgpath?>logo.gif" alt="Your Forums" title="Your Forums" border="0" /></a></td>
        <td align="right" valign="top"><font class="smalltxt">Last active: Never<br />
        </font>
        </td>
        </tr>
        <tr>
        <td align="right" valign="bottom"><font class="smalltxt">[ <?php echo $navtext?> ]</font></td>
        </tr>
        </table>
        </td>
        </tr>
        <tr>
        <td class="navtd">
        <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
        <td align="right"><a href="<?php echo ROOT?>index.php"><font class="navtd">Back to: <img src="<?php echo $imgpath?>home.gif" border="0" alt="Your Forums" title="Your Forums" /></font></a></td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center">
        <tr>
        <td width="100%" align="left"><img src="<?php echo $imgpath?>shadow.gif" border="0" alt="" title="" /></td>
        </tr>
        </table>
        <table cellspacing="0" cellpadding="1" border="0" width="97%" align="center">
        <tr>
        <td>
        <table width="100%" cellspacing="0" cellpadding="2" align="center">
        <tr>
        <td class="nav" align="left"> <a href="index.php"><?php echo $navtext?></a> </td>
        <td align="right">&nbsp;</td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <br />
        <?php
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function view_footer()
    {
        ?>
        <table align="center">
        <tr>
        <td align="center" class="smalltxt">
        <br />
        <br />
        Powered by GaiaBB
        <br />
        Copyright © 2009 The GaiaBB Group. All rights reserved.
        <br />
        <br />
        </td>
        </tr>
        </table>
        <br /></td></tr></table></td></tr></table>
        <a id="bottom" name="bottom"></a>
        </body>
        </html>
        <?php
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function view_shadow()
    {
        ?>
        <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center">
        <tr>
        <td width="100%" align="left"><img src="<?php echo ROOT?>images/problue/shadow.gif" border="0" alt="" title="" /></td>
        </tr>
        </table>
        <?php
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function select_db($database)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function find_database()
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function error()
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function free_result($query = null)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function fetch_array($query, $type=SQL_ASSOC)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function field_type($result, $field)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function field_name($result, $field)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function field_len($result, $field)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function field_flags($result, $field)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function field_table($result, $field)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function query($sql)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function fetch_tables($dbname = NULL)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function result($query, $row = 0, $field = NULL)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function num_rows($query)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function num_fields($query)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function insert_id()
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function fetch_row($query)
    {
    }

    /**
    * time() - create a SQL timestamp
    *
    * @param    $time, optional, an arbitrary point in time
    * @return   string, the left padded timestamp suitable for SQL queries
    */
    function time($time=NULL)
    {
        global $onlinetime;

        if ($time === NULL)
        {
            $time = $onlinetime;
        }
        return "LPAD('".$time."', '15', '0')";
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function start_timer()
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function stop_timer()
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function getTables()
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function close()
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function getVersion()
    {
    }

    function insertAttachment($table, $aid, $tid, $pid, $filename, $filetype, $filesize, $fileheight, $filewidth, & $attachment, $downloads = 0)
    {
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function escape($str, $length = -1)
    {
    }
}
?>