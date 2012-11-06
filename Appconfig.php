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

require_once 'bitcoin/bitcoin.inc';
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
$NOW_PLUS_ONE_YEAR = time()+60*60*24*366;
ini_set('session.gc_maxlifetime', $NOW_PLUS_ONE_YEAR);
ini_set('session.cookie_lifetime', $NOW_PLUS_ONE_YEAR);
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'/slot-machine1/sessions');
session_start();

echo '<pre>';
echo ' <br />$_COOKIE: ';
var_dump($_COOKIE);
echo '<br />$_SESSION: ';
var_dump($_SESSION);
echo '</pre>';
?>
