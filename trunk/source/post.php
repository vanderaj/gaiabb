<?php
/**
 * GaiaBB
 * Copyright (c) 2011 The GaiaBB Group
 * http://www.GaiaBB.com
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

define('CACHECONTROL', 'private');
define('ROOT', './');

require_once (ROOT . 'header.php');
require_once (ROOTCLASS . 'forum.class.php');

loadtpl('functions_smilieinsert', 'functions_smilieinsert_smilie', 'functions_bbcodeinsert', 'functions_bbcode', 'post_attachmentbox', 'post_edit', 'post_captcha', 'register_captchajs', 'post_edit_attachment', 'post_loggedin', 'post_newpoll', 'post_newthread', 'post_notloggedin', 'post_preview', 'post_reply', 'post_reply_review_post', 'post_reply_review_toolong', 'post_attach_edit_JS', 'post_edit_attach_JS', 'post_edit_delete', 'post_delete_confirm', 'viewforum_password');

$shadow = shadowfx();
$meta = metaTags();

eval ('$css = "' . template('css') . '";');

smcwcache();

$forumController = new Forum();

$captcha = '';
$pid = getRequestInt('pid');
$tid = getRequestInt('tid');
$fid = getRequestInt('fid');
$aid = getRequestInt('aid');
$posterror = false;

$subject = formVar('subject');
$posticon = formVar('posticon');
$message = formVar('message');
$smileyoff = formYesNo('smileyoff');
$usesig = formYesNo('usesig');
$bbcodeoff = formYesNo('bbcodeoff');
$emailnotify = formYesNo('emailnotify');
$toptopic = formYesNo('toptopic');
$closetopic = formYesNo('closetopic');
$poll = (getRequestVar('poll') == 'yes') ? true : false;
$pollanswers = formVar('pollanswers');
$repquote = getRequestInt('repquote');
$captchaword = formVar('pollanswers');
$subjectprefix = formVar('subjectprefix');

validatePpp();

function isAuthorized()
{
    global $self, $username, $password, $lang;

    if (isset ($self['status']) && $self['status'] == 'Banned')
    {
        error($lang['bannedmessage'], true, '', '', false, true, false, true);
        exit;
    }

    if (X_GUEST)
    {
        error($lang['textnoguestposting'], true, '', '', false, true, false, true);
        exit;
    }

    if (X_MEMBER && (isset ($self['ban']) && $self['ban'] == 'posts' || $self['ban'] == 'both'))
    {
        error($lang['textbanfrompost'], true, '', '', false, true, false, true);
    }

    if (X_MEMBER)
    {
        $username = $self['username'];
        $password = '';
    }

    return true;
}

$thread = array ();
$thread['subject'] = $attachfile = '';

isAuthorized();

if ($tid !== 0)
{
    $query = $db->query("SELECT fid, subject FROM " . X_PREFIX . "threads WHERE tid = '$tid' LIMIT 1");
    if ($db->num_rows($query) == 1)
    {
        $thread = $db->fetch_array($query);
        $thread['subject'] = censor(stripslashes($thread['subject']));
        $fid = (int) $thread['fid'];
        $db->free_result($query);
    }
    else
    {
        error($lang['textnothread'], true, '', '', false, true, false, true);
    }
}

$query = $db->query("SELECT * FROM " . X_PREFIX . "forums WHERE fid = '$fid' AND status = 'on'");
$forums = $db->fetch_array($query);
$db->free_result($query);
$forums['name'] = stripslashes($forums['name']);

if (($fid == 0 && $tid == 0) || isset ($forums['type']) && $forums['type'] != 'forum' && $forums['type'] != 'sub' && $forums['fid'] != $fid)
{
    $posterror = $lang['textnoforum'];
}

if (isset ($forums['mpfa']) && $forums['mpfa'] != 0 && isset ($self['postnum']) && $self['postnum'] < $forums['mpfa'] && !X_SADMIN)
{
    $message = str_replace("*posts*", $forums['mpfa'], $lang['mpfae']);
    error($message, true, '', '', false, true, false, true);
}

if (isset ($forums['type']) && $forums['type'] == 'forum')
{
    nav('<a href="' . ROOT . 'viewforum.php?fid=' . $fid . '">' . stripslashes($forums['name']) . '</a>');
    btitle(stripslashes($forums['name']));
}
else
{
    if (isset ($forums['fup']))
    {
        $query = $db->query("SELECT name, fid FROM " . X_PREFIX . "forums WHERE fid = '$forums[fup]'");
        $fup = $db->fetch_array($query);
        nav('<a href="' . ROOT . 'viewforum.php?fid=' . $fup['fid'] . '">' . stripslashes($fup['name']) . '</a>');
        nav('<a href="' . ROOT . 'viewforum.php?fid=' . $fid . '">' . stripslashes($forums['name']) . '</a>');
        btitle(stripslashes($fup['name']));
        btitle(stripslashes($forums['name']));
    }
}

if (isset ($forums['attachstatus']) && $forums['attachstatus'] == 'on')
{
    eval ('$attachfile = "' . template('post_attachmentbox') . '";');
}

eval ('$loggedin = "' . template('post_loggedin') . '";');

if (!empty ($self['ban']))
{
    if ($self['ban'] == 'posts' || $self['ban'] == 'both')
    {
        error($lang['textbanfrompost'], true, '', '', false, true, false, true);
    }
}

if (isset ($self['status']) && $self['status'] == 'Banned')
{
    error($lang['bannedmessage'], true, '', '', false, true, false, true);
}

$listed_icons = 0;
$icons = '<input type="radio" name="posticon" value="" />&nbsp;' . $lang['textnone'];
if ($action != 'edit')
{
    $qsmilie = $db->query("SELECT url, code FROM " . X_PREFIX . "smilies WHERE type = 'picon'");
    while ($smilie = $db->fetch_array($qsmilie))
    {
        $icons .= '&nbsp;<input type="radio" name="posticon" value="' . $smilie['url'] . '" />&nbsp;<img src="' . $THEME['smdir'] . '/' . $smilie['url'] . '" alt="' . $smilie['url'] . '" title="' . $smilie['url'] . '" border="0px" />';
        $listed_icons += 1;
        if ($listed_icons == 10)
        {
            $icons .= '<br />';
            $listed_icons = 0;
        }
    }
    $db->free_result($qsmilie);
}

// evaluate which bbcode javascript file to use
if ($bbcode_js != '')
{
    $bbcode_js_sc = 'bbcodefns-' . $bbcode_js . '.js';
}
else
{
    $bbcode_js_sc = 'bbcodefns.js';
}

eval ('$bbcodescript = "' . template('functions_bbcode') . '";');

if (isset ($forums['subjectprefixes']) && !empty ($forums['subjectprefixes']))
{
    $prefix = array ();
    $prefix[] = '<select name="subjectprefix">';
    $prefix[] = '<option value="">' . $lang['topicselectprefix'] . '</option>';
    $prefixes = explode(',', $forums['subjectprefixes']);
    for ($i = 0; $i < count($prefixes); $i++)
    {
        $prefixes[$i] = trim($prefixes[$i]);
        if ($subjectprefix == $prefixes[$i])
        {
            $prefix[] = '<option value="' . stripslashes($prefixes[$i]) . '" ' . $selHTML . '>' . stripslashes($prefixes[$i]) . '</option>';
        }
        else
        {
            $prefix[] = '<option value="' . stripslashes($prefixes[$i]) . '">' . stripslashes($prefixes[$i]) . '</option>';
        }
    }
    $prefix[] = '</select>';
    $prefix = implode("\n", $prefix);
}
else
{
    $subjectprefix = $prefix = '';
}

$allowimgcode = (isset ($forums['allowimgcode']) && $forums['allowimgcode'] == 'yes') ? $lang['texton'] : $lang['textoff'];
$allowsmilies = (isset ($forums['allowsmilies']) && $forums['allowsmilies'] == 'yes') ? $lang['texton'] : $lang['textoff'];
$allowbbcode = (isset ($forums['allowbbcode']) && $forums['allowbbcode'] == 'yes') ? $lang['texton'] : $lang['textoff'];
$pperm['type'] = (isset ($action) && $action == 'newthread') ? 'thread' : (isset ($action) && $action == 'reply') ? 'reply' : ((isset ($action) && $action == 'delete') ? 'delete' : 'edit');

if (!postperm($forums, $pperm['type']))
{
    error($lang['privforummsg'], true, '', '', false, true, false, true);
}

$guestpostingmsg = '';

if ($posterror)
{
    error($posterror, true, '', '', false, true, false, true);
}

$smileyoff = formYesNo('smileyoff');
if ($smileyoff == 'yes')
{
    $smileoffcheck = $cheHTML;
}
else
{
    $smileoffcheck = '';
    $smileyoff = 'no';
}

$bbcodeoff = formYesNo('bbcodeoff');
if ($bbcodeoff == 'yes')
{
    $codeoffcheck = $cheHTML;
}
else
{
    $codeoffcheck = '';
    $bbcodeoff = 'no';
}

$emailnotify = formYesNo('emailnotify');
if ($emailnotify == 'yes')
{
    $emailnotifycheck = $cheHTML;
}
else
{
    $emailnotifycheck = '';
    $emailnotify = 'no';
}

if (onSubmit('previewpost') && $usesig == 'yes')
{
    $usesigcheck = $cheHTML;
}
elseif (onSubmit('previewpost'))
{
    $usesigcheck = '';
}
elseif (isset ($self['sig']) && !empty ($self['sig']))
{
    $usesigcheck = $cheHTML;
}
else
{
    $usesigcheck = '';
}

$status1 = modcheck($forums['moderator']);
if (X_STAFF && $status1 == 'Moderator')
{
    $toptopic = formYesNo('toptopic');
    $topcheck = (isset ($toptopic) && $toptopic == 'yes') ? $cheHTML : '';
    $closetopic = formYesNo('closetopic');
    $closecheck = (isset ($toptopic) && $closetopic == 'yes' || isset ($forums['closethreads']) && $forums['closethreads'] == 'on') ? $cheHTML : '';
}
else
{
    $topcheck = $closecheck = $toptopic = $closetopic = '';
}

pwverify($forums['password'], 'post.php?action=' . $action . '&fid=' . $fid . '&tid=' . $tid . '&repquote=' . $repquote . '&poll=' . $poll, $fid);

$config_cache->expire('settings');
$config_cache->expire('theme');
$config_cache->expire('pluglinks');
$config_cache->expire('whosonline');
$config_cache->expire('forumjump');

$query = $db->query("SELECT * FROM " . X_PREFIX . "forums WHERE fid = '$fid' AND status = 'on'");
$forum = $db->fetch_array($query);
$db->free_result($query);
$authorization = privfcheck($forum['private'], $forum['userlist']);
if (!$authorization)
{
    error($lang['privforummsg'], true, '', '', false, true, false, true);
}

if (!empty ($posticon))
{
    $thread['icon'] = (file_exists($THEME['smdir'] . '/' . $posticon)) ? '<img src="' . $THEME['smdir'] . '/' . $posticon . '" alt="' . $THEME['smdir'] . '/' . $posticon . '" title="' . $THEME['smdir'] . '/' . $posticon . '" border="0px" />' : '';
    $icons = str_replace('<input type="radio" name="posticon" value="' . $posticon . '" />', '<input type="radio" name="posticon" value="' . $posticon . '" ' . $cheHTML . ' />', $icons);
}
else
{
    $thread['icon'] = '';
    $icons = str_replace('<input type="radio" name="posticon" value="" />', '<input type="radio" name="posticon" value="" checked="checked" />', $icons);
}

if (onSubmit('topicsubmit'))
{
    if (empty ($subject))
    {
        $preview = error($lang['textnosubject'], false, '', '<br /><br />', false, false, true, false);
        $error = true;
        unset ($topicsubmit);
        if (isset ($previewpost))
        {
            unset ($previewpost);
        }
    }
    else
    {
        $preview = '';
        $error = false;
    }
}
else
{
    $error = false;
}

$bbcodeinsert = bbcodeinsert();
$smilieinsert = smilieinsert();

if (onSubmit('previewpost'))
{
    $currtime = $onlinetime;
    $date = gmdate($self['dateformat'], $currtime + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
    $time = gmdate($self['timecode'], $currtime + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
    $poston = $lang['textposton'] . ' ' . $date . ' ' . $lang['textat'] . ' ' . $time;
    $subject = checkInput($subject);
    $message = checkInput($message);
    $message1 = postify($message, $smileyoff, $bbcodeoff, $forums['allowsmilies'], $forums['allowbbcode'], $forums['allowimgcode']);
    $dissubject = censor($subject);
    // if inital preview and post/thread has not been created it will not have a tid of zero.
    // if it was more than zero (editing post which exists) then we use stripslashes() otherwise we don't.
    $previewcode = ($pid > 0) ? '$preview = stripslashes("' . template('post_preview') . '");' : '$preview = "' . template('post_preview') . '";';
    eval ($previewcode);
}
else
{
    $preview = '';
}

switch ($action)
{
    case 'newthread' :
        $fattachnum = (int) $forum['attachnum'];
        eval ('$attachscript = "' . template('post_attach_edit_JS') . '";');
        $priv = privfcheck($forums['private'], $forums['userlist']);
        if ($poll)
        {
            nav($lang['textnewpoll']);
            btitle($lang['textnewpoll']);
        }
        else
        {
            nav($lang['textpostnew']);
            btitle($lang['textpostnew']);
        }

        if (isset ($forums['mpnt']) && $forums['mpnt'] != 0 && isset ($self['postnum']) && $self['postnum'] < $forums['mpnt'] && !X_SADMIN)
        {
            $message = str_replace("*posts*", $forums['mpnt'], $lang['mpnte']);
            error($message, true, '', '', false, true, false, true);
        }

        if (noSubmit('topicsubmit'))
        {
            eval ('echo "' . template('header') . '";');
            $status1 = modcheck($forums['moderator']);

            $topoption = $closeoption = '';
            if (X_STAFF && $status1 == 'Moderator')
            {
                $topoption = '<br /><input type="checkbox" name="toptopic" value="yes" ' . $topcheck . ' /> ' . $lang['topmsgques'];
                $closeoption = '<br /><input type="checkbox" name="closetopic" value="yes" ' . $closecheck . ' /> ' . $lang['closeonpost'] . '<br />';
            }

            if ($poll && isset ($forums['pollstatus']) && $forums['pollstatus'] != 'off')
            {
                if (!isset ($pollanswers))
                {
                    $pollanswers = '';
                }
                eval ('echo stripslashes("' . template('post_newpoll') . '");');
            }
            else
            {
                eval ('echo stripslashes("' . template('post_newthread') . '");');
            }
        }

        if (onSubmit('topicsubmit'))
        {
            if (empty ($subject) || empty ($message))
            {
                error($lang['postnothing'], true, '', '', false, true, false, true);
            }

            if (!X_ADMIN)
            {
                if (strlen($message) < $forums['minchars'])
                {
                    $message = str_replace("*chars*", $forums['minchars'], $lang['mincharsmsg']);
                    error($message, true, '', '', false, true, false, true);
                }
            }

            if (!postperm($forums, 'thread'))
            {
                error($lang['postpermerr'], true, '', '', false, true, false, true);
            }


            if (!X_ADMIN)
            {
                $query = $db->query("SELECT l.username, l.dateline, f.type, f.fup FROM " . X_PREFIX . "forums f LEFT JOIN " . X_PREFIX . "lastposts l ON f.lastpost = l.tid WHERE fid = '$fid' LIMIT 1");
                $for = $db->fetch_array($query);
                $db->free_result($query);

                $rightnow = $onlinetime - $CONFIG['floodctrl'];
                if ($rightnow <= $for['dateline'] && $username == $for['username'])
                {
                    error($lang['floodprotect'], true, '', '', false, true, false, true);
                }
            }

            if (!empty ($posticon))
            {
                $posticon = $db->escape($posticon);
                $query = $db->query("SELECT id FROM " . X_PREFIX . "smilies WHERE type = 'picon' AND url = '$posticon' ORDER BY id ASC");
                if (!$db->result($query, 0))
                {
                    exit;
                }
                $db->free_result($query);
            }
            else
            {
                $posticon = '';
            }

            for ($i = 0; $i < $forum['attachnum']; $i++)
            {
                if (isset ($_FILES['attach']['name'][$i]))
                {
                    // The external check alone will fail if exceeds the size. ~martijn
                    if ($_FILES['attach']['error'][$i] === 1 || $_FILES['attach']['error'][$i] === 2)
                    {
                        error($lang['attachtoobig']);
                    }

                    if (isset ($_FILES['attach']) && ($attachment = get_attached_file_multi($_FILES['attach'], $i, $forums['attachstatus'])) === false)
                    {
                        continue;
                    }
                    else
                    {
                        if (isset ($attachment))
                        {
                            $next_tid = $db->getNextId('threads');
                            $next_pid = $db->getNextId('posts');

                            $filename = $db->escape($filename);
                            $filetype = $db->escape($filetype);
                            $filesize = intval($filesize);
                            $fileheight = intval($fileheight);
                            $filewidth = intval($filewidth);

                            $db->query("INSERT INTO " . X_PREFIX . "attachments (tid, pid, filename, filetype, filesize, fileheight, filewidth, attachment, downloads) VALUES ($next_tid, $next_pid, '$filename', '$filetype', '$filesize', '$fileheight', '$filewidth', '$attachment', 0)");
                        }
                    }
                }
            }

            $subject = $db->escape(checkInput($subject));
            $message = $db->escape(checkInput($message));
            $forums['subjectprefixes'] = $db->escape(checkInput($forums['subjectprefixes']));

            $db->query("INSERT INTO " . X_PREFIX . "threads (fid, subject, icon, views, replies, author, closed, topped) VALUES ($fid, '" . $db->escape($subjectprefix) . " $subject', '$posticon', 0, 0, '$username', '', 0)");
            $tid = $db->insert_id();

            $db->query("INSERT INTO " . X_PREFIX . "posts (fid, tid, author, message, subject, dateline, icon, usesig, useip, bbcodeoff, smileyoff) VALUES ($fid, $tid, '$username', '$message', '" . $db->escape($subjectprefix) . " $subject', " . $db->time($onlinetime) . ", '$posticon', '$usesig', '$onlineip', '$bbcodeoff', '$smileyoff')");
            $pid = $db->insert_id();

            $db->query("INSERT INTO " . X_PREFIX . "lastposts (tid, uid, username, dateline, pid) VALUES ($tid, " . $self['uid'] . ", '$username', '$onlinetime', $pid)");

            if (isset ($forum['type']) && $forum['type'] == 'sub')
            {
                $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$tid', threads = threads+1, posts = posts+1 WHERE fid = '$forum[fup]'");
            }

            $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$tid', threads = threads+1, posts = posts+1 WHERE fid = '$fid'");

            if (X_MEMBER && $poll && isset ($pollanswers) && isset ($forums['pollstatus']) && $forums['pollstatus'] != 'off')
            {
                $pollanswers = checkInput($pollanswers);
                $pollopts = explode("\n", $pollanswers);
                $pnumnum = count($pollopts);
                if ($pnumnum < 2 && !empty ($pollanswers))
                {
                    error($lang['too_few_pollopts']);
                }

                $query = $db->query("SELECT vote_id, vote_id FROM " . X_PREFIX . "vote_desc WHERE topic_id = '$tid'");
                if ($query)
                {
                    $vote_id = $db->fetch_array($query);
                    $vote_id = intval($vote_id['vote_id']);
                    if ($vote_id > 0)
                    {
                        $db->query("DELETE FROM " . X_PREFIX . "vote_results WHERE vote_id = '$vote_id'");
                        $db->query("DELETE FROM " . X_PREFIX . "vote_voters WHERE vote_id = '$vote_id'");
                        $db->query("DELETE FROM " . X_PREFIX . "vote_desc WHERE vote_id = '$vote_id'");
                    }
                }
                $db->free_result($query);

                $db->query("INSERT INTO " . X_PREFIX . "vote_desc (topic_id, vote_text) VALUES ($tid, '$subject')");
                $vote_id = $db->insert_id();

                $i = 1;
                foreach ($pollopts as $p)
                {
                    $p = $db->escape(trim($p));
                    $db->query("INSERT INTO " . X_PREFIX . "vote_results (vote_id, vote_option_id, vote_option_text, vote_result) VALUES ($vote_id, $i, '$p', 0)");
                    $i++;
                }
                $db->query("UPDATE " . X_PREFIX . "threads SET pollopts = '1' WHERE tid = '$tid'");
            }

            if ($emailnotify == 'yes')
            {
                $query = $db->query("SELECT tid FROM " . X_PREFIX . "subscriptions WHERE tid = '$tid' AND username = '" . $self['username'] . "' AND type = 'subscription'");
                $thread = $db->fetch_array($query);
                $db->free_result($query);
                if (!$thread)
                {
                    $db->query("INSERT INTO " . X_PREFIX . "subscriptions (tid, username, type) VALUES ($tid, '$username', 'subscription')");
                }
            }

            if (isset ($forums['postcount']) && $forums['postcount'] == 'on')
            {
                $db->query("UPDATE " . X_PREFIX . "members SET threadnum = threadnum+1, postnum = postnum+1 WHERE username like '$username'");
            }
            else
            {
                $db->query("UPDATE " . X_PREFIX . "members SET threadnum = threadnum+1 WHERE username like '$username'");
            }

            if ((X_STAFF) && $toptopic == 'yes')
            {
                if (X_MOD && !isset ($modCheck))
                {
                    $modCheck = false;
                    $mods = $db->result($db->query("SELECT moderator FROM " . X_PREFIX . "forums WHERE fid = '$fid'"), 0);
                    $mods = explode(',', $mods);
                    foreach ($mods as $mod)
                    {
                        if (trim($mod) == $self['username'])
                        {
                            $modCheck = true;
                            break;
                        }
                    }
                }

                if (X_STAFF && ($modCheck || X_SMOD || X_ADMIN))
                {
                    $db->query("UPDATE " . X_PREFIX . "threads SET topped = '1' WHERE tid = '$tid' AND fid = '$fid'");
                    modaudit($self['username'], 'toptopic', $fid, $tid);
                }
            }

            if ((X_STAFF) && $closetopic == 'yes')
            {
                if (X_MOD && !isset ($modCheck))
                {
                    $modCheck = false;
                    $mods = $db->result($db->query("SELECT moderator FROM " . X_PREFIX . "forums WHERE fid = '$fid'"), 0);
                    $mods = explode(',', $mods);
                    foreach ($mods as $mod)
                    {
                        if (trim($mod) == $self['username'])
                        {
                            $modCheck = true;
                            break;
                        }
                    }
                }

                if (X_STAFF && ($modCheck || X_SMOD || X_ADMIN))
                {
                    $db->query("UPDATE " . X_PREFIX . "threads SET closed = 'yes' WHERE tid = '$tid' AND fid = '$fid'");
                    modaudit($self['username'], 'closetopic', $fid, $tid);
                }
            }

            eval ('echo "' . template('header') . '";');

            $query = $db->query("SELECT count(tid) FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
            $posts = $db->result($query, 0);
            $db->free_result($query);

            $topicpages = quickpage($posts, $self['ppp']);

            message($lang['postmsg'], false, '', '', "viewtopic.php?tid=" . $tid . "&page=" . $topicpages . "#pid" . $pid, true, false, true);
        }
        break;

    case 'reply' :
        $fattachnum = (int) $forum['attachnum'];
        eval ('$attachscript = "' . template('post_attach_edit_JS') . '";');
        nav('<a href="' . ROOT . 'viewtopic.php?tid=' . $tid . '">' . $thread['subject'] . '</a>');
        nav($lang['textreply']);
        btitle($thread['subject']);
        btitle($lang['textreply']);

        if (isset ($forums['mpnp']) && $forums['mpnp'] != 0 && isset ($self['postnum']) && $self['postnum'] < $forums['mpnp'] && !X_SADMIN)
        {
            $message = str_replace("*posts*", $forums['mpnp'], $lang['mpnpe']);
            error($message, false, '', '', false, true, false, true);
        }

        $priv = privfcheck($forums['private'], $forums['userlist']);
        if (noSubmit('replysubmit'))
        {
            $posts = '';
            eval ('echo "' . template('header') . '";');
            $status1 = modcheck($forums['moderator']);

            $topoption = $closeoption = '';
            if (X_STAFF && $status1 == 'Moderator')
            {
                $topoption = '<br /><input type="checkbox" name="toptopic" value="yes" ' . $topcheck . ' /> ' . $lang['topmsgques'];
                $closeoption = '<br /><input type="checkbox" name="closetopic" value="yes" ' . $closecheck . ' /> ' . $lang['closemsgques'];
            }

            if ($repquote > 0)
            {
                $query = $db->query("SELECT p.message, p.fid, p.author, f.private AS fprivate, f.userlist AS fuserlist, f.password AS fpassword FROM " . X_PREFIX . "posts p, " . X_PREFIX . "forums f WHERE pid = '$repquote' AND f.fid = p.fid");
                $thaquote = $db->fetch_array($query);
                $db->free_result($query);
                $quotefid = $thaquote['fid'];
                $pass = trim($thaquote['fpassword']);

                if (!X_ADMIN && trim($pass) != '' && $_COOKIE['fidpw' . $quotefid] != $pass)
                {
                    error($lang['privforummsg'], false, '', '', false, true, false, true);
                }

                $authorization = privfcheck($thaquote['fprivate'], $thaquote['fuserlist']);
                if (!$authorization)
                {
                    error($lang['privforummsg'], false, '', '', false, true, false, true);
                }

                $message = censor($thaquote['message']);
                $message = "[quote][i]$lang[origpostedby] $thaquote[author][/i]\n$thaquote[message] [/quote]";
            }

            $querytop = $db->query("SELECT COUNT(tid) FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
            $replynum = $db->result($querytop, 0);
            $db->free_result($querytop);

            if ($replynum >= $self['ppp'])
            {
                $threadlink = ROOT . 'viewtopic.php?fid=' . $fid . '&amp;tid=' . $tid;
                eval ($lang['evaltrevlt']);
                eval ('$posts .= "' . template('post_reply_review_toolong') . '";');
            }
            else
            {
                $thisbg = $THEME['altbg1'];
                $query = $db->query("SELECT * FROM " . X_PREFIX . "posts WHERE tid = '$tid' ORDER BY dateline DESC");
                while ($post = $db->fetch_array($query))
                {
                    $date = gmdate($self['dateformat'], $post['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
                    $time = gmdate($self['timecode'], $post['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
                    $poston = $lang['textposton'] . ' ' . $date . ' ' . $lang['textat'] . ' ' . $time;

                    if (!empty ($post['icon']) && file_exists($THEME['smdir'] . '/' . $post['icon']))
                    {
                        $post['icon'] = '<img src="' . $THEME['smdir'] . '/' . $post['icon'] . '" alt="' . $lang['altpostmood'] . '" title="' . $lang['altpostmood'] . '" border="0px" />';
                    }
                    else
                    {
                        $post['icon'] = '';
                    }

                    $post['message'] = $db->escape($post['message']);
                    $post['message'] = postify($post['message'], $post['smileyoff'], $post['bbcodeoff'], $forums['allowsmilies'], 'no', $forums['allowbbcode'], $forums['allowimgcode']);
                    eval ('$posts .= "' . template('post_reply_review_post') . '";');

                    if ($thisbg == $THEME['altbg2'])
                    {
                        $thisbg = $THEME['altbg1'];
                    }
                    else
                    {
                        $thisbg = $THEME['altbg2'];
                    }
                }
                $db->free_result($query);
            }

            if (isset ($forums['attachstatus']) && $forums['attachstatus'] == 'on')
            {
                eval ('$attachfile = "' . template('post_attachmentbox') . '";');
            }

            eval ('echo stripslashes("' . template('post_reply') . '");');
        }

        if (onSubmit('replysubmit'))
        {
            if (empty ($message))
            {
                error($lang['mincharsnomsg']);
            }

            if (!X_ADMIN)
            {
                if (strlen($message) < $forums['minchars'])
                {
                    $message = str_replace("*chars*", $forums['minchars'], $lang['mincharsmsg']);
                    error($message, true, '', '', false, true, false, true);
                }
            }

            if (!postperm($forums, 'reply'))
            {
                error($lang['postpermerr'], true, '', '', false, true, false, true);
            }

            if (!empty ($posticon))
            {
                $posticon = $db->escape($posticon);
                $query = $db->query("SELECT id FROM " . X_PREFIX . "smilies WHERE type = 'picon' AND url = '$posticon' ORDER BY id ASC");
                if (!$db->result($query, 0))
                {
                    exit;
                }
                $db->free_result($query);
            }
            else
            {
                $posticon = '';
            }

            if (!X_ADMIN)
            {
                $query = $db->query("SELECT l.username, l.dateline, f.type, f.fup FROM " . X_PREFIX . "forums f LEFT JOIN " . X_PREFIX . "lastposts l ON f.lastpost = l.tid WHERE fid = '$fid' LIMIT 1");
                $for = $db->fetch_array($query);
                $db->free_result($query);

                $rightnow = $onlinetime - $CONFIG['floodctrl'];
                if ($rightnow <= $for['dateline'] && $username == $for['username'])
                {
                    error($lang['floodprotect'], true, '', '', false, true, false, true);
                }
            }

            if ($usesig != 'yes')
            {
                $usesig = 'no';
            }

            for ($i = 0; $i < $forum['attachnum']; $i++)
            {
                if (isset ($_FILES['attach']['name'][$i]))
                {
                    // The external check alone will fail if exceeds the size. ~martijn
                    if ($_FILES['attach']['error'][$i] === 1 || $_FILES['attach']['error'][$i] === 2)
                    {
                        error($lang['attachtoobig']);
                    }

                    if (isset ($_FILES['attach']) && ($attachment = get_attached_file_multi($_FILES['attach'], $i, $forums['attachstatus'])) === false)
                    {
                        continue;
                    }
                    else
                    {
                        if (isset ($attachment))
                        {
                            $next_pid = $db->getNextId('posts');
                            $filename = $db->escape($filename);
                            $filetype = $db->escape($filetype);
                            $filesize = intval($filesize);
                            $fileheight = intval($fileheight);
                            $filewidth = intval($filewidth);

                            $db->query("INSERT INTO " . X_PREFIX . "attachments (tid, pid, filename, filetype, filesize, fileheight, filewidth, attachment, downloads) VALUES ($tid, $next_pid, '$filename', '$filetype', '$filesize', '$fileheight', '$filewidth', '$attachment', 0)");
                        }
                    }
                }
            }

            $subject = $db->escape($subject);
            $message = $db->escape($message);
            $forums['subjectprefixes'] = $db->escape(trim($forums['subjectprefixes']));

            $query = $db->query("SELECT closed, topped FROM " . X_PREFIX . "threads WHERE fid = '$fid' AND tid = '$tid'");
            $closed1 = $db->fetch_array($query);
            $db->free_result($query);
            $closed = $closed1['closed'];
            if ($closed == 'yes' && !X_STAFF)
            {
                error($lang['closedmsg'], true, '', '', false, true, false, true);
            }
            else
            {
                $subject = checkInput($subject);
                $message = checkInput($message);
                $db->query("INSERT INTO " . X_PREFIX . "posts (fid, tid, author, message, subject, dateline, icon, usesig, useip, bbcodeoff, smileyoff) VALUES ($fid, $tid, '$username', '$message', '" . $db->escape($subjectprefix) . " $subject', " . $db->time($onlinetime) . ", '$posticon', '$usesig', '$onlineip', '$bbcodeoff', '$smileyoff')");
                $pid = $db->insert_id();

                if ((X_STAFF) && $toptopic == 'yes')
                {
                    if (X_MOD && !isset ($modCheck))
                    {
                        $modCheck = false;
                        $mods = $db->result($db->query("SELECT moderator FROM " . X_PREFIX . "forums WHERE fid = '$fid'"), 0);
                        $mods = explode(',', $mods);
                        foreach ($mods as $mod)
                        {
                            if (trim($mod) == $self['username'])
                            {
                                $modCheck = true;
                                break;
                            }
                        }
                    }

                    if (X_STAFF && ($modCheck || X_SMOD || X_ADMIN))
                    {
                        $db->query("UPDATE " . X_PREFIX . "threads SET topped = '1' WHERE tid = '$tid' AND fid = '$fid'");
                        modaudit($self['username'], 'toptopic', $fid, $tid);
                    }
                }

                if ((X_STAFF) && $closetopic == 'yes')
                {
                    if (X_MOD && !isset ($modCheck))
                    {
                        $modCheck = false;
                        $mods = $db->result($db->query("SELECT moderator FROM " . X_PREFIX . "forums WHERE fid = '$fid'"), 0);
                        $mods = explode(',', $mods);
                        foreach ($mods as $mod)
                        {
                            if (trim($mod) == $self['username'])
                            {
                                $modCheck = true;
                                break;
                            }
                        }
                    }

                    if (X_STAFF && ($modCheck || X_SMOD || X_ADMIN))
                    {
                        $db->query("UPDATE " . X_PREFIX . "threads SET closed = 'yes' WHERE tid = '$tid' AND fid = '$fid'");
                        modaudit($self['username'], 'closetopic', $fid, $tid);
                    }
                }

                $db->query("UPDATE " . X_PREFIX . "lastposts SET uid = '" . $self['uid'] . "', username = '$username', dateline = '$onlinetime', pid = '$pid' WHERE tid = '$tid' LIMIT 1");
                $db->query("UPDATE " . X_PREFIX . "threads SET replies = replies+1 WHERE (tid = '$tid' AND fid = '$fid') OR closed = 'moved|$tid'");

                if (isset ($forum['type']) && $forum['type'] == 'sub')
                {
                    $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$tid', posts = posts+1 WHERE fid = '$forum[fup]'");
                }

                $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$tid', posts = posts+1 WHERE fid = '$fid'");

                if (isset ($forums['postcount']) && $forums['postcount'] == 'on')
                {
                    $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum+1 WHERE username = '$username'");
                }

                $query = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE pid <= $pid AND tid = '$tid'");
                $posts = $db->result($query, 0);
                $db->free_result($query);

                if (isset ($self['psorting']) && $self['psorting'] == 'ASC' && ($posts > $self['ppp']))
                {
                    $topicpages = quickpage($posts, $self['ppp']);
                }
                else
                {
                    $topicpages = 1;
                }

                $date = $db->result($db->query("SELECT dateline FROM " . X_PREFIX . "posts WHERE tid = '$tid' AND pid < '$pid' ORDER BY pid ASC LIMIT 1"), 0);
                $subquery = $db->query("SELECT m.username, m.email, m.lastvisit, m.status FROM " . X_PREFIX . "subscriptions f LEFT JOIN " . X_PREFIX . "members m ON (m.username = f.username) WHERE f.type = 'subscription' AND f.tid = '$tid' AND f.username != '$username'");
                while ($subs = $db->fetch_array($subquery))
                {
                    if ($subs['status'] == 'Banned' || $subs['lastvisit'] < $date)
                    {
                        continue;
                    }

                    $threadurl = $CONFIG['boardurl'] . 'viewtopic.php?tid=' . $tid . '&page=' . $topicpages . '#pid' . $pid;

                    $mailsys->setTo($subs['email']);
                    $mailsys->setFrom($CONFIG['adminemail'], $CONFIG['bbname']);
                    $mailsys->setSubject($lang['textsubsubject'] . ' ' . $thread['subject']);
                    $mailsys->setMessage($username . ' ' . $lang['textsubbody'] . " \n" . $threadurl);
                    $mailsys->Send();
                }
                $db->free_result($subquery);

                if ($emailnotify == 'yes')
                {
                    $query = $db->query("SELECT tid FROM " . X_PREFIX . "subscriptions WHERE tid = '$tid' AND username = '" . $self['username'] . "' AND type = 'subscription'");
                    if ($db->num_rows($query) < 1)
                    {
                        $db->query("INSERT INTO " . X_PREFIX . "subscriptions (tid, username, type) VALUES ($tid, '$username', 'subscription')");
                    }
                    $db->free_result($query);
                }

                eval ('echo "' . template('header') . '";');
            }

            message($lang['replymsg'], false, '', '', 'viewtopic.php?tid=' . $tid . '&page=' . $topicpages . '#pid' . $pid, true, false, true);
        }
        break;

    case 'delete' :
        if (isset ($self['status']) && $self['status'] == 'Banned')
        {
            error($lang['bannedmessage']);
        }

        if (!postperm($forums, 'delete'))
        {
            error($lang['postpermerr']);
        }

        if (noSubmit('deletepostsubmit'))
        {
            nav('<a href="' . ROOT . 'viewtopic.php?tid=' . $tid . '">' . $thread['subject'] . '</a>');
            nav($lang['textdeletepost']);
            btitle($lang['textdeletepost']);
            eval ('echo "' . template('header') . '";');
            eval ('echo "' . template('post_delete_confirm') . '";');
        }

        if (onSubmit('deletepostsubmit'))
        {
            $query = $db->query("SELECT pid FROM " . X_PREFIX . "posts WHERE tid = '$tid' ORDER BY dateline LIMIT 1");
            $isfirstpost = $db->fetch_array($query);
            $db->free_result($query);

            $query = $db->query("SELECT p.author as author, m.status as status, p.subject as subject FROM " . X_PREFIX . "posts p LEFT JOIN " . X_PREFIX . "members m ON p.author = m.username WHERE pid = '$pid' AND tid = '$tid' AND fid = '$fid'");
            $orig = $db->fetch_array($query);
            $db->free_result($query);

            $query = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE pid <= $pid AND tid = '$tid'");
            $ppages = $db->result($query, 0);
            $db->free_result($query);

            if (isset ($self['psorting']) && $self['psorting'] == 'ASC' && ($ppages > $self['ppp']))
            {
                $topicpages = quickpage($ppages, $self['ppp']);
            }
            else
            {
                $topicpages = 1;
            }

            if (X_STAFF && $status1 == 'Moderator' || $self['username'] == $orig['author'])
            {
                if ($CONFIG['allowrankedit'] == 'on')
                {
                    switch ($orig['status'])
                    {
                        case 'Super Administrator' :
                            if (!X_SADMIN)
                            {
                                error($lang['noedit']);
                            }
                            break;
                        case 'Administrator' :
                            if (!X_ADMIN)
                            {
                                error($lang['noedit']);
                            }
                            break;
                        case 'Super Moderator' :
                            if (!X_ADMIN && isset ($self['status']) && $self['status'] != 'Super Moderator')
                            {
                                error($lang['noedit']);
                            }
                            break;
                        case 'Moderator' :
                            if (!X_ADMIN && isset ($self['status']) && $self['status'] != 'Moderator')
                            {
                                error($lang['noedit']);
                            }
                            break;
                    }
                }

                eval ('echo "' . template('header') . '";');

                $orig_author = $db->escape($orig['author']);

                if ($isfirstpost['pid'] != $pid)
                {
                    if (isset ($forums['postcount']) && $forums['postcount'] == 'on')
                    {
                        $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum-1 WHERE username = '" . $orig_author . "'");
                    }
                    $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE pid = '$pid'");
                    $db->query("DELETE FROM " . X_PREFIX . "posts WHERE pid = '$pid'");
                    updateforumcount($fid);
                    updatethreadcount($tid);

                    // We don't log self-deletes, only staff deletes
                    if (X_STAFF && $self['username'] != $orig['author'])
                    {
                        modaudit($self['username'], 'deletepost', $fid, $tid, $lang['postdeletedpost'] . ' ' . $pid . ' ' . $lang['postofuser'] . ' ' . $orig['author']);
                    }

                    message($lang['deletepostmsg'], false, '', '', 'viewtopic.php?tid=' . $tid . '&page=' . $topicpages, true, false, true);

                }
                elseif ($isfirstpost['pid'] == $pid)
                {
                    $query = $db->query("SELECT pid FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
                    $numrows = $db->num_rows($query);
                    $db->free_result($query);

                    if ($numrows == 1)
                    {
                        if (isset ($forums['postcount']) && $forums['postcount'] == 'on')
                        {
                            $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum-1 WHERE username = '" . $orig_author . "'");
                        }
                        $db->query("DELETE FROM " . X_PREFIX . "threads WHERE tid = '$tid'");
                        $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE tid = '$tid'");
                        $db->query("DELETE FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
                    }

                    if ($numrows > 1)
                    {
                        if (isset ($forums['postcount']) && $forums['postcount'] == 'on')
                        {
                            $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum-1 WHERE username = '" . $orig_author . "'");
                        }
                        $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE pid = '$pid'");
                        $db->query("DELETE FROM " . X_PREFIX . "posts WHERE pid = '$pid'");
                        $db->query("UPDATE " . X_PREFIX . "posts SET subject = '" . $db->escape($orig['subject']) . "' WHERE tid = '$tid' ORDER BY dateline ASC LIMIT 1");
                    }

                    if (isset ($forum['type']) && isset ($forum['type']) == 'sub')
                    {
                        $forumController->updateLPFUP($forum['fup'], $fid);
                    }
                    updateforumcount($fid);
                    updatethreadcount($tid);

                    if (X_STAFF && $self['username'] != $orig['author'])
                    {
                        modaudit($self['username'], 'deletepost', $fid, $tid, $lang['postdeletedpost'] . ' ' . $pid . ' ' . $lang['postofuser'] . ' ' . $orig['author']);
                    }

                    // extra switch to check which message to display and thus where to redirect to
                    switch ($numrows)
                    {
                        case '1' :
                            message($lang['deletepostmsg'], false, '', '', 'viewforum.php?fid=' . $fid, true, false, true);
                            break;
                        default :
                            message($lang['deletepostmsg'], false, '', '', 'viewtopic.php?tid=' . $tid, true, false, true);
                            break;
                    }
                }
            }
            else
            {
                error($lang['noedit']);
            }
        }
        break;

    case 'edit' :
        eval ('$attachscript = "' . template('post_edit_attach_JS') . '";');
        nav('<a href="' . ROOT . 'viewtopic.php?tid=' . $tid . '">' . $thread['subject'] . '</a>');
        nav($lang['texteditpost']);
        btitle($thread['subject']);
        btitle($lang['texteditpost']);

        if (!postperm($forums, 'edit'))
        {
            error($lang['postpermerr']);
        }

        if (noSubmit('editsubmit'))
        {
            eval ('echo "' . template('header') . '";');
            $status1 = modcheck($forums['moderator']);

            $topoption = $closeoption = '';
            if (X_STAFF && $status1 == 'Moderator')
            {
                $topoption = '<br /><input type="checkbox" name="toptopic" value="yes" ' . $topcheck . ' /> ' . $lang['topmsgques'];
                $closeoption = '<br /><input type="checkbox" name="closetopic" value="yes" ' . $closecheck . ' /> ' . $lang['closemsgedit'];
            }

            $postdelete = '';
            eval ('$postdelete = "' . template('post_edit_delete') . '";');

            $queryextra = $db->query("SELECT f.* FROM " . X_PREFIX . "posts p LEFT JOIN " . X_PREFIX . "forums f ON (f.fid = p.fid) WHERE p.tid = '$tid' AND p.pid = '$pid'");
            $forum = $db->fetch_array($queryextra);
            $db->free_result($queryextra);

            $authorization = privfcheck($forum['private'], $forum['userlist']);
            if (!$authorization)
            {
                error($lang['privforummsg'], false);
            }

            if (isset ($previewpost))
            {
                $postinfo = array (
                    "usesig" => $usesig,
                    "bbcodeoff" => $bbcodeoff,
                    "smileyoff" => $smileyoff,
                    "message" => $message,
                    "subject" => $subject,
                    'icon' => $posticon
                );
                $query = $db->query("SELECT filename, filesize, downloads FROM " . X_PREFIX . "attachments WHERE pid = '$pid' AND tid = '$tid'");
                if ($db->num_rows($query) > 0)
                {
                    $postinfo = array_merge($postinfo, $db->fetch_array($query));
                }
                $db->free_result($query);
            }
            else
            {
                $query = $db->query("SELECT p.* FROM " . X_PREFIX . "posts p WHERE p.pid = '$pid' AND p.tid = '$tid' AND p.fid = '$forum[fid]'");
                $postinfo = $db->fetch_array($query);
                $db->free_result($query);
            }

            if (isset ($postinfo['filesize']))
            {
                $postinfo['filesize'] = number_format($postinfo['filesize'], 0, '.', ',');
            }

            $postinfo['message'] = stripslashes($postinfo['message']);

            $offcheck1 = '';
            if (isset ($postinfo['bbcodeoff']) && $postinfo['bbcodeoff'] == 'yes')
            {
                $offcheck1 = $cheHTML;
            }

            $offcheck2 = '';
            if (isset ($postinfo['smileyoff']) && $postinfo['smileyoff'] == 'yes')
            {
                $offcheck2 = $cheHTML;
            }

            $offcheck3 = '';
            if (isset ($postinfo['usesig']) && $postinfo['usesig'] == 'yes')
            {
                $offcheck3 = $cheHTML;
            }

            $querysmilie = $db->query("SELECT * FROM " . X_PREFIX . "smilies WHERE type = 'picon' ORDER BY id ASC");
            while ($smilie = $db->fetch_array($querysmilie))
            {
                if ($postinfo['icon'] == $smilie['url'])
                {
                    $icons .= '&nbsp;<input type="radio" name="posticon" value="' . $smilie['url'] . '" ' . $cheHTML . ' />&nbsp;<img src="' . $THEME['smdir'] . '/' . $smilie['url'] . '" alt="' . $smilie['code'] . '" title="' . $smilie['code'] . '" border="0px" />';
                }
                else
                {
                    $icons .= '&nbsp;<input type="radio" name="posticon" value="' . $smilie['url'] . '" />&nbsp;<img src="' . $THEME['smdir'] . '/' . $smilie['url'] . '" alt="' . $smilie['code'] . '" title="' . $smilie['code'] . '" border="0px" />';
                }

                $listed_icons += 1;
                if ($listed_icons == 10)
                {
                    $icons .= '<br />';
                    $listed_icons = 0;
                }
            }
            $db->free_result($querysmilie);

            $postinfo['subject'] = stripslashes(censor($postinfo['subject']));
            $postinfo['subject'] = str_replace('"', "&quot;", $postinfo['subject']);

            $message = $postinfo['message'];
            $subject = $postinfo['subject'];

            if (isset ($previewpost))
            {
                $message = censor($message);
            }

            $q = $db->query("SELECT * FROM " . X_PREFIX . "attachments WHERE pid = '$pid'");
            $i = 0;
            if ($db->num_rows($q) > 0)
            {
                while ($attach = $db->fetch_array($q))
                {
                    eval ('$attachment[] = "' . template('post_edit_attachment') . '";');
                    $i++;
                }
                $attachment = implode("\n", $attachment);
                $db->free_result($q);
            }
            else
            {
                $attachment = '';
            }

            $fattachnum = $forum['attachnum'] - $i;
            if ($fattachnum > 0 && isset ($forums['attachstatus']) && $forums['attachstatus'] == 'on')
            {
                eval ('$attachment .= "' . template('post_attachmentbox') . '";');
            }
            eval ('echo "' . template('post_attach_edit_JS') . template('post_edit') . '";');
        }

        if (onSubmit('editsubmit'))
        {
            $date = gmdate($self['dateformat']);
            if ($CONFIG['editedby'] == 'on')
            {
                $message .= "\n\n[ $lang[textediton] $date $lang[textby] $username ]";
            }

            $subject = $db->escape(checkInput($subject));
            $message = $db->escape(checkInput($message));
            $posticon = $db->escape($posticon);

            $status1 = modcheck($forums['moderator'], $username);

            if (!empty ($posticon))
            {
                $query = $db->query("SELECT id FROM " . X_PREFIX . "smilies WHERE type = 'picon' AND url = '$posticon' ORDER BY id ASC");
                if (!$db->result($query, 0))
                {
                    exit;
                }
                $db->free_result($query);
            }
            else
            {
                $posticon = '';
            }

            $query = $db->query("SELECT pid FROM " . X_PREFIX . "posts WHERE tid = '$tid' ORDER BY dateline LIMIT 1");
            $isfirstpost = $db->fetch_array($query);
            $db->free_result($query);

            if (empty ($subject) && $pid == $isfirstpost['pid'])
            {
                error($lang['textnosubject']);
            }

            if (empty ($message))
            {
                error($lang['mincharsnomsg']);
            }

            if (!X_ADMIN)
            {
                if (strlen($message) < $forums['minchars'])
                {
                    $message = str_replace("*chars*", $forums['minchars'], $lang['mincharsmsg']);
                    error($message);
                }
            }

            $query = $db->query("SELECT p.author as author, m.status as status, p.subject as subject FROM " . X_PREFIX . "posts p LEFT JOIN " . X_PREFIX . "members m ON p.author = m.username WHERE pid = '$pid' AND tid = '$tid' AND fid = '$fid'");
            $orig = $db->fetch_array($query);
            $db->free_result($query);

            if ((X_STAFF && $status1 == 'Moderator') || $username == $orig['author'])
            {
                if ($CONFIG['allowrankedit'] == 'on')
                {
                    switch ($orig['status'])
                    {
                        case 'Super Administrator' :
                            if (!X_SADMIN && $self['username'] != $orig['author'])
                            {
                                error($lang['noedit']);
                            }
                            break;
                        case 'Administrator' :
                            if (!X_ADMIN && $self['username'] != $orig['author'])
                            {
                                error($lang['noedit']);
                            }
                            break;
                        case 'Super Moderator' :
                            if ((!X_ADMIN && isset ($self['status']) && $self['status'] != 'Super Moderator') && $self['username'] != $orig['author'])
                            {
                                error($lang['noedit']);
                            }
                            break;
                        case 'Moderator' :
                            if ((!X_ADMIN && isset ($self['status']) && $self['status'] != 'Moderator') && $self['username'] != $orig['author'])
                            {
                                error($lang['noedit']);
                            }
                            break;
                    }
                }

                $attach_edit = (isset ($attach_edit) ? $attach_edit : '');
                $fattachnum = $forum['attachnum'] - count($attach_edit);

                for ($i = 0; $i < $fattachnum; $i++)
                {
                    if (isset ($_FILES['attach']['name'][$i]))
                    {
                        // The external check alone will fail if exceeds the size. ~martijn
                        if ($_FILES['attach']['error'][$i] === 1 || $_FILES['attach']['error'][$i] === 2)
                        {
                            error($lang['attachtoobig']);
                        }

                        if (isset ($_FILES['attach']) && ($attachment = get_attached_file_multi($_FILES['attach'], $i, $forums['attachstatus'])) === false)
                        {
                            continue;
                        }
                        else
                        {
                            if (isset ($attachment))
                            {
                                $filename = $db->escape($filename);
                                $filetype = $db->escape($filetype);
                                $filesize = intval($filesize);
                                $fileheight = intval($fileheight);
                                $filewidth = intval($filewidth);

                                $db->query("INSERT INTO " . X_PREFIX . "attachments (tid, pid, filename, filetype, filesize, fileheight, filewidth, attachment, downloads) VALUES ($tid, $pid, '$filename', '$filetype', '$filesize', '$fileheight', '$filewidth', '$attachment', 0)");
                            }
                        }
                    }
                }

                if ($isfirstpost['pid'] == $pid)
                {
                    $db->query("UPDATE " . X_PREFIX . "threads SET icon = '$posticon', subject = '" . $db->escape($subjectprefix) . " $subject' WHERE tid = '$tid'");
                }

                $threaddelete = 'no';
                eval ('echo "' . template('header') . '";');

                $db->query("UPDATE " . X_PREFIX . "posts SET message = '$message', usesig = '$usesig', bbcodeoff = '$bbcodeoff', smileyoff = '$smileyoff', icon = '$posticon', subject = '" . $db->escape($subjectprefix) . " $subject' WHERE pid = '$pid'");

                for ($i = 0; $i < $fattachnum; $i++)
                {
                    if (isset ($_FILES['attach']['name'][$i]))
                    {
                        // The external check alone will fail if exceeds the size. ~martijn
                        if ($_FILES['attach']['error'][$i] === 1 || $_FILES['attach']['error'][$i] === 2)
                        {
                            error($lang['attachtoobig']);
                        }

                        if (isset ($_FILES['attach']) && ($attachment = get_attached_file_multi($_FILES['attach'], $i, $forums['attachstatus'])) === false)
                        {
                            continue;
                        }
                        else
                        {
                            if (isset ($attachment))
                            {
                                $filename = $db->escape($filename);
                                $filetype = $db->escape($filetype);
                                $filesize = intval($filesize);
                                $fileheight = intval($fileheight);
                                $filewidth = intval($filewidth);

                                $db->query("INSERT INTO " . X_PREFIX . "attachments (tid, pid, filename, filetype, filesize, fileheight, filewidth, attachment, downloads) VALUES ($tid, $pid, '$filename', '$filetype', '$filesize', '$fileheight', '$filewidth', '$attachment', 0)");
                            }
                        }
                    }
                }

                if (isset ($attach_edit) && is_array($attach_edit))
                {
                    for ($i = 0; $i < count($attach_edit); $i++)
                    {
                        switch ($attach_edit[$i])
                        {
                            case 'replace' :
                                if (isset ($_FILES['attach_edit_file']['name'][$i]))
                                {
                                    // The external check alone will fail if exceeds the size. ~martijn
                                    if ($_FILES['attach_edit_file']['error'][$i] === 1 || $_FILES['attach_edit_file']['error'][$i] === 2)
                                    {
                                        error($lang['attachtoobig']);
                                    }

                                    $attachment = get_attached_file_multi($_FILES['attach_edit_file'], $i, $forum['attachstatus']);
                                    if (!$attachment)
                                    {
                                        break;
                                    }
                                    else
                                    {
                                        $attach_edit_aid[$i] = intval($attach_edit_aid[$i]);
                                        $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE pid = '$pid' AND aid = '$attach_edit_aid[$i]'");
                                        $filename = $db->escape($filename);
                                        $filetype = $db->escape($filetype);
                                        $filesize = intval($filesize);
                                        $fileheight = intval($fileheight);
                                        $filewidth = intval($filewidth);

                                        $db->query("INSERT INTO " . X_PREFIX . "attachments (tid, pid, filename, filetype, filesize, fileheight, filewidth, attachment, downloads) VALUES ($tid, $pid, '$filename', '$filetype', '$filesize', '$fileheight', '$filewidth', '$attachment', 0)");
                                    }
                                }
                                break;
                            case 'rename' :
                                $name = basename($attach_edit_name[$i]);
                                if (strlen(trim($name)) > 2 || preg_match('#^[^:\\/?*<>|]+$#', $name) == 1)
                                {
                                    $name = $db->escape($name);
                                    $attach_edit_aid[$i] = intval($attach_edit_aid[$i]);
                                    $db->query("UPDATE " . X_PREFIX . "attachments SET filename = '$name' WHERE pid = '$pid' AND aid = '$attach_edit_aid[$i]'");
                                }
                                break;
                            case 'delete' :
                                $attach_edit_aid[$i] = intval($attach_edit_aid[$i]);
                                $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE pid = '$pid' AND aid = '$attach_edit_aid[$i]'");
                                break;
                            default :
                                break;
                        }
                    }
                }

                if ((X_STAFF) && $toptopic == 'yes')
                {
                    if (X_MOD && !isset ($modCheck))
                    {
                        $modCheck = false;
                        $mods = $db->result($db->query("SELECT moderator FROM " . X_PREFIX . "forums WHERE fid = '$fid'"), 0);
                        $mods = explode(',', $mods);
                        foreach ($mods as $mod)
                        {
                            if (trim($mod) == $self['username'])
                            {
                                $modCheck = true;
                                break;
                            }
                        }
                    }

                    if (X_STAFF && ($modCheck || X_SMOD || X_ADMIN))
                    {
                        $db->query("UPDATE " . X_PREFIX . "threads SET topped = '1' WHERE tid = '$tid' AND fid = '$fid'");
                        modaudit($self['username'], 'toptopic', $fid, $tid);
                    }
                }

                if ((X_STAFF) && $closetopic == 'yes')
                {
                    if (X_MOD && !isset ($modCheck))
                    {
                        $modCheck = false;
                        $mods = $db->result($db->query("SELECT moderator FROM " . X_PREFIX . "forums WHERE fid = '$fid'"), 0);
                        $mods = explode(',', $mods);
                        foreach ($mods as $mod)
                        {
                            if (trim($mod) == $self['username'])
                            {
                                $modCheck = true;
                                break;
                            }
                        }
                    }

                    if (X_STAFF && ($modCheck || X_SMOD || X_ADMIN))
                    {
                        $db->query("UPDATE " . X_PREFIX . "threads SET closed = 'yes' WHERE tid = '$tid' AND fid = '$fid'");
                        modaudit($self['username'], 'closetopic', $fid, $tid);
                    }
                }

                $query = $db->query("SELECT author FROM " . X_PREFIX . "posts WHERE pid = '$pid'");
                $postUser = $db->result($query, 0);
                $db->free_result($query);

                if (isset ($delete) && $delete == 'yes' && !($isfirstpost['pid'] == $pid))
                {
                    if (isset ($forums['postcount']) && $forums['postcount'] == 'on')
                    {
                        $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum-1 WHERE username = '" . $db->escape($orig['author']) . "'");
                    }
                    $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE pid = '$pid'");
                    $db->query("DELETE FROM " . X_PREFIX . "posts WHERE pid = '$pid'");
                    updateforumcount($fid);
                    updatethreadcount($tid);
                    //updatelastposts(); ~ use this function with caution; VERY server-consuming ~martijn

                    // We don't log self-deletes, only staff deletes
                    if (X_STAFF && $self['username'] != $postUser)
                    {
                        modaudit($self['username'], 'deletepost', $fid, $tid, 'Deleted post ' . $pid . ' of user ' . $postUser);
                    }
                }
                elseif (isset ($delete) && $delete == 'yes' && $isfirstpost['pid'] == $pid)
                {
                    $query = $db->query("SELECT pid FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
                    $numrows = $db->num_rows($query);
                    $db->free_result($query);

                    if ($numrows == 1)
                    {
                        if (isset ($forums['postcount']) && $forums['postcount'] == 'on')
                        {
                            $query = $db->query("SELECT author FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
                            while ($result = $db->fetch_array($query))
                            {
                                $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum-1 WHERE username = '" . $db->escape($result['author']) . "'");
                            }
                            $db->free_result($query);
                        }
                        $db->query("DELETE FROM " . X_PREFIX . "threads WHERE tid = '$tid'");
                        $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE tid = '$tid'");
                        $db->query("DELETE FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
                        $threaddelete = 'yes';
                    }

                    if ($numrows > 1)
                    {
                        if (isset ($forums['postcount']) && $forums['postcount'] == 'on')
                        {
                            $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum-1 WHERE username = '" . $db->escape($orig['author']) . "'");
                        }
                        $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE pid = '$pid'");
                        $db->query("DELETE FROM " . X_PREFIX . "posts WHERE pid = '$pid'");
                        $db->query("UPDATE " . X_PREFIX . "posts SET subject = '" . $db->escape($orig['subject']) . "' WHERE tid = '$tid' ORDER BY dateline ASC LIMIT 1");

                        $threaddelete = 'no';
                    }
                    updateforumcount($fid);
                    updatethreadcount($tid);
                    //updatelastposts(); ~ use this function with caution; VERY server-consuming ~martijn

                    if (X_STAFF && $self['username'] != $postUser)
                    {
                        modaudit($self['username'], 'deletepost', $fid, $tid, 'Deleted post ' . $pid . ' of user ' . $postUser);
                    }
                }
            }
            else
            {
                error($lang['noedit']);
            }

            if ($threaddelete != 'yes')
            {
                $query = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE pid <= $pid AND tid = '$tid' AND fid = '$fid'");
                $posts = $db->result($query, 0);
                $db->free_result($query);
                if (isset ($self['psorting']) && $self['psorting'] == 'ASC')
                {
                    $topicpages = quickpage($posts, $self['ppp']);
                }
                else
                {
                    $topicpages = 1;
                }
                message($lang['editpostmsg'], false, '', '', "viewtopic.php?tid=${tid}&page=${topicpages}#pid${pid}", true, false, true);
            }
            else
            {
                message($lang['editpostmsg'], false, '', '', 'viewforum.php?fid=' . $fid, true, false, true);
            }
        }
        break;
    case 'captcha' :
        require_once (ROOTCLASS . 'captcha.class.php');
        $captcha = new captcha();
        break;
    default :
        error($lang['textnoaction'], true, '', '', false, true, false, true);
        break;
}

loadtime();
eval ('echo "' . template('footer') . '";');
?>
