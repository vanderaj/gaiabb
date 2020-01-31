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

define('ROOT', '../');
define('ROOTINC', '../include/');
define('ROOTCLASS', '../class/');
define('ROOTHELPER', '../helper/');

require_once '../header.php';
require_once '../include/admincp.inc.php';
require_once '../helper/formHelper.php';

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['General_Settings']);
btitle($lang['textcp']);
btitle($lang['General_Settings']);

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

function viewPanel()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML;

    $whosonlineon = $whosonlineoff = '';
    formHelper::getSettingOnOffHtml('whosonlinestatus', $whosonlineon, $whosonlineoff);
    $whosonlinetodayon = $whosonlinetodayoff = '';
    formHelper::getSettingOnOffHtml('whosonlinetoday', $whosonlinetodayon, $whosonlinetodayoff);
    $regon = $regoff = '';
    formHelper::getSettingOnOffHtml('regstatus', $regon, $regoff);
    $regonlyon = $regonlyoff = '';
    formHelper::getSettingOnOffHtml('regviewonly', $regonlyon, $regonlyoff);
    $hideon = $hideoff = '';
    formHelper::getSettingOnOffHtml('hideprivate', $hideon, $hideoff);
    $echeckon = $echeckoff = '';
    formHelper::getSettingOnOffHtml('emailcheck', $echeckon, $echeckoff);
    $searchon = $searchoff = '';
    formHelper::getSettingOnOffHtml('searchstatus', $searchon, $searchoff);
    $faqon = $faqoff = '';
    formHelper::getSettingOnOffHtml('faqstatus', $faqon, $faqoff);
    $memliston = $memlistoff = '';
    formHelper::getSettingOnOffHtml('memliststatus', $memliston, $memlistoff);
    $topicon = $topicoff = '';
    formHelper::getSettingOnOffHtml('topicactivity_status', $topicon, $topicoff);
    $statson = $statsoff = '';
    formHelper::getSettingOnOffHtml('stats', $statson, $statsoff);
    $coppaon = $coppaoff = '';
    formHelper::getSettingOnOffHtml('coppa', $coppaon, $coppaoff);
    $sigbbcodeon = $sigbbcodeoff = '';
    formHelper::getSettingOnOffHtml('sigbbcode', $sigbbcodeon, $sigbbcodeoff);
    $reportposton = $reportpostoff = '';
    formHelper::getSettingOnOffHtml('reportpost', $reportposton, $reportpostoff);
    $bbinserton = $bbinsertoff = '';
    formHelper::getSettingOnOffHtml('bbinsert', $bbinserton, $bbinsertoff);
    $smileyinserton = $smileyinsertoff = '';
    formHelper::getSettingOnOffHtml('smileyinsert', $smileyinserton, $smileyinsertoff);
    $doubleeon = $doubleeoff = '';
    formHelper::getSettingOnOffHtml('doublee', $doubleeon, $doubleeoff);
    $editedbyon = $editedbyoff = '';
    formHelper::getSettingOnOffHtml('editedby', $editedbyon, $editedbyoff);
    $dotfolderson = $dotfoldersoff = '';
    formHelper::getSettingOnOffHtml('dotfolders', $dotfolderson, $dotfoldersoff);
    $attachimgposton = $attachimgpostoff = '';
    formHelper::getSettingOnOffHtml('attachimgpost', $attachimgposton, $attachimgpostoff);
    $attachborderon = $attachborderoff = '';
    formHelper::getSettingOnOffHtml('attachborder', $attachborderon, $attachborderoff);
    $allowrankediton = $allowrankeditoff = '';
    formHelper::getSettingOnOffHtml('allowrankedit', $allowrankediton, $allowrankeditoff);
    $whosrobot_on = $whosrobot_off = '';
    formHelper::getSettingOnOffHtml('whosrobot_status', $whosrobot_on, $whosrobot_off);
    $whosrobotname_on = $whosrobotname_off = '';
    formHelper::getSettingOnOffHtml('whosrobotname_status', $whosrobotname_on, $whosrobotname_off);
    $whosguest_on = $whosguest_off = '';
    formHelper::getSettingOnOffHtml('whosguest_status', $whosguest_on, $whosguest_off);
    $pmattachstatuson = $pmattachstatusoff = '';
    formHelper::getSettingOnOffHtml('pmattachstatus', $pmattachstatuson, $pmattachstatusoff);
    $indexstatson = $indexstatsoff = '';
    formHelper::getSettingOnOffHtml('indexstats', $indexstatson, $indexstatsoff);
    $notepadon = $notepadoff = '';
    formHelper::getSettingOnOffHtml('notepadstatus', $notepadon, $notepadoff);
    $pmstatuson = $pmstatusoff = '';
    formHelper::getSettingOnOffHtml('pmstatus', $pmstatuson, $pmstatusoff);
    $viewlocationon = $viewlocationoff = '';
    formHelper::getSettingOnOffHtml('viewlocation', $viewlocationon, $viewlocationoff);
    $contactus_on = $contactus_off = '';
    formHelper::getSettingOnOffHtml('contactus', $contactus_on, $contactus_off);
    $attachiconon = $attachiconoff = '';
    formHelper::getSettingOnOffHtml('attachicon_status', $attachiconon, $attachiconoff);
    $resetsigon = $resetsigoff = '';
    formHelper::getSettingOnOffHtml('resetsig', $resetsigon, $resetsigoff);
    $forumjumpon = $forumjumpoff = '';
    formHelper::getSettingOnOffHtml('forumjump', $forumjumpon, $forumjumpoff);
    $showsubson = $showsubsoff = '';
    formHelper::getSettingOnOffHtml('showsubs', $showsubson, $showsubsoff);
    $viewattachyes = $viewattachno = '';
    formHelper::getSettingYesNoHtml('viewattach', $viewattachyes, $viewattachno);
    $rpgyes = $rpgno = '';
    formHelper::getSettingYesNoHtml('rpg_status', $rpgyes, $rpgno);
    $notifycheck[0] = $notifycheck[1] = $notifycheck[2] = false;
    switch ($CONFIG['notifyonreg']) {
        case 'off':
            $notifycheck[0] = true;
            break;
        case 'pm':
            $notifycheck[1] = true;
            break;
        default:
            $notifycheck[2] = true;
            break;
    }
    $footer_options = explode('-', $CONFIG['footer_options']);
    if (in_array('serverload', $footer_options)) {
        $sel_serverload = true;
    } else {
        $sel_serverload = false;
    }
    if (in_array('queries', $footer_options)) {
        $sel_queries = true;
    } else {
        $sel_queries = false;
    }
    if (in_array('phpsql', $footer_options)) {
        $sel_phpsql = true;
    } else {
        $sel_phpsql = false;
    }
    if (in_array('loadtimes', $footer_options)) {
        $sel_loadtimes = true;
    } else {
        $sel_loadtimes = false;
    }
    $values = array(
        'serverload',
        'queries',
        'phpsql',
        'loadtimes',
    );
    $names = array(
        $lang['Enable_Server_Load'],
        $lang['Enable_Queries'],
        $lang['Enable_PHP_SQL_Calculation'],
        $lang['Enable_Page_loadtimes'],
    );
    $checked = array(
        $sel_serverload,
        $sel_queries,
        $sel_phpsql,
        $sel_loadtimes,
    );
    $CONFIG['usernamenotify'] = stripslashes($CONFIG['usernamenotify']);
    ?>
    <form method="post" action="cp_general.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['admin_main_settings3'] ?></td>
                        </tr>
                        <?php
