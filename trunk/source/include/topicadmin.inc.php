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

if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false))
{
    exit('This file is not designed to be called directly');
}

class mod
{
    function mod()
    {
        global $lang, $action;

        if (!X_STAFF && $action != 'votepoll' && $action != 'report')
        {
            error($lang['textnoaction'], false);
        }
    }

    function statuscheck($fid)
    {
        global $lang, $db, $self;

        if (!X_STAFF)
        {
            error($lang['textnoaction'], false);
            return false;
        }

        $query = $db->query("SELECT moderator FROM ".X_PREFIX."forums WHERE fid = '$fid'");
        $mods = $db->result($query, 0);
        $db->free_result($query);
        $status1 = modcheck($mods);

        if (X_SMOD || X_ADMIN)
        {
            $status1 = 'Moderator';
        }

        if ($status1 != 'Moderator')
        {
            error($lang['textnoaction'], false);
            return false;
        }
        return true;
    }

    function log($user='', $action, $fid, $tid)
    {
        global $self, $db;

        if (empty($user))
        {
            $user = $self['username'];
        }

        $action = addslashes($action);

        $db->query("INSERT INTO ".X_PREFIX."modlogs (tid, username, action, fid, date) VALUES ('$tid', '$user', '$action', '$fid', ".$db->time().")");
        return true;
    }

    function doDelete()
    {
        global $db, $lang, $tid, $fid, $self, $action;

        $query = $db->query("SELECT author FROM ".X_PREFIX."posts WHERE tid = '$tid'");

        if ($query === false || $db->num_rows($query) == 0)
        {
            error($lang['textnothread'], false);
        }

        while ($result = $db->fetch_array($query))
        {
            $db->query("UPDATE ".X_PREFIX."members SET postnum = postnum-1 WHERE username = '$result[author]'");
        }
        $db->free_result($query);

        $query = $db->query("SELECT subject FROM ".X_PREFIX."threads WHERE tid = '$tid'");
        $subject = $db->result($query, 0);
        $db->free_result($query);

        $db->query("DELETE FROM ".X_PREFIX."threads WHERE tid = '$tid'");
        $db->query("DELETE FROM ".X_PREFIX."lastposts WHERE tid = '$tid'");
        $db->query("DELETE FROM ".X_PREFIX."posts WHERE tid = '$tid'");
        $db->query("DELETE FROM ".X_PREFIX."attachments WHERE tid = '$tid'");
        $db->query("DELETE FROM ".X_PREFIX."favorites WHERE tid = '$tid'");
        $db->query("DELETE FROM ".X_PREFIX."subscriptions WHERE tid = '$tid'");

        $db->query("DELETE FROM ".X_PREFIX."threads WHERE closed = 'moved|$tid'");

        $query = $db->query("SELECT * FROM ".X_PREFIX."forums WHERE fid = '$fid'");
        $forums = $db->fetch_array($query);
        $db->free_result($query);

        if (isset($forums['type']) && isset($forums['type']) == 'sub')
        {
            updateforumcount($forums['fup']);
        }
        updateforumcount($fid);

        $this->log($self['username'], $action . ": " . $subject, $fid, $tid);
        message($lang['deletethreadmsg'], false, '', '', 'viewforum.php?fid='.$fid, true, false, true);
    }

    function viewDelete()
    {
        global $THEME, $lang, $fid, $tid, $self, $oToken, $shadow;
        eval('echo stripslashes("'.template('topicadmin_delete').'");');
    }

    function doClose($closed)
    {
        global $db, $self, $fid, $tid, $lang;

        if ($closed == 'yes')
        {
            $db->query("UPDATE ".X_PREFIX."threads SET closed='' WHERE tid = '$tid' AND fid = '$fid'");
        }
        else
        {
            $db->query("UPDATE ".X_PREFIX."threads SET closed='yes' WHERE tid = '$tid' AND fid = '$fid'");
        }

        $query = $db->query("SELECT subject FROM ".X_PREFIX."threads WHERE tid = '$tid'");
        $subject = $db->result($query, 0);
        $db->free_result($query);

        $act = ($closed != '') ? 'open' : 'close' . ": " . $subject;
        $this->log($self['username'], $act, $fid, $tid);
        message($lang['closethreadmsg'], false, '', '', 'viewforum.php?fid='.$fid, true, false, true);
    }

    function viewClose($closed)
    {
        global $lang, $fid, $tid, $self, $THEME, $self, $oToken, $shadow;

        if ($closed == 'yes')
        {
            $lang['textclosethread'] = $lang['textopenthread'];
        }
        else if (empty($closed))
        {
            $lang['textclosethread'] = $lang['textclosethread'];
        }
        eval('echo stripslashes("'.template('topicadmin_openclose').'");');
    }

