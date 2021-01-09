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

if (!X_STAFF) {
    error($lang['notpermitted'], false);
}

require_once ROOT . 'include/topicadmin.inc.php';

$pid = getInt('pid');
$tid = getInt('tid');
$fid = getInt('fid');
$action = getVar('action');
$mass_mod = formVar('mass_mod');
$cmassmod = getFormArrayInt('cmassmod');
$tids = formVar('tids');
$othertid = formInt('othertid');
$newfid = getRequestInt('newfid');
$moveto = formInt('moveto');

if ($mass_mod) {
    $action = $mass_mod;
}

$threads = array();

if ($cmassmod) {
    foreach ($cmassmod as $key => $val) {
        $threads[] = $val;
    }
    $tids = (count($threads) > 1) ? implode(', ', $threads) : (int) $threads[0][0];
}

$mod = new GaiaBB\Mod();
$mod->statuscheck($fid);

function doEmptySubmit($fid, $tids)
{
    global $db, $mod, $self;

    $leavealone = array();
    $tids = explode(', ', $tids);

    foreach ($tids as $post) {
        $query = $db->query("SELECT pid, author FROM " . X_PREFIX . "posts WHERE tid = '$post' ORDER BY pid ASC LIMIT 1");
        $posts = $db->fetchArray($query);
        $db->freeResult($query);
        $leavealone[] = $posts['pid'];
    }

    $tids = implode(', ', $tids);
    $pids = implode(', ', $leavealone);

    $db->query("DELETE FROM " . X_PREFIX . "posts WHERE tid IN ($tids) AND pid NOT IN ($pids)");

    $tids = explode(', ', $tids);
    for ($i = 0; $i < count($tids); $i++) {
        updatethreadcount($tids[$i]);
    }

    updateforumcount($fid);

    foreach ($tids as $tid) {
        $mod->log($self['username'], 'empty', $fid, $tid);
    }
}

function doDeleteSubmit($fid, $tids)
{
    global $db, $action, $self, $mod;

    $member = array();
    $query = $db->query("SELECT * FROM " . X_PREFIX . "posts WHERE tid IN ($tids)");
    while (($result = $db->fetchArray($query)) != false) {
        $db->query("UPDATE " . X_PREFIX . "members SET postnum = postnum-1 WHERE username = '$result[author]'");
    }
    $db->freeResult($query);

    $mod_tids = explode(', ', $tids);
    foreach ($mod_tids as $tid) {
        $query = $db->query("SELECT subject FROM " . X_PREFIX . "threads WHERE tid = '$tid'");
        $subject = $db->result($query, 0);
        $db->freeResult($query);
        $mod->log($self['username'], $action . ": " . $subject, $fid, $tid);
    }

    $db->query("DELETE FROM " . X_PREFIX . "threads WHERE tid IN ($tids)");
    $db->query("DELETE FROM " . X_PREFIX . "lastposts WHERE tid IN ($tids)");
    $db->query("DELETE FROM " . X_PREFIX . "posts WHERE tid IN ($tids)");
    $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE tid IN ($tids)");
    $db->query("DELETE FROM " . X_PREFIX . "favorites WHERE tid IN ($tids)");
    $db->query("DELETE FROM " . X_PREFIX . "subscriptions WHERE tid IN ($tids)");

    foreach ($mod_tids as $tid) {
        $db->query("DELETE FROM " . X_PREFIX . "threads WHERE closed='moved|$tid'");
    }
}

function doTopSubmit($fid, $tids)
{
    global $db, $self, $mod;

    $top = array();
    $untop = array();
    $query = $db->query("SELECT tid, topped FROM " . X_PREFIX . "threads WHERE fid = '$fid' AND tid IN ($tids)");
    while (($thread = $db->fetchArray($query)) != false) {
        if ($thread['topped'] == 1) {
            $untop[] = $thread['tid'];
            $act = 'untop';
        } else {
            $top[] = $thread['tid'];
            $act = 'top';
        }
        $mod->log($self['username'], $act, $fid, $thread['tid']);
    }

    if (count($untop) != 0) {
        $untop = implode(', ', $untop);
        $db->query("UPDATE " . X_PREFIX . "threads SET topped = '0' WHERE tid IN ($untop)");
    }

    if (count($top) != 0) {
        $top = implode(', ', $top);
        $db->query("UPDATE " . X_PREFIX . "threads SET topped = '1' WHERE tid IN ($top)");
    }
}