formHelper::formSelectOnOff($lang['textsearchstatus'], 'searchstatusnew', $searchon, $searchoff);
    formHelper::formSelectOnOff($lang['textfaqstatus'], 'faqstatusnew', $faqon, $faqoff);
    formHelper::formSelectOnOff($lang['topicactivitystatus'], 'topicactivity_statusnew', $topicon, $topicoff);
    formHelper::formSelectOnOff($lang['textstatsstatus'], 'statsnew', $statson, $statsoff);
    formHelper::formSelectOnOff($lang['textmemliststatus'], 'memliststatusnew', $memliston, $memlistoff);
    formHelper::formSelectOnOff($lang['contactusstatus'], 'contactusnew', $contactus_on, $contactus_off);
    formHelper::formSelectOnOff($lang['coppastatus'], 'coppanew', $coppaon, $coppaoff);
    formHelper::formSelectOnOff($lang['reportpoststatus'], 'reportpostnew', $reportposton, $reportpostoff);
    formHelper::formSelectOnOff($lang['allowrankedit'], 'allowrankeditnew', $allowrankediton, $allowrankeditoff);
    formHelper::formSelectOnOff($lang['whosrobotstatus'], 'whosrobot_statusnew', $whosrobot_on, $whosrobot_off);
    formHelper::formSelectOnOff($lang['whosrobotnamestatus'], 'whosrobotname_statusnew', $whosrobotname_on, $whosrobotname_off);
    formHelper::formSelectOnOff($lang['whosgueststatus'], 'whosguest_statusnew', $whosguest_on, $whosguest_off);
    formHelper::formSelectOnOff($lang['pmattachstatus'], 'pmattachstatusnew', $pmattachstatuson, $pmattachstatusoff);
    formHelper::formSelectOnOff($lang['pmstatus'], 'pmstatusnew', $pmstatuson, $pmstatusoff);
    formHelper::formSelectOnOff($lang['indexstats'], 'indexstatsnew', $indexstatson, $indexstatsoff);
    formHelper::formSelectOnOff($lang['notepadstatus'], 'notepadstatusnew', $notepadon, $notepadoff);
    ?>
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['admin_main_settings4'] ?></td>
                        </tr>
                        <?php
