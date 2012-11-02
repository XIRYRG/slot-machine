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
require_once 'Payline.php';
require_once 'Paytable.php';

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
  protected $last_payline;//, $reels = array(3);
  public $reel1,$reel2,$reel3;
  //return new randomly generated payline
  public function get_new_payline(){
    for ($i = 0; $i < 3; $i++){
      $syms[$i] = $this->reels[$i]->get_new_randomly_choosed_symbol();
    }
    $new_payline = new Payline($syms[0], $syms[1], $syms[2]);
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
  public $symbol_weight_reel1,$symbol_weight_reel2,$symbol_weight_reel3;
  function weight_table_filling(){
    //the symbol weight on reelN
    $this->symbol_weight_reel1[Symbol::$pyramid] = 4;
    $this->symbol_weight_reel1[Symbol::$bitcoin] = 5;
    $this->symbol_weight_reel1[Symbol::$anonymous] = 6;
    $this->symbol_weight_reel1[Symbol::$onion] = 6;
    $this->symbol_weight_reel1[Symbol::$anarchy] = 7;
    $this->symbol_weight_reel1[Symbol::$peace] = 8;
    $this->symbol_weight_reel1[Symbol::$blank] = 28;
    
    $this->symbol_weight_reel2[Symbol::$pyramid] = 3;
    $this->symbol_weight_reel2[Symbol::$bitcoin] = 4;
    $this->symbol_weight_reel2[Symbol::$anonymous] = 4;
    $this->symbol_weight_reel2[Symbol::$onion] = 5;
    $this->symbol_weight_reel2[Symbol::$anarchy] = 5;
    $this->symbol_weight_reel2[Symbol::$peace] = 6;
    $this->symbol_weight_reel2[Symbol::$blank] = 37;
    
    $this->symbol_weight_reel3[Symbol::$pyramid] = 1;
    $this->symbol_weight_reel3[Symbol::$bitcoin] = 2;
    $this->symbol_weight_reel3[Symbol::$anonymous] = 3;
    $this->symbol_weight_reel3[Symbol::$onion] = 4;
    $this->symbol_weight_reel3[Symbol::$anarchy] = 6;
    $this->symbol_weight_reel3[Symbol::$peace] = 6;
    $this->symbol_weight_reel3[Symbol::$blank] = 42;
    //total: 64 for every reel
    
    $this->reel1 = new Reel('reel1');
    $this->reel1->reel_line = $this->get_symbols_reel_line($this->symbol_weight_reel1);
    $this->reel2 = new Reel('reel2');
    $this->reel2->reel_line = $this->get_symbols_reel_line($this->symbol_weight_reel2);
    $this->reel3 = new Reel('reel3');
    $this->reel3->reel_line = $this->get_symbols_reel_line($this->symbol_weight_reel3);
    /*
    $this->reel1_line = $this->get_symbols_reel_line($this->reel1);
    $this->reel2_line = $this->get_symbols_reel_line($this->reel2);
    $this->reel3_line = $this->get_symbols_reel_line($this->reel3);
    */
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
    //echo $current_num_in_weight_arr;
    return $weight_arr;
  }
}

$w1 = WeightTable::get_instance();
/*
echo $w1->reel1->get_new_randomly_choosed_symbol();
echo ' ';
echo $w1->reel2->get_new_randomly_choosed_symbol();
echo ' ';
echo $w1->reel3->get_new_randomly_choosed_symbol();
echo ' ';
 * 
 */