    function doMove()
    {
        global $fid, $lang, $db, $tid, $forums, $fup, $self, $action;

        $moveto = formInt('moveto');
        $type = formVar('type');

        if ($moveto != 0)
        {
            if ($moveto == $fid)
            {
                error($lang['topicmovefail'], false);
            }

            $query = $db->query("SELECT t.tid, t.fid, t.author, t.subject, t.topped, l.uid as lp_uid, l.username as lp_user, l.dateline as lp_dateline, l.pid as lp_pid FROM ".X_PREFIX."threads t, ".X_PREFIX."lastposts l WHERE t.tid = '$tid' AND t.tid = l.tid");

            if ($db->num_rows($query) == 0)
            {
                error($lang['textnothread'], false);
            }

            $info = $db->fetch_array($query);
            $db->free_result($query);

            // Move the thread
            $db->query("UPDATE ".X_PREFIX."threads SET fid = '$moveto' WHERE tid = '$tid' AND fid = '$fid'");
            $db->query("UPDATE ".X_PREFIX."posts SET fid = '$moveto' WHERE tid = '$tid' AND fid = '$fid'");

            // Leave a redirect?
            if ($type == 'redirect')
            {
                // Create a new thread for the redirect in the OLD forum
                $db->query("INSERT INTO ".X_PREFIX."threads (tid, fid, subject, icon, views, replies, author, closed, topped) VALUES('', '$info[fid]', '$info[subject]', '', '-', '-', '$info[author]', 'moved|$info[tid]', '$info[topped]')");
                $ntid = $db->insert_id();
                $db->query("INSERT INTO ".X_PREFIX."posts (fid, tid, author, message, subject) VALUES ('$info[fid]', '$ntid', '$info[author]', '$info[tid]', '$info[subject]')");
                $db->query("INSERT INTO ".X_PREFIX."lastposts (tid, uid, username, dateline, pid) SELECT '$ntid', uid, username, dateline, pid FROM ".X_PREFIX."lastposts WHERE tid = '$info[tid]'");

            }
        }
        else
        {
            error($lang['errormovingthreads'], false);
        }

        if (isset($forums['type']) && isset($forums['type']) == 'sub')
        {
            updateforumcount($fup['fid']);
        }
        updateforumcount($fid);

        updateforumcount($moveto);
        updatethreadcount($tid);

        $f = "$fid -> $moveto";

        $query = $db->query("SELECT subject FROM ".X_PREFIX."threads WHERE tid = '$tid'");
        $subject = $db->result($query, 0);
        $db->free_result($query);

        $this->log($self['username'], $action . ': ' . $subject, $f, $tid);
        message($lang['movethreadmsg'], false, '', '', 'viewforum.php?fid='.$fid, true, false, true);
    }

    function viewMove()
    {
        global $lang, $fid, $tid, $self, $THEME, $oToken, $shadow, $cheHTML;
        $forumselect = forumList('moveto', false, false);
        eval('echo stripslashes("'.template('topicadmin_move').'");');
    }

    function doTop($topped)
    {
        global $db, $self, $fid, $tid, $lang;

        if ($topped == 1)
        {
            $db->query("UPDATE ".X_PREFIX."threads SET topped = '0' WHERE tid = '$tid' AND fid = '$fid'");
        }
        else if ($topped == 0)
        {
            $db->query("UPDATE ".X_PREFIX."threads SET topped = '1' WHERE tid = '$tid' AND fid = '$fid'");
        }
            $act =($topped ? 'untop' : 'top');
            $this->log($self['username'], $act, $fid, $tid);

        message($lang['topthreadmsg'], false, '', '', 'viewforum.php?fid='.$fid, true, false, true);
    }

    function viewTop($topped)
    {
        global $lang, $fid, $tid, $self, $THEME, $oToken, $shadow;

        if ($topped == 1)
        {
            $lang['texttopthread'] = $lang['textuntopthread'];
        }
        eval('echo stripslashes("'.template('topicadmin_topuntop').'");');
    }

    function doBump()
    {
        global $db, $self, $fid, $tid, $onlinetime, $action, $lang;

        $pid = $db->result($db->query("SELECT pid FROM ".X_PREFIX."posts WHERE tid='$tid' ORDER BY pid DESC LIMIT 1"), 0);

        $db->query("UPDATE ".X_PREFIX."lastposts SET dateline = '$onlinetime' WHERE tid = '$tid'");
        $db->query("UPDATE ".X_PREFIX."forums SET lastpost='$tid' WHERE fid = '$fid'");
        $this->log($self['username'], $action, $fid, $tid);
        message($lang['bumpthreadmsg'], false, '', '', 'viewforum.php?fid='.$fid, true, false, true);
    }

