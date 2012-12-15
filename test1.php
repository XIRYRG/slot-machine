<?php
require_once 'Dumpit.php';
require_once 'jsonRPCClient.php';


//$bitcoin = new jsonRPCClient('https://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@99.192.139.66/', true);
//$bitcoin = new jsonRPCClient('https://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@localhost', true); 
//  echo "<pre>\n";
//  print_r($bitcoin->getinfo()); echo "\n";
//  //echo 
//  //echo "Received: ".$bitcoin->getreceivedbylabel("Your Address")."\n";
//  echo $bitcoin->getbalance();
//  echo "</pre>";
//  
//  




  $bitcoin_client_instance = MyBitcoinClient::get_instance();
  //$bitcoin_client_instance->setCaCertificate( __DIR__ .'/ca-bundle.crt' );
if ($bitcoin_client_instance->can_connect()) {
  echo 'Connect: can_connect <br />';
  echo 'Full balance: '.$bitcoin_client_instance->getbalance();
  echo '<br/>';
  echo $bitcoin_client_instance->getinfo();
}


/*
//$data = send_get('https://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@cs1205.mojohost.com:8332/','','bitbandit.eu');
$data = send_post('https://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@cs1205.mojohost.com:8332/','username=bitcoinrpc&password=bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj', '', 'bitbandit.eu');
dump_it($data);
*/

/*
$ch = curl_init();
//curl_setopt ($ch, CURLOPT_URL, "https://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@cs1205.mojohost.com:8332"); 
//curl_setopt ($ch, CURLOPT_URL, "https://cs1205.mojohost.com/");
//curl_setopt ($ch, CURLOPT_URL, "https://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@localhost:8332");
curl_setopt($ch, CURLOPT_URL, "https://localhost");
curl_setopt($ch, CURLOPT_HEADER,1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
curl_setopt($ch, CURLOPT_PORT, 8332);
//try to use full path
//curl_setopt($ch, CURLOPT_CAINFO, __DIR__ .'/server.cert');
//curl_setopt($ch, CURLOPT_CAINFO, __DIR__ .'/xampp_local_server.crt');
curl_setopt($ch, CURLOPT_CAINFO, __DIR__ .'/ca-bundle.crt');


curl_setopt($ch, CURLOPT_VERBOSE,1);
//curl_setopt($ch, CURLOPT_CERTINFO, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
$result = curl_exec ($ch);

  $bitcoin_client_instance = MyBitcoinClient::get_instance();
if ($bitcoin_client_instance->can_connect()) {
  echo 'Connect: can_connect <br />';
  echo 'Full balance: '.$bitcoin_client_instance->getbalance();
  echo '<br/>';
  echo $bitcoin_client_instance->getinfo();
}


$info = curl_getinfo($ch);
dump_it($info);
echo $type = curl_multi_getcontent($ch);




curl_close ($ch); 
echo $result;

//$url = 'https://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@cs1205.mojohost.com:443';
//$contextOptions = array(
//    'ssl' => array(
//        'verify_peer'   => true,
//        'allow_self_signed' => true,
//        'cafile'        => __DIR__ . '/server.cert',
//        'verify_depth'  => 5
//    //    'CN_match'      => 'secure.example.com'
//    )
//);
//$sslContext = stream_context_create($contextOptions);
//$default = stream_context_set_default($contextOptions);
//$result = file_get_contents($url, NULL, $sslContext);
//dump_it($result);
//dump_it($default);
//
//$bitcoin = new BitcoinClient('https', 'bitcoinrpc', 'bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj', '64.59.69.66',/*'cs1205.mojohost.com',/ '8332', 'server.cert', 2);
//dump_it($bitcoin->getinfo());

/*
$bitcoin_client_instance = MyBitcoinClient::get_instance();
if ($bitcoin_client_instance->can_connect()) {
  echo 'Connect: can_connect <br />';
  echo 'Full balance: '.$bitcoin_client_instance->getbalance();
  echo '<br/>';
  echo $bitcoin_client_instance->getinfo();
}

$bitcoin = new jsonRPCClient('https://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@cs1205.mojohost.com:8332/');
dump_it($bitcoin->getinfo());



//$response="";
////$uri = $scheme . "://" . $username . ":" . $password . "@" . $address . ":" . $port . "/";
//if ($fp = fsockopen ("ssl://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@cs1205.mojohost.com", 443, $errno, $errstr, 30))
//{
//  $request ="GET / HTTP/1.0\r\n";
//  $request.="Host: https://cs1205.mojohost.com\r\n";
//  $request.="Content-Type: application/x-www-form-urlencoded\r\n";
//  $request.="Content-Length: 7\r\n";
//  $request.="\r\n\r\n";
//  $request.="foo=bar";
//
//  fwrite($fp,$request,strlen($request));
//
//  while (!feof($fp))
//      $response.=fread($fp,8192);
//
//  fclose($fp);
//}
//else
//  die('Could not open socket');
//
//echo "<pre>\n";
//echo htmlentities($response);
//echo "</pre>\n";

//$context = stream_context_create();
//j
///* Sends an http request to www.example.com
//   with additional headers shown above */
//$fp = fopen('https://cs1205.mojohost.com', 'r', false, $context);
//fpassthru($fp);
//fclose($fp);




//$bitcoin = new jsonRPCClient('https://bitcoinrpc:bBvvbjBP4fSHAnLF38PeHcExtYrCgCRR6j9EL68yEPj@cs1205.mojohost.com:8333/');
//dump_it($bitcoin->getinfo());
?>
