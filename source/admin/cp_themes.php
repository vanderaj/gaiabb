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

eval('$css = "' . template('css') . '";');

$download = formInt('download');

if (X_ADMIN) {
    if ($action == 'themes' && isset($download)) {
        $contents = array();
        $query = $db->query("SELECT * FROM " . X_PREFIX . "themes WHERE themeid='$download'");
        $themebits = $db->fetch_array($query);
        $db->free_result($query);
        foreach ($themebits as $key => $val) {
            if (!is_integer($key) && $key != 'themeid' && $key != 'dummy') {
                $contents[] = $key . '=' . $val;
            }
        }
        $name = str_replace(' ', '+', $themebits['name']);
        header("Content-Type: application/x-ms-download");
        header("Content-Disposition: filename=${name}-theme.gbb");
        echo implode("\r\n", $contents);
        exit();
    }
}

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['themes']);
btitle($lang['textcp']);
btitle($lang['themes']);

eval('echo "' . template('cp_header') . '";');

if (!X_ADMIN) {
    error($lang['adminonly'], false);
}

smcwcache();

$auditaction = $_SERVER['REQUEST_URI'];
$aapos = strpos($auditaction, "?");
if ($aapos !== false) {
    $auditaction = substr($auditaction, $aapos + 1);
}

$auditaction = addslashes("$onlineip|#|$auditaction");
adminaudit($self['username'], $auditaction, 0, 0);

displayAdminPanel();

function doImportTheme()
{
    global $db, $lang;
    $themebits = readFileAsINI($_FILES['themefile']['tmp_name']);
    if (!is_array($themebits)) {
        $themebits = (array) $themebits;
    }

    if (!array_key_exists('name', $themebits)) {
        cp_error($lang['textthemeimportfail'], false, '', '</td></tr></table>', 'cp_themes.php?action=themes');
    }

    $keysql = array();
    $valsql = array();
    foreach ($themebits as $key => $val) {
        if ($key == 'themeid') {
            $val = '';
            continue; // Jump over the identity row for MySQL 5.0
        } else
        if ($key == 'name') {
            $name = $val;
        }
        $keysql[] = $key;
        $valsql[] = "'$val'";
    }
    $keysql = implode(', ', $keysql);
    $valsql = implode(', ', $valsql);

    $query = $db->query("SELECT COUNT(themeid) FROM " . X_PREFIX . "themes WHERE name = '" . addslashes($name) . "'");
    if ($db->result($query, 0) > 0) {
        $db->free_result($query);
        cp_error($lang['theme_already_exists'], false, '', '</td></tr></table>', 'cp_themes.php?action=themes');
    }

    $sql = "INSERT INTO " . X_PREFIX . "themes ($keysql) VALUES ($valsql);";
    $query = $db->query($sql);
    if (!$query) {
        cp_message($lang['textthemeimportfail'], false, '', '</td></tr></table>', 'cp_themes.php?action=themes');
    } else {
        cp_message($lang['textthemeimportsuccess'], false, '', '</td></tr></table>', 'cp_themes.php?action=themes');
    }
}

function doUpdateTheme()
{
    global $db;

    $number_of_themes = $db->result($db->query("SELECT count(themeid) FROM " . X_PREFIX . "themes"), 0);
    $theme_delete = formArray('theme_delete');
    if (isset($theme_delete) && count($theme_delete) >= $number_of_themes) {
        cp_error($lang['delete_all_themes'], false, '', '</td></tr></table>', 'cp_themes.php?action=themes');
    }
    if (isset($theme_delete)) {
        foreach ($theme_delete as $themeid) {
            $otherid = $db->result($db->query("SELECT themeid FROM " . X_PREFIX . "themes WHERE themeid != '$themeid' ORDER BY rand() LIMIT 1"), 0);
            $db->query("UPDATE " . X_PREFIX . "members SET theme = '$otherid' WHERE theme = '$themeid'");
            $db->query("UPDATE " . X_PREFIX . "forums SET theme = '0' WHERE theme = '$themeid'");
            if ($CONFIG['theme'] == $themeid) {
                $db->query("UPDATE " . X_PREFIX . "settings SET theme = '$otherid'");
            }
            $db->query("DELETE FROM " . X_PREFIX . "themes WHERE themeid = '$themeid'");
        }
    }

    $theme_name = formArray('theme_name');
    foreach ($theme_name as $themeid => $name) {
        $name = addslashes(trim($name));
        $db->query("UPDATE " . X_PREFIX . "themes SET name = '$name' WHERE themeid = '$themeid'");
    }
    cp_message($lang['themeupdate'], false, '', '</td></tr></table>', 'cp_themes.php?action=themes');
}

