<?php
$db = new database();
class database {
    
    private $connect    = NULL;
    private $host       = DB_HOST;
    private $user       = DB_USER;
    private $password   = DB_PASSWORD;
    private $db         = DB_NAME;

    private $result     = NULL;
    private $sql        = NULL;

    public $error       = 0;

    public function __construct() {

        $this->MySQLconnect();
    }

    public function __destruct() {

        $this->MySQLclose();
    }

    private function MySQLconnect() {
        
        $this->connect = @mysql_connect($this->host, $this->user, $this->password) or exit("Unavailable HOST: " . mysql_error());

        if($this->connect) {

            mysql_select_db($this->db, $this->connect) or exit("Unavailable DATABASE: " . mysql_error());
        }
    }

    private function MySQLclose() {

        @mysql_close($this->connect) or exit("Cannot be closed: " . mysql_error());
        
    }

    private function MySQLresult() {

        $this->result = @mysql_query($this->sql);// or exit($this->sql . " " . mysql_error());
        if(! $this->result) $this->error = mysql_errno() . ": " . mysql_error();
    }

    public function query($sql = "") {

        $this->sql = $sql;
        $this->MySQLresult();

        if(preg_match("/insert/i", $sql)) return mysql_insert_id();
    }

    public function loadObject() {
        if((int) $this->error > 0) {

            echo $this->sql, "<br />", $this->error, "<br />";
            return;
        }

        $return = array();
        if($this->result) $return = mysql_fetch_object($this->result);
        
        return $return;
    }

    public function loadObjectList() {
        if((int) $this->error > 0) {

            echo $this->sql, "<br />", $this->error, "<br />";
            return;
        }
        
        $return = array();
        if($this->result) {

            while($row = mysql_fetch_object($this->result)) {
                $return[] = $row;
            }
        }

        return $return;
    }

    public function count() {

        if($this->result) $count = mysql_num_rows($this->result);
        else $count = 0;

        return $count;
    }
}
?>
