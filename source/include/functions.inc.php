<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB and XMB Forum 2 (BBCode)
 * Copyright (c) 2001 - 2012 The XMB Development Team
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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

function nav($add = false)
{
    global $db, $navigation, $navsymbol, $THEME;

    if (!$add) {
        $navigation = '';
    } else {
        $navigation .= ($THEME['navsymbol'] ? ' ' . $THEME['navsymbol'] . ' ' : '') . $add;
    }
}

function btitle($btadd = false, $btdash = true)
{
    global $db, $btitle;

    if (!$btadd) {
        $btitle = '';
    } else {
        $btitle .= ($btdash ? ' - ' : '') . $btadd;
    }
}

function template($name)
{
    global $db, $CONFIG;

    if (($template = templatecache(X_CACHE_GET, $name)) === false) {
        $query = $db->query("SELECT template FROM " . X_PREFIX . "templates WHERE name = '$name'");
        if ($db->num_rows($query) == 1) {
            if (X_SADMIN && DEBUG) {
                trigger_error('Efficiency Notice: The template `' . $name . '` was not preloaded.', E_USER_NOTICE);
            }
            $gettemplate = $db->fetch_array($query);
            templatecache(X_CACHE_PUT, $name, $gettemplate['template']);
            $template = $gettemplate['template'];
        } else {
            if (X_SADMIN && DEBUG) {
                trigger_error('Efficiency Warning: The template `' . $name . '` could not be found.', E_USER_WARNING);
            }
        }
        $db->free_result($query);
    }

    $template = str_replace("\\'", "'", $template);

    if ($name != 'phpinclude' && $CONFIG['comment'] == 'on') {
        return "<!--Begin Template: $name -->\n$template\n<!-- End Template: $name -->";
    } else {
        return $template;
    }
}

function templatecache($type = X_CACHE_GET, $name, $data = '')
{
    static $cache;
    $retval = false;
    switch ($type) {
        case X_CACHE_GET:
            if (isset($cache[$name])) {
                $retval = $cache[$name];
            }
            break;
        case X_CACHE_PUT:
            $cache[$name] = $data;
            $retval = true;
            break;
    }

    return $retval;
}

function loadtpl($tpl)
{
    global $db;

    $num = func_num_args();
    if ($num < 1) {
        echo 'Not enough arguments given to loadtpl() on line: ' . __LINE__;
        return false;
    } else {
        $namesarray = func_get_args();
        $namesarray = array_unique(array_merge(func_get_args(), array(
            'css',
            'error',
            'footer',
            'footer_querynum',
            'footer_load',
            'footer_phpsql',
            'footer_totaltime',
            'header',
            'message',
            'meta_tags',
            'shadow',
            'shadow2'
        )));
        $sql = "'" . implode("', '", $namesarray) . "'";
        $query = $db->query("SELECT name, template FROM " . X_PREFIX . "templates WHERE name IN ($sql)");
        while (($template = $db->fetch_array($query)) != false) {
            templatecache(X_CACHE_PUT, $template['name'], $template['template']);
        }
        $db->free_result($query);
    }
}

function censor($txt, $ignorespaces = false)
{
    global $censorcache;

    if (is_array($censorcache)) {
        if (count($censorcache) > 0) {
            reset($censorcache);

            while ((list($find, $replace) = each($censorcache)) != false) {
                if ($ignorespaces === true) {
                    $txt = str_replace($find, $replace, $txt);
                } else {
                    $txt = preg_replace('#(^|[[:space:].,!?[]{}()])(' . preg_quote($find) . ')($|[[:space:].,!?()[]{}])#si', "$1" . $replace . "$3", $txt);
                }
            }
        }
    }
    return $txt;
}

function smile($txt)
{
    global $THEME, $smiliesnum, $smiliecache;

    if ($smiliesnum > 0) {
        reset($smiliecache);
        foreach ($smiliecache as $code => $url) {
            $txt = str_replace($code, '<img src="' . $THEME['smdir'] . '/' . $url . '" border="0" alt="' . $code . '" title="' . $code . '" />', $txt);
        }
    }
    return $txt;
}

function createAbsFSizeFromRel($rel = 0)
{
    global $THEME;
    static $cachedFs;

    $res = '';
    if (!is_array($cachedFs) || count($cachedFs) != 2) {
        preg_match('#([0-9]+)([a-z]+)?#i', $THEME['fontsize'], $res);
        $cachedFs[0] = $res[1];
        $cachedFs[1] = $res[2];

        if (empty($cachedFs[1])) {
            $cachedFs[1] = 'px';
        }
    }
    $o = ($rel + $cachedFs[0]) . $cachedFs[1];
    return $o;
}

function check_image_size($matches)
{
    global $CONFIG, $lang;

    $CONFIG['bbc_maxwd'] = (int)$CONFIG['bbc_maxwd'];
    $CONFIG['bbc_maxht'] = (int)$CONFIG['bbc_maxht'];

    if (empty($matches[3]))
        $matches[3] = '';

    $imgurl = $matches[1] . '://' . $matches[2] . $matches[3];
    if ((list($width, $height) = getimagesize($imgurl)) != false) {
        $w_ratio = $CONFIG['bbc_maxwd'] / $width;
        $h_ratio = $CONFIG['bbc_maxht'] / $height;

        if (($height <= $CONFIG['bbc_maxht']) && ($width <= $CONFIG['bbc_maxwd'])) {
            $n_height = $height;
            $n_width = $width;
        } else
            if (($w_ratio * $height) < $CONFIG['bbc_maxht']) {
                $n_height = ceil($w_ratio * $height);
                $n_width = $CONFIG['bbc_maxwd'];
            } else {
                $n_height = $CONFIG['bbc_maxht'];
                $n_width = ceil($h_ratio * $width);
            }
        $replace = '[img=' . $n_width . 'x' . $n_height . ']' . $imgurl . '[/img]';
    } else {
        $replace = '[bad img]' . $imgurl . '[/bad img]';
    }
    return $replace;
}

function decode_entities($text)
{
    if (!empty($text)) {
        $text = html_entity_decode($text, ENT_HTML5, "UTF-8"); // Requires PHP 5.4 and later

        $text = preg_replace_callback("/(&#[0-9]+;)/", function ($md) {
            return mb_convert_encoding($md[1], "UTF-8", "HTML-ENTITIES");
        }, $text);
        $text = preg_replace_callback("/&#x([a-f0-9]+);/", function ($mh) {
            return mb_convert_encoding($mh[1], "UTF-8", "HTML-ENTITIES");
        }, $text);
    }
    return $text;
}

/**
 * Full parsing of [code] tags.
 *
 * @param string $message
 * @return array Odd number indexes contain the code block contents.
 */
function bbcodeCode($message)
{
    $counter = 0;
    $offset = 0;
    $done = FALSE;
    $messagearray = array();
    while (!$done) {
        $pos = strpos($message, '[code]', $offset);
        if (FALSE === $pos) {
            $messagearray[$counter] = substr($message, $offset);
            $messagearray[$counter] = str_replace('[/code]', '&#091;/code]', $messagearray[$counter]);
            if ($counter > 1) {
                $messagearray[$counter] = '[/code]' . $messagearray[$counter];
            }
            $done = TRUE;
        } else {
            $pos += strlen('[code]');
            $messagearray[$counter] = substr($message, $offset, $pos - $offset);
            $messagearray[$counter] = str_replace('[/code]', '&#091;/code]', $messagearray[$counter]);
            if ($counter > 1) {
                $messagearray[$counter] = '[/code]' . $messagearray[$counter];
            }
            $counter++;
            $offset = $pos;
            $pos = strpos($message, '[/code]', $offset);
            if (FALSE === $pos) {
                $messagearray[$counter] = substr($message, $offset);
                $counter++;
                $messagearray[$counter] = '[/code]';
                $done = TRUE;
            } else {
                $messagearray[$counter] = substr($message, $offset, $pos - $offset);
                $counter++;
                $offset = $pos + strlen('[/code]');
            }
        }
    }
    return $messagearray;
}

/**
 * Wraps long lines but avoids certain elements.
 *
 * @since 1.9.11.12
 * @param string $input Read/Write Variable
 */
function post_wordwrap(&$input)
{
    $br = trim(nl2br("\n"));
    $messagearray = preg_split("#<!-- nobr -->|<!-- /nobr -->#", $input);
    for ($i = 0; $i < sizeof($messagearray); $i++) {
        if ($i % 2 == 0) {
            $messagearray[$i] = explode($br, $messagearray[$i]);
            foreach ($messagearray[$i] as $key => $val) {
                $messagearray[$i][$key] = wordwrap($val, 150, "\n", TRUE);
            }
            $messagearray[$i] = implode($br, $messagearray[$i]);
        } // else inside nobr block
    }
    $input = implode('', $messagearray);
}

/**
 * Guarantees each BBCode has an equal number of open and close tags.
 *
 * @since 1.9.11.12
 * @param string $message Read/Write Variable
 * @param array $regex Indexed by code name
 */
function bbcodeBalanceTags(&$message, $regex)
{
    foreach ($regex as $code => $pattern) {
        if (is_array($pattern)) {
            $open = 0;
            foreach ($pattern as $subpattern) {
                $open += preg_match_all($subpattern, $message, $matches);
            }
        } else {
            $open = preg_match_all($pattern, $message, $matches);
        }
        $close = substr_count($message, "[/$code]");
        $open -= $close;
        if ($open > 0) {
            $message .= str_repeat("[/$code]", $open);
        } elseif ($open < 0) {
            $message = preg_replace("@\\[/$code]@", "&#091;/$code]", $message, -$open);
        }
    }
}

