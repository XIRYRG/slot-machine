<?php
require_once 'Appconfig.php';
/*
$cookie = Cookie::get_instance();
echo $cookie->get_cookie('<asdfasf>');
dump_it($cookie);*/
//echo '123';

try {
  //$m = MyBitcoinClient::get_instance();
  //echo $m->getbalance('900b15b28c5dbdb15fb626dbde50861b14274384');
  //dump_it($m->getaddressesbyaccount('myWallet'));
  //dump_it($m->getinfo());
  //echo $m->getreceivedbyaccount('900b15b28c5dbdb15fb626dbde50861b14274384');
  //echo $m->move('900b15b28c5dbdb15fb626dbde50861b14274384', 'myWallet', 0.01, 5, 'move BTC between accounts' );
  //dump_it(getdate());
  //echo date('h:i:s d.m.Y');
} catch (Exception $exc) {
  dump_it($exc->getTraceAsString());
}
/*
$u = new User();
if ($u->get_from_db('900b15b28c5dbdb15fb626dbde50861b14274384')){
  echo 'good';
}
else{
  echo 'bad';
}
dump_it($u);

*/

//show tables
show_generated_total_weight_table();
possible_combinations();
?>