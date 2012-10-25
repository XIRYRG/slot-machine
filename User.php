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
    $this->auth();
  }
  function auth(){
    //user registered already
    if (!empty($_COOKIE['uid'])){
      echo 'User ID: uid=';
      $uid = $_COOKIE['uid'];
      //search for user by given uid
      if ($this->get_from_db($uid)){
        
      }
    }
    //user visits first time
    else {
      reg();
    }
    return self;
  }
  function reg(){
    $u1->phpsessid = session_id();
    $u1->uid = sha1(session_id());
    $u1->btc_recieve_address = 'asdfasfd';
    SetCookie("uid",$u1->uid, $NOW_PLUS_ONE_YEAR, '/');
  }
  function get_from_db($uid){
    $uid = mysql_real_escape_string($uid);
    $db = dbconfig::get_instance();
    dump_it($db);
    $user = $db->mysql_fetch_array("SELECT * FROM users WHERE uid = $uid");
    $this->uid = $uid;
    $this->bitcoin_recieve_address = $user['bitcoin_recieve_address'];
    $this->money_balance = $user['money_balance'];
  }
}

$u1 = new User();
$u1->btc_recieve_address = 'asdfasfd';
$u1->phpsessid = session_id();
$u1->uid = sha1(session_id());

$u1_serialized = serialize($u1);
SetCookie("uid",$u1->uid, $NOW_PLUS_ONE_YEAR, '/');
dump_it(unserialize($u1_serialized));
//echo $u1->btc_recieve_address;
echo session_id();
echo '<br>';
echo $uid = sha1(session_id());
$u1->auth();
?>
