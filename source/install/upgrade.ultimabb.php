<?php

/**
 * GaiaBB
 * Copyright (c) 2011-2025 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB's installer (ajv)
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
 **/

// phpcs:disable PSR1.Files.SideEffects

namespace GaiaBB;

require_once "upgrade.model.php";

class UpgradeUltimaBB extends Upgrade
{
    public function __construct($indb, $in_prgbar)
    {
        parent::__construct($indb, $in_prgbar);
    }

    /**
     * renameTables() - rename database tables
     *
     * GaiaBB has same table names as UltimaBB
     *
     * @param  $prg - progress percentage
     * @return boolean - completed
     */
    public function renameTables($prg)
    {
        setBar($this->prgbar, $prg);

        return true;
    }

    /**
     * addTables() - add any new tables
     *
     * GaiaBB checks to see if FAQ tables exist and puts them back if not
     *
     * @param  $prg - progress percentage
     * @return boolean - completed
     */
    public function addTables($prg)
    {
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        if (!$this->tableExists('faq')) {
            schema_create_faq($this->db, X_PREFIX);
            schema_insert_faq($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        if (!$this->tableExists('guestcount')) {
            schema_create_guestcount($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        if (!$this->tableExists('pluglinks')) {
            schema_create_pluglinks($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        if (!$this->tableExists('robotcount')) {
            schema_create_robotcount($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        if (!$this->tableExists('subscriptions')) {
            schema_create_subscriptions($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        return true;
    }

    /**
     * deleteTables() - delete any unnecessary tables
     *
     * GaiaBB removes a calendar and events from the UltimaBB database
     *
     * @param  $prg - progress percentage
     * @return boolean - completed
     */
    public function deleteTables($prg)
    {
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        if ($this->tableExists('calendar')) {
            $this->db->query("DROP TABLE `" . X_PREFIX . "calendar`");
        }
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        if ($this->tableExists('events')) {
            $this->db->query("DROP TABLE `" . X_PREFIX . "events`");
        }
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        if ($this->tableExists('holidays')) {
            $this->db->query("DROP TABLE `" . X_PREFIX . "holidays`");
        }
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.01;

        return true;
    }

    /**
     * alterTables() - alter database tables
     *
     * Change the schema from UltimaBB to GaiaBB's
     *
     * @param  $prg - progress percentage
     * @return boolean - completed
     */
    public function alterTables($prg)
    {
        setBar($this->prgbar, $prg);
        $this->prgbar += 0.05;

        switch ($this->schemaver) {
            case 34:
                $query = "ALTER TABLE `" . X_PREFIX . "forums` ";
                $query .= "CHANGE `postperm` `postperm` varchar(7) NOT NULL DEFAULT ''";
                $this->db->query($query);

                setBar($this->prgbar, $prg);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "settings` ";
                $query .= "ADD `login_max_attempts` smallint(2) NOT NULL DEFAULT '5'";

                $this->db->query($query);

                setBar($this->prgbar, $prg);
                $this->prgbar += 0.05;
                break;

            case 35:
                $query = "ALTER TABLE `" . X_PREFIX . "members` ";
                $query .= "ADD `forcelogout` set('yes','no') NOT NULL DEFAULT 'no'";

                $this->db->query($query);

                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;
                break;

            case 41:
                $query = "ALTER TABLE `" . X_PREFIX . "adminlogs` MODIFY action TEXT NOT NULL ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "faq` MODIFY name TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "faq` MODIFY description TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "forums` MODIFY userlist TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "forums` MODIFY mt_open TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "forums` MODIFY mt_close TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "forums` MODIFY subjectprefixes TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "members` MODIFY sig TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "members` MODIFY pmfolders TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "members` MODIFY notepad TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "plugins` MODIFY name TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "plugins` MODIFY url TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "plugins` MODIFY img TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "pm` MODIFY subject TINYTEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "pm` MODIFY message TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "posts` MODIFY subject TINYTEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "posts` MODIFY message TEXT NOT NULL DEFAULT '' ";
                $this->db->query($query);

                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                // Indexes

                // Some index schemas may not be well constructed, so we're going to rebuild it
                if ($this->indexExists(X_PREFIX . 'members', 'threadnum')) {
                    $query = "ALTER TABLE `" . X_PREFIX . "members` DROP INDEX threadnum ";
                    $this->db->query($query);
                }

                if ($this->indexExists(X_PREFIX . 'members', 'invisible')) {
                    $query = "ALTER TABLE `" . X_PREFIX . "members` DROP INDEX invisible_idx ";
                    $this->db->query($query);
                }

                if ($this->indexExists(X_PREFIX . 'members', 'invisible_idx')) {
                    $query = "ALTER TABLE `" . X_PREFIX . "members` DROP INDEX invisible_idx ";
                    $this->db->query($query);
                }

                $query = "ALTER TABLE `" . X_PREFIX . "settings` DROP PRIMARY KEY ";
                $this->db->query($query);

                if ($this->indexExists(X_PREFIX . 'whosonline', 'username')) {
                    $query = "ALTER TABLE `" . X_PREFIX . "members` DROP INDEX username ";
                    $this->db->query($query);
                }

                if ($this->indexExists(X_PREFIX . 'whosonline', 'username_idx')) {
                    $query = "ALTER TABLE `" . X_PREFIX . "members` DROP INDEX username_idx ";
                    $this->db->query($query);
                }

                $query = "ALTER TABLE `" . X_PREFIX . "members` ADD INDEX threadnum (`threadnum`) ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "members` ADD INDEX invisible (`invisible`) ";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "settings` CREATE PRIMARY KEY (`config_name`)";
                $this->db->query($query);

                $query = "ALTER TABLE `" . X_PREFIX . "whosonline` ADD INDEX username (`username`(8))";
                $this->db->query($query);

                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                // Change character sets to a modern alternative
                $query = "ALTER TABLE `" . X_PREFIX . "addresses` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "attachments` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "banned` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "dateformats` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "faq` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "favorites` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "forums` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "guestcount` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "lastposts` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "members` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "modlogs` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "pluglinks` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "pm` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;
                $query = "ALTER TABLE `" . X_PREFIX . "pm_attachments` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "posts` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "ranks` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "restricted` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "robotcount` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "robots` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "settings` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "smilies` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "subscriptions` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "templates` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "themes` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "threads` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "vote_desc` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "vote_results` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "vote_voters` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "whosonline` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;

                $query = "ALTER TABLE `" . X_PREFIX . "words` CONVERT TO CHARACTER SET utf8mb4";
                $this->db->query($query);
                setBar($this->prgbar, $this->prgbar);
                $this->prgbar += 0.05;
                break;

            default:
                break;
        }

        $query = "UPDATE `" . X_PREFIX . "settings` SET schemaver='50'";
        $this->db->query($query);

        setBar($this->prgbar, $this->prgbar);

        return true;
    }

    /**
     * migrateData() - migrate data from UltimaBB
     *
     * As UltimaBB and GaiaBB are close cousins, main thing is to reset templates
     *
     * @param  $prg - progress percentage
     * @return boolean - completed
     */
    public function migrateData($prg)
    {
        setBar($this->prgbar, $prg);
        $prg += 0.1;

        reset_templates($this->db, X_PREFIX);

        setBar($this->prgbar, $prg);

        return true;
    }

    /**
     * migrateSettings() - migrate settings from UltimaBB
     *
     * As UltimaBB and GaiaBB are close cousins, nothing to do
     *
     * @param  $prg - progress percentage
     * @return boolean - completed
     */
    public function migrateSettings($prg)
    {
        return true;
    }
}
