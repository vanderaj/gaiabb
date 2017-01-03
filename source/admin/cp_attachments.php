<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2017 The GaiaBB Group
 * http://www.GaiaBB.com
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

require_once('../header.php');
require_once('../class/admincp.inc.php');

loadtpl('cp_header', 'cp_footer', 'cp_message', 'cp_error');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();
nav('<a href="index.php">' . $lang['textcp'] . '</a>');
nav($lang['textattachman']);
btitle($lang['textcp']);
btitle($lang['textattachman']);

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
    global $THEME, $lang, $shadow2;
    global $oToken;
    $forumselect = forumList('forumprune', false, true);
    ?>
    <form method="post" action="cp_attachments.php?action=attachments">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td class="title" colspan="2"><?php echo $lang['textsearch'] ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="35%"
                                nowrap="nowrap"><?php echo $lang['attachmanwherename'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="filename" size="30"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="35%"
                                nowrap="nowrap"><?php echo $lang['attachmanwhereauthor'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="author" size="40"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="35%"
                                nowrap="nowrap"><?php echo $lang['attachmanwhereforum'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><?php echo $forumselect ?></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="35%"
                                nowrap="nowrap"><?php echo $lang['attachmanwheresizesmaller'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="sizeless" size="20"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="35%"
                                nowrap="nowrap"><?php echo $lang['attachmanwheresizegreater'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="sizemore" size="20"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="35%"
                                nowrap="nowrap"><?php echo $lang['attachmanwheredlcountsmaller'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="dlcountless" size="20"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="35%"
                                nowrap="nowrap"><?php echo $lang['attachmanwheredlcountgreater'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="dlcountmore" size="20"/></td>
                        </tr>
                        <tr class="tablerow">
                            <td bgcolor="<?php echo $THEME['altbg1'] ?>" width="35%"
                                nowrap="nowrap"><?php echo $lang['attachmanwheredaysold'] ?></td>
                            <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="text"
                                                                                name="daysold" size="20"/></td>
                        </tr>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="2"><input type="submit" name="searchsubmit"
                                                   class="submit" value="<?php echo $lang['textsubmitchanges'] ?>"/>
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
function doDelete()
{
    global $db, $onlinetime, $oToken, $THEME, $lang, $shadow2;

    $queryforum = $querydate = $queryauthor = $queryname = $querysizeless = $querysizemore = '';
    $forumprune = formVar('forumprune');
    if (!empty($forumprune) && $forumprune != $lang['textall']) {
        $queryforum = "AND p.fid=" . intval($forumprune) . " ";
    }

    $daysold = formInt('daysold');
    if ($daysold > 0) {
        $datethen = $onlinetime - (86400 * $daysold);
        $querydate = "AND p.dateline <= '$datethen' ";
    }

    $author = formVar('author');
    if (!empty($author)) {
        $author = $db->escape($author);
        $queryauthor = "AND p.author = '$author' ";
    }

    $filename = formVar('filename'); // TODO make path safe
    if (!empty($filename)) {
        $filename = $db->escape($filename);
        $queryname = "AND a.filename LIKE '%$filename%' ";
    }

    $sizeless = formInt('sizeless');
    if ($sizeless > 0) {
        $querysizeless = "AND a.filesize < $sizeless ";
    }

    $sizemore = formInt('sizemore');
    if ($sizemore > 0) {
        $querysizemore = "AND a.filesize > $sizemore ";
    }

    $dlcountless = formInt('dlcountless');
    if ($dlcountless > 0) {
        $querydlcountless = "AND a.downloads < $dlcountless ";
    }

    $dlcountmore = formInt('dlcountmore');
    if ($dlcountmore > 0) {
        $querydlcountmore = "AND a.downloads > $dlcountmore ";
    }

    $query = $db->query("SELECT a.*, p.*, t.tid, t.subject AS tsubject, f.name AS fname FROM " . X_PREFIX . "attachments a, " . X_PREFIX . "posts p, " . X_PREFIX . "threads t, " . X_PREFIX . "forums f WHERE a.pid = p.pid AND t.tid = a.tid AND f.fid = p.fid $queryforum $querydate $queryauthor $queryname $querysizeless $querysizemore");
    while (($attachment = $db->fetch_array($query)) != false) {
        $afilename = "filename$attachment[aid]";
        $afilename = formVar($afilename);
        if ($attachment['filename'] != $afilename) {
            $db->query("UPDATE " . X_PREFIX . "attachments SET filename = '$afilename' WHERE aid = '$attachment[aid]'");
        }
    }
    $db->free_result($query);
    cp_message($lang['textattachmentsupdate'], false, '', '</td></tr></table>', 'cp_attachments.php?action=attachments', true, false, true);
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
function doSearch()
{
    global $db, $onlinetime, $oToken, $THEME, $lang, $shadow2;
    ?>
    <form method="post" action="cp_attachments.php?action=attachments">
        <input type="hidden" name="token"
               value="<?php echo $oToken->get_new_token() ?>"/>
        <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
               align="center">
            <tr>
                <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                    <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                           cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                        <tr class="category">
                            <td colspan="6" class="title"><strong><?php echo $lang['textattachsearchresults'] ?></td>
                        </tr>
                        <tr class="header">
                            <td width="4%" align="center"><?php echo $lang['textdeleteques'] ?></td>
                            <td width="25%"><?php echo $lang['textfilename'] ?></td>
                            <td width="29%"><?php echo $lang['textauthor'] ?></td>
                            <td width="27%"><?php echo $lang['textinthread'] ?></td>
                            <td width="10%"><?php echo $lang['textfilesize'] ?></td>
                            <td width="5%"><?php echo $lang['textdownloads'] ?></td>
                        </tr>
                        <?php
                        $restriction = $orderby = '';
                        $forumprune = formInt('forumprune');
                        if ($forumprune != 0) {
                            $restriction .= "AND p.fid=$forumprune ";
                        }

                        $daysold = formInt('daysold');
                        if ($daysold != 0) {
                            $datethen = $onlinetime - (86400 * $daysold);
                            $restriction .= "AND p.dateline <= $datethen ";
                            $orderby = ' ORDER BY p.dateline ASC';
                        }

                        $author = formVar('author');
                        if (!empty($author)) {
                            $author = $db->escape($author);
                            $restriction .= "AND p.author = '$author' ";
                            $orderby = ' ORDER BY p.author ASC';
                        }

                        $filename = formVar('filename'); // TODO make filename safe
                        if (!empty($filename)) {
                            $filename = $db->escape($filename);
                            $restriction .= "AND a.filename LIKE '%$filename%' ";
                        }

                        $sizeless = formInt('sizeless');
                        if ($sizeless > 0) {
                            $restriction .= "AND a.filesize < $sizeless ";
                            $orderby = ' ORDER BY a.filesize DESC';
                        }

                        $sizemore = formInt('sizemore');
                        if ($sizemore > 0) {
                            $restriction .= "AND a.filesize > $sizemore ";
                            $orderby = ' ORDER BY a.filesize DESC';
                        }

                        $dlcountless = formInt('dlcountless');
                        if ($dlcountless > 0) {
                            $restriction .= "AND a.downloads < $dlcountless ";
                            $orderby = ' ORDER BY a.downloads DESC';
                        }

                        $dlcountmore = formInt('dlcountmore');
                        if ($dlcountmore > 0) {
                            $restriction .= "AND a.downloads > $dlcountmore ";
                            $orderby = ' ORDER BY a.downloads DESC ';
                        }

                        $query = $db->query("SELECT a.*, m.uid as author_uid, p.*, t.tid, t.subject AS tsubject, f.name AS fname FROM " . X_PREFIX . "attachments a, " . X_PREFIX . "members m, " . X_PREFIX . "posts p, " . X_PREFIX . "threads t, " . X_PREFIX . "forums f WHERE a.pid = p.pid AND p.author = m.username AND t.tid = a.tid AND f.fid = p.fid $restriction $orderby");
                        while (($attachment = $db->fetch_array($query)) != false) {
                            $attachsize = strlen($attachment['attachment']);
                            if ($attachsize >= 1073741824) {
                                $attachsize = round($attachsize / 1073741824 * 100) / 100 . "gb";
                            } else
                                if ($attachsize >= 1048576) {
                                    $attachsize = round($attachsize / 1048576 * 100) / 100 . "mb";
                                } else
                                    if ($attachsize >= 1024) {
                                        $attachsize = round($attachsize / 1024 * 100) / 100 . "kb";
                                    } else {
                                        $attachsize = $attachsize . "b";
                                    }
                            $attachment['tsubject'] = stripslashes($attachment['tsubject']);
                            $attachment['fname'] = stripslashes($attachment['fname']);
                            $attachment['filename'] = stripslashes($attachment['filename']);
                            ?>
                            <tr>
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>" class="ctrtablerow"
                                    valign="middle"><a
                                            href="cp_attachments.php?action=delete_attachment&amp;aid=<?php echo $attachment['aid'] ?>&amp;token=<?php echo $oToken->get_new_token() ?>"><?php echo $lang['textdeleteques'] ?></a>

                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"
                                    valign="top"><input type="text"
                                                        name="filename<?php echo $attachment['aid'] ?>"
                                                        value="<?php echo $attachment['filename'] ?>"><br/>
                                    <span class="smalltxt"><a
                                                href="../viewtopic.php?action=attachment&amp;tid=<?php echo $attachment['tid'] ?>&amp;pid=<?php echo $attachment['pid'] ?>&amp;aid=<?php echo $attachment['aid'] ?>"
                                                target="_blank"><?php echo $lang['textdownload'] ?></a></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"
                                    valign="top"><a
                                            href="../viewprofile.php?memberid=<?php echo urlencode($attachment['author_uid']) ?>"
                                            target="_blank"><?php echo $attachment['author'] ?></a></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"
                                    valign="top"><a
                                            href="../viewtopic.php?tid=<?php echo $attachment['tid'] ?>"
                                            target="_blank"><?php echo $attachment['tsubject'] ?></a><br/>
                                    <span class="smalltxt"><?php echo $lang['textinforum'] ?> <a
                                                href="../viewforum.php?fid=<?php echo $attachment['fid'] ?>"
                                                target="_blank"><?php echo $attachment['fname'] ?></a></span></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"
                                    valign="top" align="center"><?php echo $attachsize ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow"
                                    valign="top" align="center"><?php echo $attachment['downloads'] ?></td>
                            </tr>
                            <?php
                        }
                        $db->free_result($query);
                        ?>
                        <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                            <td colspan="6"><input class="submit" type="submit"
                                                   name="deletesubmit"
                                                   value="<?php echo $lang['textsubmitchanges'] ?>"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php echo $shadow2 ?>
        <input type="hidden" name="filename"
               value="<?php echo htmlentities($filename) ?>"/> <input type="hidden"
                                                                      name="author"
                                                                      value="<?php echo htmlentities($author) ?>"/>
        <input
                type="hidden" name="forumprune"
                value="<?php echo htmlentities($forumprune) ?>"/> <input type="hidden"
                                                                         name="sizeless"
                                                                         value="<?php echo $sizeless ?>"/> <input
                type="hidden"
                name="sizemore" value="<?php echo $sizemore ?>"/> <input type="hidden"
                                                                         name="dlcountless"
                                                                         value="<?php echo $dlcountless ?>"/> <input
                type="hidden" name="dlcountmore" value="<?php echo $dlcountmore ?>"/>
        <input type="hidden" name="daysold" value="<?php echo $daysold ?>"/>
    </form>
    </td>
    </tr>
    </table>
    <?php
}

displayAdminPanel();

switch ($action) {
    case 'attachments':
        if (noSubmit('attachsubmit') && noSubmit('searchsubmit')) {
            viewPanel();
        }

        if (onSubmit('searchsubmit')) {
            $oToken->assert_token();
            doSearch();
        }

        if (onSubmit('deletesubmit')) {
            $oToken->assert_token();
            doDelete();
        }
        break;
    case 'delete_attachment':
        $oToken->assert_token();

        $aid = getRequestInt('aid');
        $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE aid = '$aid'");
        cp_message($lang['textattachmentsupdate'], false, '', '</td></tr></table>', 'cp_attachments.php?action=attachments', true, false, true);
        break;
    default:
        viewPanel();
        break;
}

loadtime();
eval ('echo "' . template('cp_footer') . '";');
?>
