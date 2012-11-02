<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Reel
 *
 * @author vadim24816
 */

require_once 'dumpit.php';
class Reel {
  public $reel_line;
  public function __construct() {
    ;
  }
  public function get_new_randomly_generated_symbol(){
    $rand_num = mt_rand(0, 63);
    return $rand_num;
  }
}

$r1 = new Reel();
echo $r1->get_new_randomly_generated_symbol();
dump_it($r1);

?>
