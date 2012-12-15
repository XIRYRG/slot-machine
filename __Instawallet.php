<?php
class Instawallet {
  public $wallet_id, $address, $balance;
  protected $path_new_wallet = 'https://www.instawallet.org/api/v1/new_wallet';
  

  //new_wallet() - Request a new wallet
	//Parameters:	none
	//Returns:		wallet_id
  /*
	function new_wallet() {
		$url = 'https://www.instawallet.org/api/v1/new_wallet';
		$json = file_get_contents($url);
		$data = json_decode($json);
    echo 'json';
    echo $json;
		if(!$data->successful) {
			return false;
		} else {
			return $data->wallet_id;
		}
	}
  */
 function __construct() {
   $this->wallet_id = $this->new_wallet_curl();
   $this->address = $this->address_curl($this->wallet_id);
   $this->balance = $this->balance_curl($this->wallet_id);
 }
  function init_curl($ch, $post, $post_fields){
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // Отключить ошибку "SSL certificate problem, verify that the CA cert is OK"
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //for POST method using
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_POST, $post);
    // Отключить ошибку "SSL: certificate subject name 'hostname.ru' does not match target host name '123.123.123.123'"
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  }
  //the same function but with cURL
  function new_wallet_curl() {
    $ch = curl_init($this->path_new_wallet);
    //curl_setopt($ch, CURLOPT_POST, 1);
    $this->init_curl($ch,1,1);
    $json = curl_exec($ch);
		$data = json_decode($json);
    curl_close($ch);
		if(!$data->successful) {
      echo $data->message;
			return false;
		} else {
      //$this->wallet_id = $json_obj->wallet_id;
			return $data->wallet_id;
		}
    
    
	}

	//address() - Request the address of an existing wallet
	//Parameters:	wallet_id
	//Returns:		address
  /*
	function address($wallet_id = null) {
		if(empty($wallet_id)) {
			return false;
		} else {
			$url = 'https://www.instawallet.org/api/v1/w/' . $wallet_id . '/address';
			$json = file_get_contents($url);
			$data = json_decode($json);
			if(!$data->successful) {
				return false;
			} else {
				return $data->address;
			}
		}
	}
   * 
   */
  function address_curl($wallet_id = null) {
		if(empty($wallet_id)) {
			return false;
		} else {
			$url = 'https://www.instawallet.org/api/v1/w/' . $wallet_id . '/address';
      $ch = curl_init($url);
      $this->init_curl($ch,0,0);
			$json = curl_exec($ch);
      $data = json_decode($json);
      curl_close($ch);
			if(!$data->successful) {
        echo $data->message;
				return false;
			} else {
				return $data->address;
			}
		}
	}

	//balance() - Request the current balance of an existing wallet
	//Parameters:	wallet_id
	//Returns:		balance
  /*
	function balance($wallet_id = null) {
		if(empty($wallet_id)) {
			return false;
		} else {
			$url = 'https://www.instawallet.org/api/v1/w/' . $wallet_id . '/balance';
			$json = file_get_contents($url);
			$data = json_decode($json);
			if(!$data->successful) {
				return false;
			} else {
				return $data->balance;
			}
		}
	}
   * 
   */

  function balance_curl($wallet_id = null) {
		if(empty($wallet_id)) {
			return false;
		} 
    $url = 'https://www.instawallet.org/api/v1/w/' . $wallet_id . '/balance';
    $ch = curl_init($url);
    $this->init_curl($ch,0,0);
    $json = curl_exec($ch);
    $data = json_decode($json);
    if(!$data->successful) {
      echo $data->message;
      return false;
    } else {
      return $data->balance;
    }
	}
  
	//payment() - Request a payment be initiated from an existing wallet
  /*
	function payment() {
		$url = 'https://www.instawallet.org/api/v1/w/' . $wallet_id . '/payment';
		$json = file_get_contents($url);
		return json_decode($json);
	}
   * 
   */
  
  function payment_curl($wallet_id = null, $reciever_addres, $amount) {
    if(empty($wallet_id) || empty($reciever_addres) || empty($amount)) {
			return false;
    }
		$url = 'https://www.instawallet.org/api/v1/w/' . $wallet_id . '/payment';
    $ch = curl_init($url);
    $this->init_curl($ch,1,'address='.$reciever_addres.';amount='.$amount);
		$json = curl_exec($ch);
    $data = json_decode($json);
    if(!$data->successful) {
      echo $data->message;
      return false;
    }
    echo $data->message;
		return json_decode($json);
	}
  function get_sender_address(){
    //http://www.bitcoinforum.com/security-technical-support-and-tutorials/how-to-find-out-the-sender-address/
    //http://blockexplorer.com/q/mytransactions/1Cvvr8AsCfbbVQ2xoWiFD1Gb2VRbGsEf28
    //https://bitcointalk.org/index.php?topic=112741.0
  }
}
?>