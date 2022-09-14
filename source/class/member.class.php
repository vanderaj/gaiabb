<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2022 The GaiaBB Group
 * https://github.com/vanderaj/gaiabb
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

if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

if (!defined('ROOTCLASS')) {
    define('ROOTCLASS', '../class/');
}

require_once ROOTCLASS . 'attachments.class.php';
require_once ROOTCLASS . 'forum.class.php';
require_once ROOTCLASS . 'thread.class.php';
require_once ROOTCLASS . 'post.class.php';
require_once ROOTCLASS . 'favorite.class.php';
require_once ROOTCLASS . 'subscription.class.php';
require_once ROOTCLASS . 'pm.class.php';

/**
 * This object creates a nice wrapper for dealing with members.
 *
 * It can be used in several different ways:
 *
 * $member = new member(); // just create an object
 * $member->record['username'] = "fred";
 * $member->update();
 *
 * In the above example, if "fred" already exists, the query will fail as MySQL
 * will fail it. You must know this sort of stuff as the class is not very smart
 *
 * Finding an existing member, and work with their user record:
 *
 * $member = new member($uid); // loads $uid's user record
 * if ($member !== false)
 * {
 *    $member->record['username'] = 'fred';
 *    $member->update();
 * }
 *
 * or
 *
 * $member = new member();
 * if ($member->findByName('example') !== false)
 * {
 *    $member->record['customstatus'] = 'Example status';
 *    $member->update();
 * }
 *
 * You only need to call update() if you'd like your changes to be persisted. Otherwise,
 * don't bother :)
 *
 * Lastly, you don't need to do much to delete members:
 *
 * $member = new member();
 * $member->delete($uid); // delete UID directly
 *
 * Note: You do not need to unescape / escape data using this object. It does it for you.
 *
 * @package GaiaBB
 */
class member
{
    public $dirty;
    public $record;
    public $uid;
    public $extracol = array();

    public function member($memberId = 0)
    {
        if ($memberId == 0) {
            // create a new member object... in memory
            $this->init();
            return true;
        } else {
            // find the member in the database
            // return false if not found
            return $this->findById($memberId);
        }
    }

    public function init()
    {
        $this->dirty = true;
        $this->record = array(
            'uid' => 0,
            'username' => '',
            'password' => '',
            'regdate' => '',
            'postnum' => '',
            'email' => '',
            'site' => '',
            'aim' => '',
            'status' => '',
            'location' => '',
            'bio' => '',
            'sig' => '',
            'showemail' => '',
            'timeoffset' => '',
            'icq' => '',
            'avatar' => '',
            'yahoo' => '',
            'customstatus' => '',
            'theme' => '',
            'bday' => '',
            'langfile' => '',
            'tpp' => '',
            'ppp' => '',
            'newsletter' => '',
            'regip' => '',
            'timeformat' => '',
            'msn' => '',
            'ban' => '',
            'dateformat' => '',
            'ignorepm' => '',
            'lastvisit' => '',
            'mood' => '',
            'pwdate' => '',
            'invisible' => '',
            'pmfolders' => '',
            'saveogpm' => '',
            'emailonpm' => '',
            'daylightsavings' => '',
            'viewavatars' => '',
            'photo' => '',
            'psorting' => '',
            'viewsigs' => '',
            'firstname' => '',
            'lastname' => '',
            'showname' => '',
            'occupation' => '',
            'notepad' => '',
            'blog' => '',
            'views' => '',
            'expview' => '',
            'threadnum' => '',
            'readrules' => '',
        );
        $this->processExtraCol();
        $this->uid = 0;
    }

    public function findById($uid)
    {
        global $db;

        $query = @$db->query("SELECT * FROM " . X_PREFIX . "members WHERE uid = '" . intval($uid) . "'");
        if ($query && $db->num_rows($query) === 1) {
            $this->record = $db->fetch_array($query);
            $db->free_result($query);
            array_map('stripslashes', $this->record);
            $this->uid = $this->record['uid'];
            $this->dirty = true;
            return true;
        }
        return false;
    }

    public function findByName($name)
    {
        global $db;

        $query = @$db->query("SELECT * FROM " . X_PREFIX . "members WHERE username = '" . $db->escape($name) . "'");
        if ($query && $db->num_rows($query) === 1) {
            $this->record = $db->fetch_array($db);
            $db->free_result($query);
            array_map('stripslashes', $this->record);
            $this->uid = $this->record['uid'];
            $this->dirty = true;
            return true;
        }
        return false;
    }

