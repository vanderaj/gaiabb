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

define('DEBUG_REG', true);
define('ROOT', '../');
define('ROOTINC', '../include/');
define('ROOTCLASS', '../class/');

require_once(ROOT.'header.php');
require_once(ROOTINC.'admincp.inc.php');
require_once(ROOTINC.'settings.inc.php');

loadtpl(
'cp_header',
'cp_footer',
'cp_message',
'cp_error'
);

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['General_Settings']);
btitle($lang['textcp']);
btitle($lang['General_Settings']);

eval('$css = "'.template('css').'";');
eval('echo "'.template('cp_header').'";');

if (!X_ADMIN)
{
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
    settingHTML('whosonlinestatus', $whosonlineon, $whosonlineoff);
    $whosonlinetodayon = $whosonlinetodayoff = '';
    settingHTML('whosonlinetoday', $whosonlinetodayon, $whosonlinetodayoff);
    $regon = $regoff = '';
    settingHTML('regstatus', $regon, $regoff);
    $regonlyon = $regonlyoff = '';
    settingHTML('regviewonly', $regonlyon, $regonlyoff);
    $hideon = $hideoff = '';
    settingHTML('hideprivate', $hideon, $hideoff);
    $echeckon = $echeckoff = '';
    settingHTML('emailcheck', $echeckon, $echeckoff);
    $searchon = $searchoff = '';
    settingHTML('searchstatus', $searchon, $searchoff);
    $faqon = $faqoff = '';
    settingHTML('faqstatus', $faqon, $faqoff);
    $memliston = $memlistoff = '';
    settingHTML('memliststatus', $memliston, $memlistoff);
    $topicon = $topicoff = '';
    settingHTML('topicactivity_status', $topicon, $topicoff);
    $statson = $statsoff = '';
    settingHTML('stats', $statson, $statsoff);
    $coppaon = $coppaoff = '';
    settingHTML('coppa', $coppaon, $coppaoff);
    $sigbbcodeon = $sigbbcodeoff = '';
    settingHTML('sigbbcode', $sigbbcodeon, $sigbbcodeoff);
    $reportposton = $reportpostoff = '';
    settingHTML('reportpost', $reportposton, $reportpostoff);
    $bbinserton = $bbinsertoff = '';
    settingHTML('bbinsert', $bbinserton, $bbinsertoff);
    $smileyinserton = $smileyinsertoff = '';
    settingHTML('smileyinsert', $smileyinserton, $smileyinsertoff);
    $doubleeon = $doubleeoff = '';
    settingHTML('doublee', $doubleeon, $doubleeoff);
    $editedbyon = $editedbyoff = '';
    settingHTML('editedby', $editedbyon, $editedbyoff);
    $dotfolderson = $dotfoldersoff = '';
    settingHTML('dotfolders', $dotfolderson, $dotfoldersoff);
    $attachimgposton = $attachimgpostoff = '';
    settingHTML('attachimgpost', $attachimgposton, $attachimgpostoff);
    $attachborderon = $attachborderoff = '';
    settingHTML('attachborder', $attachborderon, $attachborderoff);
    $allowrankediton = $allowrankeditoff = '';
    settingHTML('allowrankedit', $allowrankediton, $allowrankeditoff);
    $whosrobot_on = $whosrobot_off = '';
    settingHTML('whosrobot_status', $whosrobot_on, $whosrobot_off);
    $whosrobotname_on = $whosrobotname_off = '';
    settingHTML('whosrobotname_status', $whosrobotname_on, $whosrobotname_off);
    $whosguest_on = $whosguest_off = '';
    settingHTML('whosguest_status', $whosguest_on, $whosguest_off);
    $pmattachstatuson = $pmattachstatusoff = '';
    settingHTML('pmattachstatus', $pmattachstatuson, $pmattachstatusoff);
    $indexstatson = $indexstatsoff = '';
    settingHTML('indexstats', $indexstatson, $indexstatsoff);
    $notepadon = $notepadoff = '';
    settingHTML('notepadstatus', $notepadon, $notepadoff);
    $pmstatuson = $pmstatusoff = '';
    settingHTML('pmstatus', $pmstatuson, $pmstatusoff);
    $viewlocationon = $viewlocationoff = '';
    settingHTML('viewlocation', $viewlocationon, $viewlocationoff);
    $contactus_on = $contactus_off = '';
    settingHTML('contactus', $contactus_on, $contactus_off);
    $modstatuson = $modstatusoff = '';
    settingHTML('mod_status', $modstatuson, $modstatusoff);
    $attachiconon = $attachiconoff = '';
    settingHTML('attachicon_status', $attachiconon, $attachiconoff);
    $resetsigon = $resetsigoff = '';
    settingHTML('resetsig', $resetsigon, $resetsigoff);
    $forumjumpon = $forumjumpoff = '';
    settingHTML('forumjump', $forumjumpon, $forumjumpoff);
    $showsubson = $showsubsoff = '';
    settingHTML('showsubs', $showsubson, $showsubsoff);
    $viewattachyes = $viewattachno = '';
    settingYesNo('viewattach', $viewattachyes, $viewattachno);
    $rpgyes = $rpgno = '';
    settingYesNo('rpg_status', $rpgyes, $rpgno);
    $notifycheck[0] = $notifycheck[1] = $notifycheck[2] = false;
    switch ($CONFIG['notifyonreg'])
    {
        case 'off' :
            $notifycheck[0] = true;
            break;
        case 'pm' :
            $notifycheck[1] = true;
            break;
        default :
            $notifycheck[2] = true;
            break;
    }
    $footer_options = explode('-', $CONFIG['footer_options']);
    if (in_array('serverload', $footer_options))
    {
        $sel_serverload = true;
    } else
    {
        $sel_serverload = false;
    }
    if (in_array('queries', $footer_options))
    {
        $sel_queries = true;
    } else
    {
        $sel_queries = false;
    }
    if (in_array('phpsql', $footer_options))
    {
        $sel_phpsql = true;
    } else
    {
        $sel_phpsql = false;
    }
    if (in_array('loadtimes', $footer_options))
    {
        $sel_loadtimes = true;
    } else
    {
        $sel_loadtimes = false;
    }
    $values = array ('serverload', 'queries', 'phpsql', 'loadtimes');
    $names = array ($lang['Enable_Server_Load'], $lang['Enable_Queries'], $lang['Enable_PHP_SQL_Calculation'], $lang['Enable_Page_loadtimes']);
    $checked = array ($sel_serverload, $sel_queries, $sel_phpsql, $sel_loadtimes);
    $CONFIG['usernamenotify'] = stripslashes($CONFIG['usernamenotify']);
    ?>
    <form method="post" action="cp_general.php">
    <input type="hidden" name="token" value="<?php echo $oToken->get_new_token()?>" />
    <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
    <tr>
    <td bgcolor="<?php echo $THEME['bordercolor']?>">
    <table border="0px" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
    <tr class="category">
    <td class="title" colspan="2"><?php echo $lang['admin_main_settings3']?></td>
    </tr>
    <?php
    printsetting1($lang['textsearchstatus'], 'searchstatusnew', $searchon, $searchoff);
    printsetting1($lang['textfaqstatus'], 'faqstatusnew', $faqon, $faqoff);
    printsetting1($lang['topicactivitystatus'], 'topicactivity_statusnew', $topicon, $topicoff);
    printsetting1($lang['textstatsstatus'], 'statsnew', $statson, $statsoff);
    printsetting1($lang['textmemliststatus'], 'memliststatusnew', $memliston, $memlistoff);
    printsetting1($lang['contactusstatus'], 'contactusnew', $contactus_on, $contactus_off);
    printsetting1($lang['mod_status'], 'mod_statusnew', $modstatuson, $modstatusoff);
    printsetting1($lang['coppastatus'], 'coppanew', $coppaon, $coppaoff);
    printsetting1($lang['reportpoststatus'], 'reportpostnew', $reportposton, $reportpostoff);
    printsetting1($lang['allowrankedit'], 'allowrankeditnew', $allowrankediton, $allowrankeditoff);
    printsetting1($lang['whosrobotstatus'], 'whosrobot_statusnew', $whosrobot_on, $whosrobot_off);
    printsetting1($lang['whosrobotnamestatus'], 'whosrobotname_statusnew', $whosrobotname_on, $whosrobotname_off);
    printsetting1($lang['whosgueststatus'], 'whosguest_statusnew', $whosguest_on, $whosguest_off);
    printsetting1($lang['pmattachstatus'], 'pmattachstatusnew', $pmattachstatuson, $pmattachstatusoff);
    printsetting1($lang['pmstatus'], 'pmstatusnew', $pmstatuson, $pmstatusoff);
    printsetting1($lang['indexstats'], 'indexstatsnew', $indexstatson, $indexstatsoff);
    printsetting1($lang['notepadstatus'], 'notepadstatusnew', $notepadon, $notepadoff);
    ?>
    <tr class="category">
    <td class="title" colspan="2"><?php echo $lang['admin_main_settings4']?></td>
    </tr>
    <?php
    printsetting1($lang['whosonline_on'], 'whosonlinestatusnew', $whosonlineon, $whosonlineoff);
    printsetting1($lang['whosonlinetoday'], 'whosonlinetodaynew', $whosonlinetodayon, $whosonlinetodayoff);
    printsetting2($lang['smtotal'], 'smtotalnew', $CONFIG['smtotal'], 5);
    printsetting2($lang['smcols'], 'smcolsnew', $CONFIG['smcols'], 5);
    printsetting1($lang['dotfolders'], "dotfoldersnew", $dotfolderson, $dotfoldersoff);
    printsetting1($lang['editedby'], 'editedbynew', $editedbyon, $editedbyoff);
    printsetting1($lang['attachimginpost'], 'attachimgpostnew', $attachimgposton, $attachimgpostoff);
    printsetting1($lang['attachborder'], 'attachbordernew', $attachborderon, $attachborderoff);
    printsetting1($lang['Attachicon_Status'], 'attachicon_statusnew', $attachiconon, $attachiconoff);
    printsetting5($lang['Viewattach_Status'], 'viewattachnew', $viewattachyes, $viewattachno);
    printsetting5($lang['rpg_status'], 'rpg_statusnew', $rpgyes, $rpgno);
    printsetting1($lang['viewlocation'], 'viewlocationnew', $viewlocationon, $viewlocationoff);
    printsetting1($lang['resetsig'], 'resetsignew', $resetsigon, $resetsigoff);
    printsetting1($lang['forumjump'], 'forumjumpnew', $forumjumpon, $forumjumpoff);
    printsetting1($lang['showsubs'], 'showsubsnew', $showsubson, $showsubsoff);
    ?>
    <tr class="category">
    <td class="title" colspan="2"><?php echo $lang['admin_main_settings5']?></td>
    </tr>
    <?php
    printsetting1($lang['reg_on'], 'regstatusnew', $regon, $regoff);
    printsetting3($lang['notifyonreg'], 'notifyonregnew', array ($lang['textoff'], $lang['viapm'], $lang['viaemail']), array ('off', 'pm', 'email'), $notifycheck, false);
    printsetting4($lang['notify'], 5, 'usernamenotifynew', 50, $CONFIG['usernamenotify']);
    printsetting1($lang['textreggedonly'], 'regviewonlynew', $regonlyon, $regonlyoff);
    printsetting1($lang['texthidepriv'], 'hideprivatenew', $hideon, $hideoff);
    printsetting1($lang['emailverify'], 'emailchecknew', $echeckon, $echeckoff);
    printsetting2($lang['textflood'], 'floodctrlnew', $CONFIG['floodctrl'], 3);
    printsetting2($lang['pmquota'], 'pmquotanew', $CONFIG['pmquota'], 3);
    printsetting2($lang['login_max_setting'], 'loginattemptsnew', $CONFIG['login_max_attempts'], 2);
    printsetting2($lang['inactiveusers'], 'inactiveusersnew', $CONFIG['inactiveusers'], 3);
    printsetting1($lang['doublee'], 'doubleenew', $doubleeon, $doubleeoff);
    ?>
    <tr class="category">
    <td class="title" colspan="2"><?php echo $lang['admin_main_settings6']?></td>
    </tr>
    <?php
    printsetting2($lang['texthottopic'], 'hottopicnew', $CONFIG['hottopic'], 3);
    printsetting1($lang['bbinsert'], 'bbinsertnew', $bbinserton, $bbinsertoff);
    printsetting1($lang['smileyinsert'], 'smileyinsertnew', $smileyinserton, $smileyinsertoff);
    printsetting3($lang['footer_options'], 'new_footer_options', $names, $values, $checked);
    printsetting1($lang['sigbbcode'], 'sigbbcodenew', $sigbbcodeon, $sigbbcodeoff);
    ?>
    <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2']?>">
    <td colspan="2"><input class="submit" type="submit" name="generalsubmit" value="<?php echo $lang['textsubmitchanges']?>" /></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    <?php echo $shadow2?>
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
    $mod_statusnew = formOnOff('mod_statusnew');
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
    if (!empty($new_footer_options))
    {
        $footer_options = implode('-', $new_footer_options);
    }
    $sigbbcodenew = formOnOff('sigbbcodenew');

    $config_array = array (
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
        'mod_status' => $mod_statusnew,
        'attachicon_status' => $attachicon_statusnew,
        'whosonlinetoday' => $whosonlinetodaynew,
        'resetsig' => $resetsignew,
        'forumjump' => $forumjumpnew,
        'showsubs' => $showsubsnew,
        'login_max_attempts' => $loginattemptsnew,
        'inactiveusers' => $inactiveusersnew
    );

    // execute query
    foreach ($config_array as $key => $value)
    {
        $db->query("UPDATE ".X_PREFIX."settings SET config_value = '$value' WHERE config_name = '$key' LIMIT 1");
    }

    cp_message($lang['textsettingsupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('generalsubmit'))
{
    viewPanel();
}

if (onSubmit('generalsubmit'))
{
        doPanel();
}

loadtime();
eval('echo "'.template('cp_footer').'";');
?>