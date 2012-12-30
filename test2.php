<?php
require_once 'MyBitcoinClient.php';
require_once 'Dumpit.php';
require_once 'User.php';

//echo AppConfig::$domainname;

//SetCookie("uid",  '900b15b28c5dbdb15fb626dbde50861b14274384', AppConfig::now_plus_one_year(), '/', '.bitbandit.eu');
//SetCookie("uid",  $user->uid, AppConfig::now_plus_one_year(), '/', '.bitbandit.eu', true, false);
$user = User::get_instance();

$b = MyBitcoinClient::get_instance();
echo '<br />All money: '.$b->getbalance();
echo '<br />Money in SlotBank: '.$b->getbalance('SlotBank');
echo '<br />Current User money: '.$b->getbalance($user->uid);
//$amount = $b->getbalance($user->uid);
$amount = $b->getbalance();
//to SlotBank
//$b->move($user->uid, 'SlotBank', $amount, 0,'Move from the user account to the common slot bitcoin account');
//to user
//$b->move('SlotBank', $user->uid, $amount, 0,'Move from the user account to the common slot bitcoin account');
echo '<br />';
echo '<br />All money: '.$b->getbalance();
echo '<br />Money in SlotBank: '.$b->getbalance('SlotBank');
echo '<br />Current User money: '.$b->getbalance($user->uid);
echo '<br /><br />';
echo 'SlotBank:';
dump_it($b->getaddressesbyaccount('SlotBank'));
echo 'uid '.$user->uid;
dump_it($b->getaddressesbyaccount($user->uid));

echo 'Account SlotBank has address '.$b->getaccountaddress('SlotBank');

//$transactions_list = $b->query("listtransactions", "SlotBank", "1", "0");
$transactions_list = $b->query("listtransactions", $user->uid, "10", "0");
dump_it($transactions_list[0]);

$txid = $transactions_list[0]["txid"];
dump_it($b->getinfo());
echo $txid;
$raw_transaction_arr = $b->query("getrawtransaction", $txid, "1");
dump_it($raw_transaction_arr);
$user->user_wallet = $raw_transaction_arr['vout'][1]['scriptPubKey']['addresses'][0];

echo $user->user_wallet;


?>
