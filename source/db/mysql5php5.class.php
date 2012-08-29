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

// check to ensure no direct viewing of page
if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false))
{
    exit('This file is not designed to be called directly');
}

define('X_DBCLASSNAME', 'mysql5Php5');
define('X_FRIENDLYNAME', 'MySQL 4.1 and 5.0');
define('X_DALMINPHP', '5.2.6');
define('X_DALMAXPHP', '5.9.9');

class mysql5Php5 {
    public $querynum = 0;
    public $querylist = array();
    public $querytimes = array();
    public $duration = 0;

    private $conn = null;           // Maintains the connection object
    private $result = null;         // Maintains the result object, if it exists
    private $db = '';
    private $timer = 0;
    private $queryStr = '';
    private $force = false;         // Used to force connections. When you want to panic, set to true, otherwise
                                    // it will soft error
    private $tablepre = '';

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function mysql5Php5() {
        $this->db = '';
        $this->conn = null;
        $this->result = null;
    }

    /**
    * function() - short description of function
    *
    * Long description of function
    *
    * @param    $varname    type, what it does
    * @return   type, what the return does
    */
    function connect($dbhost = "localhost", $dbuser, $dbpw, $dbname, $pconnect = 0, $force_db = false, $new_link = false, $tablepre = '')
    {
        try
        {
            if (!empty($tablepre))
            {
                $this->tablepre = $tablepre;
            }
            else
            {
                $this->tablepre = X_PREFIX;
            }

            $this->force = $force_db;

            if ((version_compare(phpversion(), "5.2.6")) < 0)
            {
                throw new Exception("Unsupported PHP version");
            }

            $this->conn = new mysqli($dbhost, $dbuser, $dbpw);
            if (mysqli_connect_errno())
            {
                throw new Exception("Could not connect to the database server");
            }

            if ((version_compare($this->getVersion(), "4.1.0")) == -1)
            {
                throw new Exception("Unsupported MySQL version");
            }

            if ($this->select_db($dbname, $force_db) === false)
            {
                throw new Exception("Could not select the database");
            }
        }
        catch (Exception $error)
        {
            $this->panic("Database connection error", $error);
            return false;
        }

        unset($GLOBALS['dbhost'], $GLOBALS['dbuser'], $GLOBALS['dbpw']);
        return true;
    }

    /**
    * panic() - an fatal error has occured. Stop right now
    *
    * @param    $head   string, the heading of the warning
    * @param    $msg    string, the message to display
    * @return   Does not return
    */
    function panic($head, $msg)
    {
    	global $CONFIG;
    	
    	// TODO: make this configurable as not every host has mail configured
    	// TODO: make this use the SMTP classes, if possible
    	if ( isset($CONFIG['adminemail']) && isset($CONFIG['bbname']) ) 
    	{
			mail($CONFIG['adminemail'], 'GaiaBB :: Database panic from '.$CONFIG['bbname'], $msg->getMessage() . "\r\n" . $this->conn->error );    		
    	}
		
        $this->view_header($head);
        ?>
        <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center" bgcolor="#5176B5">
        <tr>
        <td>
        <table border="0" cellspacing="1px" cellpadding="5px" width="100%">
        <tr>
        <td class="category"><font color="#000000"><strong><?php echo $head?></strong></font></td>
        </tr>
        <tr>
	<?php
	if ( DEBUG ) { ?>
        <td class="tablerow" bgcolor="#ffffff" align="left"><?php echo $msg->getMessage()?></td>
	<?php 
	} else { ?>
        <td class="tablerow" bgcolor="#ffffff" align="left">A suffusion of yellow. Please wait a few minutes and try again</td>
	<?php } ?> 
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <?php
        $this->view_shadow();

        // DEBUG mode is a security issue, but with panic() we have no database context
        // => no X_SADMIN, so no error messages possible. So this code warns of bad things
        // with DEBUG
        if (defined('DEBUG') && DEBUG)
        {
            $errnum = mysqli_connect_errno();
            $errmsg = mysqli_connect_error();
            if (empty($errmsg))
            {
                $errmsg = "No MySQL error";
            }
            ?>
            <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center" bgcolor="#5176B5">
            <tr>
            <td>
            <table border="0" cellspacing="1px" cellpadding="5px" width="100%">
            <tr>
            <td class="category"><font color="#000000"><strong>Detailed Debugging Information</strong></font></td>
            </tr>
            <tr>
            <td class="tablerow" bgcolor="#ffffff" align="left">
            Security warning: DEBUG should be set to false in production
            </td>
            </tr>
            <tr>
            <td class="tablerow" bgcolor="#ffffff" align="left">
            MySQL error <?php echo $errnum?>, Error Message: <?php echo $errmsg?></td>
            <tr>
            <td class="tablerow" bgcolor="#ffffff" align="left">
            Exception code <?php echo $msg->getCode()?> occurred on line <?php echo $msg->getLine()?> in file <?php echo $msg->getFile()?>
            </td>
            </tr>
            <tr>
            <td class="tablerow" bgcolor="#ffffff" align="left">
            Last Query:<br />
            <?php echo $this->queryStr; ?>
            </td>
            </tr>
            <tr>
            <td class="tablerow" bgcolor="#ffffff" align="left">Stack trace:<br />
            <?php
            $traces = explode('#', $msg->getTraceAsString());
            foreach ($traces as $trace)
            {
                echo $trace. '<br />';
            }
            ?>
            </td>
            </tr>
            </table>
            </td>
            </tr>
            </table>
            <?php
            $this->view_shadow();
        } // end debug
        ?>
        <?php
        $this->view_footer();
        exit;
    }

