<?php
require_once 'appconfig.php';
require_once 'dumpit.php';
class dbconfig {
  public static function get_instance(){
    if (is_null(self::$db)){
      self::$db = new dbconfig();
      self::$db->mysql_pconnect();
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
    $this->hostname = 'localhost:3306';
    $this->pass = 'slot_pass';
    $this->port = 3306;
     */

    $this->dbname = 'slot_db';
    $this->username = 'root';
    $this->hostname = 'localhost:3306';
    $this->pass = '';
    $this->port = 3306;

  }
    function mysql_connect(){
      $link = mysql_connect($this->hostname, $this->username, $this->pass);
      // try to connect for MySQL
      if (!$link) {
        echo "MySQL connection error";
        exit;
      }
      else{
        echo 'Connected...';
      }
      mysql_select_db($this->dbname, $link);
    }
    function mysql_pconnect(){
      $this->config_filling();
      echo 'mysql_pconnect';
      $link = mysql_pconnect($this->hostname, $this->username, $this->pass);
      // try to connect for MySQL
      if (!$link) {
        echo "MySQL connection error";
        echo mysql_error();
        exit;
      }
      else{
        echo 'Connected...';
      }
      mysql_select_db($this->dbname);
      $result = mysql_query("SELECT * FROM users", $link);
      while($row = mysql_fetch_array($result)){
        dump_it($row);
      }
    }
    function query($query_string){
      mysql_real_escape_string($query_string);
      $result = mysql_query($query_string);
      return $result;
    }
    function mysql_fetch_array($query_string){
      $q = $this->query($query_string);
      dump_it($q);
      while($row = mysql_fetch_array($q)){
        dump_it($row);
      }
    }
}
$db = dbconfig::get_instance();
$db->mysql_pconnect();
$db->mysql_fetch_array("SELECT * FROM users");

?>