function bbcode(&$message, $allowimgcode, $allowurlcode)
{
    global $lang, $imgdir;

    //Balance simple tags.
    $begin = array(
        0 => '[b]',
        1 => '[i]',
        2 => '[u]',
        3 => '[marquee]',
        4 => '[blink]',
        5 => '[strike]',
        6 => '[quote]',
        8 => '[list]',
        9 => '[list=1]',
        10 => '[list=a]',
        11 => '[list=A]',
    );

    $end = array(
        0 => '[/b]',
        1 => '[/i]',
        2 => '[/u]',
        3 => '[/marquee]',
        4 => '[/blink]',
        5 => '[/strike]',
        6 => '[/quote]',
        8 => '[/list]',
        9 => '[/list=1]',
        10 => '[/list=a]',
        11 => '[/list=A]',
    );

    foreach ($begin as $key => $value) {
        $check = substr_count($message, $value) - substr_count($message, $end[$key]);
        if ($check > 0) {
            $message .= str_repeat($end[$key], $check);
        } else if ($check < 0) {
            $message = str_repeat($value, abs($check)) . $message;
        }
    }

    // Balance regex tags.
    $regex = array();
    $regex['align'] = "@\\[align=(left|center|right|justify)\\]@i";
    $regex['font'] = "@\\[font=([a-z\\r\\n\\t 0-9]+)\\]@i";
    $regex['rquote'] = "@\\[rquote=(\\d+)&(?:amp;)?tid=(\\d+)&(?:amp;)?author=([^\\[\\]<>]+)\\]@s";
    $regex['size'] = "@\\[size=([+-]?[0-9]{1,2})\\]@";
    $regex['color'] = array();
    $regex['color']['named'] = "@\\[color=(White|Black|Red|Yellow|Pink|Green|Orange|Purple|Blue|Beige|Brown|Teal|Navy|Maroon|LimeGreen|aqua|fuchsia|gray|silver|lime|olive)\\]@i";
    $regex['color']['hex'] = "@\\[color=#([\\da-f]{3,6})\\]@i";
    $regex['color']['rgb'] = "@\\[color=rgb\\(([\\s]*[\\d]{1,3}%?[\\s]*,[\\s]*[\\d]{1,3}%?[\\s]*,[\\s]*[\\d]{1,3}%?[\\s]*)\\)\\]@i";

    bbcodeBalanceTags($message, $regex);

    // Replace simple tags.
    $find = array(
        0 => '[b]',
        1 => '[/b]',
        2 => '[i]',
        3 => '[/i]',
        4 => '[u]',
        5 => '[/u]',
        6 => '[marquee]',
        7 => '[/marquee]',
        8 => '[blink]',
        9 => '[/blink]',
        10 => '[strike]',
        11 => '[/strike]',
        12 => '[quote]',
        13 => '[/quote]',
        14 => '[code]',
        15 => '[/code]',
        16 => '[list]',
        17 => '[/list]',
        18 => '[list=1]',
        19 => '[list=a]',
        20 => '[list=A]',
        21 => '[/list=1]',
        22 => '[/list=a]',
        23 => '[/list=A]',
        24 => '[*]',
        25 => '[/color]',
        26 => '[/font]',
        27 => '[/size]',
        28 => '[/align]',
        29 => '[/rquote]'
    );

    $replace = array(
        0 => '<strong>',
        1 => '</strong>',
        2 => '<em>',
        3 => '</em>',
        4 => '<u>',
        5 => '</u>',
        6 => '<marquee>',
        7 => '</marquee>',
        8 => '<blink>',
        9 => '</blink>',
        10 => '<strike>',
        11 => '</strike>',
        12 => '</font> <!-- nobr --><table align="center" class="quote" cellspacing="0" cellpadding="0"><tr><td class="quote">' . $lang['textquote'] . '</td></tr><tr><td class="quotemessage"><!-- /nobr -->',
        13 => ' </td></tr></table><font class="mediumtxt">',
        14 => '</font> <!-- nobr --><table align="center" class="code" cellspacing="0" cellpadding="0"><tr><td class="code">' . $lang['textcode'] . '</td></tr><tr><td class="codemessage"><code>',
        15 => '</code></td></tr></table><font class="mediumtxt"><!-- /nobr -->',
        16 => '<ul type="square">',
        17 => '</ul>',
        18 => '<ol type="1">',
        19 => '<ol type="A">',
        20 => '<ol type="A">',
        21 => '</ol>',
        22 => '</ol>',
        23 => '</ol>',
        24 => '<li />',
        25 => '</span>',
        26 => '</span>',
        27 => '</span>',
        28 => '</div>',
        29 => ' </td></tr></table><font class="mediumtxt">'
    );

    $message = str_replace($find, $replace, $message);

    // Replace regex tags.
    $patterns = array();
    $replacements = array();

    $patterns[] = $regex['rquote'];
    $replacements[] = '</font> <!-- nobr --><table align="center" class="quote" cellspacing="0" cellpadding="0"><tr><td class="quote">' . $lang['textquote'] . ' <a href="viewthread.php?tid=$2&amp;goto=search&amp;pid=$1" rel="nofollow">' . $lang['origpostedby'] . ' $3 &nbsp;<img src="' . $imgdir . '/lastpost.gif" border="0" alt="" style="vertical-align: middle;" /></a></td></tr><tr><td class="quotemessage"><!-- /nobr -->';
    $patterns[] = $regex['color']['named'];
    $replacements[] = '<span style="color: $1;">';
    $patterns[] = $regex['color']['hex'];
    $replacements[] = '<span style="color: #$1;">';
    $patterns[] = $regex['color']['rgb'];
    $replacements[] = '<span style="color: rgb($1);">';
    $patterns[] = $regex['font'];
    $replacements[] = '<span style="font-family: $1;">';
    $patterns[] = $regex['align'];
    $replacements[] = '<div style="text-align: $1;">';

    $patterns[] = "@\\[pid=(\\d+)&amp;tid=(\\d+)](.*?)\\[/pid]@si";
    $replacements[] = '<a <!-- nobr -->href="viewthread.php?tid=$2&amp;goto=search&amp;pid=$1"><strong><!-- /nobr -->$3</strong> &nbsp;<img src="' . $imgdir . '/lastpost.gif" border="0" alt="" style="vertical-align: middle;" /></a>';

    if ($allowimgcode != 'no' && $allowimgcode != 'off') {
        if (false == stripos($message, 'javascript:')) {
            $patterns[] = '#\[img\](http[s]?|ftp[s]?){1}://([:a-z\\./_\-0-9%~]+){1}\[/img\]#Smi';
            $replacements[] = '<img <!-- nobr -->src="\1://\2\3"<!-- /nobr --> border="0" alt="" />';
            $patterns[] = "#[img=([0-9]*?){1}x([0-9]*?)](http[s]?|ftp[s]?){1}://([:~a-z\\./0-9_-%]+){1}(?[a-z=0-9&_-;~]*)?[/img]#Smi";
            $replacements[] = '<img width="\1" height="\2" <!-- nobr -->src="\3://\4\5"<!-- /nobr --> alt="" border="0" />';
        }
    }

    $patterns[] = "#\\[email\\]([^\"'<>]+?)\\[/email\\]#mi";
    $replacements[] = '<a href="mailto:\1">\1</a>';
    $patterns[] = "#\\[email=([^\"'<>\\[\\]]+)\\](.+?)\\[/email\\]#mi";
    $replacements[] = '<a href="mailto:\1">\2</a>';

    $patterns[] = "#\[youtube\]([^\"'<>]*?)\[/youtube\]#Smi";
    $replacements[] = '<object type="application/x-shockwave-flash" style="width:425px; height:350px;" data="http://www.youtube.com/v/\1"><param name="movie" value="http://www.youtube.com/v/\1" /></object>';

    $message = preg_replace($patterns, $replacements, $message);

    $message = preg_replace_callback($regex['size'], 'bbcodeSizeTags', $message);

    if ($allowurlcode) {
        /*
         This block positioned last so that bare URLs may appear adjacent to BBCodes without matching on square braces.
         Regexp explanation: match strings surrounded by whitespace or () or ><.  Do not include the surrounding chars.
         Group 1 will be identical to the full match so that the callback function can be reused for [url] codes.
         */
        $regexp = '(?<=^|\s|>|\()'
            . '('
            . '(?:(?:http|ftp)s?://|www)'
            . '[-a-z0-9.]+\.[a-z]{2,4}'
            . '[^\s()"\'<>\[\]]*'
            . ')'
            . '(?=$|\s|<|\))';
        $message = preg_replace_callback("#$regexp#Smi", 'bbcodeLongURLs', $message);

        //[url]http://www.example.com/[/url]
        //[url]www.example.com[/url]
        $message = preg_replace_callback("#\[url\]([^\"'<>]+?)\[/url\]#i", 'bbcodeLongURLs', $message);

        //[url=http://www.example.com/]Lorem Ipsum[/url]
        //[url=www.example.com]Lorem Ipsum[/url]
        $message = preg_replace_callback("#\[url=([^\"'<>\[\]]+)](.*?)\[/url\]#i", 'bbcodeLongURLs', $message);
    }

    return TRUE;
}

