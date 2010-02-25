<?php
/**
 * GaiaBB
 * Copyright (c) 2010 The GaiaBB Group
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
    exit ('This file is not designed to be called directly');
}

class AuthState
{
    private $state;
    public $ubbuid;
    public $ubbpw;

    function __construct()
    {
		$this->state = array();
        $this->ubbuid = '';
        $this->ubbpw = '';

        $this->get();
    }

    function get()
    {
        if (isset ($_COOKIE['ubbstate']))
        {
            try
            {
                $tmpState = $_COOKIE['ubbstate'];
                
                $tmpState = base64_decode($tmpState, true);
                
                if ($tmpState === false)
                {
                    throw new Exception("Invalid decode of state string");
                }

                // $this->state = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, GAIABB_MASTERKEY, $tmpState, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
//                if ($this->state === false)
//                {
//                    throw new Exception("Invalid decryption");
//                }
                $this->state = unserialize($tmpState);
                
                if (isset ($this->state['version']) && $this->state['version'] != 1)
                {
                    throw new Exception("Invalid state version, or state is not valid.");
                }

                $this->ubbuid = $this->state['ubbuid'];
                $this->ubbpw = $this->state['ubbpw'];
            }
            catch (Exception $e)
            {
                global $db;

                $db->panic("authState :: get() - Failed to decrypt authentication state", $e);
            }
        }
    }

    function convert()
    {
        if (isset ($_COOKIE['ubbuid']))
        {
            $this->ubbuid = $_COOKIE['ubbuid'];
        }

        if (isset ($_COOKIE['ubbpw']))
        {
            $this->ubbpw = $_COOKIE['ubbpw'];
        }

        $this->update();
    }

    function update()
    {
		global $onlinetime, $cookiepath, $cookiedomain;

        try
        {
            $this->state['version'] = 1;
            $this->state['ubbuid'] = $this->ubbuid;
            $this->state['ubbpw'] = $this->ubbpw;

            $tmpState = serialize($this->state);

//            $tmpState = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, GAIABB_MASTERKEY, $tmpState, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
//            if ($this->state === false)
//            {
//                throw new Exception("Invalid encryption");
//            }
            $tmpState = base64_encode($tmpState);
	    	$currtime = $onlinetime + (86400 * 30);
            setcookie('ubbstate', $tmpState, $currtime, $cookiepath, $cookiedomain);
        }
        catch (Exception $e)
        {
            global $db;

            $db->panic("authState :: update() - Failed to update authentication state", $e);
        }
    }
}

class AuthC
{
    function __construct()
    {
    }

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
        global $ubbpw, $ubbuid;
        
        $retval = false;

        if ( isset($_SESSION['ubbuid']) && is_numeric($_SESSION['ubbuid']) )
        {
            $ubbuid = intval($_SESSION['ubbuid']);
            $ubbpw = $_SESSION['ubbpw'];
                    
            $retval = true;
        }
        return $retval;
    }

    function autoLoginViaAuthState()
    {
        global $ubbpw, $ubbuid, $authState;
        
        $retval = false;

        $authState->get();

        if ( isset($authState->ubbuid) && is_numeric($authState->ubbuid) )
        {
            $ubbuid = $_SESSION['ubbuid'] = $authState->ubbuid;        
            $ubbpw = $_SESSION['ubbpw'] = $authState->ubbpw;

            $retval = true;
        }
        return $retval;
    }

    function autoLoginViaCookie()
    {
    	global $authState;
    	
        $retval = false;

        if ( isset($_COOKIE['ubbuid']) && is_numeric($_COOKIE['ubbuid']) )
        {
        	$authState->convert();
        	$retval = $this->autoLoginViaAuthState();
        }
        return $retval;
    }


    function autoLogin()
    {
        global $db, $self, $ubbuser, $ubbpw, $ubbuid, $onlinetime, $CONFIG;
        
        $ubbuser = '';
        $ubbpw = '';
        $ubbuid = 0;
        
        $auto = false;
        
        if (isset($_SESSION['ubbuid']) && $_SESSION['ubbuid'] > 0)
        {
            $auto = $this->autoLoginViaSession();
        }
        else if ( isset($_COOKIE['ubbstate']) )
        {
            $auto = $this->autoLoginViaAuthState();
        }
        else if ( isset($_COOKIE['ubbuid']) )
        {
            $auto = $this->autoLoginViaCookie();    
        }

        $q = false;
        $self['status'] = '';
        $userrec = array ();
        if ($auto && $ubbuid > 0)
        {
            $mq = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE uid = '$ubbuid'");
            $userrec = $db->fetch_array($mq);
            if (($db->num_rows($mq) == 1) && ($userrec['password'] == $ubbpw) )
            {
                $db->query("UPDATE " . X_PREFIX . "members SET lastvisit = " . $db->time($onlinetime) . " WHERE uid = '$ubbuid'");
                $q = true;
            }
            $db->free_result($mq);
        }

        if ($q)
        {
            define('X_MEMBER', true);
            define('X_GUEST', false);
            foreach ($userrec as $key => $val)
            {
                $self[$key] = $val;
            }

            $ubbuser = $self['username'];
            
            if (empty ($self['theme']))
            {
                $self['theme'] = 0;
            }
        }
        else
        {
            define('X_MEMBER', false);
            define('X_GUEST', true);

            $self = array (
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
                'forcelogout' => 'no',
                
            );
        }
    }

    function updateLastVisit()
    {
        global $onlinetime, $cookiepath, $cookiedomain;
        global $ubblva, $ubblvb;
        global $lastvisit, $lastvisit2;

        $ubblva = isset ($_COOKIE['ubblva']) ? intval($_COOKIE['ubblva']) : 0;
        $ubblvb = isset ($_COOKIE['ubblvb']) ? intval($_COOKIE['ubblvb']) : 0;

        setcookie('ubblva', $onlinetime, $onlinetime + (86400 * 365), $cookiepath, $cookiedomain);

        $thetime = $onlinetime;
        if ($ubblvb > 0)
        {
            $thetime = $ubblvb;
        }
        else
        {
            if ($ubblva > 0)
            {
                $thetime = $ubblva;
            }
        }
        
        setcookie('ubblvb', $thetime, ($onlinetime + 600), $cookiepath, $cookiedomain);

        $lastvisit = $thetime;
        $lastvisit2 = $lastvisit - 540;
    }

    function updateOldTopics()
    {
        global $onlinetime, $cookiepath, $cookiedomain;

        $oldtopics = isset ($_COOKIE['oldtopics']) ? $_COOKIE['oldtopics'] : '';
        if (!empty($oldtopics))
        {
            setcookie('oldtopics', $oldtopics, ($onlinetime +600), $cookiepath, $cookiedomain);
        }
    }

    function checkExcessiveLogins()
    {
        global $lang, $onlinetime, $THEME, $shadow;

        $errmsg = '';
        
        if (!empty ($_SESSION['login_next_attempt']) && $_SESSION['login_next_attempt'] > $onlinetime)
        {
            eval ('$errmsg = "' . template('login_time_met') . '";');
        }
        elseif (!empty ($_SESSION['login_next_attempt']) && $_SESSION['login_next_attempt'] <= $onlinetime)
        {
            unset ($_SESSION['login_next_attempt']);
            unset ($_SESSION['login_attempts']);
        }
        
        return $errmsg;
    }

    function checkForceLogout()
    {
        global $lang, $db, $onlinetime, $self;
        
        if ( X_MEMBER && !X_ADMIN && isset($self['forcelogout']) && $self['forcelogout'] == 'yes' )
        {
            // Make sure no logout is looped and the forced setting is thus reset again
            $query = $db->query("UPDATE ".X_PREFIX."members SET forcelogout = 'no' WHERE uid = '$self[uid]'");
           
            // Gives user a nice message as to why they were suddenly logged out
            message($lang['forcelogout_reason'], true, false, false, false, true, false, true);
            $this->logout('index.php', 2.5);
        }
    }

    function updateLoginCounters()
    {
        global $CONFIG, $onlinetime, $lang, $THEME, $shadow;
        
        $errmsg = '';
		eval ('$errmsg = "' . template('login_incorrectdetails') . '";');
		        
        if (!isset ($_SESSION['login_attempts']))
        {
            $_SESSION['login_attempts'] = 1;
        }
        else
        {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= $CONFIG['login_max_attempts'])
            {
                $_SESSION['login_next_attempt'] = $onlinetime + 600;
                eval ('$errmsg = "' . template('login_loginmaxmet') . '";'); 
            }
        }
        
        return $errmsg;
    }

    function login()
    {
        global $db, $member, $cookiedomain, $cookiepath, $onlinetime, $onlineip, $authState, $ubbuid, $ubbpw;
        
        $errmsg = '';
        
        $username = formVar('username');
        $password = md5(formVar('password'));
        $hide = form10('hide');
        $remember = formYesNo('remember');

        // Beware for this...
        $username = $db->escape($username, -1, true);

        $query = $db->query("SELECT * FROM " . X_PREFIX . "members WHERE username = '$username'");
        if ($query && $db->num_rows($query) == 1)
        {
            $member = $db->fetch_array($query);
        }
        $db->free_result($query);
        
        if (count($member) > 0 && $member['password'] == $password)
        {
            unset ($_SESSION['login_next_attempt']);
            unset ($_SESSION['login_attempts']);

            $this->expireCaches();

            $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE ip = '$onlineip' && username = 'xguest123'");
            $currtime = $onlinetime + (86400 * 30);

            $ubbuid = $_SESSION['ubbuid'] = $member['uid'];
			$ubbpw = $_SESSION['ubbpw'] = $password;
			
			// Logging on invisible?
            $db->query("UPDATE " . X_PREFIX . "members SET invisible = '$hide' WHERE uid = '$ubbuid'");

            // Logging on permanently?
            if ( $remember == 'yes' )
            {
                // Set a permanent cookie, which will generally last a month without being updated
                $authState->ubbuid = $ubbuid;
                $authState->ubbpw = $ubbpw;
                $authState->update();
            }

            session_regenerate_id(false);

            redirect('index.php', 0);
        }
        else
        {
            $errmsg = $this->updateLoginCounters();
        }
        
        return $errmsg;
    }

    function logout($url = 'index.php', $delay = 0)
    {
        global $db, $onlinetime, $cookiepath, $cookiedomain;
        
        $query = $db->query("DELETE FROM " . X_PREFIX . "whosonline WHERE username = '$self[uid]'");

        if (isset($_COOKIE['ubbstate'])) {
        	setcookie("ubbstate", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['ubbuser'])) {
        	setcookie("ubbuser", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['ubbpw'])) {
        	setcookie("ubbpw", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['ubbuid'])) {
        	setcookie("ubbuid", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['oldtopics'])) {
        	setcookie("oldtopics", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['ubblva'])) {
        	setcookie("ubblva", '', 0, $cookiepath, $cookiedomain);
        }
        if (isset($_COOKIE['ubblvb'])) {
        	setcookie("ubblvb", '', 0, $cookiepath, $cookiedomain);
        }

        // loop trough all password-forum-cookies and remove them
        foreach ($_COOKIE as $key => $val)
        {
            if (preg_match('#^fidpw([0-9]+)$#', $key))
            {
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
?>
