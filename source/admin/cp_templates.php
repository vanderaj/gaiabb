<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2022 The GaiaBB Group
 * https://github.com/vanderaj/gaiabb
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

define('ROOT', '../');
define('ROOTINC', '../include/');
define('ROOTCLASS', '../class/');

require_once ROOT . 'header.php';
require_once ROOTINC . 'admincp.inc.php';

loadtpl(
    'cp_header',
    'cp_footer',
    'cp_message',
    'cp_error'
);

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

eval('$css = "' . template('css') . '";');

if (X_SADMIN) {
    if ($action == 'templates' && isset($download)) {
        $code = '';
        $templates = $db->query("SELECT * FROM " . X_PREFIX . "templates");
        while ($template = $db->fetch_array($templates)) {
            $template['template'] = trim($template['template']);
            $template['name'] = trim($template['name']);
            if ($template['name'] != '') {
                $template['template'] = stripslashes($template['template']);
                $code .= $template['name'] . "|#*UBB TEMPLATE*#|\r\n" . $template['template'] . "\r\n\r\n|#*UBB TEMPLATE FILE*#|";
            }
        }
        header("Content-disposition: attachment; filename=templates.ubb");
        header("Content-Length: " . strlen($code));
        header("Content-type: unknown/unknown");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $code;
        exit;
    }
}

nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['templates']);
btitle($lang['textcp']);
btitle($lang['templates']);

eval('echo "' . template('cp_header') . '";');

