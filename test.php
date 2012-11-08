<?php
require_once 'Appconfig.php';
/*
$cookie = Cookie::get_instance();
echo $cookie->get_cookie('<asdfasf>');
dump_it($cookie);*/
//echo '123';

try {
  $m = MyBitcoinClient::get_instance();
  //echo $m->getinfo ();
} catch (Exception $exc) {
  dump_it($exc->getTraceAsString());
}

$u = new User();
if ($u->get_from_db('900b15b28c5dbdb15fb626dbde50861b14274384')){
  echo 'good';
}
else{
  echo 'bad';
}
dump_it($u);


?>