function postify($message, $smileyoff = 'no', $bbcodeoff = 'no', $allowsmilies = 'yes', $allowbbcode = 'yes', $allowimgcode = 'yes', $ignorespaces = false, $ismood = 'no', $wrap = 'yes')
{
    $bballow = ($allowbbcode == 'yes' || $allowbbcode == 'on') ? (($bbcodeoff != 'off' && $bbcodeoff != 'yes') ? true : false) : false;
    $smiliesallow = ($allowsmilies == 'yes' || $allowsmilies == 'on') ? (($smileyoff != 'off' && $smileyoff != 'yes') ? true : false) : false;
    $allowurlcode = ($ismood != 'yes');

    if ($bballow) {
        if ($ismood == 'yes') {
            $message = str_replace(array('[rquote=', '[quote]', '[/quote]', '[code]', '[/code]', '[list]', '[/list]', '[list=1]', '[list=a]', '[list=A]', '[/list=1]', '[/list=a]', '[/list=A]', '[*]'), '_', $message);
        }

        //Remove the code block contents from $message.
        $messagearray = bbcodeCode($message);
        $message = array();
        for ($i = 0; $i < count($messagearray); $i += 2) {
            $message[$i] = $messagearray[$i];
        }
        $message = implode("<!-- code -->", $message);

        // Do BBCode
        $message = rawHTMLmessage($message, 'no');  // GaiaBB does not support raw HTML messages
        if ($smiliesallow) {
            smile($message);
        }
        bbcode($message, $allowimgcode, $allowurlcode);
        $message = nl2br($message);

        // Replace the code block contents in $message.
        if (count($messagearray) > 1) {
            $message = explode("<!-- code -->", $message);
            for ($i = 0; $i < count($message) - 1; $i++) {
                $message[$i] .= censor($messagearray[$i * 2 + 1]);
            }
            $message = implode("", $message);
        }

        if ('yes' == $wrap) {
            post_wordwrap($message);
        } else {
            $message = str_replace(array('<!-- nobr -->', '<!-- /nobr -->'), array('', ''), $message);
        }
    } else {
        $message = rawHTMLmessage($message, 'no');  // GaiaBB does not support raw HTML messages
        if ($smiliesallow) {
            smile($message);
        }
        $message = nl2br($message);
        if ('yes' == $wrap) {
            post_wordwrap($message);
        }
    }

    $message = preg_replace('#(script|about|applet|activex|chrome):#Sis', "\\1 &#058;", $message);

    return $message;
}

