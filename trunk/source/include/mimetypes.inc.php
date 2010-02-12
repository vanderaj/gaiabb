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

// check to ensure no direct viewing of page
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false))
{
    exit('This file is not designed to be called directly');
}

// create action per extension
switch ($extention)
{
    case 'zip':
    case 'rar':
    case 'tar':
    case 'tgz':
    case 'gz':
        $attachicon = '<img src="./images/mimetypes/zip.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'txt':
    case 'text':
        $attachicon = '<img src="./images/mimetypes/txt.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'css':
    case 'xml':
        $attachicon = '<img src="./images/mimetypes/script.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'mp3':
    case 'wav':
    case 'mid':
        $attachicon = '<img src="./images/mimetypes/music.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'doc':
        $attachicon = '<img src="./images/mimetypes/doc.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'hqx':
        $attachicon = '<img src="./images/mimetypes/stuffit.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'html':
    case 'htm':
        $attachicon = '<img src="./images/mimetypes/html.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'gif':
    case 'bmp':
    case 'ico':
    case 'jpg':
    case 'jpeg':
    case 'jpe':
    case 'png':
    case 'psd':
        $attachicon = '<img src="./images/mimetypes/gif.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'ppt':
        $attachicon = '<img src="./images/mimetypes/ppt.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'pdf':
        $attachicon = '<img src="./images/mimetypes/pdf.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'ra':
    case 'ram':
        $attachicon = '<img src="./images/mimetypes/real_audio.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'viv':
    case 'asx':
        $attachicon = '<img src="./images/mimetypes/win_player.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'mov':
    case 'tiff':
    case 'aiff':
    case 'mpg':
    case 'mpeg':
    case 'swf':
    case 'divx':
    case 'avi':
        $attachicon = '<img src="./images/mimetypes/quicktime.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'js':
    case 'ps':
        $attachicon = '<img src="./images/mimetypes/postscript.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'rtf':
        $attachicon = '<img src="./images/mimetypes/rtf.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    case 'php':
        $attachicon = '<img src="./images/mimetypes/php.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
    default:
        $attachicon = '<img src="./images/mimetypes/unknown.gif" alt="'.$lang['Attachicon_Alt'].'" title="'.$lang['Attachicon_Alt'].'" border="0" />&nbsp;';
        break;
}
?>