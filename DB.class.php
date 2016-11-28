<?php
//
// @version 1.0
// @author Youssef ES-SEQALLY
// @link http://proweb.ma
//


class DB {
    private $version = "1.0";
    private $dbh;
    private $stmt;
    private $tableName = "";
    private $last_insert_id = "";
    private $last_query = "";
    //
    // @function:   __construct
    // @since: 1.0
    //
    // Used to connect to MySQL server
    //
    public function __construct($db_host=null, $db_user=null, $db_pass=null, $db_dbname=null) {
        // Connect
        $this->dbh = new PDO("mysql:host=$db_host;dbname=$db_dbname",
            $db_user,
            $db_pass,
            array( PDO::ATTR_PERSISTENT => true )
        );
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    //
    // @function:   table
    // @since :     1.0
    //
    // Sets the table for the query
    //
    public function table($table) {
        $this->tableName = $table;
        return $this;
    }
    //
    // @function:   query
    // @since :     1.0
    //
    // Write a full query using this function.
    //
    public function query($query) {
        $this->last_query = $query;
        $this->stmt = $this->dbh->prepare($query);
        return $this;
    }
    //
    // @function:   execute
    // @since :     1.0
    //
    // execute the query
    //
    public function execute() {
        $this->stmt->execute();
        $this->last_insert_id = $this->dbh->lastInsertId();
        return $this;
    }
    //
    // @function:   row
    // @since :     1.0
    //
    // fetch a single row after execution
    //
    public function row() {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
    //
    // @function:   bind
    // @since :     1.0
    //
    // bind a value to a prepared query
    //
    public function bind($pos, $value, $type = null) {
        if( is_null($type) ) {
            switch( true ) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($pos, $value, $type);
        return $this;
    }
    //
    // @function:   error
    // @since :     1.0
    //
    public function error($error) {
        die($error);
    }
    //
    // @function:   last_insert_id
    // @since :     1.0
    //
    public function last_insert_id() {
        return $this->last_insert_id;
    }
    //
    // @function:   last_query
    // @since :     1.0
    //
    public function last_query() {
        return $this->last_query;
    }
    //
    // @function:   num_results
    // @since :     1.0
    //
    public function num_results() {
        return $this->stmt->rowCount();
    }
    //
    // @function:   clean
    // @since :     1.0
    //
    public function clean() {
        $this->stmt->closeCursor();
        return $this;
    }
    //
    // @function:   insertupdate
    // @since :     1.0
    //
    // Use function to Either Insert or Update depending on Primary Key
    //
    public function insertupdate($data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $prepare = array_keys($data);
        for ($i=0; $i<count($values); $i++) {
            $prepare[$i] = ':' . $prepare[$i];
        }
        $sqlinsert = "INSERT INTO `".$this->tableName."` (`".implode('`, `', $fields)."`) VALUES (".implode(', ', $prepare).")";
        $sqlupdates = array();
        foreach ($data as $key => $val) $sqlupdates[] = "`".$key."` = :".($key).'2';
        $sqlupdate = "UPDATE ".implode(', ', $sqlupdates);
        $query = $sqlinsert." ON DUPLICATE KEY ".$sqlupdate;
        $this->query($query);
        // Bind INSERT Vars
        for ($i=0; $i<count($values); $i++) {
            $this->bind($prepare[$i], $values[$i]);
        }
        // Bind UPDATE Vars
        for ($i=0; $i<count($values); $i++) {
            $this->bind($prepare[$i].'2', $values[$i]);
        }
        $this->execute();
        return $this->last_insert_id();
    }
    public function debugInfo() {
        return $this->stmt->debugDumpParams();
    }
    public function version() {
        return $this->version;
    }
}