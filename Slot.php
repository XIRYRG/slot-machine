<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Slot
 *
 * @author vadim24816
 */

require_once 'appconfig.php';
require_once 'dumpit.php';
require_once 'Symbol.php';
require_once 'Reel.php';

class Slot {
  //make it singletone
  protected static $slot;
  private function __construct(){}
  private function __clone(){} 
  private function __wakeup(){} 
  public static function get_instance(){
    if (is_null(self::$slot)){
      self::$slot = new Slot();
      //self::$slot->slot_filling();
      return self::$slot;
    }
    return self::$slot;
  }
  
  //last payline == current payline, reels is array of reel objects
  protected $last_payline, $reels = array(3);
  //return new randomly generated payline
  public function get_new_payline(){
    for ($i = 0; $i < 3; $i++){
      $syms[$i] = $this->reels[$i]->get_new_randomly_generated_symbol();
    }
    $new_payline = new Payline($sym1, $sym2, $sym3);
    return $new_payline;
  }
  
}

class WeightTable{
  //make it singletone
  protected static $weight_table;
  private function __construct(){}
  private function __clone(){} 
  private function __wakeup(){} 
  public static function get_instance(){
    if (is_null(self::$weight_table)){
      self::$weight_table = new WeightTable();
      self::$weight_table->weight_table_filling();
      return self::$weight_table;
    }
    return self::$weight_table;
  }
  
  //protected
          public $reel1,$reel2,$reel3;
  public $reel1;
  function weight_table_filling(){
    $this->reel1[Symbol::$pyramid] = 4;
    $this->reel1[Symbol::$bitcoin] = 5;
    $this->reel1[Symbol::$anonymous] = 6;
    $this->reel1[Symbol::$onion] = 6;
    $this->reel1[Symbol::$anarchy] = 7;
    $this->reel1[Symbol::$peace] = 8;
    $this->reel1[Symbol::$blank] = 28;
    
    $this->reel2[Symbol::$pyramid] = 3;
    $this->reel2[Symbol::$bitcoin] = 4;
    $this->reel2[Symbol::$anonymous] = 4;
    $this->reel2[Symbol::$onion] = 5;
    $this->reel2[Symbol::$anarchy] = 5;
    $this->reel2[Symbol::$peace] = 6;
    $this->reel2[Symbol::$blank] = 37;
    
    $this->reel3[Symbol::$pyramid] = 1;
    $this->reel3[Symbol::$bitcoin] = 2;
    $this->reel3[Symbol::$anonymous] = 3;
    $this->reel3[Symbol::$onion] = 4;
    $this->reel3[Symbol::$anarchy] = 6;
    $this->reel3[Symbol::$peace] = 6;
    $this->reel3[Symbol::$blank] = 42;
    //total: 64 for every reel
  }
  //$this->reel1
  //return the filled line (array) that consists of 64 symbols considering the weight table
  function get_symbols_reel_line($reel){
    //weight_arr == reel line
    $weight_arr = array(64);
    $current_num_in_weight_arr = 0;//counter in weight_arr
    //for every symbol
    foreach ($reel as $key => $value) {
      for ($i = 0; $i < $reel[$key]; $i++){
        $weight_arr[$current_num_in_weight_arr] = $key;
        //total number of processed cells
        $current_num_in_weight_arr++;
      }
    }
    echo $current_num_in_weight_arr;
    return $weight_arr;
  }
}

$w1 = WeightTable::get_instance();
$line1 = $w1->get_symbols_reel_line($w1->reel1);
dump_it($line1);
$line2 = $w1->get_symbols_reel_line($w1->reel2);
dump_it($line2);
$line3 = $w1->get_symbols_reel_line($w1->reel3);
dump_it($line3);

?>
