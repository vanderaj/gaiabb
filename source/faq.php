<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2020 The XMB Development Team
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
define('CACHECONTROL', 'public');

require_once 'header.php';
require_once ROOT . 'class/csrf.class.php';

loadtpl('error_nologinsession');

$meta = metaTags();
$shadow = shadowfx();
smcwcache();

$page = getVar('page');
$faqid = getInt('faqid');
$flyto = getVar('flyto');

if ($faqid < 0) {
    $faqid = 0;
}

$faq = $rankrows = $smilierows = $stars = '';

eval('$css = "' . template('css') . '";');

eval('$header = "' . template('header') . '";');

if ($CONFIG['faqstatus'] != 'on' && $page == '') {
    error($lang['fnasorry']);
}

switch ($page) {
    case 'usermaint':
        $fquery = $db->query("SELECT fid FROM " . X_PREFIX . "faq WHERE code = 'usermaint'");
        $theid = $db->result($fquery, 0);
        $db->freeResult($fquery);
        redirect("faq.php?faqid=" . $theid, 0);
        nav('<a href="faq.php">' . $lang['textfaq'] . '</a>');
        nav($lang['textuserman']);
        btitle($lang['textfaq']);
        btitle($lang['textuserman']);
        break;
    case 'using':
        $fquery = $db->query("SELECT fid FROM " . X_PREFIX . "faq WHERE code = 'using'");
        $theid = $db->result($fquery, 0);
        $db->freeResult($fquery);
        redirect("faq.php?faqid=" . $theid, 0);
        nav('<a href="faq.php">' . $lang['textfaq'] . '</a>');
        nav($lang['textuseboa']);
        btitle($lang['textfaq']);
        btitle($lang['textuseboa']);
        break;
    case 'messages':
        $fquery = $db->query("SELECT fid FROM " . X_PREFIX . "faq WHERE code = 'messages'");
        $theid = $db->result($fquery, 0);
        $db->freeResult($fquery);
        redirect("faq.php?faqid=" . $theid, 0);
        nav('<a href="faq.php">' . $lang['textfaq'] . '</a>');
        nav($lang['textpostread']);
        btitle($lang['textfaq']);
        btitle($lang['textpostread']);
        break;
    case 'misc':
        $fquery = $db->query("SELECT fid FROM " . X_PREFIX . "faq WHERE code = 'misc'");
        $theid = $db->result($fquery, 0);
        $db->freeResult($fquery);
        redirect("faq.php?faqid=" . $theid, 0);
        nav('<a href="faq.php">' . $lang['textfaq'] . '</a>');
        nav($lang['textmiscfaq']);
        btitle($lang['textfaq']);
        btitle($lang['textmiscfaq']);
        break;
    case 'forumrules':
        nav();
        nav($lang['textbbrules']);
        btitle();
        btitle($lang['textbbrules']);
        break;
    case 'noadmin':
        nav();
        nav($lang['error']);
        btitle();
        btitle($lang['error']);
        break;
    case 'agreerules':
        nav();
        nav($lang['textbbrules']);
        btitle();
        btitle($lang['textbbrules']);
        break;
    default:
        nav('<a href="faq.php">' . $lang['textfaq'] . '</a>');
        btitle($lang['textfaq']);
        break;
}