    function viewBump()
    {
        global $lang, $fid, $tid, $self, $THEME, $oToken, $shadow;
        eval('echo stripslashes("'.template('topicadmin_bump').'");');
    }

    function doEmpty()
    {
        global $db, $tid, $fid, $self, $action, $lang;

        $query = $db->query("SELECT pid FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY pid ASC LIMIT 1");

        if ($db->num_rows($query) == 0)
        {
            error($lang['textnothread'], false);
        }

        $pid = $db->result($query, 0);
        $db->free_result($query);
        $db->query("DELETE FROM ".X_PREFIX."posts WHERE tid = '$tid' AND pid != '$pid'");
        updatethreadcount($tid);
        updateforumcount($fid);

        $this->log($self['username'], $action, $fid, $tid);
        message($lang['emptythreadmsg'], false, '', '', 'viewtopic.php?tid='.$tid, true, false, true);
    }

    function viewEmpty()
    {
        global $lang, $fid, $tid, $self, $THEME, $oToken, $shadow;
        eval('echo stripslashes("'.template('topicadmin_empty').'");');
    }

    function doSplit()
    {
        global $db, $lang, $tid, $fid, $self, $action, $onlinetime;

        $subject = formVar('subject');

        if (empty($subject))
        {
            error($lang['textnosubject'], false);
        }

        $subject = addslashes($subject);
        $q1 = $db->query("SELECT author, subject FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY dateline LIMIT 0,1");
        if ($db->num_rows($q1) == 0)
        {
            error($lang['textnothread'], false);
        }
        $db->free_result($q1);

        $oldmove = getFormArrayInt('move', false);
        $newmove = implode(',', $oldmove);

        if (!empty($subject))
        {
            $db->query("INSERT INTO ".X_PREFIX."threads (tid, fid, subject, icon, views, replies, author, closed, topped) VALUES ('', '$fid', '$subject', '', '0', '0', '".$self['username']."', '', '')");
            $newtid = $db->insert_id();
            $db->query("INSERT INTO ".X_PREFIX."lastposts (tid, uid, username, dateline, pid) VALUES ('$newtid', '-', '-', '-', '-')");

        }

        if (isset($newmove))
        {
            $db->query("UPDATE ".X_PREFIX."posts SET tid = '$newtid', subject = '$subject' WHERE pid IN ($newmove)");
            $db->query("UPDATE ".X_PREFIX."attachments SET tid = '$newtid' WHERE pid IN ($newmove)");
            $db->query("UPDATE ".X_PREFIX."threads SET replies = replies+".count($oldmove)." WHERE tid = '$newtid'");
            $db->query("UPDATE ".X_PREFIX."threads SET replies = replies-".count($oldmove)." WHERE tid = '$tid'");
        }

        $q3 = $db->query("SELECT author FROM ".X_PREFIX."posts WHERE tid = '$newtid' ORDER BY dateline ASC LIMIT 0,1");
        $firstauthor = $db->result($q3, 0);
        $db->free_result($q3);

        $q4 = $db->query("SELECT author, dateline, pid FROM ".X_PREFIX."posts WHERE tid = '$newtid' ORDER BY dateline DESC LIMIT 0,1");
        $lastpost = $db->fetch_array($q4);
        $db->free_result($q4);

        $db->query("UPDATE ".X_PREFIX."threads SET author = '$firstauthor', replies = replies-1 WHERE tid = '$newtid'");
        $lastpost_uid = $db->result($db->query("SELECT DISTINCT uid FROM ".X_PREFIX."members WHERE username = '$lastpost[author]'"),0);
        $db->query("UPDATE ".X_PREFIX."lastposts SET uid = '$lastpost_uid', username = '$lastpost[author]', dateline = '$lastpost[dateline]', pid = '$lastpost[pid]' WHERE tid = '$newtid'");

        $q5 = $db->query("SELECT author FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY dateline ASC LIMIT 0,1");
        $firstauthor = $db->result($q5, 0);
        $db->free_result($q5);

        $q6 = $db->query("SELECT author, dateline, pid FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY dateline DESC LIMIT 0,1");
        $lastpost = $db->fetch_array($q6);
        $db->free_result($q6);

        $db->query("UPDATE ".X_PREFIX."threads SET author = '$firstauthor' WHERE tid = '$tid'");
        $lastpost_uid = $db->result($db->query("SELECT DISTINCT uid FROM ".X_PREFIX."members WHERE username = '$lastpost[author]'"),0);
        $db->query("UPDATE ".X_PREFIX."lastposts SET uid = '$lastpost_uid', username = '$lastpost[author]', dateline = '$lastpost[dateline]', pid = '$lastpost[pid]' WHERE tid = '$tid'");

        $this->log($self['username'], $action, $fid, "$tid, $newtid");
        message($lang['splitthreadmsg'], false, '', '', 'viewforum.php?fid='.$fid, true, false, true);
    }

