<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2021 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2021 The XMB Development Team
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

namespace GaiaBB;

class Thread
{

    public function __construct()
    {
    }

    public function init()
    {
    }

    public function findById()
    {
    }

    public function save()
    {
    }

    public function exists()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }

    public function addPost()
    {
    }

    public function editPost()
    {
    }

    public function deletePost()
    {
    }

    // Find if the thread's first post detail matches the first post
    public function fixFirstPost()
    {
        global $db;

        $query = $db->query("SELECT tid, author, subject FROM " . X_PREFIX . "threads");
        while (($thread = $db->fetchArray($query)) != false) {
            $postQuery = $db->query("SELECT author, subject FROM " . X_PREFIX . "posts WHERE tid='" . $thread['tid'] . "' ORDER BY dateline asc LIMIT 1");
            $post = $db->fetchArray($postQuery);
            if ($post['author'] != $thread['author'] || $post['subject'] != $thread['subject']) {
                $db->query("UPDATE " . X_PREFIX . "threads SET author='" . $db->escape($post['author']) . "', subject='" . $db->escape($post['subject']) . "' WHERE tid='" . $thread['tid'] . "'");
            }
            $db->freeResult($postQuery);
        }
        $db->freeResult($query);
        return true;
    }

    public function fixLastPost()
    {
        global $db;

        // Forums
        $query = $db->query("SELECT fid FROM " . X_PREFIX . "forums ORDER BY fid DESC");
        while (($forums = $db->fetchArray($query)) != false) {
            $posts = $db->query("SELECT tid FROM " . X_PREFIX . "posts WHERE fid = '$forums[fid]' ORDER BY pid DESC LIMIT 0,1");
            $lp2 = $db->fetchArray($posts);
            $lp = $lp2['tid'];

            $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$lp' WHERE fid = '$forums[fid]' LIMIT 1");
        }
        $db->freeResult($query);

        // Threads
        $query = $db->query("SELECT tid FROM " . X_PREFIX . "threads ORDER BY tid DESC");
        while (($threads = $db->fetchArray($query)) != false) {
            $posts = $db->query("SELECT p.author, m.uid, p.dateline, p.pid FROM " . X_PREFIX . "posts p, " . X_PREFIX . "members m WHERE p.author = m.username AND tid = '$threads[tid]' ORDER BY dateline DESC LIMIT 0,1");
            $lp = $db->fetchArray($posts);
            $db->freeResult($posts);
            $db->query("UPDATE " . X_PREFIX . "lastposts SET uid = '$lp[uid]', username = '$lp[author]', dateline = '$lp[dateline]', pid = '$lp[pid]' WHERE tid = '$threads[tid]' LIMIT 1");
        }
        $db->freeResult($query);

        // NULL Threads -> If these exist, they'll cause double forums and such.
        $query = $db->query("DELETE FROM " . X_PREFIX . "lastposts WHERE tid = '0'");
        $db->freeResult($query);
        return true;
    }

    public function prevNextThreads()
    {
        global $db, $tid, $fid, $lang;

        if (!empty($tid)) {
            $retval = array();

            // Previous Thread Link
            $query = $db->query("SELECT t.tid as t_tid FROM " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "threads o ON o.tid = $tid LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid LEFT JOIN " . X_PREFIX . "lastposts x ON x.tid = o.tid WHERE l.dateline < x.dateline AND t.fid = '$fid' ORDER BY l.dateline DESC LIMIT 1");
            if ($db->numRows($query) > 0) {
                $pthread = $db->fetchArray($query);
                $db->freeResult($query);
                $prevthreadid = intval($pthread['t_tid']);
                $retval['previous'] = 'viewtopic.php?tid=' . $prevthreadid;
            }

            // Next Thread Link
            $query = $db->query("SELECT t.tid as t_tid FROM " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "threads o ON o.tid = $tid LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid LEFT JOIN " . X_PREFIX . "lastposts x ON x.tid = o.tid WHERE l.dateline > x.dateline AND t.fid = '$fid' ORDER BY l.dateline ASC LIMIT 1");
            if ($db->numRows($query) != 1) {
                return $retval;
            }
            $nthread = $db->fetchArray($query);
            $db->freeResult($query);
            $nextthreadid = intval($nthread['t_tid']);
            $retval['next'] = 'viewtopic.php?tid=' . $nextthreadid;

            return $retval;
        }
        return false;
    }
}