formHelper::formSelectOnOff($lang['whosonline_on'], 'whosonlinestatusnew', $whosonlineon, $whosonlineoff);
    formHelper::formSelectOnOff($lang['whosonlinetoday'], 'whosonlinetodaynew', $whosonlinetodayon, $whosonlinetodayoff);
    formHelper::formTextBox($lang['smtotal'], 'smtotalnew', $CONFIG['smtotal'], 5);
    formHelper::formTextBox($lang['smcols'], 'smcolsnew', $CONFIG['smcols'], 5);
    formHelper::formSelectOnOff($lang['dotfolders'], "dotfoldersnew", $dotfolderson, $dotfoldersoff);
    formHelper::formSelectOnOff($lang['editedby'], 'editedbynew', $editedbyon, $editedbyoff);
    formHelper::formSelectOnOff($lang['attachimginpost'], 'attachimgpostnew', $attachimgposton, $attachimgpostoff);
    formHelper::formSelectOnOff($lang['attachborder'], 'attachbordernew', $attachborderon, $attachborderoff);
    formHelper::formSelectOnOff($lang['Attachicon_Status'], 'attachicon_statusnew', $attachiconon, $attachiconoff);
    formHelper::formSelectYesNo($lang['Viewattach_Status'], 'viewattachnew', $viewattachyes, $viewattachno);
    formHelper::formSelectYesNo($lang['rpg_status'], 'rpg_statusnew', $rpgyes, $rpgno);
    formHelper::formSelectOnOff($lang['viewlocation'], 'viewlocationnew', $viewlocationon, $viewlocationoff);
    formHelper::formSelectOnOff($lang['resetsig'], 'resetsignew', $resetsigon, $resetsigoff);
    formHelper::formSelectOnOff($lang['forumjump'], 'forumjumpnew', $forumjumpon, $forumjumpoff);
    formHelper::formSelectOnOff($lang['showsubs'], 'showsubsnew', $showsubson, $showsubsoff);
    ?>
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['admin_main_settings5'] ?></td>
                        </tr>
                        <?php
