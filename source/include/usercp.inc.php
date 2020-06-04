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

// check to ensure no direct viewing of page
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

require_once('member.class.php');

function makenav($current)
{
    global $THEME, $lang, $menu, $CONFIG, $shadow, $shadow2, $self;

    if ($THEME['celloverfx'] == 'on') {
        $sortby_fx = "onmouseover=\"this.style.backgroundColor='$THEME[altbg1]';\" onmouseout=\"this.style.backgroundColor='$THEME[altbg2]';\"";
    } else {
        $sortby_fx = '';
    }

    $menu .= '<tr class="category"><td width="20%" align="center" class="title">' . $lang['usercp_options'] . '</td></tr>';

    if ($current == 'profile') {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['texteditpro'] . '</strong></td></tr>';
    } else {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=profile">' . $lang['texteditpro'] . '</a></td></tr>';
    }

    if ($current == 'options') {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['Edit_Options'] . '</strong></td></tr>';
    } else {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=options">' . $lang['Edit_Options'] . '</a></td></tr>';
    }

    if ($current == 'email') {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['Edit_Email'] . '</strong></td></tr>';
    } else {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=email">' . $lang['Edit_Email'] . '</a></td></tr>';
    }

    if ($CONFIG['avastatus'] == 'on' || $CONFIG['avatar_whocanupload'] != 'off') {
        if ($current == 'avatar') {
            $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['Edit_Avatar'] . '</strong></td></tr>';
        } else {
            $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=avatar">' . $lang['Edit_Avatar'] . '</a></td></tr>';
        }
    }

    if ($current == 'password') {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['Edit_Password'] . '</strong></td></tr>';
    } else {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=password">' . $lang['Edit_Password'] . '</a></td></tr>';
    }

    if ($current == 'signature') {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['Edit_Signature'] . '</strong></td></tr>';
    } else {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=signature">' . $lang['Edit_Signature'] . '</a></td></tr>';
    }

    if ($CONFIG['photostatus'] == 'on' || $CONFIG['photo_whocanupload'] != 'off') {
        if ($current == 'photo') {
            $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['edit_personal_photo'] . '</strong></td></tr>';
        } else {
            $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=photo">' . $lang['edit_personal_photo'] . '</a></td></tr>';
        }
    }

    $menu .= '<tr class="category"><td width="20%" align="center"><font color="' . $THEME['cattext'] . '"><strong>' . $lang['Subscribed_Threads'] . '</strong></font></td></tr>';

    if ($current == 'favorites') {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['List_Favorites'] . '</strong></td></tr>';
    } else {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=favorites">' . $lang['List_Favorites'] . '</a></td></tr>';
    }

    if ($current == 'subscriptions') {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['List_Subscriptions'] . '</strong></td></tr>';
    } else {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=subscriptions">' . $lang['List_Subscriptions'] . '</a></td></tr>';
    }

    $menu .= '<tr class="category"><td width="20%" align="center"><font color="' . $THEME['cattext'] . '"><strong>' . $lang['usercp_miscellaneous'] . '</strong></font></td></tr>';

    $menu .= "<tr><td bgcolor=\"$THEME[altbg2]\" width=\"20%\" class=\"tablerow\" " . $sortby_fx . "><a href=\"#\" onclick=\"Popup('./address.php?', 'Window', 450, 400);\">" . $lang['textaddresslist'] . "</a></td></tr>";

    if (X_MEMBER && !($CONFIG['pmstatus'] == 'off' && isset($self['status']) && $self['status'] == 'Member')) {
        $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./pm.php">' . $lang['textpmmessenger'] . '</a></td></tr>';
    }

    if ($CONFIG['avatars_status'] == 'on') {
        if ($current == 'gallery') {
            $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['avatargallery'] . '</strong></td></tr>';
        } else {
            $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="usercp.php?action=gallery">' . $lang['avatargallery'] . '</a></td></tr>';
        }
    }

    if ($CONFIG['notepadstatus'] == 'on') {
        if ($current == 'notepad') {
            $menu .= '<tr><td bgcolor="' . $THEME['altbg1'] . '" width="20%" class="tablerow"><strong>' . $lang['notepad'] . '</strong></td></tr>';
        } else {
            $menu .= '<tr><td bgcolor="' . $THEME['altbg2'] . '" width="20%" class="tablerow" ' . $sortby_fx . '><a href="./usercp.php?action=notepad">' . $lang['notepad'] . '</a></td></tr>';
        }
    }
}

function table_msg($outputmsg, $return = 0)
{
    global $THEME, $shadow, $shadow2, $lang;

    $output = '';

    if (isset($return) && $return == 1) {
        $return = true;
    } else {
        $return = false;
    }

    eval('$output = "' . template('usercp_outputmsg') . '";');

    if ($return === false) {
        return $output;
    } else {
        echo $output;
    }
}

function BDayDisplay()
{
    global $db, $member, $self;
    global $sel0, $sel1, $sel2, $sel3, $sel4, $sel5, $sel6;
    global $sel7, $sel8, $sel9, $sel10, $sel11, $sel12;
    global $dayselect, $num, $selHTML, $lang, $bday;

    $bday = str_replace(',', '', $member['bday']);
    $bday = explode(' ', $bday);

    if ($bday[0] == '') {
        $sel0 = $selHTML;
    } else
        if ($bday[0] == $lang['textjan']) {
            $sel1 = $selHTML;
        } else
            if ($bday[0] == $lang['textfeb']) {
                $sel2 = $selHTML;
            } else
                if ($bday[0] == $lang['textmar']) {
                    $sel3 = $selHTML;
                } else
                    if ($bday[0] == $lang['textapr']) {
                        $sel4 = $selHTML;
                    } else
                        if ($bday[0] == $lang['textmay']) {
                            $sel5 = $selHTML;
                        } else
                            if ($bday[0] == $lang['textjun']) {
                                $sel6 = $selHTML;
                            } else
                                if ($bday[0] == $lang['textjul']) {
                                    $sel7 = $selHTML;
                                } else
                                    if ($bday[0] == $lang['textaug']) {
                                        $sel8 = $selHTML;
                                    } else
                                        if ($bday[0] == $lang['textsep']) {
                                            $sel9 = $selHTML;
                                        } else
                                            if ($bday[0] == $lang['textoct']) {
                                                $sel10 = $selHTML;
                                            } else
                                                if ($bday[0] == $lang['textnov']) {
                                                    $sel11 = $selHTML;
                                                } else
                                                    if ($bday[0] == $lang['textdec']) {
                                                        $sel12 = $selHTML;
                                                    }

    $dayselect = array();
    $dayselect[] = '<select name="day">';
    $dayselect[] = '<option value="">' . $lang['textnone'] . '</option>';
    for ($num = 1; $num <= 31; $num++) {
        if (isset($bday[1]) && $bday[1] == $num) {
            $dayselect[] = '<option value="' . $num . '" ' . $selHTML . '>' . $num . '</option>';
        } else {
            $dayselect[] = '<option value="' . $num . '">' . $num . '</option>';
        }
    }
    $dayselect[] = '</select>';
    $dayselect = implode("\n", $dayselect);

    $bday[2] = (isset($bday[2])) ? $bday[2] : '';
}

function TimeOffsetDisplay()
{
    global $db, $member, $self, $selHTML, $lang;
    global $timezone1, $timezone2, $timezone3, $timezone4, $timezone5, $timezone6;
    global $timezone7, $timezone8, $timezone9, $timezone10, $timezone11, $timezone12;
    global $timezone13, $timezone14, $timezone15, $timezone16, $timezone17, $timezone18;
    global $timezone19, $timezone20, $timezone21, $timezone22, $timezone23, $timezone24;
    global $timezone25, $timezone26, $timezone27, $timezone28, $timezone29, $timezone30;
    global $timezone31, $timezone32, $timezone33;

    $timezone1 = $timezone2 = $timezone3 = $timezone4 = $timezone5 = $timezone6 = '';
    $timezone7 = $timezone8 = $timezone9 = $timezone10 = $timezone11 = $timezone12 = '';
    $timezone13 = $timezone14 = $timezone15 = $timezone16 = $timezone17 = $timezone18 = '';
    $timezone19 = $timezone20 = $timezone21 = $timezone22 = $timezone23 = $timezone24 = '';
    $timezone25 = $timezone26 = $timezone27 = $timezone28 = $timezone29 = $timezone30 = '';
    $timezone31 = $timezone32 = $timezone33 = '';
    switch ($member['timeoffset']) {
        case '-12.00':
            $timezone1 = $selHTML;
            break;
        case '-11.00':
            $timezone2 = $selHTML;
            break;
        case '-10.00':
            $timezone3 = $selHTML;
            break;
        case '-9.00':
            $timezone4 = $selHTML;
            break;
        case '-8.00':
            $timezone5 = $selHTML;
            break;
        case '-7.00':
            $timezone6 = $selHTML;
            break;
        case '-6.00':
            $timezone7 = $selHTML;
            break;
        case '-5.00':
            $timezone8 = $selHTML;
            break;
        case '-4.00':
            $timezone9 = $selHTML;
            break;
        case '-3.50':
            $timezone10 = $selHTML;
            break;
        case '-3.00':
            $timezone11 = $selHTML;
            break;
        case '-2.00':
            $timezone12 = $selHTML;
            break;
        case '-1.00':
            $timezone13 = $selHTML;
            break;
        case '1.00':
            $timezone15 = $selHTML;
            break;
        case '2.00':
            $timezone16 = $selHTML;
            break;
        case '3.00':
            $timezone17 = $selHTML;
            break;
        case '3.50':
            $timezone18 = $selHTML;
            break;
        case '4.00':
            $timezone19 = $selHTML;
            break;
        case '4.50':
            $timezone20 = $selHTML;
            break;
        case '5.00':
            $timezone21 = $selHTML;
            break;
        case '5.50':
            $timezone22 = $selHTML;
            break;
        case '5.75':
            $timezone23 = $selHTML;
            break;
        case '6.00':
            $timezone24 = $selHTML;
            break;
        case '6.50':
            $timezone25 = $selHTML;
            break;
        case '7.00':
            $timezone26 = $selHTML;
            break;
        case '8.00':
            $timezone27 = $selHTML;
            break;
        case '9.00':
            $timezone28 = $selHTML;
            break;
        case '9.50':
            $timezone29 = $selHTML;
            break;
        case '10.00':
            $timezone30 = $selHTML;
            break;
        case '11.00':
            $timezone31 = $selHTML;
            break;
        case '12.00':
            $timezone32 = $selHTML;
            break;
        case '13.00':
            $timezone33 = $selHTML;
            break;
        case '0.00':
        default:
            $timezone14 = $selHTML;
            break;
    }
}

