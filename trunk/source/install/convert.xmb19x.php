<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2014 The GaiaBB Group
 * http://www.GaiaBB.com
 *
 * Based off UltimaBB's installer (ajv)
 * Copyright (c) 2004 - 2007 The UltimaBB Group 
 * (defunct)
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

define('X_CONVERT', 'xmb19x');

class xmb19x extends convert
{

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function disableBoards()
    {
        $bboff = $this->fromDbHost->query("UPDATE " . X_PREFIX2 . "settings SET bbstatus='off'");
        if ($bboff === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'settings table at XMB is intact. There was a problem turning your board off.', true);
        }
        
        $bboff2 = $this->toDbHost->query("UPDATE " . X_PREFIX . "settings SET bbstatus='off'");
        if ($bboff2 === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem turning your board off.', true);
        }
        setBar($this->prgbar, 0.25);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function isAuth()
    {
        $admin = $this->fromDbHost->escape($_SESSION['admin_user']);
        $adminpw = md5($_SESSION['admin_pass']);
        
        $query = $this->fromDbHost->query("SELECT username, password, status,email FROM " . X_PREFIX2 . "members WHERE username = '$admin'");
        if ($query === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'members table at XMB is intact. There was a problem querying for your members.', true);
        }
        $user = $this->fromDbHost->fetch_array($query);
        $rows = $this->fromDbHost->num_rows($query);
        $this->fromDbHost->free_result($query);
        
