<?php
require_once 'Appconfig.php';
//todo: no connection exception
class MyBitcoinClient{
  public static function get_instance(){
    if (is_null(self::$bitcoin)){
      self::$bitcoin = new MyBitcoinClient();
      self::$bitcoin->config_filling();
      return self::$bitcoin;
    }
    return self::$bitcoin;
  }
  private function __construct(){  }
  private function __clone(){} 
  private function __wakeup(){}
  protected static $bitcoin;
  private $scheme, $username, $password;
  protected function config_filling(){
    if ($_SERVER['HTTP_HOST'] == '109.174.40.94' || $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1'){
      $this->scheme = 'https';
      $this->username = 'bitcoinrpc';
      $this->password = 'bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj';
      $this->address = "localhost";
      $this->port = 8332;
      $this->certificate_path = __DIR__ .'/mysitename.crt';
      //echo 'path to cert<br/>';
      //echo $this->certificate_path;
      $this->debug_level = 0;
      //self::$bitcoin = new BitcoinClient($this->scheme, $this->username, $this->password, $this->address, $this->port, $this->certificate_path, $this->debug_level);
    }
    else{
      $this->scheme = 'https';
      $this->username = 'bitcoinrpc';
      $this->password = 'bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj';
      $this->address = "99.192.139.66";
      //$this->address = "cs1205.mojohost.com";
      $this->port = 8332;
      //$this->certificate_path = "cs1205.crt";
      //$this->certificate_path = "server.cert";
      $this->certificate_path = __DIR__ .'/mysitename.crt';
      //echo $this->certificate_path;
      $this->debug_level = 0;
    }
    self::$bitcoin = new BitcoinClient($this->scheme, $this->username, $this->password, $this->address, $this->port, $this->certificate_path, $this->debug_level);
  }
}

?>
