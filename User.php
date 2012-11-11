<?php
require_once 'Appconfig.php';


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
     
<?php
class User {
  //make it singletone
  /*
  protected static $user;
  private function __construct(){}
  private function __clone(){} 
  private function __wakeup(){} 
  public static function get_instance(){
    if (is_null(self::$user)){
      self::$user = new User();
      self::$user->auth();
      return self::$user;
    }
    return self::$user;
  }
  */
  public $uid, $phpsessid, $user_wallet, $bitcoin_recieve_address, $money_balance, $affiliateusername, $remote_user_address;//, $bitcoin_recieve_address, $money_balance;
  
  public function auth(){
    //SetCookie("uid",  '900b15b28c5dbdb15fb626dbde50861b14274384', AppConfig::now_plus_one_year(), '/');
    $_COOKIE['uid'] = '900b15b28c5dbdb15fb626dbde50861b14274384';
    //todo: no DB connection exception!
    //user registered already
    if (!empty($_COOKIE['uid'])){// || !empty($_COOKIE['bitcoin_recieve_address'])){
      $uid = htmlentities($_COOKIE['uid']);
      $this->uid = $uid;
      //$bitcoin_recieve_address = htmlentities($_COOKIE['bitcoin_recieve_address']);
      //search for user by given uid
      if ($this->get_from_db($uid)){
        $this->phpsessid = session_id();
      }
    }
    //user visits first time
    else {
      /*
      try{
        $this->reg();
      }
      catch (BitcoinClientException $e) {
        echo $e->getMessage();
      }
       * 
       */
      if (!$this->reg())
        throw new Exception('Can\'t register new user');
    }
    return $this;
  }
  public function logout(){
    //clear session and cookie
    $_SESSION = array();
    dump_it(session_name());
    //clear SID in COOKIES
    unset($_COOKIE[session_name()]);
    unset($_COOKIE['uid']);
    unset($_COOKIE['bitcoin_recieve_address']);
    session_destroy();
    echo 'You are logged out';
  }
  public function reg(){
    if (!empty($_SERVER['REMOTE_ADDR'])){
      $remote_user_address = $_SERVER['REMOTE_ADDR'];
    }
    
    $this->phpsessid = session_id();
    //$this->uid = sha1(session_id());
    
    //if this uid has had in DB already it generates new uid
    $this->uid = sha1(uniqid(""));
    //echo $this->uid;
    while($this->get_from_db($this->uid)){
      $this->uid = sha1(uniqid(""));
    }
    //echo '<br>';
    //echo $this->uid;
    $this->money_balance = 50;
    
    //create the new wallet for new user
    $bitcoin_client_instance = MyBitcoinClient::get_instance();
    //no connection with bitcoin server
    if (!$bitcoin_client_instance->can_connect()){
      throw new BitcoinClientException("No connection with bitcoin server");
    }
    try{//to get bitcoin_recieve_address
      $this->bitcoin_recieve_address = $bitcoin_client_instance->getaccountaddress($this->uid);
    }
    catch (BitcoinClientException $e) {
      echo $e->getMessage();
    }
    $this->user_wallet = 'No_yet';
    $this->affiliateusername = 'Nobody';
    $this->remote_user_address = $remote_user_address;
    //e.g.:                  04:34:19 11.11.2012
    $this->created_at = date('h:i:s d.m.Y');
    if ($this->save_in_db()){
      SetCookie("uid",  $this->uid, AppConfig::now_plus_one_year(), '/');
      //todo: set it when user have sent money to slot
      SetCookie("user_wallet",  $this->user_wallet, AppConfig::now_plus_one_year(), '/');
      return true;
    }
    else {
      return false;
    }
  }
  //get user record from db
  function get_from_db($uid){
    $uid = mysql_real_escape_string($uid);
    $db = DBconfig::get_instance();
    $user = $db->mysql_fetch_array('SELECT * FROM users WHERE uid = \''.$uid.'\'');
    //if there is no user with given uid
    if ($user == FALSE){
      //echo 'user == false';
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
  function is_user_exist($uid){
    $uid = mysql_real_escape_string($uid);
    $db = DBconfig::get_instance();
    $user = $db->mysql_fetch_array('SELECT * FROM users WHERE uid = \''.$uid.'\'');
    //if there is no user with given uid
    if ($user == FALSE){
      //echo 'user == false';
      return FALSE;
    }
    //the user is found
    else{
      return TRUE;
    }
  }
  //for updating money balance
  function update_from_db(){
    $this->get_from_db($this->uid);
  }
  function save_in_db(){
    $db = DBconfig::get_instance();
    //if user not exist insert in DB
    if (!$this->is_user_exist($this->uid)){
      $res = $db->query("INSERT INTO users (uid, bitcoin_recieve_address, user_wallet, money_balance, affiliateusername, created_at, remote_user_address) 
        VALUES ('$this->uid', '$this->bitcoin_recieve_address', '$this->user_wallet', '$this->money_balance', '$this->affiliateusername', NOW(), '$this->remote_user_address')");//NOW() == '$this->created_at',
      
    }
    //if user exists already just update record
    else {
      $q = "UPDATE users SET 
        `bitcoin_recieve_address` = '$this->bitcoin_recieve_address',
        `user_wallet` = '$this->user_wallet',
        `money_balance` = '$this->money_balance',
        `affiliateusername` = '$this->affiliateusername',
        `created_at` = '$this->created_at',
        `remote_user_address` = '$this->remote_user_address'
        WHERE `uid` = '$this->uid'
      ";
      $res = $db->query($q);
      return true;
    }
    
    if (!$res){
      return FALSE;
    }
    //update user after saving
    $this->get_from_db($this->uid);
    return true;
  }
}

//$u1_serialized = serialize($u1);
//SetCookie("uid",$u1->uid, $NOW_PLUS_ONE_YEAR, '/');
//dump_it(unserialize($u1_serialized));
//$u1->logout();

?>
