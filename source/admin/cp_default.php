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
nav($lang['admin_main_settings2']);
btitle($lang['textcp']);
btitle($lang['admin_main_settings2']);

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
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG, $selHTML;
    global $self, $onlinetime, $gbblva;

    $langfileselect = langSelect();

    $themelist = array();
    $themelist[] = '<select name="themenew">';
    $query = $db->query("SELECT themeid, name FROM " . X_PREFIX . "themes WHERE themestatus = 'on' ORDER BY name ASC");
    while (($themeinfo = $db->fetch_array($query)) != false) {
        $themeinfo['name'] = stripslashes($themeinfo['name']);
        if ($themeinfo['themeid'] == $CONFIG['theme']) {
            $themelist[] = '<option value="' . $themeinfo['themeid'] . '" selected="selected">' . $themeinfo['name'] . '</option>';
        } else {
            $themelist[] = '<option value="' . $themeinfo['themeid'] . '">' . $themeinfo['name'] . '</option>';
        }
    }
    $themelist[] = '</select>';
    $themelist = implode("\n", $themelist);
    $db->free_result($query);

    $timezone1 = $timezone2 = $timezone3 = $timezone4 = $timezone5 = $timezone6 = false;
    $timezone7 = $timezone8 = $timezone9 = $timezone10 = $timezone11 = $timezone12 = false;
    $timezone13 = $timezone14 = $timezone15 = $timezone16 = $timezone17 = $timezone18 = false;
    $timezone19 = $timezone20 = $timezone21 = $timezone22 = $timezone23 = $timezone24 = false;
    $timezone25 = $timezone26 = $timezone27 = $timezone28 = $timezone29 = $timezone30 = false;
    $timezone31 = $timezone32 = $timezone33 = false;

    switch ($CONFIG['def_tz']) {
        case '-12.00':
            $timezone1 = true;
            break;
        case '-11.00':
            $timezone2 = true;
            break;
        case '-10.00':
            $timezone3 = true;
            break;
        case '-9.00':
            $timezone4 = true;
            break;
        case '-8.00':
            $timezone5 = true;
            break;
        case '-7.00':
            $timezone6 = true;
            break;
        case '-6.00':
            $timezone7 = true;
            break;
        case '-5.00':
            $timezone8 = true;
            break;
        case '-4.00':
            $timezone9 = true;
            break;
        case '-3.50':
            $timezone10 = true;
            break;
        case '-3.00':
            $timezone11 = true;
            break;
        case '-2.00':
            $timezone12 = true;
            break;
        case '-1.00':
            $timezone13 = true;
            break;
        case '1.00':
            $timezone15 = true;
            break;
        case '2.00':
            $timezone16 = true;
            break;
        case '3.00':
            $timezone17 = true;
            break;
        case '3.50':
            $timezone18 = true;
            break;
        case '4.00':
            $timezone19 = true;
            break;
        case '4.50':
            $timezone20 = true;
            break;
        case '5.00':
            $timezone21 = true;
            break;
        case '5.50':
            $timezone22 = true;
            break;
        case '5.75':
            $timezone23 = true;
            break;
        case '6.00':
            $timezone24 = true;
            break;
        case '6.50':
            $timezone25 = true;
            break;
        case '7.00':
            $timezone26 = true;
            break;
        case '8.00':
            $timezone27 = true;
            break;
        case '9.00':
            $timezone28 = true;
            break;
        case '9.50':
            $timezone29 = true;
            break;
        case '10.00':
            $timezone30 = true;
            break;
        case '11.00':
            $timezone31 = true;
            break;
        case '12.00':
            $timezone32 = true;
            break;
        case '13.00':
            $timezone33 = true;
            break;
        case '0.00':
        default:
            $timezone14 = true;
            break;
    }

    $currdate = gmdate($self['timecode'], $onlinetime);
    eval($lang['evaloffset']);

    $daylight1 = $daylight2 = '';
    switch ($CONFIG['daylightsavings']) {
        case '3600':
            $daylight1 = $selHTML;
            break;
        default:
            $daylight2 = $selHTML;
            break;
    }

    $check12 = $check24 = '';
    switch ($CONFIG['timeformat']) {
        case '24':
            $check24 = $selHTML;
            break;
        default:
            $check12 = $selHTML;
            break;
    }

    $bbcimg_statuson = $bbcimg_statusoff = '';
    formHelper::getSettingOnOffHtml('bbcimg_status', $bbcimg_statuson, $bbcimg_statusoff);

    $timeformatlist = array();
    $timeformatlist[] = '<select name="timeformatnew">';
    $timeformatlist[] = '<option value="24" ' . $check24 . '>' . gmdate("H:i", $onlinetime + ($self['timeoffset'] * 3600) + $self['daylightsavings']) . '</option>';
    $timeformatlist[] = '<option value="12" ' . $check12 . '>' . gmdate("h:i A", $onlinetime + ($self['timeoffset'] * 3600) + $self['daylightsavings']) . '</option>';
    $timeformatlist[] = '</select>';
    $timeformatlist = implode("\n", $timeformatlist);

    if ($CONFIG['attach_num_default'] <= 0) {
        $attach_num = 0;
    } else {
        $attach_num = $CONFIG['attach_num_default'];
    }

    $CONFIG['max_attheight'] = (int) $CONFIG['max_attheight'];
    $CONFIG['max_attwidth'] = (int) $CONFIG['max_attwidth'];
    $CONFIG['bbc_maxwd'] = (int) $CONFIG['bbc_maxwd'];
    $CONFIG['bbc_maxht'] = (int) $CONFIG['bbc_maxht'];
    ?>
    <form method="post" action="cp_default.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td colspan="2" class="title"><?php echo $lang['admin_main_settings2'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textlanguage'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $langfileselect ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texttheme'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $themelist ?></td>
                        </tr>
                        <?php
