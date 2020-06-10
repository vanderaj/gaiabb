<?php

namespace GaiaBB;

class PmDAO
{

    public function __construct()
    {
    }

    public function insertPm($to, $from, $to_uid, $from_uid, $type, $owner, $folder, $subject, $message, $isRead, $isSent, $usepmsig)
    {
        global $db, $onlinetime;

        $to = $db->escape($to, -1, true);
        $from = $db->escape($from, -1, true);
        $to_uid = $db->escape($to_uid);
        $from_uid = $db->escape($from_uid);
        $type = $db->escape($type);
        $owner = $db->escape($owner, -1, true);
        $folder = $db->escape($folder);
        $subject = $db->escape($subject);
        $message = $db->escape($message);
        $isRead = $db->escape($isRead);
        $isSent = $db->escape($isSent);
        $usepmsig = $db->escape($usepmsig);

        $result = $db->query("INSERT INTO " . X_PREFIX . "pm (pmid, msgto, msgfrom, msgto_uid, msgfrom_uid, type, owner, folder, subject, message, dateline, readstatus, sentstatus, usesig) VALUES ('', '$to', '$from', '$to_uid', '$from_uid', '$type', '$owner', '$folder', '$subject', '$message', '$onlinetime', '$isRead', '$isSent', '$usepmsig')");
        if ($result == false) {
            return false;
        }
        return $db->insertId();
    }
}

