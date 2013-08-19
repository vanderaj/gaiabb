<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2013 The GaiaBB Group
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
function view_repair_index($path)
{
    top_box('Select repair actions to complete');
    $emerg = "disabled";
    $instr = " (Requires uploading emergency.php)";
    if (file_exists('./emergency.php')) {
        $emerg = "";
        $instr = "";
    }
    ?>
    <tr>
    <td class="tablerow" bgcolor="#ffffff">
    <br>
    <form action="index.php" method="post">
    <input type="checkbox" name="summary" value="on" checked>&nbsp;Display summary of admin/mod users</p>
    <input type="checkbox" name="resttemp" value="on">&nbsp;Restore templates</p>
    <input type="checkbox" name="resetset" value="on">&nbsp;Reset to factory settings</p>
    <input type="checkbox" name="config" value="on" <?php echo $emerg?>>&nbsp;Re-create config.php<?php echo $instr ?></p>
    <input type="checkbox" name="createsa" value="on" <?php echo $emerg?>>&nbsp;Create Super Administrator account<?php echo $instr ?></p>
    <input type="checkbox" name="disablesa" value="on" <?php echo $emerg?>>&nbsp;Disable all other admins/mods<?php echo $instr ?></p>
    <input type="hidden" name="path" value="<?php echo $path?>">
    <input type="hidden" name="step" value="repair">
    <INPUT TYPE="submit" VALUE="Continue &gt;" />
    </form>
    </td>
    </tr>
    </table>
    </tr>
    </table>
    <?php
    view_shadow();
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function view_repair_action()
{
    output_buffering(false);
    ?>
    <script src="../js/progressbar.js" type="text/javascript" language="javascript1.2"></script>
    <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center" bgcolor="#5176B5">
    <tr>
    <td>
    <table border="0" cellspacing="1px" cellpadding="5px" width="100%">
    <tr>
    <td class="category"><font color="#000000"><strong>Repairing GaiaBB</strong></font></td>
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
    <script type="text/javascript" language="javascript1.2"><!--
    var repairBar = new progressBar(
    1,         //border thickness
    '#000000', //border colour
    '#ced7e6', //background colour
    '#a4acb8', //bar colour
    400,       //width of bar (excluding border)
    16,        //height of bar (excluding border)
    1          //direction of progress: 1 = right, 2 = down, 3 = left, 4 = up
    );
    //--></script>
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
    return "repairBar";
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function view_repair_complete()
{
    print_error('Repair Complete', 'GaiaBB repair has completed. You should login and change the settings in the <a href="../admin/index.php">Admin Control Panel</a><p>Remove the install directory for additional safety.');
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function view_repair_warncomplete()
{
    print_error('Install Complete', 'GaiaBB repair has finished, but with warnings. We strongly recommend you attend to the warnings before continuing.');
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function view_admins($db, $tablepre)
{
    top_box('Administrator level users');
    $query = $db->query("SELECT username, status FROM `".$tablepre."members` WHERE status IN ('Super Administrator', 'Administrator', 'Super Moderator', 'Moderator') ORDER BY status, username");
    ?>
    <table border="0" cellspacing="1px" cellpadding="5px" width="100%" >
    <?php
    if ($query !== false && $db->num_rows($query) > 0)
    {
        while ($user = $db->fetch_array($query)) {
            echo "<tr><td class=\"tablerow\" bgcolor=\"#ffffff\" width=30%>$user[username]</td>";
            echo "<td class=\"tablerow\" bgcolor=\"#ffffff\" width=70%>$user[status]</td></tr>";
        }
    }
    else
    {
        echo "<tr><td class=\"tablerow\" bgcolor=\"#ffffff\" width=30%>No users</td>";
        echo "<td class=\"tablerow\" bgcolor=\"#ffffff\" width=70%>&nbsp;</td></tr>";
    }
    $db->free_result($query);
    ?>
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
}
?>