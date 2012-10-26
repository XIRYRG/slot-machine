<?php
require_once 'appconfig.php';
require_once 'dumpit.php';
require_once 'dbconfig.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author vadim24816
 */

          
         
class User {
  public $uid, $phpsessid, $bitcoin_recieve_address, $money_balance;
  function __construct() {
    //$this->auth();
  }
  function auth(){
    echo 'auth()';
    echo '<br />';
    //user registered already
    if (!empty($_COOKIE['uid'])){
      echo 'User ID: uid='.$_COOKIE['uid'];
      $uid = $_COOKIE['uid'];
      //search for user by given uid
      if ($this->get_from_db($uid)){
        $this->phpsessid = session_id();
      }
    }
    //user visits first time
    else {
      $this->reg();
    }
    return $this;
  }
  function reg(){
    $this->phpsessid = session_id();
    //todo: use uniqid
    $this->uid = sha1(session_id());
    //todo: include a btc library and generate addr
    $this->btc_recieve_address = 'AAAAAa';
    $this->money_balance = 0;
    //$NOW_PLUS_ONE_YEAR = time()+60*60*24*366;
    //SetCookie("uid",  $this->uid, $NOW_PLUS_ONE_YEAR, '/');
    SetCookie("uid",  $this->uid, AppConfig::now_plus_one_year(), '/');
    $this->save_in_db();
  }
  //get user record from db
  function get_from_db($uid){
    $uid = mysql_real_escape_string($uid);
    $db = dbconfig::get_instance();
    $user = $db->mysql_fetch_array('SELECT * FROM users WHERE uid = \''.$uid.'\'');
    //if there is no user with given uid
    if ($user == FALSE){
      return FALSE;
    }
    //the user is found
    else{
      $this->uid = $uid;
      $this->bitcoin_recieve_address = $user['bitcoin_recieve_address'];
      $this->money_balance = $user['money_balance'];
      return TRUE;
    }
  }
  function save_in_db(){
    $user = $this;
    $db = dbconfig::get_instance();
    $res = $db->query("INSERT INTO users (uid, bitcoin_recieve_address, money_balance) 
      VALUES ('$user->uid', '$user->bitcoin_recieve_address', '$user->money_balance')");
    if (!$res){
      return FALSE;
    }
  }
}

$u1 = new User();
//$u1->btc_recieve_address = 'asdfasfd';
//$u1->phpsessid = session_id();
//$u1->uid = sha1(session_id());
$u1->auth();

$u1_serialized = serialize($u1);
SetCookie("uid",$u1->uid, $NOW_PLUS_ONE_YEAR, '/');
dump_it(unserialize($u1_serialized));
//echo $u1->btc_recieve_address;
//echo session_id();
//echo '<br>';
//echo $uid = sha1(session_id());
//$u1->auth();
?>
