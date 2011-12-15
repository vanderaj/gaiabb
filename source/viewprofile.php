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

// Safe to use without global.inc.php
define('DEBUG_REG', true);
define('ROOT', './');

require_once(ROOT.'header.php');
require_once(ROOTINC.'theme.inc.php');

loadtpl(
'viewprofile',
'viewprofile_email',
'viewprofile_aka',
'viewprofile_sig',
'viewprofile_pm'
);

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();
smcwcache();

eval('$css = "'.template('css').'";');

nav('<a href="memberlist.php?action=list">'.$lang['textmemberlist'].'</a>');
nav($lang['textviewpro']);
btitle($lang['textmemberlist']);
btitle($lang['textviewpro']);

if (X_GUEST)
{
    error($lang['textnoaction']);
}

$memberinfo = array();

$memberid = getInt('memberid');
if ($memberid > 0)
{
    $memberinfo = $db->fetch_array($db->query("SELECT * FROM ".X_PREFIX."members WHERE uid='$memberid'"));
    $member = $memberinfo['username'];
}
else
{
   error($lang['nomember']);
}

$db->query("UPDATE ".X_PREFIX."members SET views = views+1 WHERE username = '$member'");
if ($memberinfo['status'] == 'Administrator' || $memberinfo['status'] == 'Super Administrator' || $memberinfo['status'] == 'Super Moderator' || $memberinfo['status'] == 'Moderator')
{
    $limit = "title = '$memberinfo[status]'";
}
else
{
    $limit = "posts <= '$memberinfo[postnum]' AND title!='Super Administrator' AND title!='Administrator' AND title!='Super Moderator' AND title!='Moderator'";
}
$rank = $db->fetch_array($db->query("SELECT * FROM ".X_PREFIX."ranks WHERE $limit ORDER BY posts DESC LIMIT 1"));