    /**
    * select_db() - select the database
    *
    * @param    $database   string - the database name to select
    * @return   true if success, exits if not (this is a panic)
    */
    function select_db($database)
    {
        try
        {
            $this->db = $database;
            if ($this->conn->select_db($this->db) === false)
            {
                throw new Exception("Could not locate the database. Please check the configuration.");
            }

            if ($this->force && $this->find_database($this->db) === false)
            {
                throw new Exception('Could not find any database containing the needed tables. Please reconfigure config.php or install GaiaBB');
            }
        }
        catch (Exception $error)
        {
            if ($this->force)
            {
                $this->close();
            }
            $this->panic("Database connection error", $error);
            return false;
        }

        return true;
    }

    /**
    * find_database() - See if the nominated database has the tables we need
    *
    * As this is checked for every page, we only check for the settings
    * table. If the others aren't present, then we can't help that
    *
    * @return   bool, true if success, false if fail
    */
    function find_database()
    {
        $tables = $this->getTables();
        if ($tables === false)
        {
            return false;
        }

        return in_array($this->tablepre.'settings', $tables);
    }

    /**
    * error() - Get the error message from MySQL
    *
    * If the link doesn't exist, we test for the connect error
    *
    * @return   string, the error message
    */
    function error()
    {
        if ($this->conn)
        {
            return $this->conn->error;
        }
        return mysqli_connect_error();
    }

    /**
    * free_result() - free query result
    *
    * This frees up needed resources on intensive pages, like post.php
    * Call me often
    *
    * @param    $result  A result set requiring to be freed
    * @return   bool, true = success, false = failure
    */
    function free_result($result = null)
    {
        if ($result)
        {
            @$result->free();
        }
        if ($this->result !== null && $this->result !== false && $this->result !== true)
        {
            @$this->result->free();
        }

        $this->result = null;
        return true;
    }

    /**
    * fetch_array() - return an array of results from the query
    *
    * Use this to gather results from previous queries
    *
    * @param    $result result set from a previously executed query
    * @return   array of results, or null if no more results or false if no previous result set
    */
    function fetch_array($query, $type = MYSQLI_ASSOC)
    {
        if ($query !== null || $query !== false)
        {
            return $query->fetch_array($type);
        }
        else if ($this->result != null && $this->result !== true && $this->result !== false)
        {
            return $this->result->fetch_array($type);
        }

        return false;
    }

    /**
    * field_type() - return the field type from a previously executed query
    *
    * This is primarily used by the dbinfo admin panel
    *
    * @param    $result     MySQL 5 query result resource
    * @param    $field      int, the field you'd like the type of
    * @return   string, the field name, false fail
    */
    function field_type($result, $field)
    {
        $result->field_seek($field);
        $fieldInfo = $result->fetch_field();
        return $fieldInfo->type;
    }

    /**
    * field_name() - return the field name from a previously executed query
    *
    * This is primarily used by the dbinfo admin panel
    *
    * @param    $result     MySQL 5 query result resource
    * @param    $field      int, the field you'd like the name of
    * @return   string, the field name, false fail
    */
    function field_name($result, $field)
    {
        $result->field_seek($field);
        $fieldInfo = $result->fetch_field();
        return $fieldInfo->name;
    }

    /**
    * field_len() - return the field length from a previously executed query
    *
    * This is primarily used by the dbinfo admin panel
    *
    * @param    $result     MySQL 5 query result resource
    * @param    $field      int, the field you'd like the length of
    * @return   string, the field name, false fail
    */
    function field_len($result, $field)
    {
        $result->field_seek($field);
        $fieldInfo = $result->fetch_field();
        return $fieldInfo->max_length;
    }

