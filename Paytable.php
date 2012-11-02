<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Paytable
 *
 * @author vadim24816
 */

require_once 'Payline.php';
require_once 'Symbol.php';
require_once 'appconfig.php';

class Paytable {
  //make it singletone
  protected static $paytable;
  private function __construct(){}
  private function __clone(){} 
  private function __wakeup(){} 
  public static function get_instance(){
    if (is_null(self::$paytable)){
      self::$paytable = new Paytable();
      self::$paytable->paytable_filling();
      return self::$paytable;
    }
    return self::$paytable;
  }
  
  //win paylines
  protected $pyramid_3, $bitcoin_3, $anonymous_3, $onion_3, $anarchy_3, $peace_3, $bitcoin_2, $bitcoin_1;
  //filling win paylines
  public function paytable_filling(){
    $this->pyramid_3 = new Payline(Symbol::$pyramid, Symbol::$pyramid, Symbol::$pyramid);
    $this->bitcoin_3 = new Payline(Symbol::$bitcoin, Symbol::$bitcoin, Symbol::$bitcoin);
    $this->anonymous_3 = new Payline(Symbol::$anonymous, Symbol::$anonymous, Symbol::$anonymous);
    $this->onion_3 = new Payline(Symbol::$onion, Symbol::$onion, Symbol::$onion);
    $this->anarchy_3 = new Payline(Symbol::$anarchy, Symbol::$anarchy, Symbol::$anarchy);
    $this->peace_3 = new Payline(Symbol::$peace, Symbol::$peace, Symbol::$peace);
    $this->bitcoin_2 = new Payline(Symbol::$bitcoin, Symbol::$bitcoin, Symbol::$any);
    $this->bitcoin_1 = new Payline(Symbol::$bitcoin, Symbol::$any, Symbol::$any);
  }
  //compare given payline with win payline
  public function paylines_matching_with_wins($payline){
    switch ($payline){
      //3 matches
      case $this->pyramid_3:
        echo 'pyramid_3';
        return 5000;
        break;
      case $this->bitcoin_3:
        echo 'bitcoin_3';
        return 1000;
        break;
      case $this->anonymous_3:
        echo 'anonymous_3';
        return 200;
        break;
      case $this->onion_3:
        echo 'onion_3';
        return 100;
        break;
      case $this->anarchy_3:
        echo 'anarchy_3';
        return 50;
        break;
      case $this->peace_3:
        echo 'peace_3';
        return 25;
        break;
      //something else
      default:
        //bitcoin_2
        if ($this->amount_of_symbols_in_payline($payline, Symbol::$bitcoin) == 2){
          echo 'bitcoin_2';
          return 10;
        }
        //bitcoin_1
        if ($this->amount_of_symbols_in_payline($payline, Symbol::$bitcoin) == 1){
          echo 'bitcoin_1';
          return 2;
        }
        echo 'you are not win';
        return 0;
    }
  }
  //return amount of secific symbol in 
  public function amount_of_symbols_in_payline($payline, $symbol){
    $amount = 0;
    $payline_symbols = $payline->get_symbols_array();
    for ($i=0; $i<3; $i++){
      //found 1 more symbol
      if ($payline_symbols[$i] == $symbol){
        $amount++;
      }
    }
    return $amount;
  }
}

$paytable1 = Paytable::get_instance();
$payline1 = new Payline(Symbol::$blank, Symbol::$any, Symbol::$any);
$paytable1->paylines_matching_with_wins($payline1);
?>
