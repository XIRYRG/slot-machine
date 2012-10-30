<?php
require_once 'appconfig.php';
require_once 'dumpit.php';
require_once 'dbconfig.php';
require_once 'Instawallet.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author vadim24816
 */
?>
     
<html>
  <head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://blockchain.info//Resources/wallet/pay-now-button.js"></script>
  </head>
  <body>
    <div style="font-size:16px;margin:10px;width:300px" class="blockchain-btn"
       data-address="1A8JiWcwvpY7tAopUkSnGuEYHmzGYfZPiq"
       data-anonymous="false"
       data-callback="https://mydomain.com/callback_url">
      <div class="blockchain stage-begin">
          <img src="http://static.blockchain.info//Resources/buttons/pay_now_64.png">
      </div>
      <div class="blockchain stage-loading" style="text-align:center">
          <img src="http://static.blockchain.info//Resources/loading-large.gif">
      </div>
      <div class="blockchain stage-ready">
          Please send payment to bitcoin address <b>[[address]]</b>
      </div>
      <div class="blockchain stage-paid">
          Payment Received [[value]] BTC. Thank You.
      </div>
      <div class="blockchain stage-error">
          <font color="red">[[error]]</font>
      </div>
    </div>
  </body>
</html>

<?php
class User {
  public $uid, $phpsessid, $wallet;//, $bitcoin_recieve_address, $money_balance;
  function __construct() {
    //$this->auth();
  }
  function auth(){
    //user registered already
    if (!empty($_COOKIE['uid'])){
      //echo 'User ID: uid='.$_COOKIE['uid'];
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
  function logout(){
    //clear session
    $_SESSION = array();
    dump_it(session_name());
    //clear SID in COOKIES
    unset($_COOKIE[session_name()]);
    unset($_COOKIE['uid']);
    session_destroy();
    echo 'You are logged out';
  }
  function reg(){
    $this->phpsessid = session_id();
    //$this->uid = sha1(session_id());
    $this->uid = sha1(uniqid(""));
    //todo: include a btc library and generate addr
    $this->btc_recieve_address = 'AAAAAa';
    $this->money_balance = 0;
    //$NOW_PLUS_ONE_YEAR = time()+60*60*24*366;
    //SetCookie("uid",  $this->uid, $NOW_PLUS_ONE_YEAR, '/');
    SetCookie("uid",  $this->uid, AppConfig::now_plus_one_year(), '/');
    //create the new wallet for new user
    $w1 = new Instawallet();
    $w1->new_wallet_curl();
    //$w1->payment_curl($w1->wallet_id, '1NWPkVs7q9SQuvbo6oizatR7mPtnZB18Qb', 1000000);
    dump_it($w1);
    $this->bitcoin_recieve_address = $w1->address;
    $this->wallet = $w1;
    
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
$u1->auth();

$u1_serialized = serialize($u1);
SetCookie("uid",$u1->uid, $NOW_PLUS_ONE_YEAR, '/');
dump_it(unserialize($u1_serialized));
//$u1->logout();

?>