    /**
    * field_flags() - return the field flags from a previously executed query
    *
    * This is primarily used by the dbinfo admin panel
    *
    * @param    $result     MySQL 5 query result resource
    * @param    $field      int, the field you'd like the flags of
    * @return   string, the field name, false fail
    */
    function field_flags($result, $field)
    {
        $result->field_seek($field);
        $fieldInfo = $result->fetch_field();
        return $fieldInfo->flags;
    }

    /**
    * field_table() - return the table name of a field
    *
    * This is primarily used by the dbinfo admin panel
    *
    * @param    $result     MySQL 5 query result resource
    * @param    $field      int, the field you'd like the tablename of
    * @return   string, the field name, false fail
    */
    function field_table($result, $field)
    {
        $result->field_seek($field);
        $fieldInfo = $result->fetch_field();
        return $fieldInfo->table;
    }

    /**
    * query() - Query the database using basic strings
    *
    * This is not the best way of addressing the database
    * as it intermingles data and instructions, which means that
    * instructions may come from attack data. However, there's
    * lots of queries in GaiaBB, so we need this for a while
    * yet.
    *
    * @param    $sql    the SQL query
    * @return   result set if good, false if bad
    */
    function query($sql)
    {
        try
        {
            $this->queryStr = $sql;

            if (!$this->conn)
            {
                throw new Exception("The database server is not connected.");
            }

            $this->start_timer();

            $this->result = $this->conn->query($sql);

            if ($this->result === false || $this->conn->errno !== 0)
            {
                throw new Exception("The database server has not processed the query. Please try again.");
            }

            $this->querynum++;
	    if ( DEBUG ) {
		    $this->querylist[] = $sql;
	    }
            $this->querytimes[] = $this->stop_timer();

            return $this->result;
        }
        catch (Exception $error)
        {
            $this->result = null;
            $this->panic("Query Failed", $error);
        }
        return false;
    }

    /**
    * result() - return a single value from a single value query
    *
    * @param    $result, record set obtained from a previous query
    * @param    $row, the row to use. Typically, it's 0
    * @param    $field, the named field you'd like back
    * @return   a mixed result based upon the query, false if no data
    */
    function result($result, $row = 0, $field = NULL)
    {
        if ($result === false || $result === null)
        {
            $result = $this->result;
        }

        $result->data_seek($row);
        if ($field == NULL)
        {
            $field = 0;
        }
        $tmp = $result->fetch_array();

        // In MySQL 4, result() returned false, so we do too.
        if ($tmp == null)
        {
            return false;
        }

        $tmp = $tmp[$field];
        return($tmp);
    }

    /**
    * num_rows() - return the number of rows in a query
    *
    * @param    $result  result resource
    * @return   int, number of rows, false if failed
    */
    function num_rows($result)
    {
        if ($result)
        {
            return $result->num_rows;
        }
        else if ($this->result)
        {
            return $this->result->num_rows;
        }
        return false;
    }

    /**
    * num_fields() - return the number of fields in a query
    *
    * @param    $result  the result from the previous successful query
    * @return   int, number of fields, false if failed
    */
    function num_fields($result)
    {
        if ($result !== null && $result !== false)
        {
            return $result->field_count;
        }
        else if ($this->result !== null && $this->result !== false)
        {
            return $this->result->field_count;
        }
        return false;
    }

    /**
    * insert_id() - find the row ID of the last query
    *
    * @return   int if success, false if fail
    */
    function insert_id()
    {
        if ($this->conn)
        {
            return $this->conn->insert_id;
        }
        return false;
    }

    /**
    * getNextId() - find the next auto_increment value for any given table
    *
    * @return   int if success, false if fail
    */
    function getNextId($table = '')
    {
        if ( !empty($table) )
        {
            $table = $this->tablepre.$table;
            $query = $this->query("SHOW TABLE STATUS FROM $this->db LIKE '$table'");
            $column = $this->fetch_array($query);
            $retval = (int) $column['Auto_increment'];

            return $retval;
        }
        return false;
    }

    /**
    * fetch_row() - fetch a row from the result set
    *
    * @param    $result, result set from the user
    * @return   array of strings, the row
    */
    function fetch_row($result)
    {
        if ($result)
        {
            return $result->fetch_row();
        }
        else if ($this->result)
        {
            return $this->result->fetch_row();
        }
        return false;
    }