    function viewSplit()
    {
        global $db, $lang, $fid, $tid, $self, $THEME, $oToken, $shadow;

        $qr = $db->query("SELECT replies FROM ".X_PREFIX."threads WHERE tid = '$tid'");
        if ($db->num_rows($qr) == 0)
        {
            error($lang['textnothread'], false);
        }
        $replies = $db->result($qr, 0);
        $db->free_result($qr);

        if ($replies == 0)
        {
            error($lang['cantsplit'], false);
        }

        $posts = '';
        $qp = $db->query("SELECT * FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY dateline");
        while ($post = $db->fetch_array($qp))
        {
            $bbcodeoff = $post['bbcodeoff'];
            $smileyoff = $post['smileyoff'];
            $post['message'] = addslashes($post['message']);
            $post['message'] = postify($post['message'], $smileyoff, $bbcodeoff);
            eval('$posts .= "'.template('topicadmin_split_row').'";');
        }
        $db->free_result($qp);
        eval('echo stripslashes("'.template('topicadmin_split').'");');
    }

    function doMerge()
    {
        global $db, $lang, $tid, $self, $action, $fid;

        $othertid = getRequestInt('othertid');

        if ($tid == $othertid)
        {
            error($lang['cannotmergesamethread']);
        }

        if ($tid == 0 || $othertid == 0)
        {
            error($lang['mergenothread']);
        }

        $queryadd1 = $db->query("SELECT replies, fid FROM ".X_PREFIX."threads WHERE tid = '$othertid'");
        $queryadd2 = $db->query("SELECT replies, fid FROM ".X_PREFIX."threads WHERE tid = '$tid'");

        if ($db->num_rows($queryadd1) == 0 || $db->num_rows($queryadd2) == 0)
        {
            error($lang['mergenothread']);
        }

        $replyadd = $db->result($queryadd1, 0, 'replies');
        $otherfid = $db->result($queryadd1, 0, 'fid');
        $replyadd2 = $db->result($queryadd2, 0, 'replies');
        $replyadd++;
        $replyadd = $replyadd + $replyadd2;

        $db->query("UPDATE ".X_PREFIX."posts SET tid = '$tid', fid = '$fid' WHERE tid = '$othertid'");
        $db->query("UPDATE ".X_PREFIX."attachments SET tid = '$tid' WHERE tid = '$othertid'");
        $db->query("DELETE FROM ".X_PREFIX."threads WHERE tid = '$othertid'");
        $db->query("UPDATE ".X_PREFIX."forums SET threads = threads-1 WHERE fid='$otherfid'");

        $query = $db->query("SELECT * FROM ".X_PREFIX."favorites WHERE tid = '$othertid' OR tid = '$tid'");
        if ($db->num_rows($query) == 2)
        {
            $db->free_result($query);
            $db->query("DELETE FROM ".X_PREFIX."favorites WHERE tid = '$othertid'");
        }
        else
        {
            $db->query("UPDATE ".X_PREFIX."favorites SET tid = '$tid' WHERE tid = '$othertid'");
        }

        $query = $db->query("SELECT * FROM ".X_PREFIX."subscriptions WHERE tid = '$othertid' OR tid = '$tid'");
        if ($db->num_rows($query) == 2)
        {
            $db->free_result($query);
            $db->query("DELETE FROM ".X_PREFIX."subscriptions WHERE tid = '$othertid'");
        }
        else
        {
            $db->query("UPDATE ".X_PREFIX."subscriptions SET tid = '$tid' WHERE tid = '$othertid'");
        }

        $query = $db->query("SELECT subject, author, icon FROM ".X_PREFIX."posts WHERE tid = '$tid' OR tid = '$othertid' ORDER BY pid ASC LIMIT 1");
        $thread = $db->fetch_array($query);
        $db->free_result($query);

        $query = $db->query("SELECT author, dateline, pid FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY dateline DESC LIMIT 0,1");
        $lastpost = $db->fetch_array($query);
        $db->free_result($query);

        $db->query("UPDATE ".X_PREFIX."threads SET replies = '$replyadd', subject = '$thread[subject]', icon = '$thread[icon]', author = '$thread[author]' WHERE tid = '$tid'");
        $lastpost_uid = $db->result($db->query("SELECT DISTINCT uid FROM ".X_PREFIX."members WHERE username = '$lastpost[author]'"),0);
        $db->query("UPDATE ".X_PREFIX."lastposts SET uid = '$lastpost_uid', username = '$lastpost[author]', dateline = '$lastpost[dateline]', pid = '$lastpost[pid]' WHERE tid = '$tid'");


        $this->log($self['username'], $action, $fid, "$othertid, $tid");
        message($lang['mergethreadmsg'], false, '', '', 'viewforum.php?fid='.$fid, true, false, true);
    }