function forum($forum, $template)
{
    global $db, $THEME, $CONFIG, $lang, $self, $lastvisit2;
    global $oldtopics, $lastvisit, $index_subforums, $sub, $subforums, $MODERATORS, $moderators_cache;

    echo "<!-- " . print_r($forum) . " -->";

    if (empty($forum['private'])) {
        $forum['private'] = '1';    // 1 = default
    }

    if (empty($forum['userlist'])) {
        $forum['userlist'] = '';
    }

    if (!empty($forum['lp_user'])) {
        $dalast = $forum['lp_dateline'];
        if ($forum['lp_user'] != 'Anonymous') {
            if (X_MEMBER) {
                $forum['lp_user'] = '<a href="viewprofile.php?memberid=' . intval($forum['lp_uid']) . '"><strong>' . trim($forum['lp_user']) . '</strong></a>';
            } else {
                $forum['lp_user'] = '<strong>' . trim($forum['lp_user']) . '</strong>';
            }
        } else {
            $forum['lp_user'] = $lang['textanonymous'];
        }

        $lastPid = isset($forum['lp_pid']) ? $forum['lp_pid'] : 0;

        $lastpostdate = gmdate($self['dateformat'], $forum['lp_dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $lastposttime = gmdate($self['timecode'], $forum['lp_dateline'] + ($self['timeoffset'] * 3600) + $self['daylightsavings']);
        $lastpost = $lang['lastreply1'] . ' ' . $lastpostdate . ' ' . $lang['textat'] . ' ' . $lastposttime . '<br />' . $lang['textby'] . ' ' . $forum['lp_user'];
        eval('$lastpostrow = "' . template('' . $template . '_lastpost') . '";');
    }

    if (empty($forum['lastpost']) && empty($forum['lp_user'])) {
        $dalast = 0;
        $lastpost = $lang['textnever'];
        eval('$lastpostrow = "' . template('' . $template . '_nolastpost') . '";');
    }

    if ($lastvisit < $dalast && (strpos($oldtopics, '|' . $lastPid . '|') === false)) {
        $folder = '<img src="' . $THEME['imgdir'] . '/red_folder.gif" alt="' . $lang['altredfolder'] . '" title="' . $lang['altredfolder'] . '" border="0" />';
    } else {
        $folder = '<img src="' . $THEME['imgdir'] . '/folder.gif" alt="' . $lang['altfolder'] . '" title="' . $lang['altfolder'] . '" border="0" />';
    }

    if ($dalast == '') {
        $folder = '<img src="' . $THEME['imgdir'] . '/folder.gif" alt="' . $lang['altfolder'] . '" title="' . $lang['altfolder'] . '" border="0" />';
    }

    $foruminfo = '';
    if (X_SADMIN || $CONFIG['hideprivate'] == 'off' || privfcheck($forum['private'], $forum['userlist'])) {
        if (isset($forum['moderator']) && !empty($forum['moderator'])) {
            $moderators = explode(', ', $forum['moderator']);
            $forum['moderator'] = array();
            for ($num = 0; $num < count($moderators); $num++) {
                if (X_MEMBER) {
                    $mcheck = array_search(strtolower($moderators[$num]), $MODERATORS);
                    if ($mcheck === false) {
                        // Expire the mod-cache, because something's missing from it
                        $moderators_cache->expire('moderators');

                        $lpquery = $db->query("SELECT uid FROM " . X_PREFIX . "members WHERE username = '$moderators[$num]' LIMIT 1");
                        $lparray = $db->fetch_array($lpquery);
                        $forum['moderator'][] = '<option value="viewprofile.php?memberid=' . intval($lparray['uid']) . '">' . trim($moderators[$num]) . '</option>';
                    } else {
                        $forum['moderator'][] = '<option value="viewprofile.php?memberid=' . intval($mcheck) . '">' . trim($moderators[$num]) . '</option>';
                    }
                } else {
                    $forum['moderator'][] = '<option value="' . $moderators[$num] . '" disabled="disabled">' . trim($moderators[$num]) . '</option>';
                }
            }
            $forum['moderator'] = implode("\n", $forum['moderator']);
            $forum['moderator'] = stripslashes($forum['moderator']);
        }

        // create subforums on index
        $subforums = array();
        if (count($index_subforums) > 0) {
            for ($i = 0; $i < count($index_subforums); $i++) {
                $sub = $index_subforums[$i];
                if ($sub['fup'] == $forum['fid']) {
                    if (X_SADMIN || $CONFIG['hideprivate'] == 'off' || privfcheck($sub['private'], $sub['userlist'])) {
                        $subforums[] = '<a href="' . 'viewforum.php?fid=' . intval($sub['fid']) . '">' . stripslashes($sub['name']) . '</a>';
                    }
                }
            }
        }

        if (!empty($subforums)) {
            $subforums = implode(', ', $subforums);
            $subforums = '<br /><strong>' . $lang['textsubforums'] . '</strong> ' . $subforums;
        } else {
            $subforums = '';
        }

        $mouseover = '';
        if (isset($forum['fid'])) {
            $mouseover = celloverfx('viewforum.php?fid=' . intval($forum['fid']));
        }

        eval('$foruminfo = stripslashes("' . template($template) . '");');
    }
    $dalast = '';
    return $foruminfo;
}

function multi($num, $perpage, $page, $mpurl, $strict = false)
{
    $multipage = $GLOBALS['lang']['textpages'];

    $pages = quickpage($num, $perpage);

    if ($pages > 1) {
        if ($page == 0) {
            if ($pages < 4) {
                $to = $pages;
            } else {
                $to = 3;
            }
        } else
            if ($page == $pages) {
                $to = $pages;
            } else
                if ($page == $pages - 1) {
                    $to = $page + 1;
                } else
                    if ($page == $pages - 2) {
                        $to = $page + 2;
                    } else {
                        $to = $page + 3;
                    }

        if ($page >= 0 && $page <= 3) {
            $from = 1;
        } else {
            $from = $page - 3;
        }

        $to--;
        $from++;

        $string = (strpos($mpurl, '?') !== false) ? '&amp;' : '?';
        if (1 != $page) {
            $multipage .= '&nbsp;&nbsp;<u><a href="' . $mpurl . $string . 'page=1" onclick="event.cancelBubble=true">1</a></u>';
            if (2 < $from) {
                $multipage .= '&nbsp;&nbsp;..';
            }
        } else {
            $multipage .= '&nbsp;&nbsp;<strong>1</strong>';
        }

        for ($i = $from; $i <= $to; $i++) {
            if ($i != $page) {
                $multipage .= '&nbsp;&nbsp;<u><a href="' . $mpurl . $string . 'page=' . $i . '" onclick="event.cancelBubble=true">' . $i . '</a></u>';
            } else {
                $multipage .= '&nbsp;&nbsp;<strong>' . $i . '</strong>';
            }
        }

        if ($pages != $page) {
            if (($pages - 1) > $to) {
                $multipage .= '&nbsp;&nbsp;..';
            }
            $multipage .= '&nbsp;&nbsp;<u><a href="' . $mpurl . $string . 'page=' . $pages . '" onclick="event.cancelBubble=true">' . $pages . '</a></u>';
        } else {
            $multipage .= '&nbsp;&nbsp;<strong>' . $pages . '</strong>';
        }
    } else
        if ($strict !== true) {
            return false;
        }

    return $multipage;
}

function quickpage($things, $thingsperpage)
{
    return ((($things > 0) && ($thingsperpage > 0) && ($things > $thingsperpage)) ? ceil($things / $thingsperpage) : 1);
}

function smilieinsert()
{
    global $db, $CONFIG, $THEME;

    $retval = '';
    if ($CONFIG['smileyinsert'] == 'on' && $CONFIG['smcols'] != '') {
        $col_smilies = $total = 0;
        $smilies = '<tr>';

        if ($CONFIG['smtotal'] == 0) {
            $querysmilie = $db->query("SELECT * FROM " . X_PREFIX . "smilies WHERE type = 'smiley' ORDER BY id ASC") or die($db->error());
        } else {
            $querysmilie = $db->query("SELECT * FROM " . X_PREFIX . "smilies WHERE type = 'smiley' ORDER BY id ASC LIMIT $CONFIG[smtotal]") or die($db->error());
        }

        while (($smilie = $db->fetch_array($querysmilie)) != false) {
            eval('$smilies .= "' . template('functions_smilieinsert_smilie') . '";');

            $col_smilies++;
            $total++;
            if ($col_smilies == $CONFIG['smcols']) {
                if ($total == $CONFIG['smtotal']) {
                    $smilies .= '</tr>';
                } else {
                    $smilies .= '</tr><tr>';
                }
                $col_smilies = 0;
            }
        }
        $db->free_result($querysmilie);

        if ($col_smilies > 0) {
            $smilies .= '</tr>';
        }

        eval('$retval = "' . template('functions_smilieinsert') . '";');
    }
    return $retval;
}

function bbcodeinsert()
{
    global $THEME, $CONFIG, $lang, $selHTML, $cheHTML;

    $bbcode = '';
    if ($CONFIG['bbinsert'] == 'on') {
        eval('$bbcode = "' . template('functions_bbcodeinsert') . '";');
    }
    return $bbcode;
}

function updateforumcount($fid)
{
    global $db;

    $postcount = $threadcount = 0;
    $query = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE fid = '$fid'");
    $postcount = $db->result($query, 0);
    $db->free_result($query);

    $query = $db->query("SELECT COUNT(tid) FROM " . X_PREFIX . "threads WHERE (fid = '$fid' AND closed != 'moved')");
    $threadcount = $db->result($query, 0);
    $db->free_result($query);

    $query = $db->query("SELECT fid FROM " . X_PREFIX . "forums WHERE fup = '$fid'");
    while (($children = $db->fetch_array($query)) != false) {
        $chquery1 = $db->query("SELECT COUNT(pid) FROM " . X_PREFIX . "posts WHERE fid = '$children[fid]'");
        $postcount += $db->result($chquery1, 0);
        $db->free_result($chquery1);

        $chquery2 = $db->query("SELECT COUNT(tid) FROM " . X_PREFIX . "threads WHERE fid = '$children[fid]' AND closed != 'moved'");
        $threadcount += $db->result($chquery2, 0);
        $db->free_result($chquery2);
    }
    $db->free_result($query);

    $query = $db->query("SELECT tid FROM " . X_PREFIX . "posts WHERE fid = '$fid' ORDER BY pid DESC LIMIT 0,1");
    $lastpost = $db->result($query, 0);
    $db->query("UPDATE " . X_PREFIX . "forums SET posts = '$postcount', threads = '$threadcount', lastpost = '$lastpost' WHERE fid = '$fid'");
    $db->free_result($query);
}

function updatethreadcount($tid)
{
    global $db;

    $query = $db->query("SELECT count(tid) FROM " . X_PREFIX . "posts WHERE tid = '$tid'");
    $replycount = $db->result($query, 0);
    $db->free_result($query);
    $replycount--;

    $query = $db->query("SELECT p.author, m.uid, p.dateline, p.pid FROM " . X_PREFIX . "posts p LEFT JOIN " . X_PREFIX . "members m ON p.author = m.username WHERE tid = '$tid' ORDER BY dateline DESC LIMIT 0,1");
    $lp = $db->fetch_array($query);
    $db->free_result($query);

    $db->query("UPDATE " . X_PREFIX . "threads SET replies = '$replycount' WHERE tid = '$tid' LIMIT 1");
    $db->query("UPDATE " . X_PREFIX . "lastposts SET uid = '$lp[uid]', username = '$lp[author]', dateline = '$lp[dateline]', pid = '$lp[pid]' WHERE tid = '$tid' LIMIT 1");
}

function updatelastposts()
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
}

function smcwcache()
{
    global $db, $smiliecache, $censorcache, $smiliesnum, $wordsnum;
    static $cached, $THEME, $CONFIG;

    if (!$cached) {
        $smiliecache = array();
        $censorcache = array();

        $query = $db->query("SELECT code, url FROM " . X_PREFIX . "smilies WHERE type = 'smiley'");
        $smiliesnum = $db->num_rows($query);

        if ($smiliesnum > 0) {
            while (($smilie = $db->fetch_array($query)) != false) {
                $code = $smilie['code'];
                $smiliecache[$code] = $smilie['url'];
            }
        }
        $db->free_result($query);

        $query = $db->query("SELECT find, replace1 FROM " . X_PREFIX . "words");
        $wordsnum = $db->num_rows($query);
        if ($wordsnum > 0) {
            while (($word = $db->fetch_array($query)) != false) {
                $find = $word['find'];
                $censorcache[$find] = $word['replace1'];
            }
        }
        $db->free_result($query);

        $cached = true;
        return true;
    }
    return false;
}

function loadtime()
{
    global $footerstuff, $starttime, $CONFIG, $db, $lang;

    $mtime2 = explode(' ', microtime());
    $endtime = $mtime2[1] + $mtime2[0];

    $totaltime = ($endtime - $starttime);

    $footer_options = explode('-', $CONFIG['footer_options']);

    if (X_SADMIN && in_array('serverload', $footer_options)) {
        $load = ServerLoad();
        if ($load != '') {
            eval("\$footerstuff['load'] = \"" . template('footer_load') . "\";");
        } else {
            $footerstuff['load'] = '';
        }
    } else {
        $footerstuff['load'] = '';
    }

    if (in_array('queries', $footer_options)) {
        $querynum = $db->querynum;
        eval("\$footerstuff['querynum'] = \"" . template('footer_querynum') . "\";");
    } else {
        $footerstuff['querynum'] = '';
    }

    if (in_array('phpsql', $footer_options)) {
        $db_duration = number_format(($db->duration / $totaltime) * 100, 1);
        $php_duration = number_format((1 - ($db->duration / $totaltime)) * 100, 1);
        eval("\$footerstuff['phpsql'] = \"" . template('footer_phpsql') . "\";");
    } else {
        $footerstuff['phpsql'] = '';
    }

    if (in_array('loadtimes', $footer_options) && X_ADMIN) {
        $totaltime = number_format($totaltime, 7);
        eval("\$footerstuff['totaltime'] = \"" . template('footer_totaltime') . "\";");
    } else {
        $footerstuff['totaltime'] = '';
    }

    $footerstuff['querydump'] = '';
    if (DEBUG && DEBUGLEVEL > 0) {
        if ((DEBUGLEVEL == 1 && X_SADMIN) || (DEBUGLEVEL == 2 && X_MEMBER) || DEBUGLEVEL == 3) {
            $stuff = array();
            $stuff[] = '<table style="width: 97%;"><tr><td style="width: 2em;">#</td><td style="width: 8em;">Duration:</td><td>Query:</td></tr>';
            $querylist = $db->querylist;
            foreach ($querylist as $key => $val) {
                $val = mysql_syn_highlight(htmlentities($val));
                $stuff[] = '<tr><td><strong>' . ++$key . '.</strong></td><td>' . number_format($db->querytimes[$key - 1], 8) . '</td><td>' . $val . '</td></tr>';
            }
            $stuff[] = '</table>';
            $footerstuff['querydump'] = implode("\n", $stuff);
        }
    }
    return $footerstuff;
}

function pwverify($pass = '', $url, $fid, $showHeader = false)
{
    global $self, $cookiepath, $cookiedomain, $lang;

    if (X_SADMIN) {
        return true;
    }

    $pass = trim($pass);

    $fidpw = isset($_COOKIE['fidpw' . $fid]) ? trim($_COOKIE['fidpw' . $fid]) : '';

    if ($pass === $fidpw) {
        return true;
    }

    if ($pass !== '' && $fidpw === '') {
        $postpw = isset($_POST['pw']) ? trim($_POST['pw']) : '';
        if ($pass === $postpw) {
            put_cookie("fidpw$fid", $pass, (time() + (86400 * 30)), $cookiepath, $cookiedomain, null, X_SET_HEADER);
            redirect($url, 0);
            return true;
        }
    }

    $pwform = '';
    eval('$pwform = "' . template('viewforum_password') . '";');

    $myErr = ($postpw != '') ? $lang['invalidforumpw'] : $lang['forumpwinfo'];

    error($myErr, $showHeader, '', $pwform, false, true, false, true);

    return false;
}

function redirect($path, $timeout = 2, $type = X_REDIRECT_HEADER)
{
    global $lang;

    if (strpos(urldecode($path), "\n") !== false || strpos(urldecode($path), "\r") !== false) {
        error($lang['error_security_msg']);
    }

    session_write_close();

    $type = (headers_sent() || $type == X_REDIRECT_JS) ? X_REDIRECT_JS : X_REDIRECT_HEADER;
    if ($type == X_REDIRECT_JS) {
        ?>
        <script language="javascript" type="text/javascript">
            function redirect() {
                window.location.replace("<?php echo $path?>");
            }
            setTimeout("redirect();", <?php echo($timeout * 1000)?>);
        </script>
        <?php
    } else {
        if ($timeout == 0) {
            header("Location: $path");
        } else {
            header("Refresh: $timeout; URL=./$path");
        }
        exit();
    }
}

function set_whocanpost($pperm, $fid)
{
    global $lang, $whopost1, $whopost2;
    static $cache;

    if (!isset($cache[$fid])) {
        switch ($pperm[0]) {
            case 1:
                $whopost1 = $lang['whocanpost11'];
                break;
            case 2:
                $whopost1 = $lang['whocanpost12'];
                break;
            case 3:
                $whopost1 = $lang['whocanpost13'];
                break;
            case 4:
                $whopost1 = $lang['whocanpost14'];
                break;
            case 5:
                $whopost1 = $lang['whocanpost15'];
                break;
        }

        switch ($pperm[1]) {
            case 1:
                $whopost2 = $lang['whocanpost21'];
                break;
            case 2:
                $whopost2 = $lang['whocanpost22'];
                break;
            case 3:
                $whopost2 = $lang['whocanpost23'];
                break;
            case 4:
                $whopost2 = $lang['whocanpost24'];
                break;
            case 5:
                $whopost2 = $lang['whocanpost25'];
                break;
        }
        $cache[$fid] = true;
    }
}

function modcheck($mods, $user = '')
{
    global $self;

    if (X_GUEST) {
        return false;
    }

    if (X_ADMIN || X_SMOD) {
        return 'Moderator';
    }

    if (X_MOD) {
        if (empty($user)) {
            $user = $self['username'];
        }
        $user = strtoupper($user);
        $mods = explode(',', $mods);
        foreach ($mods as $key => $moderator) {
            if (strtoupper(trim($moderator)) == $user) {
                return 'Moderator';
            }
        }
    }
    return '';
}

function privfcheck($private, $userlist)
{
    global $self;
    $retval = false;

    if (X_SADMIN) {
        return true;
    }

    switch ($private) {
        case 1: // all users
            $userlist = trim($userlist);
            if (empty($userlist)) {
                return true;
            }

            // Guests can never gain entry in a userlist resource
            if (X_GUEST) {
                return false;
            }

            $user = explode(',', $userlist);
            $xuser = strtolower($self['username']);

            foreach ($user as $usr) {
                $usr = strtolower(trim($usr));
                if ($usr != '' && $xuser == $usr) {
                    return true;
                }
            }
            break;
        case 2: // admins only
            if (X_ADMIN) {
                return true;
            }
            break;
        case 3: // admins/mods
            if (X_STAFF) {
                return true;
            }
            break;
        case 4: // deny all
            $retval = false;
            break;

        case 5: // Only registered users
            if (X_GUEST) {
                return false;
            }
            $userlist = trim($userlist);
            if (empty($userlist)) {
                return true;
            }

            $user = explode(',', $userlist);
            $xuser = strtolower($self['username']);

            foreach ($user as $usr) {
                $usr = strtolower(trim($usr));
                if ($usr != '' && $xuser == $usr) {
                    return true;
                }
            }
            break;

        default:
            break;
    }
    return $retval;
}

function postperm(&$forums, $type)
{
    global $lang, $self, $perm;

    if (!isset($forums) || !isset($forums['postperm'])) {
        return false;
    }

    $pperm = explode('|', $forums['postperm']);
    set_whocanpost($pperm, $forums['fid']);

    if (X_SADMIN) {
        return true;
    }

    // $private = VIEW permission
    // $pperm[0] = THREAD permission
    // $pperm[1] = REPLY permission
    // $pperm[2] = EDIT permissions (post XMB 1.9.1)
    // $pperm[3] = DELETE permissions (post XMB 1.9.1)

    // 1 = Normal
    // 2 = Admins
    // 3 = Admin/Mods
    // 4 = Deny / No viewing / No posting
    // 5 = Registered users (and admins/mods) only - no guests

    switch ($type) {
        case 'thread':
            $perm = isset($pperm[0]) ? $pperm[0] : 0;
            break;
        case 'reply':
            $perm = isset($pperm[1]) ? $pperm[1] : 0;
            break;
        case 'edit':
            $perm = isset($pperm[2]) ? $pperm[2] : $pperm[1]; // Not all forums are upgraded yet
            break;
        case 'delete':
            $perm = isset($pperm[3]) ? $pperm[3] : $pperm[1]; // Not all forums are upgraded yet
            break;
    }

    switch ($forums['private']) {
        case 5: // registered users only
            // If there is a password or userlist, test to see if the logged in user can see
            // the forum
            if (!empty($forums['password']) || !empty($forums['userlist'])) {
                return privfcheck($forums['private'], $forums['userlist']);
            }

            // If you're a member and $perm = 1, you're in!
            // Remember, admins are members, too. Prevents guests and banned users from
            // getting in
            if (X_MEMBER) {
                switch ($perm) {
                    case 1:
                        if (X_MEMBER || X_GUEST) {
                            return true;
                        }
                        break;
                    case 5:
                        if (X_MEMBER) {
                            return true;
                        }
                        break;
                    case 2:
                        if (X_ADMIN) {
                            return true;
                        }
                        break;
                    case 3:
                        if (X_STAFF) {
                            return true;
                        }
                        break;
                    case 4:
                        return false;
                        break;
                    default:
                        return false;
                        break;
                }
            }
            break;

        case 1: // all can view
            // If there is a password or userlist, test to see if the logged in user can see
            // the forum
            if (!empty($forums['password']) || !empty($forums['userlist'])) {
                return privfcheck($forums['private'], $forums['userlist']);
            }

            // If you're a guest or member and $perm = 1, you're in!
            // Remember, admins are members, too. Prevents banned users from
            // getting in
            if (X_MEMBER || X_GUEST) {
                switch ($perm) {
                    case 1:
                        return true;
                        break;
                    case 5:
                        if (X_MEMBER) {
                            return true;
                        }
                        break;
                    case 2:
                        if (X_ADMIN) {
                            return true;
                        }
                        break;
                    case 3:
                        if (X_STAFF) {
                            return true;
                        }
                        break;
                    case 4:
                    default:
                        return false;
                        break;
                }
            }
            break;
        case 2: // admin only, and requires perm in 1 .. 2 / 5
            if (X_ADMIN && ($perm < 3 || $perm == 5)) {
                return true;
            }
            break;
        case 3: // admin/mods and requires perm in 1 .. 3 / 5
            if (X_STAFF && ($perm < 4 || $perm == 5)) {
                return true;
            }
            break;
        case 4:

            // none can see, so just return false
            break;
        default:

            // shouldn't get here, so fail closed
            break;
    }
    return false;
}

function get_extension($filename)
{
    $a = explode('.', $filename);
    $count = count($a);
    if ($count == 1) {
        return '';
    } else {
        return $a[$count - 1];
    }
}

function get_attached_file($file, $attachstatus)
{
    global $db, $forum, $CONFIG, $lang, $filename, $filetype, $filesize, $fileheight, $filewidth;

    $filename = $filetype = $fileheight = $filewidth = '';
    $filesize = 0;

    if (is_array($file) && $file['name'] != 'none' && !empty($file['name']) && $forum['attachstatus'] != 'off' && is_uploaded_file($file['tmp_name'])) {
        if (!isValidFilename($file['name'])) {
            error($lang['invalidFilename'], false, '', '', false, false, false, false);
            return false;
        }

        $filesize = intval(filesize($file['tmp_name']));
        if ($filesize > $CONFIG['max_attach_size']) {
            error($lang['attachtoobig'], false, '', '', false, false, false, false);
            return false;
        }

        $attachment = addslashes(fread(fopen($file['tmp_name'], 'rb'), filesize($file['tmp_name'])));
        $filename = checkInput($file['name']);
        $filetype = checkInput($file['type']);

        $extension = strtolower(substr(strrchr($file['name'], '.'), 1));
        if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png' || $extension == 'bmp') {
            $exsize = getimagesize($file['tmp_name']);
            $fileheight = $exsize[1];
            $filewidth = $exsize[0];
        }

        if ($filesize !== 0) {
            return $attachment;
        }
    }
    return false;
}

