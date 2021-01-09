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
nav($lang['textforums']);
btitle($lang['textcp']);
btitle($lang['textforums']);

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

    $groups = array();
    $forums = array();
    $forums[0] = array();
    $forumlist = array();
    $subs = array();
    $i = 0;
    $query = $db->query("SELECT fid, type, name, displayorder, status, fup FROM " . X_PREFIX . "forums ORDER BY fup ASC, displayorder ASC");
    while (($selForums = $db->fetchArray($query)) != false) {
        if ($selForums['type'] == 'group') {
            $groups[$i]['fid'] = $selForums['fid'];
            $groups[$i]['name'] = htmlspecialchars($selForums['name']);
            $groups[$i]['displayorder'] = $selForums['displayorder'];
            $groups[$i]['status'] = $selForums['status'];
            $groups[$i]['fup'] = $selForums['fup'];
        } elseif ($selForums['type'] == 'forum') {
            $id = ($selForums['fup'] == '') ? 0 : $selForums['fup'];
            $forums[$id][$i]['fid'] = $selForums['fid'];
            $forums[$id][$i]['name'] = htmlspecialchars($selForums['name']);
            $forums[$id][$i]['displayorder'] = $selForums['displayorder'];
            $forums[$id][$i]['status'] = $selForums['status'];
            $forums[$id][$i]['fup'] = $selForums['fup'];
            $forumlist[$i]['fid'] = $selForums['fid'];
            $forumlist[$i]['name'] = $selForums['name'];
        } elseif ($selForums['type'] == 'sub') {
            $subs[$selForums['fup']][$i]['fid'] = $selForums['fid'];
            $subs[$selForums['fup']][$i]['name'] = htmlspecialchars($selForums['name']);
            $subs[$selForums['fup']][$i]['displayorder'] = $selForums['displayorder'];
            $subs[$selForums['fup']][$i]['status'] = $selForums['status'];
            $subs[$selForums['fup']][$i]['fup'] = $selForums['fup'];
        }
        $i++;
    }
    $db->freeResult($query);
    ?>
    <form method="post" action="cp_forums.php">
        <input type="hidden" name="csrf_token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr>
                            <td class="category"><font
                                        color="<?php echo $THEME['cattext'] ?>"><strong><?php echo $lang['textforumopts'] ?></strong></font>
                            </td>
                        </tr>
                        <?php
                        foreach ($forums[0] as $forum) {
                            $on = $off = '';
                            switch ($forum['status']) {
                                case 'on':
                                    $on = $selHTML;
                                    break;
                            
                                default:
                                    $off = $selHTML;
                                    break;
                            }
                            ?>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td class="smalltxt"><input type="checkbox"
                                                            name="delete<?php echo $forum['fid'] ?>"
                                                            value="<?php echo $forum['fid'] ?>"/> <input type="text"
                                                                                                         name="name<?php echo $forum['fid'] ?>"
                                                                                                         value="<?php echo stripslashes($forum['name']) ?>"/>
                                    &nbsp; <?php echo $lang['textorder'] ?>
                                    <input type="text" name="displayorder<?php echo $forum['fid'] ?>"
                                           size="2" value="<?php echo $forum['displayorder'] ?>"/>&nbsp; <select
                                            name="status<?php echo $forum['fid'] ?>">
                                        <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                        <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                    </select>&nbsp; <select name="moveto<?php echo $forum['fid'] ?>">
                                        <?php
                                        foreach ($groups as $moveforum) {
                                            echo '<option value="' . $moveforum['fid'] . '">' . stripslashes($moveforum['name']) . '</option>';
                                        }
                                        ?>
                                    </select> <a
                                            href="cp_forums.php?fdetails=<?php echo $forum['fid'] ?>"><?php echo $lang['textmoreopts'] ?></a>
                                </td>
                            </tr>
                            <?php
                            if (array_key_exists($forum['fid'], $subs)) {
                                foreach ($subs[$forum['fid']] as $subforum) {
                                    $on = $off = '';
                                    switch ($subforum['status']) {
                                        case 'on':
                                            $on = $selHTML;
                                            break;
                                        default:
                                            $off = $selHTML;
                                            break;
                                    }
                                    ?>
                                    <tr bgcolor="<?php echo $THEME['altbg2'] ?>"
                                        class="tablerow">
                                        <td class="smalltxt">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input
                                                    type="checkbox" name="delete<?php echo $subforum['fid'] ?>"
                                                    value="<?php echo $subforum['fid'] ?>"/> &nbsp; <input
                                                    type="text" name="name<?php echo $subforum['fid'] ?>"
                                                    value="<?php echo stripslashes($subforum['name']) ?>"/>
                                            &nbsp; <?php echo $lang['textorder'] ?>
                                            <input type="text"
                                                   name="displayorder<?php echo $subforum['fid'] ?>" size="2"
                                                   value="<?php echo $subforum['displayorder'] ?>"/> &nbsp; <select
                                                    name="status<?php echo $subforum['fid'] ?>">
                                                <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                                <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                            </select> &nbsp; <select
                                                    name="moveto<?php echo $subforum['fid'] ?>">
                                                <?php
                                                foreach ($forumlist as $moveforum) {
                                                    if ($subforum['fup'] == $moveforum['fid']) {
                                                        echo '<option value="' . $moveforum['fid'] . '" selected="selected">' . stripslashes($moveforum['name']) . '</option>';
                                                    } else {
                                                        echo '<option value="' . $moveforum['fid'] . '">' . stripslashes($moveforum['name']) . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select> <a
                                                    href="cp_forums.php?fdetails=<?php echo $subforum['fid'] ?>"><?php echo $lang['textmoreopts'] ?></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        foreach ($groups as $group) {
                            $on = $off = '';
                            switch ($group['status']) {
                                case 'on':
                                    $on = $selHTML;
                                    break;

                                default:
                                    $off = $selHTML;
                                    break;
                            }
                            ?>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td>&nbsp;</td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                                <td class="smalltxt">
                                    <?php
                                    if (count($groups) != '1') {
                                        ?>
                                        <input type="checkbox"
                                               name="delete<?php echo $group['fid'] ?>"
                                               value="<?php echo $group['fid'] ?>"/>
                                        <?php
                                    }
                                    ?>
                                    <input type="text" name="name<?php echo $group['fid'] ?>"
                                           value="<?php echo stripslashes($group['name']) ?>"/>
                                    &nbsp; <?php echo $lang['textorder'] ?> <input type="text"
                                                                                   name="displayorder<?php echo $group['fid'] ?>"
                                                                                   size="2"
                                                                                   value="<?php echo $group['displayorder'] ?>"/>
                                    &nbsp; <select
                                            name="status<?php echo $group['fid'] ?>">
                                        <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                        <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                    </select>
                                </td>
                            </tr>
                            <?php
                            if (array_key_exists($group['fid'], $forums)) {
                                foreach ($forums[$group['fid']] as $forum) {
                                    $on = $off = '';
                                    switch ($forum['status']) {
                                        case 'on':
                                            $on = $selHTML;
                                            break;

                                        default:
                                            $off = $selHTML;
                                            break;
                                    }
                                    ?>
                                    <tr bgcolor="<?php echo $THEME['altbg2'] ?>"
                                        class="tablerow">
                                        <td class="smalltxt">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input
                                                    type="checkbox" name="delete<?php echo $forum['fid'] ?>"
                                                    value="<?php echo $forum['fid'] ?>"/> &nbsp;<input type="text"
                                                                                                       name="name<?php echo $forum['fid'] ?>"
                                                                                                       value="<?php echo stripslashes($forum['name']) ?>"/>
                                            &nbsp; <?php echo $lang['textorder'] ?> <input
                                                    type="text" name="displayorder<?php echo $forum['fid'] ?>"
                                                    size="2" value="<?php echo $forum['displayorder'] ?>"/> &nbsp;
                                            <select
                                                    name="status<?php echo $forum['fid'] ?>">
                                                <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                                <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                            </select> &nbsp; <select name="moveto<?php echo $forum['fid'] ?>">
                                            <?php
                                            foreach ($groups as $moveforum) {
                                                if ($moveforum['fid'] == $forum['fup']) {
                                                    $curgroup = $selHTML;
                                                } else {
                                                    $curgroup = '';
                                                }
                                                echo '<option value="' . $moveforum['fid'] . '" ' . $curgroup . '>' . stripslashes($moveforum['name']) . '</option>';
                                            }
                                            ?>
                                            </select> <a
                                                    href="cp_forums.php?fdetails=<?php echo $forum['fid'] ?>"><?php echo $lang['textmoreopts'] ?></a>
                                        </td>
                                    </tr>
                                    <?php
                                    if (array_key_exists($forum['fid'], $subs)) {
                                        foreach ($subs[$forum['fid']] as $forum) {
                                            $on = $off = '';
                                            switch ($forum['status']) {
                                                case 'on':
                                                    $on = $selHTML;
                                                    break;

                                                default:
                                                    $off = $selHTML;
                                                    break;
                                            }
                                            ?>
                                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>"
                                                class="tablerow">
                                                <td class="smalltxt">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input
                                                            type="checkbox" name="delete<?php echo $forum['fid'] ?>"
                                                            value="<?php echo $forum['fid'] ?>"/> &nbsp;<input
                                                            type="text"
                                                            name="name<?php echo $forum['fid'] ?>"
                                                            value="<?php echo stripslashes($forum['name']) ?>"/>
                                                    &nbsp; <?php echo $lang['textorder'] ?> <input
                                                            type="text" name="displayorder<?php echo $forum['fid'] ?>"
                                                            size="2" value="<?php echo $forum['displayorder'] ?>"/>
                                                    &nbsp; <select
                                                            name="status<?php echo $forum['fid'] ?>">
                                                        <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                                        <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                                    </select> &nbsp; <select name="moveto<?php echo $forum['fid'] ?>">
                                                    <?php
                                                    foreach ($forumlist as $moveforum) {
                                                        if ($moveforum['fid'] == $forum['fup']) {
                                                            echo '<option value="' . $moveforum['fid'] . '" selected="selected">' . stripslashes($moveforum['name']) . '</option>';
                                                        } else {
                                                            echo '<option value="' . $moveforum['fid'] . '">' . stripslashes($moveforum['name']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                    </select> <a
                                                            href="cp_forums.php?fdetails=<?php echo $forum['fid'] ?>"><?php echo $lang['textmoreopts'] ?></a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                        <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                            <td>&nbsp;</td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td class="smalltxt"><input type="text" name="newgname"
                                                        value="<?php echo $lang['textnewgroup'] ?>"/>
                                &nbsp; <?php echo $lang['textorder'] ?> <input type="text"
                                                                               name="newgorder" size="2"/> &nbsp;
                                <select name="newgstatus">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="smalltxt"><input
                                        type="text" name="newfname"
                                        value="<?php echo $lang['textnewforum'] ?>"/>
                                &nbsp; <?php echo $lang['textorder'] ?> <input type="text"
                                                                               name="newforder" size="2"/> &nbsp;
                                <select name="newfstatus">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select>
                                <?php
                                if (!empty($groups)) {
                                    ?>
                                &nbsp; <select name="newffup">
                                    <?php
                                    foreach ($groups as $group) {
                                        echo '<option value="' . $group['fid'] . '">' . stripslashes($group['name']) . '</option>';
                                    }
                                }
                                ?>
                                </select></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td class="smalltxt"><input type="text" name="newsubname"
                                                        value="<?php echo $lang['textnewsubf'] ?>"/>
                                &nbsp; <?php echo $lang['textorder'] ?> <input type="text"
                                                                               name="newsuborder" size="2"/> &nbsp;
                                <select name="newsubstatus">
                                    <option
                                            value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select>
                                <?php
                                if (!empty($forumlist)) {
                                    ?>
                                    &nbsp; <select name="newsubfup">
                                    <?php
                                    foreach ($forumlist as $group) {
                                        echo '<option value="' . $group['fid'] . '">' . stripslashes($group['name']) . '</option>';
                                    }
                                    ?>
                                    </select>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow"><input
                                        type="submit" name="forumsubmit"
                                        value="<?php echo $lang['textsubmitchanges'] ?>" class="submit"/></td>
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

function viewDetailsPanel($fdetails)
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML;
    ?>
    <form method="post"
          action="cp_forums.php?fdetails=<?php echo $fdetails ?>">
        <input type="hidden" name="csrf_token"
               value="<?php echo $oToken->createToken() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr>
                            <td class="category" colspan="2"><font
                                        color="<?php echo $THEME['cattext'] ?>"><strong><?php echo $lang['textforumopts'] ?></strong></font>
                            </td>
                        </tr>
                        <?php
                        $queryg = $db->query("SELECT * FROM " . X_PREFIX . "forums WHERE fid = '$fdetails'");
                        $forum = $db->fetchArray($queryg);
                        $db->freeResult($queryg);
                        $themelist = array();
                        $themelist[] = '<select name="themeforumnew">';
                        $themelist[] = '<option value="0">' . $lang['textusedefault'] . '</option>';
                        $query = $db->query("SELECT themeid, name FROM " . X_PREFIX . "themes WHERE themestatus = 'on' ORDER BY name ASC");
                        while (($themeinfo = $db->fetchArray($query)) != false) {
                            if ($themeinfo['themeid'] == $forum['theme']) {
                                $themelist[] = '<option value="' . $themeinfo['themeid'] . '" selected="selected">' . stripslashes($themeinfo['name']) . '</option>';
                            } else {
                                $themelist[] = '<option value="' . $themeinfo['themeid'] . '">' . stripslashes($themeinfo['name']) . '</option>';
                            }
                        }
                        $themelist[] = '</select>';
                        $themelist = implode("\n", $themelist);
                        $db->freeResult($query);
                        $checked1 = '';
                        switch ($forum['allowsmilies']) {
                            case 'yes':
                                $checked1 = $cheHTML;
                                break;
                            default:
                                $checked1 = '';
                                break;
                        }
                        $checked2 = '';
                        switch ($forum['allowbbcode']) {
                            case 'yes':
                                $checked2 = $cheHTML;
                                break;
                            default:
                                $checked2 = '';
                                break;
                        }
                        $checked3 = '';
                        switch ($forum['allowimgcode']) {
                            case 'yes':
                                $checked3 = $cheHTML;
                                break;
                            default:
                                $checked3 = '';
                                break;
                        }
                        $checked4 = '';
                        switch ($forum['attachstatus']) {
                            case 'on':
                                $checked4 = $cheHTML;
                                break;
                            default:
                                $checked4 = '';
                                break;
                        }
                        $checked5 = '';
                        switch ($forum['pollstatus']) {
                            case 'on':
                                $checked5 = $cheHTML;
                                break;
                            default:
                                $checked5 = '';
                                break;
                        }
                        $checked6 = '';
                        switch ($forum['guestposting']) {
                            case 'on':
                                $checked6 = $cheHTML;
                                break;
                            default:
                                $checked6 = '';
                                break;
                        }
                        $pperm = explode('|', $forum['postperm']);
                        $type11 = $type12 = $type13 = $type14 = $type15 = '';
                        switch ($pperm[0]) {
                            case '2':
                                $type12 = $selHTML;
                                break;
                            case '3':
                                $type13 = $selHTML;
                                break;
                            case '4':
                                $type14 = $selHTML;
                                break;
                            case '5':
                                $type15 = $selHTML;
                                break;
                            default:
                                $type11 = $selHTML;
                                break;
                        }
                        $type21 = $type22 = $type23 = $type24 = $type25 = '';
                        switch ($pperm[1]) {
                            case '2':
                                $type22 = $selHTML;
                                break;
                            case '3':
                                $type23 = $selHTML;
                                break;
                            case '4':
                                $type24 = $selHTML;
                                break;
                            case '5':
                                $type25 = $selHTML;
                                break;
                            default:
                                $type21 = $selHTML;
                                break;
                        }
                        $type31 = $type32 = $type33 = $type34 = $type35 = '';
                        switch ($forum['private']) {
                            case '2':
                                $type32 = $selHTML;
                                break;
                            case '3':
                                $type33 = $selHTML;
                                break;
                            case '4':
                                $type34 = $selHTML;
                                break;
                            case '5':
                                $type35 = $selHTML;
                                break;
                            default:
                                $type31 = $selHTML;
                                break;
                        }
                        $type41 = $type42 = $type43 = $type44 = $type45 = '';
                        $pperm[2] = isset($pperm[2]) ? $pperm[2] : 0;
                        switch ($pperm[2]) {
                            case '2':
                                $type42 = $selHTML;
                                break;
                            case '3':
                                $type43 = $selHTML;
                                break;
                            case '4':
                                $type44 = $selHTML;
                                break;
                            case '5':
                                $type45 = $selHTML;
                                break;
                            default:
                                $type41 = $selHTML;
                                break;
                        }
                        $type51 = $type52 = $type53 = $type54 = $type55 = '';
                        $pperm[3] = isset($pperm[3]) ? $pperm[3] : 0;
                        switch ($pperm[3]) {
                            case '2':
                                $type52 = $selHTML;
                                break;
                            case '3':
                                $type53 = $selHTML;
                                break;
                            case '4':
                                $type54 = $selHTML;
                                break;
                            case '5':
                                $type55 = $selHTML;
                                break;
                            default:
                                $type51 = $selHTML;
                                break;
                        }
                        $fruleson = $frulesoff = '';
                        switch ($forum['frules_status']) {
                            case 'on':
                                $fruleson = $selHTML;
                                break;
                            default:
                                $frulesoff = $selHTML;
                                break;
                        }
                        $markthreadson = $markthreadsoff = '';
                        switch ($forum['mt_status']) {
                            case 'on':
                                $markthreadson = $selHTML;
                                break;
                            default:
                                $markthreadsoff = $selHTML;
                                break;
                        }
                        $closethreadson = $closethreadsoff = '';
                        switch ($forum['closethreads']) {
                            case 'on':
                                $closethreadson = $selHTML;
                                break;
                            default:
                                $closethreadsoff = $selHTML;
                                break;
                        }
                        $quickreplyon = $quickreplyoff = '';
                        switch ($forum['quickreply']) {
                            case 'on':
                                $quickreplyon = $selHTML;
                                break;
                            default:
                                $quickreplyoff = $selHTML;
                                break;
                        }
                        $postcounton = $postcountoff = '';
                        switch ($forum['postcount']) {
                            case 'on':
                                $postcounton = $selHTML;
                                break;
                            default:
                                $postcountoff = $selHTML;
                                break;
                        }
                        $forum['name'] = stripslashes(htmlspecialchars($forum['name']));
                        $forum['description'] = stripslashes(htmlspecialchars($forum['description']));
                        $forum['userlist'] = stripslashes($forum['userlist']);
                        $forum['frules'] = stripslashes($forum['frules']);
                        $forum['mt_open'] = stripslashes($forum['mt_open']);
                        $forum['mt_close'] = stripslashes($forum['mt_close']);
                        $forum['subjectprefixes'] = stripslashes($forum['subjectprefixes']);
                        $forum['minchars'] = (int) $forum['minchars'];
                        $forum['mpfa'] = (int) $forum['mpfa'];
                        $forum['mpnp'] = (int) $forum['mpnp'];
                        $forum['mpnt'] = (int) $forum['mpnt'];
                        $forum['attachnum'] = (int) $forum['attachnum'];
                        $CONFIG['attach_num_default'] = (int) $CONFIG['attach_num_default'];
                        GaiaBB\FormHelper::formTextBox($lang['textforumname'], 'namenew', $forum['name'], 20);
                        GaiaBB\FormHelper::formTextBox2($lang['textdesc'], 5, 'descnew', 50, $forum['description']);
                        GaiaBB\FormHelper::formSelectOnOff($lang['frules_status'], 'frules_statusnew', $fruleson, $frulesoff);
                        GaiaBB\FormHelper::formTextBox2($lang['frules_explain'], 5, 'frulesnew', 50, $forum['frules']);
                        GaiaBB\FormHelper::formSelectOnOff($lang['closethreadsstatus'], 'closethreadsnew', $closethreadson, $closethreadsoff);
                        GaiaBB\FormHelper::formSelectOnOff($lang['quickreply_status'], 'quickreplynew', $quickreplyon, $quickreplyoff);
                        GaiaBB\FormHelper::formSelectOnOff($lang['fpostcount'], 'postcountnew', $postcounton, $postcountoff);
                        GaiaBB\FormHelper::formTextBox($lang['minchars'], 'mincharsnew', $forum['minchars'], 2);
                        GaiaBB\FormHelper::formTextBox($lang['mpfa'], 'mpfanew', $forum['mpfa'], 2);
                        GaiaBB\FormHelper::formTextBox($lang['mpnp'], 'mpnpnew', $forum['mpnp'], 2);
                        GaiaBB\FormHelper::formTextBox($lang['mpnt'], 'mpntnew', $forum['mpnt'], 2);
                        ?>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['multiattach'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="attachnumnew" size="2"
                                                                                value="<?php echo ($forum['attachnum'] > 0 ? $forum['attachnum'] : $CONFIG['attach_num_default']) ?>"/>
                            </td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                valign="top"><?php echo $lang['textallow'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="smalltxt">
                                <?php
                                GaiaBB\FormHelper::formCheckBox('allowsmiliesnew', 'yes', $checked1, $lang['textsmilies']);
                                GaiaBB\FormHelper::formCheckBox('allowbbcodenew', 'yes', $checked2, $lang['textbbcode']);
                                GaiaBB\FormHelper::formCheckBox('allowimgcodenew', 'yes', $checked3, $lang['textimgcode']);
                                GaiaBB\FormHelper::formCheckBox('attachstatusnew', 'on', $checked4, $lang['attachments']);
                                GaiaBB\FormHelper::formCheckBox('pollstatusnew', 'on', $checked5, $lang['polls']);
                                GaiaBB\FormHelper::formCheckBox('guestpostingnew', 'on', $checked6, $lang['textanonymousposting']);
                                ?>
                            </td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texttheme'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $themelist ?></td>
                        </tr>
                        <?php
                        GaiaBB\FormHelper::formSelectOnOff($lang['markthreadstatus'], 'mt_statusnew', $markthreadson, $markthreadsoff);
                        GaiaBB\FormHelper::formTextBox2($lang['markthreadopen'], 4, 'mt_opennew', 50, $forum['mt_open']);
                        GaiaBB\FormHelper::formTextBox2($lang['markthreadclose'], 4, 'mt_closenew', 50, $forum['mt_close']);
                        GaiaBB\FormHelper::formTextBox2($lang['topicsubjectprefixes'], 5, 'subjectprefixesnew', 50, $forum['subjectprefixes']);
                        ?>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['whopostop1'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select
                                        name="postperm1">
                                    <option value="1" <?php echo $type11 ?>><?php echo $lang['textpermission1'] ?></option>
                                    <option value="5" <?php echo $type15 ?>><?php echo $lang['textpermission5'] ?></option>
                                    <option value="2" <?php echo $type12 ?>><?php echo $lang['textpermission2'] ?></option>
                                    <option value="3" <?php echo $type13 ?>><?php echo $lang['textpermission3'] ?></option>
                                    <option value="4" <?php echo $type14 ?>><?php echo $lang['textpermission41'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['whopostop2'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select
                                        name="postperm2">
                                    <option value="1" <?php echo $type21 ?>><?php echo $lang['textpermission1'] ?></option>
                                    <option value="5" <?php echo $type25 ?>><?php echo $lang['textpermission5'] ?></option>
                                    <option value="2" <?php echo $type22 ?>><?php echo $lang['textpermission2'] ?></option>
                                    <option value="3" <?php echo $type23 ?>><?php echo $lang['textpermission3'] ?></option>
                                    <option value="4" <?php echo $type24 ?>><?php echo $lang['textpermission41'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['whopostop3'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select
                                        name="postperm3">
                                    <option value="1" <?php echo $type41 ?>><?php echo $lang['textpermission1'] ?></option>
                                    <option value="5" <?php echo $type45 ?>><?php echo $lang['textpermission5'] ?></option>
                                    <option value="2" <?php echo $type42 ?>><?php echo $lang['textpermission2'] ?></option>
                                    <option value="3" <?php echo $type43 ?>><?php echo $lang['textpermission3'] ?></option>
                                    <option value="4" <?php echo $type44 ?>><?php echo $lang['textpermission41'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['whopostop4'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select
                                        name="postperm4">
                                    <option value="1" <?php echo $type51 ?>><?php echo $lang['textpermission1'] ?></option>
                                    <option value="5" <?php echo $type55 ?>><?php echo $lang['textpermission5'] ?></option>
                                    <option value="2" <?php echo $type52 ?>><?php echo $lang['textpermission2'] ?></option>
                                    <option value="3" <?php echo $type53 ?>><?php echo $lang['textpermission3'] ?></option>
                                    <option value="4" <?php echo $type54 ?>><?php echo $lang['textpermission41'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['whoview'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select
                                        name="privatenew">
                                    <option value="1" <?php echo $type31 ?>><?php echo $lang['textpermission1'] ?></option>
                                    <option value="5" <?php echo $type35 ?>><?php echo $lang['textpermission5'] ?></option>
                                    <option value="2" <?php echo $type32 ?>><?php echo $lang['textpermission2'] ?></option>
                                    <option value="3" <?php echo $type33 ?>><?php echo $lang['textpermission3'] ?></option>
                                    <option value="4" <?php echo $type34 ?>><?php echo $lang['textpermission42'] ?></option>
                                </select></td>
                        </tr>
                        <?php
                        GaiaBB\FormHelper::formTextBox2($lang['textuserlist'], 5, 'userlistnew', 50, $forum['userlist']);
                        GaiaBB\FormHelper::formTextPassBox($lang['forumpw'], 'passwordnew', $forum['password'], 20);
                        ?>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textdeleteques'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="checkbox"
                                                                                name="delete"
                                                                                value="<?php echo $forum['fid'] ?>"/>
                            </td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                            <td colspan="2"><input type="submit" name="forumsubmit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>" class="submit"/>
                            </td>
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

function doPanel($fdetails)
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;

    $oToken->assertToken();

    if ($fdetails == 0) {
        $queryforum = $db->query("SELECT fid, type FROM " . X_PREFIX . "forums WHERE type = 'forum' OR type = 'sub'");
        $db->query("DELETE FROM " . X_PREFIX . "forums WHERE name = ''");
        while (($forum = $db->fetchArray($queryforum)) != false) {
            $displayorder = "displayorder$forum[fid]";
            $displayorder = formInt($displayorder);
            $name = "name$forum[fid]";
            $name = $db->escape(decode_entities(formVar($name)));
            $self['status'] = "status$forum[fid]";
            $self['status'] = $db->escape(decode_entities(formVar($self['status'])));
            $delete = "delete$forum[fid]";
            $delete = formInt($delete);
            $moveto = "moveto$forum[fid]";
            $moveto = formInt($moveto);
            if ($delete > 0) {
                $db->query("DELETE FROM " . X_PREFIX . "forums WHERE (type = 'forum' OR type = 'sub') AND fid = '$delete'");
                $querythread = $db->query("SELECT tid, author FROM " . X_PREFIX . "threads WHERE fid = '$delete'");
                while (($thread = $db->fetchArray($querythread)) != false) {
                    $db->query("DELETE FROM " . X_PREFIX . "threads WHERE tid = '$thread[tid]'");
                    $db->query("DELETE FROM " . X_PREFIX . "favorites WHERE tid = '$thread[tid]'");
                    $db->query("DELETE FROM " . X_PREFIX . "subscriptions WHERE tid = '$thread[tid]'");
                    $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum-1 WHERE username = '$thread[author]'");
                    $querypost = $db->query("SELECT pid, author FROM " . X_PREFIX . "posts WHERE tid = '$thread[tid]'");
                    while (($post = $db->fetchArray($querypost)) != false) {
                        $db->query("DELETE FROM " . X_PREFIX . "posts WHERE pid = '$post[pid]'");
                        $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum-1 WHERE username = '$post[author]'");
                    }
                    $db->freeResult($querypost);
                }
                $db->freeResult($querythread);
            }

            $db->query("UPDATE " . X_PREFIX . "forums SET name = '$name', displayorder = " . $displayorder . ", status = '$self[status]', fup = " . $moveto . " WHERE fid = '$forum[fid]'");
        }
        $db->freeResult($queryforum);
        $querygroup = $db->query("SELECT fid FROM " . X_PREFIX . "forums WHERE type = 'group'");
        $deleter = array();
        while (($group2 = $db->fetchArray($querygroup)) != false) {
            $delete2 = "delete$group2[fid]";
            if (isset(${$delete2})) {
                $deleter[] = ${$delete2};
            }
        }
        $numgroups = $db->numRows($querygroup);
        if (count($deleter) == $numgroups) {
            cp_error($lang['forumnodeleteall'], false, '', '</td></tr></table>', 'cp_forums.php', true);
        }
        $db->freeResult($querygroup);
        $querygroup2 = $db->query("SELECT fid FROM " . X_PREFIX . "forums WHERE type = 'group'");
        while (($group = $db->fetchArray($querygroup2)) != false) {
            $name = "name$group[fid]";
            $name = $db->escape(decode_entities(formVar($name)));
            $displayorder = "displayorder$group[fid]";
            $displayorder = formInt($displayorder);
            $self['status'] = "status$group[fid]";
            $self['status'] = $db->escape(decode_entities(formVar($self['status'])));
            $delete = "delete$group[fid]";
            $delete = formInt($delete);
            if ($delete > 0) {
                $query = $db->query("SELECT fid FROM " . X_PREFIX . "forums WHERE type = 'forum' AND fup = '$delete'");
                while (($forum = $db->fetchArray($query)) != false) {
                    $db->query("UPDATE " . X_PREFIX . "forums SET fup = '' WHERE type = 'forum' AND fup = '$delete'");
                }
                $db->query("DELETE FROM " . X_PREFIX . "forums WHERE type = 'group' AND fid = '$delete'");
            }
            $db->query("UPDATE " . X_PREFIX . "forums SET name = '$name', displayorder = " . (int) $displayorder . ", status = '$self[status]' WHERE fid = '$group[fid]'");
        }

        $newfname = $db->escape(decode_entities(formVar('newfname')));
        $newforder = formInt('newforder');
        $newffup = formInt('newffup');
        $newfstatus = $db->escape(decode_entities(formVar('newfstatus')));

        $db->freeResult($querygroup2);
        if ($newfname != $lang['textnewforum']) {
            $db->query("INSERT INTO " . X_PREFIX . "forums (type, name, status, moderator, displayorder, private, description, allowsmilies, allowbbcode, userlist, posts, threads, fup, postperm, allowimgcode, attachstatus, pollstatus, password, guestposting, minchars, attachnum, frules_status, frules, mt_status, mt_open, mt_close, closethreads, quickreply, subjectprefixes, mpnt, mpnp, mpfa, postcount) VALUES ('forum', '$newfname', '$newfstatus', '', " . $newforder . ", '1', '', 'yes', 'yes', '', 0, 0, " . $newffup . ", '1|1|1', 'yes', 'on', 'on', '', 'off', 0, $CONFIG[attach_num_default], 'off', '', 'off', '', '', 'off', 'on', '', 0, 0, 0, 'on')");
        }

        $newgname = $db->escape(decode_entities(formVar('newgname')));
        $newgorder = formInt('newgorder');
        $newgstatus = $db->escape(decode_entities(formVar('newgstatus')));
        if ($newgname != $lang['textnewgroup']) {
            $db->query("INSERT INTO " . X_PREFIX . "forums (type, name, status, moderator, displayorder, private, description, allowsmilies, allowbbcode, userlist, posts, threads, fup, postperm, allowimgcode, attachstatus, pollstatus, password, guestposting, minchars, attachnum, frules_status, frules, mt_status, mt_open, mt_close, closethreads, quickreply, subjectprefixes, mpnt, mpnp, mpfa, postcount) VALUES ('group', '$newgname', '$newgstatus', '', " . $newgorder . ", '', '', '', '', '', 0, 0, '', '', '', '', '', '', 'off', 0, $CONFIG[attach_num_default], 'off', '', 'off', '', '', 'off', 'on', '', 0, 0, '0', 'on')");
        }

        $newsubname = $db->escape(decode_entities(formVar('newsubname')));
        $newsuborder = formInt('newsuborder');
        $newsubfup = formInt('newsubfup');
        $newsubstatus = $db->escape(decode_entities(formVar('newsubstatus')));
        if ($newsubname != $lang['textnewsubf']) {
            $db->query("INSERT INTO " . X_PREFIX . "forums (type, name, status, moderator, displayorder, private, description, allowsmilies, allowbbcode, userlist, posts, threads, fup, postperm, allowimgcode, attachstatus, pollstatus, password, guestposting, minchars, attachnum, frules_status, frules, mt_status, mt_open, mt_close, closethreads, quickreply, subjectprefixes, mpnt, mpnp, mpfa, postcount) VALUES ('sub', '$newsubname', '$newsubstatus', '', " . $newsuborder . ", 1, '', 'yes', 'yes', '', 0, 0, " . $newsubfup . ", '1|1|1', 'yes', 'on', 'on', '', 'off', 0, $CONFIG[attach_num_default], 'off', '', 'off', '', '', 'off', 'on', '', 0, 0, 0, 'on')");
        }
        cp_message($lang['textforumupdate'], false, '', '</td></tr></table>', 'cp_forums.php', true, false, true);
    } else {
        $allowsmiliesnew = formYesNo('allowsmiliesnew');
        $allowbbcodenew = formYesNo('allowbbcodenew');
        $allowimgcodenew = formYesNo('allowimgcodenew');
        $attachstatusnew = formOnOff('attachstatusnew');
        $pollstatusnew = formOnOff('pollstatusnew');
        $guestpostingnew = formOnOff('guestpostingnew');
        $frules_statusnew = formOnOff('frules_statusnew');
        $mt_statusnew = formOnOff('mt_statusnew');
        $closethreadsnew = formOnOff('closethreadsnew');
        $quickreplynew = formOnOff('quickreplynew');
        $postcountnew = formOnOff('postcountnew');
        $themeforumnew = formInt('themeforumnew');
        $mincharsnew = formInt('mincharsnew');
        $attachnumnew = formInt('attachnumnew');
        $mpntnew = formInt('mpntnew');
        $mpnpnew = formInt('mpnpnew');
        $mpfanew = formInt('mpfanew');
        $namenew = $db->escape(decode_entities(formVar('namenew')));
        $descnew = $db->escape(decode_entities(formVar('descnew')));
        $userlistnew = $db->escape(decode_entities(formVar('userlistnew')));
        $frulesnew = $db->escape(decode_entities(formVar('frulesnew')));
        $privatenew = $db->escape(decode_entities(formVar('privatenew')));
        $passwordnew = $db->escape(decode_entities(formVar('passwordnew')));
        $mt_opennew = $db->escape(decode_entities(formVar('mt_opennew')));
        $mt_closenew = $db->escape(decode_entities(formVar('mt_closenew')));
        $postperm1 = formInt('postperm1');
        $postperm2 = formInt('postperm2');
        $postperm3 = formInt('postperm3');
        $postperm4 = formInt('postperm4');

        $subjectprefixesnew = $db->escape(decode_entities(formVar('subjectprefixesnew')));
        $db->query("UPDATE " . X_PREFIX . "forums SET
            name = '$namenew',
            description = '$descnew',
            allowsmilies = '$allowsmiliesnew',
            allowbbcode = '$allowbbcodenew',
            theme = '$themeforumnew',
            userlist = '$userlistnew',
            private = '$privatenew',
            postperm = '$postperm1|$postperm2|$postperm3|$postperm4',
            allowimgcode = '$allowimgcodenew',
            attachstatus = '$attachstatusnew',
            pollstatus = '$pollstatusnew',
            password = '$passwordnew',
            guestposting = '$guestpostingnew',
            minchars = '$mincharsnew',
            attachnum = '$attachnumnew',
            frules_status = '$frules_statusnew',
            frules = '$frulesnew',
            mt_status = '$mt_statusnew',
            mt_open = '$mt_opennew',
            mt_close = '$mt_closenew',
            closethreads = '$closethreadsnew',
            quickreply = '$quickreplynew',
            subjectprefixes = '$subjectprefixesnew',
            mpnp = '$mpnpnew',
            mpnt = '$mpntnew',
            mpfa = '$mpfanew',
            postcount = '$postcountnew'
            WHERE fid = '$fdetails'
        ");

        $delete = formInt('delete');
        if ($delete > 0) {
            $db->query("DELETE FROM " . X_PREFIX . "forums WHERE fid = '$delete'");
        }
        cp_message($lang['textforumupdate'], false, '', '</td></tr></table>', 'cp_forums.php', true, false, true);
    }
}

displayAdminPanel();

$fdetails = getRequestInt('fdetails');

if (noSubmit('forumsubmit') && empty($fdetails)) {
    viewPanel();
}

if (noSubmit('forumsubmit') && !empty($fdetails)) {
    viewDetailsPanel($fdetails);
}

if (onSubmit('forumsubmit')) {
    doPanel($fdetails);
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
