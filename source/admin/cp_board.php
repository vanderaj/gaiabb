<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2021 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2021 The XMB Development Team
 * https://forums.xmbforum2.com/
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
if (!defined('ROOT')) {
    define('ROOT', '../');
}

require_once ROOT . 'header.php';
require_once ROOT . 'include/admincp.inc.php';
require_once ROOT . 'helper/formHelper.php';

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['admin_main_settings1']);
btitle($lang['textcp']);
btitle($lang['admin_main_settings1']);

eval('$css = "' . template('css') . '";');
eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    adminaudit($self['username'], '', 0, 0, 'Authorization failed');
    error($lang['adminonly'], false);
}
adminaudit($self['username'], '', 0, 0);

smcwcache();

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
function viewPanel()
{
    global $oToken, $CONFIG, $THEME, $lang, $shadow2;

    $onselect = $offselect = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('bbstatus', $onselect, $offselect);
    $metatag_statuson = $metatag_statusoff = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('metatag_status', $metatag_statuson, $metatag_statusoff);
    $pmwelcomestatuson = $pmwelcomestatusoff = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('pmwelcomestatus', $pmwelcomestatuson, $pmwelcomestatusoff);
    $show_full_on = $show_full_off = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('show_full_info', $show_full_on, $show_full_off);
    $comment_on = $comment_off = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('comment', $comment_on, $comment_off);
    $ipreg_on = $ipreg_off = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('ipreg', $ipreg_on, $ipreg_off);
    $ipcheck_on = $ipcheck_off = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('ipcheck', $ipcheck_on, $ipcheck_off);
    $specq_on = $specq_off = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('specq', $specq_on, $specq_off);
    $predf_on = $predf_off = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('predformat', $predf_on, $predf_off);
    $whosoptomized_on = $whosoptomized_off = '';
    GaiaBB\FormHelper::getSettingOnOffHtml('whosoptomized', $whosoptomized_on, $whosoptomized_off);

    $max_attach_sizenew = intval($CONFIG['max_attach_size']) / 1024;
    if ($max_attach_sizenew < 10 || $max_attach_sizenew > 1024) {
        $max_attach_sizenew = 100;
    }

    $CONFIG['sitename'] = stripslashes($CONFIG['sitename']);
    $CONFIG['bbname'] = stripslashes($CONFIG['bbname']);
    $CONFIG['siteurl'] = stripslashes($CONFIG['siteurl']);
    $CONFIG['boardurl'] = stripslashes($CONFIG['boardurl']);
    $CONFIG['adminemail'] = stripslashes($CONFIG['adminemail']);
    $CONFIG['bboffreason'] = stripslashes($CONFIG['bboffreason']);
    $CONFIG['copyright'] = stripslashes($CONFIG['copyright']);
    $CONFIG['metatag_keywords'] = stripslashes($CONFIG['metatag_keywords']);
    $CONFIG['metatag_description'] = stripslashes($CONFIG['metatag_description']);
    $CONFIG['pmwelcomesubject'] = stripslashes($CONFIG['pmwelcomesubject']);
    $CONFIG['pmwelcomemessage'] = stripslashes($CONFIG['pmwelcomemessage']);
    $CONFIG['pmwelcomefrom'] = stripslashes($CONFIG['pmwelcomefrom']);
    ?>
    <form method="post" action="cp_board.php">
        <input type="hidden" name="csrf_token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['admin_main_settings1'] ?></td>
                        </tr>
                        <?php
                        GaiaBB\FormHelper::formTextBox($lang['textsitename'], 'sitenamenew', $CONFIG['sitename'], 50);
                        GaiaBB\FormHelper::formTextBox($lang['bbname'], 'bbnamenew', $CONFIG['bbname'], 50);
                        GaiaBB\FormHelper::formTextBox($lang['textsiteurl'], 'siteurlnew', $CONFIG['siteurl'], 50);
                        GaiaBB\FormHelper::formTextBox($lang['textboardurl'], 'boardurlnew', $CONFIG['boardurl'], 50);
                        GaiaBB\FormHelper::formTextBox($lang['adminemail'], 'adminemailnew', $CONFIG['adminemail'], 50);
                        GaiaBB\FormHelper::formTextBox($lang['copyrightnotice'], 'copyrightnew', $CONFIG['copyright'], 50);
                        GaiaBB\FormHelper::formSelectOnOff($lang['metatag_status'], 'metatag_statusnew', $metatag_statuson, $metatag_statusoff);
                        GaiaBB\FormHelper::formTextBox($lang['metatag_keywords'], 'metatag_keywordsnew', $CONFIG['metatag_keywords'], 50);
                        GaiaBB\FormHelper::formTextBox($lang['metatag_description'], 'metatag_descriptionnew', $CONFIG['metatag_description'], 50);
                        GaiaBB\FormHelper::formSelectOnOff($lang['textbstatus'], 'bbstatusnew', $onselect, $offselect);
                        GaiaBB\FormHelper::formTextBox2($lang['textbboffreason'], 5, 'bboffreasonnew', 50, $CONFIG['bboffreason']);
                        GaiaBB\FormHelper::formSelectOnOff($lang['set_show_full_info'], 'show_full_infonew', $show_full_on, $show_full_off);
                        GaiaBB\FormHelper::formSelectOnOff($lang['set_comment'], 'commentnew', $comment_on, $comment_off);
                        GaiaBB\FormHelper::formSelectOnOff($lang['set_ipreg'], 'ipregnew', $ipreg_on, $ipreg_off);
                        GaiaBB\FormHelper::formSelectOnOff($lang['set_ipcheck'], 'ipchecknew', $ipcheck_on, $ipcheck_off);
                        GaiaBB\FormHelper::formSelectOnOff($lang['set_specq'], 'specqnew', $specq_on, $specq_off);
                        GaiaBB\FormHelper::formSelectOnOff($lang['set_predformat'], 'predformatnew', $predf_on, $predf_off);
                        GaiaBB\FormHelper::formSelectOnOff($lang['whosoptomized'], 'whosoptomizednew', $whosoptomized_on, $whosoptomized_off);
                        GaiaBB\FormHelper::formTextBox($lang['set_max_attach_size'], 'max_attach_sizenew', $max_attach_sizenew, 10);
                        GaiaBB\FormHelper::formSelectOnOff($lang['pmwelcomestatus'], 'pmwelcomestatusnew', $pmwelcomestatuson, $pmwelcomestatusoff);
                        GaiaBB\FormHelper::formTextBox($lang['pmwelcomefrom'], 'pmwelcomefromnew', $CONFIG['pmwelcomefrom'], 32);
                        GaiaBB\FormHelper::formTextBox($lang['pmwelcomesubject'], 'pmwelcomesubjectnew', $CONFIG['pmwelcomesubject'], 32);
                        GaiaBB\FormHelper::formTextBox2($lang['pmwelcomemessage'], 5, 'pmwelcomemessagenew', 50, $CONFIG['pmwelcomemessage']);
                        ?>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input class="submit" type="submit"
                                                   name="boardsubmit"
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
function doPanel()
{
    global $oToken, $CONFIG, $THEME, $lang, $shadow2, $db;

    $oToken->assertToken();

    $bbstatusnew = formOnOff('bbstatusnew');
    $metatag_statusnew = formOnOff('metatag_statusnew');
    $pmwelcomestatusnew = formOnOff('pmwelcomestatusnew');
    $show_full_infonew = formOnOff('show_full_infonew');
    $commentnew = formOnOff('commentnew');
    $ipregnew = formOnOff('ipregnew');
    $ipchecknew = formOnOff('ipchecknew');
    $specqnew = formOnOff('specqnew');
    $predformatnew = formOnOff('predformatnew');
    $whosoptomizednew = formOnOff('whosoptomizednew');

    $max_attach_sizenew = formInt('max_attach_sizenew');
    if ($max_attach_sizenew < 10 || $max_attach_sizenew > 1024) {
        $max_attach_sizenew = 100;
    }
    $max_attach_sizenew = $max_attach_sizenew * 1024;

    $bboffreasonnew = $db->escape(formVar('bboffreasonnew'));
    if ($bbstatusnew == 'off' && empty($bboffreasonnew)) {
        cp_error($lang['bbstatusempty'], false, '', '</td></tr></table>');
    }

    $sitenamenew = $db->escape(formVar('sitenamenew'));
    $bbnamenew = $db->escape(formVar('bbnamenew'));
    $siteurlnew = $db->escape(formVar('siteurlnew'));
    $boardurlnew = $db->escape(formVar('boardurlnew'));
    $adminemailnew = $db->escape(formVar('adminemailnew'));
    $copyrightnew = $db->escape(formVar('copyrightnew'));
    $metatag_keywordsnew = $db->escape(formVar('metatag_keywordsnew'));
    $metatag_descriptionnew = $db->escape(formVar('metatag_descriptionnew'));
    $pmwelcomesubjectnew = $db->escape(formVar('pmwelcomesubjectnew'));
    $pmwelcomemessagenew = $db->escape(formVar('pmwelcomemessagenew'));
    $pmwelcomefromnew = $db->escape(formVar('pmwelcomefromnew'));

    $config_array = array(
        'bbname' => $bbnamenew,
        'bbstatus' => $bbstatusnew,
        'bboffreason' => $bboffreasonnew,
        'sitename' => $sitenamenew,
        'siteurl' => $siteurlnew,
        'boardurl' => $boardurlnew,
        'adminemail' => $adminemailnew,
        'copyright' => $copyrightnew,
        'metatag_status' => $metatag_statusnew,
        'metatag_keywords' => $metatag_keywordsnew,
        'metatag_description' => $metatag_descriptionnew,
        'pmwelcomestatus' => $pmwelcomestatusnew,
        'pmwelcomefrom' => $pmwelcomefromnew,
        'pmwelcomesubject' => $pmwelcomesubjectnew,
        'pmwelcomemessage' => $pmwelcomemessagenew,
        'show_full_info' => $show_full_infonew,
        'comment' => $commentnew,
        'ipreg' => $ipregnew,
        'ipcheck' => $ipchecknew,
        'specq' => $specqnew,
        'predformat' => $predformatnew,
        'max_attach_size' => $max_attach_sizenew,
        'whosoptomized' => $whosoptomizednew,
    );

    // execute query
    foreach ($config_array as $key => $value) {
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$value' WHERE config_name = '$key' LIMIT 1");
    }

    cp_message($lang['textsettingsupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('boardsubmit')) {
    viewPanel();
}

if (onSubmit('boardsubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>