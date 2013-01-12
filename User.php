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
  
  public $uid, $phpsessid, $user_wallet, $bitcoin_receive_address, $money_balance, $affiliateusername, $remote_user_address;//, $bitcoin_receive_address, $money_balance;
  
  public function auth(){
    $file = '';
    $line = '';
    //check for whether headers was already sent
    if (headers_sent($file, $line)){
      echo 'file: '.$file;
      echo 'line: '.$line;
    }
    //SetCookie("uid",  '900b15b28c5dbdb15fb626dbde50861b14274384', AppConfig::now_plus_one_year(), '/');
    //$_COOKIE['uid'] = '900b15b28c5dbdb15fb626dbde50861b14274384';
    //todo: no DB connection exception!
    //user registered already
    if (!empty($_COOKIE['uid'])){// || !empty($_COOKIE['bitcoin_receive_address'])){
      $uid = htmlentities($_COOKIE['uid']);
      $this->uid = $uid;
      //$bitcoin_receive_address = htmlentities($_COOKIE['bitcoin_receive_address']);
      //search for user by given uid
      if ($this->get_from_db($uid)){
        $this->phpsessid = session_id();
      }
      //what if uid hadn't found in db?
      else{
        try{
          throw new Exception('User hadn\'t found in database. Please clear cookies in your browser');
        }
        catch (Exception $e){
          //$this->reg();
          //stack trace show FULL INFO about bitcoin, with login and passwords
          dump_it($e->getTraceAsString());
        }
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
      if (!$this->reg()){
        dump_it($e->getTraceAsString());
        //throw new Exception('Can\'t register new user');
      }
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
    unset($_COOKIE['bitcoin_receive_address']);
    session_destroy();
    echo 'You are logged out';
  }
  public function reg(){
    if (!empty($_SERVER['REMOTE_ADDR'])){
      $remote_user_address = $_SERVER['REMOTE_ADDR'];
    }
    $this->phpsessid = session_id();
    //if this uid has had in DB already it generates new uid
    $this->uid = sha1(uniqid(""));
    while($this->get_from_db($this->uid)){
      $this->uid = sha1(uniqid(""));
    }
    $this->money_balance = 0;//10250;
    
    
    
    
    
    //uncomment
    //create the new wallet for new user
    
    $bitcoin_client_instance = MyBitcoinClient::get_instance();
    //no connection with bitcoin server
    if ($bitcoin_client_instance->can_connect() === true){
      //throw new BitcoinClientException("No connection with bitcoin server");

      try{//to get bitcoin_receive_address
        $this->bitcoin_receive_address = $bitcoin_client_instance->getaccountaddress($this->uid);
      }
      catch (BitcoinClientException $e) {
        //todo: write exceptions to error/exceptions log file
        dump_it($e->getTraceAsString());
        //can't set cookie because of this error echo
      }
    }

        
    
    
    $this->user_wallet = 'No_yet';
    $this->affiliateusername = 'Nobody';
    $this->remote_user_address = $remote_user_address;
    //e.g.:                  04:34:19 11.11.2012
    $this->created_at = date('h:i:s d.m.Y');
    if ($this->save_in_db()){
      $file = '';
      $line = '';
      //check for whether headers was already sent
      if (headers_sent($file, $line)){
        echo 'file: '.$file;
        echo 'line: '.$line;
      }
      SetCookie("uid", $this->uid, AppConfig::now_plus_x_years(), '/', /*'.bitbandit.eu'*/AppConfig::$domainname, true, false);
      //todo: set it when user have sent money to slot
      SetCookie("user_wallet",  $this->user_wallet, AppConfig::now_plus_x_years(), '/', /*'.bitbandit.eu'*/AppConfig::$domainname, true, false);
      return true;
    }
    else {
      //throw new Exception('Failed to add to the database.');
      echo 'Failed to add to the database.';
//      catch (Exception $e) {
//        dump_it($e->getTraceAsString());
//      }
      return false;
    }
  }
  //get user record from db
  function get_from_db($uid){
    try{
      $db = DBconfig::get_instance();
      $user = $db->mysqli_fetch_array('SELECT * FROM users WHERE uid = \''.$uid.'\'');
      //$user = $db->mysqli_fetch_array('SELECT * FROM users WHERE uid = '.$uid);
    }
    catch (Exception $e){
      dump_it($e->getTraceAsString());
    }
    
    //if there is no user with given uid
    if ($user == FALSE){
      //echo 'user == false';
      return FALSE;
    }
    //the user is found
    else{
      $this->uid = $uid;
      $this->bitcoin_receive_address = $user['bitcoin_receive_address'];
      $this->money_balance = $user['money_balance'];
      $this->user_wallet = $user['user_wallet'];
      $this->remote_user_address = $user['remote_user_address'];
      $this->affiliateusername = $user['affiliateusername'];
      return TRUE;
    }
  }
  function is_user_exist($uid){
    $db = DBconfig::get_instance();
    $user = $db->mysqli_fetch_array('SELECT * FROM users WHERE uid = \''.$uid.'\'');
    //echo 'SELECT * FROM users WHERE uid = \''.$uid.'\'';
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
      $res = $db->query( "INSERT INTO users (uid, bitcoin_receive_address, user_wallet, money_balance, affiliateusername, created_at, remote_user_address) 
        VALUES ('$this->uid', '$this->bitcoin_receive_address', '$this->user_wallet', '$this->money_balance', '$this->affiliateusername', NOW(), '$this->remote_user_address')");//NOW() == '$this->created_at',
//      echo "INSERT INTO users (uid, bitcoin_receive_address, user_wallet, money_balance, affiliateusername, created_at, remote_user_address) 
//        VALUES ('$this->uid', '$this->bitcoin_receive_address', '$this->user_wallet', '$this->money_balance', '$this->affiliateusername', NOW(), '$this->remote_user_address')";
    }
    //if user exists already just update record
    else {
      //`updated_at` = '$this->updated_at',
      $res = $db->query( "UPDATE users SET 
        `bitcoin_receive_address` = '$this->bitcoin_receive_address',
        `user_wallet` = '$this->user_wallet',
        `money_balance` = '$this->money_balance',
        `affiliateusername` = '$this->affiliateusername',
        `remote_user_address` = '$this->remote_user_address'
        WHERE `uid` = '$this->uid'
      ");
    }
//    echo "UPDATE users SET 
//        `bitcoin_receive_address` = '$this->bitcoin_receive_address',
//        `user_wallet` = '$this->user_wallet',
//        `money_balance` = '$this->money_balance',
//        `affiliateusername` = '$this->affiliateusername',
//        `remote_user_address` = '$this->remote_user_address'
//        WHERE `uid` = '$this->uid'
//      ";
    if (!$res){
      //echo 'no res';
      return FALSE;
    }
    //update user after saving
    $this->get_from_db($this->uid);
    return true;
  }
  public function get_user_wallet_by_uid(/*$uid = null*/){
    //by default (if uid not given) return user_wallet_address for current user 
//    if ($uid === null){
//      $uid = $this->uid;
//    }
    $b = MyBitcoinClient::get_instance();
    //todo: get transaction where category == receive!!!
    //$transactions_list = $b->query("listtransactions", $uid, "1", "0");
    //listtransactions, uid = $uid, count = 50, start_from = 0
    $transactions_list = $b->query("listtransactions", $this->uid, "50", "0");
    // 0, null, false
    if (!$transactions_list){
      echo 'Error. Bitcoin hasn\'t transactions for given user id';
      return false;
    }
    $transactions_list_size = count($transactions_list);
    //exactly 1 transcation - where category should be 'receive'
    if ($transactions_list_size == 1 && isset($transactions_list[0]['receive'])){
      $txid = $transactions_list[0]["txid"];
    }
    //if count of transactions in $transactions_list are great than 1
    //start to search transaction where 'category' == 'receive'
    if ($transactions_list_size > 1){
      for($i = 0; $i < $transactions_list_size; $i++){
        //if transaction has 'category' and 'txid'
        if (!empty($transactions_list[$i]['category']) && !empty($transactions_list[$i]['txid'])){
          $txid = $transactions_list[$i]['txid'];
          //echo $txid;
        }
        foreach ($transactions_list[$i] as $key => $value) {
          if ($key === 'receive'){
            $txid = $transactions_list[0]["txid"];
          }
        }
      }
      
    }
    //if there are no transactions with 'receive' category
    if (!isset($txid)){
      return false;
    }
    //$txid = '4e56ce612c560e8eef187415078aec1e26b89ead41399aa8dd3edce7963d8ea7';
    //dump_it($b->getinfo());
    //echo $txid;
    $raw_transaction_arr = $b->query("getrawtransaction", $txid, "1");
    
    if (empty($raw_transaction_arr['vout'])){
      echo 'User\'s bitcoin wallet address not found.';
      return false;
    }
    //user's address placed in 'vout' 
    for($i = 0; $i < count($raw_transaction_arr['vout']); $i++){
      //search 'addresses' array in 'scriptPubKey' array
      if (!empty($raw_transaction_arr['vout'][$i]['scriptPubKey']) && !empty($raw_transaction_arr['vout'][0]['scriptPubKey']['addresses'])){
        $addresses_count = count($raw_transaction_arr['vout'][0]['scriptPubKey']['addresses']);
        //take the last address
        $user_wallet_address = $raw_transaction_arr['vout'][0]['scriptPubKey']['addresses'][$addresses_count-1];
      }
    }
    
    //$user_wallet_address = $raw_transaction_arr['vout'][0]['scriptPubKey']['addresses'][0];
    //dump_it($raw_transaction_arr);
    $address_and_txid = array('user_wallet_address' => $user_wallet_address, 'txid' => $txid);
    //return $user_wallet_address;
    return $address_and_txid;
  }
  
  //one function for cash_in and cash_out
  public function cash_move($to_where = 'in'){
    if ($to_where !== 'in' && $to_where !== 'out'){
      //little stupid
      return false;
    }
    $m = MyBitcoinClient::get_instance();
    $user = $this;
    //todo: todo
//    if (account_not_exist($user->uid)){
//      echo 0;
//      return false;
//    }
    
    //user_bitcoin_money_balance was transfered to SlotBank account, so it's null.
    //$user_bitcoin_money_balance = $user->money_balance;
    //$user_bitcoin_money_balance = $m->getbalance($user->uid, 0);
    if ($m->can_connect() !== true){
      return false;
    }
    
    //payments processing 
    //
    //all money user has
    //$user->money_balance = $user_bitcoin_money_balance;
    if ($to_where == 'in'){
      //for 'In' payments we use bitcoind(!) user balance
      $user_bitcoin_money_balance = $m->getbalance($user->uid, 0);
      //no in/outcoming payments was made
      if ($user_bitcoin_money_balance <= 0){
        return false;
      }
      //move money was sent by user to common slot account
      $m->move($user->uid, Slot::$bitcoin_account_name/*'SlotBank'*/, $user_bitcoin_money_balance, 0,'Move from the user account '.$user->uid.' to the common slot bitcoin account '.Slot::$bitcoin_account_name.' Money: '.$user->money_balance);
      //ADD (+=) new payment to user balance
      $user->money_balance += $user_bitcoin_money_balance;
      //find out user's bitcoin wallet address
      $user_walllet_address_and_txid = $user->get_user_wallet_by_uid();
      $wallet_from_which_user_sent_money = $user_walllet_address_and_txid['user_wallet_address'];
      //find out the input transaction txid
      $txid = $user_walllet_address_and_txid['txid'];
      //( txid, all money, deposit = true, uid )
      $t = new Transaction($txid, $user_bitcoin_money_balance, true, $user->uid);
      //keep amount of money was moved for returning
      $amont_of_money_was_moved = $user_bitcoin_money_balance;
      //should be not 0 if money was received (there is transaction which has 'received' category)
      if ($wallet_from_which_user_sent_money !== 0){
        $user->user_wallet = $wallet_from_which_user_sent_money;
        
        //$user->save_in_db();
      }
      else{
        //money was not really received 
        return false;
      }
    }
    if ($to_where == 'out'){
      //nothing to out
      if ($user->money_balance <= 0){
        return false;
      }
      //first move bitcoins from common account to user account
      //$m->move(Slot::$bitcoin_account_name/*'SlotBank'*/, $user->uid, $user->money_balance, 0/*Appconfig::$min_confirmations_for_cash_out/*2*/,'Move from the common slot bitcoin account '.Slot::$bitcoin_account_name.' to the user account '.$user->uid.' Money: '.$user->money_balance);
      //second move bitcoins from user account to user wallet with 2(!) confirmations
      //$txid = $m->sendfrom($user->uid, $user->user_wallet, (float)$user->money_balance, 2/*Appconfig::$min_confirmations_for_cash_out*/, 'Cash out from user account '.$user->uid.' to user wallet addres '.$user->user_wallet, 'Thank you for playing'/*AppConfig::$message_bitcoin_show_when_user_withdrawn_money*/);
      //Send amount from the server's available balance
      $txid = $m->sendtoaddress($user->user_wallet, $user->money_balance, 'Cash out from user account '.$user->uid.' to user wallet addres '.$user->user_wallet, 'Thank you for playing'/*AppConfig::$message_bitcoin_show_when_user_withdrawn_money*/);
      //dump_it($txid);
      //get amount of fee for withdrawn transaction
      $arr = $m->gettransaction($txid);//$m->query("gettransaction", $txid);
      
      //fee is negative!
      $fee = $arr['fee'];
      //dump_it($arr);
      //$m->move(Slot::$bitcoin_account_name/*'SlotBank'*/, $user->uid, abs($fee), 0,"Pay fee for user's transaction of  money withdrawal");
      
      //for 'Out' payments we use user balance from DB
      $t = new Transaction($txid, $user->money_balance, false, $user->uid);
      //Send amount from the server's available balance
      //$m->sendtoaddress($user->user_wallet, $user->money_balance, 'Cash out from user account '.$user->uid.' to user wallet addres '.$user->user_wallet, 'Thank you for playing'/*AppConfig::$message_bitcoin_show_when_user_withdrawn_money*/);
      //$m->move($user->uid, $user->user_wallet, $user->money_balance, 0/*Appconfig::$min_confirmations_for_cash_out/*2*/,'Move from the user bitcoin account '.$user->uid.' to the user wallet '.$user->user_wallet.' Money: '.$user->money_balance);
      
      //keep amount of money was moved for returning
      $amont_of_money_was_moved = $user->money_balance;
      //make user money null
      $user->money_balance = 0;
    }
    //and SAVE user wallet in DB!
    $user->save_in_db();
    //$json = $user->money_balance;
    //money was outputed
    return $amont_of_money_was_moved;
  }
  //aliases
  public function cash_out(){
    return $this->cash_move($where = 'out');
  }
  public function cash_in(){
    return $this->cash_move($where = 'in');
  }
}
?>