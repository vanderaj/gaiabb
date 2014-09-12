<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2014 The GaiaBB Group
 * http://www.GaiaBB.com
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

if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false))
{
    exit('This file is not designed to be called directly');
}
/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function view_install_index()
{
    output_buffering(false);
    ?>
    <script src="../js/progressbar.js" type="text/javascript" language="javascript1.2"></script>
    <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center" bgcolor="#5176B5">
    <tr>
    <td>
    <table border="0" cellspacing="1px" cellpadding="5px" width="100%">
    <tr>
    <td class="category"><font color="#000000"><strong>Installing UltimaBB</strong></font></td>
    </tr>
    <tr>
    <td class="tablerow" bgcolor="#ffffff">
    <table>
    <tr>
    <td>
    <p class="subject">Progress</p>
    </td>
    </tr><tr>
    <td>
    <script type="text/javascript" language="javascript1.2">
    <!--
    var installBar = new progressBar(
        1,         //border thickness
        '#000000', //border colour
        '#ced7e6', //background colour
        '#a4acb8', //bar colour
        400,       //width of bar (excluding border)
        16,        //height of bar (excluding border)
        1          //direction of progress: 1 = right, 2 = down, 3 = left, 4 = up
    );
    //-->
    </script>
    </td>
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
    <td width="100%"><img src="../images/problue/shadow.gif" border="0" alt="" title="" /></td>
    </tr>
    </table>
    <?php
    return "installBar";
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function view_install_complete()
{
    $_SESSION = array();
    print_error('Install Complete', 'GaiaBB installation has completed. You should login and change the settings in the <a href="../admin/index.php">Admin Control Panel</a><p>Please consider removing the install directory for additional safety.');
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function view_install_warncomplete()
{
    $_SESSION = array();
    print_error('Install Complete', 'GaiaBB installation has completed, but with warnings. We strongly recommend you attend to the warnings before continuing.');
}
?>