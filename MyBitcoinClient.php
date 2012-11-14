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
    $this->scheme = 'http';
    $this->username = 'bitcoinrpc';
    $this->password = 'bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj';
    $this->address = "localhost";
    $this->port = 8332;
    $this->certificate_path = "";
    $this->debug_level = 0;
    self::$bitcoin = new BitcoinClient($this->scheme, $this->username, $this->password, $this->address, $this->port, $this->certificate_path, $this->debug_level);
  }
  public function query(){
    
  }
}

?>
