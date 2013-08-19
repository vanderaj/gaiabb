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
function print_error($head, $msg, $die=true)
{
    ?>
    <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center" bgcolor="#5176B5">
    <tr>
    <td>
    <table border="0" cellspacing="1px" cellpadding="5px" width="100%">
    <tr>
    <td class="category"><font color="#000000"><strong><?php echo $head?></strong></font></td>
    </tr>
    <tr>
    <td class="tablerow" bgcolor="#ffffff"><?php echo $msg?></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    <?php
    view_shadow();
    if ($die)
    {
        view_footer();
        exit;
    }
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function setBar($thebar, $value)
{
    echo "<script>$thebar.setBar($value)</script>";
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function setCol($theBar, $color)
{
    echo "<script>$theBar.setCol('$color')</script>";
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function view_config($path)
{
    top_box('Forum Configuration');
    ?>
    <tr>
    <td class="tablerow" bgcolor="#ffffff">
    <table border=0 cellspacing=0 cellpadding=0 width="80%" >
    <tr>
    <td width="80%">
    <form action="index.php" method="post" autocomplete="off">
    <div align="center">
    <center>
    <table border="0" cellspacing="0" width="80%" cellpadding="0">
    <tr>
    <td width="101%" colspan="2"><p><br />Please choose the configuration method:<br /><br />
    <input type="radio" name="confMethod" value="create" checked>Attempt to create config.php for me<br />
    <input type="radio" name="confMethod" value="display">Show the proposed configuration on screen<br />
    <input type="radio" name="confMethod" value="download">Download the configuration for manual upload<br /><br />
    </td>
    </tr>
    <tr>
    <td width="101%" colspan="2">
    <input type="hidden" name="path" value="<?php echo $path?>">
    <input type="hidden" name="step" value="conf">
    <input type="submit" value="Configure &gt;" name="submit">
    </form>
    </td>
    </tr>
    </table>
    </center>
    </div>
    </td>
    </tr>
    </table>
    </table>
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
function view_config_screen($path, $config)
{
    top_box('Copy and paste the following into a file called "config.php" and upload it to your server');
    ?>
    <tr>
    <td class="tablerow" bgcolor="#ffffff">
    <?php highlight_string($config); ?>
    </td>
    </tr>
    <tr>
    <td>
    <form action="index.php" method="post">
    <input type="hidden" name="step" value="conf" />
    <input type="hidden" name="path" value="<?php echo $path?>" />
    <input type="submit" value="Continue &gt;" />
    </form>
    <?php
}

/**
* view_config_download() - Downloading the config file
* 
* Download the configuration via octet stream
*
* @param    config    string, a line delimeted file ready to be downloaded
*/
function view_config_download($config)
{
    header("Content-type: application/octet-stream");
    $size = strlen($config);
    header("Content-length: $size");
    header("Content-Disposition: attachment; filename=config.php");
    header("Content-Description: GaiaBB Configuration");
    header("Pragma: no-cache");
    header("Expires: 0");
    // Start file download
    echo $config;
}

/**
* function() - short description of function
*
* Long description of function
*
* @param    $varname    type, what it does
* @return   type, what the return does
*/
function view_eula($path)
{
    if ($path == 'install')
    {
        $step = 'backup';
    }
    else
    {
        $step = 'eula';
    }

    top_box(INSTALLVER . ' License Agreement');
    ?>
    <tr>
    <td class="tablerow" bgcolor="#ffffff">
    <table border="0" cellspacing="0" cellpadding="0" cols="1" width="80%">
    <tr>
    <td>
    <p>Please read over the agreement below, and if you agree, click "I agree to these terms"
    under the agreement.</p>
    <br /><br />
    </td>
    </tr>
    <tr>
    <td align="center" valign="center">
    <textarea cols="80" rows="15"  wrap='soft' name="agreement" style= "font-family: Courier, New Courier; font-size: 8pt" readonly="readonly">
    <?php
    $license = @file_get_contents(ROOT . "install/COPYING");
    if ($license == '')
    {
        ?>
        License not found! Please upload COPYING to the installation area.
        </textarea><br /><br />
        <?php
    }
    else
    {
        echo $license;
        ?>
        </textarea><br /><br />
        <form action="index.php" method="post">
        <input type="hidden" name="step" value="eula">
        <input type="hidden" name="path" value="<?php echo $path?>">
        <input type="submit" value="I Agree To These Terms &gt;" />
        </form>
        <?php
    }
    ?>
    </td>
    </tr>
    </table>
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
function view_backup($path, $boardType = "GaiaBB")
{
    top_box('Dangerous Operation Warning');
    ?>
    <tr>
    <td class="tablerow" bgcolor="#ffffff">
    Before we can proceed, please check the following:
    <br /><br />
    <form action="index.php" method="post">
    <input type="checkbox" name="fbackup">&nbsp;Do you have a full backup of your <?php echo $boardType?> files?<br />
    <input type="checkbox" name="dbackup">&nbsp;Do you have a full backup of your <?php echo $boardType?> database?<br />
    <input type="hidden" name="path" value="<?php echo $path?>">
    <input type="hidden" name="step" value="backup">
    <br />
    <input type="submit" value="Yes &gt;" />
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
function view_admin($path)
{
    $action = $redir = '';

    switch ($path)
    {
        case 'convert':
        case 'upgrade':
        case 'repair':
            $action = 'Confirm';
            break;
        case 'install':
        default:
            $action = 'Create';
    }

    top_box($action . ' Super Administrator Credentials')
    ?>
    <tr>
    <td class="tablerow" bgcolor="#ffffff">
    <p>Please enter the username, password
    <?php
    if ($path == "install")
    {
        ?>
        , and e-mail address
        <?php
    }
    ?>
    &nbsp;for the Super Administrator.</p>
    <?php
    if ($path == "convert")
    {
        ?>
        <br />
        <p class="subTitle">Conversion Note:</p>
        <p>The admin credentials <b>must</b> be the same for both the original board and the fresh GaiaBB install.
        <?php
    }
    ?>
    <br />
    <form action="index.php" method="post" autocomplete="off">
    <table>
    <tr>
    <td>Username:</td>
    <td><input type="text" name="frmUsername" size="32" autofocus="autofocus" /></td>
    </tr>
    <tr>
    <td>Password:</td>
    <td><input type="password" name="frmPassword" size="32" /></td>
    </tr>
    <?php
    if ($action == "Create")
    {
        ?>
        <tr>
        <td>Confirm Password:</td>
        <td><input type="password" name="frmPasswordCfm" size="32"></td>
        </tr>
        <tr>
        <td>E-mail:</td>
        <td><input type="text" name="frmEmail" size="32"></td>
        </tr>
        <?php
    }
    ?>
    <tr>
    <td>
    <br />
    <br />
    <input type="hidden" name="path" value="<?php echo $path?>">
    <input type="hidden" name="step" value="admin">
    <input type="hidden" name="type" value="<?php echo $action?>">
    <input type="submit" value="<?php echo $action?> &gt;">
    </td>
    </tr>
    </table>
    </form>
    </td>
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
function top_box($header = 'CHANGE ME')
{
    ?>
    <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center" bgcolor="#5176B5">
    <tr>
    <td>
    <table border="0" cellspacing="1px" cellpadding="5px" width="100%">
    <tr>
    <td style="background-image: url(../images/problue/topbg.gif)" class="category"><font color="#000000">
    <strong><?php echo $header?></strong></font></td>
    </tr>
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
function view_default_index()
{
    top_box('Thank you for choosing GaiaBB as your message board.');
    ?>
    <tr>
    <td class="tablerow" bgcolor="#ffffff">
    Please choose one of the following options:
    <br /><br />
    <form action="index.php" method="post">
    <input type="radio" name="path" value="install" checked="checked" />Install <?php echo INSTALLVER?><br />
    <input type="radio" name="path" value="repair" />Repair <?php echo INSTALLVER?><br />
    <input type="radio" name="path" value="upgrade" />Upgrade to latest <?php echo INSTALLVER?><br />
    <br />
    <input type="radio" name="path" value="convertxmb" />Convert from XMB Forum 1.9.x<br />
    <input type="radio" name="path" value="convertphpbb" />Convert from phpBB 2.0.x<br />
    <input type="hidden" name="step" value="preflight" />
    <input type="hidden" name="noconfig" value="1" />
    <br />
    <input type="submit" value="Next &gt;" />
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
function view_header($title = "GaiaBB Installer", $path)
{
    $logo = ROOT.'images/logo.png';
    $installnavimg =  $upgradenavimg = $repairnavimg = $convertnavimg = ROOT."images/problue/home.gif";
    $navtext = '';
    switch ($path)
    {
        case 'install':
            $installnavimg = ROOT."images/prored/home.gif";
            $navtext = "Install";
            break;
        case 'upgrade':
            $upgradenavimg = ROOT."images/prored/home.gif";
            $navtext = "Upgrade";
            break;
        case 'repair':
            $repairnavimg = ROOT."images/prored/home.gif";
            $navtext = "Repair";
            break;
        case 'convertxmb':
            $convertnavimg = ROOT."images/prored/home.gif";
            $navtext = "Convert";
            break;
    }
    ?>
	<!DOCTYPE html>
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title><?php echo $title?></title>
    <link rel=stylesheet type="text/css" href="install.css" title="Install stylesheet">
    <style type="text/css">@import url("install.css");</style>
    </head>
    <body bgcolor="#97A6BF" text="#000000">
    <a name="top"></a>
    <table cellspacing="0" cellpadding="0" border="0" width="100%" bgcolor="#5176B5" align="center"><tr><td><table border="0" cellspacing="1px" cellpadding="5px" width="100%" align="center"><tr><td bgcolor="#FFFFFF" align="center"><br />
    <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center">
    <tr>
    <td bgcolor="#5176B5"><table border="0" cellspacing="1" cellpadding="6" width="100%">
    <tr>
    <td width="74%" style="background-image: url(../images/problue/topbg.gif)"><table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
    <td valign="top" rowspan="2"><a href="index.php"><img src="../images/logo.png" alt="Your Forums" title="Your Forums" border="0" /></a></td>
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
    <td class="navtd">
    <img src="<?php echo $installnavimg?>" alt="Install" title="Install Forums" border="0" />
    <a href="index.php?action=install">
    <font class="navtd">Install</font></a> &nbsp;
    <img src="<?php echo $upgradenavimg?>" alt="Upgrade" title="Upgrade Forums" border="0" />
    <a href="index.php?action=upgrade">
    <font class="navtd">Upgrade</font></a> &nbsp;
    <img src="<?php echo $repairnavimg?>" alt="Repair" title="Repair Forums" border="0" />
    <a href="index.php?action=repair">
    <font class="navtd">Repair</font></a></font> &nbsp;
    <img src="<?php echo $convertnavimg?>" alt="Convert" title="Convert Forums" border="0" />
    <a href="index.php?action=convert"><font class="navtd">Convert</font></a>
    </td>
    <td align="right"><a href="../index.php"><font class="navtd">Back to: <img src="../images/problue/home.gif" border="0" alt="Your Forums" title="Your Forums" /></font></a></td>
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
    <table cellspacing="0" cellpadding="1" border="0" width="97%" align="center">
    <tr>
    <td>
    <table width="100%" cellspacing="0" cellpadding="2" align="center">
    <tr>
    <td class="nav"> <a href="index.php"><?php echo $navtext?></a> </td>
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
    Copyright &copy; 2013 GaiaBB Group. All rights reserved.
    <br />
    <br />
    </td>
    </tr>
    </table>
    <br /></td></tr></table></td></tr></table>
    <a id="bottom" name="bottom"></a>
    <?php
    if(DEBUG)
    {
        ?>
        <hr />
        Session Object:
        <pre>
        <?php print_r($_SESSION); ?>
        </pre>
        Form Object:
        <pre>
        <?php print_r($_POST); ?>
        </pre>
        DEBUG log:
        <pre>
        <?php global $debug_log; print_r($debug_log); ?>
        </pre>
        <?php
    }
    ?>
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
function view_database($path)
{
    $types = get_databases();
    top_box('Database Configuration');
    ?>
    <tr>
    <td class="tablerow" bgcolor="#ffffff">
    <table border="0" cellspacing="0" width="80%" cellpadding="0">
    <tr>
    <td width="80%">
    <form action="index.php" method="post">
    <input type="hidden" name="path" value="<?php echo $path?>">
    <input type="hidden" name="step" value="conf">
    <input type="hidden" name="confMethod" value="skip">
    <p><strong>Note:</strong> If you have already uploaded a correct config.php, click  <input type="submit" value="Skip this step &gt;" name="skip">
    </form>
    </td>
    </tr>
    <tr>
    <td width="80%">
    <p><strong>Otherwise</strong> Please fill in the details and click "Configure"<br />
    <br />
    <br />
    </td>
    </tr>
    <tr>
    <td width="48%">
    <form action="index.php" method="post" autocomplete="off">
    <strong>Database Name</strong><br />Name of your database<br />&nbsp;</font></td>
    <td width="53%"><input type="text" name="db_name" size="40" autofocus /><br /> &nbsp;</td>
    </font>
    </tr>
    <tr>
    <td width="48%"><strong>Database Username</font></strong><br />User used to access database<br />&nbsp;</font></td>
    <td width="53%"><input type="text" name="db_user" size="40" /><br />&nbsp;</td>
    </tr>
    <tr>
    <td width="48%"><strong>Database Password</font></strong><br />Keep this secret<br />&nbsp;</font></td>
    <td width="53%"><input type="password" name="db_pw" size="40" /><br />&nbsp;</td>
    </tr>
    <tr>
    <td width="48%"><strong>Database Host</font></strong><br />Database Host, usually &quot;<i>localhost or 127.0.0.1</i>&quot;<br />&nbsp;</font></td>
    <td width="53%"><input type="text" name="db_host" size="40" value="127.0.0.1" /><br />&nbsp;</td>
    </tr>
    <tr>
    <td width="48%"><strong>Database Type</font></strong><br />Usually MariaDB (also compatible with Oracle MySQL)</font></td>
    <td width="53%"><?php echo $types?><br />&nbsp;</td>
    </tr>
    <tr>
    <td width="48%"><strong>Table Prefix Setting</strong><br />This setting is for the table prefix, for every board you have installed, this should be different<br />&nbsp;</font></td>
    <td width="53%"><input type="text" name="table_pre" size="40" value="gbb_" /><br />  &nbsp;</td>
    </tr>
    <tr>
    <td width="48%"><strong>Full URL</strong><br />If this value is wrong, please change it after the forum has installed.<br />&nbsp;</font></td>
    <td width="53%"><input type="text" name="foo" size="40" value="<?php echo get_boardurl()?>" readonly="readonly" /><br />  &nbsp;</td>
    </tr>
    <tr>
    <td>
    <input type="hidden" name="path" value="<?php echo $path?>" />
    <input type="hidden" name="step" value="db" />
    <input type="submit" value="Configure &gt;" />
    </form>
    </td>
    </tr>
    </table>
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
function view_shadow()
{
    ?>
    <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center">
    <tr>
    <td width="100%"><img src="../images/problue/shadow.gif" border="0" alt="" title="" /></td>
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
// Control output buffering. Call with true for normal PHP behavior, call with false
// when you want PHP to send data to the user whenever echo or whatever is called.
function output_buffering($status)
{
    while (ob_get_level() > 0)
    {
        ob_end_flush();
    }

    if ($status)
    {
        ob_implicit_flush(0);
        ob_start();
    }
    else
    {
        ob_implicit_flush(1);
    }
}
?>