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
 * Based off XMB
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
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

require_once 'mimetypes.inc.php';

namespace GaiaBB;

class Attachment
{

    public $attachments;

    public function __construct()
    {
    }

    public function findByID()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }

    public function init()
    {
    }

    public function get_attachments($tid)
    {
        global $db, $start_limit, $self;

        $pids = array();
        $q = $db->query("SELECT pid FROM " . X_PREFIX . "posts WHERE tid = '$tid' ORDER BY pid LIMIT $start_limit, " . $self['ppp']);
        if ($q === false || $db->numRows($q) == 0) {
            $db->freeResult($q);
            return false;
        }

        while (($row = $db->fetchArray($q)) != false) {
            $pids[] = $row['pid'];
        }
        $db->freeResult($q);

        if (empty($pids)) {
            return false;
        }

        $pids = "'" . implode("', '", $pids) . "'";
        $this->attachments = array();
        $q = $db->query("SELECT * FROM " . X_PREFIX . "attachments WHERE pid IN($pids)");

        if ($q === false || $db->numRows($q) == 0) {
            $db->freeResult($q);
            return false;
        }

        while (($row = $db->fetchArray($q)) != false) {
            $this->attachments[] = $row;
        }
        $db->freeResult($q);

        return true;
    }

    public function get_post_attachments($pid)
    {
        global $CONFIG, $THEME, $lang, $post, $forum, $tid;
        global $n_height, $n_width, $attachicon, $postauthor;

        $retval = '';
        $attachments = $this->attachments;
        reset($attachments);

        $attachicon = '';
        foreach ($attachments as $attach) {
            if ((intval($attach['fileheight']) == 0 || intval($attach['filewidth']) == 0) && strpos($attach['filetype'], 'image') !== false) {
                $this->upgrade_attachment($attach);
            }

            if ($attach['pid'] == $pid) {
                $post['filename'] = htmlspecialchars($attach['filename']);
                if (!empty($attach['filename']) && isset($forum['attachstatus']) && $forum['attachstatus'] != 'off') {
                    $extension = strtolower(substr(strrchr($post['filename'], '.'), 1));
                    $attachsize = $this->format_attach($attach['filesize']);
                    $downloadcount = $attach['downloads'];
                    if ($downloadcount == '') {
                        $downloadcount = 0;
                    }

                    if ($CONFIG['viewattach'] == 'no' && X_GUEST) {
                        eval("\$post['message'] .= \"" . template('viewtopic_post_attachment_none') . "\";");
                    } elseif ($CONFIG['attachimgpost'] == 'on' && ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png' || $extension == 'bmp')) {
                        if ($attach['fileheight'] != '' && $attach['filewidth'] != '') {
                            $CONFIG['max_attheight'] = (int) $CONFIG['max_attheight'];
                            $CONFIG['max_attwidth'] = (int) $CONFIG['max_attwidth'];
                            $h_ratio = $CONFIG['max_attheight'] / $attach['fileheight'];
                            $w_ratio = $CONFIG['max_attwidth'] / $attach['filewidth'];
                            if (($attach['fileheight'] <= $CONFIG['max_attheight']) && ($attach['filewidth'] <= $CONFIG['max_attwidth'])) {
                                $n_height = $attach['fileheight'];
                                $n_width = $attach['filewidth'];
                            } elseif (($w_ratio * $attach['fileheight']) < $CONFIG['max_attheight']) {
                                $n_height = ceil($w_ratio * $attach['fileheight']);
                                $n_width = $CONFIG['max_attwidth'];
                            } else {
                                $n_height = $CONFIG['max_attheight'];
                                $n_width = ceil($h_ratio * $attach['filewidth']);
                            }
                        }

                        if ($CONFIG['attachborder'] == 'on') {
                            if ($CONFIG['attachicon_status'] == 'on') {
                                $attachicon = getMimeType($extension);
                            }
                            eval("\$post['message'] .= \"" . template('viewtopic_post_attachmentimage') . "\";");
                        } else {
                            if ($CONFIG['attachicon_status'] == 'on') {
                                $attachicon = getMimeType($extension);
                            }
                            eval("\$post['message'] .= \"" . template('viewtopic_post_attimg_noborder') . "\";");
                        }
                    } else {
                        if ($CONFIG['attachborder'] == 'on') {
                            if ($CONFIG['attachicon_status'] == 'on') {
                                $attachicon = getMimeType($extension);
                            }
                            eval("\$post['message'] .= \"" . template('viewtopic_post_attachment') . "\";");
                        } else {
                            if ($CONFIG['attachicon_status'] == 'on') {
                                $attachicon = getMimeType($extension);
                            }
                            eval("\$post['message'] .= \"" . template('viewtopic_post_attach_noborder') . "\";");
                        }
                    }
                }
            }
        }
    }

    public function upgrade_attachment(&$attach)
    {
        global $db;

        $aid = intval($attach['aid']);
        if ($aid == 0 || ini_get('safe_mode')) {
            return false;
        }

        $tempfn = tempnam("", "");
        $temp = fopen($tempfn, "w");
        fwrite($temp, $attach['attachment']);
        fclose($temp);
        $exsize = getimagesize($tempfn);
        unlink($tempfn);

        $attach['fileheight'] = intval($exsize[1]);
        $attach['filewidth'] = intval($exsize[0]);

        $db->query("UPDATE " . X_PREFIX . "attachments SET fileheight = " . $attach['fileheight'] . ", filewidth = " . $attach['filewidth'] . " WHERE aid = " . $aid);
    }

    public function format_attach($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = round($bytes / 1073741824 * 100) / 100 . "gb";
        } elseif ($bytes >= 1048576) {
            $bytes = round($bytes / 1048576 * 100) / 100 . "mb";
        } elseif ($bytes >= 1024) {
            $bytes = round($bytes / 1024 * 100) / 100 . "kb";
        } else {
            $bytes = $bytes . "b";
        }
        return $bytes;
    }

    public function fixOrphans(&$count, &$count2)
    {
        global $db;

        $query = $db->query("SELECT pid, aid FROM " . X_PREFIX . "attachments WHERE 1 ORDER BY pid ASC");
        $count = $count2 = 0;
        while (($attach = $db->fetchArray($query)) != false) {
            $count2++;
            $query2 = $db->query("SELECT pid FROM " . X_PREFIX . "posts WHERE pid = $attach[pid]");
            $thread = $db->fetchArray($query2);
            $db->freeResult($query2);
            if (empty($thread['pid'])) {
                $count++;
                $db->query("DELETE FROM " . X_PREFIX . "attachments WHERE pid = $attach[pid] AND aid = '$attach[aid]'");
            }
        }
        $db->freeResult($query);
    }
}
