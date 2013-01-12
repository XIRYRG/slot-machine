<?php
require_once 'MyBitcoinClient.php';
require_once 'Dumpit.php';
require_once 'User.php';

$slot = Slot::get_instance();
$user = User::get_instance();
//dump_it($slot);

////echo AppConfig::$domainname;
//
////SetCookie("uid",  '900b15b28c5dbdb15fb626dbde50861b14274384', AppConfig::now_plus_one_year(), '/', '.bitbandit.eu');
////SetCookie("uid",  $user->uid, AppConfig::now_plus_one_year(), '/', '.bitbandit.eu', true, false);
//$user = User::get_instance();
//
$b = MyBitcoinClient::get_instance();
echo '<br />All money: '.$b->getbalance(NULL,0);

//$b->move('SlotBank', $user->uid, 0.05, 0,'Move from the user account to the common slot bitcoin account');
echo '<br />Money in SlotBank: '.$b->getbalance('SlotBank',0);
echo '<br />Current User money: '.$b->getbalance($user->uid,0);
////$amount = $b->getbalance($user->uid);
//$amount = $b->getbalance();


//$amount = 0.0195;
////to user
//$b->move('SlotBank', $user->uid, $amount, 0,'Move from the user account to the common slot bitcoin account');
///to SlotBank
//$b->move($user->uid, 'SlotBank', $amount, 0,'Move from the user account to the common slot bitcoin account');
//echo '<br />';
//echo '<br />All money: '.$b->getbalance();
//echo '<br />Money in SlotBank: '.$b->getbalance('SlotBank');
//echo '<br />Current User money: '.$b->getbalance($user->uid);
//echo '<br /><br />';
////echo 'SlotBank:';
////echo 'uid '.$user->uid;
//dump_it($b->getaddressesbyaccount($user->uid));
dump_it($user);




/*

$b = MyBitcoinClient::get_instance();
    //todo: get transaction where category == receive!!!
    //$transactions_list = $b->query("listtransactions", $uid, "1", "0");
    //listtransactions, uid = $uid, count = 50, start_from = 0
    //$transactions_list = $b->query("listtransactions", $uid, "5", "0");
    $transactions_list = $b->query("listtransactions", $user->uid, "10", "0");

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
    dump_it($raw_transaction_arr);
    dump_it($user_wallet_address);
    
    */
    
    
    
//dump_it($b->query("listaccounts"));

////
////echo 'Account SlotBank has address '.$b->getaccountaddress('SlotBank');
////
//////$transactions_list = $b->query("listtransactions", "SlotBank", "1", "0");
//$transactions_list = $b->query("listtransactions", $user->uid, "10", "0");
////dump_it($transactions_list[0]);
////
//$txid = $transactions_list[0]["txid"];
//echo $txid;
//$raw_transaction_arr = $b->query("getrawtransaction", $txid, "1");
//dump_it($raw_transaction_arr);
////$user->user_wallet = $raw_transaction_arr['vout'][1]['scriptPubKey']['addresses'][0];
//
//echo '<br>';
//$user->get_user_wallet_by_uid();

echo $user->user_wallet;

////echo $b->sendfrom('SlotBank', '1JedC8gQqfNoP5ykGtTwr567DpGiZcvQ5q', 0.01, 1);
//dump_it($b->query("listreceivedbyaddress"));

//last values
//All money: 0.058
//Money in SlotBank: 0.079
//Current User money: 0



?>