    function viewMerge()
    {
        global $lang, $fid, $tid, $self, $THEME, $oToken, $shadow;
        eval('echo stripslashes("'.template('topicadmin_merge').'");');
    }

    function doPrune()
    {
        global $db, $lang, $tid, $self, $action, $fid, $forums, $fup;

        $query = $db->query("SELECT author, pid, message FROM ".X_PREFIX."posts WHERE tid = '$tid'");
        while ($post = $db->fetch_array($query))
        {
            $move = "move$post[pid]";
            $move = getRequestInt($move);
            if (!empty($move))
            {
                $db->query("UPDATE ".X_PREFIX."members SET postnum = postnum-1 WHERE username = '{$post['author']}'");
                $db->query("DELETE FROM ".X_PREFIX."posts WHERE pid = '$move'");
                $db->query("DELETE FROM ".X_PREFIX."attachments WHERE pid = '$move'");
                $db->query("UPDATE ".X_PREFIX."threads SET replies = replies-1 WHERE tid = '$tid'");
            }
        }
        $db->free_result($query);

        $query = $db->query("SELECT author FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY dateline ASC LIMIT 0,1");
        $firstauthor = $db->result($query, 0);
        $db->free_result($query);

        $query = $db->query("SELECT pid, author, dateline FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY dateline DESC LIMIT 0,1");
        $lastpost = $db->fetch_array($query);
        $db->free_result($query);

        $db->query("UPDATE ".X_PREFIX."threads SET author = '$firstauthor' WHERE tid = '$tid'");
        $lastpost_uid = $db->result($db->query("SELECT DISTINCT uid FROM ".X_PREFIX."members WHERE username = '$lastpost[author]'"),0);
        $db->query("UPDATE ".X_PREFIX."lastposts SET uid = '$lastpost_uid', username = '$lastpost[author]', dateline = '$lastpost[dateline]', pid = '$lastpost[pid]' WHERE tid = '$tid'");


        if (isset($forums['type']) && isset($forums['type']) == 'sub')
        {
            $query = $db->query("SELECT fup FROM ".X_PREFIX."forums WHERE fid = '$fid' LIMIT 1");
            $fup = $db->fetch_array($query);
            $db->free_result($query);
            updateforumcount($fup['fup']);
        }
        updateforumcount($fid);

        $this->log($self['username'], $action, $fid, $tid);
        message($lang['complete_threadprune'], false, '', '', 'viewforum.php?fid='.$fid, true, false, true);
    }

    function viewPrune()
    {
        global $db, $lang, $fid, $tid, $self, $THEME, $oToken, $shadow;

        $query = $db->query("SELECT replies FROM ".X_PREFIX."threads WHERE tid = '$tid'");
        if ($db->num_rows($query) == 0)
        {
            $db->free_result($query);
            error($lang['textnothread'], false);
        }

        $replies = $db->result($query, 0);
        $db->free_result($query);

        if ($replies == 0)
        {
            error($lang['cantthreadprune'], false);
        }

        $posts = '';
        $query = $db->query("SELECT * FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY dateline");
        while ($post = $db->fetch_array($query))
        {
            $bbcodeoff = $post['bbcodeoff'];
            $smileyoff = $post['smileyoff'];
            $post['message'] = addslashes($post['message']);
            $post['message'] = postify($post['message'], $smileyoff, $bbcodeoff);
            eval('$posts .= "'.template('topicadmin_threadprune_row').'";');
        }
        $db->free_result($query);
        eval('echo stripslashes("'.template('topicadmin_threadprune').'");');
    }

