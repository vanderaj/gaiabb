<?php

namespace GaiaBB;

class Upgrade
{
    public $db;
    public $prgbar;
    public $schemaver;

    /**
     * __construct() - constructor for Upgrade base class
     *
     * Constructor
     *
     */
    public function __construct($indb, $in_prgbar)
    {
        $this->db = $indb;
        $this->prgbar = $in_prgbar;

        if ($this->columnExists('settings', 'schemaver')) {
            $query = $this->db->query("SELECT schemaver FROM `" . X_PREFIX . "settings`");
            $this->schemaver = $this->db->result($query, 0);
            $this->db->freeResult($query);
        }
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function columnExists($table, $column)
    {
        $query = $this->db->query("SHOW COLUMNS FROM `" . X_PREFIX . $table . "` LIKE '" . $column . "'");
        $rows = $this->db->numRows($query);
        return ($rows > 0) ? true : false;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function tableExists($table)
    {
        $query = $this->db->query("SHOW TABLES LIKE '" . X_PREFIX . $table . "'");
        $rows = $this->db->numRows($query);
        return ($rows > 0) ? true : false;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function renameTables($prg)
    {
        return true;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function addTables($prg)
    {
        return true;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function deleteTables($prg)
    {
        return true;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function alterTables($prg)
    {
        return true;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function migrateData($prg)
    {
        return true;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param $varname type,
     *            what it does
     * @return type, what the return does
     */
    public function migrateSettings($prg)
    {
        return true;
    }
}