if ($memberinfo['uid'] == '')
{
    error($lang['nomember']);
}
else
{
    eval('echo "'.template('header').'";');

    $encodeuser = rawurlencode($memberinfo['username']);
    $daysreg = ($onlinetime - $memberinfo['regdate']) / (24*3600);
    if ($daysreg > 1)
    {
        $tpd = $memberinfo['threadnum'] / $daysreg;
        $tpd = round($tpd, 2);
        $ppd = $memberinfo['postnum'] / $daysreg;
        $ppd = round($ppd, 2);
    }
    else
    {
        $tpd = $memberinfo['threadnum'];
        $ppd = $memberinfo['postnum'];
    }

    $icon = $pre = $suff = '';
    switch ($memberinfo['status'])
    {
        case 'Super Administrator':
            if ($THEME['riconstatus'] == 'on')
            {
                $icon = '<img src="'.$THEME['ricondir'].'/online_supadmin.gif" alt="'.$lang['ranksupadmin'].'" title="'.$lang['ranksupadmin'].'" border="0px" />';
                $pre = '<span style="color:'.$THEME['spacolor'].'"><strong><u><em>';
                $suff = '</em></u></strong></span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            else
            {
                $icon = '';
                $pre = '<span style="color:'.$THEME['spacolor'].'"><strong><u><em>';
                $suff = '</em></u></strong></span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            break;
        case 'Administrator':
            if ($THEME['riconstatus'] == 'on')
            {
                $icon = '<img src="'.$THEME['ricondir'].'/online_admin.gif" alt="'.$lang['rankadmin'].'" title="'.$lang['rankadmin'].'" border="0px" />';
                $pre = '<span style="color:'.$THEME['admcolor'].'"><strong><u>';
                $suff = '</u></strong></span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            else
            {
                $icon = '';
                $pre = '<span style="color:'.$THEME['admcolor'].'"><strong><u>';
                $suff = '</u></strong></span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            break;
        case 'Super Moderator':
            if ($THEME['riconstatus'] == 'on')
            {
                $icon = '<img src="'.$THEME['ricondir'].'/online_supmod.gif" alt="'.$lang['ranksupmod'].'" title="'.$lang['ranksupmod'].'" border="0px" />';
                $pre = '<span style="color:'.$THEME['spmcolor'].'"><em><strong>';
                $suff = '</strong></em></span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            else
            {
                $icon = '';
                $pre = '<span style="color:'.$THEME['spmcolor'].'"><em><strong>';
                $suff = '</strong></em></span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            break;
        case 'Moderator':
            if ($THEME['riconstatus'] == 'on')
            {
                $icon = '<img src="'.$THEME['ricondir'].'/online_mod.gif" alt="'.$lang['rankmod'].'" title="'.$lang['rankmod'].'" border="0px" />';
                $pre = '<span style="color:'.$THEME['modcolor'].'"><strong>';
                $suff = '</strong></span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            else
            {
                $icon = '';
                $pre = '<span style="color:'.$THEME['modcolor'].'"><strong>';
                $suff = '</strong></span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            break;
        default:
            if ($THEME['riconstatus'] == 'on')
            {
                $icon = '<img src="'.$THEME['ricondir'].'/online_mem.gif" alt="'.$lang['rankmem'].'" title="'.$lang['rankmem'].'" border="0px" />';
                $pre = '<span style="color:'.$THEME['memcolor'].'">';
                $suff = '</span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            else
            {
                $icon = '';
                $pre = '<span style="color:'.$THEME['memcolor'].'">';
                $suff = '</span>';
                $memstatus = $icon.''.$pre.''.$memberinfo['username'].''.$suff;
            }
            break;
    }

    $memberinfo['regdate'] = gmdate($self['dateformat'], $memberinfo['regdate'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);

    if (!empty($memberinfo['theme']) && $memberinfo['theme'] != 0)
    {
        $membertheme = ${'theme'.$memberinfo['theme']};
    }
    else
    {
        $membertheme = ${'theme'.$CONFIG['theme']} . $lang['defaulttheme'];;
    }

    if (strpos($memberinfo['site'], 'http') === false)
    {
        $memberinfo['site'] = "http://$memberinfo[site]";
    }

    if ($memberinfo['site'] != 'http://')
    {
        $memberinfo['site'] = censor($memberinfo['site']);
        $memberinfo['site'] = '<a href="'.$memberinfo['site'].'" target="_blank">'.$memberinfo['site'].'</a>';
    }
    else
    {
        $memberinfo['site'] = $lang['profilenoinformation'];
    }

    if (strpos($memberinfo['blog'], 'http') === false)
    {
        $memberinfo['blog'] = "http://$memberinfo[blog]";
    }

    if ($memberinfo['blog'] != 'http://')
    {
        $memberinfo['blog'] = censor($memberinfo['blog']);
        $memberinfo['blog'] = '<a href="'.$memberinfo['blog'].'" target="_blank">'.$memberinfo['blog'].'</a>';
    }
    else
    {
        $memberinfo['blog'] = $lang['profilenoinformation'];
    }

    if (!empty($rank['avatarrank']))
    {
        $rank['avatarrank'] = '<img src="'.$rank['avatarrank'].'" alt="'.$lang['Rank_Avatar_Alt'].'" title="'.$lang['Rank_Avatar_Alt'].'" border="0px" />';
    }
    else
    {
        $rank['avatarrank'] = '';
    }

    if (!empty($memberinfo['avatar']))
    {
        $memberinfo['avatar'] = censor($memberinfo['avatar']);
        $memberinfo['avatar'] = '<img src="'.$memberinfo['avatar'].'" alt="'.$lang['altavatar'].'" title="'.$lang['altavatar'].'" border="0px" />';
    }
    else
    {
        $memberinfo['avatar'] = '<img src="./images/no_avatar.gif" alt="'.$lang['altnoavatar'].'" title="'.$lang['altnoavatar'].'" border="0px" />';
    }

    if (!empty($memberinfo['photo']))
    {
        $memberinfo['photo'] = censor($memberinfo['photo']);
        $memberinfo['photo'] = '<img src="'.$memberinfo['photo'].'" alt="'.$lang['photoalt'].'" title="'.$lang['photoalt'].'" border="0px" />';
    }
    else
    {
        $memberinfo['photo'] = '<img src="./images/no_avatar.gif" alt="'.$lang['altnophoto'].'" title="'.$lang['altnophoto'].'" border="0px" />';
    }

    $akablock = '';
    if (!empty($memberinfo['firstname']) || !empty($memberinfo['lastname']) && $memberinfo['showname'] == 'yes')
    {
        $memberinfo['firstname'] = censor($memberinfo['firstname']);
        $memberinfo['lastname'] = censor($memberinfo['lastname']);
        eval('$akablock = "'.template('viewprofile_aka').'";');
    }

    switch ($memberinfo['status'])
    {
        case 'Moderator':
            $star = 'star_mod.gif';
            break;
        case 'Super Moderator':
            $star = 'star_supmod.gif';
            break;
        case 'Administrator':
            $star = 'star_admin.gif';
            break;
        case 'Super Administrator':
            $star = 'star_supadmin.gif';
            break;
        default:
            $star = 'star.gif';
            break;
    }
    $stars = str_repeat('<img src="'.$THEME['imgdir'].'/'.$star.'" alt="*" title="*" border="0px" />', $rank['stars']);

    $q = $db->fetch_array($db->query("SELECT invisible FROM ".X_PREFIX."whosonline WHERE username='$member'"));
    if (!$q)
    {
        $onlinenow = $lang['memberisoff'];
    }
    else
    {
        switch ($q['invisible'])
        {
            case '1':
                $onlinenow = X_ADMIN ? $lang['memberison'].' ('.$lang['hidden'].')' : $lang['memberisoff'];
                break;
            case '0':
                $onlinenow = $lang['memberison'];
                break;
            default:
                $onlinenow = $lang['memberisoff'];
                break;
        }
    }

    if (!empty($memberinfo['customstatus']))
    {
        $showtitle = $rank['title'];
        $customstatus = '<br />'.censor($memberinfo['customstatus']);
    }
    else
    {
        $showtitle = $rank['title'];
        $customstatus = '';
    }

    if (!($memberinfo['lastvisit'] > 0))
    {
        $lastmembervisittext = $lang['textpendinglogin'];
    }
    else
    {
        $lastvisitdate = gmdate($self['dateformat'], $memberinfo['lastvisit'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $lastvisittime = gmdate($self['timecode'], $memberinfo['lastvisit'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $lastmembervisittext = $lastvisitdate.' '.$lang['textat'].' '.$lastvisittime;
    }

    $query = $db->query("SELECT COUNT(tid) FROM ".X_PREFIX."threads");
    $threads = $db->result($query, 0);
    $db->free_result($query);

    $threadtot = $threads;
    if ($threadtot == 0 || $memberinfo['threadnum'] == 0)
    {
        $t_percent = 0;
    }
    else
    {
        $t_percent = $memberinfo['threadnum']*100/$threadtot;
        $t_percent = round($t_percent, 2);
    }

    $query = $db->query("SELECT COUNT(pid) FROM ".X_PREFIX."posts");
    $posts = $db->result($query, 0);
    $db->free_result($query);

    $posttot = $posts;
    if ($posttot == 0 || $memberinfo['postnum'] == 0)
    {
        $percent = 0;
    }
    else
    {
        $percent = ($memberinfo['postnum']*100)/$posttot;
        $percent = round($percent, 2);
    }

    if (!empty($memberinfo['bio']))
    {
        $memberinfo['bio'] = postify($memberinfo['bio']);
        $memberinfo['bio'] = censor($memberinfo['bio']);
    }
    else
    {
        $memberinfo['bio'] = $lang['profilenoinformation'];
    }

    $emailblock = '';
    if (X_MEMBER && !empty($memberinfo['email']) && $memberinfo['showemail'] == 'yes')
    {
        $memberinfo['email'] = censor($memberinfo['email']);
        eval('$emailblock = "'.template('viewprofile_email').'";');
    }

    $pmblock = '';
    if (X_MEMBER && !($CONFIG['pmstatus'] == 'off' && isset($self['status']) && $self['status'] == 'Member'))
    {
        eval('$pmblock = "'.template('viewprofile_pm').'";');
    }

    $sigblock = '';
    if (!empty($memberinfo['sig']))
    {
        $memberinfo['sig'] = postify($memberinfo['sig']);
        $memberinfo['sig'] = censor($memberinfo['sig']);
        eval('$sigblock = "'.template('viewprofile_sig').'";');
    }

    $admin_edit = NULL;
    if (X_SADMIN)
    {
        $admin_edit = ' - '.$lang['adminoption'].' <a href="editprofile.php?memberid='.$memberinfo['uid'].'">'.$lang['admin_edituseraccount'].'</a>';
    }

    if (!empty($memberinfo['mood']))
    {
        $memberinfo['mood'] = postify($memberinfo['mood'], 'no', 'no', 'yes', 'yes', false, 'yes', 'yes');
        $memberinfo['mood'] = censor($memberinfo['mood']);
    }
    else
    {
        $memberinfo['mood'] = $lang['profilenoinformation'];
    }

    if (!empty($memberinfo['location']))
    {
        $memberinfo['location'] = censor($memberinfo['location']);
    }
    else
    {
        $memberinfo['location'] = $lang['profilenoinformation'];
    }

    if (!empty($memberinfo['aim']))
    {
        $memberinfo['aim'] = censor($memberinfo['aim']);
    }
    else
    {
        $memberinfo['aim'] = $lang['profilenoinformation'];
    }

    if (!empty($memberinfo['icq']))
    {
        $memberinfo['icq'] = censor($memberinfo['icq']);
        $memberinfo['icq'] = '<a href="http://web.icq.com/whitepages/about_me/1,,,00.html?Uin='.$memberinfo['icq'].'" target="_blank">'.$memberinfo['icq'].'</a>';
    }
    else
    {
        $memberinfo['icq'] = $lang['profilenoinformation'];
    }

    if (!empty($memberinfo['yahoo']))
    {
        $memberinfo['yahoo'] = censor($memberinfo['yahoo']);
        $memberinfo['yahoo'] = '<a href="http://profiles.yahoo.com/'.$memberinfo['yahoo'].'" target="_blank">'.$memberinfo['yahoo'].'</a>';
    }
    else
    {
        $memberinfo['yahoo'] = $lang['profilenoinformation'];
    }

    if (!empty($memberinfo['msn']))
    {
        $memberinfo['msn'] = censor($memberinfo['msn']);
        $memberinfo['msn'] = '<a href="http://members.msn.com/'.$memberinfo['msn'].'" target="_blank">'.$memberinfo['msn'].'</a>';
    }
    else
    {
        $memberinfo['msn'] = $lang['profilenoinformation'];
    }

    if (!empty($memberinfo['occupation']))
    {
        $memberinfo['occupation'] = censor($memberinfo['occupation']);
    }
    else
    {
        $memberinfo['occupation'] = $lang['profilenoinformation'];
    }

    if (!empty($memberinfo['bday']))
    {
        $memberinfo['bday'] = censor($memberinfo['bday']);
    }
    else
    {
        $memberinfo['bday'] = $lang['profilenoinformation'];
    }

    $restrict = '';
    switch ($self['status'])
    {
        case 'Member':
            $restrict .= " f.password='' AND f.private!='3' AND";
        case 'Moderator':
        case 'Super Moderator':
            $restrict .= " f.password='' AND f.private!='2' AND";
        case 'Administrator':
            $restrict .= " f.userlist='' AND f.password='' AND";
        case 'Super Administrator':
            break;
        default:
            $restrict .= " f.private!='5' AND f.private!='3' AND f.private!='2' AND f.userlist='' AND f.password='' AND";
            break;
    }

    $query = $db->query("SELECT f.name, p.fid, COUNT(DISTINCT p.pid) as posts FROM ".X_PREFIX."posts p LEFT JOIN ".X_PREFIX."forums f ON p.fid=f.fid WHERE $restrict p.author='$member' GROUP BY p.fid ORDER BY posts DESC LIMIT 1");
    $forum = $db->fetch_array($query);
    $db->free_result($query);

    if ($forum['posts'] < 1 || $memberinfo['postnum'] < 1)
    {
        $topforum = $lang['textnopostsyet'];
    }
    else
    {
        $topforum = '<a href="viewforum.php?fid='.intval($forum['fid']).'">'.$forum['name'].'</a> ('.intval($forum['posts']).' '.$lang['textdeleteposts'].') [ '.round(($forum['posts']/$memberinfo['postnum'])*100, 1).'% '.$lang['textoftotposts'].' ]';
    }

    $query = $db->query("SELECT t.tid, t.subject, p.dateline FROM (".X_PREFIX."posts p, ".X_PREFIX."threads t) LEFT JOIN ".X_PREFIX."forums f ON p.fid=f.fid WHERE $restrict t.author='$member' AND p.tid=t.tid ORDER BY t.tid DESC LIMIT 1");
    if ($thread = $db->fetch_array($query))
    {
        $lastthreaddate = gmdate($self['dateformat'], $thread['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $lastthreadtime = gmdate($self['timecode'], $thread['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $lastthreadtext = $lastthreaddate.' '.$lang['textat'].' '.$lastthreadtime;
        $thread['subject'] = shortenString(censor($thread['subject']), 80, X_SHORTEN_SOFT|X_SHORTEN_HARD, '...');
        $lastthread = '<a href="viewtopic.php?tid='.intval($thread['tid']).'">'.$thread['subject'].'</a> ('.$lastthreadtext.')';
    }
    else
    {
        $lastthread = $lang['textnothreadsyet'];
    }
    $db->free_result($query);

    $query = $db->query("SELECT t.tid, t.subject, p.dateline, p.pid FROM (".X_PREFIX."posts p, ".X_PREFIX."threads t) LEFT JOIN ".X_PREFIX."forums f ON p.fid=f.fid WHERE $restrict p.author='$member' AND p.tid=t.tid ORDER BY p.dateline DESC LIMIT 1");
    if ($post = $db->fetch_array($query))
    {
        $posts = $db->result($db->query("SELECT COUNT(pid) FROM ".X_PREFIX."posts WHERE tid = '$post[tid]' AND pid < '$post[pid]'"), 0)+1;

        validatePpp();

        $page = quickpage($posts, $self['ppp']);

        $lastpostdate = gmdate($self['dateformat'], $post['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $lastposttime = gmdate($self['timecode'], $post['dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $lastposttext = $lastpostdate.' '.$lang['textat'].' '.$lastposttime;
        $post['subject'] = shortenString(censor($post['subject']), 80, X_SHORTEN_SOFT|X_SHORTEN_HARD, '...');
        $lastpost = '<a href="viewtopic.php?tid='.intval($post['tid']).'&amp;page='.$page.'#pid'.intval($post['pid']).'">'.$post['subject'].'</a> ('.$lastposttext.')';
    }
    else
    {
        $lastpost = $lang['textnopostsyet'];
    }
    $db->free_result($query);

    $lang['searchusermsg'] = str_replace('*USER*', $memberinfo['username'], $lang['searchusermsg']);

    eval('echo stripslashes("'.template('viewprofile').'");');
}

loadtime();
eval('echo "'.template('footer').'";');
?>