    function doCopy()
    {
        global $db, $lang, $tid, $self, $action, $fid, $forums, $fup;

        $newfid = getRequestInt('newfid');

        if ($newfid < 1)
        {
            error($lang['errormovingthreads'], false);
        }
        $this->statuscheck($newfid);

        $query = $db->query("SELECT * FROM ".X_PREFIX."threads WHERE tid = '$tid'");
        if ($db->num_rows($query) == 0)
        {
            error($lang['textnothread'], false);
        }

        $thread = $db->fetch_array($query);
        foreach ($thread as $key => $val)
        {
            switch ($key)
            {
                case 'tid':
                    $tid = $thread['tid'];
                    unset($thread[$key]);
                    break;
                case 'fid':
                    $thread['fid'] = $newfid;
                    break;
                default:
                    break;
            }
        }

        $cols = array();
        $vals = array();

        reset($thread);
        foreach ($thread as $key => $val)
        {
            if (trim($key) == '')
            {
                continue;
            }

           if ($key == 'subject')
            {
                $val = '[Copy] '.$val;
            }

            $cols[] = $key;
            $vals[] = addslashes($val);
        }

        reset($thread);
        $columns = implode(', ', $cols);
        $values = "'".implode("', '", $vals)."'";

        $db->query("INSERT INTO ".X_PREFIX."threads ($columns) VALUES($values)") or die($db->error());
        $newtid = $db->insert_id();
        $db->query("INSERT INTO ".X_PREFIX."lastposts (tid, uid, username, dateline, pid) SELECT '$ntid', uid, username, dateline, pid FROM ".X_PREFIX."lastposts WHERE tid = '$tid'");


        $cols = array();
        $vals = array();

        $query = $db->query("SELECT * FROM ".X_PREFIX."posts WHERE tid = '$tid' ORDER BY pid ASC");
        while ($post = $db->fetch_array($query))
        {
            $post['fid'] = $newfid;
            $post['tid'] = $newtid;

            $oldPid = $post['pid'];

            unset($post['pid']);
            reset($post);

            foreach ($post as $key => $val)
            {
                $cols[] = $key;
                // Database won't take it without slashes...
                $vals[] = addslashes($val);
            }

            $columns = implode(', ', $cols);
            $values  = "'".implode("', '", $vals)."'";

            $cols = array();
            $vals = array();

            $db->query("INSERT INTO ".X_PREFIX."posts ($columns) VALUES ($values)") or die($db->error());
            $newpid = $db->insert_id();

            $db->query("INSERT INTO ".X_PREFIX."attachments(`tid`,`pid`,`filename`,`filetype`,`filesize`,`attachment`,`downloads`) SELECT '$newtid','$newpid',`filename`,`filetype`,`filesize`,`attachment`,`downloads` FROM ".X_PREFIX."attachments WHERE pid = '$oldPid'");
        }
        $db->free_result($query);

        $this->log($self['username'], $action, $fid, $tid);
        message($lang['copythreadmsg'], false, '', '', 'viewforum.php?fid='.$fid, true, false, true);
    }

    function viewCopy()
    {
        global $lang, $fid, $tid, $self, $THEME, $oToken, $shadow;

        $forumselect = forumList('newfid', false, false);
        eval('echo stripslashes("'.template('topicadmin_copy').'");');
    }

    function doReport()
    {
        global $db, $lang, $tid, $pid, $action, $fid, $forums, $fup;
        global $onlinetime, $self, $CONFIG;

        $query = $db->query("SELECT count(pid) FROM ".X_PREFIX."posts WHERE tid = '$tid'");
        $postcount = $db->result($query, 0);
        $db->free_result($query);
        
        if ($postcount == 0)
        {
            error($lang['textnothread'], false);
        }

        $mods = array();
        
        $query = $db->query("SELECT username FROM ".X_PREFIX."members WHERE status = 'Super Administrator' OR status = 'Administrator'");
        while ($usr = $db->fetch_array($query))
        {
            $mods[] = $usr['username'];
        }
        $db->free_result($query);
        
        $query = $db->query("SELECT moderator FROM ".X_PREFIX."forums WHERE fid = '$fid'");
        $reports = explode(", ", $db->result($query, 0));
        $db->free_result($query);
        
        $mods = array_unique(array_merge($mods, $reports));

        $sent = 0;
        $time = $db->time($onlinetime);
        foreach ($mods as $key => $mod)
        {
            $mod = trim($mod);
            $q = $db->query("SELECT ppp FROM ".X_PREFIX."members WHERE username = '$mod'");
            if ($db->num_rows($q) == 0)
            {
                continue;
            }
            $page = quickpage($postcount, $db->result($q, 0));
            $posturl = $CONFIG['boardurl']."viewtopic.php?tid=$tid&page=$page#pid$pid";
            $reason = checkInput(formVar('reason'));
            $message = $lang['reportmessage'].' '.$posturl."\n\n".$lang['reason'].' '.$reason;

            $db->query("INSERT INTO ".X_PREFIX."pm (pmid, msgto, msgfrom, type, owner, folder, subject, message, dateline, readstatus, sentstatus, usesig) VALUES ('', '".$db->escape($mod)."', '".$db->escape($self['username'])."', 'incoming', '".$db->escape($mod)."', 'Inbox', '".$lang['reportsubject']."', '".$db->escape($message)."', $time, 'no', 'yes', 'no')");
            $sent++;
        }

        $page = quickpage($postcount, $self['tpp']);
        message($lang['reportmsg'], false, '', '', "viewtopic.php?tid=$tid&page=".$page."#pid$pid", true, false, true);
    }

