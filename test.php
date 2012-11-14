<?php
require_once 'Appconfig.php';
/*
$cookie = Cookie::get_instance();
echo $cookie->get_cookie('<asdfasf>');
dump_it($cookie);*/
//echo '123';

try {
  Transaction::show_transactions($option = 'last20');
  
  //$t = new Transaction();
  //$t->get_from_db('asfdasfd');
  //$t = new Transaction($transaction_id = 'asfdasfd', $money_amount = 123, $deposit = false);

  //dump_it($t);
  //echo 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  //dump_it($_SERVER);
  /*
  $m = MyBitcoinClient::get_instance();
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