formHelper::formTextBox($lang['textppp'], 'postperpagenew', $CONFIG['postperpage'], 3);
    formHelper::formTextBox($lang['texttpp'], 'topicperpagenew', $CONFIG['topicperpage'], 3);
    formHelper::formTextBox($lang['textmpp'], 'memberperpagenew', $CONFIG['memberperpage'], 3);
    formHelper::formTextBox($lang['customposts'], 'custompostsnew', $CONFIG['customposts'], 3);
    formHelper::formTextBox($lang['pmposts'], 'pmpostsnew', $CONFIG['pmposts'], 3);
    formHelper::formTextBox($lang['max_reg_day'], 'max_reg_daynew', $CONFIG['max_reg_day'], 3);
    formHelper::formTextBox($lang['set_maxsigchars'], 'maxsigcharsnew', $CONFIG['maxsigchars'], 3);
    formHelper::formTextBox($lang['viewsigminposts'], 'viewsigminpostsnew', $CONFIG['viewsigminposts'], 3);
    formHelper::formTextBox($lang['attachnumdef'], 'attachnumnew', $attach_num, 2);
    formHelper::formTextBox($lang['max_attheight'], 'max_attheightnew', $CONFIG['max_attheight'], 3);
    formHelper::formTextBox($lang['max_attwidth'], 'max_attwidthnew', $CONFIG['max_attwidth'], 3);
    formHelper::formSelectOnOff($lang['bbcimg_status'], 'bbcimg_statusnew', $bbcimg_statuson, $bbcimg_statusoff);
    formHelper::formTextBox($lang['bbc_maxht'], 'bbc_maxhtnew', $CONFIG['bbc_maxht'], 3);
    formHelper::formTextBox($lang['bbc_maxwd'], 'bbc_maxwdnew', $CONFIG['bbc_maxwd'], 3);
    ?>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['daylightsavings'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select
                                        name="daylightsavings1">
                                    <option value="3600" <?php echo $daylight1 ?>><?php echo $lang['textyes'] ?></option>
                                    <option value="0" <?php echo $daylight2 ?>><?php echo $lang['textno'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texttimeformat'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $timeformatlist ?></td>
                        </tr>
                        <?php
if ($CONFIG['predformat'] == 'on') {
        $df = "<tr class=\"tablerow\">\n\t<td bgcolor=\"$THEME[altbg1]\">$lang[dateformat1]</td>\n";
    } else {
        $df = "<tr class=\"tablerow\">\n\t<td bgcolor=\"$THEME[altbg1]\">$lang[dateformat2]</td>\n";
    }
    $df = $df . "\t<td bgcolor=\"$THEME[altbg2]\"><select name=\"dateformatnew\">\n";
    $querydf = $db->query("SELECT * FROM " . X_PREFIX . "dateformats");
    while (($dformats = $db->fetch_array($querydf)) != false) {
        if ($CONFIG['predformat'] == 'on') {
            $example = gmdate(formatDate($dformats['dateformat']), $gbblva + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        } else {
            $example = $dformats['dateformat'];
        }

        if ($CONFIG['dateformat'] == $dformats['dateformat']) {
            $df = $df . "\t<option value=\"$dformats[dateformat]\" selected=\"selected\">$example</option>\n";
        } else {
            $df = $df . "\t<option value=\"$dformats[dateformat]\">$example</option>\n";
        }
    }
    $df = $df . "\t</select>\n\t</td>\n</tr>";
    echo $df;
    $db->free_result($querydf);

    formHelper::formSelectList($lang['textoffset'], 'new_def_tz', array(
        $lang['timezone1'],
        $lang['timezone2'],
        $lang['timezone3'],
        $lang['timezone4'],
        $lang['timezone5'],
        $lang['timezone6'],
        $lang['timezone7'],
        $lang['timezone8'],
        $lang['timezone9'],
        $lang['timezone10'],
        $lang['timezone11'],
        $lang['timezone12'],
        $lang['timezone13'],
        $lang['timezone14'],
        $lang['timezone15'],
        $lang['timezone16'],
        $lang['timezone17'],
        $lang['timezone18'],
        $lang['timezone19'],
        $lang['timezone20'],
        $lang['timezone21'],
        $lang['timezone22'],
        $lang['timezone23'],
        $lang['timezone24'],
        $lang['timezone25'],
        $lang['timezone26'],
        $lang['timezone27'],
        $lang['timezone28'],
        $lang['timezone29'],
        $lang['timezone30'],
        $lang['timezone31'],
        $lang['timezone32'],
        $lang['timezone33'],
    ), array(
        '-12',
        '-11',
        '-10',
        '-9',
        '-8',
        '-7',
        '-6',
        '-5',
        '-4',
        '-3.5',
        '-3',
        '-2',
        '-1',
        '0',
        '1',
        '2',
        '3',
        '3.5',
        '4',
        '4.5',
        '5',
        '5.5',
        '5.75',
        '6',
        '6.5',
        '7',
        '8',
        '9',
        '9.5',
        '10',
        '11',
        '12',
        '13',
    ), array(
        $timezone1,
        $timezone2,
        $timezone3,
        $timezone4,
        $timezone5,
        $timezone6,
        $timezone7,
        $timezone8,
        $timezone9,
        $timezone10,
        $timezone11,
        $timezone12,
        $timezone13,
        $timezone14,
        $timezone15,
        $timezone16,
        $timezone17,
        $timezone18,
        $timezone19,
        $timezone20,
        $timezone21,
        $timezone22,
        $timezone23,
        $timezone24,
        $timezone25,
        $timezone26,
        $timezone27,
        $timezone28,
        $timezone29,
        $timezone30,
        $timezone31,
        $timezone32,
        $timezone33,
    ), false);
    ?>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input class="submit" type="submit"
                                                   name="defaultsubmit"
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
    global $shadow2, $lang, $db, $THEME;
    global $oToken;

    $oToken->assert_token();

    $max_attheightnew = formInt('max_attheightnew');
    $max_attwidthnew = formInt('max_attwidthnew');
    $topicperpagenew = formInt('topicperpagenew');
    $memberperpagenew = formInt('memberperpagenew');
    $daylightsavings1 = formInt('daylightsavings1');
    $postperpagenew = formInt('postperpagenew');
    $custompostsnew = formInt('custompostsnew');
    $pmpostsnew = formInt('pmpostsnew');
    $new_def_tz = formInt('new_def_tz');
    $custompostsnew = formInt('custompostsnew');
    $max_reg_daynew = formInt('max_reg_daynew');
    $viewsigminpostsnew = formInt('viewsigminpostsnew');
    $attachnumnew = formInt('attachnumnew');
    $maxsigcharsnew = formInt('maxsigcharsnew');
    $bbc_maxwdnew = formInt('bbc_maxwdnew');
    $bbc_maxhtnew = formInt('bbc_maxhtnew');
    $bbcimg_statusnew = formOnOff('bbcimg_statusnew');
    $themenew = formInt('themenew');
    $timeformatnew = formVar('timeformatnew');
    $timeformatnew = ($timeformatnew == 24) ? 24 : 12;
    $daylightsavings1 = ($daylightsavings1 == 3600) ? 3600 : 0;
    $langfilenew = $db->escape(findLangName(formInt('langfilenew')));
    $dateformatnew = $db->escape(formVar('dateformatnew'));

    $config_array = array(
        'langfile' => $langfilenew,
        'postperpage' => $postperpagenew,
        'topicperpage' => $topicperpagenew,
        'theme' => $themenew,
        'memberperpage' => $memberperpagenew,
        'timeformat' => $timeformatnew,
        'dateformat' => $dateformatnew,
        'def_tz' => $new_def_tz,
        'daylightsavings' => $daylightsavings1,
        'customposts' => $custompostsnew,
        'pmposts' => $pmpostsnew,
        'max_reg_day' => $max_reg_daynew,
        'viewsigminposts' => $viewsigminpostsnew,
        'attach_num_default' => $attachnumnew,
        'max_attheight' => $max_attheightnew,
        'max_attwidth' => $max_attheightnew,
        'maxsigchars' => $maxsigcharsnew,
        'bbcimg_status' => $bbcimg_statusnew,
        'bbc_maxht' => $bbc_maxhtnew,
        'bbc_maxwd' => $bbc_maxwdnew,
    );

    // execute query
    foreach ($config_array as $key => $value) {
        $db->query("UPDATE " . X_PREFIX . "settings SET config_value = '$value' WHERE config_name = '$key' LIMIT 1");
    }

    cp_message($lang['textsettingsupdate'], false, '', '</td></tr></table>', 'index.php', true, false, true);
}

displayAdminPanel();

if (noSubmit('defaultsubmit')) {
    viewPanel();
}

if (onSubmit('defaultsubmit')) {
    doPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>