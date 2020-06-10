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
     * indexExists() - test for index existence
     *
     * Test if a named database index is present
     *
     * @param string $table         Table name to test
     * @param string $indexname     Index name to test
     * @return boolean              true if the index exists, false if not
     **/
    public function indexExists($table, $indexname)
    {
        $query = $this->db->query("SELECT COUNT(1) IndexPresent FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema=DATABASE() AND table_name='" . X_PREFIX . $table . "' AND index_name='" . X_PREFIX . $indexname . "'");
        $indexPresent = $this->db->result($query, 0);
        return ($indexPresent > 0) ? true : false;
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