    public function findUsernameByUid($uid)
    {
        global $db;

        if ($uid == 0) {
            return false;
        }

        $query = $db->query("SELECT username FROM " . X_PREFIX . "members WHERE uid = '" . intval($uid) . "'");
        if ($query === false || $db->num_rows($query) !== 1) {
            $db->free_result($query);
            return false;
        }
        $username = $db->result($query);
        $db->free_result($query);
        return $username;

    }

    public function findUidByUsername($username)
    {
        global $db;

        if (empty($username)) {
            return false;
        }

        $query = $db->query("SELECT uid FROM " . X_PREFIX . "members WHERE username = '" . $db->escape($username) . "'");
        if ($query === false || $db->num_rows($query) !== 1) {
            $db->free_result($query);
            return false;
        }
        $uid = $db->result($query);
        $db->free_result($query);
        return $uid;
    }

    public function exists($name, $email = '')
    {
        global $db;

        $emailClause = '';
        if (!empty($email)) {
            $emailClause = " OR email = '" . $email . "'";
        }

        $query = $db->query("SELECT username FROM " . X_PREFIX . "members WHERE username='" . $db->escape($name) . "'$emailClause");
        if ($db->num_rows($query) > 0) {
            return true;
        }
        return false;
    }

    public function isRestricted($username, $email, &$fail, &$efail)
    {
        global $db;

        $fail = $efail = false;
        $query = $db->query("SELECT * FROM " . X_PREFIX . "restricted");
        while ($restriction = $db->fetch_array($query)) {
            if ($restriction['case_sensitivity'] == 1) {
                $username = strtolower($username);
                $email = strtolower($email);
                $restriction['name'] = strtolower($restriction['name']);
            }

            if ($restriction['partial'] == 1) {
                if (strpos($username, $restriction['name']) !== false) {
                    $fail = true;
                    break;
                }

                if (strpos($email, $restriction['name']) !== false) {
                    $efail = true;
                    break;
                }
            } else {
                if ($username == $restriction['name']) {
                    $fail = true;
                    break;
                }

                if ($email == $restriction['name']) {
                    $efail = true;
                    break;
                }
            }
        }
        $db->free_result($query);
        return ($fail || $efail);
    }

    // TODO: remove and merge with above
    public function check_restricted($userto)
    {
        global $db;

        $nameokay = true;
        $query = $db->query("SELECT * FROM " . X_PREFIX . "restricted");

        if ($db->num_rows($query) > 0) {
            while ($restriction = $db->fetch_array($query)) {
                if ($restriction['case_sensitivity'] == 1) {
                    if ($restriction['partial'] == 1) {
                        if (strpos($userto, $restriction['name']) !== false) {
                            $nameokay = false;
                        }
                    } else {
                        if ($userto == $restriction['name']) {
                            $nameokay = false;
                        }
                    }
                } else {
                    $t_username = strtolower($userto);
                    $restriction['name'] = strtolower($restriction['name']);
                    if ($restriction['partial'] == 1) {
                        if (strpos($t_username, $restriction['name']) !== false) {
                            $nameokay = false;
                        }
                    } else {
                        if ($t_username == $restriction['name']) {
                            $nameokay = false;
                        }
                    }
                }
            }
        }
        $db->free_result($query);
        return $nameokay;
    }

