<?php
require_once 'Appconfig.php';

try {
  $user = User::get_instance();
  //$t = new Transaction('1111111', 500100, true, $user->uid);
  
  $bitcoin_client_instance = MyBitcoinClient::get_instance();
  if ($bitcoin_client_instance->can_connect()) {
    echo 'Connect: can_connect <br />';
    //echo 'Full balance: '.$bitcoin_client_instance->getbalance();
    echo '<br/>';
    //echo $bitcoin_client_instance->getinfo();
  }
//  $amount = $bitcoin_client_instance->getbalance('ultraNewWallet');
//  dump_it($bitcoin_client_instance->getbalance('ultraNewWallet'));
//  dump_it($bitcoin_client_instance->getbalance('SlotBank'));
//  $bitcoin_client_instance->move('ultraNewWallet', 'SlotBank', $amount, 0,'Move from the user account to the common slot bitcoin account');
//  
//  dump_it($bitcoin_client_instance->getbalance('ultraNewWallet'));
//          
//  //dump_it($bitcoin_client_instance->query_arg_to_parameter('getrawtransaction bf4ffc37b48f99403f44c1d2d65da82c6480f88c3fbaf95b2519e023b27d309f'));
//  //dump_it($bitcoin_client_instance->query('decoderawtransaction 01000000015a3a6801276cc5c06eed99fac43f32b017dfe17a48545e75264372885af2a509010000008a473044022046e7bb771b2a8665e9c081968afb685b2af7d09e22c2e185185a0ab7f4c34de9022023e1b6984b274b5df3c18e198e5ea9483f2416889904c96ae2d5c795413a5bd101410489f6dc4e14ac9f2d59ae926c4a5f2546daee14d578ada6b3cb0d1b9c3cb59758b40e7265e5b58595073f1fcecf2a3046636a6a62202cf057af5a7315dd524619ffffffff02a0bb0d00000000001976a91494e0e7a97a51e19fd57a05e3901cfaccf595d7dd88ac30c11d00000000001976a914a2c324ca1403c123779a9d4bcce45fb6ec5c43d688ac00000000'));
//  //dump_it($bitcoin_client_instance->query('getrawtransaction bf4ffc37b48f99403f44c1d2d65da82c6480f88c3fbaf95b2519e023b27d309f'));
//
//  $raw_transaction_arr = ($bitcoin_client_instance->query('getrawtransaction', 'bf4ffc37b48f99403f44c1d2d65da82c6480f88c3fbaf95b2519e023b27d309f', '1'));
//  dump_it($raw_transaction_arr['vout'][1]['scriptPubKey']['addresses'][0]);

  //dump_it($bitcoin_client_instance->gettransaction('bf4ffc37b48f99403f44c1d2d65da82c6480f88c3fbaf95b2519e023b27d309f'));
  //dump_it($bitcoin_client_instance->gettransaction);
  /*
    $user = User::get_instance();
    //$u->auth();
    dump_it($user);
    $slot = Slot::get_instance($user);
    dump_it($slot);
    //Transaction::show_transactions($option = 'last20');

    //$t = new Transaction();
    //$t->get_from_db('asfdasfd');
    //$t = new Transaction($transaction_id = 'asfdasfd', $money_amount = 123, $deposit = false);

    //dump_it($t);
    //echo 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    //dump_it($_SERVER);

    $m = MyBitcoinClient::get_instance();
    dump_it($m->can_connect());
    dump_it($m);

    /*
    echo $m->getbalance('900b15b28c5dbdb15fb626dbde50861b14274384');
    echo '<br>';
    echo $m->getbalance('SlotBank');
    echo '<br>';
    echo $acc = $m->getaccount('1Fnfo4mbjZc6eqW15vnb2yo9EozgdmtdWa');
    echo $m->getbalance($acc);
    echo '<br>';
    $slot = Slot::get_instance();
    echo Slot::$bitcoin_address;
    echo '<br>';
    echo Slot::$bitcoin_account_name;


    //echo $slot->bitcoin
    dump_it($m->getaddressesbyaccount('SlotBank'));
   * 
   */
  //dump_it($m->listreceivedbyaccount());
  //dump_it($m->getaddressesbyaccount('myWallet'));
  //dump_it($m->getinfo());
  //echo $m->getreceivedbyaccount('900b15b28c5dbdb15fb626dbde50861b14274384');
  //echo $m->move('900b15b28c5dbdb15fb626dbde50861b14274384', 'myWallet', 0.01, 5, 'move BTC between accounts' );
  //dump_it(getdate());
  //echo date('h:i:s d.m.Y');
} catch (Exception $exc) {
  dump_it($exc->getTraceAsString());
}
?>
<!--<table id="RecentBetsTable">
  <tbody><tr class="title">
      <td>Details<a class="tip" oldtitle="See all the details for this particular bet" title="">(?)</a></td>
      <td>Processed<a class="tip" oldtitle="Time when the bet occurred" title="">(?)</a></td>
      <td>Bet<a class="tip" oldtitle="The specific bet option the player picked" title="">(?)</a></td>
      <td>Bet Tx<a class="tip" oldtitle="Bitcoin transaction ID for the incoming bet" title="">(?)</a></td>
      <td>Pay Tx<a class="tip" oldtitle="Bitcoin transaction ID for the outgoing payment" title="">(?)</a></td>
      <td>Address<a class="tip" oldtitle="First six digits of the sender's Bitcoin address" title="">(?)</a></td>
      <td>Bet Amount<a class="tip" oldtitle="The amount wagered on this bet" title="">(?)</a></td>
      <td>Result<a class="tip" oldtitle="Shows if the bet won or lost" title="">(?)</a></td>
      <td>Payment Amount<a class="tip" oldtitle="The amount paid out" title="">(?)</a></td>
      <td>Lucky<a class="tip" oldtitle="The lucky number which determines whether the bet wins or loses" title="">(?)</a></td>
    </tr>
    <tr class="oldrows" style="opacity: 1;">
      <td><a href="/full.php?tx=9e1ec459d452df28487d9a09f6bf803d744bc4f3653cbe856767eec813291575">Details</a></td>
      <td>Sunday @ 19:49:58</td>
      <td>lessthan 32768</td>
      <td><a href="http://blockchain.info/search?search=9e1ec459d452df28487d9a09f6bf803d744bc4f3653cbe856767eec813291575">9e1ec459</a>
      </td><td><a href="http://blockchain.info/search?search=991ef2326f7c825e9df7b06c8e698c8bae925233d90e4d7e21fda7ee1675b376">991ef232</a></td>
      <td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td>
      <td align="right">0.01000000</td><td>WIN</td><td align="right">0.01907000</td><td align="right">9723</td></tr>
     Query time: 0.00015807151794434 seconds 
    <tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=965e811f0774d0bcdb96fbbbcd87f753043d7bc995a444d472ee42bfe46ec0be">Details</a></td><td>Sunday @ 19:49:51</td><td>lessthan 56000</td><td><a href="http://blockchain.info/search?search=965e811f0774d0bcdb96fbbbcd87f753043d7bc995a444d472ee42bfe46ec0be">965e811f</a></td><td><a href="http://blockchain.info/search?search=003a38958bd55720d52e0bc0c59488a1994b8553e92e22fc65861e593eabe6cd">003a3895</a></td><td><a href="http://blockchain.info/address/1H923RtpTqQis7PifmZgaExLDvJfBFyf3p">1H923Rt</a></td><td align="right">0.13000000</td><td>WIN</td><td align="right">0.14863585</td><td align="right">37374</td></tr> Query time: 0.00020813941955566 seconds <tr class="oldrows" style="opacity: 1;"><td><a href="/full.php?tx=64a4c668d4ac1bb0dacfeebec931277f92fe843a94e489a890b72524e677c110">Details</a></td><td>Sunday @ 19:49:48</td><td>lessthan 32000</td><td><a href="http://blockchain.info/search?search=64a4c668d4ac1bb0dacfeebec931277f92fe843a94e489a890b72524e677c110">64a4c668</a></td><td><a href="http://blockchain.info/search?search=e62533a6431010198288772a2aa4277476a406eef029f4344a13fba8780067fc">e62533a6</a></td><td><a href="http://blockchain.info/address/1PWCKiocdBPYKNaNhjhyUXVeaVR6K11Lrg">1PWCKio</a></td><td align="right">1.15947408</td><td>LOSE</td><td align="right">0.00529737</td><td align="right">63010</td></tr> Query time: 0.00018119812011719 seconds <tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=17cfb4878436bb0547d915c0213f5527b41c465ac7e60fe95b24da18297abc27">Details</a></td><td>Sunday @ 19:49:46</td><td>lessthan 32768</td><td><a href="http://blockchain.info/search?search=17cfb4878436bb0547d915c0213f5527b41c465ac7e60fe95b24da18297abc27">17cfb487</a></td><td><a href="http://blockchain.info/search?search=d6a6d53a22ef844f2e6934525aeda77b6c1142b0258679095cab3b56ab1f94d3">d6a6d53a</a></td><td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td><td align="right">0.02000000</td><td>WIN</td><td align="right">0.03864000</td><td align="right">27803</td></tr> Query time: 0.00015687942504883 seconds <tr class="oldrows" style="opacity: 1;"><td><a href="/full.php?tx=58611d1176cd73d28a3a2e8543f453cbd8a8b7dbf8eaf26fac1807601a971569">Details</a></td><td>Sunday @ 19:49:35</td><td>lessthan 32768</td><td><a href="http://blockchain.info/search?search=58611d1176cd73d28a3a2e8543f453cbd8a8b7dbf8eaf26fac1807601a971569">58611d11</a></td><td><a href="http://blockchain.info/search?search=11d7628365ad1be0858542edcc922c0e6137b55b0a9c9a58135f04221aae5549">11d76283</a></td><td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td><td align="right">0.01000000</td><td>LOSE</td><td align="right">0.00000001</td><td align="right">53189</td></tr> Query time: 0.00030398368835449 seconds <tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=623350c4515c773c85eb1dacebf032355c254de91486f7ebf8b0653e9e9fe02f">Details</a></td><td>Sunday @ 19:49:24</td><td>lessthan 32768</td><td><a href="http://blockchain.info/search?search=623350c4515c773c85eb1dacebf032355c254de91486f7ebf8b0653e9e9fe02f">623350c4</a></td><td><a href="http://blockchain.info/search?search=dd24c0347b4b5baa50a55264835e50b76f2b54f37c31bf054d30c8d3dc33046d">dd24c034</a></td><td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td><td align="right">0.01000000</td><td>WIN</td><td align="right">0.01907000</td><td align="right">30664</td></tr> Query time: 0.00020003318786621 seconds <tr class="oldrows" style="opacity: 1;"><td><a href="/full.php?tx=6c75b0d767a8f008ba784cf135925ba30eb5a5a4e43e6972e006e3e2056a72ab">Details</a></td><td>Sunday @ 19:49:13</td><td>lessthan 32768</td><td><a href="http://blockchain.info/search?search=6c75b0d767a8f008ba784cf135925ba30eb5a5a4e43e6972e006e3e2056a72ab">6c75b0d7</a></td><td><a href="http://blockchain.info/search?search=fba404dffcbba5898025e8b7a7c523943ece69db36d6bf928874c8724935757e">fba404df</a></td><td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td><td align="right">0.01000000</td><td>WIN</td><td align="right">0.01907000</td><td align="right">6517</td></tr> Query time: 0.00018000602722168 seconds <tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=2ce9baf575a07ae4defad6842050138bfff271fbb5a8c23f962c8cd0e92c6d34">Details</a></td><td>Sunday @ 19:49:08</td><td>lessthan 32000</td><td><a href="http://blockchain.info/search?search=2ce9baf575a07ae4defad6842050138bfff271fbb5a8c23f962c8cd0e92c6d34">2ce9baf5</a></td><td><a href="http://blockchain.info/search?search=f80a724b58bb3d46b05306925bba39c4e1c0df3cc259eb1c91954c388d163a34">f80a724b</a></td><td><a href="http://blockchain.info/address/1KT4QJj2u5TZuAcwRQGnt4Pque9sdpQjQo">1KT4QJj</a></td><td align="right">0.56013240</td><td>LOSE</td><td align="right">0.00230066</td><td align="right">43717</td></tr> Query time: 0.00016498565673828 seconds <tr class="oldrows" style="opacity: 1;"><td><a href="/full.php?tx=b6deb566af7c1c08682a1e1e12585fd49e2416ff313ccdd0095cebddec74d74d">Details</a></td><td>Sunday @ 19:48:52</td><td>lessthan 56000</td><td><a href="http://blockchain.info/search?search=b6deb566af7c1c08682a1e1e12585fd49e2416ff313ccdd0095cebddec74d74d">b6deb566</a></td><td><a href="http://blockchain.info/search?search=009814d7ba330184893e18d48954fb17886fb7c667828715b1723fe96673970b">009814d7</a></td><td><a href="http://blockchain.info/address/19CM4wa1b3TLJXgTaNe5ssTJ9dvqMbamoP">19CM4wa</a></td><td align="right">0.00160000</td><td>WIN</td><td align="right">0.00133551</td><td align="right">36926</td></tr> Query time: 0.00021600723266602 seconds <tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=414b1dcf04695589c462a06d580bfbdd780460fa1c51d3930affeb0a09fcceb3">Details</a></td><td>Sunday @ 19:48:46</td><td>lessthan 32000</td><td><a href="http://blockchain.info/search?search=414b1dcf04695589c462a06d580bfbdd780460fa1c51d3930affeb0a09fcceb3">414b1dcf</a></td><td><a href="http://blockchain.info/search?search=113400f51e52e03a51565ecfef05eb886fe3592b81471f2825c11b99f5e44d46">113400f5</a></td><td><a href="http://blockchain.info/address/19wGn469EDopGNzcrAFZVDX3K16RWVj7uB">19wGn46</a></td><td align="right">0.27059536</td><td>LOSE</td><td align="right">0.00085297</td><td align="right">52268</td></tr> Query time: 0.00016403198242188 seconds  Query time: 0.0002140998840332 seconds  Query time: 0.01046895980835 seconds  Query time: 0.00014209747314453 seconds  Query time: 0.00020909309387207 seconds  Query time: 0.00017619132995605 seconds  Query time: 0.00018095970153809 seconds  Query time: 0.0007929801940918 seconds 
  </tbody></table>-->