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

require_once 'Appconfig.php';

class Reel {
  public $reel_line = array(),$name;
  public function __construct($name) {
    $this->name = $name;
  }
  //return new symbol choosed randomly
  public function get_new_randomly_choosed_symbol(){
    //reinit generator
    //mt_srand();
    //choose the symbol number
    //$rand_num = mt_rand(0, 63);
    $randomizer = Randomizer::get_instance();
    $randomizer->mt_srand();//reinit
    $rand_num = $randomizer->mt_rand();
    
    
    $sym = $this->reel_line[$rand_num];
    return $sym;
  }
  //
  public function filling_by_given_symbol_specifin_number_of_cells($symbol, $number){
    for($i = 0; $i < $number; $i++){
      array_push($this->reel_line, $symbol);
    }
    /*
    echo $start_index = count($this->reel_line);
    for( $start_index; $start_index < $start_index + $number; $start_index++ ){
      //$this->reel_line[$start_index] = $symbol;
      array_push($this->reel_line, $symbol);
    }
     * 
     */
  }
}
/*
$r = new Reel('asdf');
dump_it($r);
$r->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anarchy, 3);
$r->filling_by_given_symbol_specifin_number_of_cells(Symbol::$bitcoin, 4);
dump_it($r);
*/
?>