    public function update()
    {
        global $db;

        // Don't bother updating the datbase if the object is clean
        if (!$this->dirty) {
            return false;
        }

        // If $this->uid is zero, it's memory only, so insert
        // If $this->uid is non-zero, it's database resident already, so update
        $sql = '';
        if ($this->uid == 0) {
            $sql = "INSERT INTO " . X_PREFIX . "members (
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
                        ignorepm,
                        lastvisit,
                        mood,
                        pwdate,
                        invisible,
                        pmfolders,
                        saveogpm,
                        emailonpm,
                        daylightsavings,
                        viewavatars,
                        photo,
                        psorting,
                        viewsigs,
                        firstname,
                        lastname,
                        showname,
                        occupation,
                        notepad,
                        blog,
                        views,
                        expview,
                        threadnum,
                        readrules
            " . $this->processExtraCol('insertnames') . ")" .
            "VALUES (" .
            "'" . $db->escape($this->record['username'], -1, true) . "'," .
            "'" . $db->escape($this->record['password']) . "'," .
            "'" . $db->escape($this->record['regdate']) . "'," .
            "'" . intval($this->record['postnum']) . "'," .
            "'" . $db->escape($this->record['email'], -1, true) . "'," .
            "'" . $db->escape($this->record['site']) . "'," .
            "'" . $db->escape($this->record['aim']) . "'," .
            "'" . $db->escape($this->record['status']) . "'," .
            "'" . $db->escape($this->record['location']) . "'," .
            "'" . $db->escape($this->record['bio']) . "'," .
            "'" . $db->escape($this->record['sig'], -1, true) . "'," .
            "'" . $db->escape($this->record['showemail']) . "'," .
            "'" . intval($this->record['timeoffset']) . "'," .
            "'" . $db->escape($this->record['icq']) . "'," .
            "'" . $db->escape($this->record['avatar']) . "'," .
            "'" . $db->escape($this->record['yahoo']) . "'," .
            "'" . $db->escape($this->record['customstatus']) . "'," .
            "'" . $db->escape($this->record['theme']) . "'," .
            "'" . $db->escape($this->record['bday']) . "'," .
            "'" . $db->escape($this->record['langfile']) . "'," .
            "'" . intval($this->record['tpp']) . "'," .
            "'" . intval($this->record['ppp']) . "'," .
            "'" . $db->escape($this->record['newsletter']) . "'," .
            "'" . $db->escape($this->record['regip']) . "'," .
            "'" . $db->escape($this->record['timeformat']) . "'," .
            "'" . $db->escape($this->record['msn']) . "'," .
            "'" . $db->escape($this->record['ban']) . "'," .
            "'" . $db->escape($this->record['dateformat']) . "'," .
            "'" . $db->escape($this->record['ignorepm']) . "'," .
            "'" . $db->escape($this->record['lastvisit']) . "'," .
            "'" . $db->escape($this->record['mood']) . "'," .
            "'" . $db->escape($this->record['pwdate']) . "'," .
            "'" . $db->escape($this->record['invisible']) . "'," .
            "'" . $db->escape($this->record['pmfolders']) . "'," .
            "'" . $db->escape($this->record['saveogpm']) . "'," .
            "'" . $db->escape($this->record['emailonpm']) . "'," .
            "'" . $db->escape($this->record['daylightsavings']) . "'," .
            "'" . $db->escape($this->record['viewavatars']) . "'," .
            "'" . $db->escape($this->record['photo']) . "'," .
            "'" . $db->escape($this->record['psorting']) . "'," .
            "'" . $db->escape($this->record['viewsigs']) . "'," .
            "'" . $db->escape($this->record['firstname']) . "'," .
            "'" . $db->escape($this->record['lastname']) . "'," .
            "'" . $db->escape($this->record['showname']) . "'," .
            "'" . $db->escape($this->record['occupation']) . "'," .
            "'" . $db->escape($this->record['notepad']) . "'," .
            "'" . $db->escape($this->record['blog']) . "'," .
            "'" . intval($this->record['views']) . "'," .
            "'" . $db->escape($this->record['expview']) . "'," .
            "'" . intval($this->record['threadnum']) . "'," .
            "'" . $db->escape($this->record['readrules']) . "'" .
            $this->processExtraCol('insertvalues') . ")";
        } else {
            $sql = "UPDATE " . X_PREFIX . "members SET " .
            "username = '" . $db->escape($this->record['username']) . "', " .
            "password = '" . $db->escape($this->record['password']) . "', " .
            "regdate = '" . $db->escape($this->record['regdate']) . "', " .
            "postnum = '" . intval($this->record['postnum']) . "', " .
            "email = '" . $db->escape($this->record['email']) . "', " .
            "site = '" . $db->escape($this->record['site']) . "', " .
            "aim = '" . $db->escape($this->record['aim']) . "', " .
            "status = '" . $db->escape($this->record['status']) . "', " .
            "location = '" . $db->escape($this->record['location']) . "', " .
            "bio = '" . $db->escape($this->record['bio']) . "', " .
            "sig = '" . $db->escape($this->record['sig'], -1, true) . "', " .
            "showemail = '" . $db->escape($this->record['showemail']) . "', " .
            "timeoffset = '" . intval($this->record['timeoffset']) . "', " .
            "icq = '" . $db->escape($this->record['icq']) . "', " .
            "avatar = '" . $db->escape($this->record['avatar']) . "', " .
            "yahoo = '" . $db->escape($this->record['yahoo']) . "', " .
            "customstatus = '" . $db->escape($this->record['customstatus']) . "', " .
            "theme = '" . $db->escape($this->record['theme']) . "', " .
            "bday = '" . $db->escape($this->record['bday']) . "', " .
            "langfile = '" . $db->escape($this->record['langfile']) . "', " .
            "tpp = '" . intval($this->record['tpp']) . "', " .
            "ppp = '" . intval($this->record['ppp']) . "', " .
            "newsletter = '" . $db->escape($this->record['newsletter']) . "', " .
            "regip = '" . $db->escape($this->record['regip']) . "', " .
            "timeformat = '" . $db->escape($this->record['timeformat']) . "', " .
            "msn = '" . $db->escape($this->record['msn']) . "', " .
            "ban = '" . $db->escape($this->record['ban']) . "', " .
            "dateformat = '" . $db->escape($this->record['dateformat']) . "', " .
            "ignorepm = '" . $db->escape($this->record['ignorepm']) . "', " .
            "lastvisit = '" . $db->escape($this->record['lastvisit']) . "', " .
            "mood = '" . $db->escape($this->record['mood']) . "', " .
            "pwdate = '" . $db->escape($this->record['pwdate']) . "', " .
            "invisible = '" . $db->escape($this->record['invisible']) . "', " .
            "pmfolders = '" . $db->escape($this->record['pmfolders']) . "', " .
            "saveogpm = '" . $db->escape($this->record['saveogpm']) . "', " .
            "emailonpm = '" . $db->escape($this->record['emailonpm']) . "', " .
            "daylightsavings = '" . $db->escape($this->record['daylightsavings']) . "', " .
            "viewavatars = '" . $db->escape($this->record['viewavatars']) . "', " .
            "photo = '" . $db->escape($this->record['photo']) . "', " .
            "psorting = '" . $db->escape($this->record['psorting']) . "', " .
            "viewsigs = '" . $db->escape($this->record['viewsigs']) . "', " .
            "firstname = '" . $db->escape($this->record['firstname']) . "', " .
            "lastname = '" . $db->escape($this->record['lastname']) . "', " .
            "showname = '" . $db->escape($this->record['showname']) . "', " .
            "occupation = '" . $db->escape($this->record['occupation']) . "', " .
            "notepad = '" . $db->escape($this->record['notepad']) . "', " .
            "blog = '" . $db->escape($this->record['blog']) . "', " .
            "views = '" . intval($this->record['views']) . "', " .
            "expview = '" . $db->escape($this->record['expview']) . "', " .
            "threadnum = '" . $db->escape($this->record['threadnum']) . "', " .
            "readrules = '" . $db->escape($this->record['readrules']) . "' " .
            $this->processExtraCol('update') .
            " WHERE uid = '" . intval($this->record['uid']) . "'";
        }

