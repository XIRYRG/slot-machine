<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Randomizer
 *
 * @author vadim24816
 */
class Randomizer {
  //make it singletone
  protected static $randomizer;
  private function __construct(){}
  private function __clone(){} 
  private function __wakeup(){} 
  public static function get_instance(){
    if (is_null(self::$randomizer)){
      self::$randomizer = new Randomizer();
      //self::$slot->slot_filling();
      return self::$randomizer;
    }
    return self::$randomizer;
  }
  
  //return new random number using mt_rand()
  public function rand(){
    srand();
    $rand_num = rand(0, 63);
    return $rand_num;
  }
  
  //reinit generator of mt_rand() using mt_srand()
  public function mt_srand(){
    mt_srand();
  }
  //return new random number using mt_rand()
  public function mt_rand(){
    $rand_num = mt_rand(0, 63);
    return $rand_num;
  }
  
}

?>
