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
//$user = new User();

$user = User::get_instance();
//$user->auth();
$slot = Slot::get_instance($user);


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
    $json = json_encode($slot->spin($betFromClient));
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
  //todo: checking if the same user makes requests too often
  case 'getInterestingFacts':
    $cashed_out_money = Transaction::get_total_cached_out_money();
    $total_spin_number = $slot->get_total_spin_number();
    $json = '{"cashed_out_money":"'.$cashed_out_money.'","games_played":"'.$total_spin_number.'"}';
    echo $json;
    break;
  case 'power':
    //for admin only!
    if (!$_SESSION['admin']){
      echo 'You are not logged as admin';
      return false;
    }
    if (!empty($_POST['power']) && $_POST['power'] == 'checkPower'){
      echo $slot->power_on;
      return true;
    }
    if (empty($_POST['power']) || ($_POST['power'] != 'on' && $_POST['power'] != 'off')){
      echo $_POST['power'];
      return false;
    }
    
    if ($res = $slot->power_switch($_POST['power'])){
      echo $res;  
      //echo 'Slot '. $_POST['power'];
    }
    else{
      echo 'Nothing';
    }
    break;
    case 'transactions':
      if (!$_SESSION['admin']){
        echo 'You are not logged as admin';
        return false;
      }
      if (!empty($_POST['fromDate']) && !empty($_POST['toDate'])){
        $fromDate = mysql_real_escape_string($_POST['fromDate']);
        $toDate = mysql_real_escape_string($_POST['toDate']);
        $table = Transaction::show_transactions('admin', 10, $fromDate, $toDate);
        echo $table;
      }
      break;
  default:
    break;
}

class AjaxRequestsProcessing {
  
}

?>
