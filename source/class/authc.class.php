<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2017 The GaiaBB Group
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

class AuthState
{

    private $state;

    public $gbbuid;
    public $gbbpw;

    function __construct()
    {
        $this->state = array();
        $this->gbbuid = '';
        $this->gbbpw = '';
        
        $this->get();
    }

    function get()
    {
        if (isset($_COOKIE['gbbstate'])) {
            try {
                $tmpState = $_COOKIE['gbbstate'];
                
                $tmpState = base64_decode($tmpState, true);
                
                if ($tmpState === false) {
                    throw new Exception("Invalid decode of state string");
                }
                
                // $this->state = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, GAIABB_MASTERKEY, $tmpState, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
                // if ($this->state === false)
                // {
                // throw new Exception("Invalid decryption");
                // }
                $this->state = unserialize($tmpState);
                
                if (isset($this->state['version']) && $this->state['version'] != 1) {
                    throw new Exception("Invalid state version, or state is not valid.");
                }
                
                $this->gbbuid = $this->state['gbbuid'];
                $this->gbbpw = $this->state['gbbpw'];
            } catch (Exception $e) {
                global $db;
                
                $db->panic("authState :: get() - Failed to decrypt authentication state", $e);
            }
        }
    }

    function convert()
    {
        if (isset($_COOKIE['gbbuid'])) {
            $this->gbbuid = $_COOKIE['gbbuid'];
        }
        
        if (isset($_COOKIE['gbbpw'])) {
            $this->gbbpw = $_COOKIE['gbbpw'];
        }
        
        $this->update();
    }

    function update()
    {
        global $onlinetime, $cookiepath, $cookiedomain;
        
        try {
            $this->state['version'] = 1;
            $this->state['gbbuid'] = $this->gbbuid;
            $this->state['gbbpw'] = $this->gbbpw;
            
            $tmpState = serialize($this->state);
            
            // $tmpState = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, GAIABB_MASTERKEY, $tmpState, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
            // if ($this->state === false)
            // {
            // throw new Exception("Invalid encryption");
            // }
            $tmpState = base64_encode($tmpState);
            $currtime = $onlinetime + (86400 * 30);
            setcookie('gbbstate', $tmpState, $currtime, $cookiepath, $cookiedomain);
        } catch (Exception $e) {
            global $db;
            
            $db->panic("authState :: update() - Failed to update authentication state", $e);
        }
    }
}

class AuthC
{

    function __construct()
    {}

    function expireCaches()
    {
        global $config_cache, $moderators_cache;
        
        $config_cache->expire('settings');
        $moderators_cache->expire('moderators');
        $config_cache->expire('theme');
        $config_cache->expire('pluglinks');
        $config_cache->expire('whosonline');
        $config_cache->expire('forumjump');
    }

    function autoLoginViaSession()
    {
        global $gbbpw, $gbbuid;
        
        $retval = false;
        
        if (isset($_SESSION['gbbuid']) && is_numeric($_SESSION['gbbuid'])) {
            $gbbuid = intval($_SESSION['gbbuid']);
            $gbbpw = $_SESSION['gbbpw'];
            
            $retval = true;
        }
        return $retval;
    }

    function autoLoginViaAuthState()
    {
        global $gbbpw, $gbbuid, $authState;
        
        $retval = false;
        
        $authState->get();
        
        if (isset($authState->gbbuid) && is_numeric($authState->gbbuid)) {
            $gbbuid = $_SESSION['gbbuid'] = $authState->gbbuid;
            $gbbpw = $_SESSION['gbbpw'] = $authState->gbbpw;
            
            $retval = true;
        }
        return $retval;
    }

    function autoLoginViaCookie()
    {
        global $authState;
        
        $retval = false;
        
        if (isset($_COOKIE['gbbuid']) && is_numeric($_COOKIE['gbbuid'])) {
            $authState->convert();
            $retval = $this->autoLoginViaAuthState();
        }
        return $retval;
    }

