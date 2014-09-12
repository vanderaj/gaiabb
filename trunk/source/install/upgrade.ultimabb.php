<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2013 The GaiaBB Group
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

require_once "upgrade.model.php";

class upgrade_ultimaBB extends Upgrade
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
    function rename_tables($prg)
    {
        setBar($this->prgbar, $prg);
        
        return true;
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
    function add_tables($prg)
    {
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        if (! $this->table_exists('faq')) {
            schema_create_faq($this->db, X_PREFIX);
            schema_insert_faq($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        if (! $this->table_exists('guestcount')) {
            schema_create_guestcount($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        if (! $this->table_exists('pluglinks')) {
            schema_create_pluglinks($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        if (! $this->table_exists('robotcount')) {
            schema_create_robotcount($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        if (! $this->table_exists('subscriptions')) {
            schema_create_subscriptions($this->db, X_PREFIX);
        }
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        return true;
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
    function delete_tables($prg)
    {
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        if ($this->table_exists('calendar')) {
            $this->db->query("DROP TABLE `" . X_PREFIX . "calendar`");
        }
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        if ($this->table_exists('events')) {
            $this->db->query("DROP TABLE `" . X_PREFIX . "events`");
        }
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        if ($this->table_exists('holidays')) {
            $this->db->query("DROP TABLE `" . X_PREFIX . "holidays`");
        }
        setBar($this->prgbar, $prg);
        $prg += 0.01;
        
        return true;
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
    function alter_tables($prg)
    {
        setBar($this->prgbar, $prg);
        $prg += 0.05;
        
        switch ($this->schemaver) {
            case 34:
                $query = "ALTER TABLE `" . X_PREFIX . "forums` ";
                $query .= "CHANGE `postperm` `postperm` varchar(7) NOT NULL DEFAULT ''";
                $this->db->query($query);
                
                setBar($this->prgbar, $prg);
                $prg += 0.05;
                
                $query = "ALTER TABLE `" . X_PREFIX . "settings` ";
                $query .= "ADD `login_max_attempts` smallint(2) NOT NULL DEFAULT '5'";
                
                $this->db->query($query);
                
                setBar($this->prgbar, $prg);
                $prg += 0.05;
                break;
            case 35:
                $query = "ALTER TABLE `" . X_PREFIX . "members` ";
                $query .= "ADD `forcelogout` set('yes','no') NOT NULL DEFAULT 'no'";
                
                $this->db->query($query);
                
                setBar($this->prgbar, $prg);
                $prg += 0.05;
            default:
                break;
        }
        
        $query = "UPDATE `" . X_PREFIX . "settings` SET schemaver='36'";
        $this->db->query($query);
        
        setBar($this->prgbar, $prg);
        
        return true;
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
    function migrate_data($prg)
    {
        setBar($this->prgbar, $prg);
        $prg += 0.1;
        
        reset_templates($this->db, X_PREFIX);
        
        setBar($this->prgbar, $prg);
        
        return true;
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
    function migrate_settings($prg)
    {
        return true;
    }
}
?>