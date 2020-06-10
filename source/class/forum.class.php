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
 * Copyright (c) 2001 - 2004 The XMB Development Team
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

class Forum
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

    public function fixLastPost()
    {
        global $db;

        if (!((bool) ini_get('safe_mode'))) {
            set_time_limit(0);
        }

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
    }

    public function updateLPFUP($fup = 0, $fid = 0)
    {
        global $db;

        if (!empty($fid) && !empty($fup)) {
            $posts = $db->query("SELECT tid FROM " . X_PREFIX . "posts WHERE fid = '$fid' ORDER BY pid DESC LIMIT 0,1");
            $lp2 = $db->fetchArray($posts);
            $lp = $lp2['tid'];

            $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$lp' WHERE fid = '$fup'");

            return true;
        }
        return false;
    }

    public function fixThreadPostCount()
    {
        global $db;

        $query = $db->query("SELECT fid FROM " . X_PREFIX . "forums WHERE type = 'forum'");
        while (($forum = $db->fetchArray($query)) != false) {
            $threadnum = $postnum = $sub_threadnum = $sub_postnum = 0;
            $squery = $stquery = $spquery = $ftquery = $fpquery = '';
            $squery = $db->query("SELECT fid FROM " . X_PREFIX . "forums WHERE fup = '$forum[fid]' AND type = 'sub'");
            while (($sub = $db->fetchArray($squery)) != false) {
                $stquery = $db->query("SELECT COUNT(tid) FROM " . X_PREFIX . "threads WHERE fid = '$sub[fid]'");
                $sub_threadnum = $db->result($stquery, 0);
                $db->freeResult($stquery);
                $spquery = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE fid = '$sub[fid]'");
                $sub_postnum = $db->result($spquery, 0);
                $db->freeResult($spquery);
                $db->query("UPDATE " . X_PREFIX . "forums SET threads = '$sub_threadnum', posts = '$sub_postnum' WHERE fid = '$sub[fid]'");
                $threadnum += $sub_threadnum;
                $postnum += $sub_postnum;
            }
            $db->freeResult($squery);
            $ftquery = $db->query("SELECT COUNT(tid) FROM " . X_PREFIX . "threads WHERE fid = '$forum[fid]'");
            $threadnum += $db->result($ftquery, 0);
            $db->freeResult($ftquery);
            $fpquery = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE fid = '$forum[fid]'");
            $postnum += $db->result($fpquery, 0);
            $db->freeResult($fpquery);
            $db->query("UPDATE " . X_PREFIX . "forums SET threads = '$threadnum', posts = '$postnum' WHERE fid = '$forum[fid]'");
        }
    }
}
