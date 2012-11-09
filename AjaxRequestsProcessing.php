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



//$get_request = $_GET['slot'];
$user = new User();
$user->auth();
$slot = Slot::get_instance();
$post_request = $_POST['slot'];
switch ($post_request) {
  case 'sync':
    //todo: make auth!!
    $user->get_from_db($user->uid);
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
    $betFromClient = $_POST['currentBet'];
    $json = $slot->spin($betFromClient);
    echo $json;
    break;

  default:
    break;
}

class AjaxRequestsProcessing {
  
}

?>