function get_attached_file_multi($file, $i, $attachstatus)
{
    global $db, $forum, $lang, $filename, $filetype, $filesize, $fileheight, $filewidth, $CONFIG;

    $filename = $filetype = $fileheight = $filewidth = '';
    $filesize = 0;

    if ($file['name'] != 'none' && !empty($file['name'][$i]) && $forum['attachstatus'] != 'off' && is_uploaded_file($file['tmp_name'][$i])) {
        if (!isValidFilename($file['name'][$i])) {
            error($lang['invalidFilename'], false, '', '', false, false, false, false);
            return false;
        }

        $filesize = intval(filesize($file['tmp_name'][$i]));
        if ($filesize > $CONFIG['max_attach_size']) {
            error($lang['attachtoobig'], false, '', '', false, false, false, false);
            return false;
        }

        $attachment = addslashes(fread(fopen($file['tmp_name'][$i], 'rb'), $filesize));
        $filename = checkInput($file['name'][$i]);
        $filetype = checkInput($file['type'][$i]);

        $extension = strtolower(substr(strrchr($file['name'][$i], '.'), 1));
        if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png' || $extension == 'bmp') {
            $exsize = getimagesize($file['tmp_name'][$i]);
            $fileheight = $exsize[1];
            $filewidth = $exsize[0];
        }

        if ($filesize !== 0) {
            return $attachment;
        }
    }
    return false;
}

function ServerLoad()
{
    // Cant do anything on Windows easily
    if (getenv("OS") === "Windows_NT") {
        return array(
            ''
        );
    }

    if (($stats = @exec('uptime')) != false) {
        $i = strpos($stats, "average");
        $load = str_replace(',', ' ', substr($stats, $i));
        $parts = explode(' ', $load);
        $count = count($parts);
        $first = explode(' ', $parts[$count - 3]);
        $c = count($first);
        $first = $first[$c - 1];

        return array(
            $first,
            $parts[$count - 2],
            $parts[$count - 1]
        );
    }
    return array();
}