        if ($rows != 1 || $admin != $user['username'] || $adminpw != $user['password'] || $user['status'] != 'Super Administrator') {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'You are not a Super Administrator at your XMB forum. The conversion can not continue.', true);
        }
        
        setBar($this->prgbar, 0.2);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function members()
    {
        $memfromquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "members");
        if ($memfromquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'members table at XMB is intact. There was a problem querying for your members.', true);
        }
        
        $this->toDbHost->query("TRUNCATE " . X_PREFIX . "members");
        
        while (($row = $this->fromDbHost->fetch_array($memfromquery)) != false) {
            $meminsert = $this->toDbHost->query("INSERT INTO `" . X_PREFIX . "members` (
            uid,
            username,
            password,
            regdate,
            postnum,
            email,
            site,
            aim,
            status,
            location,
            bio,
            sig,
            showemail,
            timeoffset,
            icq,
            avatar,
            yahoo,
            customstatus,
            theme,
            bday,
            langfile,
            tpp,
            ppp,
            newsletter,
            regip,
            timeformat,
            msn,
            ban,
            dateformat,
            ignoreu2u,
            lastvisit,
            mood,
            pwdate,
            invisible,
            u2ufolders,
            saveogu2u,
            emailonu2u
          ) VALUES (
            '" . intval($row['uid']) . "',
            '" . $this->toDbHost->escape($row['username']) . "',
            '" . $this->toDbHost->escape($row['password']) . "',
            '" . $this->toDbHost->escape($row['regdate']) . "',
            '" . intval($row['postnum']) . "',
            '" . $this->toDbHost->escape($row['email']) . "',
            '" . $this->toDbHost->escape($row['site']) . "',
            '" . $this->toDbHost->escape($row['aim']) . "',
            '" . $this->toDbHost->escape($row['status']) . "',
            '$row[location]',
            '" . $this->toDbHost->escape($row['bio']) . "',
            '" . $this->toDbHost->escape($row['sig']) . "',
            '" . $this->toDbHost->escape($row['showemail']) . "',
            '" . intval($row['timeoffset']) . "',
            '" . $this->toDbHost->escape($row['icq']) . "',
            '" . $this->toDbHost->escape($row['avatar']) . "',
            '" . $this->toDbHost->escape($row['yahoo']) . "',
            '" . $this->toDbHost->escape($row['customstatus']) . "',
            '1',
            '" . $this->toDbHost->escape($row['bday']) . "',
            '" . $this->toDbHost->escape($row['langfile']) . "',
            '" . $this->toDbHost->escape($row['tpp']) . "',
            '" . $this->toDbHost->escape($row['ppp']) . "',
            '" . $this->toDbHost->escape($row['newsletter']) . "',
            '" . $this->toDbHost->escape($row['regip']) . "',
            '" . $this->toDbHost->escape($row['timeformat']) . "',
            '" . $this->toDbHost->escape($row['msn']) . "',
            '" . $this->toDbHost->escape($row['ban']) . "',
            '" . $this->toDbHost->escape($row['dateformat']) . "',
            '" . $this->toDbHost->escape($row['ignoreu2u']) . "',
            '" . $this->toDbHost->escape($row['lastvisit']) . "',
            '" . $this->toDbHost->escape($row['mood']) . "',
            '" . $this->toDbHost->escape($row['pwdate']) . "',
            '" . intval($row['invisible']) . "',
            '" . $this->toDbHost->escape($row['u2ufolders']) . "',
            '" . $this->toDbHost->escape($row['saveogu2u']) . "',
            '" . $this->toDbHost->escape($row['emailonu2u']) . "'
          )");
            
            if ($meminsert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'members table.', true);
            }
        }
        $this->fromDbHost->free_result($memfromquery);
        setBar($this->prgbar, 0.3);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function posts()
    {
        $postquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "posts");
        if ($postquery == false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'posts table at XMB is intact. There was a problem querying for your members.', true);
        }
        
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "posts");
        while (($row = $this->fromDbHost->fetch_array($postquery)) != false) {
            $posts_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "posts (fid, tid, pid, author, message, subject, dateline, icon, usesig, useip, bbcodeoff, smileyoff) VALUES ('$row[fid]','$row[tid]','$row[pid]','" . $this->toDbHost->escape($row['author']) . "','" . $this->toDbHost->escape($row['message']) . "','" . $this->toDbHost->escape(stripslashes($row['subject'])) . "','$row[dateline]','$row[icon]','$row[usesig]','$row[useip]','$row[bbcodeoff]','$row[smileyoff]')");
            if ($posts_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'posts table.', true);
            }
        }
        $this->fromDbHost->free_result($postquery);
        setBar($this->prgbar, 0.5);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function polls()
    {
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "vote_desc");
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "vote_results");
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "vote_voters");
        
        // Find all the threads which have non-blank polls
        $pollquery = $this->toDbHost->query("SELECT tid, pollopts, subject FROM " . X_PREFIX . "threads WHERE pollopts != ''");
        if ($pollquery) {
            while (($row = $this->toDbHost->fetch_array($pollquery)) != false) {
                // skip over converted rows
                if ($row['pollopts'] === '1') {
                    continue;
                }
                // Grab the thread name for the poll name
                $tid = $row['tid'];
                $subject = $row['subject']; // it's already censored and addslashed
                                            // crack pollopt
                $pollentry = explode('#|#', stripslashes($row['pollopts']));
                $cResults = count($pollentry) - 1;
                $results = array();
                for ($i = 0; $i < $cResults; $i ++) {
                    $answer = array();
                    $answer = explode('||~|~||', $pollentry[$i]);
                    $results[$i][0] = $i + 1;
                    $results[$i][1] = $this->toDbHost->escape(trim($answer[0]));
                    $results[$i][2] = (int) $answer[1];
                }
                // create the poll description
                $vote_desc_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "vote_desc (topic_id, vote_text) VALUES ('$tid', '$subject')");
                if ($vote_desc_insert === false) {
                    setCol($this->prgbar, '#ff0000');
                    print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'vote_desc table.', true);
                }
                $vote_id = $this->toDbHost->insert_id();
                
                foreach ($results as $r => $result) {
                    $vote_results_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "vote_results (vote_id, vote_option_id, vote_option_text, vote_result) VALUES ('$vote_id','$result[0]', '$result[1]', '$result[2]')");
                    
                    if ($vote_results_insert === false) {
                        setCol($this->prgbar, '#ff0000');
                        print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'vote_results table.', true);
                    }
                }
                // make sure the users are noted as voting
                // This is on a best efforts basis - users may have been deleted or renamed
                // and users with spaces in their names cannot work - they will be able to
                // re-vote
                $users = explode(" ", trim($pollentry[$cResults]));
                $eIP = '7f000001'; // The information on which IP they posted from has never been captured
                foreach ($users as $user) {
                    $user = trim($user);
                    if ($user != '') {
                        $q = $this->toDbHost->query("SELECT uid FROM " . X_PREFIX . "members WHERE username = '$user'");
                        if ($q !== false) {
                            $uid = $this->toDbHost->fetch_array($q);
                            $uid = $uid['uid'];
                            $vote_voters_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "vote_voters (vote_id, vote_user_id, vote_user_ip) VALUES ('$vote_id', '$uid', '$eIP')");
                            if ($vote_voters_insert == false) {
                                setCol($this->prgbar, '#ff0000');
                                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'vote_voters table.', true);
                            }
                        } else {
                            setCol($this->prgbar, '#ff0000');
                            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'members table at XMB is intact. There was a problem querying for your members.', true);
                        }
                    }
                }
            }
        }
        $this->toDbHost->free_result($pollquery);
        // Set pollopt to "yep" status
        $thread_update = $this->toDbHost->query("UPDATE " . X_PREFIX . "threads SET pollopts = '1' WHERE pollopts != ''");
        if ($thread_update == false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'threads table at XMB is intact. There was a problem updating your Threads table.', true);
        }
        setBar($this->prgbar, 0.6);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function ranks()
    {
        $ranksquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "ranks");
        if ($ranksquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'ranks table at XMB is intact. There was a problem querying for your ranks.', true);
        }
        
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "ranks");
        while (($row = $this->fromDbHost->fetch_array($ranksquery)) != false) {
            $ranks_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "ranks (title, posts, id, stars, allowavatars, avatarrank) VALUES ('" . $this->toDbHost->escape($row['title']) . "', '$row[posts]', '$row[id]', '$row[stars]', '$row[allowavatars]', '$row[avatarrank]')");
            if ($ranks_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'ranks table.', true);
            }
        }
        $this->fromDbHost->free_result($ranksquery);
        
        // Add back special ranks if they've been removed
        $query = $this->toDbHost->query("SELECT title FROM `" . X_PREFIX . "ranks` WHERE title in ('Moderator', 'Super Moderator', 'Administrator', 'Super Administrator')");
        if ($query) {
            if ($this->toDbHost->num_rows($query) != 4) {
                $this->toDbHost->query("DELETE FROM `" . X_PREFIX . "ranks` WHERE title in ('Moderator', 'Super Moderator', 'Administrator', 'Super Administrator') OR posts = -1");
                $this->toDbHost->query("INSERT INTO `" . X_PREFIX . "ranks` (title, posts, stars, allowavatars) VALUES ('Moderator', -1, 6, 'yes')");
                $this->toDbHost->query("INSERT INTO `" . X_PREFIX . "ranks` (title, posts, stars, allowavatars) VALUES ('Super Moderator', -1, 7, 'yes')");
                $this->toDbHost->query("INSERT INTO `" . X_PREFIX . "ranks` (title, posts, stars, allowavatars) VALUES ('Administrator', -1, 8, 'yes')");
                $this->toDbHost->query("INSERT INTO `" . X_PREFIX . "ranks` (title, posts, stars, allowavatars) VALUES ('Super Administrator', -1, 9, 'yes')");
            }
        }
        
        setBar($this->prgbar, 0.75);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function threads()
    {
        $threadsquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "threads");
        if ($threadsquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'threads table at XMB is intact. There was a problem querying for your threads.', true);
        }
        
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "threads");
        while (($row = $this->fromDbHost->fetch_array($threadsquery)) != false) {
            $threads_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "threads (tid, fid, subject, icon, lastpost, views, replies, author, closed, topped, pollopts) VALUES ('$row[tid]','$row[fid]','" . $this->toDbHost->escape($row['subject']) . "','$row[icon]','$row[lastpost]','$row[views]','$row[replies]','$row[author]','$row[closed]','$row[topped]','" . $this->toDbHost->escape($row['pollopts']) . "')");
            if ($threads_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'threads table.', true);
            }
        }
        $this->fromDbHost->free_result($threadsquery);
        setBar($this->prgbar, 0.45);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function forums()
    {
        $forumsquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "forums");
        if ($forumsquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'forums table at XMB is intact. There was a problem querying for your forums.', true);
        }
        $fupless = true;
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "forums");
        while (($row = $this->fromDbHost->fetch_array($forumsquery)) != false) {
            if ($row['type'] != 'group') {
                $update = false;
                $pp = trim($row['postperm']);
                // In 1.8 postperm was a single value, which we extend to all three thread|reply|edit pperms
                // In < 1.9.3, postperm was thread|reply, which we extend by copying the reply pperm to edit
                if (strlen($pp) > 0) {
                    if (strpos($pp, '|') === false) { // 1.8 -> 1.9.3
                        $update = true;
                        $row['postperm'] = $pp . '|' . $pp . '|' . $pp . '|' . $pp;
                    } else {
                        $pperm = explode('|', $pp);
                        if (count($pperm) == 2) { // 1.9.1 -> 1.9.3
                            $row['postperm'] = $pperm[0] . '|' . $pperm[1] . '|' . $pperm[1] . '|' . $pperm[1];
                            $update = true;
                        }
                    }
                }
            }
            if ($fupless && $row['fup'] != '0') {
                $fupless = false;
            }
            $forums_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "forums (type, fid, name, status, lastpost, moderator, displayorder, private, description, allowsmilies, allowbbcode, userlist, theme, posts, threads, fup, postperm, allowimgcode, attachstatus, pollstatus, password, guestposting) VALUES ('$row[type]','$row[fid]','" . $this->toDbHost->escape($row['name']) . "','$row[status]','$row[lastpost]','" . $this->toDbHost->escape($row['moderator']) . "','$row[displayorder]','$row[private]','" . $this->toDbHost->escape($row['description']) . "','$row[allowsmilies]','$row[allowbbcode]','" . $this->toDbHost->escape($row['userlist']) . "','$row[theme]','$row[posts]','$row[threads]','$row[fup]','$row[postperm]','$row[allowimgcode]','$row[attachstatus]','$row[pollstatus]','$row[password]','$row[guestposting]')");
            if ($forums_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'forums table.', true);
            }
        }
        if ($fupless) {
            $this->toDbHost->query("INSERT INTO `" . X_PREFIX . "forums` (`type`, `name`, `status`, `displayorder`, `private`, `description`, `fup`, `postperm`) VALUES ('group','DEFAULT Category','on',1,'' ,'',0,'')");
            $newgroupfid = $this->toDbHost->insert_id();
            $this->toDbHost->query("UPDATE `" . X_PREFIX . "forums` SET fup='" . $newgroupfid . "' WHERE fup='0' AND type='forum'");
        }
        
        $this->fromDbHost->free_result($forumsquery);
        setBar($this->prgbar, 0.4);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function attachments()
    {
        $attachquery = $this->fromDbHost->query("SELECT aid FROM " . X_PREFIX2 . "attachments");
        if ($attachquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'attachments table at XMB is intact. There was a problem querying for your attachments.', true);
        }
        $rows = $this->fromDbHost->num_rows($attachquery);
        $this->fromDbHost->free_result($attachquery);
        
        if ($rows > 1000) {
            $secs = $rows / 100; // Rough speed guess
            print_error('Attachment Conversion', 'Converting ' . $rows . ' attachments will take approximately ' . $secs . ' seconds.', false);
        }
        
        // Clear out the new table
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "attachments");
        
        // This query processes approximately 100 images per second
        $this->toDbHost->query("INSERT INTO " . X_PREFIX . "attachments (aid, tid, pid, filename, filetype, filesize, attachment, downloads) SELECT aid, tid, pid, filename, filetype, filesize, attachment, downloads FROM " . X_PREFIX2 . "attachments");
        
        if ($rows > 1000) {
            $secs = $rows / 100; // Rough speed guess
            print_error('Attachment Conversion', 'Adding image sizes to the attachments will take approximately another ' . $secs . ' seconds.', false);
            $now = time();
        }
        
        $attachquery = $this->toDbHost->query("SELECT aid, filetype, attachment  FROM " . X_PREFIX . "attachments");
        while (($row = $this->toDbHost->fetch_array($attachquery)) != false) {
            if (strpos($row['filetype'], 'image') !== false) {
                $exsize = getimagesize($row['attachment']);
                $attach_insert = $this->toDbHost->query("UPDATE " . X_PREFIX . "attachments set fileheight = '" . intval($exsize[1]) . "', filewidth = '" . intval($exsize[0]) . "' WHERE aid = '" . $row['aid'] . "'");
                if ($attach_insert === false) {
                    setCol($this->prgbar, '#ff0000');
                    print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'attachments table.', true);
                }
            }
            
            $row['attachment'] = null; // free up the blob
            $row = null;
        }
        $this->toDbHost->free_result($attachquery);
        setBar($this->prgbar, 0.8);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function addresses()
    {
        $addressquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "buddys");
        if ($addressquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'buddys table at XMB is intact. There was a problem querying for your buddys.', true);
        }
        
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "addresses");
        while (($row = $this->fromDbHost->fetch_array($addressquery)) != false) {
            $address_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "addresses (username, addressname) VALUES ('" . $this->toDbHost->escape($row['username']) . "','" . $this->toDbHost->escape($row['buddyname']) . "')");
            if ($address_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'addresses table.', true);
            }
        }
        $this->fromDbHost->free_result($addressquery);
        setBar($this->prgbar, 0.85);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function favorites()
    {
        $favquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "favorites WHERE type='favorite'");
        if ($favquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'favorites table at XMB is intact. There was a problem querying for your favorites.', true);
        }
        
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "favorites");
        while (($row = $this->fromDbHost->fetch_array($favquery)) != false) {
            $favorites_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "favorites (tid, username, type) VALUES ('$row[tid]','" . $this->toDbHost->escape($row['username']) . "','$row[type]')");
            if ($favorites_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'favorites table.', true);
            }
        }
        $this->fromDbHost->free_result($favquery);
        setBar($this->prgbar, 0.87);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function subscriptions()
    {
        $subquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "favorites WHERE type='subscription'");
        if ($subquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'favorites table at XMB is intact. There was a problem querying for your favorites.', true);
        }
        
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "subscriptions");
        while (($row = $this->fromDbHost->fetch_array($subquery)) != false) {
            $subs_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "subscriptions (tid, username, type) VALUES ('$row[tid]','" . $this->toDbHost->escape($row['username']) . "','$row[type]')");
            if ($subs_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'subscriptions table.', true);
            }
        }
        $this->fromDbHost->free_result($subquery);
        setBar($this->prgbar, 0.9);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function censors()
    {
        $censorquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "words");
        if ($censorquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'words table at XMB is intact. There was a problem querying for your wordlist.', true);
        }
        
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "words");
        while (($row = $this->fromDbHost->fetch_array($censorquery)) != false) {
            $word_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "words (find, replace1, id) VALUES ('" . $this->toDbHost->escape($row['find']) . "','" . $this->toDbHost->escape($row['replace1']) . "','$row[id]')");
            if ($word_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'words table.', true);
            }
        }
        $this->fromDbHost->free_result($censorquery);
        setBar($this->prgbar, 0.91);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function banned()
    {
        $banquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "banned");
        if ($banquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'banned table at XMB is intact. There was a problem querying for your banned users.', true);
        }
        
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "banned");
        while (($row = $this->fromDbHost->fetch_array($banquery)) != false) {
            $ban_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "banned (ip1, ip2, ip3, ip4, dateline, id) VALUES ('$row[ip1]','$row[ip2]','$row[ip3]','$row[ip4]','$row[dateline]','$row[id]')");
            if ($ban_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'banned table.', true);
            }
        }
        $this->fromDbHost->free_result($banquery);
        setBar($this->prgbar, 0.92);
    }

    /**
     * XMB on / off to new schema
     *
     * @param string $varname
     *            name of the variable
     * @param boolean $glob
     *            is this variable also a global?
     * @return string yes if set to yes, no otherwise
     */
    function valOnOff($varname)
    {
        if (isset($varname) && strtolower($varname) == 'on') {
            return 'on';
        }
        return 'off';
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function settings()
    {
        $settingsquery = $this->fromDbHost->query("SELECT * FROM `" . X_PREFIX2 . "settings`");
        if ($settingsquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'settings table at XMB is intact. There was a problem querying for your settings.', true);
        }
        $oldsettings = $this->fromDbHost->fetch_array($settingsquery);
        
        $settings_sql = "UPDATE `" . X_PREFIX . "settings` SET " . "bbname='" . $this->toDbHost->escape($oldsettings['bbname']) . "', " . "sitename='" . $this->toDbHost->escape($oldsettings['sitename']) . "', " . "bboffreason='" . $this->toDbHost->escape($oldsettings['bboffreason']) . "', " . "bbrulestxt='" . $this->toDbHost->escape($oldsettings['bbrulestxt']) . "', " . "siteurl='" . $this->toDbHost->escape($oldsettings['siteurl']) . "', " . "indexnewstxt='" . $this->toDbHost->escape($oldsettings['tickercontents']) . "', " . "sitename='" . $this->toDbHost->escape($oldsettings['sitename']) . "', " . "adminemail='" . $this->toDbHost->escape($oldsettings['adminemail']) . "', " . 
        
        // Integers
        "postperpage='" . intval($oldsettings['postperpage']) . "', " . "topicperpage='" . intval($oldsettings['topicperpage']) . "', " . "hottopic='" . intval($oldsettings['hottopic']) . "', " . "u2uquota='" . intval($oldsettings['u2uquota']) . "', " . 
        
        // on / off (XMB has many varients on this, but we try to do as best we can
        "whosonlinestatus='" . $this->valOnOff($oldsettings['whosonlinestatus']) . "', " . "regstatus='" . $this->valOnOff($oldsettings['regstatus']) . "', " . "regviewonly='" . $this->valOnOff($oldsettings['regviewonly']) . "', " . "hideprivate='" . $this->valOnOff($oldsettings['hideprivate']) . "', " . "emailcheck='" . $this->valOnOff($oldsettings['emailcheck']) . "', " . 

        "searchstatus='" . $this->valOnOff($oldsettings['searchstatus']) . "', " . "faqstatus='" . $this->valOnOff($oldsettings['faqstatus']) . "', " . "memliststatus='" . $this->valOnOff($oldsettings['memliststatus']) . "', " . "avastatus='" . $this->valOnOff($oldsettings['avastatus']) . "', " . "bbrules='" . $this->valOnOff($oldsettings['bbrules']) . "', " . "coppa='" . $this->valOnOff($oldsettings['coppa']) . "', " . "sigbbcode='" . $this->valOnOff($oldsettings['sigbbcode']) . "', " . "reportpost='" . $this->valOnOff($oldsettings['reportpost']) . "', " . "bbinsert='" . $this->valOnOff($oldsettings['bbinsert']) . "', " . "smileyinsert='" . $this->valOnOff($oldsettings['smileyinsert']) . "', " . "doublee='" . $this->valOnOff($oldsettings['doublee']) . "', " . "editedby='" . $this->valOnOff($oldsettings['editedby']) . "', " . "dotfolders='" . $this->valOnOff($oldsettings['dotfolders']) . "', " . "attachimgpost='" . $this->valOnOff($oldsettings['attachimgpost']) . "', " . "topicactivity_status='" . $this->valOnOff($oldsettings['todaysposts']) . "', " . "stats='" . $this->valOnOff($oldsettings['statsstatus']) . "'";
        
        $settings_update = $this->toDbHost->query($settings_sql);
        if ($settings_update === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem updating the ' . X_PREFIX . 'settings table', true);
        }
        $this->fromDbHost->free_result($settingsquery);
        setBar($this->prgbar, 0.26);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function messages()
    {
        $u2uquery = $this->fromDbHost->query("SELECT * FROM " . X_PREFIX2 . "u2u");
        if ($u2uquery === false) {
            setCol($this->prgbar, '#ff0000');
            print_error('Conversion Error', 'Please make sure your ' . X_PREFIX2 . 'u2u table at XMB is intact. There was a problem querying for your U2Us.', true);
        }
        
        $this->toDbHost->query("TRUNCATE TABLE " . X_PREFIX . "u2u");
        while (($row = $this->fromDbHost->fetch_array($u2uquery)) != false) {
            $u2u_insert = $this->toDbHost->query("INSERT INTO " . X_PREFIX . "u2u (u2uid, msgto, msgfrom, type, owner, folder, subject, message, dateline, readstatus, sentstatus) VALUES ('$row[u2uid]', '" . $this->toDbHost->escape($row['msgto']) . "', '" . $this->toDbHost->escape($row['msgfrom']) . "', '$row[type]', '" . $this->toDbHost->escape($row['owner']) . "', '" . $this->toDbHost->escape($row['folder']) . "', '" . $this->toDbHost->escape($row['subject']) . "', '" . $this->toDbHost->escape($row['message']) . "', '$row[dateline]', '$row[readstatus]', '$row[sentstatus]')");
            if ($u2u_insert === false) {
                setCol($this->prgbar, '#ff0000');
                print_error('Conversion Error', 'Please make sure that GaiaBB had successfully installed. There was a problem inserting into the ' . X_PREFIX . 'u2u table.', true);
            }
        }
        $this->fromDbHost->free_result($u2uquery);
        setBar($this->prgbar, 0.97);
    }

    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    function finish()
    {
        $this->toDbHost->query("UPDATE " . X_PREFIX . "settings SET bbstatus='on'");
        setBar($this->prgbar, 1.0);
    }
}
?>