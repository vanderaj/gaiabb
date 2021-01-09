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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

/**
 * Convert URL to a safely displayable text element
 *
 * Control XSS and censored words when you need to display a URL safely
 *
 * @param string $url
 *            URL to convert to displayable text
 * @return string the sanitized url
 *
 */
function url_to_text($url)
{
    global $db, $lang, $self;
    static $restrict, $rset, $fname, $tsub, $member;

    if (!$rset) {
        switch ($self['status']) {
            case 'Member':
                $restrict .= " f.private != '3' AND";
                // no break
            case 'Moderator':
                // no break
            case 'Super Moderator':
                $restrict .= " f.private != '2' AND";
                // no break
            case 'Administrator':
                $restrict .= " f.userlist = '' AND";
                $restrict .= " f.password = '' AND";
                // no break
            case 'Super Administrator':
                break;
                
            default:
                $restrict .= " f.private != '5' AND";
                $restrict .= " f.private != '3' AND";
                $restrict .= " f.private != '2' AND";
                $restrict .= " f.userlist = '' AND";
                $restrict .= " f.password = '' AND";
                break;
        }
        $rset = true;
    }
    if (false !== strpos($url, 'tid') && false === strpos($url, '/post.php')) {
        $temp = explode('?', $url);
        $urls = explode('&', $temp[1]);
        foreach ($urls as $key => $val) {
            if (strpos($val, 'tid') !== false) {
                $tid = (int) substr($val, 4);
            }
        }
        if (isset($tsub[$tid])) {
            $location = $lang['onlineviewtopic'] . ' ' . censor($tsub[$tid]);
        } else {
            $query = $db->query("SELECT t.fid, t.subject FROM " . X_PREFIX . "forums f, " . X_PREFIX . "threads t WHERE $restrict f.fid = t.fid AND t.tid = '$tid'");
            while (($locate = $db->fetchArray($query)) != false) {
                $location = $lang['onlineviewtopic'] . ' ' . censor(stripslashes($locate['subject']));
                $tsub[$tid] = censor(stripslashes($locate['subject']));
            }
            $db->freeResult($query);
        }
        // address issue with seeing attachments directly in whos online
        if (false !== strpos($url, 'action=attachment')) {
            $url = substr($url, 0, strpos($url, '?'));
            $url .= '?tid=' . $tid;
        }
    } elseif (false !== strpos($url, 'fid') && false === strpos($url, "/post.php")) {
        $temp = explode('?', $url);
        $urls = explode('&', $temp[1]);
        foreach ($urls as $key => $val) {
            if (strpos($val, 'fid') !== false) {
                $fid = (int) substr($val, 4);
            }
        }
        if (isset($fname[$fid])) {
            $location = $lang['onlineviewforum'] . ' ' . $fname[$fid];
        } else {
            $query = $db->query("SELECT name FROM " . X_PREFIX . "forums f WHERE $restrict f.fid = '$fid'");
            while (($locate = $db->fetchArray($query)) != false) {
                $location = $lang['onlineviewforum'] . ' ' . $locate['name'];
                $fname[$fid] = $locate['name'];
            }
            $db->freeResult($query);
        }
    } elseif (false !== strpos($url, '/usercp.php')) {
        $location = $lang['onlineusercp'];
    } elseif (false !== strpos($url, '/admin/cp_analyzetables.php') || false !== strpos($url, '/admin/cp_attachments.php') || false !== strpos($url, '/admin/cp_avatars.php') || false !== strpos($url, '/admin/cp_board.php') || false !== strpos($url, '/admin/cp_captcha.php') || false !== strpos($url, '/admin/cp_censors.php') || false !== strpos($url, '/admin/cp_checktables.php') || false !== strpos($url, '/admin/cp_closethreads.php') || false !== strpos($url, '/admin/cp_dateformats.php') || false !== strpos($url, '/admin/cp_default.php') || false !== strpos($url, '/admin/cp_deleteoldpms.php') || false !== strpos($url, '/admin/cp_emptymodlogs.php') || false !== strpos($url, '/admin/cp_emptyadminlogs.php') || false !== strpos($url, '/admin/cp_fixattachments.php') || false !== strpos($url, '/admin/cp_fixftotals.php') || false !== strpos($url, '/admin/cp_fixlastposts.php') || false !== strpos($url, '/admin/cp_fixmposts.php') || false !== strpos($url, '/admin/cp_fixmthreads.php') || false !== strpos($url, '/admin/cp_fixsmilies.php') || false !== strpos($url, '/admin/cp_fixfavorites.php') || false !== strpos($url, '/admin/cp_fixorphanedposts.php') || false !== strpos($url, '/admin/cp_fixsubscriptions.php') || false !== strpos($url, '/admin/cp_fixttotals.php') || false !== strpos($url, '/admin/cp_forums.php') || false !== strpos($url, '/admin/cp_general.php') || false !== strpos($url, '/admin/cp_inactivemembers.php') || false !== strpos($url, '/admin/cp_ipban.php') || false !== strpos($url, '/admin/cp_logs.php') || false !== strpos($url, '/admin/cp_members.php') || false !== strpos($url, '/admin/cp_moderators.php') || false !== strpos($url, '/admin/cp_news.php') || false !== strpos($url, '/admin/cp_newsletter.php') || false !== strpos($url, '/admin/cp_notepad.php') || false !== strpos($url, '/admin/cp_onlinedump.php') || false !== strpos($url, '/admin/cp_optimizetables.php') || false !== strpos($url, '/admin/cp_photos.php') || false !== strpos($url, '/admin/cp_pluglinks.php') || false !== strpos($url, '/admin/cp_posticons.php') || false !== strpos($url, '/admin/cp_ranks.php') || false !== strpos($url, '/admin/cp_rawsql.php') || false !== strpos($url, '/admin/cp_reguser.php') || false !== strpos($url, '/admin/cp_rename.php') || false !== strpos($url, '/admin/cp_repairtables.php') || false !== strpos($url, '/admin/cp_restrictions.php') || false !== strpos($url, '/admin/cp_robots.php') || false !== strpos($url, '/admin/cp_search.php') || false !== strpos($url, '/admin/cp_smilies.php') || false !== strpos($url, '/admin/cp_smtp.php') || false !== strpos($url, '/admin/cp_templates.php') || false !== strpos($url, '/admin/cp_themes.php') || false !== strpos($url, '/admin/cp_pmdump.php') || false !== strpos($url, '/admin/cp_updatemoods.php') || false !== strpos($url, '/admin/index.php') || false !== strpos($url, '/editprofile.php')) {
        if (!X_ADMIN) {
            $location = $lang['onlineindex'];
            $url = 'index.php';
        } else {
            $location = $lang['onlinecp'];
        }
    } elseif (false !== strpos($url, '/index.php')) {
        $location = $lang['onlineindex'];
    } elseif (false !== strpos($url, '/register.php')) {
        if (false !== strpos($url, 'action=reg') || false !== strpos($url, 'action=captcha')) {
            $location = $lang['onlinereg'];
        } elseif (false !== strpos($url, 'action=coppa')) {
            $location = $lang['onlinecoppa'];
        }
    } elseif (false !== strpos($url, '/faq.php')) {
        if (false !== strpos($url, 'page=admin') || false !== strpos($url, 'page=rulesedit')) {
            if (!X_ADMIN) {
                $location = $lang['onlineindex'];
                $url = 'index.php';
            } else {
                $location = $lang['onlinecp'];
            }
        } elseif (false !== strpos($url, 'page=forumrules')) {
            $location = $lang['onlineviewrules'];
        } else {
            $location = $lang['onlinefaq'];
        }
    } elseif (false !== strpos($url, '/viewprofile.php')) {
        $temp = explode('?', $url);
        $urls = explode('&', $temp[1]);
        foreach ($urls as $argument) {
            if (strpos($argument, 'memberid') !== false) {
                $member_id = str_replace('memberid=', '', $argument);
                $member_id = (int) $db->escape($member_id);
                $member = $db->result($db->query("SELECT DISTINCT username FROM " . X_PREFIX . "members WHERE uid = '$member_id'"), 0);
            }
        }
        eval("\$location = \"$lang[onlineviewpro]\";");
    } elseif (false !== strpos($url, '/email.php')) {
        $temp = explode('?', $url);
        $urls = explode('&', $temp[1]);
        foreach ($urls as $argument) {
            if (strpos($argument, 'member') !== false) {
                $member = str_replace('member=', '', $argument);
            }
        }
        eval("\$location = \"$lang[emailmemonline]\";");
    } elseif (false !== strpos($url, '/memberlist.php')) {
        if (false !== strpos($url, 'action=list')) {
            $location = $lang['onlinememlist'];
        }
    } elseif (false !== strpos($url, '/post.php')) {
        if (false !== strpos($url, 'action=edit')) {
            $location = $lang['onlinepostedit'];
        } elseif (false !== strpos($url, 'action=newthread')) {
            $location = $lang['onlinepostnewthread'];
        } elseif (false !== strpos($url, 'action=reply')) {
            $location = $lang['onlinepostreply'];
        }
    } elseif (false !== strpos($url, '/search.php')) {
        $location = $lang['onlinesearch'];
    } elseif (false !== strpos($url, '/smilies.php')) {
        $location = $lang['onlinesmilies'];
    } elseif (false !== strpos($url, '/logout.php')) {
        $location = $lang['onlinelogout'];
    } elseif (false !== strpos($url, '/viewonline.php')) {
        $location = $lang['onlinewhosonline'];
    } elseif (false !== strpos($url, '/lostpw.php')) {
        $location = $lang['onlinelostpw'];
    } elseif (false !== strpos($url, '/login.php')) {
        $location = $lang['onlinelogin'];
    } elseif (false !== strpos($url, '/stats.php')) {
        $location = $lang['onlinestats'];
    } elseif (false !== strpos($url, '/activity.php')) {
        $location = $lang['topicactivityonline'];
    } elseif (false !== strpos($url, '/topicadmin.php') || false !== strpos($url, '/mod/index.php') || false !== strpos($url, '/mod/mod_censors.php') || false !== strpos($url, '/mod/mod_checktables.php') || false !== strpos($url, '/mod/mod_fixftotals.php') || false !== strpos($url, '/mod/mod_fixlastposts.php') || false !== strpos($url, '/mod/mod_fixmtotals.php') || false !== strpos($url, '/mod/mod_fixorphanedattachments.php') || false !== strpos($url, '/mod/mod_fixorphanedfavorites.php') || false !== strpos($url, '/mod/mod_foxorphanedposts.php') || false !== strpos($url, '/mod/mod_fixorphanedthreads.php') || false !== strpos($url, '/mod/mod_fixttotals.php') || false !== strpos($url, '/mod/mod_members.php') || false !== strpos($url, '/mod/mod_newsletter.php') || false !== strpos($url, '/mod/mod_search.php') || false !== strpos($url, '/mod/mod_updatemoods.php') || false !== strpos($url, '/mod/mod_whosonlinedump.php')) {
        if (!X_STAFF) {
            $location = $lang['onlineindex'];
            $url = 'index.php';
        } else {
            $location = $lang['onlinetopicadmin'];
        }
    } elseif (false !== strpos($url, '/contact.php')) {
        $location = $lang['contactonline'];
    } elseif (false !== strpos($url, '/pm.php')) {
        if (false !== strpos($url, 'action=send')) {
            $location = $lang['onlinepmsend'];
        } elseif (false !== strpos($url, 'action=delete')) {
            $location = $lang['onlinepmdelete'];
        } elseif (false !== strpos($url, 'action=ignore') || false !== strpos($url, 'action=ignoresubmit')) {
            $location = $lang['onlinepmignore'];
        } elseif (false !== strpos($url, 'action=view')) {
            $location = $lang['onlinepmview'];
        } elseif (false !== strpos($url, 'action=folders')) {
            $location = $lang['onlinemanagefolders'];
        }
        if (!X_SADMIN) {
            $url = 'pm.php';
        }
    } else {
        $location = $lang['onlineindex'];
    }
    if (empty($location)) {
        $url = 'index.php';
        $location = $lang['onlineindex'];
    } else {
        $location = str_replace('%20', '&nbsp;', $location);
    }
    $url = addslashes(trim($url));
    $return = array();
    $return['url'] = checkInput($url);
    $return['text'] = $location;
    return $return;
}
