<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2019 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

function expireAdminConfigCache()
{
    global $config_cache;

    $config_cache->expire('settings');
    $config_cache->expire('theme');
    $config_cache->expire('whosonline');
    $config_cache->expire('forumjump');
}

expireAdminConfigCache();

function displayAdminPanel()
{
    global $THEME, $CONFIG, $lang, $shadow2, $oToken;
    ?>
    <script language="JavaScript" type="text/javascript"
            src="../js/admin.js"></script>
    <table cellspacing="0" cellpadding="1" border="0"
           width="<?php echo $THEME['tablewidth'] ?>" align="center">
        <tr>
            <td nowrap="nowrap" width="180px" valign="top" class="tablerow">
                <table cellspacing="0" cellpadding="0" border="0" style="width: 99%;">
                    <tr>
                        <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                            <table cellspacing="<?php echo $THEME['borderwidth'] ?>" border="0"
                                   cellpadding="<?php echo $THEME['tablespace'] ?>"
                                   style="width: 100%;">
                                <tr class="category">
                                    <td style="text-align: center;"><a
                                                href="javascript:viewset('<?php echo $lang['admin_gsettings'] ?>')"><strong><font
                                                        color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['admin_gsettings'] ?></font></strong></a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="<?php echo $lang['admin_gsettings'] ?>">
                                    <td>
                                        <ul>
                                            <li><a href="cp_board.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['admin_main_settings1'] ?></a></li>
                                            <li><a href="cp_general.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['General_Settings'] ?></a></li>
                                            <li><a href="cp_default.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['admin_main_settings2'] ?></a></li>
                                            <li><a href="cp_avatars.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['Avatar_Settings'] ?></a></li>
                                            <li><a href="cp_photos.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['photo_main_settings'] ?></a></li>
                                            <li><a href="cp_smtp.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['Smtp_Settings'] ?></a></li>
                                            <li><a href="cp_captcha.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['captcha_settings'] ?></a></li>
                                            <li><a href="cp_dateformats.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['Date_Format_Settings'] ?></a></li>
                                            <li><a href="cp_robots.php?action=robots"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['Robot_Settings'] ?></a></li>
                                            <li><a href="cp_pluglinks.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['pluglinkadmin'] ?></a></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="category">
                                    <td style="text-align: center;"><a
                                                href="javascript:viewset('<?php echo $lang['textforums'] ?>')"><strong><font
                                                        color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['textforums'] ?></font></strong></a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="<?php echo $lang['textforums'] ?>">
                                    <td>
                                        <ul>
                                            <li><a href="cp_forums.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textforums'] ?></a></li>
                                            <li><a href="cp_moderators.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textmods'] ?></a></li>
                                            <li><a href="cp_prune.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textprune'] ?></a></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="category">
                                    <td style="text-align: center;"><a
                                                href="javascript:viewset('<?php echo $lang['textmembers'] ?>')"><strong><font
                                                        color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['textmembers'] ?></font></strong></a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="<?php echo $lang['textmembers'] ?>">
                                    <td>
                                        <ul>
                                            <li>
                                                <a href="cp_members.php?action=members"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textmembers'] ?></a></li>
                                            <li><a href="cp_ranks.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textuserranks'] ?></a></li>
                                            <li><a href="cp_reguser.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['reguser'] ?></a></li>
                                            <li><a href="cp_rename.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['admin_rename_txt'] ?></a></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="category">
                                    <td style="text-align: center;"><a
                                                href="javascript:viewset('<?php echo $lang['admin_mtools'] ?>')"><strong><font
                                                        color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['admin_mtools'] ?></font></strong></a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="<?php echo $lang['admin_mtools']; ?>">
                                    <td>
                                        <ul>
                                            <li><a href="cp_ipban.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textipban'] ?></a></li>
                                            <li><a href="cp_inactivemembers.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['inactive'] ?></a></li>
                                            <li><a href="cp_fixftotals.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textfixposts'] ?></a></li>
                                            <li><a href="cp_fixlastposts.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textfixlastposts'] ?></a></li>
                                            <li><a href="cp_fixmposts.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textfixmemposts'] ?></a></li>
                                            <li><a href="cp_fixmthreads.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textfixmemthreads'] ?></a></li>
                                            <li><a href="cp_fixttotals.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textfixthread'] ?></a></li>
                                            <li><a href="cp_closethreads.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['inactivethreads'] ?></a></li>
                                            <li><a href="cp_forcelogout.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textforcelogout'] ?></a></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="category">
                                    <td style="text-align: center;"><a
                                                href="javascript:viewset('<?php echo $lang['altadmintools'] ?>')"><strong><font
                                                        color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['altadmintools'] ?></font></strong></a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="<?php echo $lang['altadmintools'] ?>">
                                    <td>
                                        <ul>
                                            <li><a href="cp_news.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['indexnewscp'] ?></a></li>
                                            <li><a href="cp_faq.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textfaq'] ?> <?php echo $lang['faq_N'] ?></a>
                                            </li>
                                            <li><a href="cp_rules.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textbbrules'] ?></a></li>
                                            <li><a href="cp_restrictions.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['cprestricted'] ?></a></li>
                                            <li><a href="cp_censors.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textcensors'] ?></a></li>
                                            <li>
                                                <a href="cp_attachments.php?action=attachments"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textattachman'] ?></a></li>
                                            <li><a href="cp_newsletter.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textnewsletter'] ?></a></li>
                                            <li><a href="cp_search.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['cpsearch'] ?></a></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="category">
                                    <td style="text-align: center;"><a
                                                href="javascript:viewset('<?php echo $lang['look_feel'] ?>')"><strong><font
                                                        color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['look_feel'] ?></font></strong></a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="<?php echo $lang['look_feel'] ?>">
                                    <td>
                                        <ul>
                                            <li><a href="cp_smilies.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['smilies'] ?></a></li>
                                            <li><a href="cp_posticons.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['posticons'] ?></a></li>
                                            <li>
                                                <a href="cp_templates.php?action=templates"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['templates'] ?></a></li>
                                            <li><a href="cp_themes.php?action=themes"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['themes'] ?></a></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="category">
                                    <td style="text-align: center;"><a
                                                href="javascript:viewset('<?php echo $lang['logs'] ?>')"><strong><font
                                                        color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['logs'] ?></font></strong></a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="<?php echo $lang['logs'] ?>">
                                    <td>
                                        <ul>
                                            <li><a href="cp_notepad.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['Admin_Notes'] ?></a></li>
                                            <li><a href="cp_logs.php?action=cplog"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textcplogs'] ?></a></li>
                                            <li><a href="cp_logs.php?action=modlog"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textmodlogs'] ?></a></li>
                                            <li><a href="cp_emptyadminlogs.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['emptyadminlogs'] ?></a></li>
                                            <li><a href="cp_emptymodlogs.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['emptymodlogs'] ?></a></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="category">
                                    <td style="text-align: center;"><a
                                                href="javascript:viewset('<?php echo $lang['admin_ctools'] ?>')"><strong><font
                                                        color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['admin_ctools'] ?></font></strong></a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="<?php echo $lang['admin_ctools'] ?>">
                                    <td>
                                        <ul>
                                            <li><a href="cp_fixthreads.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textfixothreads'] ?></a></li>
                                            <li><a href="cp_fixattachments.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textfixoattachments'] ?></a></li>
                                            <li><a href="cp_fixorphanedposts.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['tool_fixtids'] ?></a></li>
                                            <li><a href="cp_fixfavorites.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['tool_fixfavtids'] ?></a></li>
                                            <li><a href="cp_fixsubscriptions.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['tool_fixsubtids'] ?></a></li>
                                            <li><a href="cp_fixsmilies.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['fixsmilies'] ?></a></li>
                                            <li><a href="cp_pmdump.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['pmdump'] ?></a></li>
                                            <li><a href="cp_deleteoldpms.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['Delete_old_pms'] ?></a></li>
                                            <li><a href="cp_onlinedump.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['cpwodump'] ?></a></li>
                                            <li><a href="cp_updatemoods.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['textfixmoods'] ?></a></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="category">
                                    <td style="text-align: center;"><a
                                                href="javascript:viewset('<?php echo $lang['mysql_tools'] ?>')"><strong><font
                                                        color="<?php echo $THEME['cattext'] ?>"><?php echo $lang['mysql_tools'] ?></font></strong></a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="<?php echo $lang['mysql_tools'] ?>">
                                    <td>
                                        <ul>
                                            <li><a href="cp_rawsql.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['raw_mysql'] ?></a></li>
                                            <li><a href="cp_analyzetables.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['analyze'] ?></a></li>
                                            <li><a href="cp_checktables.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['checktables'] ?></a></li>
                                            <li><a href="cp_optimizetables.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['optimize'] ?></a></li>
                                            <li><a href="cp_repairtables.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['repair'] ?></a></li>
                                            <li><a href="cp_dbinfo.php"><?php echo $THEME['navsymbol'] ?>
                                                    &nbsp;<?php echo $lang['dbinfo'] ?></a></li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <?php echo $shadow2 ?>
                <script language="JavaScript" type="text/javascript"
                        src="../js/admin_menu.js"></script>
            </td>
            <td valign="top">
    <?php
}

?>