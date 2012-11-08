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
$post_request = $_POST['slot'];
switch ($post_request) {
  case 'sync':
    $user->get_from_db($user->uid);
    if ($return_string = json_encode($user)){
      echo $return_string;
    }
    
    break;

  default:
    break;
}

class AjaxRequestsProcessing {
  
}

?>