if (!X_SADMIN) {
    error($lang['superadminonly'], false);
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

if ($action == 'templates') {
    if (noSubmit('edit') && noSubmit('editsubmit') && noSubmit('delete') && noSubmit('deletesubmit') && noSubmit('new') && noSubmit('restore') && noSubmit('restoresubmit') && noSubmit('rename') && noSubmit('renamesubmit') && noSubmit('backup_cur') && noSubmit('backup_curyes') && noSubmit('restore_cur') && noSubmit('restore_curyes')) {
        $templatelist = array();
        $templatelist[] = '<select name="tid">';
        $query = $db->query("SELECT * FROM " . X_PREFIX . "templates ORDER BY name");
        $templatelist[] = '<option value="default" selected="selected">' . $lang['selecttemplate'] . '</option>';
        while ($template = $db->fetch_array($query)) {
            if (!empty($template['name'])) {
                $templatelist[] = '<option value="' . $template['id'] . '">' . $template['name'] . '</option>';
            }
        }
        $templatelist[] = '</select>';
        $templatelist = implode("\n", $templatelist);
        $db->free_result($query);
        ?>
        <form method="post" action="cp_templates.php?action=templates">
        <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
        <tr>
        <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
        <tr class="category">
        <td class="title"><?php echo $lang['templates'] ?></td>
        </tr>
        <tr class="tablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text" name="newtemplatename" size="30" maxlength="50" value="" />&nbsp;&nbsp;<input type="submit" class="submit" name="new" value="<?php echo $lang['newtemplate'] ?>" /></td>
        </tr>
        <tr class="tablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $templatelist ?></td>
        </tr>
        <tr class="tablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>">
        <input type="submit" class="submit" name="edit" value="<?php echo $lang['textedit'] ?>" />&nbsp;
        <input type="submit" class="submit" name="delete" value="<?php echo $lang['deletebutton'] ?>" />&nbsp;
        <input type="submit" class="submit" name="rename" value="<?php echo $lang['template_button'] ?>" />&nbsp;
        <input type="submit" class="submit" name="restore" value="<?php echo $lang['textrestoredeftemps'] ?>" />&nbsp;
        <input type="submit" class="submit" name="download" value="<?php echo $lang['textdownloadtemps'] ?>" />
        </td>
        </tr>
        <tr class="tablerow">
        <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['templatedef_note'] ?></td>
        </tr>
        <tr class="tablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>">
        <input type="submit" class="submit" name="backup_cur" value="<?php echo $lang['template_backupcur'] ?>" />&nbsp;
        <input type="submit" class="submit" name="restore_cur" value="<?php echo $lang['template_restorecur'] ?>" />
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

    if (onSubmit('restore')) {
        ?>
        <form method="post" action="cp_templates.php?action=templates">
        <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
        <tr>
        <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
        <tr class="category">
        <td class="title"><?php echo $lang['templates'] ?></td>
        </tr>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['templaterestoreconfirm'] ?></td>
        </tr>
        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
        <td><input type="submit" class="submit" name="restoresubmit" value="<?php echo $lang['textyes'] ?>" /></td>
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

    if (onSubmit('restoresubmit')) {
        if (!file_exists('./templates.ubb')) {
            cp_error($lang['no_templates'], false, '', '</td></tr></table>');
        }
        $db->query("TRUNCATE " . X_PREFIX . "templates");
        $filesize = filesize('./templates.ubb');
        $fp = fopen('./templates.ubb', 'r');
        $templatesfile = fread($fp, $filesize);
        fclose($fp);
        $templates = explode("|#*UBB TEMPLATE FILE*#|", $templatesfile);
        // while (list($key, $val) = each($templates)) {
        foreach ($templates as $key => $val) {
            $template = explode("|#*UBB TEMPLATE*#|", $val);
            if (isset($template[1])) {
                $template[1] = addslashes($template[1]);
                $db->query("INSERT INTO " . X_PREFIX . "templates (name, template) VALUES ('" . addslashes($template[0]) . "', '" . addslashes($template[1]) . "')");
            }
        }
        $db->query("DELETE FROM " . X_PREFIX . "templates WHERE name = ''");
        cp_message($lang['templatesrestoredone'], false, '', '</td></tr></table>', 'cp_templates.php?action=templates', true, false, true);
    }

    if (onSubmit('edit') && noSubmit('editsubmit')) {
        if ($tid == "default") {
            cp_error($lang['selecttemplate'], false, '', '</td></tr></table>');
        }
        ?>
        <form method="post" action="cp_templates.php?action=templates&amp;tid=<?php echo $tid ?>">
        <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
        <tr>
        <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
        <tr class="category">
        <td class="title"><?php echo $lang['templates'] ?></td>
        </tr>
        <?php
$query = $db->query("SELECT * FROM " . X_PREFIX . "templates WHERE id = '$tid' ORDER BY name");
        $template = $db->fetch_array($query);
        $template['template'] = stripslashes(htmlspecialchars($template['template']));
        ?>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $lang['templatename'] ?>&nbsp;<strong><?php echo $template['name'] ?></strong></td>
        </tr>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><textarea style="width: 100%" rows="20" name="templatenew"><?php echo $template['template'] ?></textarea></td>
        </tr>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="submit" name="editsubmit" class="submit" value="<?php echo $lang['textsubmitchanges'] ?>" /></strong></td>
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

    if (onSubmit('editsubmit')) {
        $templatenew = addslashes(trim($templatenew));
        if ($tid == 'new') {
            if (empty($namenew)) {
                cp_error($lang['templateempty'], false, '', '</td></tr></table>');
            } else {
                $check = $db->query("SELECT name FROM " . X_PREFIX . "templates WHERE name = '$namenew'");
                if ($check && $db->num_rows($check) != 0) {
                    cp_error($lang['templateexists'], false, '', '</td></tr></table>');
                } else {
                    $db->query("INSERT INTO " . X_PREFIX . "templates (name, template) VALUES ('$namenew', '$templatenew')");
                }
            }
        } else {
            $db->query("UPDATE " . X_PREFIX . "templates SET template = '$templatenew' WHERE id = '$tid'");
        }

        cp_message($lang['templatesupdate'], false, '', '</td></tr></table>', 'cp_templates.php?action=templates', true, false, true);
    }

    if (onSubmit('delete')) {
        if ($tid == 'default') {
            cp_error($lang['selecttemplate'], false, '', '</td></tr></table>');
        }
        ?>
        <form method="post" action="cp_templates.php?action=templates&amp;tid=<?php echo $tid ?>">
        <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
        <tr>
        <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
        <tr class="category">
        <td class="title"><?php echo $lang['templates'] ?></td>
        </tr>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['templatedelconfirm'] ?></td>
        </tr>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="submit" class="submit" name="deletesubmit" value="<?php echo $lang['textyes'] ?>" /></td>
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

    if (onSubmit('deletesubmit')) {
        $db->query("DELETE FROM " . X_PREFIX . "templates WHERE id = '$tid'");
        cp_message($lang['templatesdelete'], false, '', '</td></tr></table>', 'cp_templates.php?action=templates', true, false, true);
    }

    if (onSubmit('rename') && noSubmit('renamesubmit')) {
        if ($tid == 'default') {
            cp_error($lang['selecttemplate'], false, '', '</td></tr></table>');
        }
        ?>
        <form method="post" action="cp_templates.php?action=templates&amp;tid=<?php echo $tid ?>">
        <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
        <tr>
        <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
        <tr class="category">
        <td class="title" colspan="2"><?php echo $lang['templates'] ?></td>
        </tr>
        <?php
$query = $db->query("SELECT * FROM " . X_PREFIX . "templates WHERE id = '$tid' ORDER BY name");
        $template_info = $db->fetch_array($query);
        ?>
        <tr class="tablerow">
        <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textfrom'] ?></td>
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $template_info['name'] ?></td>
        </tr>
        <tr class="tablerow">
        <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textto'] ?></td>
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text" name="new_name" size="30" value="" /></td>
        </tr>
        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
        <td colspan="2"><input type="submit" name="renamesubmit" class="submit" value="<?php echo $lang['template_button'] ?>" /></td>
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

    if (onSubmit('renamesubmit') && noSubmit('rename')) {
        $check_newname = $db->query("SELECT name FROM " . X_PREFIX . "templates WHERE name = '$new_name'");
        if ($check_newname && $db->num_rows($check_newname) != 0) {
            cp_error($lang['templateexists'], false, '', '</td></tr></table>');
        } else {
            $db->query("UPDATE " . X_PREFIX . "templates SET name = '$new_name' WHERE id = '$tid'");
            cp_message($lang['template_renamed'], false, '', '</td></tr></table>', 'cp_templates.php?action=templates', true, false, true);
        }
    }

    if (onSubmit('backup_cur')) {
        ?>
        <form method="post" action="cp_templates.php?action=templates">
        <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
        <tr>
        <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
        <tr class="category">
        <td class="title"><?php echo $lang['templates'] ?></td>
        </tr>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['template_baccur_text'] ?></td>
        </tr>
        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
        <td><input type="submit" class="submit" name="backup_curyes" value="<?php echo $lang['textyes'] ?>" /></td>
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

    if (onSubmit('backup_curyes')) {
        if (!is_writable('./templates/')) {
            cp_error($lang['template_nowrite'], false, '', '</td></tr></table>');
        } else {
            $code = '';
            $templates = $db->query("SELECT * FROM " . X_PREFIX . "templates");
            while ($template = $db->fetch_array($templates)) {
                $template['name'] = trim($template['name']);
                $template['template'] = trim(stripslashes($template['template']));
                $code .= $template['name'] . "|#*UBB TEMPLATE*#|\r\n" . $template['template'] . "\r\n\r\n|#*UBB TEMPLATE FILE*#|";
                $stream = @fopen('./templates/templates-current.ubb', 'w+');
                fwrite($stream, $code, strlen($code));
                fclose($stream);
            }
        }
        cp_message($lang['template_current_bac'], false, '', '</td></tr></table>', 'cp_templates.php?action=templates', true, false, true);
    }

    if (onSubmit('restore_cur')) {
        ?>
        <form method="post" action="cp_templates.php?action=templates">
        <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
        <tr>
        <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
        <tr class="category">
        <td class="title"><?php echo $lang['templates'] ?></td>
        </tr>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['template_rescur_text'] ?></td>
        </tr>
        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
        <td><input type="submit" class="submit" name="restore_curyes" value="<?php echo $lang['textyes'] ?>" /></td>
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

    if (onSubmit('restore_curyes')) {
        if (!file_exists('./templates/templates-current.ubb')) {
            cp_error($lang['template_current_no'], false, '', '</td></tr></table>');
        } else {
            $db->query("TRUNCATE " . X_PREFIX . "templates");
            $filesize = filesize('./templates/templates-current.ubb');
            $fp = fopen('./templates/templates-current.ubb', 'r');
            $templatesfile = fread($fp, $filesize);
            fclose($fp);
            $templates = explode("|#*UBB TEMPLATE FILE*#|", $templatesfile);
            // while (list($key, $val) = each($templates)) {
            foreach ($templates as $key => $val) {
                $template = explode("|#*UBB TEMPLATE*#|", $val);
                if (isset($template[1])) {
                    $template[1] = addslashes($template[1]);
                    $db->query("INSERT INTO " . X_PREFIX . "templates (id, name, template) VALUES ('', '" . addslashes($template[0]) . "', '" . addslashes($template[1]) . "')");
                }
            }

            if (is_writable('./templates/')) {
                $code = '';
                $templates = $db->query("SELECT * FROM " . X_PREFIX . "templates");
                while ($template = $db->fetch_array($templates)) {
                    $template['name'] = trim($template['name']);
                    $template['template'] = trim(stripslashes($template['template']));
                    $code .= $template['name'] . "|#*UBB TEMPLATE*#|\r\n" . $template['template'] . "\r\n\r\n|#*UBB TEMPLATE FILE*#|";
                    $stream = @fopen('./templates/templates-current.ubb', 'w+');
                    fwrite($stream, $code, strlen($code));
                    fclose($stream);
                }
            }
            $db->query("DELETE FROM " . X_PREFIX . "templates WHERE name = ''");
            cp_message($lang['template_current_up'], false, '', '</td></tr></table>', 'cp_templates.php?action=templates', true, false, true);
        }
    }

    if (onSubmit('new')) {
        ?>
        <form method="post" action="cp_templates.php?action=templates&amp;tid=new">
        <input type="hidden" name="token" value="<?php echo $oToken->get_new_token() ?>" />
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
        <tr>
        <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
        <tr class="category">
        <td class="title"><?php echo $lang['templates'] ?></td>
        </tr>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $lang['templatename'] ?>&nbsp;<input type="text" name="namenew" size="30" value="<?php echo $newtemplatename ?>" /></td>
        </tr>
        <tr class="ctrtablerow">
        <td bgcolor="<?php echo $THEME['altbg2'] ?>"><textarea style="width: 100%" rows="20" name="templatenew"></textarea></td>
        </tr>
        <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
        <td><input type="submit" name="editsubmit" value="<?php echo $lang['textsubmitchanges'] ?>" class="submit" /></td>
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
}

loadtime();
eval('echo "' . template('cp_footer') . '";');
?>
