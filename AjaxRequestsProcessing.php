<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxRequestsProcessing
 *
 * @author vadim24816
 */

require_once 'Appconfig.php';

if (!empty($_POST['slot'])){
  $post_request = $_POST['slot'];
}
else {
  exit('No slot option in POST array');
}

//$get_request = $_GET['slot'];
$user = new User();
$user->auth();
$slot = Slot::get_instance();
//$post_request = '';

switch ($post_request) {
  case 'sync':
    //todo: make auth!!
    //$user->get_from_db($user->uid);
    if ($return_string = json_encode($user)){
      echo $return_string;
    }
    
    break;
  case 'spinPressed':
    //no bet
    if (empty($_POST['currentBet'])){
      echo 'bet wasn\'t transferred';
      return false;
    }
    //normal mode
    $betFromClient = $_POST['currentBet'];
    $json = $slot->spin($betFromClient);
    echo $json;
    break;
  case 'checkForIncommingPayment':
    $m = MyBitcoinClient::get_instance();
    //$user->update_from_db();
    //$user->money_balance = $m->getbalance($user->uid);
    //$user->save_in_db();
    $json = $user->money_balance;
    echo $json;
    break;
  case 'getInterestingFacts':
    $cashed_out_money = Transaction::get_total_cached_out_money();
    $total_spin_number = $slot->get_total_spin_number();
    $json = '{"cashed_out_money":"'.$cashed_out_money.'","games_played":"'.$total_spin_number.'"}';
    echo $json;
    break;
  default:
    break;
}

class AjaxRequestsProcessing {
  
}

?>
