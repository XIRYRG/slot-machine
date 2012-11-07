<?php
require_once 'Appconfig.php';

class DBconfig {
  public static function get_instance(){
    if (is_null(self::$db)){
      self::$db = new DBconfig();
      self::$db->mysql_connect();
      return self::$db;
    }
    return self::$db;
  }
  private function __construct(){}
  private function __clone(){} 
  private function __wakeup(){} 
  protected static $db;
  public $username, $hostname, $pass, $dbname;

  function config_filling(){
    /*
    $this->dbname = 'slot_db';
    $this->username = 'slot_user';
    $this->hostname = 'localhost';
    $this->pass = 'slot_pass';
    $this->port = 3306;
     */

    $this->dbname = 'slot_db';
    $this->username = 'root';
    $this->hostname = 'localhost:3306';
    $this->pass = 'ltuz*8Ff';
    $this->port = 3306;

  }
    function mysql_connect(){
      $this->config_filling();
      $link = mysql_connect($this->hostname, $this->username, $this->pass);
      // try to connect for MySQL
      if (!$link) {
        echo "MySQL connection error";
        echo mysql_error();
        exit;
      }
      else{
        //echo 'Connected...';
      }
      mysql_select_db($this->dbname, $link);
    }
    function mysql_pconnect(){
      $this->config_filling();
      $link = mysql_pconnect($this->hostname, $this->username, $this->pass);
      // try to connect for MySQL
      if (!$link) {
        echo "MySQL connection error";
        echo mysql_error();
        exit;
      }
      else{
        //echo 'Connected...';
      }
      mysql_select_db($this->dbname);
    }
    function query($query_string){
      mysql_real_escape_string($query_string);
      $result = mysql_query($query_string);
      return $result;
    }
    function mysql_fetch_array($query_string){
      $q = $this->query($query_string);
      return mysql_fetch_array($q);
    }
}
$db = DBconfig::get_instance();
?>
