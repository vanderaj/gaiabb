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

require_once '../header.php';
require_once '../class/admincp.inc.php';

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error', 'functions_bbcode', 'functions_bbcodeinsert');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['textfaq'] . ' ' . $lang['faq_N']);
btitle($lang['textcp']);
btitle($lang['textfaq'] . ' ' . $lang['faq_N']);

eval('$css = "' . template('css') . '";');
if ($bbcode_js != '') {
    $bbcode_js_sc = 'bbcodefns-' . $bbcode_js . '.js';
} else {
    $bbcode_js_sc = 'bbcodefns.js';
}

eval('$bbcodescript = "' . template('functions_bbcode') . '";');
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
    global $selHTML, $cheHTML;

    $groups = array();
    $items = array();
    $items['0'] = array();
    $itemlist = array();
    $i = 0;
    $query = $db->query("SELECT fid, type, name, displayorder, status, fup, code, view FROM " . X_PREFIX . "faq ORDER BY fup ASC, displayorder ASC");
    while (($selItems = $db->fetch_array($query)) != false) {
        if ($selItems['type'] == 'group') {
            $groups[$i]['fid'] = $selItems['fid'];
            $groups[$i]['name'] = $selItems['name'];
            $groups[$i]['displayorder'] = $selItems['displayorder'];
            $groups[$i]['status'] = $selItems['status'];
            $groups[$i]['fup'] = $selItems['fup'];
            $groups[$i]['code'] = $selItems['code'];
        } else
        if ($selItems['type'] == 'item') {
            $id = (empty($selItems['fup'])) ? 0 : $selItems['fup'];
            $items[$id][$i]['fid'] = $selItems['fid'];
            $items[$id][$i]['name'] = $selItems['name'];
            $items[$id][$i]['displayorder'] = $selItems['displayorder'];
            $items[$id][$i]['status'] = $selItems['status'];
            $items[$id][$i]['fup'] = $selItems['fup'];
            $items[$id][$i]['view'] = $selItems['view'];
            $itemlist[$i]['fid'] = $selItems['fid'];
            $itemlist[$i]['name'] = $selItems['name'];
        }
        $i++;
    }
    $db->free_result($query);
    ?>
    <form method="post" action="cp_faq.php">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title"><?php echo $lang['FAQ_Management_System'] ?></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                            <td><?php echo $lang['faq_O'] ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php echo $shadow2 ?>
        <br/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title"><?php echo $lang['faq_I'] ?></td>
                        </tr>
                        <?php