function memberYesNo($self, &$yes, &$no)
{
    global $member, $selHTML;

    $yes = $no = '';
    switch ($member[$self]) {
        case 'yes':
            $yes = $selHTML;
            break;
        default:
            $no = $selHTML;
            break;
    }
}

class userObj
{

    function viewProfile()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $shadow2, $menu, $member;
        global $sel0, $sel1, $sel2, $sel3, $sel4, $sel5, $sel6;
        global $sel7, $sel8, $sel9, $sel10, $sel11, $sel12;
        global $dayselect, $bday;

        $member = $self;

        BDayDisplay();

        $customblock = 'usercp_custom_none';
        if (X_STAFF || (X_MEMBER && $member['postnum'] > $CONFIG['customposts'])) {
            $customblock = 'usercp_custom';
        }
        eval('$customblock = "' . template($customblock) . '";');

        eval('$output = "' . template('usercp_profile') . '";');
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
    }

    function submitProfile()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $shadow2, $menu, $member;
        global $dayselect, $bday;

        reset($self);

        if (empty($self['username'])) {
            error($lang['badname'], false);
        }

        // Grab the current member's uid, and use it to populate some fields
        $member = new member($self['uid']);

        // Check that we populated the object correctly
        if ($member->record['uid'] !== $self['uid']) {
            error($lang['badname'], false);
        }

        $month = $db->escape(formVar('month'));
        $day = formInt('day', false);
        $year = formInt('year', false);
        if ($year == '' || $year == 0) {
            $comma = '';
        } else {
            $comma = ', ';
        }

        $member->record['bday'] = $month . ' ' . $day . $comma . $year;
        $member->record['location'] = formVar('newlocation');
        $member->record['icq'] = formVar('newicq');
        $member->record['yahoo'] = formVar('newyahoo');
        $member->record['aim'] = formVar('newaim');
        $member->record['msn'] = formVar('newmsn');
        $member->record['site'] = formVar('newsite');
        $member->record['bio'] = formVar('newbio');
        $member->record['mood'] = formVar('newmood');
        $member->record['firstname'] = formVar('firstname');
        $member->record['lastname'] = formVar('lastname');
        $member->record['occupation'] = formVar('newoccupation');
        $member->record['blog'] = formVar('newblog');

        if (X_STAFF || (X_MEMBER && $member->record['postnum'] > $CONFIG['customposts'])) {
            $member->record['customstatus'] = formVar('newcustomstatus');
        }

        $member->dirty = true;
        $member->update();

        $output = table_msg($lang['usercpeditpromsg']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php', 2.5, X_REDIRECT_JS);
    }

    function viewOption()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG, $member;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;
        global $timezone1, $timezone2, $timezone3, $timezone4, $timezone5, $timezone6;
        global $timezone7, $timezone8, $timezone9, $timezone10, $timezone11, $timezone12;
        global $timezone13, $timezone14, $timezone15, $timezone16, $timezone17, $timezone18;
        global $timezone19, $timezone20, $timezone21, $timezone22, $timezone23, $timezone24;
        global $timezone25, $timezone26, $timezone27, $timezone28, $timezone29, $timezone30;
        global $timezone31, $timezone32, $timezone33;
        global $gbblva;

        $member = $self;

        $showemailyes = $showemailno = '';
        memberYesNo('showemail', $showemailyes, $showemailno);

        $newsletteryes = $newsletterno = '';
        memberYesNo('newsletter', $newsletteryes, $newsletterno);

        $saveogpmyes = $saveogpmno = '';
        memberYesNo('saveogpm', $saveogpmyes, $saveogpmno);

        $emailonpmyes = $emailonpmno = '';
        memberYesNo('emailonpm', $emailonpmyes, $emailonpmno);

        $viewavatarsyes = $viewavatarsno = '';
        memberYesNo('viewavatars', $viewavatarsyes, $viewavatarsno);

        $viewsigsyes = $viewsigsno = '';
        memberYesNo('viewsigs', $viewsigsyes, $viewsigsno);

        $shownameyes = $shownameno = '';
        memberYesNo('showname', $shownameyes, $shownameno);

        $expviewyes = $expviewno = '';
        memberYesNo('expview', $expviewyes, $expviewno);

        $invisibleyes = $invisibleno = '';
        switch ($member['invisible']) {
            case '1':
                $invisibleyes = $selHTML;
                break;
            default:
                $invisibleno = $selHTML;
                break;
        }

        $dstyes = $dstno = '';
        switch ($member['daylightsavings']) {
            case '3600':
                $dstyes = $selHTML;
                break;
            default:
                $dstno = $selHTML;
                break;
        }

        $selectasc = $selectdesc = '';
        switch ($member['psorting']) {
            case 'ASC':
                $selectasc = $selHTML;
                break;
            default:
                $selectdesc = $selHTML;
                break;
        }

        $currdate = gmdate($self['timecode'], $onlinetime);
        eval($lang['evaloffset']);

        TimeOffsetDisplay();

        $themelist = array();
        $themelist[] = '<select name="thememem">';
        $themelist[] = '<option value="0">' . $lang['textusedefault'] . '</option>';
        $query = $db->query("SELECT themeid, name FROM " . X_PREFIX . "themes WHERE themestatus = 'on' ORDER BY name ASC");
        while (($themeinfo = $db->fetch_array($query)) != false) {
            if ($themeinfo['themeid'] == $member['theme']) {
                $themelist[] = '<option value="' . $themeinfo['themeid'] . '" ' . $selHTML . '>' . stripslashes($themeinfo['name']) . '</option>';
            } else {
                $themelist[] = '<option value="' . $themeinfo['themeid'] . '">' . stripslashes($themeinfo['name']) . '</option>';
            }
        }
        $themelist[] = '</select>';
        $themelist = implode("\n", $themelist);
        $db->free_result($query);

        $langfileselect = langSelect();

        $check12 = $check24 = '';
        switch ($member['timeformat']) {
            case '24':
                $check24 = $selHTML;
                break;
            default:
                $check12 = $selHTML;
                break;
        }

        $timeformatlist = array();
        $timeformatlist[] = '<select name="timeformatnew">';
        $timeformatlist[] = '<option value="24" ' . $check24 . '>' . gmdate("H:i", $onlinetime + ($self['timeoffset'] * 3600) + $self['daylightsavings']) . '</option>';
        $timeformatlist[] = '<option value="12" ' . $check12 . '>' . gmdate("h:i A", $onlinetime + ($self['timeoffset'] * 3600) + $self['daylightsavings']) . '</option>';
        $timeformatlist[] = '</select>';
        $timeformatlist = implode("\n", $timeformatlist);

        if ($CONFIG['predformat'] == 'on') {
            $df = "<tr>\n\t<td bgcolor=\"$THEME[altbg1]\" class=\"tablerow\" width=\"22%\">$lang[dateformat1]</td>\n";
        } else {
            $df = "<tr>\n\t<td bgcolor=\"$THEME[altbg1]\" class=\"tablerow\" width=\"22%\">$lang[dateformat2]</td>\n";
        }

        $df .= "\t<td bgcolor=\"$THEME[altbg2]\" class=\"tablerow\"><select name=\"dateformatnew\">\n";
        $querydf = $db->query("SELECT * FROM " . X_PREFIX . "dateformats");
        while (($dformats = $db->fetch_array($querydf)) != false) {
            if ($CONFIG['predformat'] == 'on') {
                $example = gmdate(formatDate($dformats['dateformat']), $gbblva + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
            } else {
                $example = $dformats['dateformat'];
            }

            $dformats['dateformat'] = str_replace(array(
                'mm',
                'dd',
                'yyyy',
                'yy'
            ), array(
                'n',
                'j',
                'Y',
                'y'
            ), $dformats['dateformat']);

            if ($member['dateformat'] == $dformats['dateformat']) {
                $df .= "\t<option value=\"$dformats[dateformat]\" $selHTML>$example</option>\n";
            } else {
                $df .= "\t<option value=\"$dformats[dateformat]\">$example</option>\n";
            }
        }
        $df .= "\t</select>\n\t</td>\n</tr>";
        $db->free_result($querydf);

        $akablock = '';
        if (!empty($self['firstname']) || !empty($self['firstname'])) {
            eval('$akablock = "' . template('usercp_options_aka') . '";');
        }

        eval('$output = "' . template('usercp_options') . '";');
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
    }

    function submitOption()
    {
        global $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;

        reset($self);

        if (empty($self['username'])) {
            error($lang['badname'], false);
        }

        // Grab the current member's uid, and use it to populate some fields
        $member = new member($self['uid']);

        // Check that we populated the object correctly
        if ($member->record['uid'] !== $self['uid']) {
            error($lang['badname'], false);
        }

        $member->record['showemail'] = formYesNo('newshowemail');
        $member->record['newsletter'] = formYesNo('newnewsletter');
        $member->record['saveogpm'] = formYesNo('saveogpm');
        $member->record['emailonpm'] = formYesNo('emailonpm');
        $member->record['viewavatars'] = formYesNo('viewavatars');
        $member->record['viewsigs'] = formYesNo('viewsigs');
        $member->record['expview'] = formYesNo('expview');
        $member->record['invisible'] = form10('newinv');
        $member->record['daylightsavings'] = form3600('daylightsavings1');
        $member->record['theme'] = formInt('thememem');
        $psorting = formVar('psorting');
        if ($psorting != 'ASC') {
            $psorting = 'DESC';
        }
        $member->record['psorting'] = $psorting;

        $tppnew = formInt('tppnew');
        if ($tppnew < 5) {
            $tppnew = $CONFIG['topicperpage'];
        }
        $member->record['tpp'] = $tppnew;

        $pppnew = formInt('pppnew');
        if ($pppnew < 5) {
            $pppnew = $CONFIG['postperpage'];
        }
        $member->record['ppp'] = $pppnew;

        $timeoffset1 = formInt('timeoffset1');
        if ($timeoffset1 < -12 || $timeoffset1 > 13) {
            $timeoffset1 = $CONFIG['def_tz'];
        }
        $member->record['timeoffset'] = $timeoffset1;

        $member->record['timeformat'] = formVar('timeformatnew');
        $member->record['dateformat'] = formVar('dateformatnew');
        $member->record['langfile'] = findLangName(formInt('langfilenew'));

        if (!empty($self['firstname']) || !empty($self['firstname'])) {
            $member->record['showname'] = formYesNo('showname');
        }

        $member->dirty = true;
        $member->update();

        $output = table_msg($lang['usercpeditpromsg']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php', 2.5, X_REDIRECT_JS);
    }

    function submitEmail()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG, $mailsys;
        global $selHTML, $self, $onlinetime, $shadow2, $menu, $authC;
        global $currtime, $cookiepath, $cookiedomain, $onlineip;

        reset($self);

        if (empty($self['username'])) {
            error($lang['badname'], false);
        }

        // Grab the current member's uid, and use it to populate some fields
        $member = new member($self['uid']);

        // Check that we populated the object correctly
        if ($member->record['uid'] !== $self['uid']) {
            error($lang['badname'], false);
        }

        $username = formVar('username');
        $newemail = formVar('newemail');

        if (isset($_POST['newemail']) && (!isset($_POST['newemail']) || isset($_GET['newemail']))) {
            $auditaction = $_SERVER['REQUEST_URI'];
            $aapos = strpos($auditaction, "?");
            if ($aapos !== false) {
                $auditaction = substr($auditaction, $aapos + 1);
            }
            $auditaction = $db->escape("$onlineip|#|$auditaction");
            adminaudit($self['username'], $auditaction, 0, 0, "Potential XSS exploit using newemail");
            die("Hack atttempt recorded in audit logs.");
        }

        $email = $db->escape(formVar('newemail'), -1, true);

        $efail = false;
        $query = $db->query("SELECT * FROM " . X_PREFIX . "restricted");
        while (($erestrict = $db->fetch_array($query)) != false) {
            if ($erestrict['case_sensitivity'] == 1) {
                if ($erestrict['partial'] == 1) {
                    if (strpos($email, $erestrict['name']) !== false) {
                        $efail = true;
                    }
                } else {
                    if ($email == $erestrict['name']) {
                        $efail = true;
                    }
                }
            } else {
                $t_email = strtolower($email);
                $erestrict['name'] = strtolower($erestrict['name']);

                if ($erestrict['partial'] == 1) {
                    if (strpos($t_email, $erestrict['name']) !== false) {
                        $efail = true;
                    }
                } else {
                    if ($t_email == $erestrict['name']) {
                        $efail = true;
                    }
                }
            }
        }
        $db->free_result($query);

        if ($efail) {
            error($lang['emailvaliderror1'], false);
        }

        if (empty($email) || isValidEmail($email) == false) {
            error($lang['emailvaliderror2'], false);
        }

        if ($email != $member->record['email']) {

            $newpass = $get = $max = $chars = '';

            $chars = "23456789abcdefghjkmnpqrstuvwxyz";
            mt_srand((double)microtime() * 1000000);
            $max = mt_rand(8, 12);
            for ($get = strlen($chars), $i = 0; $i < $max; $i++) {
                $newpass .= $chars[mt_rand(0, $get)];
            }

            $newmd5pass = md5(trim($newpass));
            $db->query("UPDATE " . X_PREFIX . "members SET email = '$email', password = '$newmd5pass' WHERE uid = '" . $self['uid'] . "'");
            $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE username = '" . $self['username'] . "'");

            $messagebody = $lang['emailvalidpwis'] . "\n\n" . $self['username'] . "\n" . $newpass;

            $mailsys->setTo($email);
            $mailsys->setFrom($CONFIG['adminemail'], $CONFIG['bbname']);
            $mailsys->setSubject($lang['textyourpw']);
            $mailsys->setMessage($messagebody);
            $mailsys->Send();

            $authC->logout();
        }
        $db->query("UPDATE " . X_PREFIX . "members SET email = '$email' WHERE uid = '" . $self['uid'] . "'");

        $output = table_msg($lang['usercpeditpromsg']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php', 2.5, X_REDIRECT_JS);
    }

    function viewPhoto()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;

        $member = $self;

        $photo = '';
        if ($CONFIG['photostatus'] == 'on') {
            eval('$photo = "' . template('usercp_photourl') . '";');
        }
        $userphoto = $photodeletebutton = $photohidden = '';
        if ($CONFIG['photo_whocanupload'] != 'off' || $CONFIG['photostatus'] == 'on') {
            if (!empty($member['photo'])) {
                eval('$userphoto = "' . template('usercp_photouser') . '";');
                $photodeletebutton = '<br /><input type="checkbox" name="photodel" value="1" />' . $lang['photo_Delete'] . '';
            }

            if (($CONFIG['photo_whocanupload'] == 'all') && X_MEMBER) {
                eval('$photohidden = "' . template('usercp_photohidden') . '";');
            } else
                if (($CONFIG['photo_whocanupload'] == 'staff') && X_STAFF) {
                    eval('$photohidden = "' . template('usercp_photohidden') . '";');
                }
        }
        if ($CONFIG['photostatus'] == 'on' || $CONFIG['photo_whocanupload'] != 'off') {
            eval('$photodelbtn = "' . template('usercp_photosubmit') . '";');
        } else {
            eval('$photodelbtn = "' . template('usercp_photonone') . '";');
        }
        eval('$output = "' . template('usercp_photo') . '";');
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
    }

    function submitPhoto()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;

        reset($self);

        if (empty($self['username'])) {
            error($lang['badname'], false);
        }

        // Grab the current member's uid, and use it to populate some fields
        $member = new member($self['uid']);

        // Check that we populated the object correctly
        if ($member->record['uid'] !== $self['uid']) {
            error($lang['badname'], false);
        }

        if (isset($_POST['newphoto'])) {
            if ('http' == substr($_POST['newphoto'], 0, 4)) {
                // (raw)urlencode() creates a mess. We're gonna kill any bad urls with escape(), so no worries.
                $_POST['newphoto'] = str_replace(' ', '%20', $_POST['newphoto']);
            }
        } else {
            $_POST['newphoto'] = '';
        }

        $photo = '';

        if (!isset($_FILES['photofile']['name']) || !$_FILES['photofile']['tmp_name'] || empty($_FILES['photofile']['name'])) {
            $photo = $db->escape(formVar('newphoto'), -1, true);

            $max_size = explode('x', $CONFIG['max_photo_size']);
            if ($max_size[0] > 0 && $max_size[1] > 0 && substr_count($photo, ',') < 2) {
                $size = @getimagesize($photo);
                if ($size === false) {
                    error($lang['pic_not_located'], false);
                } else
                    if (($size[0] > $max_size[0] && $max_size[0] > 0) || ($size[1] > $max_size[1] && $max_size[1] > 0) && !X_ADMIN) {
                        error($lang['photo_too_big'] . $CONFIG['max_photo_size'] . $lang['photo_Pixels'], false);
                    }
            }
        }

        if (isset($_COOKIE['photofile']) || isset($_POST['photofile']) || isset($_GET['photofile'])) {
            die('Action Halted Due To Illegal Acivity!!');
            exit();
        }

        if (isset($_FILES['photofile']['name']) && $_FILES['photofile']['tmp_name'] && !empty($_FILES['photofile']['name'])) {
            $photoext = substr($_FILES['photofile']['name'], strlen($_FILES['photofile']['name']) - 3, 3);
            $newphotoname = $member->record['uid'] . '.' . $onlinetime . '.' . $photoext;
            $check = $_FILES['photofile'];

            $CONFIG['photo_filesize'] = (int)$CONFIG['photo_filesize'];
            if (($check['size'] > $CONFIG['photo_filesize']) && !X_ADMIN) {
                error($lang['photo_too_big'] . $CONFIG['photo_filesize'] . $lang['photo_Bytes'], false);
            }

            $photopath = $CONFIG['photo_path'] . '/' . $newphotoname;
            $tmppath = $check['tmp_name'];

            if (!preg_match('/gif|jpeg|png|jpg|bmp/i', $photoext)) {
                error($lang['photo_invalid_ext'], false);
            }

            if (!is_writable($CONFIG['photo_path'])) {
                error($lang['photo_nowrite'], false);
            }

            $size = getimagesize($tmppath);
            $width = $size[0];
            $height = $size[1];
            $type = $size[2];

            if (!((bool)ini_get('safe_mode'))) {
                set_time_limit(30);
            }
            $imginfo = getimagesize($tmppath);
            $type = $imginfo[2];

            switch ($type) {
                case IMAGETYPE_GIF:
                    if (!function_exists('imagecreatefromgif')) {
                        return $tmppath;
                    }
                    $srcImage = imagecreatefromgif($tmppath);
                    break;
                case IMAGETYPE_JPEG:
                    if (!function_exists('imagecreatefromjpeg')) {
                        return $tmppath;
                    }
                    $srcImage = imagecreatefromjpeg($tmppath);
                    break;
                case IMAGETYPE_PNG:
                    if (!function_exists('imagecreatefrompng')) {
                        return $tmppath;
                    }
                    $srcImage = imagecreatefrompng($tmppath);
                    break;
                case IMAGETYPE_WBMP:
                    if (!function_exists('imagecreatefromwbmp')) {
                        return $tmppath;
                    }
                    $srcImage = imagecreatefromwbmp($tmppath);
                    break;
                default:
                    return $tmppath;
            }

            if ($width > $CONFIG['photo_max_width']) {
                $newwidth = $CONFIG['photo_new_width'];
                $newheight = ($newwidth / $width) * $height;
            } else
                if ($height > $CONFIG['photo_max_height']) {
                    $newheight = $CONFIG['photo_new_height'];
                    $newwidth = ($newheight / $height) * $width;
                }

            if (isset($newwidth)) {
                $destImage = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                switch ($type) {
                    case IMAGETYPE_GIF:
                        imagegif($destImage, $tmppath);
                        break;
                    case IMAGETYPE_JPEG:
                        imagejpeg($destImage, $tmppath);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($destImage, $tmppath);
                        break;
                    case IMAGETYPE_WBMP:
                        imagewbmp($destImage, $tmppath);
                        break;
                }
                imagedestroy($srcImage);
                imagedestroy($destImage);
            }

            copy($tmppath, $photopath);
            $db->query("UPDATE " . X_PREFIX . "members SET photo = '$photopath' WHERE uid = '" . $self['uid'] . "'");
        }

        if (isset($_POST['newphoto']) && empty($_FILES['photofile']['name'])) {
            $db->query("UPDATE " . X_PREFIX . "members SET photo = '$photo' WHERE uid = '" . $self['uid'] . "'");
        }

        if (onSubmit('photosubmit') && isset($_POST['photodel']) != 1 && empty($_POST['newphoto']) && empty($_FILES['photofile']['name'])) {

            $db->query("UPDATE " . X_PREFIX . "members SET photo = '$self[photo]' WHERE uid = '" . $self['uid'] . "'");
        }
        if (isset($_POST['photodel']) && isset($_POST['photodel']) == 1 && empty($_FILES['photofile']['name'])) {
            if (file_exists($member->record['photo'])) {
                unlink($member->record['photo']);
            }
            $db->query("UPDATE " . X_PREFIX . "members SET photo = '' WHERE uid = '" . $self['uid'] . "'");
        }
        $output = table_msg($lang['photo_Updated']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php?action=photo', 2.5, X_REDIRECT_JS);
    }

    function submitPassword()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;
        global $cookiepath, $cookiedomain, $authC, $onlineip;

        reset($self);

        if (empty($self['username'])) {
            $output = table_msg($lang['badname']);
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
            redirect('usercp.php?action=password', 2.5, X_REDIRECT_JS);
            exit();
        }

        // Grab the current member's uid, and use it to populate some fields
        $member = new member($self['uid']);

        // Check that we populated the object correctly
        if ($member->record['uid'] !== $self['uid'] || empty($member->record['username'])) {
            $output = table_msg($lang['badname']);
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
            redirect('usercp.php?action=password', 2.5, X_REDIRECT_JS);
            exit();
        }

        if ($self['password'] != $member->record['password']) {
            $output = table_msg($lang['textpwincorrect']);
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
            redirect('usercp.php?action=password', 2.5, X_REDIRECT_JS);
            exit();
        }

        if (isset($_POST['newpassword']) && (!isset($_POST['newpassword']) || isset($_GET['newpassword']))) {
            $auditaction = $_SERVER['REQUEST_URI'];
            $aapos = strpos($auditaction, "?");
            if ($aapos !== false) {
                $auditaction = substr($auditaction, $aapos + 1);
            }
            $auditaction = $db->escape("$onlineip|#|$auditaction");
            adminaudit($self['username'], $auditaction, 0, 0, "Potential XSS exploit using newpassword");
            die("Hack atttempt recorded in audit logs.");
        }

        $curpassword = formVar('curpassword');
        $newpassword = formVar('newpassword');
        $newpasswordcf = formVar('newpasswordcf');

        if (empty($newpassword) || empty($newpasswordcf)) {
            $output = table_msg($lang['Empty_Password_Error']);
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
            redirect('usercp.php?action=password', 2.5, X_REDIRECT_JS);
            exit();
        }

        if (strlen($newpassword) < 5 && strlen($newpasswordcf) < 5) {
            $output = table_msg($lang['passwordlimits']);
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
            redirect('usercp.php?action=password', 2.5, X_REDIRECT_JS);
            exit();
        }

        if (!empty($newpassword) && !empty($newpasswordcf)) {
            if ($newpassword != $newpasswordcf) {
                $output = table_msg($lang['pwnomatch']);
                eval('echo stripslashes("' . template('usercp_home_layout') . '");');
                redirect('usercp.php?action=password', 2.5, X_REDIRECT_JS);
                exit();
            }

            $curpassword = md5($curpassword);
            $curpwq = $db->query("SELECT password FROM " . X_PREFIX . "members WHERE uid = '" . $self['uid'] . "'");
            $curpwdata = $db->fetch_array($curpwq);
            $db->free_result($curpwq);

            if ($curpassword != $curpwdata['password']) {
                $output = table_msg($lang['pwcurincorrect']);
                eval('echo stripslashes("' . template('usercp_home_layout') . '");');
                redirect('usercp.php?action=password', 2.5, X_REDIRECT_JS);
                exit();
            }

            $newpassword = md5($newpassword);

            $pwtxt = "password = '$newpassword'";

            $db->query("UPDATE " . X_PREFIX . "members SET $pwtxt WHERE uid = '" . $self['uid'] . "'");

            $currtime = $onlinetime + (86400 * 30);
            $output = table_msg($lang['passwordsuccess']);
            eval('echo stripslashes("' . template('usercp_home_layout') . '");');
            $authC->logout('index.php', 2.5);
            exit();
        }

        $output = table_msg($lang['usercpeditpromsg']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php', 2.5, X_REDIRECT_JS);
    }

    function viewSignature()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;
        global $bbcode_js, $js_path;

        $bbcodeinsert = bbcodeinsert();
        $smilieinsert = smilieinsert();

        $member = $self;

        if ($CONFIG['sigbbcode'] == 'on') {
            $bbcodeis = '<strong>' . $lang['texton'] . '</strong>';
        } else {
            $bbcodeis = '<strong>' . $lang['textoff'] . '</strong>';
        }

        $sigblock = '';
        if (!empty($self['sig'])) {
            $self['sig'] = censor($self['sig']);
            $self['sig'] = postify($self['sig'], 'no', 'no', 'yes', $CONFIG['sigbbcode'], 'yes');
            eval('$sigblock = "' . template('usercp_sig_preview') . '";');
        }

        $numsig = str_replace("\r\n", "\n", $member['sig']);
        $numsig = str_replace("\r", "\n", $numsig);

        $numofchars = strlen($numsig);

        eval('$output = "' . template('usercp_signature') . '";');
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
    }

    /**
     * submitSignature() - process signature submission
     *
     * User has changed their signature.
     *
     * @return does not return if error, redirects on success
     */
    function submitSignature()
    {
        global $db, $THEME, $lang, $CONFIG, $self;
        global $shadow2, $menu;

        reset($self);

        if (empty($self['username'])) {
            error($lang['badname'], false);
        }

        // Grab the current member's uid, and use it to populate some fields
        $member = new member($self['uid']);

        // Check that we populated the object correctly
        if ($member->record['uid'] !== $self['uid']) {
            error($lang['badname'], false);
        }

        $sig = $db->escape(formVar('newsig'), -1, true);

        if (!empty($sig)) {
            $sig_patterns = array(
                '#[img]((ht|f)tp://)([^\r\n\t<"]*?)[/img]#si',
                '#[url=([a-z0-9]+://)([\w\-]+.([\w\-]+.)*[\w]+(:[0-9]+)?(/[^ "\n\r\t<]*?)?)](.*?)[/url]#si'
            );
            $sig_replacements = array(
                "",
                "\\6"
            );

            for ($i = 0; $i < count($sig_patterns); $i++) {
                if (preg_match($sig_patterns[$i], $sig)) {
                    $sig_replace = preg_replace($sig_patterns[$i], $sig_replacements[$i], $sig);
                }
            }
            if (strlen($_POST['newsig']) > $CONFIG['maxsigchars']) {
                error($lang['signature_too_long'], false);
            }
        }

        if ($CONFIG['resetsig'] == 'on') { // reset signatures in all posts (may take a while, but this query doesn't get used often)
            if (empty($sig)) {
                $db->query("UPDATE " . X_PREFIX . "posts SET usesig = 'no' WHERE author = '" . $self['username'] . "'");
                $db->query("UPDATE " . X_PREFIX . "members SET sig = '' WHERE uid = '" . $self['uid'] . "'");
            } else {
                $db->query("UPDATE " . X_PREFIX . "members SET sig = '$sig' WHERE uid = '" . $self['uid'] . "'");
                $db->query("UPDATE " . X_PREFIX . "posts SET usesig = 'yes' WHERE author = '" . $self['username'] . "'");
            }
        } else { // Do not reset signatures, just change it
            if (empty($sig)) {
                $db->query("UPDATE " . X_PREFIX . "members SET sig = '' WHERE uid = '" . $self['uid'] . "'");
            } else {
                $db->query("UPDATE " . X_PREFIX . "members SET sig = '$sig' WHERE uid = '" . $self['uid'] . "'");
            }
        }

        $output = table_msg($lang['usercpeditpromsg']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php', 2.5, X_REDIRECT_JS);
    }

    function viewAvatar()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;

        $member = $self;

        $avatar = '';
        if ($CONFIG['avastatus'] == 'on') {
            eval('$avatar = "' . template('usercp_avatarurl') . '";');
        }

        $useravatar = $avdeletebutton = $avatarhidden = '';
        if ($CONFIG['avatar_whocanupload'] != 'off' || $CONFIG['avastatus'] == 'on') {
            if (!empty($member['avatar'])) {
                eval('$useravatar = "' . template('usercp_avataruser') . '";');
                $avdeletebutton = '<br /><input type="checkbox" name="avatardel" value="1" />' . $lang['Avatar_Delete'] . '';
            }

            $avatarhidden = '';
            switch ($CONFIG['avatar_whocanupload']) {
                case 'all':
                    eval('$avatarhidden = "' . template('usercp_avatarhidden') . '";');
                    break;
                case 'staff':
                    eval('$avatarhidden = "' . template('usercp_avatarhidden') . '";');
                    break;
                default:
                    $avatarhidden = '';
                    break;
            }
        }

        if ($CONFIG['avastatus'] == 'on' || $CONFIG['avatar_whocanupload'] != 'off') {
            eval('$avatardelbtn = "' . template('usercp_avatarsubmit') . '";');
        } else {
            eval('$avatardelbtn = "' . template('usercp_avatarnone') . '";');
        }

        eval('$output = "' . template('usercp_avatar') . '";');
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
    }

    function submitAvatar()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;

        reset($self);

        if (empty($self['username'])) {
            error($lang['badname'], false);
        }

        // Grab the current member's uid, and use it to populate some fields
        $member = new member($self['uid']);

        // Check that we populated the object correctly
        if ($member->record['uid'] !== $self['uid']) {
            error($lang['badname'], false);
        }

        if (isset($_POST['newavatar'])) {
            if ('http' == substr($_POST['newavatar'], 0, 4)) {
                // (raw)urlencode() creates a mess. We're gonna kill any bad urls with escape(), so no worries.
                $_POST['newavatar'] = str_replace(' ', '%20', $_POST['newavatar']);
            }
        } else {
            $_POST['newavatar'] = '';
        }

        $avatar = '';
        if (!isset($_FILES['avatarfile']['name']) || !$_FILES['avatarfile']['tmp_name'] || empty($_FILES['avatarfile']['name'])) {

            $avatar = $db->escape(formVar('newavatar'), -1, true);

            $max_size = explode('x', $CONFIG['max_avatar_size']);
            if ($max_size[0] > 0 && $max_size[1] > 0 && substr_count($avatar, ',') < 2) {
                $size = @getimagesize($avatar);
                if ($size === false) {
                    error($lang['pic_not_located'], false);
                } else
                    if (($size[0] > $max_size[0] && $max_size[0] > 0) || ($size[1] > $max_size[1] && $max_size[1] > 0) && !X_ADMIN) {
                        error($lang['avatar_too_big'] . $CONFIG['max_avatar_size'] . $lang['Avatar_Pixels'], false);
                    }
            }
        }

        if (isset($_COOKIE['avatarfile']) || isset($_POST['avatarfile']) || isset($_GET['avatarfile'])) {
            die('Action Halted Due To Illegal Acivity!!');
            exit();
        }

        if (isset($_FILES['avatarfile']['name']) && $_FILES['avatarfile']['tmp_name'] && !empty($_FILES['avatarfile']['name'])) {
            $avatarext = substr($_FILES['avatarfile']['name'], strlen($_FILES['avatarfile']['name']) - 3, 3);
            $newavatarname = $member->record['uid'] . '.' . $onlinetime . '.' . $avatarext;
            $check = $_FILES['avatarfile'];

            $CONFIG['avatar_filesize'] = (int)$CONFIG['avatar_filesize'];
            if (($check['size'] > $CONFIG['avatar_filesize']) && !X_ADMIN) {
                error($lang['avatar_too_big'] . $CONFIG['avatar_filesize'] . $lang['Avatar_Bytes'], false);
            }

            $avatarpath = $CONFIG['avatar_path'] . '/' . $newavatarname;
            $tmppath = $check['tmp_name'];

            if (!preg_match('gif|jpeg|png|jpg|bmp', $avatarext)) {
                error($lang['avatar_invalid_ext'], false);
            }

            if (!is_writable($CONFIG['avatar_path'])) {
                error($lang['avatar_nowrite'], false);
            }

            $size = getimagesize($tmppath);
            $width = $size[0];
            $height = $size[1];
            $type = $size[2];

            if (!((bool)ini_get('safe_mode'))) {
                set_time_limit(30);
            }

            $imginfo = getimagesize($tmppath);
            $type = $imginfo[2];

            switch ($type) {
                case IMAGETYPE_GIF:
                    if (!function_exists('imagecreatefromgif')) {
                        return $tmppath;
                    }
                    $srcImage = imagecreatefromgif($tmppath);
                    break;
                case IMAGETYPE_JPEG:
                    if (!function_exists('imagecreatefromjpeg')) {
                        return $tmppath;
                    }
                    $srcImage = imagecreatefromjpeg($tmppath);
                    break;
                case IMAGETYPE_PNG:
                    if (!function_exists('imagecreatefrompng')) {
                        return $tmppath;
                    }
                    $srcImage = imagecreatefrompng($tmppath);
                    break;
                case IMAGETYPE_WBMP:
                    if (!function_exists('imagecreatefromwbmp')) {
                        return $tmppath;
                    }
                    $srcImage = imagecreatefromwbmp($tmppath);
                    break;
                default:
                    return $tmppath;
            }

            if ($width > $CONFIG['avatar_max_width']) {
                $newwidth = $CONFIG['avatar_new_width'];
                $newheight = ($newwidth / $width) * $height;
            } else
                if ($height > $CONFIG['avatar_max_height']) {
                    $newheight = $CONFIG['avatar_new_height'];
                    $newwidth = ($newheight / $height) * $width;
                }

            if (isset($newwidth)) {
                $destImage = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                switch ($type) {
                    case IMAGETYPE_GIF:
                        imagegif($destImage, $tmppath);
                        break;
                    case IMAGETYPE_JPEG:
                        imagejpeg($destImage, $tmppath);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($destImage, $tmppath);
                        break;
                    case IMAGETYPE_WBMP:
                        imagewbmp($destImage, $tmppath);
                        break;
                }
                imagedestroy($srcImage);
                imagedestroy($destImage);
            }

            copy($tmppath, $avatarpath);
            $db->query("UPDATE " . X_PREFIX . "members SET avatar = '$avatarpath' WHERE uid = '" . $self['uid'] . "'");
        }

        if (isset($_POST['newavatar']) && empty($_FILES['avatarfile']['name'])) {
            $db->query("UPDATE " . X_PREFIX . "members SET avatar = '$avatar' WHERE uid = '" . $self['uid'] . "'");
        }

        if (onSubmit('avatarsubmit') && isset($_POST['avatardel']) != 1 && empty($_POST['newavatar']) && empty($_FILES['avatarfile']['name'])) {
            $db->query("UPDATE " . X_PREFIX . "members SET avatar = '$self[avatar]' WHERE uid = '" . $self['uid'] . "'");
        }

        if (isset($_POST['avatardel']) && isset($_POST['avatardel']) == 1 && empty($_FILES['avatarfile']['name'])) {
            if (file_exists($member->record['avatar'])) {
                unlink($member->record['avatar']);
            }
            $db->query("UPDATE " . X_PREFIX . "members SET avatar = '' WHERE uid = '" . $self['uid'] . "'");
        }

        $output = table_msg($lang['Avatar_Updated']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php?action=avatar', 2.5, X_REDIRECT_JS);
    }

    function viewAvatarGallery()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;

        $avatarfolder = $CONFIG['avgalpath'];

        $avatars = $avatarname = $avatar = $submitbutton = '';

        $total = getInt('total');
        $page = getInt('page');
        $type = getRequestVar('type');

        $multipage = '';

        if ($page < 1) {
            $page = 1;
        }

        if (!($dirf = @opendir($avatarfolder))) {
            error($lang['folderdoesnotexist'], false, '', '', 'usercp.php');
        }

        while (false !== ($file = readdir($dirf))) {
            $avatardir[] = $file;
        }
        closedir($dirf);
        sort($avatardir);
        $totalf = count($avatardir);
        $subfolders = '';
        for ($i = 1; $i < $totalf; $i++) {
            if ($avatardir[$i] != '.' && $avatardir[$i] != '..' && is_dir($avatarfolder . '/' . $avatardir[$i])) {
                if ($type == $avatardir[$i]) {
                    $folderimg = 'openav.gif';
                    $pre = '<strong>';
                    $suf = '</strong>';
                } else {
                    $folderimg = 'closeav.gif';
                    $pre = $suf = '';
                }
                $subfolders .= '<br />&nbsp;&nbsp;&nbsp;<img src="' . $THEME['imgdir'] . '/' . $folderimg . '" border="0px" alt="' . $lang['altfolder'] . '" title="' . $lang['altfolder'] . '" /> <a href="usercp.php?action=gallery&amp;type=' . $avatardir[$i] . '">' . $pre . '' . $avatardir[$i] . '' . $suf . '</a>';
            }
        }

        if ($type) {
            // Only allow directories within our Avatar Gallery
            $pattern = "/^[A-Za-z0-9_\s]*$/i";
            if (preg_match($pattern, $type) == 1) {
                $avatarfolder .= '/' . $type . '/';

                if (!($dir = @opendir($avatarfolder))) {
                    error($lang['folderdoesnotexist'], false, '', '', 'usercp.php');
                }

                while (false !== ($file = readdir($dir))) {
                    $size = filesize($avatarfolder . '' . $file);
                    $size = round(($size / 100), 1);
                    $ext = strtolower(substr(strrchr($file, '.'), 1));
                    if ($file != '.' && $file != '..' && $file != 'index.html' && !is_dir($file) && (($ext == 'jpg' || $ext == 'gif' || $ext == 'png' || $ext == 'bmp' || $ext == 'jpeg') && $size > 1)) {
                        $avatarname[] = $file;
                    }
                }

                if (!empty($avatarname)) {
                    closedir($dir);
                    sort($avatarname);
                    $totalc = count($avatarname);

                    if (isset($page)) {
                        // if ($page < 1) {
                        //     $page = 1;
                        // }
                        $start_limit = ($page - 1) * $CONFIG['avatars_perpage'];
                        if (($page * $CONFIG['avatars_perpage']) > $totalc) {
                            $end_limit = $start_limit + ($totalc - (($page - 1) * $CONFIG['avatars_perpage']));
                        } else {
                            $end_limit = $start_limit + $CONFIG['avatars_perpage'];
                        }
                    } else {
                        $start_limit = 0;
                        if ($CONFIG['avatars_perpage'] > $totalc) {
                            $end_limit = $totalc;
                        } else {
                            $end_limit = $start_limit + $CONFIG['avatars_perpage'];
                        }
                        $page = 1;
                    }

                    $mpurl = 'usercp.php?action=gallery&amp;type=' . $type;

                    if (($multipage = multi($totalc, $CONFIG['avatars_perpage'], $page, $mpurl)) !== false) {
                        eval('$multipage = "' . template('usercp_gallery_multipage') . '";');
                    }

                    $listed_avatars = $frc = 0;
                    for ($a = $start_limit; $a < $end_limit; $a++) {
                        $size = filesize($avatarfolder . '/' . $avatarname[$a]);
                        $size = round(($size / 1024), 1) . 'kb';

                        if ($listed_avatars == 0) {
                            $avatars .= '<tr>';
                            $avatars .= "\n";
                            $frc++;
                        }

                        $avatars .= '<td class="ctrtablerow"><img src="' . $avatarfolder . '' . $avatarname[$a] . '" border="0px" alt="' . $avatarname[$a] . '" title="' . $avatarname[$a] . '" /><br /><font class="smalltxt">' . $size . '</font><br /><input type="radio" name="avataricon" value="' . $avatarfolder . '' . $avatarname[$a] . '" /></td>';
                        $avatars .= "\n";

                        $listed_avatars += 1;
                        if ($listed_avatars == $CONFIG['avatars_perrow']) {
                            $avatars .= '</tr>';
                            $avatars .= "\n";
                            $listed_avatars = 0;
                        }
                        $total = count($avatarname[$a]);
                    }

                    for ($z = 0; $z < (($frc * $CONFIG['avatars_perrow']) - ($end_limit - $start_limit)); $z++) {
                        $avatars .= '<td class="ctrtablerow">&nbsp;</td>';
                        $avatars .= "\n";
                    }
                }
            } else {
                // Invalid directory, can't get here without tampering
                global $onlineip;

                $auditaction = $_SERVER['REQUEST_URI'];
                $aapos = strpos($auditaction, "?");
                if ($aapos !== false) {
                    $auditaction = substr($auditaction, $aapos + 1);
                }
                $auditaction .= ", Potential exploit using avatar gallery directory path";
                $auditaction = $db->escape("$onlineip|#|$auditaction");
                adminaudit($self['username'], $auditaction, 0, 0);
                die("Hack atttempt recorded in audit logs.");
            }
        }

        if (!empty($self['avatar'])) {
            $avatar = '<br />' . $lang['currentavatarmsg'] . '<br /><br /><img src="' . $self['avatar'] . '" border="0px" alt="' . $lang['altavatar'] . '" title="' . $lang['altavatar'] . '" /><br />';
        } else {
            $avatar = '<br />' . $lang['nocurrentavatarmsg'] . '<br /><br /><img src="images/no_avatar.gif" border="0px" alt="' . $lang['altnoavatar'] . '" title="' . $lang['altnoavatar'] . '" /><br />';
        }

        if (empty($type) && $total == 0) {
            $avatars .= '<td class="ctrtablerow"><br />' . $lang['welcomeavatarmsg'] . '<br />' . $avatar . '<br />' . $lang['navigateavatarmsg'] . '<br /><br /></td></tr>';
            $avatars .= "\n";
        }

        if ($total > 0) {
            $submitbutton = '<tr><td class="ctrtablerow" bgcolor="' . $THEME['altbg2'] . '" colspan="2"><input type="submit" name="avatarsubmit" value="' . $lang['updateavatar'] . '" /></td></tr>';
        } else
            if ($total < 0) {
                error($lang['noavatarsinfolder'], false);
            }
        eval('$output = "' . template('usercp_gallery') . '";');
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
    }

    function submitAvatarGallery()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu, $avatar;

        if (empty($_POST['avataricon'])) {
            error($lang['noavatarselected'], false);
        }

        reset($self);

        if (empty($self['username'])) {
            error($lang['badname'], false);
        }

        // Grab the current member's uid, and use it to populate some fields
        $member = new member($self['uid']);

        // Check that we populated the object correctly
        if ($member->record['uid'] !== $self['uid']) {
            error($lang['badname'], false);
        }

        $max_size = explode('x', $CONFIG['max_avatar_size']);
        if ($max_size[0] > 0 && $max_size[1] > 0 && substr_count($avatar, ',') < 2) {
            $size = getimagesize($avatar);
            if ($size === false) {
                $self['avatar'] = '';
            } else
                if (($size[0] > $max_size[0] && $max_size[0] > 0) || ($size[1] > $max_size[1] && $max_size[1] > 0) && !X_ADMIN) {
                    error($lang['avatar_too_big'] . $CONFIG['max_avatar_size'] . $lang['avatarpixels'], false);
                }
        }
        $db->query("UPDATE " . X_PREFIX . "members SET avatar = '$_POST[avataricon]' WHERE uid = '" . $self['uid'] . "'");
        $output = table_msg($lang['avatarupdated']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php?action=gallery', 2.5, X_REDIRECT_JS);
    }

    function submitAddFavorite($tid)
    {
        global $lang, $THEME, $menu, $shadow2, $CONFIG;

        if ($tid == 0) {
            error($lang['fnasorry'], false);
        }

        $favObj = new favorite();
        if ($favObj->exists($tid)) {
            error($lang['favonlistmsg'], false);
        }

        $favObj->dirty = true;
        $favObj->tid = intval($tid);
        $favObj->update();

        $output = table_msg($lang['favaddedmsg']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php?action=favorites', 2.5, X_REDIRECT_JS);
    }

    function viewFavorites()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;

        $query = $db->query("SELECT f.*, t.fid, t.icon, l.uid as lp_uid, l.username as lp_user, l.dateline as lp_dateline, t.subject, t.replies FROM " . X_PREFIX . "favorites f, " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid WHERE f.tid = t.tid AND f.username = '" . $self['username'] . "' AND f.type = 'favorite' ORDER BY l.dateline DESC");
        $favArray = array();
        while (($row = $db->fetch_array($query)) != false) {
            $favArray[] = $row;
        }
        $db->free_result($query);
        $favnum = 0;
        $favs = '';
        $tmOffset = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
        foreach ($favArray as $fav) {
            $query2 = $db->query("SELECT name, fup, fid FROM " . X_PREFIX . "forums WHERE fid = '$fav[fid]'");
            $forum = $db->fetch_array($query2);
            $db->free_result($query2);

            $dalast = $fav['lp_dateline'];
            $fav['lp_user'] = '<a href="viewprofile.php?memberid=' . intval($fav['lp_uid']) . '">' . trim($fav['lp_user']) . '</a>';
            $lastreplydate = gmdate($self['dateformat'], $fav['lp_dateline'] + $tmOffset);
            $lastreplytime = gmdate($self['timecode'], $fav['lp_dateline'] + $tmOffset);
            $lastpost = $lang['lastreply1'] . ' ' . $lastreplydate . ' ' . $lang['textat'] . ' ' . $lastreplytime . '<br />' . $lang['textby'] . ' ' . $fav['lp_user'];
            $fav['subject'] = stripslashes(censor($fav['subject']));

            if (!empty($fav['icon']) && file_exists($THEME['smdir'] . '/' . $fav['icon'])) {
                $fav['icon'] = '<img src="' . $THEME['smdir'] . '/' . $fav['icon'] . '" alt="' . $fav['icon'] . '" title="' . $fav['icon'] . '" border="0px" />';
            } else {
                $fav['icon'] = '';
            }

            $mouseover = celloverfx('viewtopic.php?tid=' . $fav['tid'] . '');

            $favnum++;
            eval('$favs .= "' . template('usercp_favs_row') . '";');
        }
        $db->free_result($query);

        $favsbtn = '';
        if ($favnum != 0) {
            eval('$favsbtn = "' . template('usercp_favs_button') . '";');
        }

        if ($favnum == 0) {
            eval('$favs = "' . template('usercp_favs_none') . '";');
        }

        eval('$output = "' . template('usercp_favs') . '";');
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
    }

    function submitManageFavorites()
    {
        global $lang, $THEME, $menu, $shadow2, $CONFIG;

        $favObj = new favorite();
        $favObj->deleteByFormTids();

        $output = table_msg($lang['favsdeletedmsg']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php?action=favorites', 2.5, X_REDIRECT_JS);
    }

    function viewSubscriptions()
    {
        global $db, $oToken, $lang, $THEME, $title, $CONFIG;
        global $selHTML, $self, $onlinetime, $shadow2, $menu;

        $query = $db->query("SELECT f.*, t.fid, t.icon, l.uid as lp_uid, l.username as lp_user, l.dateline as lp_dateline, t.subject, t.replies FROM " . X_PREFIX . "subscriptions f, " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid WHERE f.tid = t.tid AND f.username = '" . $self['username'] . "' AND f.type = 'subscription' ORDER BY l.dateline DESC");
        $favArray = array();
        while (($row = $db->fetch_array($query)) != false) {
            $favArray[] = $row;
        }
        $db->free_result($query);
        $subnum = 0;
        $subscriptions = '';
        $tmOffset = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
        foreach ($favArray as $fav) {
            $query2 = $db->query("SELECT name, fup, fid FROM " . X_PREFIX . "forums WHERE fid = '$fav[fid]'");
            $forum = $db->fetch_array($query2);
            $db->free_result($query2);

            $dalast = $fav['lp_dateline'];
            $fav['lp_user'] = '<a href="viewprofile.php?memberid=' . intval($fav['lp_uid']) . '">' . trim($fav['lp_user']) . '</a>';
            $lastreplydate = gmdate($self['dateformat'], $fav['lp_dateline'] + $tmOffset);
            $lastreplytime = gmdate($self['timecode'], $fav['lp_dateline'] + $tmOffset);
            $lastpost = $lang['lastreply1'] . ' ' . $lastreplydate . ' ' . $lang['textat'] . ' ' . $lastreplytime . '<br />' . $lang['textby'] . ' ' . $fav['lp_user'];
            $fav['subject'] = stripslashes(censor($fav['subject']));

            if (!empty($fav['icon']) && file_exists($THEME['smdir'] . '/' . $fav['icon'])) {
                $fav['icon'] = '<img src="' . $THEME['smdir'] . '/' . $fav['icon'] . '" alt="' . $fav['icon'] . '" title="' . $fav['icon'] . '" border="0px" />';
            } else {
                $fav['icon'] = '';
            }

            $mouseover = celloverfx('viewtopic.php?tid=' . $fav['tid'] . '');

            $subnum++;
            eval('$subscriptions .= "' . template('usercp_subscriptions_row') . '";');
        }

        $subsbtn = '';
        if ($subnum != 0) {
            eval('$subsbtn = "' . template('usercp_subscriptions_button') . '";');
        }

        if ($subnum == 0) {
            eval('$subscriptions = "' . template('usercp_subscriptions_none') . '";');
        }

        eval('$output = "' . template('usercp_subscriptions') . '";');
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
    }

    function submitAddSubscription($tid)
    {
        global $lang, $THEME, $menu, $shadow2, $CONFIG;

        if ($tid === 0) {
            error($lang['fnasorry'], false);
        }

        $subObj = new subscription();
        if ($subObj->exists($tid)) {
            error($lang['subonlistmsg'], false);
        }

        $subObj->tid = intval($tid);
        $subObj->dirty = true;
        $subObj->update();

        $output = table_msg($lang['subaddedmsg']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php?action=subscriptions', 2.5, X_REDIRECT_JS);
    }

    function submitManageSubscriptions()
    {
        global $lang, $THEME, $menu, $shadow2, $CONFIG;

        $subObj = new subscription();
        $subObj->deleteByFormTids();

        $output = table_msg($lang['subsdeletedmsg']);
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
        redirect('usercp.php?action=subscriptions', 2.5, X_REDIRECT_JS);
    }

    function viewUserCP()
    {
        global $db, $THEME, $title, $theme, $lang, $CONFIG;
        global $selHTML, $self, $shadow2, $menu;

        $query = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE uid = '" . $self['uid'] . "'");
        $member = $db->fetch_array($query);
        $db->free_result($query);

        $limit = "posts <= '$member[postnum]' AND title != 'Super Administrator' AND title != 'Administrator' AND title != 'Super Moderator' AND title != 'Super Moderator' AND title != 'Moderator'";
        switch ($member['status']) {
            case 'Administrator':
                $limit = "title = '$member[status]'";
                break;
            case 'Super Administrator':
                $limit = "title = '$member[status]'";
                break;
            case 'Super Moderator':
                $limit = "title = '$member[status]'";
                break;
            case 'Moderator':
                $limit = "title = '$member[status]'";
                break;
            default:
                $limit = "posts <= '$member[postnum]' AND title != 'Super Administrator' AND title != 'Administrator' AND title != 'Super Moderator' AND title != 'Super Moderator' AND title != 'Moderator'";
                break;
        }
        $rank = $db->fetch_array($db->query("SELECT * FROM " . X_PREFIX . "ranks WHERE $limit ORDER BY posts DESC LIMIT 1"));

        $allowavatars = $rank['allowavatars'];

        $star = 'star.gif';
        switch ($member['status']) {
            case 'Moderator':
                $star = 'star_mod.gif';
                break;
            case 'Super Moderator':
                $star = 'star_supmod.gif';
                break;
            case 'Administrator':
                $star = 'star_admin.gif';
                break;
            case 'Super Administrator':
                $star = 'star_supadmin.gif';
                break;
            default:
                $star = 'star.gif';
                break;
        }
        $stars = str_repeat('<img src="' . $THEME['imgdir'] . '/' . $star . '" alt="*" title="*" border="0px" />', $rank['stars']) . '<br />';

        $icon = $pre = $suff = '';
        switch ($member['status']) {
            case 'Super Administrator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_supadmin.gif" alt="' . $lang['ranksupadmin'] . '" title="' . $lang['ranksupadmin'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                    $suff = '</em></u></strong></span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['spacolor'] . '"><strong><u><em>';
                    $suff = '</em></u></strong></span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                }
                break;
            case 'Administrator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_admin.gif" alt="' . $lang['rankadmin'] . '" title="' . $lang['rankadmin'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                    $suff = '</u></strong></span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['admcolor'] . '"><strong><u>';
                    $suff = '</u></strong></span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                }
                break;
            case 'Super Moderator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_supmod.gif" alt="' . $lang['ranksupmod'] . '" title="' . $lang['ranksupmod'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                    $suff = '</strong></em></span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['spmcolor'] . '"><em><strong>';
                    $suff = '</strong></em></span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                }
                break;
            case 'Moderator':
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_mod.gif" alt="' . $lang['rankmod'] . '" title="' . $lang['rankmod'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                    $suff = '</strong></span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['modcolor'] . '"><strong>';
                    $suff = '</strong></span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                }
                break;
            default:
                if ($THEME['riconstatus'] == 'on') {
                    $icon = '<img src="' . $THEME['ricondir'] . '/online_mem.gif" alt="' . $lang['rankmem'] . '" title="' . $lang['rankmem'] . '" border="0px" />';
                    $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                    $suff = '</span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                } else {
                    $icon = '';
                    $pre = '<span style="color:' . $THEME['memcolor'] . '">';
                    $suff = '</span>';
                    $member['username'] = $icon . '' . $pre . '' . $member['username'] . '' . $suff;
                }
                break;
        }

        if ($allowavatars == 'no') {
            $member['avatar'] = '<img src="images/no_avatar.gif" alt="' . $lang['altnoavatar'] . '" title="' . $lang['altnoavatar'] . '" border="0px" />';
        }

        if (isset($rank['avatarrank']) && !empty($rank['avatarrank'])) {
            $rank['avatarrank'] = '<img src="' . $rank['avatarrank'] . '" alt="' . $lang['Rank_Avatar_Alt'] . '" title="' . $lang['Rank_Avatar_Alt'] . '" border="0px" />';
        } else {
            $rank['avatarrank'] = '';
        }

        $showtitle = (!empty($rank['title'])) ? stripslashes($rank['title']) . '<br />' : '';
        $customstatus = (!empty($member['customstatus'])) ? stripslashes(censor($member['customstatus'])) . '<br />' : '';

        if (!empty($member['avatar']) && $allowavatars != 'no') {
            $member['avatar'] = censor($member['avatar']);
            $member['avatar'] = stripslashes($member['avatar']);
            $member['avatar'] = '<img src="' . $member['avatar'] . '" alt="' . $lang['altavatar'] . '" title="' . $lang['altavatar'] . '" border="0px" />';
        } else {
            $member['avatar'] = '<img src="images/no_avatar.gif" alt="' . $lang['altnoavatar'] . '" title="' . $lang['altnoavatar'] . '" border="0px" />';
        }

        if (!empty($member['mood'])) {
            $member['mood'] = censor($member['mood']);
            $member['mood'] = postify($member['mood'], 'no', 'no', 'yes', 'yes', false, 'yes', 'yes');
        } else {
            $member['mood'] = '';
        }

        if (!empty($member['photo'])) {
            $member['photo'] = stripslashes($member['photo']);
            $member['photo'] = censor($member['photo']);
            $member['photo'] = '<img src="' . $member['photo'] . '" alt="' . $lang['photoalt'] . '" title="' . $lang['photoalt'] . '" border="0px" />';
        } else {
            $member['photo'] = '<img src="images/no_avatar.gif" alt="' . $lang['altnophoto'] . '" title="' . $lang['altnophoto'] . '" border="0px" />';
        }

        $listquickthemes = array();
        $query = $db->query("SELECT themeid, name FROM " . X_PREFIX . "themes WHERE themestatus = 'on' ORDER BY name ASC");
        $quickthemes = '';
        while (($qt = $db->fetch_array($query)) != false) {
            if ($theme == $qt['themeid']) {
                $listquickthemes[] = '<option value="' . $qt['themeid'] . '" ' . $selHTML . '>' . stripslashes($qt['name']) . '</option>';
            } else {
                $listquickthemes[] = '<option value="' . $qt['themeid'] . '">' . stripslashes($qt['name']) . '</option>';
            }
        }
        $listquickthemes = implode("\n", $listquickthemes);
        eval('$quickthemes = "' . template('usercp_home_themes') . '";');
        $db->free_result($query);

        $pmblock = '';
        if (!($CONFIG['pmstatus'] == 'off' && isset($self['status']) && $self['status'] == 'Member')) {
            $query = $db->query("SELECT * FROM " . X_PREFIX . "pm WHERE owner = '$self[username]' AND folder = 'Inbox' AND readstatus = 'no' ORDER BY dateline DESC LIMIT 0,5");
            $msgArray = array();
            while (($row = $db->fetch_array($query)) != false) {
                $msgArray[] = $row;
            }
            $db->free_result($query);
            $pmnum = count($msgArray);
            $messages = '';
            $tmOffset = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
            foreach ($msgArray as $message) {
                $postdate = gmdate($self['dateformat'], $message['dateline'] + $tmOffset);
                $posttime = gmdate($self['timecode'], $message['dateline'] + $tmOffset);
                $senton = $lang['lastreply1'] . ' ' . $postdate . ' ' . $lang['textat'] . ' ' . $posttime;

                if (empty($message['subject'])) {
                    $message['subject'] = $lang['textnosub'];
                }

                if ($message['readstatus'] == 'yes') {
                    $read = $lang['textread'];
                } else {
                    $read = $lang['textunread'];
                }

                $message['msgfrom'] = '<a href="viewprofile.php?member=' . rawurlencode($message['msgfrom']) . '">' . trim($message['msgfrom']) . '</a>';

                $message['subject'] = stripslashes($message['subject']);
                $message['subject'] = censor($message['subject']);

                $mouseover = celloverfx('pm.php?action=view&amp;pmid=' . $message['pmid'] . '');

                eval('$messages .= "' . template('usercp_home_pm_row') . '";');
            }

            if ($pmnum == 0) {
                eval('$messages = "' . template('usercp_home_pm_none') . '";');
            }
            eval('$pmblock = "' . template('usercp_home_pm') . '";');
        }

        $query = $db->query("SELECT f.*, t.*, p.*, l.uid as lp_uid, l.username as lp_user, l.dateline as lp_dateline FROM " . X_PREFIX . "favorites f, " . X_PREFIX . "threads t, " . X_PREFIX . "posts p, " . X_PREFIX . "lastposts l WHERE l.tid = t.tid AND f.tid = t.tid AND p.tid = t.tid AND p.subject = t.subject AND f.username = '" . $self['username'] . "' AND f.type = 'favorite' ORDER BY l.dateline DESC LIMIT 0,5");
        $favArray = array();
        while (($row = $db->fetch_array($query)) != false) {
            $favArray[] = $row;
        }
        $db->free_result($query);
        $favnum = count($favArray);
        $favs = '';
        $tmOffset = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
        foreach ($favArray as $fav) {
            $query = $db->query("SELECT name, fup, fid FROM " . X_PREFIX . "forums WHERE fid = '$fav[fid]'");
            $forum = $db->fetch_array($query);

            $dalast = $fav['lp_dateline'];
            $fav['lp_user'] = '<a href="viewprofile.php?memberid=' . intval($fav['lp_uid']) . '">' . trim($fav['lp_user']) . '</a>';
            $lastreplydate = gmdate($self['dateformat'], $fav['lp_dateline'] + $tmOffset);
            $lastreplytime = gmdate($self['timecode'], $fav['lp_dateline'] + $tmOffset);
            $lastpost = $lang['lastreply1'] . ' ' . $lastreplydate . ' ' . $lang['textat'] . ' ' . $lastreplytime . '<br />' . $lang['textby'] . ' ' . $fav['lp_user'];
            $fav['subject'] = stripslashes(censor($fav['subject']));

            if (!empty($fav['icon']) && file_exists($THEME['smdir'] . '/' . $fav['icon'])) {
                $fav['icon'] = '<img src="' . $THEME['smdir'] . '/' . $fav['icon'] . '" alt="' . $fav['icon'] . '" title="' . $fav['icon'] . '" border="0px" />';
            } else {
                $fav['icon'] = '';
            }

            $mouseover = celloverfx('viewtopic.php?tid=' . $fav['tid'] . '');

            eval('$favs .= "' . template('usercp_home_favs_row') . '";');
        }

        if ($favnum == 0) {
            eval('$favs = "' . template('usercp_home_favs_none') . '";');
        }

        $query = $db->query("SELECT f.*, t.fid, t.icon, l.uid as lp_uid, l.username as lp_user, l.dateline as lp_dateline, t.subject, t.replies FROM " . X_PREFIX . "subscriptions f, " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid WHERE f.tid = t.tid AND f.username = '" . $self['username'] . "' AND f.type = 'subscription' ORDER BY l.dateline DESC LIMIT 0,5");
        $favArray = array();
        while (($row = $db->fetch_array($query)) != false) {
            $favArray[] = $row;
        }
        $db->free_result($query);
        $subnum = 0;
        $subscriptions = '';
        $tmOffset = ($self['timeoffset'] * 3600) + $self['daylightsavings'];
        foreach ($favArray as $fav) {
            $query4 = $db->query("SELECT name, fup, fid FROM " . X_PREFIX . "forums WHERE fid = '$fav[fid]'");
            $forum = $db->fetch_array($query4);
            $db->free_result($query4);

            $dalast = $fav['lp_dateline'];
            $fav['lp_user'] = '<a href="viewprofile.php?memberid=' . intval($fav['lp_uid']) . '">' . trim($fav['lp_user']) . '</a>';
            $lastreplydate = gmdate($self['dateformat'], $fav['lp_dateline'] + $tmOffset);
            $lastreplytime = gmdate($self['timecode'], $fav['lp_dateline'] + $tmOffset);
            $lastpost = $lang['lastreply1'] . ' ' . $lastreplydate . ' ' . $lang['textat'] . ' ' . $lastreplytime . '<br />' . $lang['textby'] . ' ' . $fav['lp_user'];
            $fav['subject'] = stripslashes(censor($fav['subject']));

            if (!empty($fav['icon']) && file_exists($THEME['smdir'] . '/' . $fav['icon'])) {
                $fav['icon'] = '<img src="' . $THEME['smdir'] . '/' . $fav['icon'] . '" alt="' . $fav['icon'] . '" title="' . $fav['icon'] . '" border="0px" />';
            } else {
                $fav['icon'] = '';
            }

            $mouseover = celloverfx('viewtopic.php?tid=' . $fav['tid'] . '');

            $subnum++;
            eval('$subscriptions .= "' . template('usercp_home_subscriptions_row') . '";');
        }

        if ($subnum == 0) {
            eval('$subscriptions = "' . template('usercp_home_subscriptions_none') . '";');
        }

        eval('$output = "' . template('usercp_home') . '";');
        eval('echo stripslashes("' . template('usercp_home_layout') . '");');
    }
}
