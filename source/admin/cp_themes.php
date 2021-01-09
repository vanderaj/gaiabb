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
require_once ROOT . 'helper/formHelper.php';

require_once ROOT . 'include/admincp.inc.php';
require_once ROOT . 'views/admin.themes.view.php';

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

if (X_ADMIN && $action == 'downloadtheme') {
    downloadTheme();
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

$view = new GaiaBB\AdminThemeView();

displayAdminPanel();

function importTheme()
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
            continue;
        } elseif ($key == 'name') {
            $name = $val;
        }
        $keysql[] = $key;
        $valsql[] = "'$val'";
    }
    $keysql = implode(', ', $keysql);
    $valsql = implode(', ', $valsql);

    $query = $db->query("SELECT COUNT(themeid) FROM " . X_PREFIX . "themes WHERE name = '" . addslashes($name) . "'");
    if ($db->result($query, 0) > 0) {
        $db->freeResult($query);
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

function updateAllThemes()
{
    global $db, $lang, $CONFIG;

    $number_of_themes = $db->result($db->query("SELECT count(themeid) FROM " . X_PREFIX . "themes"), 0);
    $theme_delete = formArray('theme_delete');
    if (isset($theme_delete) && count($theme_delete) >= $number_of_themes) {
        cp_error($lang['delete_all_themes'], false, '', '</td></tr></table>', 'cp_themes.php?action=themes');
    }
    if (isset($theme_delete)) {
        foreach ($theme_delete as $themeid) {
            $otherid = $db->result($db->query("SELECT themeid FROM " .
                X_PREFIX . "themes WHERE themeid != '$themeid' ORDER BY rand() LIMIT 1"), 0);
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

function updateSingleTheme()
{
    global $db, $lang;

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
    $threadoptsnew = $db->escape(formVar('threadoptsnew'));

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

function createTheme()
{
    global $db, $lang;

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

function downloadTheme()
{
    global $db;

    $themeId = getInt('themeId');

    $query = $db->query("SELECT * FROM " . X_PREFIX . "themes WHERE themeid='".$themeId."'");
    $themebits = $db->fetchArray($query);
    $db->freeResult($query);

    $contents = array();
    foreach ($themebits as $key => $val) {
        if (!is_integer($key) && $key != 'themeid' && $key != 'dummy') {
            $contents[] = $key . '=' . $val;
        }
    }

    $name = str_replace(' ', '+', $themebits['name']);
    
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=".$name."-theme.gbb");
    echo implode("\r\n", $contents);
    redirect('cp_themes.php', 10);
}

switch ($action) {
    case 'createtheme':
        createTheme();
        break;

    case 'importtheme':
        importTheme();
        break;

    case 'updatesingletheme':
        updateSingleTheme();
        break;

    case 'updateallthemes':
        updateAllThemes();
        break;

    case 'newtheme':
        $view->displayNewThemePanel();
        break;

    case 'displaytheme':
        $view->displaySingleTheme();
        break;

    default:
        $view->displayThemePanel();
        break;
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