function doCloseSubmit($fid, $tids)
{
    global $db, $mod, $self;

    $close = array();
    $open = array();

    $query = $db->query("SELECT tid, closed FROM " . X_PREFIX . "threads WHERE fid = '$fid' AND tid IN ($tids)");
    while (($thread = $db->fetchArray($query)) != false) {
        if ($thread['closed'] == 'yes') {
            $open[] = $thread['tid'];
            $act = 'open';
        } else {
            $close[] = $thread['tid'];
            $act = 'close';
        }
        $mod->log($self['username'], $act, $fid, $thread['tid']);
    }

    if (count($open) != 0) {
        $open = implode(', ', $open);
        $db->query("UPDATE " . X_PREFIX . "threads SET closed = '' WHERE tid IN ($open)");
    }

    if (count($close) != 0) {
        $close = implode(', ', $close);
        $db->query("UPDATE " . X_PREFIX . "threads SET closed = 'yes' WHERE tid IN ($close)");
    }
}

switch ($action) {
    case 'close':
        if (noSubmit('closesubmit')) {
            if ($threads === false) {
                error($lang['massmod_notids'], false);
            }
            eval('echo stripslashes("' . template('massmod_openclose') . '");');
        }

        if (onSubmit('closesubmit')) {
            doCloseSubmit($fid, $tids);
        }
        break;
    case 'top':
        if (noSubmit('topsubmit')) {
            if ($threads === false) {
                error($lang['massmod_notids'], false);
            }
            eval('echo stripslashes("' . template('massmod_topuntop') . '");');
        }

        if (onSubmit('topsubmit')) {
            doTopSubmit($fid, $tids);
        }
        break;
    case 'bump':
        if (noSubmit('bumpsubmit')) {
            if ($threads === false) {
                error($lang['massmod_notids'], false);
            }
            eval('echo stripslashes("' . template('massmod_bump') . '");');
        }

        if (onSubmit('bumpsubmit')) {
            $tids = explode(', ', $tids);
            foreach ($tids as $tid) {
                $db->query("UPDATE " . X_PREFIX . "lastposts SET dateline = '$onlinetime' WHERE tid = '$tid'");
                $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$tid' WHERE fid = '$fid'");

                $mod->log($self['username'], $action, $fid, $tid);
            }
        }
        break;
    case 'copy':
        if (noSubmit('copysubmit')) {
            if ($threads === false) {
                error($lang['massmod_notids'], false);
            }
            $forumselect = forumList('newfid', false, false);
            eval('echo stripslashes("' . template('massmod_copy') . '");');
        }

        if (onSubmit('copysubmit')) {
            $query = $db->query("SELECT * FROM " . X_PREFIX . "threads WHERE tid IN ($tids)");
            while (($thread = $db->fetchArray($query)) != false) {
                foreach ($thread as $key => $val) {
                    switch ($key) {
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
                reset($thread);

                $cols = array();
                $vals = array();
                foreach ($thread as $key => $val) {
                    if (trim($key) == '') {
                        continue;
                    }

                    if ($key == 'subject') {
                        $val = '[Copy] ' . $val;
                    }

                    $cols[] = $key;
                    $vals[] = addslashes($val);
                }
                reset($thread);
                $columns = implode(', ', $cols);
                $values = "'" . implode("', '", $vals) . "'";

                $db->query("INSERT INTO " . X_PREFIX . "threads ($columns) VALUES ($values)");
                $newtid = $db->insertId();
                $db->query("INSERT INTO " . X_PREFIX . "lastposts (tid, uid, username, dateline, pid) SELECT '$newtid', uid, username, dateline, pid FROM " . X_PREFIX . "lastposts where tid = '$tid'");

                $cols = array();
                $vals = array();

                $query2 = $db->query("SELECT * FROM " . X_PREFIX . "posts WHERE tid = '$tid' ORDER BY pid ASC");
                while (($post = $db->fetchArray($query2)) != false) {
                    $post['fid'] = $newfid;
                    $post['tid'] = $newtid;

                    $oldPid = $post['pid'];

                    $post['pid'] = '';
                    reset($post);

                    foreach ($post as $key => $val) {
                        $cols[] = $key;
                        $vals[] = addslashes($val);
                    }

                    $columns = implode(', ', $cols);
                    $values = "'" . implode("', '", $vals) . "'";

                    $cols = array();
                    $vals = array();

                    $db->query("INSERT INTO " . X_PREFIX . "posts ($columns) VALUES ($values)");
                    $newpid = $db->insertId();
                    $db->query("INSERT INTO " . X_PREFIX . "attachments (`tid`,`pid`,`filename`,`filetype`,`filesize`,`attachment`,`downloads`) SELECT '$newtid','$newpid',`filename`,`filetype`,`filesize`,`attachment`,`downloads` FROM " . X_PREFIX . "attachments WHERE pid = '$oldPid'");
                }
                $db->freeResult($query2);
            }
            $db->freeResult($query);

            updateforumcount($newfid);

            $tids2 = explode(', ', $tids);
            foreach ($tids2 as $tid) {
                $mod->log($self['username'], $action, $fid, $tid);
            }
        }
        break;
    case 'delete':
        if (noSubmit('deletesubmit')) {
            if ($threads === false) {
                error($lang['massmod_notids'], false);
            }
            eval('echo stripslashes("' . template('massmod_delete') . '");');
        }

        if (onSubmit('deletesubmit')) {
            doDeleteSubmit($fid, $tids);
            updateforumcount($fid);
        }
        break;
    case 'empty':
        if (noSubmit('emptysubmit')) {
            if ($threads === false) {
                error($lang['massmod_notids'], false);
            }
            eval('echo stripslashes("' . template('massmod_empty') . '");');
        }

        if (onSubmit('emptysubmit')) {
            doEmptySubmit($fid, $tids);
        }
        break;
    case 'move':
        if (noSubmit('movesubmit')) {
            if ($threads === false) {
                error($lang['massmod_notids'], false);
            }
            $forumselect = forumList('moveto', false, false);
            eval('echo stripslashes("' . template('massmod_move') . '");');
        }

        if (onSubmit('movesubmit')) {
            $type = formVar('type');
            if ($type == 'redirect') {
                $query = $db->query("SELECT * FROM " . X_PREFIX . "threads WHERE tid IN ($tids)");
                while (($info = $db->fetchArray($query)) != false) {
                    $db->query("INSERT INTO " . X_PREFIX . "threads (tid, fid, subject, icon, views, replies, author, closed, topped) VALUES('', '$info[fid]', '$info[subject]', '', '-', '-', '$info[author]', 'moved|$info[tid]', '$info[topped]')");
                    $ntid = $db->insertId();
                    $db->query("INSERT INTO " . X_PREFIX . "posts (fid, tid, author, message, subject) VALUES ('$info[fid]', '$ntid', '$info[author]', '$info[tid]', '$info[subject]')");
                    $db->query("INSERT INTO " . X_PREFIX . "lastposts (tid, uid, username, dateline, pid) SELECT '$ntid', uid, username, dateline, pid FROM " . X_PREFIX . "lastposts WHERE tid = '$info[tid]'");
                }
            }

            $db->query("UPDATE " . X_PREFIX . "threads SET fid = '$moveto' WHERE tid IN ($tids)");
            $db->query("UPDATE " . X_PREFIX . "posts SET fid = '$moveto' WHERE tid IN ($tids)");

            updateforumcount($fid);
            updateforumcount($moveto);

            $tids = explode(', ', $tids);
            foreach ($tids as $tid) {
                $query = $db->query("SELECT subject FROM " . X_PREFIX . "threads WHERE tid = '$tid'");
                $subject = $db->fetchArray($query);
                $db->freeResult($query);

                $f = "$fid -> $moveto";
                $mod->log($self['username'], $action . ': ' . $subject['subject'], $f, $tid);
            }
        }
        break;
    case 'merge':
        if (noSubmit('mergesubmit')) {
            if ($threads === false) {
                error($lang['massmod_notids'], false);
            }
            eval('echo stripslashes("' . template('massmod_merge') . '");');
        }

        if (onSubmit('mergesubmit')) {
            $tids2 = stripslashes($tids);
            $tids = explode(', ', $tids);

            if (in_array($othertid, $tids)) {
                error($lang['cannotmergesamethread'], false);
            }

            if ($othertid == 0) {
                error($lang['mergenothread'], false);
            }

            foreach ($tids as $tid) {
                $query = $db->query("SELECT replies, fid FROM " . X_PREFIX . "threads WHERE tid = '$tid'");
                if ($db->numRows($query) == 0) {
                    error($lang['mergenothread'], false);
                }
            }
            $db->freeResult($query);

            $query = $db->query("SELECT replies, fid FROM " . X_PREFIX . "threads WHERE tid = '$othertid'");
            if ($db->numRows($query) == 0) {
                error($lang['mergenothread'], false);
            }

            $db->query("UPDATE " . X_PREFIX . "posts SET tid = '$othertid' WHERE tid IN ($tids2)");
            $db->query("UPDATE " . X_PREFIX . "attachments SET tid = '$othertid' WHERE tid IN ($tids2)");
            $db->query("DELETE FROM " . X_PREFIX . "threads WHERE tid IN ($tids2)");
            $db->query("DELETE FROM " . X_PREFIX . "lastposts WHERE tid IN ($tids2)");
            $db->query("UPDATE " . X_PREFIX . "forums SET threads = threads-" . count($tids) . " WHERE fid = '$fid'");
            updatethreadcount($othertid);

            foreach ($tids as $tid) {
                $mod->log($self['username'], $action, $fid, "$othertid, $tid");
            }
        }
        break;
    case 'markthread':
        if (noSubmit('markthreadsubmit')) {
            if ($threads === false) {
                error($lang['massmod_notids'], false);
            }
            $query = $db->query("SELECT mt_open, mt_close FROM " . X_PREFIX . "forums WHERE fid = '$fid'");
            $forums = $db->fetchArray($query);
            $db->freeResult($query);

            $query = $db->query("SELECT * FROM " . X_PREFIX . "threads WHERE tid IN ($tids)");
            $thread = $db->fetchArray($query);
            $db->freeResult($query);

            $openprefixes = explode(',', $forums['mt_open']);
            for ($i = 0; $i < count($openprefixes); $i++) {
                $openprefixes[$i] = trim($openprefixes[$i]);
            }

            $closeprefixes = explode(',', $forums['mt_close']);
            for ($i = 0; $i < count($closeprefixes); $i++) {
                $closeprefixes[$i] = trim($closeprefixes[$i]);
            }

            $prefixes = array_merge($openprefixes, $closeprefixes);
            natcasesort($prefixes);

            $markthread_select = array();
            $markthread_select[] = '<select name="newmarkthread">';
            $markthread_select[] = '<option value="none">' . $lang['textnone'] . '</option>';
            foreach ($prefixes as $prefix) {
                $prefix = trim($prefix);
                if (strpos($thread['subject'], '[' . $prefix . ']') !== false) {
                    $markthread_select[] = '<option value="' . $prefix . '" $selHTML>' . $prefix . '</option>';
                } else {
                    $markthread_select[] = '<option value="' . $prefix . '">' . $prefix . '</option>';
                }
            }
            $markthread_select[] = '</select>';
            $markthread_select = implode("\n", $markthread_select);
            eval('echo stripslashes("' . template('massmod_markthread') . '");');
        }

        if (onSubmit('markthreadsubmit')) {
            $newmarkthread = formVar('newmarkthread');
            $query = $db->query("SELECT mt_open, mt_close FROM " . X_PREFIX . "forums WHERE fid = '$fid'");
            $forums = $db->fetchArray($query);
            $db->freeResult($query);

            $openprefixes = explode(',', $forums['mt_open']);
            for ($i = 0; $i < count($openprefixes); $i++) {
                $openprefixes[$i] = trim($openprefixes[$i]);
            }

            $closeprefixes = explode(',', $forums['mt_close']);
            for ($i = 0; $i < count($closeprefixes); $i++) {
                $closeprefixes[$i] = trim($closeprefixes[$i]);
            }

            $prefixes = array_merge($openprefixes, $closeprefixes);
            natcasesort($prefixes);

            if (in_array($newmarkthread, $closeprefixes) !== false) {
                $closed = 'yes';
            } else {
                $closed = '';
            }

            if ($newmarkthread == 'none') {
                $newmarkthread = '';
            } else {
                $newmarkthread = '[' . $newmarkthread . ']';
            }

            $tids = explode(', ', $tids);
            foreach ($tids as $tid) {
                $query = $db->query("SELECT p.*, t.tid FROM " . X_PREFIX . "posts p LEFT JOIN " . X_PREFIX . "threads t ON p.tid = t.tid WHERE p.tid = '$tid' ORDER BY dateline LIMIT 0, 1");
                $post = $db->fetchArray($query);
                $db->freeResult($query);

                foreach ($prefixes as $prefix) {
                    $prefix = trim($prefix);
                    $post['subject'] = str_replace('[' . $prefix . ']', '', $post['subject']);
                }

                $subject = addslashes($post['subject']);
                $db->query("UPDATE " . X_PREFIX . "posts SET subject = '$newmarkthread $subject' WHERE pid = '$post[pid]'");
                $db->query("UPDATE " . X_PREFIX . "threads SET closed = '$closed', subject = '$newmarkthread $subject' WHERE tid = '$tid'");
            }

            foreach ($tids as $tid) {
                $mod->log($self['username'], $action, $fid, $tid);
            }
        }
        break;
    default:
        break;
}
