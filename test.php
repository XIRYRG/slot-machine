<?php
require_once 'Dumpit.php';
require_once 'Cookie.php';
$cookie = Cookie::get_instance();
echo $cookie->get_cookie('<asdfasf>');
dump_it($cookie);
?>