    function viewReport()
    {
        global $lang, $fid, $pid, $tid, $self, $THEME, $oToken, $shadow;
        eval('echo stripslashes("'.template('topicadmin_report').'");');
    }

    function doVote()
    {
        global $db, $lang, $tid, $action, $fid, $forums, $fup;
        global $onlinetime, $self, $onlineip;

        if (!X_MEMBER)
        {
            error($lang['notloggedin'], false);
        }

        $postopnum = getRequestInt('postopnum');
        if ($postopnum === 0)
        {
            error($lang['pollvotenotselected'], false);
        }

        $query = $db->query("SELECT vote_id FROM ".X_PREFIX."vote_desc WHERE topic_id = '$tid'");
        if ($query === false)
        {
            error($lang['pollvotenotselected'], false);
        }

        $vote_id = $db->fetch_array($query);
        $vote_id = (int) $vote_id['vote_id'];
        $db->free_result($query);

        $vote_result = $db->result($db->query("SELECT COUNT(vote_option_id) FROM ".X_PREFIX."vote_results WHERE vote_id = '$vote_id' AND vote_option_id = '$postopnum'"), 0);
        if ($vote_result != 1)
        {
            error($lang['pollvotenotselected'], false);
        }

        $voted = $db->result($db->query("SELECT COUNT(vote_id) FROM ".X_PREFIX."vote_voters WHERE vote_id = '$vote_id' AND vote_user_id = '$self[uid]'"), 0);
        if ($voted === 1)
        {
            error($lang['alreadyvoted'], false);
        }

        $db->query("INSERT INTO ".X_PREFIX."vote_voters (vote_id, vote_user_id, vote_user_ip) VALUES ('$vote_id', '$self[uid]', '".encode_ip($onlineip)."')");
        $db->query("UPDATE ".X_PREFIX."vote_results SET vote_result = vote_result+1 WHERE vote_id = '$vote_id' AND vote_option_id = '$postopnum'");

        if ($tid > 0)
        {
            message($lang['votemsg'], false, '', '', 'viewtopic.php?tid='.$tid, true, false, true);
        }
        else
        {
            message($lang['votemsg'], false, '', '', 'index.php', true, false, true);
        }
    }

    function viewIP()
    {
        global $lang, $db, $fid, $tid, $self, $THEME, $oToken;

        $pid = getRequestInt('pid');
        if ($pid > 0)
        {
            $query = $db->query("SELECT * FROM ".X_PREFIX."posts WHERE pid = '$pid'");
        }
        else
        {
            $query = $db->query("SELECT * FROM ".X_PREFIX."threads WHERE tid = '$tid'");
        }

        if ($db->num_rows($query) == 0)
        {
            error($lang['textnothread'], false);
        }

        $ipinfo = $db->fetch_array($query);
        $db->free_result($query);
        ?>
        <form method="post" action="./admin/cp_ipban.php">
        <input type="hidden" name="token" value="<?php echo $oToken->get_new_token()?>" />
        <table cellspacing="0" cellpadding="0" border="0" width="60%" align="center">
        <tr>
        <td bgcolor="<?php echo $THEME['bordercolor']?>">
        <table border="0" cellspacing="<?php echo $THEME['borderwidth']?>" cellpadding="<?php echo $THEME['tablespace']?>" width="100%">
        <tr>
        <td class="header" colspan="3"><?php echo $lang['textgetip']?></td>
        </tr>
        <tr bgcolor="<?php echo $THEME['altbg2']?>">
        <td class="tablerow"><?php echo $lang['textyesip']?> <strong><?php echo $ipinfo['useip']?></strong> - <?php echo gethostbyaddr($ipinfo['useip'])?>
        <?php
        if (X_ADMIN)
        {
            $ip = explode('.', $ipinfo['useip']);
            $query = $db->query("SELECT * FROM ".X_PREFIX."banned WHERE(ip1 = '$ip[0]' OR ip1 = '-1') AND(ip2 = '$ip[1]' OR ip2 = '-1') AND(ip3 = '$ip[2]' OR ip3 = '-1') AND(ip4 = '$ip[3]' OR ip4 = '-1')");
            $result = $db->fetch_array($query);
            $db->free_result($query);

            if ($result)
            {
                $buttontext = $lang['textunbanip'];
                for ($i = 1; $i <= 4; ++$i)
                {
                    $j = "ip$i";
                    if ($result[$j] == -1)
                    {
                        $result[$j] = "*";
                        $foundmask = 1;
                    }
                }

                if ($foundmask)
                {
                    $ipmask = "<strong>$result[ip1].$result[ip2].$result[ip3].$result[ip4]</strong>";
                    eval($lang['evalipmask']);
                    $lang['bannedipmask'] = stripslashes($lang['bannedipmask']);
                    echo $lang['bannedipmask'];
                }
                else
                {
                    $lang['textbannedip'] = stripslashes($lang['textbannedip']);
                    echo $lang['textbannedip'];
                }
                echo "<input type=\"hidden\" name=\"delete$result[id]\" value=\"$result[id]\" />";
            }
            else
            {
                $buttontext = $lang['textbanip'];
                for ($i = 1; $i <= 4; ++$i)
                {
                    $j = $i - 1;
                    echo "<input type=\"hidden\" name=\"newip$i\" value=\"$ip[$j]\" />";
                }
            }
            ?>
            </td>
            </tr>
            <tr bgcolor="<?php echo $THEME['altbg1']?>">
            <td class="tablerow">
            <div align="center"><input type="submit" class="submit" name="ipbansubmit" value="<?php echo $buttontext?>" /></div>
            <?php
        }
        echo '</td></tr></table></td></tr></table></form>';
    }

