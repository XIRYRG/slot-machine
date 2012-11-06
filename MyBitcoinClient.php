<?php
require_once 'Dumpit.php';
require_once 'Appconfig.php';
/*require_once 'jsonRPCClient.php';
$bitcoin = new jsonRPCClient('http://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@127.0.0.1:8332/');

  echo "<pre>\n";
  print_r($bitcoin->getinfo());
  echo $account = $bitcoin->getaccount('1D1xjPzX8wQAJpkn9FYL5hRfBv3GBWZ3HF');
  //echo $list = $bitcoin->getaddressesbyaccoun($account);
  echo "</pre>";
 * 
 */

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
}

/*
$bitcoin = new BitcoinClient($scheme, $username, $password);
if ($bitcoin->can_connect()){
  echo $bitcoin->getaccountaddress('absolutely_new_account');
}

unset($bitcoin);
 * 
 */
//echo $acc = $bitcoin->getbalance('myWallet');

$mybit = MyBitcoinClient::get_instance();
dump_it($mybit);
?>