function doDisplayThemes()
{
    global $db, $oToken;

    $single = formInt('single');

    $query = $db->query("SELECT * FROM " . X_PREFIX . "themes WHERE themeid = '$single'");
    $themedata = $db->fetch_array($query);
    $db->free_result($query);

    $roundon = $squareon = $none = '';
    switch ($themedata['outertable']) {
        case 'round':
            $roundon = $selHTML;
            break;
        case 'square':
            $squareon = $selHTML;
            break;
        default:
            $none = $selHTML;
            break;
    }

    $threadoptimg = $threadopttxt = '';
    switch ($themedata['threadopts']) {
        case 'image':
            $threadoptimg = $selHTML;
            break;
        default:
            $threadopttxt = $selHTML;
            break;
    }

    $shadowon = $shadowoff = '';
    formHelper::getThemeOnOffHtml('shadowfx', $shadowon, $shadowoff);
    $themeon = $themeoff = '';
    formHelper::getThemeOnOffHtml('themestatus', $themeon, $themeoff);
    $celloveron = $celloveroff = '';
    formHelper::getThemeOnOffHtml('celloverfx', $celloveron, $celloveroff);
    $riconon = $riconoff = '';
    formHelper::getThemeOnOffHtml('riconstatus', $riconon, $riconoff);
    $spacecatson = $spacecatsoff = '';
    formHelper::getThemeOnOffHtml('space_cats', $spacecatson, $spacecatsoff);

    $themedata['name'] = stripslashes($themedata['name']);
    $themedata['bgcolor'] = stripslashes($themedata['bgcolor']);
    $themedata['altbg1'] = stripslashes($themedata['altbg1']);
    $themedata['altbg2'] = stripslashes($themedata['altbg2']);
    $themedata['link'] = stripslashes($themedata['link']);
    $themedata['bordercolor'] = stripslashes($themedata['bordercolor']);
    $themedata['header'] = stripslashes($themedata['header']);
    $themedata['headertext'] = stripslashes($themedata['headertext']);
    $themedata['top'] = stripslashes($themedata['top']);
    $themedata['catcolor'] = stripslashes($themedata['catcolor']);
    $themedata['tabletext'] = stripslashes($themedata['tabletext']);
    $themedata['text'] = stripslashes($themedata['text']);
    $themedata['borderwidth'] = stripslashes($themedata['borderwidth']);
    $themedata['tablewidth'] = stripslashes($themedata['tablewidth']);
    $themedata['tablespace'] = stripslashes($themedata['tablespace']);
    $themedata['fontsize'] = stripslashes($themedata['fontsize']);
    $themedata['font'] = stripslashes($themedata['font']);
    $themedata['boardimg'] = stripslashes($themedata['boardimg']);
    $themedata['imgdir'] = stripslashes($themedata['imgdir']);
    $themedata['smdir'] = stripslashes($themedata['smdir']);
    $themedata['cattext'] = stripslashes($themedata['cattext']);
    $themedata['outerbgcolor'] = stripslashes($themedata['outerbgcolor']);
    $themedata['outertable'] = stripslashes($themedata['outertable']);
    $themedata['outertablewidth'] = stripslashes($themedata['outertablewidth']);
    $themedata['outerbordercolor'] = stripslashes($themedata['outerbordercolor']);
    $themedata['outerborderwidth'] = stripslashes($themedata['outerborderwidth']);
    $themedata['navsymbol'] = stripslashes($themedata['navsymbol']);
    $themedata['spacolor'] = stripslashes($themedata['spacolor']);
    $themedata['admcolor'] = stripslashes($themedata['admcolor']);
    $themedata['spmcolor'] = stripslashes($themedata['spmcolor']);
    $themedata['modcolor'] = stripslashes($themedata['modcolor']);
    $themedata['memcolor'] = stripslashes($themedata['memcolor']);
    $themedata['ricondir'] = stripslashes($themedata['ricondir']);
    $themedata['highlight'] = stripslashes($themedata['highlight']);

    if (false === strpos($themedata['catcolor'], '.')) {
        $catcode = 'style="background-color: ' . $themedata['catcolor'] . '"';
    } else {
        $catcode = 'style="background-image: url(../' . $themedata['imgdir'] . '/' . $themedata['catcolor'] . ')"';
    }
    if (false === strpos($themedata['top'], '.')) {
        $topcode = 'style="background-color: ' . $themedata['top'] . '"';
    } else {
        $topcode = 'style="background-image: url(../' . $themedata['imgdir'] . '/' . $themedata['top'] . ')"';
    }
    ?>
    <form method="post"
          action="cp_themes.php?action=themes&amp;single=submit">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="3"><?php echo $lang['Edit_Theme'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['Theme_Status'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select
                                        name="themestatusnew">
                                    <option value="on" <?php echo $themeon ?>><?php echo $lang['texton'] ?></option>
                                    <option value="off" <?php echo $themeoff ?>><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['space_cats'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select
                                        name="space_catsnew">
                                    <option value="on" <?php echo $spacecatson ?>><?php echo $lang['texton'] ?></option>
                                    <option value="off" <?php echo $spacecatsoff ?>><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['tableshadoweffects'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select
                                        name="shadowfxnew">
                                    <option value="on" <?php echo $shadowon ?>><?php echo $lang['texton'] ?></option>
                                    <option value="off" <?php echo $shadowoff ?>><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['themecell'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select
                                        name="celloverfxnew">
                                    <option value="on" <?php echo $celloveron ?>><?php echo $lang['texton'] ?></option>
                                    <option value="off" <?php echo $celloveroff ?>><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['riconstatus'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select
                                        name="riconstatusnew">
                                    <option value="on" <?php echo $riconon ?>><?php echo $lang['texton'] ?></option>
                                    <option value="off" <?php echo $riconoff ?>><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outertable'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select
                                        name="outertablenew">
                                    <option value="none" <?php echo $none ?>><?php echo $lang['none'] ?></option>
                                    <option value="round" <?php echo $roundon ?>><?php echo $lang['round'] ?></option>
                                    <option value="square" <?php echo $squareon ?>><?php echo $lang['square'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['threadoptstatus'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select
                                        name="threadoptsnew">
                                    <option value="text" <?php echo $threadopttxt ?>><?php echo $lang['threadopttext'] ?></option>
                                    <option value="image" <?php echo $threadoptimg ?>><?php echo $lang['threadoptimage'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texthemename'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" name="namenew"
                                        value="<?php echo $themedata['name'] ?>"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['navsymbol'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" name="navsymbolnew"
                                        value="<?php echo $themedata['navsymbol'] ?>"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textbgcolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="bgcolornew"
                                                                                value="<?php echo $themedata['bgcolor'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['bgcolor'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outerbgcolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="outerbgcolornew"
                                                                                value="<?php echo $themedata['outerbgcolor'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['outerbgcolor'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textaltbg1'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="altbg1new"
                                                                                value="<?php echo $themedata['altbg1'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['altbg1'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textaltbg2'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="altbg2new"
                                                                                value="<?php echo $themedata['altbg2'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['altbg2'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['highlight'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="highlightnew"
                                                                                value="<?php echo $themedata['highlight'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['highlight'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['spacolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="spacolornew"
                                                                                value="<?php echo $themedata['spacolor'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['spacolor'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['admcolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="admcolornew"
                                                                                value="<?php echo $themedata['admcolor'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['admcolor'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['spmcolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="spmcolornew"
                                                                                value="<?php echo $themedata['spmcolor'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['spmcolor'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['modcolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="modcolornew"
                                                                                value="<?php echo $themedata['modcolor'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['modcolor'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['memcolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="memcolornew"
                                                                                value="<?php echo $themedata['memcolor'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['memcolor'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textlink'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="linknew"
                                                                                value="<?php echo $themedata['link'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['link'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textborder'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="bordercolornew"
                                                                                value="<?php echo $themedata['bordercolor'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['bordercolor'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outerbordercolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="outerbordercolornew"
                                                                                value="<?php echo $themedata['outerbordercolor'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['outerbordercolor'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textheader'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="headernew"
                                                                                value="<?php echo $themedata['header'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['header'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textheadertext'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="headertextnew"
                                                                                value="<?php echo $themedata['headertext'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['headertext'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texttop'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="topnew"
                                                                                value="<?php echo $themedata['top'] ?>"/>
                            </td>
                            <td <?php echo $topcode ?>>&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textcatcolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="catcolornew"
                                                                                value="<?php echo $themedata['catcolor'] ?>"/>
                            </td>
                            <td <?php echo $catcode ?>>&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textcattextcolor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="cattextnew"
                                                                                value="<?php echo $themedata['cattext'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['cattext'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texttabletext'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="tabletextnew"
                                                                                value="<?php echo $themedata['tabletext'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['tabletext'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texttext'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="textnew"
                                                                                value="<?php echo $themedata['text'] ?>"/>
                            </td>
                            <td bgcolor="<?php echo $themedata['text'] ?>">&nbsp;</td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textborderwidth'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" name="borderwidthnew"
                                        value="<?php echo $themedata['borderwidth'] ?>" size="2"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outerborderwidth'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" name="outerborderwidthnew"
                                        value="<?php echo $themedata['outerborderwidth'] ?>" size="2"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textwidth'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" name="tablewidthnew"
                                        value="<?php echo $themedata['tablewidth'] ?>" size="3"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outertablewidth'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" name="outertablewidthnew"
                                        value="<?php echo $themedata['outertablewidth'] ?>" size="3"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textspace'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" name="tablespacenew"
                                        value="<?php echo $themedata['tablespace'] ?>" size="2"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textfont'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" name="fontnew"
                                        value="<?php echo htmlspecialchars($themedata['font']) ?>"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textbigsize'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" name="fontsizenew"
                                        value="<?php echo $themedata['fontsize'] ?>" size="4"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textboardlogo'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" value="<?php echo $themedata['boardimg'] ?>"
                                        name="boardlogonew"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['imgdir'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" value="<?php echo $themedata['imgdir'] ?>"
                                        name="imgdirnew"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['smdir'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" value="<?php echo $themedata['smdir'] ?>"
                                        name="smdirnew"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['ricondir'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input
                                        type="text" value="<?php echo $themedata['ricondir'] ?>"
                                        name="ricondirnew"/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                            <td colspan="3"><input type="submit" class="submit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/><input
                                        type="hidden" name="orig" value="<?php echo $single ?>"/></td>
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

function doDisplayANewTheme($single)
{
    global $THEME, $oToken;
    ?>
    <form method="post"
          action="cp_themes.php?action=themes&amp;single=submit">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['textnewtheme'] ?></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['Theme_Status'] ?></td>
                            <td colspan="2"><select name="themestatusnew">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['space_cats'] ?></td>
                            <td colspan="2"><select name="space_catsnew">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['tableshadoweffects'] ?></td>
                            <td colspan="2"><select name="shadowfxnew">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['themecell'] ?></td>
                            <td colspan="2"><select name="celloverfxnew">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['riconstatus'] ?></td>
                            <td colspan="2"><select name="riconstatusnew">
                                    <option value="on"><?php echo $lang['texton'] ?></option>
                                    <option value="off"><?php echo $lang['textoff'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['outertable'] ?></td>
                            <td colspan="2"><select name="outertablenew">
                                    <option value="none"><?php echo $lang['none'] ?></option>
                                    <option value="round"><?php echo $lang['round'] ?></option>
                                    <option value="square"><?php echo $lang['square'] ?></option>
                                </select></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['threadoptstatus'] ?></td>
                            <td colspan="2"><select name="threadoptsnew">
                                    <option value="text"><?php echo $lang['threadopttext'] ?></option>
                                    <option value="image"><?php echo $lang['threadoptimage'] ?></option>
                                </select></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['texthemename'] ?></td>
                            <td><input type="text" name="namenew" value=""/></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['navsymbol'] ?></td>
                            <td><input type="text" name="navsymbolnew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textbgcolor'] ?></td>
                            <td><input type="text" name="bgcolornew" value=""/></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['outerbgcolor'] ?></td>
                            <td><input type="text" name="outerbgcolornew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textaltbg1'] ?></td>
                            <td><input type="text" name="altbg1new" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textaltbg2'] ?></td>
                            <td><input type="text" name="altbg2new" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['highlight'] ?></td>
                            <td><input type="text" name="highlightnew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['spacolor'] ?></td>
                            <td><input type="text" name="spacolornew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['admcolor'] ?></td>
                            <td><input type="text" name="admcolornew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['spmcolor'] ?></td>
                            <td><input type="text" name="spmcolornew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['modcolor'] ?></td>
                            <td><input type="text" name="modcolornew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['memcolor'] ?></td>
                            <td><input type="text" name="memcolornew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textlink'] ?></td>
                            <td><input type="text" name="linknew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textborder'] ?></td>
                            <td><input type="text" name="bordercolornew" value=""/></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['outerbordercolor'] ?></td>
                            <td><input type="text" name="outerbordercolornew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textheader'] ?></td>
                            <td><input type="text" name="headernew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textheadertext'] ?></td>
                            <td><input type="text" name="headertextnew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['texttop'] ?></td>
                            <td><input type="text" name="topnew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textcatcolor'] ?></td>
                            <td><input type="text" name="catcolornew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textcattextcolor'] ?></td>
                            <td><input type="text" name="cattextnew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['texttabletext'] ?></td>
                            <td><input type="text" name="tabletextnew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['texttext'] ?></td>
                            <td><input type="text" name="textnew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textborderwidth'] ?></td>
                            <td><input type="text" name="borderwidthnew" size="2" value=""/></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['outerborderwidth'] ?></td>
                            <td><input type="text" name="outerborderwidthnew" size="2"
                                       value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textwidth'] ?></td>
                            <td><input type="text" name="tablewidthnew" size="3" value=""/></td>
                        </tr>
                        <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td><?php echo $lang['outertablewidth'] ?></td>
                            <td><input type="text" name="outertablewidthnew" size="3" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textspace'] ?></td>
                            <td><input type="text" name="tablespacenew" size="2" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textfont'] ?></td>
                            <td><input type="text" name="fontnew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textbigsize'] ?></td>
                            <td><input type="text" name="fontsizenew" size="4" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['textboardlogo'] ?></td>
                            <td><input type="text" name="boardlogonew" value=""/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['imgdir'] ?></td>
                            <td><input type="text" name="imgdirnew" value="images/"/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['smdir'] ?></td>
                            <td><input type="text" name="smdirnew" value="images/smilies"/></td>
                        </tr>
                        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                            <td><?php echo $lang['ricondir'] ?></td>
                            <td><input type="text" name="ricondirnew" value="images/ricons"/></td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input class="submit" type="submit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/> <input
                                        type="hidden" name="newtheme" value="<?php echo $single ?>"/></td>
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

function doThemeUpdateSubmit()
{
    global $db;

    $namenew = $db->escape(formVar('namenew'));
    $bgcolornew = $db->escape(formVar('bgcolornew'));
    $altbg1new = $db->escape(formVar('altbg1new'));
    $altbg2new = $db->escape(formVar('altbg2new'));
    $linknew = $db->escape(formVar('linknew'));
    $bordercolornew = $db->escape(formVar('bordercolornew'));
    $headernew = $db->escape(formVar('headernew'));
    $headertextnew = $db->escape(formVar('headertextnew'));
    $topnew = $db->escape(formVar('topnew'));
    $catcolornew = $db->escape(formVar('catcolornew'));
    $tabletextnew = $db->escape(formVar('tabletextnew'));
    $textnew = $db->escape(formVar('textnew'));
    $borderwidthnew = $db->escape(formVar('borderwidthnew'));
    $tablewidthnew = $db->escape(formVar('tablewidthnew'));
    $tablespacenew = $db->escape(formVar('tablespacenew'));
    $fontsizenew = $db->escape(formVar('fontsizenew'));
    $fontnew = $db->escape(formVar('fontnew'));
    $boardlogonew = $db->escape(formVar('boardlogonew'));
    $imgdirnew = $db->escape(formVar('imgdirnew'));
    $smdirnew = $db->escape(formVar('smdirnew'));
    $cattextnew = $db->escape(formVar('cattextnew'));
    $outerbgcolornew = $db->escape(formVar('outerbgcolornew'));
    $outertablenew = $db->escape(formVar('outertablenew'));
    $outertablewidthnew = $db->escape(formVar('outertablewidthnew'));
    $outerbordercolornew = $db->escape(formVar('outerbordercolornew'));
    $outerborderwidthnew = $db->escape(formVar('outerborderwidthnew'));
    $navsymbolnew = $db->escape(formVar('navsymbolnew'));
    $spacolornew = $db->escape(formVar('spacolornew'));
    $admcolornew = $db->escape(formVar('admcolornew'));
    $spmcolornew = $db->escape(formVar('spmcolornew'));
    $modcolornew = $db->escape(formVar('modcolornew'));
    $memcolornew = $db->escape(formVar('memcolornew'));
    $ricondirnew = $db->escape(formVar('ricondirnew'));
    $highlightnew = $db->escape(formVar('highlightnew'));

    $shadowfxnew = formOnOff('shadowfxnew');
    $themestatusnew = formOnOff('themestatusnew');
    $celloverfxnew = formOnOff('celloverfxnew');
    $riconstatusnew = formOnOff('riconstatusnew');
    $space_catsnew = formOnOff('space_catsnew');
    $threadoptsnew = ($threadoptsnew == 'image') ? 'image' : 'text';
    $outertablenew = ($outertablenew == 'none') ? 'none' : ($outertablenew == 'round' ? 'round' : 'square');

    $orig = formInt('orig');

    $db->query("UPDATE " . X_PREFIX . "themes SET
        name = '$namenew',
        bgcolor = '$bgcolornew',
        altbg1 = '$altbg1new',
        altbg2 = '$altbg2new',
        link = '$linknew',
        bordercolor = '$bordercolornew',
        header = '$headernew',
        headertext = '$headertextnew',
        top = '$topnew',
        catcolor = '$catcolornew',
        tabletext = '$tabletextnew',
        text = '$textnew',
        borderwidth = '$borderwidthnew',
        tablewidth = '$tablewidthnew',
        tablespace = '$tablespacenew',
        fontsize = '$fontsizenew',
        font = '$fontnew',
        boardimg = '$boardlogonew',
        imgdir = '$imgdirnew',
        smdir = '$smdirnew',
        cattext = '$cattextnew',
        outerbgcolor = '$outerbgcolornew',
        outertable = '$outertablenew',
        outertablewidth = '$outertablewidthnew',
        outerbordercolor = '$outerbordercolornew',
        outerborderwidth = '$outerborderwidthnew',
        shadowfx = '$shadowfxnew',
        threadopts = '$threadoptsnew',
        themestatus = '$themestatusnew',
        navsymbol = '$navsymbolnew',
        celloverfx = '$celloverfxnew',
        riconstatus = '$riconstatusnew',
        spacolor = '$spacolornew',
        admcolor = '$admcolornew',
        spmcolor = '$spmcolornew',
        modcolor = '$modcolornew',
        memcolor = '$memcolornew',
        ricondir = '$ricondirnew',
        highlight = '$highlightnew',
        space_cats = '$space_catsnew'
        WHERE themeid = '$orig'
        ");
    if (isset($themestatusnew) && $themestatusnew != 'on') {
        $db->query("UPDATE " . X_PREFIX . "members SET theme = '0' WHERE theme = '$orig'");
    }
    cp_message($lang['themeupdate'], false, '', '</td></tr></table>', 'cp_themes.php?action=themes');
}

function doNewThemeSubmit()
{
    global $db;

    $namenew = $db->escape(formVar('namenew'));
    $bgcolornew = $db->escape(formVar('bgcolornew'));
    $altbg1new = $db->escape(formVar('altbg1new'));
    $altbg2new = $db->escape(formVar('altbg2new'));
    $linknew = $db->escape(formVar('linknew'));
    $bordercolornew = $db->escape(formVar('bordercolornew'));
    $headernew = $db->escape(formVar('headernew'));
    $headertextnew = $db->escape(formVar('headertextnew'));
    $topnew = $db->escape(formVar('topnew'));
    $catcolornew = $db->escape(formVar('catcolornew'));
    $tabletextnew = $db->escape(formVar('tabletextnew'));
    $textnew = $db->escape(formVar('textnew'));
    $borderwidthnew = $db->escape(formVar('borderwidthnew'));
    $tablewidthnew = $db->escape(formVar('tablewidthnew'));
    $tablespacenew = $db->escape(formVar('tablespacenew'));
    $fontsizenew = $db->escape(formVar('fontsizenew'));
    $fontnew = $db->escape(formVar('fontnew'));
    $boardlogonew = $db->escape(formVar('boardlogonew'));
    $imgdirnew = $db->escape(formVar('imgdirnew'));
    $smdirnew = $db->escape(formVar('smdirnew'));
    $cattextnew = $db->escape(formVar('cattextnew'));
    $outerbgcolornew = $db->escape(formVar('outerbgcolornew'));
    $outertablenew = $db->escape(formVar('outertablenew'));
    $outertablewidthnew = $db->escape(formVar('outertablewidthnew'));
    $outerbordercolornew = $db->escape(formVar('outerbordercolornew'));
    $outerborderwidthnew = $db->escape(formVar('outerborderwidthnew'));
    $navsymbolnew = $db->escape(formVar('navsymbolnew'));
    $spacolornew = $db->escape(formVar('spacolornew'));
    $admcolornew = $db->escape(formVar('admcolornew'));
    $spmcolornew = $db->escape(formVar('spmcolornew'));
    $modcolornew = $db->escape(formVar('modcolornew'));
    $memcolornew = $db->escape(formVar('memcolornew'));
    $ricondirnew = $db->escape(formVar('ricondirnew'));
    $riconstatusnew = $db->escape(formVar('riconstatusnew'));
    $highlightnew = $db->escape(formVar('highlightnew'));
    $celloverfxnew = $db->escape(formVar('celloverfxnew'));
    $shadowfxnew = $db->escape(formVar('shadowfxnew'));
    $space_catsnew = $db->escape(formVar('space_catsnew'));
    $threadoptsnew = $db->escape(formVar('threadoptsnew'));
    $themestatusnew = $db->escape(formVar('themestatusnew'));

    $db->query("INSERT INTO " . X_PREFIX . "themes (themeid, name, bgcolor, altbg1, altbg2, link, bordercolor, header, headertext, top, catcolor, tabletext, text, borderwidth, tablewidth, tablespace, font, fontsize, boardimg, imgdir, smdir, cattext, outerbgcolor, outertable, outertablewidth, outerbordercolor, outerborderwidth, shadowfx, threadopts, themestatus, navsymbol, celloverfx, riconstatus, spacolor, admcolor, spmcolor, modcolor, memcolor, ricondir, highlight, space_cats) VALUES ('', '$namenew', '$bgcolornew', '$altbg1new', '$altbg2new', '$linknew', '$bordercolornew', '$headernew', '$headertextnew', '$topnew', '$catcolornew', '$tabletextnew', '$textnew', '$borderwidthnew', '$tablewidthnew', '$tablespacenew', '$fontnew', '$fontsizenew', '$boardlogonew', '$imgdirnew', '$smdirnew', '$cattextnew', '$outerbgcolornew', '$outertablenew', '$outertablewidthnew', '$outerbordercolornew', '$outerborderwidthnew', '$shadowfxnew', '$threadoptsnew', '$themestatusnew', '$navsymbolnew', '$celloverfxnew', '$riconstatusnew', '$spacolornew', '$admcolornew', '$spmcolornew', '$modcolornew', '$memcolornew', '$ricondirnew', '$highlightnew', '$space_catsnew')");
    cp_message($lang['themeupdate'], false, '', '</td></tr></table>', 'cp_themes.php?action=themes');
}

function doDisplayThemePanel()
{
    global $db, $lang, $THEME, $oToken;

    ?>
    <form method="post" action="cp_themes.php?action=themes"
          name="theme_main">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" align="center"><?php echo $lang['textdeleteques'] ?></td>
                            <td class="title" align="center"><?php echo $lang['textthemename'] ?></td>
                            <td class="title" align="center"><?php echo $lang['numberusing'] ?></td>
                            <td class="title" align="center"><?php echo $lang['status'] ?></td>
                        </tr>
                        <?php
// altered theme code to produce a 20x speed increase
    $themeMem = array(
        0 => 0,
    );
    $tq = $db->query("SELECT theme, COUNT(theme) as cnt FROM " . X_PREFIX . "members GROUP BY theme");
    while (($t = $db->fetch_array($tq)) != false) {
        $themeMem[((int) $t['theme'])] = $t['cnt'];
    }
    $db->free_result($tq);

    $query = $db->query("SELECT name, themestatus, themeid FROM " . X_PREFIX . "themes ORDER BY name ASC");
    while (($themeinfo = $db->fetch_array($query)) != false) {
        $themeid = $themeinfo['themeid'];
        if (!isset($themeMem[$themeid])) {
            $themeMem[$themeid] = 0;
        }

        if ($themeinfo['themeid'] == $CONFIG['theme']) {
            $members = ($themeMem[$themeid] + $themeMem[0]);
        } else {
            $members = $themeMem[$themeid];
        }

        if ($themeinfo['themeid'] == $theme) {
            $checked = $cheHTML;
        } else {
            $checked = 'checked="unchecked"';
        }
        ?>
                            <tr>
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>" class="ctrtablerow"><input
                                            type="checkbox" name="theme_delete[]"
                                            value="<?php echo $themeinfo['themeid'] ?>"/></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"><input
                                            type="text" name="theme_name[<?php echo $themeinfo['themeid'] ?>]"
                                            value="<?php echo $themeinfo['name'] ?>"/>&nbsp;[ <a
                                            href="./cp_themes.php?action=themes&amp;single=<?php echo $themeinfo['themeid'] ?>"><?php echo $lang['textdetails'] ?></a>
                                    ]&nbsp;-&nbsp;[ <a
                                            href="./cp_themes.php?action=themes&amp;download=<?php echo $themeinfo['themeid'] ?>"><?php echo $lang['textdownload'] ?></a>
                                    ]
                                </td>
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                    class="ctrtablerow"><?php echo $members ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>"
                                    class="ctrtablerow"><?php echo $themeinfo['themestatus'] ?></td>
                            </tr>
                            <?php
}
    $db->free_result($query);
    ?>
                        <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                            <td colspan="4"><a
                                        href="./cp_themes.php?action=themes&amp;single=anewtheme1"><strong><?php echo $lang['textnewtheme'] ?></strong></a>
                                - <a href="#"
                                     onclick="setCheckboxes('theme_main', 'theme_delete[]', true); return false;"><?php echo $lang['checkall'] ?></a>
                                - <a href="#"
                                     onclick="setCheckboxes('theme_main', 'theme_delete[]', false); return false;"><?php echo $lang['uncheckall'] ?></a>
                                - <a href="#"
                                     onclick="invertSelection('theme_main', 'theme_delete[]'); return false;"><?php echo $lang['invertselection'] ?></a>
                            </td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="4"><input type="submit" name="themesubmit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>" class="submit"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php echo $shadow2 ?>
    </form>
    <br/>
    <form method="post" action="cp_themes.php?action=themes"
          enctype="multipart/form-data">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?> "/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td colspan="2" class="title"><?php echo $lang['textimporttheme'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textthemefile'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="file"
                                                                                name="themefile" value="" size="40"/>
                            </td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input type="submit" class="submit"
                                                   name="importsubmit"
                                                   value="<?php echo $lang['textimportsubmit'] ?>"/></td>
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

$single = formVar('single');
if ($action == 'themes') {
    if (noSubmit('themesubmit') && !isset($single) && noSubmit('importsubmit')) {
        doDisplayThemePanel();
    } else
    if (onSubmit('importsubmit') && isset($_FILES['themefile']['tmp_name'])) {
        doImportTheme();
    } else
    if (onSubmit('themesubmit')) {
        doUpdateTheme();
    } else
    if (isset($single) && $single != 'submit' && $single != 'anewtheme1') {
        doDisplayThemes();
    } else
    if (isset($single) && $single == 'anewtheme1') {
        doDisplayANewTheme($single);
    } else
    if (isset($single) && $single == 'submit' && !isset($newtheme)) {
        doThemeUpdateSubmit();
    } else
    if (isset($single) && $single == 'submit' && isset($newtheme)) {
        doNewThemeSubmit();
    }
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