    function autoLogin()
    {
        global $db, $self, $gbbuser, $gbbpw, $gbbuid, $onlinetime, $CONFIG;
        
        $gbbuser = '';
        $gbbpw = '';
        $gbbuid = 0;
        
        $auto = false;
        
        if (isset($_SESSION['gbbuid']) && $_SESSION['gbbuid'] > 0) {
            $auto = $this->autoLoginViaSession();
        } else 
            if (isset($_COOKIE['gbbstate'])) {
                $auto = $this->autoLoginViaAuthState();
            } else 
                if (isset($_COOKIE['gbbuid'])) {
                    $auto = $this->autoLoginViaCookie();
                }
        
        $q = false;
        $self['status'] = '';
        $userrec = array();
        if ($auto && $gbbuid > 0) {
            $mq = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE uid = '$gbbuid'");
            $userrec = $db->fetch_array($mq);
            if (($db->num_rows($mq) == 1) && ($userrec['password'] == $gbbpw)) {
                $db->query("UPDATE " . X_PREFIX . "members SET lastvisit = " . $db->time($onlinetime) . " WHERE uid = '$gbbuid'");
                $q = true;
            }
            $db->free_result($mq);
        }
        
        if ($q) {
            define('X_MEMBER', true);
            define('X_GUEST', false);
            foreach ($userrec as $key => $val) {
                $self[$key] = $val;
            }
            
            $gbbuser = $self['username'];
            
            if (empty($self['theme'])) {
                $self['theme'] = 0;
            }
        } else {
            define('X_MEMBER', false);
            define('X_GUEST', true);
            
            $self = array(
                'uid' => 0,
                'username' => 'Anonymous',
                'password' => '',
                'regdate' => 0,
                'postnum' => 0,
                'email' => '',
                'site' => '',
                'aim' => '',
                'status' => 'Guest',
                'location' => '',
                'bio' => '',
                'sig' => '',
                'showemail' => 'no',
                'timeoffset' => $CONFIG['def_tz'],
                'icq' => 0,
                'avatar' => '',
                'yahoo' => '',
                'customstatus' => '',
                'theme' => 0,
                'bday' => '0000-00-00',
                'langfile' => $CONFIG['langfile'],
                'tpp' => $CONFIG['topicperpage'],
                'ppp' => $CONFIG['postperpage'],
                'newsletter' => 'no',
                'regip' => '000.000.000.000',
                'timeformat' => $CONFIG['timeformat'],
                'msn' => '',
                'ban' => '',
                'dateformat' => $CONFIG['dateformat'],
                'ignorepm' => '',
                'lastvisit' => 0,
                'mood' => '',
                'pwdate' => 0,
                'invisible' => 0,
                'pmfolders' => '',
                'saveogpm' => 'no',
                'emailonpm' => 'no',
                'useoldpm' => 'no',
                'daylightsavings' => $CONFIG['daylightsavings'],
                'viewavatars' => 'yes',
                'photo' => '',
                'psorting' => 'ASC',
                'viewsigs' => 'yes',
                'firstname' => '',
                'lastname' => '',
                'showname' => 'no',
                'occupation' => '',
                'notepad' => '',
                'blog' => '',
                'views' => 0,
                'expview' => 'no',
                'threadnum' => 0,
                'readrules' => 'no',
                'forcelogout' => 'no'
            )
            ;
        }
    }

    function updateLastVisit()
    {
        global $onlinetime, $cookiepath, $cookiedomain;
        global $gbblva, $gbblvb;
        global $lastvisit, $lastvisit2;
        
        $gbblva = isset($_COOKIE['gbblva']) ? intval($_COOKIE['gbblva']) : 0;
        $gbblvb = isset($_COOKIE['gbblvb']) ? intval($_COOKIE['gbblvb']) : 0;
        
        setcookie('gbblva', $onlinetime, $onlinetime + (86400 * 365), $cookiepath, $cookiedomain);
        
        $thetime = $onlinetime;
        if ($gbblvb > 0) {
            $thetime = $gbblvb;
        } else {
            if ($gbblva > 0) {
                $thetime = $gbblva;
            }
        }
        
        setcookie('gbblvb', $thetime, ($onlinetime + 600), $cookiepath, $cookiedomain);
        
        $lastvisit = $thetime;
        $lastvisit2 = $lastvisit - 540;
    }

    function updateOldTopics()
    {
        global $onlinetime, $cookiepath, $cookiedomain;
        
        $oldtopics = isset($_COOKIE['oldtopics']) ? $_COOKIE['oldtopics'] : '';
        if (! empty($oldtopics)) {
            setcookie('oldtopics', $oldtopics, ($onlinetime + 600), $cookiepath, $cookiedomain);
        }
    }