formHelper::formSelectOnOff($lang['reg_on'], 'regstatusnew', $regon, $regoff);
    formHelper::formSelectList($lang['notifyonreg'], 'notifyonregnew', array(
        $lang['textoff'],
        $lang['viapm'],
        $lang['viaemail'],
    ), array(
        'off',
        'pm',
        'email',
    ), $notifycheck, false);
    formHelper::formTextBox2($lang['notify'], 5, 'usernamenotifynew', 50, $CONFIG['usernamenotify']);
    formHelper::formSelectOnOff($lang['textreggedonly'], 'regviewonlynew', $regonlyon, $regonlyoff);
    formHelper::formSelectOnOff($lang['texthidepriv'], 'hideprivatenew', $hideon, $hideoff);
    formHelper::formSelectOnOff($lang['emailverify'], 'emailchecknew', $echeckon, $echeckoff);
    formHelper::formTextBox($lang['textflood'], 'floodctrlnew', $CONFIG['floodctrl'], 3);
    formHelper::formTextBox($lang['pmquota'], 'pmquotanew', $CONFIG['pmquota'], 3);
    formHelper::formTextBox($lang['login_max_setting'], 'loginattemptsnew', $CONFIG['login_max_attempts'], 2);
    formHelper::formTextBox($lang['inactiveusers'], 'inactiveusersnew', $CONFIG['inactiveusers'], 3);
    formHelper::formSelectOnOff($lang['doublee'], 'doubleenew', $doubleeon, $doubleeoff);
    ?>
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['admin_main_settings6'] ?></td>
                        </tr>
                        <?php
formHelper::formTextBox($lang['texthottopic'], 'hottopicnew', $CONFIG['hottopic'], 3);
    formHelper::formSelectOnOff($lang['bbinsert'], 'bbinsertnew', $bbinserton, $bbinsertoff);
    formHelper::formSelectOnOff($lang['smileyinsert'], 'smileyinsertnew', $smileyinserton, $smileyinsertoff);
    formHelper::formSelectList($lang['footer_options'], 'new_footer_options', $names, $values, $checked);
    formHelper::formSelectOnOff($lang['sigbbcode'], 'sigbbcodenew', $sigbbcodeon, $sigbbcodeoff);
    ?>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input class="submit" type="submit"
                                                   name="generalsubmit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php echo $shadow2 ?>
    </form>
    </td>
    </tr>
    </table>
    <?php
}

