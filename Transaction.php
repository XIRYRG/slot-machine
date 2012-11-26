<?php

/**
 * Description of Transaction
 *
 * @author vadim24816
 */
require_once 'Appconfig.php';

class Transaction {

  private $transaction_date, $transaction_time, $money_amount, $transaction_id, $deposit, $uid;

  public function __construct($transaction_id = '', $money_amount = 0, $deposit = false, $uid) {
    $this->transaction_id = $transaction_id;
    $this->money_amount = $money_amount;
    $this->deposit = $deposit; //deposit == 1|true -> deposit money / deposit == 0|false -> withdraw
    //save at the creating moment
    $this->transaction_time = ''; // = $transaction_time;
    $this->transaction_date = '';
    $this->uid = $uid;
    $this->save_in_db();
  }

  public function save_in_db() {
    $db = DBconfig::get_instance();
    $res = $db->query("INSERT INTO 
      transactions(transaction_id, money_amount, deposit, transaction_date, transaction_time, uid) 
      VALUES('$this->transaction_id', '$this->money_amount', '$this->deposit', NOW(), NOW(), '$this->uid')
    ");
    if (!$res) {
      return FALSE;
    }
    return true;
  }

  public function get_from_db($transaction_id) {
    $transaction_id = mysql_real_escape_string($transaction_id);
    $db = DBconfig::get_instance();
    $transaction = $db->mysql_fetch_array('SELECT * FROM transactions WHERE transaction_id = \'' . $transaction_id . '\'');
    //if there is no user with given uid
    if ($transaction == FALSE) {
      return FALSE;
    }
    //the user is found
    else {
      $this->transaction_id = $transaction_id;
      $this->money_amount = $transaction['money_amount'];
      $this->deposit = $transaction['deposit'];
      $this->transaction_date = $transaction['transaction_date'];
      $this->transaction_time = $transaction['transaction_time'];
      $this->uid = $transaction['uid'];
      return TRUE;
    }
  }
  //endNum == 0 --> no limit
  public static function show_transactions($option = 'last', $startNum = 0, $endNum = 20, $from_date = '2012-11-01', $to_date = '2052-11-01', $fromPage = 'index') {
    $from_date = mysql_real_escape_string($from_date);
    $to_date = mysql_real_escape_string($to_date);
    $startNum = mysql_real_escape_string($startNum);
    $endNum = mysql_real_escape_string($endNum);
    $limit = "LIMIT  $startNum , $endNum";
    if ($endNum == 0)
      $limit = ' ';
    $fromPage = mysql_real_escape_string($fromPage);;
    $db = DBconfig::get_instance();
    //$t = new Transaction();
    //show last 20 by time
    if ($option == 'last') {
      $query = 'SELECT * FROM transactions ORDER BY `transaction_date` DESC ,  `transaction_time` DESC  '.$limit;
    }
    elseif ($option == 'biggestwinners') {
      $query = 'SELECT * FROM transactions WHERE `deposit` = 0 ORDER BY `money_amount` DESC '.$limit;
    }
    elseif ($option == 'transactions'){
      $query = "SELECT * FROM transactions WHERE transaction_date BETWEEN '$from_date' AND '$to_date' ORDER BY `transaction_date` DESC ,  `transaction_time` DESC  ".$limit;
    }
    else{
      return false;
    }
    //dump_it($query);
    $output = '<div id="transactions"><table border="2px" style="border-collapse: collapse; border-color: white; width: 340px;">';//start stats table
    //$transactions = $db->mysql_fetch_array($query);
    $res = $db->query($query);
    
//    while ($transactions = $db->mysql_fetch_array_by_result($res)) {
//      dump_it($transactions);
//    }
    
    $output .= 
        '<tr>
          <td>Transaction time</td>
        <td>Money</td>
        <td>Transaction ID</td>';
    if (!empty($_SESSION['admin']) && $fromPage == 'admin' &&  $_SESSION['admin'] == 'true'){
      $output .= '<td>UID</td>';
    }
        $output .= '</tr>
      ';
    
    while ($transactions = $db->mysql_fetch_array_by_result($res)) {
      if ($transactions['deposit']) {
        $money_column = '<td style="color:#E2001A;">Deposit: '.$transactions['money_amount'].' </td>';
        //$color = '#E2001A';
      }
      else {
        //$color = '#25803C';
        $money_column = '<td style="color:#25803C;">Withdrawn: '.$transactions['money_amount'].' </td>';
      }
      
      $output .= 
        '<tr>
          <td>' . $transactions['transaction_date'] . ' ' . $transactions['transaction_time'] . '</td>'
        . $money_column .
        '<td><a href="http://blockchain.info/search?search='.$transactions['transaction_id'].'">' . substr($transactions['transaction_id'], 0, 9) . '</a></td>';
      if (!empty($_SESSION['admin']) && $fromPage == 'admin' &&  $_SESSION['admin'] == 'true'){
        $output .= '<td>' . $transactions['uid'] . '</td>';
      }
      $output .= '</tr>
      ';
    }
     
    $output .= '</table></div>';//end
    echo $output;
  }
  public static function show_grouped_by_user($from_date, $to_date){
    $from_date = mysql_real_escape_string($from_date);
    $to_date = mysql_real_escape_string($to_date);
    $db = DBconfig::get_instance();
    $query = "
      SELECT t.uid, deposited_table.deposited, withdrawn_table.withdrawn
      FROM 
      transactions t
      LEFT JOIN ( 
        SELECT d1.uid, sum(d1.money_amount) deposited, d1.deposit  
        FROM transactions as d1 
        WHERE 
          d1.deposit = 1 AND 
          d1.transaction_date BETWEEN '$from_date' AND '$to_date'
        GROUP BY d1.uid ) as deposited_table
      ON deposited_table.uid = t.uid
      LEFT JOIN ( 
        SELECT d0.uid, sum(d0.money_amount) withdrawn, d0.deposit  
        FROM transactions as d0 
        WHERE 
          d0.deposit = 0 AND 
          d0.transaction_date BETWEEN '$from_date' AND '$to_date'
          GROUP BY d0.uid ) as withdrawn_table
      ON withdrawn_table.uid = t.uid
      WHERE t.transaction_date BETWEEN '$from_date' AND '$to_date'
      GROUP BY t.uid 
      ORDER BY t.id 
    ";
    $output = "
        <br />
        <table border=\"1px\" style=\"border-collapse: collapse;\">
          <tr>
            <td>Grouped by UID</td>
            <td>Total deposited</td>
            <td>Total withdrawn</td>
          </tr>
            ";
    $res = $db->query($query);
    while ($transactions_grouped_by_user = $db->mysql_fetch_array_by_result($res)){
      foreach ($transactions_grouped_by_user as $key => $value) {
        if ($value == NULL){
          $transactions_grouped_by_user[$key] = '0';
        }
      }
//      dump_it($transactions_grouped_by_user);
      $output .= 
            '<tr>
              <td>'.$transactions_grouped_by_user['uid'].'</td>
              <td>'.$transactions_grouped_by_user['deposited'].'</td>
              <td>'.$transactions_grouped_by_user['withdrawn'].'</td>
            </tr>';
    }
    $output .= '</table>';
    return $output;
  }

  public static function show_cach_in_out_profit_payback_table($from_date, $to_date){
    $from_date = mysql_real_escape_string($from_date);
    $to_date = mysql_real_escape_string($to_date);
    $total_cached_out = Transaction::get_total_cached_out_money($from_date, $to_date);
    $total_cached_in = Transaction::get_total_cached_in_money($from_date, $to_date);
    if ($total_cached_in != 0){
      $payback = ($total_cached_out/$total_cached_in)*100;
      $payback = round($payback, 2);
    }
    else{
      $total_cached_in = 0;
      $payback = 100;
    }
    $profit = $total_cached_in - $total_cached_out;
    $output_start = "
        <br />
        <table border=\"1px\" style=\"border-collapse: collapse;\">
          <tr>
            <td>Cash in</td>
            <td>Cash out</td>
            <td>Profit</td>
            <td>Payback %</td>
            ";
    $output_end = "
          </tr>
          <tr>
              <td>$total_cached_in</td>
              <td>$total_cached_out</td>
              <td>$profit</td>
              <td>$payback</td>
            </tr>
        </table>
      ";
    echo $output_start.$output_end;
  }

  //just gives a total of all the money cashed out, sums up all the withdraw transactions
  public static function get_total_cached_out_money($from_date = '2012-11-01', $to_date = '2052-11-01'){
    $from_date = mysql_real_escape_string($from_date);
    $to_date = mysql_real_escape_string($to_date);
    
    $db = DBconfig::get_instance();
    $query = "SELECT SUM(money_amount) FROM transactions WHERE `deposit` = 0  AND `transaction_date` BETWEEN '$from_date' AND '$to_date'";
    $total_cached_out = $db->mysql_fetch_array($query);
    if (!$total_cached_out[0]){
      return 0;
    }
    return $total_cached_out[0];
  }
  public static function get_total_cached_in_money($from_date = '2012-11-01', $to_date = '2052-11-01'){
    $from_date = mysql_real_escape_string($from_date);
    $to_date = mysql_real_escape_string($to_date);
    $db = DBconfig::get_instance();
    $query = "SELECT SUM(money_amount) FROM transactions WHERE `deposit` = 1 AND `transaction_date` BETWEEN '$from_date' AND '$to_date'";
    $total_cached_in = $db->mysql_fetch_array($query);
    if (!$total_cached_in[0]){
      return 0;
    }
    return $total_cached_in[0];
  }
  public static function stats(){
//    <table id="RecentBetsTable">
//				<tbody><tr class="title">
//				<td>Details<a class="tip" oldtitle="See all the details for this particular bet" title="">(?)</a></td>
//				<td>Processed<a class="tip" oldtitle="Time when the bet occurred" title="">(?)</a></td>
//				<td>Bet<a class="tip" oldtitle="The specific bet option the player picked" title="">(?)</a></td>
//				<td>Bet Tx<a class="tip" oldtitle="Bitcoin transaction ID for the incoming bet" title="">(?)</a></td>
//				<td>Pay Tx<a class="tip" oldtitle="Bitcoin transaction ID for the outgoing payment" title="">(?)</a></td>
//				<td>Address<a class="tip" oldtitle="First six digits of the sender's Bitcoin address" title="">(?)</a></td>
//				<td>Bet Amount<a class="tip" oldtitle="The amount wagered on this bet" title="">(?)</a></td>
//				<td>Result<a class="tip" oldtitle="Shows if the bet won or lost" title="">(?)</a></td>
//				<td>Payment Amount<a class="tip" oldtitle="The amount paid out" title="">(?)</a></td>
//				<td>Lucky<a class="tip" oldtitle="The lucky number which determines whether the bet wins or loses" title="">(?)</a></td>
//				</tr><tr class="oldrows" style="opacity: 1;"><td><a href="/full.php?tx=9e1ec459d452df28487d9a09f6bf803d744bc4f3653cbe856767eec813291575">Details</a></td><td>Sunday @ 19:49:58</td><td>lessthan 32768</td><td><a href="http://blockchain.info/search?search=9e1ec459d452df28487d9a09f6bf803d744bc4f3653cbe856767eec813291575">9e1ec459</a></td><td><a href="http://blockchain.info/search?search=991ef2326f7c825e9df7b06c8e698c8bae925233d90e4d7e21fda7ee1675b376">991ef232</a></td><td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td><td align="right">0.01000000</td><td>WIN</td><td align="right">0.01907000</td><td align="right">9723</td></tr><!-- Query time: 0.00015807151794434 seconds --><tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=965e811f0774d0bcdb96fbbbcd87f753043d7bc995a444d472ee42bfe46ec0be">Details</a></td><td>Sunday @ 19:49:51</td><td>lessthan 56000</td><td><a href="http://blockchain.info/search?search=965e811f0774d0bcdb96fbbbcd87f753043d7bc995a444d472ee42bfe46ec0be">965e811f</a></td><td><a href="http://blockchain.info/search?search=003a38958bd55720d52e0bc0c59488a1994b8553e92e22fc65861e593eabe6cd">003a3895</a></td><td><a href="http://blockchain.info/address/1H923RtpTqQis7PifmZgaExLDvJfBFyf3p">1H923Rt</a></td><td align="right">0.13000000</td><td>WIN</td><td align="right">0.14863585</td><td align="right">37374</td></tr><!-- Query time: 0.00020813941955566 seconds --><tr class="oldrows" style="opacity: 1;"><td><a href="/full.php?tx=64a4c668d4ac1bb0dacfeebec931277f92fe843a94e489a890b72524e677c110">Details</a></td><td>Sunday @ 19:49:48</td><td>lessthan 32000</td><td><a href="http://blockchain.info/search?search=64a4c668d4ac1bb0dacfeebec931277f92fe843a94e489a890b72524e677c110">64a4c668</a></td><td><a href="http://blockchain.info/search?search=e62533a6431010198288772a2aa4277476a406eef029f4344a13fba8780067fc">e62533a6</a></td><td><a href="http://blockchain.info/address/1PWCKiocdBPYKNaNhjhyUXVeaVR6K11Lrg">1PWCKio</a></td><td align="right">1.15947408</td><td>LOSE</td><td align="right">0.00529737</td><td align="right">63010</td></tr><!-- Query time: 0.00018119812011719 seconds --><tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=17cfb4878436bb0547d915c0213f5527b41c465ac7e60fe95b24da18297abc27">Details</a></td><td>Sunday @ 19:49:46</td><td>lessthan 32768</td><td><a href="http://blockchain.info/search?search=17cfb4878436bb0547d915c0213f5527b41c465ac7e60fe95b24da18297abc27">17cfb487</a></td><td><a href="http://blockchain.info/search?search=d6a6d53a22ef844f2e6934525aeda77b6c1142b0258679095cab3b56ab1f94d3">d6a6d53a</a></td><td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td><td align="right">0.02000000</td><td>WIN</td><td align="right">0.03864000</td><td align="right">27803</td></tr><!-- Query time: 0.00015687942504883 seconds --><tr class="oldrows" style="opacity: 1;"><td><a href="/full.php?tx=58611d1176cd73d28a3a2e8543f453cbd8a8b7dbf8eaf26fac1807601a971569">Details</a></td><td>Sunday @ 19:49:35</td><td>lessthan 32768</td><td><a href="http://blockchain.info/search?search=58611d1176cd73d28a3a2e8543f453cbd8a8b7dbf8eaf26fac1807601a971569">58611d11</a></td><td><a href="http://blockchain.info/search?search=11d7628365ad1be0858542edcc922c0e6137b55b0a9c9a58135f04221aae5549">11d76283</a></td><td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td><td align="right">0.01000000</td><td>LOSE</td><td align="right">0.00000001</td><td align="right">53189</td></tr><!-- Query time: 0.00030398368835449 seconds --><tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=623350c4515c773c85eb1dacebf032355c254de91486f7ebf8b0653e9e9fe02f">Details</a></td><td>Sunday @ 19:49:24</td><td>lessthan 32768</td><td><a href="http://blockchain.info/search?search=623350c4515c773c85eb1dacebf032355c254de91486f7ebf8b0653e9e9fe02f">623350c4</a></td><td><a href="http://blockchain.info/search?search=dd24c0347b4b5baa50a55264835e50b76f2b54f37c31bf054d30c8d3dc33046d">dd24c034</a></td><td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td><td align="right">0.01000000</td><td>WIN</td><td align="right">0.01907000</td><td align="right">30664</td></tr><!-- Query time: 0.00020003318786621 seconds --><tr class="oldrows" style="opacity: 1;"><td><a href="/full.php?tx=6c75b0d767a8f008ba784cf135925ba30eb5a5a4e43e6972e006e3e2056a72ab">Details</a></td><td>Sunday @ 19:49:13</td><td>lessthan 32768</td><td><a href="http://blockchain.info/search?search=6c75b0d767a8f008ba784cf135925ba30eb5a5a4e43e6972e006e3e2056a72ab">6c75b0d7</a></td><td><a href="http://blockchain.info/search?search=fba404dffcbba5898025e8b7a7c523943ece69db36d6bf928874c8724935757e">fba404df</a></td><td><a href="http://blockchain.info/address/1NGLFr7TnEsrHYAakydbuv5r5bwL9NxNLt">1NGLFr7</a></td><td align="right">0.01000000</td><td>WIN</td><td align="right">0.01907000</td><td align="right">6517</td></tr><!-- Query time: 0.00018000602722168 seconds --><tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=2ce9baf575a07ae4defad6842050138bfff271fbb5a8c23f962c8cd0e92c6d34">Details</a></td><td>Sunday @ 19:49:08</td><td>lessthan 32000</td><td><a href="http://blockchain.info/search?search=2ce9baf575a07ae4defad6842050138bfff271fbb5a8c23f962c8cd0e92c6d34">2ce9baf5</a></td><td><a href="http://blockchain.info/search?search=f80a724b58bb3d46b05306925bba39c4e1c0df3cc259eb1c91954c388d163a34">f80a724b</a></td><td><a href="http://blockchain.info/address/1KT4QJj2u5TZuAcwRQGnt4Pque9sdpQjQo">1KT4QJj</a></td><td align="right">0.56013240</td><td>LOSE</td><td align="right">0.00230066</td><td align="right">43717</td></tr><!-- Query time: 0.00016498565673828 seconds --><tr class="oldrows" style="opacity: 1;"><td><a href="/full.php?tx=b6deb566af7c1c08682a1e1e12585fd49e2416ff313ccdd0095cebddec74d74d">Details</a></td><td>Sunday @ 19:48:52</td><td>lessthan 56000</td><td><a href="http://blockchain.info/search?search=b6deb566af7c1c08682a1e1e12585fd49e2416ff313ccdd0095cebddec74d74d">b6deb566</a></td><td><a href="http://blockchain.info/search?search=009814d7ba330184893e18d48954fb17886fb7c667828715b1723fe96673970b">009814d7</a></td><td><a href="http://blockchain.info/address/19CM4wa1b3TLJXgTaNe5ssTJ9dvqMbamoP">19CM4wa</a></td><td align="right">0.00160000</td><td>WIN</td><td align="right">0.00133551</td><td align="right">36926</td></tr><!-- Query time: 0.00021600723266602 seconds --><tr class="colored oldrows" style="opacity: 1;"><td><a href="/full.php?tx=414b1dcf04695589c462a06d580bfbdd780460fa1c51d3930affeb0a09fcceb3">Details</a></td><td>Sunday @ 19:48:46</td><td>lessthan 32000</td><td><a href="http://blockchain.info/search?search=414b1dcf04695589c462a06d580bfbdd780460fa1c51d3930affeb0a09fcceb3">414b1dcf</a></td><td><a href="http://blockchain.info/search?search=113400f51e52e03a51565ecfef05eb886fe3592b81471f2825c11b99f5e44d46">113400f5</a></td><td><a href="http://blockchain.info/address/19wGn469EDopGNzcrAFZVDX3K16RWVj7uB">19wGn46</a></td><td align="right">0.27059536</td><td>LOSE</td><td align="right">0.00085297</td><td align="right">52268</td></tr><!-- Query time: 0.00016403198242188 seconds --><!-- Query time: 0.0002140998840332 seconds --><!-- Query time: 0.01046895980835 seconds --><!-- Query time: 0.00014209747314453 seconds --><!-- Query time: 0.00020909309387207 seconds --><!-- Query time: 0.00017619132995605 seconds --><!-- Query time: 0.00018095970153809 seconds --><!-- Query time: 0.0007929801940918 seconds -->
//												</tbody></table>
  }
}
?>