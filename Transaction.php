<?php

/**
 * Description of Transaction
 *
 * @author vadim24816
 */
require_once 'Appconfig.php';

class Transaction {

  private $transaction_time, $money_amount, $transaction_id, $deposit;

  public function __construct($transaction_id = '', $money_amount = 0, $deposit = false) {
    $this->transaction_id = $transaction_id;
    $this->money_amount = $money_amount;
    $this->deposit = $deposit; //deposit == 1|true -> deposit money / deposit == 0|false -> withdraw
    //save at the creating moment
    $this->transaction_time = ''; // = $transaction_time;
    $this->save_in_db();
  }

  public function save_in_db() {
    $db = DBconfig::get_instance();
    $res = $db->query("INSERT INTO 
      transactions(transaction_id, money_amount, deposit, transaction_time) 
      VALUES('$this->transaction_id', '$this->money_amount', '$this->deposit', NOW())
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
      $this->transaction_time = $transaction['transaction_time'];
      return TRUE;
    }
  }

  public static function show_transactions($option = 'last20') {
    $db = DBconfig::get_instance();
    //$t = new Transaction();
    //show last 20 by time
    if ($option == 'last20') {
      $query = 'SELECT * FROM transactions ORDER BY `transaction_time` DESC LIMIT  0, 20';
    }
    //
    if ($option == '20biggestwinners') {
      $query = 'SELECT * FROM transactions WHERE `deposit` = 0 ORDER BY `money_amount` DESC LIMIT  0, 20';
    }
    $output = '<table><tr>';//start stats table
    //$transactions = $db->mysql_fetch_array($query);
    while ($transactions = $db->mysql_fetch_array($query)) {
      dump_it($transactions);
    }
    /*$output .= 
        '<td>Transaction time: ' . $transactions['transaction_time'] . '</td>
        <td style:"color=\'' . $color . '\';">Money: ' . $transactions['money_amount'] . '</td>
        <td>Transaction ID: <a href="">' . substr($transactions['transaction_id'], 0, 9) . '...</a></td>
      ';
    /*
    while ($transactions = $db->mysql_fetch_array($query)) {
      if ($transactions['deposit']) {
        $color = 'red';
      }
      else {
        $color = 'geen';
      }
      /*
      $output .= 
        '<td>Transaction time: ' . $transactions['transaction_time'] . '</td>
        <td style:"color=\'' . $color . '\';">Money: ' . $transactions['money_amount'] . '</td>
        <td>Transaction ID: <a href="">' . substr($transactions['transaction_id'], 0, 9) . '...</a></td>
      ';
       
    }
     * 
     */
    $output .= '</tr></table>';//end
    echo $output;
  }
  //just gives a total of all the money cashed out, sums up all the withdraw transactions
  public static function get_total_cached_out_money(){
    $db = DBconfig::get_instance();
    $query = 'SELECT SUM(money_amount) FROM transactions WHERE `deposit` = 0';
    $total_cached_out = $db->mysql_fetch_array($query);
    return $total_cached_out[0];
  }

}
?>