function show_generated_total_weight_table() {
  echo 'generated total weight table';
  $w1 = WeightTable::get_instance();
  echo '<table border=1px>';
  echo '<tr>';
    
    echo '<td>';
    echo '---';
    echo '</td>';
    echo '<td>';
    echo Symbol::$pyramid;
    echo '</td>';
    echo '<td>';
    echo Symbol::$bitcoin;
    echo '</td>';
    echo '<td>';
    echo Symbol::$anonymous;
    echo '</td>';
    echo '<td>';
    echo Symbol::$onion;
    echo '</td>';
    echo '<td>';
    echo Symbol::$anarchy;
    echo '</td>';
    echo '<td>';
    echo Symbol::$peace;
    echo '</td>';
    echo '<td>';
    echo Symbol::$blank;
    echo '</td>';
    echo '</tr>';
  $number_of_symbol = array();
  for ($i = 0; $i < 3; $i++){
    $number_of_symbol[$i][Symbol::$pyramid] = 0;
    $number_of_symbol[$i][Symbol::$bitcoin] = 0;
    $number_of_symbol[$i][Symbol::$anonymous] = 0;
    $number_of_symbol[$i][Symbol::$onion] = 0;
    $number_of_symbol[$i][Symbol::$anarchy] = 0;
    $number_of_symbol[$i][Symbol::$peace] = 0;
    $number_of_symbol[$i][Symbol::$blank] = 0;
  }
  for($reel_num = 0; $reel_num < 3; $reel_num++){
    
    
    echo '<tr>';
    for ($i = 0; $i < 64; $i++){
      if ($reel_num == 0){
        $cur_sym = $w1->reel1->get_new_randomly_choosed_symbol();
      }
      if ($reel_num == 1){
        $cur_sym = $w1->reel2->get_new_randomly_choosed_symbol();
      }
      if ($reel_num == 2){
        $cur_sym = $w1->reel3->get_new_randomly_choosed_symbol();
      }

      if (Symbol::$pyramid == $cur_sym){
        $number_of_symbol[$reel_num][Symbol::$pyramid]++;
      }
      if (Symbol::$bitcoin == $cur_sym){
        $number_of_symbol[$reel_num][Symbol::$bitcoin]++;
      }
      if (Symbol::$anonymous == $cur_sym){
        $number_of_symbol[$reel_num][Symbol::$anonymous]++;
      }
      if (Symbol::$onion == $cur_sym){
        $number_of_symbol[$reel_num][Symbol::$onion]++;
      }
      if (Symbol::$anarchy == $cur_sym){
        $number_of_symbol[$reel_num][Symbol::$anarchy]++;
      }
      if (Symbol::$peace == $cur_sym){
        $number_of_symbol[$reel_num][Symbol::$peace]++;
      }
      if (Symbol::$blank == $cur_sym){
        $number_of_symbol[$reel_num][Symbol::$blank]++;
      }
    }
    echo '<td>';
    echo 'reel #'.$reel_num;
    echo '</td>';
    echo '<td>';
    echo $number_of_symbol[$reel_num][Symbol::$pyramid];
    echo '</td>';
    echo '<td>';
    echo $number_of_symbol[$reel_num][Symbol::$bitcoin];
    echo '</td>';
    echo '<td>';
    echo $number_of_symbol[$reel_num][Symbol::$anonymous];
    echo '</td>';
    echo '<td>';
    echo $number_of_symbol[$reel_num][Symbol::$onion];
    echo '</td>';
    echo '<td>';
    echo $number_of_symbol[$reel_num][Symbol::$anarchy];
    echo '</td>';
    echo '<td>';
    echo $number_of_symbol[$reel_num][Symbol::$peace];
    echo '</td>';
    echo '<td>';
    echo $number_of_symbol[$reel_num][Symbol::$blank];
    echo '</td>';
    
    echo '</tr>';
  }
  //dump_it($number_of_symbol);
  echo '</table>';
}

function possible_combinations(){
  $w1 = WeightTable::get_instance();
  $slot = Slot::get_instance();
  $slot->reels[0] = $w1->reel1;
  $slot->reels[1] = $w1->reel2;
  $slot->reels[2] = $w1->reel3;
  $paytable = Paytable::get_instance();
  
  $number_of_win_lines[Symbol::$pyramid] = 0;
  $number_of_win_lines[Symbol::$bitcoin] = 0;
  $number_of_win_lines[Symbol::$anonymous] = 0;
  $number_of_win_lines[Symbol::$onion] = 0;
  $number_of_win_lines[Symbol::$anarchy] = 0;
  $number_of_win_lines[Symbol::$peace] = 0;
  $number_of_win_lines[Symbol::$blank] = 0;
  $number_of_win_lines['bitcoin_2'] = 0;
  $number_of_win_lines['bitcoin_1'] = 0;
  $number_of_win_lines['lose'] = 0;
  $N = 262144;
  echo '<br /><table border=1px>';
  for($i = 0; $i < $N; $i++){
    $new_payline = $slot->get_new_payline();
    $result = $paytable->paylines_matching_with_wins($new_payline);
    switch ($result){
      case 'pyramid_3': 
        $number_of_win_lines[Symbol::$pyramid]++;
        break;
      case 'bitcoin_3': 
        $number_of_win_lines[Symbol::$bitcoin]++;
        break;
      case 'anonymous_3': 
        $number_of_win_lines[Symbol::$anonymous]++;
        break;
      case 'onion_3': 
        $number_of_win_lines[Symbol::$onion]++;
        break;
      case 'anarchy_3': 
        $number_of_win_lines[Symbol::$anarchy]++;
        break;
      case 'peace_3': 
        $number_of_win_lines[Symbol::$peace]++;
        break;
      case 'blank': 
        $number_of_win_lines[Symbol::$blank]++;
        break;
      case 'bitcoin_2': 
        $number_of_win_lines['bitcoin_2']++;
        break;
      case 'bitcoin_1': 
        $number_of_win_lines['bitcoin_1']++;
        break;
      case 'lose': 
        $number_of_win_lines['lose']++;
        break;
    }
  }
  
  echo '<tr>';
  echo '<td>';
  echo 'win combination';
  echo '</td>';
  echo '<td>';
  echo 'amount of appear';
  echo '</td>';
  echo '</tr>';
  foreach ($number_of_win_lines as $key => $value) {
    echo '<tr><td>'.$key;
    echo '</td>';
    echo '<td>';
    echo $value; //$number_of_win_lines[$key];
    echo '</td></tr>';
  }  
  echo '</table>';
}


show_generated_total_weight_table();
possible_combinations();
?>