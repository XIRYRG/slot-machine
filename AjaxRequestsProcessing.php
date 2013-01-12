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
  case 'cashOut':
    try{
      $out =  $user->cash_out();
      if ($out){
        echo $out;
      }
      else{
        //echo '{"cashOut":"0"}';
        echo '0';
      }
    }
    catch (Exception $e){
      dump_it($e->getTraceAsString());
    }
    break;
  case 'getHashedServerSeeds':
    //$result_seeds = $slot->getResultSeeds();
    $server_seeds = $slot->getHashedServerSeeds();
    //dump_it($result_seeds);
    $json = json_encode($server_seeds);
    echo $json;
    break;
  case 'sync':
    //todo: make auth!!
    //$user->get_from_db($user->uid);
    if ($return_string = json_encode($user)){
      echo $return_string;
    }
    
    break;
  case 'spinPressed':
    //no bet
    if (!isset($_POST['currentBet']) || !isset($_POST['clientSeed'])){
      echo 'bet wasn\'t transferred or client seed is wrong';
      return false;
    }
    //normal mode
    $client_seed = $_POST['clientSeed'];
    $betFromClient = $_POST['currentBet'];
    //Array [Objs]
    $json = json_encode($slot->spin($betFromClient, $client_seed));
    echo $json;
    break;
  case 'checkForIncommingPayment':
    $cashed_in_value = $user->cash_in();
    if ($cashed_in_value > 0){
      echo $cashed_in_value;
    }
    else{
      //no cashed in money
      //echo json_encode(0);
      //echo '{"cashOut":"0"}';
      echo '0';
    }
    
    break;
    //$user->update_from_db();
    
    
  //todo: checking if the same user makes requests too often
  case 'getInterestingFacts':
    $cashed_out_money = Transaction::get_total_cached_out_money();
    $total_spin_number = $slot->get_total_spin_number();
    $json = '{"cashed_out_money":"'.$cashed_out_money.'","games_played":"'.$total_spin_number.'"}';
    echo $json;
    break;
  case 'power':
    
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
    //for admin only!
    if ($_SESSION['admin']){
      //echo 'You are not logged as admin';
      //return false;
      $options['playing'] = $slot->set_option('playing', $_POST['power']);
      $options['paying_out'] = $slot->set_option('paying_out', $_POST['paying_out']);
      if (!$options['playing'] || !$options['paying_out']){
        echo 'Nothing';
      }
      else{
        echo json_encode($options);
      }
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
//        $fromDate = mysql_real_escape_string($_POST['fromDate']);
//        $toDate = mysql_real_escape_string($_POST['toDate']);
        $fromDate = ($_POST['fromDate']);
        $toDate = ($_POST['toDate']);
        //if request from admin page
        if (!empty($_POST['page']) && $_POST['page'] == 'admin'){
          $table = Transaction::show_transactions('transactions',0, 0, $fromDate, $toDate, 'admin');
          $table .= Transaction::show_grouped_by_user($fromDate, $toDate);
          $table .= Transaction::show_cach_in_out_profit_payback_table($fromDate, $toDate);
          
        }
        //if request from other page
        else{
          $table = Transaction::show_transactions('transactions',0, 0, $fromDate, $toDate);
        }
      }
      if (!empty($_POST['to']) && !empty($_POST['option'])){
//        $option = mysql_real_escape_string($_POST['option']);
//        $from = mysql_real_escape_string($_POST['from']);
//        $to = mysql_real_escape_string($_POST['to']);
        $option = $_POST['option'];
        $from = $_POST['from'];
        $to = $_POST['to'];
        $table = Transaction::show_transactions($option, $from, $to);
      }
      echo $table;
      break;
    case 'bitcoin_connect':
      $b = MyBitcoinClient::get_instance();
      if ($b->can_connect() === true){
        echo '1';
      }
      else{
        echo '0';
      }
      break;
  default:
    break;
}

class AjaxRequestsProcessing {
  
}

?>
