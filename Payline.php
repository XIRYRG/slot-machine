<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Payline
 *
 * @author vadim24816
 */
require_once 'Appconfig.php';

//array of 3 symbols like: ['pyramid', 'bitcoin', 'anonymous']
class Payline {
  public $sym1, $sym2, $sym3;
  //make the object with given symbols
  public function __construct($sym1, $sym2, $sym3) {
    if (!Symbol::is_symbol($sym1) || !Symbol::is_symbol($sym2) || !Symbol::is_symbol($sym3)){
      echo 'Not a symbols was given';
      return;
    }
    $this->sym1 = $sym1;
    $this->sym2 = $sym2;
    $this->sym3 = $sym3;
  }
  //return symbols as array
  public function get_symbols_array(){
    $syms = array(3);
    $syms[0] = $this->sym1;
    $syms[1] = $this->sym2;
    $syms[2] = $this->sym3;
    return $syms;
  }
}
//$payline1 = new Payline(Symbol::$anarchy, Symbol::$bitcoin, Symbol::$pyramid);
//$payline1 = $payline1->get_symbols_array();

?>