    /**
     * time() - create a SQL timestamp
     *
     * @param    $time, optional, an arbitrary point in time
     * @return   string, the left padded timestamp suitable for SQL queries
     */
    function time($time=NULL)
    {
    	global $onlinetime;
    
    	if ($time === NULL)
    	{
    		$time = $onlinetime;
    	}
    	return "LPAD('".$time."', '15', '0')";
    }
    
    /**
    * stop_timer() - start the query timer
    *
    * @return   always returns true
    */
    function start_timer()
    {
        $mtime = explode(' ', microtime());
        $this->timer = $mtime[1] + $mtime[0];
        return true;
    }

    /**
    * stop_timer() - stop the query timer
    *
    * @return   double, the number of seconds taken for this set of queries
    */
    function stop_timer()
    {
        $mtime = explode(' ', microtime());
        $endtime = $mtime[1] + $mtime[0];
        $taken = ($endtime - $this->timer);
        $this->duration += $taken;
        $this->timer = 0;
        return $taken;
    }

    /**
    * Get a list of tables
    *
    * The ye olde mysql4 way is no longer supported, so this way
    * returns an array just like in the olden days. This obviously
    * is not as fast as you'd like. Avoid using this function in
    * high performance situations
    *
    * @param    $database   string, the database name
    * @return   array of strings, a list of tables
    */
    function getTables()
    {
        $tables = array();
        try
        {
            if (!$this->conn)
            {
                throw new Exception("Not connected to the database");
            }

            if (empty($this->db))
            {
                throw new Exception("No database selected.");
            }

            $result = $this->conn->query("SHOW TABLES FROM ".$this->db);

            if ($result == null)
            {
                throw new Exception("No database tables can be found in the database.");
            }

            while ($table = $result->fetch_array())
            {
                $tables[] = $table[0];
            }

            $result->free_result();
        }
        catch (Exception $error)
        {
            if ($this->force)
            {
                $this->panic("List tables", $error);
            }
            return false;
        }

        return $tables;
    }

    /**
    * close() - close down access to the MySQL Server
    *
    * This frees up a connection if you're done with it
    * But when a page is exited, that happens anyway, so
    * don't get too worried about calling me
    *
    */
    function close()
    {
        if ($this->conn)
        {
            $this->conn->close();
        }
        $this->db = '';
        $this->conn = null;
        $this->result = null;
    }

    /**
    * version() - Find the version of the MySQL Server
    *
    * @return   string, the version in "5.x.x" format
    */
    function getVersion()
    {
        if ($this->conn)
        {
            return $this->conn->get_server_info();
        }
        return false;
    }

    /**
    * escape() - sanitize data suitable for the database
    *
    * Basic SQL injection prevention
    *
    * @param    $str    string, data to be sanitized
    * @param    $length int, max length of the data (this function will truncate it to that)
    * @return   string, the sanitized string
    */
    function escape($str, $length = -1, $like=false)
    {
        if ($length != -1 && strlen($str) >= $length)
        {
            $str = substr($str, 0, $length);
        }

        // Purposely slow down crap configurations to ensure consistency
        if (get_magic_quotes_gpc() == 1)
        {
            stripslashes($str);
        }

        // Get rid of two more suspects only used in LIKE clauses.
        if ($like == false)
        {
            $str = str_replace('%', '\%', $str);
//            $str = str_replace('_', '\_', $str);
        }
        // Encode the data
        if ($this->conn)
        {
            $str = $this->conn->real_escape_string($str);
        }
        else
        {
            $str = addslashes($str);
        }
        return $str;
    }