foreach ($items['0'] as $item) {
        $on = $off = '';
        if ($item['status'] == 'on') {
            $on = $selHTML;
        } else {
            $off = $selHTML;
        }
        ?>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td class="smalltxt"><input type="checkbox"
                                                            name="delete<?php echo $item['fid'] ?>"
                                                            value="<?php echo $item['fid'] ?>"/> &nbsp;<input
                                            type="text"
                                            name="name<?php echo $item['fid'] ?>"
                                            value="<?php echo stripslashes($item['name']) ?>"/>
                                    &nbsp; <?php echo $lang['textorder'] ?> <input type="text"
                                                                                   name="displayorder<?php echo $item['fid'] ?>"
                                                                                   size="2"
                                                                                   value="<?php echo $item['displayorder'] ?>"/>
                                    &nbsp; <select
                                            name="status<?php echo $item['fid'] ?>">
                                        <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                        <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                    </select> &nbsp; <select name="moveto<?php echo $item['fid'] ?>">
                                        <option
                                                value="" selected="selected">-<?php echo $lang['textnone'] ?>-
                                        </option>
                                        <?php
foreach ($groups as $moveforum) {
            echo '<option value="' . $moveforum['fid'] . '">' . stripslashes($moveforum['name']) . '</option>';
        }
        ?>
                                    </select> <a title="<?php echo stripslashes($item['name']) ?>"
                                                 href="cp_faq.php?fdetails=<?php echo $item['fid'] ?>"><?php echo $lang['faq_F'] ?></a>
                                </td>
                            </tr>
                            <?php
}

    foreach ($groups as $group) {
        $on = $off = '';
        if ($group['status'] == 'on') {
            $on = $selHTML;
        } else {
            $off = $selHTML;
        }
        ?>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td>&nbsp;</td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                                <td class="smalltxt"><input type="checkbox"
                                                            name="delete<?php echo $group['fid'] ?>"
                                                            value="<?php echo $group['fid'] ?>"/> <input type="text"
                                                                                                         name="name<?php echo $group['fid'] ?>"
                                                                                                         value="<?php echo stripslashes($group['name']) ?>"/>
                                    &nbsp; <?php echo $lang['textorder'] ?> <input type="text"
                                                                                   name="displayorder<?php echo $group['fid'] ?>"
                                                                                   size="2"
                                                                                   value="<?php echo $group['displayorder'] ?>"/>
                                    &nbsp; <select
                                            name="status<?php echo $group['fid'] ?>">
                                        <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                        <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                    </select> &nbsp; <a
                                            href="cp_faq.php?gdetails=<?php echo $group['fid'] ?>"><?php echo $lang['faq_F'] ?></a>
                                </td>
                            </tr>
                            <?php
if (array_key_exists($group['fid'], $items)) {
            foreach ($items[$group['fid']] as $item) {
                $on = $off = '';
                if ($item['status'] == 'on') {
                    $on = $selHTML;
                } else {
                    $off = $selHTML;
                }
                ?>
                                    <tr bgcolor="<?php echo $THEME['altbg2'] ?>"
                                        class="tablerow">
                                        <td class="smalltxt">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input
                                                    type="checkbox" name="delete<?php echo $item['fid'] ?>"
                                                    value="<?php echo $item['fid'] ?>"/> &nbsp;<input type="text"
                                                                                                      name="name<?php echo $item['fid'] ?>"
                                                                                                      value="<?php echo stripslashes($item['name']) ?>"/>
                                            &nbsp; <?php echo $lang['textorder'] ?> <input
                                                    type="text" name="displayorder<?php echo $item['fid'] ?>" size="2"
                                                    value="<?php echo $item['displayorder'] ?>"/> &nbsp; <select
                                                    name="status<?php echo $item['fid'] ?>">
                                                <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                                <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                            </select> &nbsp; <select name="moveto<?php echo $item['fid'] ?>">
                                                <option
                                                        value="">-<?php echo $lang['textnone'] ?>-
                                                </option>
                                                <?php
foreach ($groups as $moveforum) {
                    if ($moveforum['fid'] == $item['fup']) {
                        $curgroup = $selHTML;
                    } else {
                        $curgroup = '';
                    }
                    echo '<option value="' . $moveforum['fid'] . '" ' . $curgroup . '>' . stripslashes($moveforum['name']) . '</option>';
                }
                ?>
                                            </select> <a
                                                    title="<?php echo stripslashes($item['name']) ?>"
                                                    href="cp_faq.php?fdetails=<?php echo $item['fid'] ?>"><?php echo $lang['faq_F'] ?></a>
                                        </td>
                                    </tr>
                                    <?php
}
        }
    }
    ?>
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow"><input
                                        class="submit" type="submit" name="faqsubmit"
                                        value="<?php echo $lang['textsubmitchanges'] ?>"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php echo $shadow2 ?>
        <br/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title"><?php echo $lang['faq_B'] ?></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td class="smalltxt"><input type="text" name="newgname"
                                                        value="<?php echo $lang['faq_G'] ?>" size="40"/>
                                &nbsp; <?php echo $lang['textorder'] ?> <input type="text"
                                                                               name="newgorder" size="2"/> &nbsp;
                                <select name="newgstatus">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="smalltxt"><input
                                        type="text" name="newfname" value="<?php echo $lang['faq_H'] ?>"
                                        size="61"/> <br/>
                                <textarea rows="5" name="newfdesc" cols="60"><?php echo $lang['faq_J'] ?></textarea>
                                <br/><?php echo $lang['textorder'] ?> <input type="text"
                                                                             name="newforder" size="2"/> &nbsp; <select
                                        name="newfstatus">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select> &nbsp; <select name="newffup">
                                    <option value=""
                                            selected="selected">-<?php echo $lang['textnone'] ?>-
                                    </option>
                                    <?php
