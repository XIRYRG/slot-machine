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
  //args - (string $client_seed, string $server_seed)
  public function get_new_randomly_choosed_symbol($client_seed, $server_seed){
//    $server_seed = mt_rand(0, 1000000);
    //$result_seed = $client_seed + $server_seed;
    $client_seed = crc32($client_seed);
    $server_seed = crc32($server_seed);
    $result_seed = ($client_seed + $server_seed) % PHP_INT_MAX;
    
    $randomizer = Randomizer::get_instance();
    $randomizer->mt_srand($result_seed);//reinit
    $rand_num = $randomizer->mt_rand();
    
    
    $sym = $this->reel_line[$rand_num];
    return $sym;
  }
  //
  public function filling_by_given_symbol_specifin_number_of_cells($symbol, $number){
    for($i = 0; $i < $number; $i++){
      array_push($this->reel_line, $symbol);
    }
  }
}
?>
