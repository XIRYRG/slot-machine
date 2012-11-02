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
  
  protected $reel1,$reel2,$reel3;
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
}

$w1 = WeightTable::get_instance();
dump_it($w1);

?>