foreach ($groups as $group) {
        echo '<option value="' . $group['fid'] . '">' . stripslashes($group['name']) . '</option>';
    }
    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow"><input
                                        class="submit" type="submit" name="faqsubmit"
                                        value="<?php echo $lang['textsubmitchanges'] ?>"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
    <?php echo $shadow2 ?>
    </td>
    </tr>
    </table>
    <?php
}

function dogDetailsPanel($gdetails)
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML;
    ?>
    <form method="post" action="cp_faq.php?gdetails=<?php echo $gdetails ?>">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['faq_E'] ?></td>
                        </tr>
                        <?php
$queryg = $db->query("SELECT fid, name, status, displayorder, code FROM " . X_PREFIX . "faq WHERE fid = '$gdetails'");
    $group = $db->fetch_array($queryg);
    $db->free_result($queryg);

    $group['name'] = stripslashes($group['name']);

    $ic00 = $ic01 = $ic02 = $ic03 = $ic04 = '';
    if ($group['code'] == 'usermaint') {
        $ic01 = $selHTML;
    } else
    if ($group['code'] == 'using') {
        $ic02 = $selHTML;
    } else
    if ($group['code'] == 'messages') {
        $ic03 = $selHTML;
    } else
    if ($group['code'] == 'misc') {
        $ic04 = $selHTML;
    } else {
        $ic00 = $selHTML;
    }

    $on = $off = '';
    if ($group['status'] == 'on') {
        $on = $selHTML;
    } else {
        $off = $selHTML;
    }
    ?>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['faq_P'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="namenew"
                                                                                value="<?php echo $group['name'] ?>"/>
                            </td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textorder'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="displayordernew" size="2"
                                                                                value="<?php echo $group['displayorder'] ?>"/>
                            </td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['pmreadstatus'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select
                                        name="groupstatusnew">
                                    <option value="on" <?php echo $on ?>><?php echo $lang['texton'] ?></option>
                                    <option value="off" <?php echo $off ?>><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['faq_Q'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select name="codenew">
                                    <option
                                            value="" <?php echo $ic00 ?>>-<?php echo $lang['textnone'] ?>-
                                    </option>
                                    <option value="usermaint" <?php echo $ic01 ?>>usermaint</option>
                                    <option value="using" <?php echo $ic02 ?>>using</option>
                                    <option value="messages" <?php echo $ic03 ?>>messages</option>
                                    <option value="misc" <?php echo $ic04 ?>>misc</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"
                                align="left" colspan="2"><?php echo $lang['faq_R'] ?></td>
                        </tr>
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow"
                                colspan="2"><input class="submit" type="submit" name="gsubmit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
    <?php echo $shadow2 ?>
    </td>
    </tr>
    </table>
    <?php
}

function dogDetails($gdetails)
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;

    $name = $db->escape(formVar('namenew'));
    $code = $db->escape(formVar('codenew'));

    $displayordernew = $db->escape(formVar('displayordernew'));
    $groupstatusnew = $db->escape(formVar('groupstatusnew'));

    $querygd = $db->query("SELECT fid FROM " . X_PREFIX . "faq WHERE (type = 'group' AND fid = '$gdetails')");
    $theid = $db->result($querygd, 0);
    $db->free_result($querygd);

    $db->query("UPDATE " . X_PREFIX . "faq SET name = '$name', code = '$code', displayorder = '$displayordernew', status = '$groupstatusnew' WHERE fid = '$theid'");

    cp_message($lang['faq_A'], false, '', '</td></tr></table>', 'cp_faq.php', true, false, true);
}

function dofDetailsPanel($fdetails)
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;
    global $selHTML, $cheHTML, $bbcodeinsert;
    ?>
    <form method="post" action="cp_faq.php?fdetails=<?php echo $fdetails ?>">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['faq_E'] ?></td>
                        </tr>
                        <?php
$queryg = $db->query("SELECT * FROM " . X_PREFIX . "faq WHERE fid = '$fdetails'");
    $item = $db->fetch_array($queryg);
    $db->free_result($queryg);

    if ($item['allowsmilies'] == 'yes') {
        $checked3 = $cheHTML;
    } else {
        $checked3 = '';
    }

    if ($item['allowbbcode'] == 'yes') {
        $checked4 = $cheHTML;
    } else {
        $checked4 = '';
    }

    if ($item['allowimgcode'] == 'yes') {
        $checked5 = $cheHTML;
    } else {
        $checked5 = '';
    }

    $ic00 = $ic01 = $ic02 = $ic03 = '';
    if ($item['view'] == 1) {
        $ic01 = $selHTML;
    } else
    if ($item['view'] == 2) {
        $ic02 = $selHTML;
    } else
    if ($item['view'] == 3) {
        $ic03 = $selHTML;
    } else {
        $ic00 = $selHTML;
    }

    $item['name'] = stripslashes($item['name']);
    $item['description'] = stripslashes($item['description']);
    ?>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['faq_C'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="namenew"
                                                                                value="<?php echo $item['name'] ?>"
                                                                                size="60"/></td>
                        </tr>
                        <?php echo $bbcodeinsert ?>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" valign="top"><?php echo $lang['faq_D'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><textarea rows="20"
                                                                                   cols="60" name="descnew" id="message"
                                                                                   onselect="storeCaret(this);"
                                                                                   onclick="storeCaret(this);"
                                                                                   onkeyup="storeCaret(this);"><?php echo $item['description'] ?></textarea>
                            </td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                valign="top"><?php echo $lang['textallow'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="smalltxt"><input
                                        type="checkbox" name="allowsmiliesnew" value="yes"
                                    <?php echo $checked3 ?> /><?php echo $lang['textsmilies'] ?><br/>
                                <input type="checkbox" name="allowbbcodenew" value="yes"
                                    <?php echo $checked4 ?> /><?php echo $lang['textbbcode'] ?><br/> <input
                                        type="checkbox" name="allowimgcodenew" value="yes"
                                    <?php echo $checked5 ?> /><?php echo $lang['textimgcode'] ?><br/>
                            </td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['faq_S'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><select name="viewnew">
                                    <option
                                            value="0" <?php echo $ic00 ?>>-<?php echo $lang['textnone'] ?>-
                                    </option>
                                    <option value="1" <?php echo $ic01 ?>><?php echo $lang['smilies'] ?></option>
                                    <option value="2" <?php echo $ic02 ?>><?php echo $lang['textuserranks'] ?></option>
                                    <option value="3" <?php echo $ic03 ?>><?php echo $lang['smilies'] ?>
                                        + <?php echo $lang['textuserranks'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><strong><?php echo $lang['textdeleteques'] ?>
                                    :</strong></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="checkbox"
                                                                                name="delete"
                                                                                value="<?php echo $item['fid'] ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow"
                                colspan="2"><input class="submit" type="submit" name="faqsubmit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
    <?php echo $shadow2 ?>
    </td>
    </tr>
    </table>
    <?php
}

function faqSubmit($fdetails)
{
    global $THEME, $lang, $shadow2, $oToken, $db, $CONFIG;

    if (empty($fdetails)) {
        $db->query("DELETE FROM " . X_PREFIX . "faq WHERE name = ''");

        $queryforum = $db->query("SELECT fid, type FROM " . X_PREFIX . "faq WHERE type = 'item'");
        while (($item = $db->fetch_array($queryforum)) != false) {
            $displayorder = "displayorder$item[fid]";
            $displayorder = formInt($displayorder);
            $name = "name$item[fid]";
            $name = $db->escape(formVar($name));
            $self['status'] = "status" . $item['fid'];
            $self['status'] = $db->escape(formVar($self['status']));
            $delete = "delete$item[fid]";
            $delete = formInt($delete);
            $moveto = "moveto$item[fid]";
            $moveto = formInt($moveto);

            if ($delete > 0) {
                $db->query("DELETE FROM " . X_PREFIX . "faq WHERE type='item' AND fid='$delete'");
            }

            $db->query("UPDATE " . X_PREFIX . "faq SET name='$name', displayorder='$displayorder', status='$self[status]', fup='$moveto' WHERE fid='$item[fid]'");
        }
        $db->free_result($queryforum);

        $querygroup = $db->query("SELECT fid FROM " . X_PREFIX . "faq WHERE type='group'");
        while (($group = $db->fetch_array($querygroup)) != false) {
            $delete = "delete$group[fid]";
            $delete = formInt($delete);
            if ($delete > 0) {
                $query = $db->query("SELECT fid FROM " . X_PREFIX . "faq WHERE type = 'item' AND fup = '$delete'");
                while (($item = $db->fetch_array($query)) != false) {
                    $db->query("UPDATE " . X_PREFIX . "faq SET fup = '' WHERE type = 'item' AND fup = '$delete'");
                }

                $db->query("DELETE FROM " . X_PREFIX . "faq WHERE type = 'group' AND fid = '$delete'");
            }

            $name = "name$group[fid]";
            $name = $db->escape(formVar($name));
            $displayorder = "displayorder$group[fid]";
            $displayorder = formInt($displayorder);
            $self['status'] = "status$group[fid]";
            $self['status'] = $db->escape(formVar($self['status']));

            $db->query("UPDATE " . X_PREFIX . "faq SET name='$name', displayorder='$displayorder', status='$self[status]' WHERE fid = '$group[fid]'");
        }
        $db->free_result($querygroup);

        $newgname = $db->escape(formVar('newgname'));
        $newgstatus = $db->escape(formVar('newgstatus'));
        $newgorder = formInt('newgorder');

        if ($newgname != $lang['faq_G']) {
            $db->query("INSERT INTO " . X_PREFIX . "faq (type, fid, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode) VALUES ('group', '', '$newgname', '$newgstatus', '$newgorder', '', '', '', '', '')");
        }

        $newfname = $db->escape(formVar('newfname'));
        $newfdesc = $db->escape(formVar('newfdesc'));
        $newforder = formInt('newforder');
        $newfstatus = $db->escape(formVar('newfstatus'));
        $newffup = $db->escape(formVar('newffup'));

        if ($newfname != $lang['faq_H']) {
            $db->query("INSERT INTO " . X_PREFIX . "faq (type, fid, name, status, displayorder, description, allowsmilies, allowbbcode, fup, allowimgcode) VALUES ('item', '', '$newfname', '$newfstatus', '$newforder', '$newfdesc', 'yes', 'yes', '$newffup', 'yes')");
        }
    } else {
        $allowsmiliesnew = formYesNo('allowsmiliesnew');
        $allowbbcodenew = formYesNo('allowbbcodenew');
        $allowimgcodenew = formYesNo('allowimgcodenew');

        $namenew = $db->escape(formVar('namenew'));
        $descnew = $db->escape(formVar('descnew'));
        $viewnew = $db->escape(formVar('viewnew'));

        $db->query("UPDATE " . X_PREFIX . "faq SET name='$namenew', description='$descnew', allowsmilies='$allowsmiliesnew', allowbbcode='$allowbbcodenew', allowimgcode='$allowimgcodenew', view='$viewnew' WHERE fid='$fdetails'");

        $delete = formInt('delete');
        if ($delete > 0) {
            $db->query("DELETE FROM " . X_PREFIX . "faq WHERE fid = '$delete'");
        }
    }
    cp_message($lang['faq_A'], false, '', '</td></tr></table>', 'cp_faq.php', true, false, true);
}

displayAdminPanel();

$bbcodeinsert = bbcodeinsert();

$config_cache->expire('settings');
$moderators_cache->expire('moderators');
$config_cache->expire('theme');
$config_cache->expire('pluglinks');
$config_cache->expire('whosonline');
$config_cache->expire('forumjump');

$fdetails = getRequestInt('fdetails');
$gdetails = getRequestInt('gdetails');

if ($fdetails > 0) {
    if (noSubmit('faqsubmit')) {
        dofDetailsPanel($fdetails);
    } else
    if (onSubmit('faqsubmit')) {
        $oToken->assert_token();
        faqSubmit($fdetails);
    }
} else if ($gdetails > 0) {
    if (noSubmit('faqsubmit')) {
        dogDetailsPanel($gdetails);
    } else if (onSubmit('faqsubmit')) {
        $oToken->assert_token();
        dogDetails($gdetails);
    }
} else {
    viewPanel();
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