    function insertAttachment($table, $aid, $tid, $pid, $filename, $filetype, $filesize, $fileheight, $filewidth, & $attachment, $downloads = 0)
    {
        try
        {
            $insert_sql = "INSERT INTO ".$table." (aid, tid, pid, filename, filetype, filesize, fileheight, filewidth, attachment, downloads) VALUES (?,?,?,?,?,?,?,?,?,?)";

            $statement = $this->conn->prepare($insert_sql);
            if ($statement === false)
            {
                throw new Exception("Cannot prepare attachment for insertion");
            }

            $retval = $statement->bind_param("iiissiiibi", $aid, $tid, $pid, $filename, $filetype, $filesize, $fileheight, $filewidth, $attachment, $downloads);
            if ($retval === false)
            {
                throw new Exception("Cannot bind attachment for insertion");
            }

            $retval = $statement->execute();
            if ($retval === false)
            {
                throw new Exception("Failed to execute attachment insertion");
            }

            $statement->reset();
        }
        catch (Exception $error)
        {
            $this->panic("Failed to insert attachment: ". $statement->error, $error);
        }

        return $retval;
    }
    /**
     * function() - short description of function
     *
     * Long description of function
     *
     * @param    $varname    type, what it does
     * @return   type, what the return does
     */
    function view_header($title = "Data Abstraction Layer")
    {
    	$logo = ROOT.'images/prored/logo.gif';
    	$navtext = "Data Abstraction Layer";
    	$css = ROOT.'images/prored/style.css';
    	$imgpath = ROOT.'images/prored/';
    	?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
            <title>
            <?php echo $title ?>
            </title>
            <link rel=stylesheet type="text/css" href="<?php echo $css?>" title="Stylesheet">
            <style type="text/css">@import url("<?php echo $css ?>");</style>
            </head>
            <body bgcolor="#C9675D" text="#000000">
            <a name="top"></a>
            <table cellspacing="0" cellpadding="0" border="0" width="100%" bgcolor="#BB6255" align="center"><tr><td><table border="0" cellspacing="1px" cellpadding="5px" width="100%" align="center"><tr><td bgcolor="#FFFFFF" align="center"><br />
            <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center">
            <tr>
            <td bgcolor="#BB6255"><table border="0" cellspacing="1" cellpadding="6" width="100%">
            <tr>
            <td width="74%" style="background-image: url(<?php echo $imgpath?>topbg.gif); text-align: left"><table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
            <td valign="top" rowspan="2"><a href="index.php"><img src="<?php echo $imgpath?>logo.gif" alt="Your Forums" title="Your Forums" border="0" /></a></td>
            <td align="right" valign="top"><font class="smalltxt">Last active: Never<br />
            </font>
            </td>
            </tr>
            <tr>
            <td align="right" valign="bottom"><font class="smalltxt">[ <?php echo $navtext?> ]</font></td>
            </tr>
            </table>
            </td>
            </tr>
            <tr>
            <td class="navtd">
            <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
            <td align="right"><a href="<?php echo ROOT?>index.php"><font class="navtd">Back to: <img src="<?php echo $imgpath?>home.gif" border="0" alt="Your Forums" title="Your Forums" /></font></a></td>
            </tr>
            </table>
            </td>
            </tr>
            </table>
            </td>
            </tr>
            </table>
            <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center">
            <tr>
            <td width="100%" align="left"><img src="<?php echo $imgpath?>shadow.gif" border="0" alt="" title="" /></td>
            </tr>
            </table>
            <table cellspacing="0" cellpadding="1" border="0" width="97%" align="center">
            <tr>
            <td>
            <table width="100%" cellspacing="0" cellpadding="2" align="center">
            <tr>
            <td class="nav" align="left"> <a href="index.php"><?php echo $navtext?></a> </td>
            <td align="right">&nbsp;</td>
            </tr>
            </table>
            </td>
            </tr>
            </table>
            <br />
            <?php
        }
    
        /**
        * function() - short description of function
        *
        * Long description of function
        *
        * @param    $varname    type, what it does
        * @return   type, what the return does
        */
        function view_footer()
        {
            ?>
            <table align="center">
            <tr>
            <td align="center" class="smalltxt">
            <br />
            <br />
            Powered by GaiaBB
            <br />
            Copyright &copy; 2011 The GaiaBB Group. All rights reserved.
            <br />
            <br />
            </td>
            </tr>
            </table>
            <br /></td></tr></table></td></tr></table>
            <a id="bottom" name="bottom"></a>
            </body>
            </html>
            <?php
        }
    
        /**
        * function() - short description of function
        *
        * Long description of function
        *
        * @param    $varname    type, what it does
        * @return   type, what the return does
        */
        function view_shadow()
        {
            ?>
            <table cellspacing="0" cellpadding="0" border="0" width="97%" align="center">
            <tr>
            <td width="100%" align="left"><img src="<?php echo ROOT?>images/prored/shadow.gif" border="0" alt="" title="" /></td>
            </tr>
            </table>
            <?php
        }
        
}


/**
* MYSQL_NUM Definition, returning rows in numerical fashion
*/
define('SQL_NUM', MYSQLI_NUM);

/**
* MYSQL_BOTH Definition, returning rows in both numerical and associative fashion
*/
define('SQL_BOTH', MYSQLI_BOTH);

/**
* MYSQL_ASSOC Definition, returning rows in associative fashion
*/
define('SQL_ASSOC', MYSQLI_ASSOC);

/**
* Does MySQL 5 meet SQL 2003 standards? Yes.
*/
define('SQL2003', false);
?>