function error($msg, $showheader = true, $prepend = '', $append = '', $redirect = false, $die = true, $return_as_string = false, $showfooter = true)
{
    global $footerstuff, $lang, $navigation;
    global $CONFIG, $THEME, $shadow, $lang_nalign, $lang_code, $lang_dir, $lang_align;
    global $charset, $meta, $quickjump, $btitle, $versionpowered, $background;
    global $versionlong, $bottomcorners, $css, $bbcodescript, $attachscript;
    global $topcorners, $topbgcode, $logo, $links, $pluglink, $lastvisittext;
    global $notify, $newpmmsg;

    $args = func_get_args();

    $message = (isset($args[0]) ? $args[0] : '');
    $showheader = (isset($args[1]) ? $args[1] : true);
    $prepend = (isset($args[2]) ? $args[2] : '');
    $append = (isset($args[3]) ? $args[3] : '');
    $redirect = (isset($args[4]) ? $args[4] : false);
    $die = (isset($args[5]) ? $args[5] : true);
    $return_str = (isset($args[6]) ? $args[6] : false);
    $showfooter = (isset($args[7]) ? $args[7] : true);

    $header = $footer = $return = '';

    if ($redirect !== false) {
        redirect($redirect, 3.0, X_REDIRECT_JS);
    }

    loadtime();

    $header = '';
    if ($showheader === true) {
        if (!isset($css) || strlen($css) == 0) {
            eval('$css = "' . template('css') . '";');
        }
        eval('$header = "' . template('header') . '";');
    }

    $error = '';
    eval('$error = "' . template('error') . '";');

    $footer = '';
    if ($showfooter === true) {
        eval('$footer = "' . template('footer') . '";');
    }

    $return = '';
    if ($return_str !== false) {
        $return = $css . $header . $prepend . $error . $append . $footer;
    } else {
        echo $css . $header . $prepend . $error . $append . $footer;
    }

    if ($die) {
        exit();
    }

    return $return;
}

function cp_error($msg, $showheader = true, $prepend = '', $append = '', $redirect = false, $die = true, $return_as_string = false, $showfooter = true)
{
    global $footerstuff, $lang, $navigation;
    global $CONFIG, $THEME;

    $args = func_get_args();

    $message = (isset($args[0]) ? $args[0] : '');
    $showheader = (isset($args[1]) ? $args[1] : true);
    $prepend = (isset($args[2]) ? $args[2] : '');
    $append = (isset($args[3]) ? $args[3] : '');
    $redirect = (isset($args[4]) ? $args[4] : false);
    $die = (isset($args[5]) ? $args[5] : true);
    $return_str = (isset($args[6]) ? $args[6] : false);
    $showfooter = (isset($args[7]) ? $args[7] : true);

    $header = $footer = $return = $css = '';

    loadtime();

    if ($redirect !== false) {
        redirect($redirect, 3.0, X_REDIRECT_JS);
    }

    if ($showheader === false) {
        $header = '';
    } else {
        if (!isset($css) || strlen($css) == 0) {
            eval('$css = "' . template('css') . '";');
        }
        eval('$header = "' . template('cp_header') . '";');
    }

    $error = '';
    eval('$error = "' . template('cp_error') . '";');

    if ($showfooter === true) {
        eval('$footer = "' . template('cp_footer') . '";');
    } else {
        $footer = '';
    }

    if ($return_str !== false) {
        $return = $prepend . $error . $footer . $append;
    } else {
        echo $prepend . $error . $append . $footer;
        $return = '';
    }

    if ($die) {
        exit();
    }
    return $return;
}

function message($msg, $showheader = true, $prepend = '', $append = '', $redirect = false, $die = true, $return_as_string = false, $showfooter = true)
{
    global $footerstuff, $lang, $navigation;
    global $CONFIG, $THEME, $shadow, $lang_nalign, $lang_code, $lang_dir, $lang_align;
    global $charset, $meta, $quickjump, $btitle, $versionpowered, $background;
    global $versionlong, $bottomcorners, $css, $bbcodescript, $attachscript;
    global $topcorners, $topbgcode, $logo, $links, $pluglink, $lastvisittext;
    global $notify, $newpmmsg;

    $args = func_get_args();

    $message = (isset($args[0]) ? $args[0] : '');
    $showheader = (isset($args[1]) ? $args[1] : true);
    $prepend = (isset($args[2]) ? $args[2] : '');
    $append = (isset($args[3]) ? $args[3] : '');
    $redirect = (isset($args[4]) ? $args[4] : false);
    $die = (isset($args[5]) ? $args[5] : true);
    $return_str = (isset($args[6]) ? $args[6] : false);
    $showfooter = (isset($args[7]) ? $args[7] : true);

    $header = $footer = $return = '';

    loadtime();

    if ($redirect !== false) {
        redirect($redirect, 3.0, X_REDIRECT_JS);
    }

    if ($showheader === false) {
        $header = '';
    } else {
        if (!isset($css) || strlen($css) == 0) {
            eval('$css = "' . template('css') . '";');
        }
        eval('$header = "' . template('header') . '";');
    }

    $confirm = '';
    eval('$confirm = "' . template('message') . '";');

    if ($showfooter === true) {
        eval('$footer = "' . template('footer') . '";');
    } else {
        $footer = '';
    }

    if ($return_str !== false) {
        $return = $prepend . $confirm . $footer . $append;
    } else {
        echo $prepend . $confirm . $append . $footer;
        $return = '';
    }

    if ($die) {
        exit();
    }
    return $return;
}

function cp_message($msg, $showheader = true, $prepend = '', $append = '', $redirect = false, $die = true, $return_as_string = false, $showfooter = true)
{
    global $footerstuff, $lang, $navigation;
    global $CONFIG, $THEME;

    $args = func_get_args();

    $message = (isset($args[0]) ? $args[0] : '');
    $showheader = (isset($args[1]) ? $args[1] : true);
    $prepend = (isset($args[2]) ? $args[2] : '');
    $append = (isset($args[3]) ? $args[3] : '');
    $redirect = (isset($args[4]) ? $args[4] : false);
    $die = (isset($args[5]) ? $args[5] : true);
    $return_str = (isset($args[6]) ? $args[6] : false);
    $showfooter = (isset($args[7]) ? $args[7] : true);

    $header = $footer = $return = $css = '';

    loadtime();

    if ($redirect !== false) {
        redirect($redirect, 3.0, X_REDIRECT_JS);
    }

    if ($showheader === false) {
        $header = '';
    } else {
        if (!isset($css) || strlen($css) == 0) {
            eval('$css = "' . template('css') . '";');
        }
        eval('$header = "' . template('cp_header') . '";');
    }

    $confirm = '';
    eval('$confirm = "' . template('cp_message') . '";');

    if ($showfooter === true) {
        eval('$footer = "' . template('cp_footer') . '";');
    } else {
        $footer = '';
    }

    if ($return_str !== false) {
        $return = $prepend . $confirm . $footer . $append;
    } else {
        echo $prepend . $confirm . $append . $footer;
        $return = '';
    }

    if ($die) {
        exit();
    }
    return $return;
}

function array_keys2keys($array, $translator)
{
    $new_array = array();
    foreach ($array as $key => $val) {
        if (isset($translator[$key])) {
            $new_key = $translator[$key];
        } else {
            $new_key = $key;
        }
        $new_array[$new_key] = $val;
    }
    return $new_array;
}

function mysql_syn_highlight($query)
{
    global $tables, $tablepre;

    $find = array();
    $replace = array();

    foreach ($tables as $name) {
        $find[] = $tablepre . $name;
    }

    $find[] = 'SELECT';
    $find[] = 'UPDATE';
    $find[] = 'DELETE';
    $find[] = 'INSERT INTO ';
    $find[] = ' WHERE ';
    $find[] = ' ON ';
    $find[] = ' FROM ';
    $find[] = ' GROUP BY ';
    $find[] = 'ORDER BY ';
    $find[] = ' LEFT JOIN ';
    $find[] = ' IN ';
    $find[] = ' SET ';
    $find[] = ' AS ';
    $find[] = '(';
    $find[] = ')';
    $find[] = ' ASC';
    $find[] = ' DESC';
    $find[] = ' AND ';
    $find[] = ' OR ';
    $find[] = ' NOT';

    foreach ($find as $key => $val) {
        $replace[$key] = '</em><strong>' . $val . '</strong><em>';
    }
    return '<em>' . str_replace($find, $replace, $query) . '</em>';
}

/**
 * Get rid of registered globals if they are set
 */
function disposeGlobals()
{
    if (ini_get('register_globals')) {
        die("This program does not support running with register globals enabled");
    }
}