    function checkExcessiveLogins()
    {
        global $lang, $onlinetime, $THEME, $shadow;
        
        $errmsg = '';
        
        if (! empty($_SESSION['login_next_attempt']) && $_SESSION['login_next_attempt'] > $onlinetime) {
            eval('$errmsg = "' . template('login_time_met') . '";');
        } elseif (! empty($_SESSION['login_next_attempt']) && $_SESSION['login_next_attempt'] <= $onlinetime) {
            unset($_SESSION['login_next_attempt']);
            unset($_SESSION['login_attempts']);
        }
        
        return $errmsg;
    }

    function checkForceLogout()
    {
        global $lang, $db, $onlinetime, $self;
        
        if (X_MEMBER && ! X_ADMIN && isset($self['forcelogout']) && $self['forcelogout'] == 'yes') {
            // Make sure no logout is looped and the forced setting is thus reset again
            $query = $db->query("UPDATE " . X_PREFIX . "members SET forcelogout = 'no' WHERE uid = '$self[uid]'");
            
            // Gives user a nice message as to why they were suddenly logged out
            message($lang['forcelogout_reason'], true, false, false, false, true, false, true);
            $this->logout('index.php', 2.5);
        }
    }

    function updateLoginCounters()
    {
        global $CONFIG, $onlinetime, $lang, $THEME, $shadow;
        
        $errmsg = '';
        eval('$errmsg = "' . template('login_incorrectdetails') . '";');
        
        if (! isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 1;
        } else {
            $_SESSION['login_attempts'] ++;
            if ($_SESSION['login_attempts'] >= $CONFIG['login_max_attempts']) {
                $_SESSION['login_next_attempt'] = $onlinetime + 600;
                eval('$errmsg = "' . template('login_loginmaxmet') . '";');
            }
        }
        
        return $errmsg;
    }

    function login()
    {
        global $db, $member, $cookiedomain, $cookiepath, $onlinetime, $onlineip, $authState, $gbbuid, $gbbpw;
        
        $errmsg = '';
        
        $username = formVar('username');
        $password = md5(formVar('password'));
        $hide = form10('hide');
        $remember = formYesNo('remember');
        
        // Beware for this...
        $username = $db->escape($username, - 1, true);
        
        $query = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE username = '$username'");
        if ($query && $db->num_rows($query) == 1) {
            $member = $db->fetch_array($query);
        }
        $db->free_result($query);
        
        if (count($member) > 0 && $member['password'] == $password) {
            unset($_SESSION['login_next_attempt']);
            unset($_SESSION['login_attempts']);
            
            $this->expireCaches();
            
            $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE ip = '$onlineip' && username = 'xguest123'");
            $currtime = $onlinetime + (86400 * 30);
            
            $gbbuid = $_SESSION['gbbuid'] = $member['uid'];
            $gbbpw = $_SESSION['gbbpw'] = $password;
            
            // Logging on invisible?
            $db->query("UPDATE " . X_PREFIX . "members SET invisible = '$hide' WHERE uid = '$gbbuid'");
            
            // Logging on permanently?
            if ($remember == 'yes') {
                // Set a permanent cookie, which will generally last a month without being updated
                $authState->gbbuid = $gbbuid;
                $authState->gbbpw = $gbbpw;
                $authState->update();
            }
            
            session_regenerate_id(false);
            
            redirect('index.php', 0);
        } else {
            $errmsg = $this->updateLoginCounters();
        }
        
        return $errmsg;
    }

    function logout($url = 'index.php', $delay = 0)
    {
        global $db, $onlinetime, $cookiepath, $cookiedomain, $self;
        
        $query = $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE username = '" . $self['uid'] . "'");
        
        if (isset($_COOKIE['gbbstate'])) {
            setcookie("gbbstate", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['gbbuser'])) {
            setcookie("gbbuser", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['gbbpw'])) {
            setcookie("gbbpw", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['gbbuid'])) {
            setcookie("gbbuid", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['oldtopics'])) {
            setcookie("oldtopics", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['gbblva'])) {
            setcookie("gbblva", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['gbblvb'])) {
            setcookie("gbblvb", '', 0, $cookiepath, $cookiedomain);
        }
        
        // loop trough all password-forum-cookies and remove them
        foreach ($_COOKIE as $key => $val) {
            if (preg_match('#^fidpw([0-9]+)$#', $key)) {
                setcookie($key, '', 0, $cookiepath, $cookiedomain);
            }
        }
        
        $this->expireCaches();
        
        // Clear out the session data and session cookie
        session_regenerate_id(true);
        $_SESSION = array();
        redirect($url, $delay, (($delay > 0) ? X_REDIRECT_JS : X_REDIRECT_HEADER));
    }
}

