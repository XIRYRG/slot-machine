<?php
/**
 * Description of appconfig
 *
 * @author vadim24816
 */
//return 0;
//echo 'Appconfig';
if ($_SERVER['HTTP_HOST'] == 'bitbandit.eu'){
  AppConfig::$domainname = '.bitbandit.eu';
}
elseif ($_SERVER['HTTP_HOST'] == '109.174.40.94'){
  AppConfig::$domainname = '109.174.40.94';
}
elseif ($_SERVER['HTTP_HOST'] == 'localhost'){
  AppConfig::$domainname = 'localhost';
}
elseif ($_SERVER['HTTP_HOST'] == '127.0.0.1') {
  AppConfig::$domainname = '127.0.0.1';
}

//must be first!
ini_set('session.gc_maxlifetime', AppConfig::now_plus_x_years());
ini_set('session.cookie_lifetime', AppConfig::now_plus_x_years());
//ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'/slot-machine1/sessions');
//set session via https
session_set_cookie_params( 
    AppConfig::now_plus_x_years(), 
    '/', //path
    AppConfig::$domainname,//current domain
    //'',//any
    //'.bitbandit.eu', //cant work on local
    true, //secure
    false //http only
  ); 

session_start();

//relocate browser to https
require_once 'relocateToSecureScheme.php';

require_once 'Dumpit.php';
require_once 'DBconfig.php';
//require_once 'Instawallet.php';
require_once 'MyBitcoinClient.php';
require_once 'Randomizer.php';
require_once 'Symbol.php';
require_once 'Reel.php';
require_once 'Payline.php';
require_once 'Paytable.php';
require_once 'Cookie.php';
require_once 'User.php';
require_once 'bitcoin/bitcoin.inc';
require_once 'Slot.php';
require_once 'Transaction.php';
require_once 'functions.php';
//Cookies should be enabled
class AppConfig{
  public static $domainname = 'bitbandit.eu';
  public static $min_confirmations_for_cash_out = '2';
  public static $message_bitcoin_show_when_user_withdrawn_money = 'Thank you for playing';
  public static function now_plus_x_years($x = 10){
    return time()+60*60*24*366*$x;
  }
}
?>