function dump_query($resource, $header = true)
{
global $db, $THEME;

if (!$db->error()) {
$count = $db->num_fields($resource);
if ($header) {
?>
<tr class="category" bgcolor="<?php echo $THEME['altbg2'] ?>"
    align="center">
    <?php
    for ($i = 0; $i < $count; $i++) {
        echo '<td align="left">';
        echo '<strong><font color=' . $THEME['cattext'] . '>' . $db->field_name($resource, $i) . '</font></strong>';
        echo '</td>';
    }
    echo '</tr>';
    }

    while (($a = $db->fetch_array($resource, SQL_NUM)) != false) {
    ?>
<tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="ctrtablerow">
    <?php
    for ($i = 0; $i < $count; $i++) {
        echo '<td align="left">';

        if (trim($a[$i]) == '') {
            echo '&nbsp;';
        } else {
            echo nl2br($a[$i]);
        }
        echo '</td>';
    }
    echo '</tr>';
    }
    } else {
        error($db->error());
    }
    }

    function put_cookie($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $how = X_SET_CHOOSE)
    {
        if (($how == X_SET_CHOOSE && !headers_sent()) || $how == X_SET_HEADER) {
            return setcookie($name, $value, $expire, $path, $domain, $secure);
        } else {
            if ($expire >= 0) {
                $expire = date('r', $expire);
            } else {
                $expire = null;
            }
            ?>
            <script language="javascript" type="text/javascript">
                function setcookie(name, value="deleted", expire=0, path="", domain="", secure=0) {
                    if (expire == 0) {
                        var now = new Date();
                        expire = now.toGMTString();
                    }

                    if (path == "") {
                        path = window.location.pathname;
                    }

                    if (domain == "") {
                        domain = window.location.host;
                    }

                    // create cookie string(expire in GMT TIME!)
                    var cookie = '';
                    cookie = name + "=" + value + "; expires=" + expire + "; path=" + path + "; domain=" + domain + "; secure=" + secure + ";";
                    document.cookie += cookie;
                }
                setcookie(<?php echo $name?>, <?php echo $value?>, <?php echo $expire?>, <?php echo $path?>, <?php echo $domain?>, <?php echo $secure?>);
            </script>
            <?php
            return true;
        }
    }

    function adminaudit($user = '', $action = '', $fid, $tid, $reason = '')
    {
        global $self, $db, $onlineip;

        if ($user === '') {
            $user = $self['username'];
        }

        if (empty($action)) {
            $action = $_SERVER['REQUEST_URI'];
            $aapos = strpos($action, "?");
            if ($aapos !== false) {
                $action = substr($action, $aapos + 1);
            }

            $action = $db->escape("$onlineip|#|$action", -1, true);
        }

        $fid = (int)$fid;
        $tid = (int)$tid;
        $action = $db->escape($action, -1, true);
        $user = $db->escape($user, -1, true);

        if (!empty($reason)) {
            $reason = $db->escape($reason, -1, true);
            $action = $action . " !!! " . $reason;
        }

        $db->query("INSERT INTO " . X_PREFIX . "adminlogs (uid, tid, username, action, fid, date) VALUES ('" . $self['uid'] . "', '$tid', '$user', '$action', '$fid', " . $db->time() . ")");
        return true;
    }

    function modaudit($user = '', $action, $fid, $tid, $reason = '')
    {
        global $self, $db;

        if ($user == '') {
            $user = $self['username'];
        }

        $fid = (int)$fid;
        $tid = (int)$tid;
        $action = addslashes(checkInput($action));
        $user = addslashes(checkInput($user));
        $reason = addslashes(checkInput($reason));

        $db->query("INSERT " . X_PREFIX . "modlogs (tid, username, action, fid, date) VALUES ('$tid', '$user', '$action', '$fid', " . $db->time() . ")");
        return true;
    }

    function readFileAsINI($filename)
    {
        $lines = @file($filename);
        if ($lines === false) {
            return '';
        }

        $thefile = array();
        foreach ($lines as $line_num => $line) {
            $temp = explode("=", $line);
            if ($temp[0] != 'dummy') {
                $key = trim($temp[0]);
                $val = trim($temp[1]);
                $thefile[$key] = $val;
            }
        }
        return $thefile;
    }

    function forumList($selectname = 'srchfid', $multiple = false, $allowall = true)
    {
        global $self, $db, $lang, $selHTML;

        $restrict = array();
        switch ($self['status']) {
            case 'Member':
                $restrict[] = "private != '3'";
            case 'Moderator':
            case 'Super Moderator':
                $restrict[] = "private != '2'";
            case 'Administrator':
                $restrict[] = "userlist = ''";
            case 'Super Administrator':
                break;
            default:
                $restrict[] = "private != '5'";
                $restrict[] = "private != '3'";
                $restrict[] = "private != '2'";
                $restrict[] = "userlist = ''";
                $restrict[] = "password = ''";
                break;
        }
        $restrict = implode(' AND ', $restrict);

        if ($restrict != '') {
            $sql = $db->query("SELECT fid, type, name, fup, status, private, userlist, password FROM " . X_PREFIX . "forums WHERE $restrict AND status = 'on' ORDER BY displayorder");
        } else {
            $sql = $db->query("SELECT fid, type, name, fup, private, userlist, password FROM " . X_PREFIX . "forums ORDER BY displayorder");
        }

        $standAloneForums = array();
        $forums = array();
        $categories = array();
        $subforums = array();
        while (($forum = $db->fetch_array($sql)) != false) {
            if (!X_SADMIN && $forum['password'] != '') {
                $fidpw = isset($_COOKIE['fidpw' . $forum['fid']]) ? trim($_COOKIE['fidpw' . $forum['fid']]) : '';
                if ($forum['password'] !== $fidpw) {
                    continue;
                }
            }

            switch ($forum['type']) {
                case 'group':
                    $categories[] = $forum;
                    break;
                case 'sub':
                    if (!isset($subforums[$forum['fup']])) {
                        $subforums[$forum['fup']] = array();
                    }
                    $subforums[$forum['fup']][] = $forum;
                    break;
                case 'forum':
                default:
                    if ($forum['fup'] == 0) {
                        $standAloneForums[] = $forum;
                    } else {
                        if (!isset($forums[$forum['fup']])) {
                            $forums[$forum['fup']] = array();
                        }
                        $forums[$forum['fup']][] = $forum;
                    }
                    break;
            }
        }
        $db->free_result($sql);

        $forumselect = array();
        if (!$multiple) {
            $forumselect[] = '<select name="' . $selectname . '">';
        } else {
            $forumselect[] = '<select name="' . $selectname . '" multiple="multiple">';
        }

        if ($allowall) {
            $forumselect[] = '<option value="all" ' . $selHTML . '>' . $lang['textallforumsandsubs'] . '</option>';
        } else
            if (!$allowall && !$multiple) {
                $forumselect[] = '<option value="" disabled="disabled" ' . $selHTML . '>' . $lang['textforum'] . '</option>';
            }

        unset($forum);
        reset($forums);

        foreach ($standAloneForums as $forum) {
            $forumselect[] = '<option value="' . intval($forum['fid']) . '"> &nbsp; &raquo; ' . stripslashes($forum['name']) . '</option>';
            if (isset($subforums[$forum['fid']])) {
                foreach ($subforums[$forum['fid']] as $sub) {
                    $forumselect[] = '<option value="' . intval($sub['fid']) . '">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &raquo; ' . stripslashes($sub['name']) . '</option>';
                }
            }
        }

        $forumselect[] = '<option value="" disabled="disabled">&nbsp;</option>';
        foreach ($categories as $group) {
            if (isset($forums[$group['fid']]) && count($forums[$group['fid']]) > 0) {
                $forumselect[] = '<option value="' . intval($group['fid']) . '" disabled="disabled">' . stripslashes($group['name']) . '</option>';
                foreach ($forums[$group['fid']] as $forum) {
                    $forumselect[] = '<option value="' . intval($forum['fid']) . '"> &nbsp; &raquo; ' . stripslashes($forum['name']) . '</option>';
                    if (isset($subforums[$forum['fid']])) {
                        foreach ($subforums[$forum['fid']] as $sub) {
                            $forumselect[] = '<option value="' . intval($sub['fid']) . '">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &raquo; ' . stripslashes($sub['name']) . '</option>';
                        }
                    }
                }
            }
            $forumselect[] = '<option value="" disabled="disabled">&nbsp;</option>';
        }
        $forumselect[] = '</select>';
        return implode("\n", $forumselect);
    }

    function shadowfx()
    {
        global $THEME;

        $shadow = '';
        if ($THEME['shadowfx'] == 'on') {
            eval('$shadow = "' . template('shadow') . '";');
        }
        return $shadow;
    }

    function shadowfx2()
    {
        global $THEME;

        $shadow2 = '';
        if ($THEME['shadowfx'] == 'on') {
            eval('$shadow2 = "' . template('shadow2') . '";');
        }
        return $shadow2;
    }

    function metaTags()
    {
        global $CONFIG;

        $meta = '';
        if ($CONFIG['metatag_status'] == 'on') {
            eval('$meta = stripslashes("' . template('meta_tags') . '");');
        }
        return $meta;
    }

    function encode_ip($dotquad_ip)
    {
        $ip_sep = explode('.', $dotquad_ip);
        return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
    }

    function celloverfx($url)
    {
        global $THEME;

        $mouseover = '';
        if ($THEME['celloverfx'] == 'on') {
            $mouseover = "onclick=\"location.href='$url';\" style=\"cursor:pointer\" onmouseover=\"this.style.backgroundColor='$THEME[altbg1]'\" onmouseout=\"this.style.backgroundColor='$THEME[altbg2]'\"";
        }
        return $mouseover;
    }

    function securityChecks()
    {
        global $CONFIG, $onlineip, $url;

        if (file_exists('cplogfile.php') && !@unlink('cplogfile.php')) {
            exit('<h1>Error:</h1><br />The old logfile("cplogfile.php") has been found on the server, but could not be removed. Please remove it as soon as possible.');
        }

        if (file_exists('fixhack.php') && !@unlink('fixhack.php')) {
            exit('<h1>Error:</h1><br />The hack repair tool("fixhack.php") has been found on the server, but could not be removed. Please remove it as soon as possible.');
        }

        if (file_exists('install/emergency.php') && !@unlink('install/emergency.php')) {
            exit('<h1>Error:</h1><br />The emergency repair file("install/emergency.php") has been found on the server, but could not be removed. Please remove it as soon as possible.');
        }

        // Checks the IP-format, if it's not a IPv4, nor a IPv6 type, it will be blocked, safe to remove....
        if ($CONFIG['ipcheck'] == 'on') {
            if (!preg_match('/^([0-9]{1,3}.){3}[0-9]{1,3}$/i', $onlineip) && !preg_match('/^([a-z,0-9]{0,4}:){5}[a-z,0-9]{0,4}$/i', $onlineip) && !stristr($onlineip, ':::::')) {
                exit("Access to this website is currently not possible as your hostname/IP appears suspicous.");
            }
        }

        // Checks for various variables in the URL, if any of them is found, script is halted
        $url_check = array(
            'status=',
            'gbbuser=',
            'gbbpw=',
            '<script'
        );
        $url = trim(urldecode($url));
        foreach ($url_check as $name) {
            if (strpos($url, $name)) {
                exit('Attack attempt denied.');
            }
        }
    }

    function is_ip($ip)
    {
        $check = true;
        $ip = explode(".", $ip);
        foreach ($ip as $block) {
            if (!is_numeric($block)) {
                $check = false;
            }
        }
        return $check;
    }

    /**
     * findLangName() - given a number between 1..n, find its language name
     *
     * In olden days, we would return a partial filename to the user. In these
     * days of high security, that's no good at all. So we return a simple
     * number between 1..n to the user. This function's job is to turn that
     * returned number back into a partial filename in a safe way.
     *
     * @param $instance integer,
     *            the value from the user
     * @return string, partial filename useful for stashing in the settings array
     *
     */
    function findLangName($instance)
    {
        $instance = intval($instance);
        if ($instance < 1) {
            return "";
        }

        $dir = opendir('lang');
        $langpos = 0;
        $file = '';
        while (($file = readdir($dir)) != false) {
            if ($instance == $langpos) {
                if (is_file('lang/' . $file) && false !== strpos($file, '.lang.php')) {
                    $file = str_replace('.lang.php', '', $file);
                    break;
                }
            }
            $langpos++;
        }

        if (empty($file)) {
            $file = 'English';
        }

        return $file;
    }

    function fixUrl($matches)
    {
        $fullurl = '';
        if (!empty($matches[2])) {
            if ($matches[3] != 'www') {
                $fullurl = $matches[2];
            } else {
                $fullurl = 'http://' . $matches[2];
            }
        }
        $fullurl = strip_tags($fullurl);
        $shorturl = '';
        if (strlen($fullurl) > 80) {
            $shorturl = substr($fullurl, 0, 80);
            $shorturl = substr_replace($shorturl, '...', 77, 3);
            return ' [url=' . $fullurl . ']' . $shorturl . '[/url]';
        } else {
            return ' <a href="' . $fullurl . '" target="_blank">' . $fullurl . '</a>&nbsp;';
        }
    }

    function forumJump()
    {
        global $self, $db, $lang, $selHTML;

        $restrict = array();
        switch ($self['status']) {
            case 'Member':
                $restrict[] = "private != '3'";
            case 'Moderator':
            case 'Super Moderator':
                $restrict[] = "private != '2'";
            case 'Administrator':
                $restrict[] = "userlist = ''";
            case 'Super Administrator':
                break;
            default:
                $restrict[] = "private != '5'";
                $restrict[] = "private != '3'";
                $restrict[] = "private != '2'";
                $restrict[] = "userlist = ''";
                $restrict[] = "password = ''";
                break;
        }
        $restrict = implode(' AND ', $restrict);

        if (!empty($restrict)) {
            $sql = $db->query("SELECT fid, type, name, fup, status, private, userlist, password FROM " . X_PREFIX . "forums WHERE $restrict AND status = 'on' ORDER BY displayorder");
        } else {
            $sql = $db->query("SELECT fid, type, name, fup, private, userlist, password FROM " . X_PREFIX . "forums ORDER BY displayorder");
        }

        $standAloneForums = array();
        $forums = array();
        $categories = array();
        $subforums = array();
        while (($forum = $db->fetch_array($sql)) != false) {
            if (!X_SADMIN && $forum['password'] != '') {
                $fidpw = isset($_COOKIE['fidpw' . $forum['fid']]) ? trim($_COOKIE['fidpw' . $forum['fid']]) : '';
                if ($forum['password'] !== $fidpw) {
                    continue;
                }
            }

            switch ($forum['type']) {
                case 'group':
                    $categories[] = $forum;
                    break;
                case 'sub':
                    if (!isset($subforums[$forum['fup']])) {
                        $subforums[$forum['fup']] = array();
                    }
                    $subforums[$forum['fup']][] = $forum;
                    break;
                case 'forum':
                default:
                    if ($forum['fup'] == 0) {
                        $standAloneForums[] = $forum;
                    } else {
                        if (!isset($forums[$forum['fup']])) {
                            $forums[$forum['fup']] = array();
                        }
                        $forums[$forum['fup']][] = $forum;
                    }
                    break;
            }
        }
        $db->free_result($sql);

        $forumselect = array();
        $forumselect[] = "<select onchange=\"if (this.options[this.selectedIndex].value) {window.location=(''+this.options[this.selectedIndex].value)}\">";
        $forumselect[] = '<option value="" ' . $selHTML . '>' . $lang['forumquickjump'] . '</option>';

        unset($forum);
        reset($forums);

        foreach ($standAloneForums as $forum) {
            $forumselect[] = '<option value="' . 'viewforum.php?fid=' . intval($forum['fid']) . '"> &nbsp; &raquo; ' . stripslashes($forum['name']) . '</option>';
            if (isset($subforums[$forum['fid']])) {
                foreach ($subforums[$forum['fid']] as $sub) {
                    $forumselect[] = '<option value="' . 'viewforum.php?fid=' . intval($sub['fid']) . '">&nbsp; &nbsp; &raquo; ' . stripslashes($sub['name']) . '</option>';
                }
            }
        }

        foreach ($categories as $group) {
            if (isset($forums[$group['fid']])) {
                $forumselect[] = '<option value=""></option>';
                $forumselect[] = '<option value="' . 'index.php?gid=' . intval($group['fid']) . '">' . stripslashes($group['name']) . '</option>';
                foreach ($forums[$group['fid']] as $forum) {
                    $forumselect[] = '<option value="' . 'viewforum.php?fid=' . intval($forum['fid']) . '"> &nbsp; &raquo; ' . stripslashes($forum['name']) . '</option>';
                    if (isset($subforums[$forum['fid']])) {
                        foreach ($subforums[$forum['fid']] as $sub) {
                            $forumselect[] = '<option value="' . 'viewforum.php?fid=' . intval($sub['fid']) . '">&nbsp; &nbsp; &raquo; ' . stripslashes($sub['name']) . '</option>';
                        }
                    }
                }
            }
        }
        $forumselect[] = '</select>';
        return implode("\n", $forumselect);
    }

    function getPlugLinks()
    {
        global $config_cache, $db, $THEME;

        $pluglinks = array();
        $qp = $db->query("SELECT * FROM " . X_PREFIX . "pluglinks ORDER BY displayorder ASC");
        while (($plug = $db->fetch_array($qp)) != false) {
            if (isset($plug['status']) && $plug['status'] == 'on') {
                $img = '';
                if (isset($plug['img']) && !empty($plug['img'])) {
                    $img = '<img src="' . $THEME['imgdir'] . '/' . stripslashes($plug['img']) . '" alt="' . stripslashes($plug['name']) . '" title="' . stripslashes($plug['name']) . '" border="0px" /> ';
                }
                $pluglinks[] = '&nbsp;' . $img . '<a href="' . stripslashes($plug['url']) . '"><font class="navtd">' . stripslashes($plug['name']) . '</font></a>';
            }
        }
        $db->free_result($qp);
        $config_cache->setData('pluglinks', $pluglinks);
        return $pluglinks;
    }

    function shortenString($string, $len = 100, $shortType = X_SHORTEN_SOFT, $ps = '...')
    {
        if (strlen($string) > $len) {
            if (($shortType & X_SHORTEN_SOFT) === X_SHORTEN_SOFT) {
                $string = preg_replace('#^(.{0,' . $len . '})([\W].*)#', '\1' . $ps, $string);
            }

            if ((strlen($string) > $len + strlen($ps)) && (($shortType & X_SHORTEN_HARD) === X_SHORTEN_HARD)) {
                $string = substr($string, 0, $len) . $ps;
            }
            return $string;
        } else {
            return $string;
        }
    }

    function langswitch($revert = 'no', $thislangfile = '')
    {
        global $CONFIG, $self, $db;

        if ($revert == 'no') {
            if (empty($thislangfile) || !file_exists('lang/' . $thislangfile . '.lang.php')) {
                $langfile = $CONFIG['langfile'];
            } else {
                $langfile = $thislangfile;
            }
        } else {
            $langfile = $self['langfile'];
        }

        return $langfile;
    }

    function langSelect()
    {
        global $member, $selHTML, $CONFIG;

        $lfs = array();
        $dir = opendir('lang');
        $langpos = 0;
        while (($file = readdir($dir)) != false) {
            if (is_file('lang/' . $file) && false !== strpos($file, '.lang.php')) {
                $file = str_replace('.lang.php', '', $file);
                if ($file == $CONFIG['langfile']) {
                    $lfs[] = '<option value="' . $langpos . '" ' . $selHTML . '>' . $file . '</option>';
                } else {
                    $lfs[] = '<option value="' . $langpos . '">' . $file . '</option>';
                }
            }
            $langpos++;
        }
        natcasesort($lfs);

        return ('<select name="langfilenew">' . implode("\n", $lfs) . '</select>');
    }

    ?>
