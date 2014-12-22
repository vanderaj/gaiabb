<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2015 The GaiaBB Group
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

class Forum
{

    function Forum()
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

    function fixLastPost()
    {
        global $db;
        
        if (! ((bool) ini_get('safe_mode'))) {
            set_time_limit(0);
        }
        
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
    }

    function updateLPFUP($fup = 0, $fid = 0)
    {
        global $db;
        
        if (! empty($fid) && ! empty($fup)) {
            $posts = $db->query("SELECT tid FROM " . X_PREFIX . "posts WHERE fid = '$fid' ORDER BY pid DESC LIMIT 0,1");
            $lp2 = $db->fetch_array($posts);
            $lp = $lp2['tid'];
            
            $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$lp' WHERE fid = '$fup'");
            
            return TRUE;
        }
        return FALSE;
    }

    function fixThreadPostCount()
    {
        global $db;
        
        $query = $db->query("SELECT fid FROM " . X_PREFIX . "forums WHERE type = 'forum'");
        while (($forum = $db->fetch_array($query)) != false) {
            $threadnum = $postnum = $sub_threadnum = $sub_postnum = 0;
            $squery = $stquery = $spquery = $ftquery = $fpquery = '';
            $squery = $db->query("SELECT fid FROM " . X_PREFIX . "forums WHERE fup = '$forum[fid]' AND type = 'sub'");
            while (($sub = $db->fetch_array($squery)) != false) {
                $stquery = $db->query("SELECT COUNT(tid) FROM " . X_PREFIX . "threads WHERE fid = '$sub[fid]'");
                $sub_threadnum = $db->result($stquery, 0);
                $db->free_result($stquery);
                $spquery = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE fid = '$sub[fid]'");
                $sub_postnum = $db->result($spquery, 0);
                $db->free_result($spquery);
                $db->query("UPDATE " . X_PREFIX . "forums SET threads = '$sub_threadnum', posts = '$sub_postnum' WHERE fid = '$sub[fid]'");
                $threadnum += $sub_threadnum;
                $postnum += $sub_postnum;
            }
            $db->free_result($squery);
            $ftquery = $db->query("SELECT COUNT(tid) FROM " . X_PREFIX . "threads WHERE fid = '$forum[fid]'");
            $threadnum += $db->result($ftquery, 0);
            $db->free_result($ftquery);
            $fpquery = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE fid = '$forum[fid]'");
            $postnum += $db->result($fpquery, 0);
            $db->free_result($fpquery);
            $db->query("UPDATE " . X_PREFIX . "forums SET threads = '$threadnum', posts = '$postnum' WHERE fid = '$forum[fid]'");
        }
    }
}
?>