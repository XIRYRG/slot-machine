<?php
require_once 'Appconfig.php';

//function strToHex($string)
//{
//    $hex='';
//    for ($i=0; $i < strlen($string); $i++)
//    {
//        $hex .= dechex(ord($string[$i]));
//    }
//    return $hex;
//}

$db = DBconfig::get_instance();

try {
//  $str[0] = 520 + 818675;
//  $str[1] = 520 + 398568;
//  $str[2] = 520 + 654792;
//  $str[0] = sha1($str[0]);
//  $str[1] = sha1($str[1]);
//  $str[2] = sha1($str[2]);
//  dump_it($str);

//  $user = User::get_instance();
//  //$t = new Transaction('1111111', 500100, true, $user->uid);
//  
//  $bitcoin_client_instance = MyBitcoinClient::get_instance();
//  if ($bitcoin_client_instance->can_connect()) {
//    echo 'Connect: can_connect <br />';
//    echo 'Full balance: '.$bitcoin_client_instance->getbalance();
//    echo '<br/>';
//    //echo $bitcoin_client_instance->getinfo();
//  }
  
  
  
//  $randomizer = Randomizer::get_instance();
//  $seed = sha1('lCCMX5Fw3AjK89fYJ7PFFnpb3TDbdaEa1');
//  dump_it($seed);
//  //sscanf(crc32($seed), "%u", $unsigned_seed);
//  $seed = crc32($seed);
//  dump_it($seed);
//  
  
  
  
  //$seed = hexdec($seed);
  //dump_it($seed);
//  $seed = base_convert($seed, 16, 36);
//  dump_it($seed);
//  
//  $seed = base_convert($seed, 36, 16);
//  dump_it($seed);
  
  //$seed = intval($seed, 16);
  //dump_it($seed-1 % PHP_INT_MAX);
//  $seed = dechex($seed);
//  dump_it($seed);
//  $seed = strToHex($seed);
//  dump_it($seed);
  
//  $randomizer->mt_srand($seed);//reinit
//  echo $rand_num = $randomizer->mt_rand();
  
  $slot = Slot::get_instance();
  $slot->get_new_payline_and_servers_seeds(1);
  dump_it($slot);
  dump_it($_SESSION);
//  echo $rand_num = $randomizer->mt_rand();
//  echo $rand_num = $randomizer->mt_rand();
//  dump_it(mt_getrandmax());
  dump_it(PHP_INT_MAX);
  

//  $amount = $bitcoin_client_instance->getbalance('ultraNewWallet');
//  dump_it($bitcoin_client_instance->getbalance('ultraNewWallet'));
//  dump_it($bitcoin_client_instance->getbalance('SlotBank'));
//  $bitcoin_client_instance->move('ultraNewWallet', 'SlotBank', $amount, 0,'Move from the user account to the common slot bitcoin account');
//  
//  dump_it($bitcoin_client_instance->getbalance('ultraNewWallet'));
//          
//  //dump_it($bitcoin_client_instance->query_arg_to_parameter('getrawtransaction bf4ffc37b48f99403f44c1d2d65da82c6480f88c3fbaf95b2519e023b27d309f'));
//  //dump_it($bitcoin_client_instance->query('decoderawtransaction 01000000015a3a6801276cc5c06eed99fac43f32b017dfe17a48545e75264372885af2a509010000008a473044022046e7bb771b2a8665e9c081968afb685b2af7d09e22c2e185185a0ab7f4c34de9022023e1b6984b274b5df3c18e198e5ea9483f2416889904c96ae2d5c795413a5bd101410489f6dc4e14ac9f2d59ae926c4a5f2546daee14d578ada6b3cb0d1b9c3cb59758b40e7265e5b58595073f1fcecf2a3046636a6a62202cf057af5a7315dd524619ffffffff02a0bb0d00000000001976a91494e0e7a97a51e19fd57a05e3901cfaccf595d7dd88ac30c11d00000000001976a914a2c324ca1403c123779a9d4bcce45fb6ec5c43d688ac00000000'));
//  //dump_it($bitcoin_client_instance->query('getrawtransaction bf4ffc37b48f99403f44c1d2d65da82c6480f88c3fbaf95b2519e023b27d309f'));
//
//  $raw_transaction_arr = ($bitcoin_client_instance->query('getrawtransaction', 'bf4ffc37b48f99403f44c1d2d65da82c6480f88c3fbaf95b2519e023b27d309f', '1'));
//  dump_it($raw_transaction_arr['vout'][1]['scriptPubKey']['addresses'][0]);

  //dump_it($bitcoin_client_instance->gettransaction('bf4ffc37b48f99403f44c1d2d65da82c6480f88c3fbaf95b2519e023b27d309f'));
  //dump_it($bitcoin_client_instance->gettransaction);
  /*
    $user = User::get_instance();
    //$u->auth();
    dump_it($user);
    $slot = Slot::get_instance($user);
    dump_it($slot);
    //Transaction::show_transactions($option = 'last20');

    //$t = new Transaction();
    //$t->get_from_db('asfdasfd');
    //$t = new Transaction($transaction_id = 'asfdasfd', $money_amount = 123, $deposit = false);

    //dump_it($t);
    //echo 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    //dump_it($_SERVER);

    $m = MyBitcoinClient::get_instance();
    dump_it($m->can_connect());
    dump_it($m);

    /*
    echo $m->getbalance('900b15b28c5dbdb15fb626dbde50861b14274384');
    echo '<br>';
    echo $m->getbalance('SlotBank');
    echo '<br>';
    echo $acc = $m->getaccount('1Fnfo4mbjZc6eqW15vnb2yo9EozgdmtdWa');
    echo $m->getbalance($acc);
    echo '<br>';
    $slot = Slot::get_instance();
    echo Slot::$bitcoin_address;
    echo '<br>';
    echo Slot::$bitcoin_account_name;


    //echo $slot->bitcoin
    dump_it($m->getaddressesbyaccount('SlotBank'));
   * 
   */
  //dump_it($m->listreceivedbyaccount());
  //dump_it($m->getaddressesbyaccount('myWallet'));
  //dump_it($m->getinfo());
  //echo $m->getreceivedbyaccount('900b15b28c5dbdb15fb626dbde50861b14274384');
  //echo $m->move('900b15b28c5dbdb15fb626dbde50861b14274384', 'myWallet', 0.01, 5, 'move BTC between accounts' );
  //dump_it(getdate());
  //echo date('h:i:s d.m.Y');
} catch (Exception $exc) {
  dump_it($exc->getTraceAsString());
}
?>