    function doMarkThread()
    {
        global $db, $lang, $tid, $self, $action, $fid, $forums, $fup, $prefix, $closed, $post;

        $newmarkthread = formVar('newmarkthread');

        $query = $db->query("SELECT p.*, t.tid FROM ".X_PREFIX."posts p LEFT JOIN ".X_PREFIX."threads t ON p.tid = t.tid WHERE p.tid = '$tid' ORDER BY dateline LIMIT 0, 1");
        $post = $db->fetch_array($query);
        $db->free_result($query);

        $openprefixes = explode(',', $forums['mt_open']);
        for ($i = 0; $i <count($openprefixes); $i++)
        {
            $openprefixes[$i] = trim($openprefixes[$i]);
        }

        $closeprefixes = explode(',', $forums['mt_close']);
        for ($i = 0; $i <count($closeprefixes); $i++)
        {
            $closeprefixes[$i] = trim($closeprefixes[$i]);
        }

        $prefixes = array_merge($openprefixes, $closeprefixes);
        natcasesort($prefixes);

        foreach ($prefixes as $prefix)
        {
            $prefix = trim($prefix);
            $post['subject'] = str_replace('['.$prefix.']', '', $post['subject']);
        }

        if (in_array($newmarkthread, $closeprefixes) !== false)
        {
            $closed = 'yes';
        }
        else
        {
            $closed = '';
        }

        if ($newmarkthread == 'none')
        {
            $newmarkthread = '';
        }
        else
        {
            $newmarkthread = '['.$newmarkthread.']';
        }

        $subject = addslashes($post['subject']);
        $db->query("UPDATE ".X_PREFIX."posts SET subject = '$newmarkthread $subject' WHERE pid = '$post[pid]'");
        $db->query("UPDATE ".X_PREFIX."threads SET closed = '$closed', subject = '$newmarkthread $subject' WHERE tid = '$tid'");

        $this->log($self['username'], $action, $fid, $tid);
        message($lang['markthreadsuccess'], false, '', '', 'viewtopic.php?tid='.$tid, true, false, true);
    }

    function viewMarkThread()
    {
        global $db, $self, $lang, $forums, $thread, $fid, $pid, $tid, $THEME, $oToken, $shadow, $selHTML;

        $openprefixes = explode(',', $forums['mt_open']);
        for ($i = 0; $i <count($openprefixes); $i++)
        {
            $openprefixes[$i] = trim($openprefixes[$i]);
        }

        $closeprefixes = explode(',', $forums['mt_close']);
        for ($i = 0; $i <count($closeprefixes); $i++)
        {
            $closeprefixes[$i] = trim($closeprefixes[$i]);
        }

        $prefixes = array_merge($openprefixes, $closeprefixes);
        natcasesort($prefixes);

        $markthread_select = array();
        $markthread_select[] = '<select name="newmarkthread">';
        $markthread_select[] = '<option value="none">'.$lang['textnone'].'</option>';
        foreach ($prefixes as $prefix)
        {
            $prefix = trim($prefix);
            if (strpos($thread['subject'], '['.$prefix.']') !== false)
            {
                $markthread_select[] = '<option value="'.$prefix.'" $selHTML>'.$prefix.'</option>';
            }
            else
            {
                $markthread_select[] = '<option value="'.$prefix.'">'.$prefix.'</option>';
            }
        }
        $markthread_select[] = '</select>';
        $markthread_select = implode("\n", $markthread_select);
        eval('echo stripslashes("'.template('topicadmin_markthread').'");');
    }
}
?>