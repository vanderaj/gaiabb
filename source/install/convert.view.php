<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB's installer (ajv)
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
// phpcs:disable PSR1.Files.SideEffects
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function view_convert_backup($path, $boardType = "XMB")
{
    top_box('Dangerous Operation Warning');
    ?>
    <tr>
        <td class="tablerow" bgcolor="#ffffff">Before we can proceed, please
            check the following: <br/>
            <br/>
            <form action="index.php" method="post">
                <input type=checkbox name="fbackup">&nbsp;Do you have a full backup of your <?php echo $boardType ?>
                files?<br/>
                <input type=checkbox name="dbackup">&nbsp;Do you have a full backup of your <?php echo $boardType ?>
                database?<br/>
                <input type=hidden name="path" value="<?php echo $path ?>"> <input
                        type=hidden name="step" value="install"> <br/> <input type="submit"
                                                                              VALUE="Yes &gt;"/>
            </form>
        </td>
    </tr>
    </table>
    </tr>
    </table>
    <?php
    viewShadow();
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function view_convert_index($path)
{
    top_box('Dangerous Operation Warning');
    ?>
    <tr>
        <td class="tablerow" bgcolor="#ffffff">Before we can proceed, please
            check the following: <br/>
            <br/>
            <form action="index.php" method="post">
                <input type=checkbox name="gbbinstall">&nbsp;Have you already
                successfully installed GaiaBB?<br/> <input type=hidden name="path"
                                                           value="<?php echo $path ?>"> <input type=hidden name="step"
                                                                                               value="preconvert"> <br/>
                <input type="submit" VALUE="Yes &gt;"/>
            </form>
        </td>
    </tr>
    </table>
    </tr>
    </table>
    <?php
    viewShadow();
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function view_convert_details($path, $boardtype = 'XMB')
{
    top_box($boardtype . ' Database Details');
    ?>
    <tr>
        <td bgcolor="#ffffff">
            <table border="0" cellspacing="0" cellpadding="0" class="tablerow">
                <tr>
                    <td width="100%"><strong>Please Note!</strong><br/>
                        <ul>
                            <li><strong>You must have successfully completed a fresh
                                    installation of GaiaBB before running this tool.</strong></li>
                            <li><strong>Any existing data in the GaiaBB database will be
                                    destroyed in this upgrade.</strong></li>
                            <li>The conversion process will disable the <?php echo $boardtype; ?> board for safety
                                reasons.
                            </li>
                            <li>The conversion process will require up to twice the amount of disk space
                                that <?php echo $boardtype; ?> is currently using
                            </li>
                            <li>The conversion process may take some considerable time,
                                particularly if the database servers are not connected to
                                localhost
                            </li>
                            <li>The conversion process assumes you have written out a good
                                config.php for GaiaBB prior to performing the conversion
                            </li>
                            <li>It is safe to re-start the conversion process, but it will
                                start from scratch every time.
                            </li>
                            <li>When you are satisifed with the conversion, you may remove the <?php echo $boardtype; ?>
                                prefixed tables as they are no longer used.
                            </li>
                        </ul>
                        <br/></td>
                </tr>
                <form action="index.php" method="post" AUTOCOMPLETE="off">
                    <tr>
                        <td width="48%"><strong><?php echo $boardtype; ?> Database Name</strong><br/>Name
                            of your database<br/>&nbsp;</td>
                        <td width="53%"><input type="text" name="db_name" size="40"/><br/>
                            &nbsp;</td>
                    </tr>
                    <tr>
                        <td width="48%"><strong><?php echo $boardtype; ?> Database Username</strong><br/>User
                            used to access database<br/>&nbsp;</td>
                        <td width="53%"><input type="text" name="db_user" size="40"/><br/>&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="48%"><strong><?php echo $boardtype; ?> Database Password</strong><br/>Keep
                            this secret<br/>&nbsp;</td>
                        <td width="53%"><input type="password" name="db_pw" size="40"/><br/>&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="48%"><strong><?php echo $boardtype; ?> Database Host</strong><br/>Database
                            Host, usually &quot;<i>localhost</i> or <i>127.0.0.1</i> &quot;<br/>&nbsp;</td>
                        <td width="53%"><input type="text" name="db_host" size="40"
                                               value="127.0.0.1"/><br/>&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="48%"><strong><?php echo $boardtype; ?> Table Prefix Setting</strong><br/>This
                            setting is for the table prefix, for every board you have
                            installed, this should be different<br/>&nbsp;</td>
                        <td width="53%"><input type="text" name="table_pre" size="40"
                                               value="xmb_"/><br/> &nbsp;</td>
                    </tr>
                    <tr>
                        <td width="48%"><strong><?php echo $boardtype; ?> Super Administrator Username</strong><br/>The
                            username of the Super Administrator or comparable status for <?php echo $boardtype; ?><br/>&nbsp;
                        </td>
                        <td width="53%"><input type="text" name="admin_user" size="32"
                                               value=""/><br/> &nbsp;</td>
                    </tr>
                    <tr>
                        <td width="48%"><strong><?php echo $boardtype; ?> Super Administrator Password</strong><br/>The
                            password of the Super Administrator or comparable status for <?php echo $boardtype; ?><br/>&nbsp;
                        </td>
                        <td width="53%"><input type="password" name="admin_pass" size="40"
                                               value=""/><br/> &nbsp;</td>
                    </tr>
                    <tr>
                        <td width="48%"><strong>GaiaBB Super Administrator Username</strong><br/>The
                            username of the Super Administrator for GaiaBB<br/>&nbsp;</td>
                        <td width="53%"><input type="text" name="gbb_admin_user" size="32"
                                               value=""/><br/> &nbsp;</td>
                    </tr>
                    <tr>
                        <td width="48%"><strong>GaiaBB Super Administrator Password</strong><br/>The
                            password of the Super Administrator for GaiaBB<br/>&nbsp;</td>
                        <td width="53%"><input type="password" name="gbb_admin_pass"
                                               size="40" value=""/><br/> &nbsp;</td>
                    </tr>
                    <tr>
                        <td><input type=hidden name="path" value="<?php echo $path ?>"> <input
                                    type=hidden name="step" value="convert"> <input type="submit"
                                                                                    value="Convert &gt;">

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
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function view_convert_action($boardtype = 'XMB')
{
    output_buffering(false);
    ?>
    <script src="../js/progressbar.js" type="text/javascript" language="javascript1.2"></script>
    <table cellspacing="0" cellpadding="0" border="0" width="97%"
           align="center" bgcolor="#5176B5">
        <tr>
            <td>
                <table border="0" cellspacing="1px" cellpadding="5px" width="100%">
                    <tr>
                        <td class="category"><font color="#000000"><strong>Converting <?php echo $boardtype; ?></strong></font>
                        </td>
                    </tr>
                    <tr>
                        <td class="tablerow" bgcolor="#ffffff">
                            <table>
                                <tr>
                                    <td>
                                        <p class="subject">Progress</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <script type="text/javascript">
                                            var convertBar = new progressBar(
                                                1,         //border thickness
                                                '#000000', //border colour
                                                '#ced7e6', //background colour
                                                '#a4acb8', //bar colour
                                                400,       //width of bar (excluding border)
                                                16,        //height of bar (excluding border)
                                                1          //direction of progress: 1 = right, 2 = down, 3 = left, 4 = up
                                            );
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
    <table cellspacing="0" cellpadding="0" border="0" width="97%"
           align="center">
        <tr>
            <td width="100%"><img src="../images/problue/shadow.gif" border="0"
                                  alt="" title=""/></td>
        </tr>
    </table>
    <?php
    return "convertBar";
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function view_convert_complete($boardtype = 'XMB')
{
    $_SESSION = array();
    print_error('Convert Complete', $boardtype . ' Convertion has completed. You should login and change the settings in the <a href="../admin/index.php">Admin Control Panel</a><br />Remove the install directory for additional safety.');
}

/**
 * function() - short description of function
 *
 * TODO: Long description of function
 *
 * @param $varname type,
 *            what it does
 * @return type, what the return does
 *
 */
function view_convert_warncomplete($boardtype = 'XMB')
{
    $_SESSION = array();
    print_error('Convert Complete', $boardtype . ' Convertion has finished, but with warnings. We strongly recommend you attend to the warnings before continuing.');
}

?>