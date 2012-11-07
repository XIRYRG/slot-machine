<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of appconfig
 *
 * @author vadim24816
 */

//must be first!
ini_set('session.gc_maxlifetime', AppConfig::now_plus_one_year());
ini_set('session.cookie_lifetime', AppConfig::now_plus_one_year());
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'/slot-machine1/sessions');
session_start();

require_once 'bitcoin/bitcoin.inc';
require_once 'Dumpit.php';
require_once 'DBconfig.php';
require_once 'Instawallet.php';
require_once 'MyBitcoinClient.php';
require_once 'Randomizer.php';
require_once 'Symbol.php';
require_once 'Reel.php';
require_once 'Payline.php';
require_once 'Paytable.php';
require_once 'Cookie.php';
require_once 'User.php';

//Cookies should be enabled
class AppConfig{
  /*
  public function __construct() {
    self::filling();
  }
   * 
   */
  //public static $NOW_PLUS_ONE_YEAR;
  public static function now_plus_one_year(){
    return time()+60*60*24*366;
  }
}

echo '<pre>';
echo ' <br />$_COOKIE: ';
var_dump($_COOKIE);
echo '<br />$_SESSION: ';
var_dump($_SESSION);
echo '</pre>';
?>
