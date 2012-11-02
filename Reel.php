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
  public $reel_line,$name;
  public function __construct($name) {
    $this->name;
  }
  public function get_new_randomly_choosed_symbol(){
    mt_srand();
    //choose the symbol number
    $rand_num = mt_rand(0, 63);
    $sym = $this->reel_line[$rand_num];
    return $sym;
  }
}

$r1 = new Reel('reel1');
?>