        if ($db->query($sql) == true) {
            $this->dirty = false;
            // Mark as a database object (stops repeat insertions)
            if ($this->uid == 0) {
                $this->uid = $db->insert_id();
                $this->record['uid'] = $this->uid;
            }
            return true;
        }
        return false;
    }

    /**
     * Delete a member record
     *
     * You don't need to instantiate the object to use this baby, just pass in
     * a workable ID:
     *
     * if (member::delete($uid)) ...
     *
     * or
     *
     * $member = new member();
     * if ($member->delete($uid)) ...
     *
     * Both work identically in one query, but the first one is faster as
     * init() is not called
     *
     * Or if you don't care about performance ... take two queries!
     * $member = new member();
     * $member->findByName('example');
     * $member->delete();
     *
     * To encourage UID only stuff, there will not be a deleteByName() method.
     *
     * @param   integer   $uid   the UID of the user you'd like to delete (optional)
     * @return   mixed   the query resource if $uid is set, false otherwise
     */
    public function delete($uid = 0)
    {
        if ($this->uid === 0 && $uid === 0) {
            return false;
        }

        if ($uid !== 0) {
            $this->uid = $uid;
        }
        return @$db->query("DELETE FROM " . X_PREFIX . "members WHERE uid = '" . intval($this->uid) . "'");
    }

    public function deletePosts($uid = 0)
    {
        global $db;

        if ($this->uid === 0 && $uid === 0) {
            return false;
        }

        if ($uid !== 0) {
            $this->uid = $uid;
        }

        // Needs a lot of time
        if (!((bool) ini_get('safe_mode'))) {
            set_time_limit(0);
        }

        // Find all posts from the user, and delete them
        $query = $db->query("DELETE FROM " . X_PREFIX . "posts WHERE author = '" . $db->escape($this->record['username']) . "'");
        $this->dirty = true;
        $this->record['postnum'] = 0;
        $this->update();

        // Find orphaned attachments and delete them
        $attachObj = new attachment();
        $count = $count2 = 0;
        $attachObj->fixOrphans($count, $count2);

        // Fix up threads, so that threads with an now missing first post are re-homed to what used to be the second post
        $threadObj = new thread();
        $threadObj->fixFirstPost();
        // Fix up threads, so that lastpost is correct
        $threadObj->fixLastPost();

        // Fix up forums, so that lastpost and thread and post count is correct
        $forumObj = new forum();
        $forumObj->fixThreadPostCount();
        $forumObj->fixLastPost();
    }

    public function deleteAll($uid = 0)
    {
        if ($this->uid === 0 && $uid === 0) {
            return false;
        }

        if ($uid !== 0) {
            $this->uid = $uid;
        }

        // pm's and pm attachments
        $pmObj = new pm();
        $pmObj->deleteByUid($this->uid);

        // subscriptions
        $subObj = new subscription();
        $subObj->deleteByUid($this->uid);

        // favorites
        $favObj = new favorite();
        $favObj->deleteByUid($this->uid);

        // addresses
        $addObj = new address();
        $addObj->deleteByUid($this->uid);

        $this->deletePosts($uid);
        $this->delete($uid);
    }

    public function processExtraCol($type = '')
    {
        global $db;

        $return = '';
        $return_ok = false;
        foreach ($this->extracol as $colname => $coltype) {
            switch ($type) {
                case 'insertnames':
                    $return .= ',' . $colname;
                    $return_ok = true;
                    break;
                case 'insertvalues':
                    switch ($coltype) {
                        case 'integer':
                            $return .= ",'" . intval($this->record[$colname]) . "'";
                            break;
                        case 'string':
                        default:
                            $return .= ",'" . $db->escape($this->record[$colname]) . "'";
                            break;
                    }
                    $return_ok = true;
                    break;
                case 'update':
                    switch ($coltype) {
                        case 'integer':
                            $return .= ',' . $colname . " = '" . intval($this->record[$colname]) . "'";
                            break;
                        case 'string':
                        default:
                            $return .= ',' . $colname . " = '" . $db->escape($this->record[$colname]) . "'";
                            break;
                    }
                    $return_ok = true;
                    break;
                default:
                    $this->record[$colname] = '';
                    break;
            }
        }

        if ($return_ok) {
            return $return;
        }
    }

    public function rename($userfrom, $userto)
    {
        global $db, $lang, $self;

        // can't do it if either username is blank
        if ($userfrom == '' || $userto == '') {
            return $lang['admin_rename_fail'];
        }

        // prevent malicious chars from being used when renaming users
        if (preg_match("/[\]\['" . '",!@#~$%\^&*()+=\/\\\\:;?|.<>{}]/', $userto)) {
            return $lang['badusername'];
        }

        // ensure that total chars do not exceed or undermind minimal
        if (strlen($userto) < 4 || strlen($userto) > 25) {
            return $lang['usernamelimits'];
        }

        // user must currently exist and must not become anyone else
        $query = $db->query("SELECT username FROM " . X_PREFIX . "members WHERE username = '$userfrom'");
        $cUsrFrm = $db->num_rows($query);
        $db->free_result($query);

        $query = $db->query("SELECT username FROM " . X_PREFIX . "members WHERE username = '$userto'");
        $cUsrTo = $db->num_rows($query);
        $db->free_result($query);

        // userfrom must only be 1 (row), and userto must not exist (ie 0 rows)
        if (!($cUsrFrm == 1 && $cUsrTo == 0)) {
            return $lang['admin_rename_fail'];
        }

        // userto must not obviate restricted username rules
        if (!$this->check_restricted($userto)) {
            return $lang['restricted'];
        }

        // we're good to go, rename user
        if (!((bool) ini_get('safe_mode'))) {
            set_time_limit(180);
        }

        $db->query("UPDATE " . X_PREFIX . "members SET username = '$userto' WHERE username = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "addresses SET username = '$userto' WHERE username = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "addresses SET addressname = '$userto' WHERE addressname = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "favorites SET username = '$userto' WHERE username = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "subscriptions SET username = '$userto' WHERE username = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "adminlogs SET username = '$userto' WHERE username = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "modlogs SET username = '$userto' WHERE username = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "posts SET author = '$userto' WHERE author = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "threads SET author = '$userto' WHERE author = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "pm SET msgto = '$userto' WHERE msgto = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "pm SET msgfrom = '$userto' WHERE msgfrom = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "pm SET owner = '$userto' WHERE owner = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "pm_attachments SET owner = '$userto' WHERE owner = '$userfrom'");
        $db->query("UPDATE " . X_PREFIX . "whosonline SET username = '$userto' WHERE username = '$userfrom'");

        // update thread last posts
        $query = $db->query("SELECT tid FROM " . X_PREFIX . "lastposts WHERE username = '$userfrom'");
        while ($result = $db->fetch_array($query)) {
            $db->query("UPDATE " . X_PREFIX . "lastposts SET username = '$userto' WHERE tid = '" . $result['tid'] . "'");
        }
        $db->free_result($query);

        // update ignorepm
        $query = $db->query("SELECT ignorepm, uid FROM " . X_PREFIX . "members WHERE (ignorepm REGEXP '(^|(,))()*$userfrom()*((,)|$)')");
        while ($usr = $db->fetch_array($query)) {
            $parts = explode(',', $usr['ignorepm']);
            $index = array_search($userfrom, $parts);
            $parts[$index] = $userto;
            $parts = implode(',', $parts);
            $db->query("UPDATE " . X_PREFIX . "members SET ignorepm = '" . $parts . "' WHERE uid = '" . $usr['uid'] . "'");
        }
        $db->free_result($query);

        // update forum-accesslists
        $query = $db->query("SELECT userlist, fid FROM " . X_PREFIX . "forums WHERE (userlist REGEXP '(^|(,))()*$userfrom()*((,)|$)')");
        while ($list = $db->fetch_array($query)) {
            $parts = array_unique(array_map('trim', explode(',', $list['userlist'])));
            $index = array_search($userfrom, $parts);
            $parts[$index] = $userto;
            $parts = implode(',', $parts);
            $db->query("UPDATE " . X_PREFIX . "forums SET userlist = '" . $parts . "' WHERE fid = '" . $list['fid'] . "'");
        }
        $db->free_result($query);

        // Moderator column in forums
        $query = $db->query("SELECT moderator, fid FROM " . X_PREFIX . "forums WHERE (moderator REGEXP '(^|(,))()*$userfrom()*((,)|$)')");
        while ($mod = $db->fetch_array($query)) {
            $parts = explode(',', $mod['moderator']);
            $index = array_search($userfrom, $parts);
            $parts[$index] = $userto;
            $parts = implode(',', $parts);
            $db->query("UPDATE " . X_PREFIX . "forums SET moderator = '" . $parts . "' WHERE fid = '" . $mod['fid'] . "'");
        }
        $db->free_result($query);

        // update forum last posts
        $query = $db->query("SELECT fid, lastpost from " . X_PREFIX . "forums WHERE lastpost like '%$userfrom'");
        while ($result = $db->fetch_array($query)) {
            list($posttime, $lastauthor) = explode('|', $result['lastpost']);
            if ($lastauthor == $userfrom) {
                $newlastpost = $posttime . '|' . $userto;
                $db->query("UPDATE " . X_PREFIX . "forums SET lastpost = '$newlastpost' WHERE fid = '" . $result['fid'] . "'");
            }
        }
        $db->free_result($query);

        return (($self['username'] == $userfrom) ? $lang['admin_rename_warn_self'] : '') . $lang['admin_rename_success'];
    }

    public function fixPostTotals()
    {
        global $db;

        $query = $db->query("SELECT uid, username FROM " . X_PREFIX . "members");
        while ($mem = $db->fetch_array($query)) {
            $inner_query = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE author = '" . $db->escape($mem['username']) . "'");
            $postsnum = $db->result($inner_query, 0);
            $db->free_result($inner_query);
            $db->query("UPDATE " . X_PREFIX . "members SET postnum = '$postsnum' WHERE uid = '" . $mem['uid'] . "'");
        }
        $db->free_result($query);
    }

    public function fixThreadTotals()
    {
        global $db;

        $query = $db->query("SELECT uid, username FROM " . X_PREFIX . "members");
        while ($mem = $db->fetch_array($query)) {
            $inner_query = $db->query("SELECT COUNT(tid) FROM " . X_PREFIX . "threads WHERE author='" . $db->escape($mem['username']) . "'");
            $threadnum = $db->result($inner_query, 0);
            $db->free_result($inner_query);
            $db->query("UPDATE " . X_PREFIX . "members SET threadnum = '$threadnum' WHERE uid = '" . $mem['uid'] . "'");
        }
        $db->free_result($query);
    }
}
