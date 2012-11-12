<?php
/*
if (!empty($_SERVER['REQUEST_SCHEME']) && ($_SERVER['REQUEST_SCHEME'] != 'https')){
    header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
  }
 * 
 */
if (!isset($_SERVER['HTTPS'])){
  header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
}
?>