switch ($page) {
    case 'noadmin':
        eval('$faq = "' . template('error_nologinsession') . '";');
        $faq .= '<br />';
        break;

    case 'forumrules':
        $rquery = $db->query("SELECT * FROM " . X_PREFIX . "faq WHERE type = 'rulesset' LIMIT 0, 1");
        $orules = $db->fetchArray($rquery);
        $db->freeResult($rquery);

        $faq .= '
        <form method="post" action="faq.php?page=agreerules">
        <input type="hidden" name="csrf_token" value="$oToken->createToken()" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="' . $THEME['tablewidth'] . '" align="center">
        <tr>
        <td bgcolor="' . $THEME['bordercolor'] . '"><table border="0px" cellspacing="' . $THEME['borderwidth'] . '" cellpadding="' . $THEME['tablespace'] . '" width="100%">
        <tr class="category">
        <td class="title">' . $lang['textbbrules'] . '</td>
        </tr>';
        if (!empty($CONFIG['bbrulestxt'])) {
            $CONFIG['bbrulestxt'] = stripslashes(stripslashes($CONFIG['bbrulestxt']));
            $therules = postify($CONFIG['bbrulestxt'], '', '', $orules['allowsmilies'], $orules['allowbbcode'], $orules['allowimgcode']);
            $faq .= '<tr>
            <td width="100%" class="tablerow" bgcolor="' . $THEME['altbg1'] . '">' . stripslashes($therules) . '</td>
            </tr>';
        } else {
            $faq .= '<tr><td width="100%" class="ctrtablerow" bgcolor="' . $THEME['altbg2'] . '">' . $lang['textnone'] . '</td></tr>';
        }
        $faq .= '</table></td></tr></table></form>' . $shadow . '<br />';
        break;

    case 'agreerules':
        $faq .= '
        <form method="post" action="faq.php?page=agreerules">
        <input type="hidden" name="csrf_token" value="$oToken->createToken()" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="' . $THEME['tablewidth'] . '" align="center">
        <tr>
        <td bgcolor="' . $THEME['bordercolor'] . '"><table border="0px" cellspacing="' . $THEME['borderwidth'] . '" cellpadding="' . $THEME['tablespace'] . '" width="100%">
        <tr class="category">
        <td class="title">' . $lang['textbbrules'] . '</td>
        </tr>';
        if (noSubmit('agreesubmit')) {
            $ref = $flyto != '' ? $flyto : 'index.php';
            $rquery = $db->query("SELECT * FROM " . X_PREFIX . "faq WHERE type = 'rulesset' LIMIT 0, 1");
            $orules = $db->fetchArray($rquery);
            $db->freeResult($rquery);

            if (!empty($orules['name']) && !empty($CONFIG['bbrulestxt'])) {
                $orules['name'] = stripslashes(stripslashes($orules['name']));
                $TopRules = postify($orules['name'], '', '', $orules['allowsmilies'], $orules['allowbbcode'], $orules['allowimgcode']);
                $faq .= '<tr><td width="100%" class="tablerow" bgcolor="' . $THEME['altbg2'] . '">' . stripslashes($TopRules) . '</td></tr>';
            }

            if (!empty($CONFIG['bbrulestxt'])) {
                $CONFIG['bbrulestxt'] = stripslashes(stripslashes($CONFIG['bbrulestxt']));
                $therules = postify($CONFIG['bbrulestxt'], '', '', $orules['allowsmilies'], $orules['allowbbcode'], $orules['allowimgcode']);
                $faq .= '<tr>
                <td width="100%" class="tablerow" bgcolor="' . $THEME['altbg1'] . '">' . stripslashes($therules) . '</td>
                </tr>
                <tr>
                <td width="100%" class="ctrtablerow" bgcolor="' . $THEME['altbg2'] . '">' . $lang['faq_T'] . '</td>
                </tr>
                <tr>
                <td width="100%" class="ctrtablerow" bgcolor="' . $THEME['altbg2'] . '"><input type="hidden" name="ref" value="' . $ref . '" /><input class="submit" type="submit" name="agreesubmit" value="' . $lang['textagree'] . '" /></td>
                </tr>';
            } else {
                $faq .= '<tr><td width="100%" class="ctrtablerow" bgcolor="' . $THEME['altbg1'] . '">' . $lang['textnone'] . '</td></tr>';
            }
            $faq .= '</table></td></tr></table></form>' . $shadow . '<br />';
        }

        if (onSubmit('agreesubmit')) {
            $config_cache->expire('settings');
            $moderators_cache->expire('moderators');
            $config_cache->expire('theme');
            $config_cache->expire('pluglinks');
            $config_cache->expire('whosonline');
            $config_cache->expire('forumjump');
            $db->query("UPDATE " . X_PREFIX . "members SET readrules = 'no' WHERE uid = '" . $self['uid'] . "'");
            $faq .= '<tr bgcolor="' . $THEME['altbg2'] . '" class="ctrtablerow"><td>' . $lang['rules_E'] . '</td></tr>';
            $faq .= '</table></td></tr></table></form>' . $shadow . '<br />';
            $ref = formVar('ref');
            redirect($ref, 0);
        }
        break;

    default:
        if ($faqid == 0) {
            $groups = array();
            $items = array();
            $items['0'] = array();
            $itemlist = array();
            $i = 0;
            $query = $db->query("SELECT fid, type, name, description, displayorder, fup FROM " . X_PREFIX . "faq WHERE status = 'on' ORDER BY displayorder ASC");
            while (($selItems = $db->fetchArray($query)) != false) {
                if ($selItems['type'] == 'group') {
                    $groups[$i]['fid'] = $selItems['fid'];
                    $groups[$i]['name'] = $selItems['name'];
                    $groups[$i]['description'] = $selItems['description'];
                    $groups[$i]['displayorder'] = $selItems['displayorder'];
                    $groups[$i]['fup'] = $selItems['fup'];
                } elseif ($selItems['type'] == 'item') {
                    $id = (empty($selItems['fup'])) ? 0 : $selItems['fup'];
                    $items[$id][$i]['fid'] = $selItems['fid'];
                    $items[$id][$i]['name'] = $selItems['name'];
                    $items[$id][$i]['description'] = $selItems['description'];
                    $items[$id][$i]['displayorder'] = $selItems['displayorder'];
                    $items[$id][$i]['fup'] = $selItems['fup'];
                    $itemlist[$i]['fid'] = $selItems['fid'];
                    $itemlist[$i]['name'] = $selItems['name'];
                }
                $i++;
            }
            $db->freeResult($query);

            foreach ($groups as $group) {
                $faq .= '
                <table cellspacing="0px" cellpadding="0px" border="0px" width="' . $THEME['tablewidth'] . '" align="center">
                <tr>
                <td bgcolor="' . $THEME['bordercolor'] . '"><table border="0px" cellspacing="' . $THEME['borderwidth'] . '" cellpadding="' . $THEME['tablespace'] . '" width="100%">
                <tr class="category">
                <td width="100%"><a href="faq.php?faqid=' . $group['fid'] . '"><font color="' . $THEME['cattext'] . '"><strong>' . stripslashes($group['name']) . '</strong></font></a></td>
                </tr>
                <tr>
                <td width="100%" class="tablerow" bgcolor="' . $THEME['altbg1'] . '"><ul>
                ';
                if (array_key_exists($group['fid'], $items)) {
                    foreach ($items[$group['fid']] as $item) {
                        $faq .= '<li><a href="faq.php?faqid=' . $group['fid'] . '#' . $item['fid'] . '">' . stripslashes($item['name']) . '</a></li>';
                    }
                }
                $faq .= '</ul></td></tr></table></td></tr></table>' . $shadow . '<br />';
            }
        }

        if ($faqid > 0) {
            $groups = array();
            $items = array();
            $items['0'] = array();
            $itemlist = array();
            $i = 0;
            $query = $db->query("SELECT fid, type, name, description, displayorder, fup, allowsmilies, allowbbcode, allowimgcode, code, view FROM " . X_PREFIX . "faq WHERE (fid = '$faqid' OR fup = '$faqid') AND status = 'on' ORDER BY displayorder ASC");
            while (($selItems = $db->fetchArray($query)) != false) {
                if ($selItems['type'] == 'group') {
                    $groups[$i]['fid'] = $selItems['fid'];
                    $groups[$i]['name'] = $selItems['name'];
                    $groups[$i]['description'] = $selItems['description'];
                    $groups[$i]['displayorder'] = $selItems['displayorder'];
                    $groups[$i]['fup'] = $selItems['fup'];
                    $groups[$i]['allowsmilies'] = $selItems['allowsmilies'];
                    $groups[$i]['allowbbcode'] = $selItems['allowbbcode'];
                    $groups[$i]['allowimgcode'] = $selItems['allowimgcode'];
                } elseif ($selItems['type'] == 'item') {
                    $id = (empty($selItems['fup'])) ? 0 : $selItems['fup'];
                    $items[$id][$i]['fid'] = $selItems['fid'];
                    $items[$id][$i]['name'] = $selItems['name'];
                    $items[$id][$i]['description'] = $selItems['description'];
                    $items[$id][$i]['displayorder'] = $selItems['displayorder'];
                    $items[$id][$i]['fup'] = $selItems['fup'];
                    $items[$id][$i]['allowsmilies'] = $selItems['allowsmilies'];
                    $items[$id][$i]['allowbbcode'] = $selItems['allowbbcode'];
                    $items[$id][$i]['allowimgcode'] = $selItems['allowimgcode'];
                    $items[$id][$i]['view'] = $selItems['view'];
                    $itemlist[$i]['fid'] = $selItems['fid'];
                    $itemlist[$i]['name'] = $selItems['name'];
                }
                $i++;
            }
            $db->freeResult($query);

            foreach ($groups as $group) {
                $groupname = stripslashes($group['name']);
                nav($groupname);
                btitle($groupname);
                $faq .= '<a name="' . $group['fid'] . '"></a>';
                $faq .= '
                <table cellspacing="0px" cellpadding="0px" border="0px" width="' . $THEME['tablewidth'] . '" align="center">
                <tr>
                <td bgcolor="' . $THEME['bordercolor'] . '"><table border="0px" cellspacing="' . $THEME['borderwidth'] . '" cellpadding="' . $THEME['tablespace'] . '" width="100%">
                <tr class="category">
                <td class="title" width="100%">' . $groupname . '</td>
                </tr>
                <tr>
                <td width="100%" class="tablerow" bgcolor="' . $THEME['altbg1'] . '"><ul>';
                if (array_key_exists($group['fid'], $items)) {
                    foreach ($items[$group['fid']] as $item) {
                        $faq .= '<li><a href="#' . $item['fid'] . '">' . stripslashes($item['name']) . '</a></li>';
                    }
                }
                $faq .= '</ul></td></tr></table></td></tr></table>' . $shadow . '<br />';

                if (array_key_exists($group['fid'], $items)) {
                    foreach ($items[$group['fid']] as $item) {
                        $desc = stripslashes(stripslashes($item['description']));
                        $desc = postify($desc, '', '', $item['allowsmilies'], $item['allowbbcode'], $item['allowimgcode']);
                        $faq .= '<a name="' . $item['fid'] . '"></a>';
                        $faq .= '
                        <table cellspacing="0px" cellpadding="0px" border="0px" width="' . $THEME['tablewidth'] . '" align="center">
                        <tr>
                        <td bgcolor="' . $THEME['bordercolor'] . '">
                        <table border="0px" cellspacing="' . $THEME['borderwidth'] . '" cellpadding="' . $THEME['tablespace'] . '" width="100%">
                        <tr>
                        <td class="category" width="100%">
                        <table width="100%" cellpadding="0px" cellspacing="0px">
                        <tr>
                        <td class="mediumtxt" align="left"><font color="' . $THEME['cattext'] . '"><strong>' . stripslashes($item['name']) . '</strong></font></td>
                        <td class="mediumtxt" align="right"><font color="' . $THEME['cattext'] . '"><strong>';
                        if (X_ADMIN) {
                            $faq .= '<a href="./admin/cp_faq.php?fdetails=' . $item['fid'] . '"><em>' . $lang['faq_F'] . '</em></a>';
                        }
                        $faq .= '
                        </strong></font></td>
                        </tr>
                        </table>
                        </td>
                        </tr>
                        <tr>
                        <td width="100%" class="tablerow" bgcolor="' . $THEME['altbg1'] . '">' . stripslashes($desc) . '</td>
                        </tr>
                        </table></td>
                        </tr>
                        </table>
                        ' . $shadow . '
                        <br />';

                        if (isset($item['view']) && ($item['view'] == 1 || $item['view'] == 3)) {
                            $fmsctr = '0';
                            $faq .= '
                            <table cellspacing="0px" cellpadding="0px" border="0px" width="' . $THEME['tablewidth'] . '" align="center">
                            <tr>
                            <td bgcolor="' . $THEME['bordercolor'] . '">
                            <table border="0px" cellspacing="' . $THEME['borderwidth'] . '" cellpadding="' . $THEME['tablespace'] . '" width="100%">
                            <tr>
                            <td width="20%" class="header">' . $lang['textsmiliecode'] . '</td>
                            <td width="30%" class="header">' . $lang['smiliepreview'] . '</td>
                            <td width="20%" class="header">' . $lang['textsmiliecode'] . '</td>
                            <td width="30%" class="header">' . $lang['smiliepreview'] . '</td>
                            </tr>';
                            $querysmilie = $db->query("SELECT * FROM " . X_PREFIX . "smilies WHERE type = 'smiley'");
                            while (($smilie = $db->fetchArray($querysmilie)) != false) {
                                $fmsctr++;
                                if ($fmsctr == 1) {
                                    $faq .= '<tr>';
                                }
                                $faq .= '<td width="20%" class="tablerow" bgcolor="' . $THEME['altbg2'] . '">' . $smilie['code'] . '</td>
                                <td width="30%" class="ctrtablerow" bgcolor="' . $THEME['altbg2'] . '"><img src="' . $THEME['smdir'] . '/' . $smilie['url'] . '" alt="' . $smilie['code'] . '" /></td>';
                                if ($fmsctr == 2) {
                                    $faq .= '</tr>';
                                    $fmsctr = 0;
                                }
                            }
                            $db->freeResult($querysmilie);
                            if ($fmsctr == 1) {
                                $faq .= '<td width="20%" class="tablerow" bgcolor="' . $THEME['altbg2'] . '"></td><td width="30%" class="tablerow" bgcolor="' . $THEME['altbg2'] . '"></td></tr>';
                            }
                            $faq .= '</table></td></tr></table>' . $shadow . '<br />';
                        }

                        if (isset($item['view']) && ($item['view'] == 2 || $item['view'] == 3)) {
                            $faq .= '<table cellspacing="0px" cellpadding="0px" border="0px" width="' . $THEME['tablewidth'] . '" align="center">
                            <tr>
                            <td bgcolor="' . $THEME['bordercolor'] . '"><table border="0px" cellspacing="' . $THEME['borderwidth'] . '" cellpadding="' . $THEME['tablespace'] . '" width="100%">
                            <tr>
                            <td width="33%" class="header">' . $lang['textuserranks'] . ':</td>
                            <td width="34%" class="header">' . $lang['textstars'] . '</td>
                            <td width="33%" class="header">' . $lang['textposts'] . '</td>
                            </tr>';
                            $query = $db->query("SELECT * FROM " . X_PREFIX . "ranks WHERE title !='Moderator' AND title !='Super Moderator' AND title !='Super Administrator' AND title !='Administrator' ORDER BY posts ASC");
                            while (($ranks = $db->fetchArray($query)) != false) {
                                $stars = str_repeat('<img src="' . $THEME['imgdir'] . '/star.gif" alt="*" title="*" border="0px" />', $ranks['stars']);
                                $faq .= '<tr>
                                <td class="tablerow" bgcolor="' . $THEME['altbg2'] . '">' . $ranks['title'] . '</td>
                                <td class="tablerow" bgcolor="' . $THEME['altbg2'] . '">' . $stars . '</td>
                                <td class="tablerow" bgcolor="' . $THEME['altbg2'] . '">' . $ranks['posts'] . ' ' . $lang['memposts'] . '</td>
                                </tr>';
                                $stars = '';
                            }
                            $db->freeResult($query);
                            $faq .= '</table></td></tr></table>' . $shadow . '<br />';
                        }
                    }
                }
            }
        }
        break;
}

eval('echo "' . template('header') . '";');
echo $faq;
loadtime();
eval('echo "' . template('footer') . '";');