function doPanel()
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;

    $oToken->assert_token();

    $searchstatusnew = formOnOff('searchstatusnew');
    $faqstatusnew = formOnOff('faqstatusnew');
    $topicactivity_statusnew = formOnOff('topicactivity_statusnew');
    $statsnew = formOnOff('statsnew');
    $memliststatusnew = formOnOff('memliststatusnew');
    $contactusnew = formOnOff('contactusnew');
    $coppanew = formOnOff('coppanew');
    $reportpostnew = formOnOff('reportpostnew');
    $allowrankeditnew = formOnOff('allowrankeditnew');
    $whosrobot_statusnew = formOnOff('whosrobot_statusnew');
    $whosrobotname_statusnew = formOnOff('whosrobotname_statusnew');
    $whosguest_statusnew = formOnOff('whosguest_statusnew');
    $pmattachstatusnew = formOnOff('pmattachstatusnew');
    $pmstatusnew = formOnOff('pmstatusnew');
    $indexstatsnew = formOnOff('indexstatsnew');
    $notepadstatusnew = formOnOff('notepadstatusnew');
    $whosonlinestatusnew = formOnOff('whosonlinestatusnew');
    $whosonlinetodaynew = formOnOff('whosonlinetodaynew');
    $smtotalnew = formInt('smtotalnew');
    $smcolsnew = formInt('smcolsnew');
    $dotfoldersnew = formOnOff('dotfoldersnew');
    $editedbynew = formOnOff('editedbynew');
    $attachimgpostnew = formOnOff('attachimgpostnew');
    $attachbordernew = formOnOff('attachbordernew');
    $attachicon_statusnew = formOnOff('attachicon_statusnew');
    $viewattachnew = formYesNo('viewattachnew');
    $rpg_statusnew = formYesNo('rpg_statusnew');
    $viewlocationnew = formOnOff('viewlocationnew');
    $resetsignew = formOnOff('resetsignew');
    $forumjumpnew = formOnOff('forumjumpnew');
    $showsubsnew = formOnOff('showsubsnew');
    $regstatusnew = formOnOff('regstatusnew');
    $notifyonregnew = formVar('notifyonregnew');
    $notifyonregnew = ($notifyonregnew == 'off') ? 'off' : ($notifyonregnew == 'pm' ? 'pm' : 'email');
    $usernamenotifynew = $db->escape(formVar('usernamenotifynew'));
    $regviewonlynew = formOnOff('regviewonlynew');
    $hideprivatenew = formOnOff('hideprivatenew');
    $emailchecknew = formOnOff('emailchecknew');
    $floodctrlnew = formInt('floodctrlnew');
    $pmquotanew = formInt('pmquotanew');
    $loginattemptsnew = formInt('loginattemptsnew');
    $doubleenew = formOnOff('doubleenew');
    $hottopicnew = formInt('hottopicnew');
    $bbinsertnew = formOnOff('bbinsertnew');
    $smileyinsertnew = formOnOff('smileyinsertnew');
    $inactiveusersnew = formInt('inactiveusersnew');
    $footer_options = '';
    $new_footer_options = formArray('new_footer_options');
    if (!empty($new_footer_options)) {
        $footer_options = implode('-', $new_footer_options);
    }
    $sigbbcodenew = formOnOff('sigbbcodenew');

    $config_array = array(
        'hottopic' => $hottopicnew,
        'whosonlinestatus' => $whosonlinestatusnew,
        'regstatus' => $regstatusnew,
        'regviewonly' => $regviewonlynew,
        'floodctrl' => $floodctrlnew,
        'hideprivate' => $hideprivatenew,
        'emailcheck' => $emailchecknew,
        'searchstatus' => $searchstatusnew,
        'faqstatus' => $faqstatusnew,
        'memliststatus' => $memliststatusnew,
        'pmquota' => $pmquotanew,
        'coppa' => $coppanew,
        'sigbbcode' => $sigbbcodenew,
        'reportpost' => $reportpostnew,
        'bbinsert' => $bbinsertnew,
        'smileyinsert' => $smileyinsertnew,
        'doublee' => $doubleenew,
        'smtotal' => $smtotalnew,
        'smcols' => $smcolsnew,
        'editedby' => $editedbynew,
        'dotfolders' => $dotfoldersnew,
        'attachimgpost' => $attachimgpostnew,
        'attachborder' => $attachbordernew,
        'topicactivity_status' => $topicactivity_statusnew,
        'stats' => $statsnew,
        'footer_options' => $footer_options,
        'allowrankedit' => $allowrankeditnew,
        'notifyonreg' => $notifyonregnew,
        'whosrobot_status' => $whosrobot_statusnew,
        'whosrobotname_status' => $whosrobotname_statusnew,
        'whosguest_status' => $whosguest_statusnew,
        'pmattachstatus' => $pmattachstatusnew,
        'indexstats' => $indexstatsnew,
        'viewattach' => $viewattachnew,
        'notepadstatus' => $notepadstatusnew,
        'pmstatus' => $pmstatusnew,
        'usernamenotify' => $usernamenotifynew,
        'rpg_status' => $rpg_statusnew,
        'viewlocation' => $viewlocationnew,
        'contactus' => $contactusnew,
        'mod_status' => 'off', // Disabled as of 20100411
        'attachicon_status' => $attachicon_statusnew,
        'whosonlinetoday' => $whosonlinetodaynew,
        'resetsig' => $resetsignew,
        'forumjump' => $forumjumpnew,
        'showsubs' => $showsubsnew,
        'login_max_attempts' => $loginattemptsnew,
        'inactiveusers' => $inactiveusersnew,
    );

    // execute query
    foreach ($config_array as $key => $value) {
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$value' WHERE config_name = '$key' LIMIT 1");
    }

    cp_message($lang['textsettingsupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('generalsubmit')) {
    viewPanel();
}

if (onSubmit('generalsubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>