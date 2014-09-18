<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2014 The GaiaBB Group
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
if (! defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

class thread
{

    function __construct()
    {}

    function init()
    {}

    function findById()
    {}

    function save()
    {}

    function exists()
    {}

    function update()
    {}

    function delete()
    {}

    function addPost()
    {}

    function editPost()
    {}

    function deletePost()
    {}
    
    // Find if the thread's first post detail matches the first post
    function fixFirstPost()
    {
        global $db;
        
        $query = $db->query("SELECT tid, author, subject FROM " . X_PREFIX . "threads");
        while (($thread = $db->fetch_array($query)) != false) {
            $postQuery = $db->query("SELECT author, subject FROM " . X_PREFIX . "posts WHERE tid='" . $thread['tid'] . "' ORDER BY dateline asc LIMIT 1");
            $post = $db->fetch_array($postQuery);
            if ($post['author'] != $thread['author'] || $post['subject'] != $thread['subject']) {
                $db->query("UPDATE " . X_PREFIX . "threads SET author='" . $db->escape($post['author']) . "', subject='" . $db->escape($post['subject']) . "' WHERE tid='" . $thread['tid'] . "'");
            }
            $db->free_result($postQuery);
        }
        $db->free_result($query);
        return true;
    }

    function fixLastPost()
    {
        global $db;
        
        // Forums
        $query = $db->query("SELECT fid FROM " . X_PREFIX . "forums ORDER BY fid DESC");
        while (($forums = $db->fetch_array($query)) != false) {
            $posts = $db->query("SELECT tid FROM " . X_PREFIX . "posts WHERE fid = '$forums[fid]' ORDER BY pid DESC LIMIT 0,1");
            $lp2 = $db->fetch_array($posts);
            $lp = $lp2['tid'];
            
            $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$lp' WHERE fid = '$forums[fid]' LIMIT 1");
        }
        $db->free_result($query);
        
        // Threads
        $query = $db->query("SELECT tid FROM " . X_PREFIX . "threads ORDER BY tid DESC");
        while (($threads = $db->fetch_array($query)) != false) {
            $posts = $db->query("SELECT p.author, m.uid, p.dateline, p.pid FROM " . X_PREFIX . "posts p, " . X_PREFIX . "members m WHERE p.author = m.username AND tid = '$threads[tid]' ORDER BY dateline DESC LIMIT 0,1");
            $lp = $db->fetch_array($posts);
            $db->free_result($posts);
            $db->query("UPDATE " . X_PREFIX . "lastposts SET uid = '$lp[uid]', username = '$lp[author]', dateline = '$lp[dateline]', pid = '$lp[pid]' WHERE tid = '$threads[tid]' LIMIT 1");
        }
        $db->free_result($query);
        
        // NULL Threads -> If these exist, they'll cause double forums and such.
        $query = $db->query("DELETE FROM " . X_PREFIX . "lastposts WHERE tid = '0'");
        $db->free_result($query);
        return true;
    }

    function PrevNextThreads()
    {
        global $db, $tid, $fid, $lang;
        
        if (! empty($tid)) {
            $retval = array();
            
            // Previous Thread Link
            $query = $db->query("SELECT t.tid as t_tid FROM " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "threads o ON o.tid = $tid LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid LEFT JOIN " . X_PREFIX . "lastposts x ON x.tid = o.tid WHERE l.dateline < x.dateline AND t.fid = '$fid' ORDER BY l.dateline DESC LIMIT 1");
            if ($db->num_rows($query) > 0) {
                $pthread = $db->fetch_array($query);
                $db->free_result($query);
                $prevthreadid = intval($pthread['t_tid']);
                $retval['previous'] = 'viewtopic.php?tid=' . $prevthreadid;
            }
            
            // Next Thread Link
            $query = $db->query("SELECT t.tid as t_tid FROM " . X_PREFIX . "threads t LEFT JOIN " . X_PREFIX . "threads o ON o.tid = $tid LEFT JOIN " . X_PREFIX . "lastposts l ON l.tid = t.tid LEFT JOIN " . X_PREFIX . "lastposts x ON x.tid = o.tid WHERE l.dateline > x.dateline AND t.fid = '$fid' ORDER BY l.dateline ASC LIMIT 1");
            if ($db->num_rows($query) != 1) {
                return $retval;
            }
            $nthread = $db->fetch_array($query);
            $db->free_result($query);
            $nextthreadid = intval($nthread['t_tid']);
            $retval['next'] = 'viewtopic.php?tid=' . $nextthreadid;
            
            return $retval;
        }
        return false;
    }
}
?>