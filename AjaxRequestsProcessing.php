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
    if (empty($_POST['power'])){
      return false;
    }
    if ($_POST['power'] == 'check_options'){
      $options['playing'] = $slot->get_option('playing');
      $options['paying_out'] = $slot->get_option('paying_out');
      echo json_encode($options);
      return true;
    }
    if (
            empty($_POST['paying_out']) || 
            ($_POST['power'] != 'on' && $_POST['power'] != 'off') || 
            ($_POST['paying_out'] != 'on' && $_POST['paying_out'] != 'off')
       )
    {
      echo $_POST['power'].' '.$_POST['paying_out'];
      return false;
    }
    $options['playing'] = $slot->set_option('playing', $_POST['power']);
    $options['paying_out'] = $slot->set_option('paying_out', $_POST['paying_out']);
    if (!$options['playing'] || !$options['paying_out']){
      echo 'Nothing';
    }
    else{
      echo json_encode($options);
      
      //echo $res;
    }
    break;
    case 'transactions':
//      if (!$_SESSION['admin']){
//        echo 'You are not logged as admin';
//        return false;
//      }
      $table = 'Wrong params';
      //between from and to dates
      if (!empty($_POST['fromDate']) && !empty($_POST['toDate'])){// && !empty($_POST['option'])){
        //$option = mysql_real_escape_string($_POST['option']);
        $fromDate = mysql_real_escape_string($_POST['fromDate']);
        $toDate = mysql_real_escape_string($_POST['toDate']);
        $table = Transaction::show_transactions('transactions',0, 20, $fromDate, $toDate);
        if (!empty($_POST['page']) && $_POST['page'] == 'admin'){
          $table .= Transaction::show_cach_in_out_profit_payback_table($fromDate, $toDate);
        }
      }
      if (!empty($_POST['to']) && !empty($_POST['option'])){
        $option = mysql_real_escape_string($_POST['option']);
        $from = mysql_real_escape_string($_POST['from']);
        $to = mysql_real_escape_string($_POST['to']);
        $table = Transaction::show_transactions($option, $from, $to);
      }
      echo $table;
      break;
  default:
    break;
}

class AjaxRequestsProcessing {
  
}

?>
