<?php
//relocate browser to https
//require_once 'relocateToSecureScheme.php';

require_once 'Dumpit.php';
require_once 'DBconfig.php';
////require_once 'Instawallet.php';
//require_once 'MyBitcoinClient.php';
//require_once 'Randomizer.php';
//require_once 'Symbol.php';
//require_once 'Reel.php';
//require_once 'Payline.php';
//require_once 'Paytable.php';
//require_once 'Cookie.php';
require_once 'User.php';
//require_once 'bitcoin/bitcoin.inc';
require_once 'Slot.php';
//require_once 'Transaction.php';
//require_once 'functions.php';
//Cookies should be enabled

$db = DBconfig::get_instance();


//$db->close();
//$db = DBconfig::get_instance();
//$db->close();
//$db = DBconfig::get_instance();

//$db->close();
//$uid = '69d37bef6ba2bb1670d20b49e5e62752391aa6b6';
//$user = $db->mysqli_fetch_array("SELECT * FROM users WHERE uid = '$uid'");
//dump_it($user);

$user = User::get_instance();
echo $user->uid;
$slot = Slot::get_instance($user);

$m = MyBitcoinClient::get_instance();

$m->move(Slot::$bitcoin_account_name/*'SlotBank'*/, $user->uid, 0.02, 0,'Move from the user account '.$user->uid.' to the common slot bitcoin account'.Slot::$bitcoin_account_name.' Money: '.$user->money_balance);
//no incoming payments was made
if ($m->can_connect() !== true){
  return false;
}
$bitcoin_money_balance = $m->getbalance($user->uid, 0);
if ($bitcoin_money_balance <= 0){
  echo 0;
  return false;
}
//incoming payments processing 
echo $m->getbalance($user->uid, 0);
//all money user have sent
$user->money_balance = $bitcoin_money_balance;
//( txid, all money, deposit = true, uid )
$t = new Transaction('', $user->money_balance, true, $user->uid);
//echo Slot::$bitcoin_account_name;
//move money was sent by user to common slot account
try{
  //$m->move($user->uid, Slot::$bitcoin_account_name/*'SlotBank'*/, $user->money_balance, 0,'Move from the user account '.$user->uid.' to the common slot bitcoin account'.Slot::$bitcoin_account_name.' Money: '.$user->money_balance);
  $user->user_wallet = $user->get_user_wallet_by_uid();
  $user->save_in_db();
}
catch (Exception $e){
  //$this->reg();
  //stack trace show FULL INFO about bitcoin, with login and passwords
  dump_it($e->getTraceAsString());
}
$json = $user->money_balance;
echo $json;
?>
