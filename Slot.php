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

require_once 'Appconfig.php';

class Slot {
  //make it singletone
  protected static $slot;
  protected static $log_every_spin;
  public static $user;
  public static $bitcoin_account_name;
  public static $bitcoin_address;
  private function __construct(){}
  private function __clone(){} 
  private function __wakeup(){} 
  public static function get_instance($user){
    if (is_null(self::$slot)){
      self::$slot = new Slot();
      //if user not exist it will create him!
      //self::$user = new User();
      //self::$user = User::get_instance();
      //self::$user->auth();
      self::$user = $user;
      //common account for all money in slot
      self::$bitcoin_account_name = 'SlotBank';
      
      $bitcoin_client_instance = MyBitcoinClient::get_instance();
      self::$bitcoin_address = $bitcoin_client_instance->getaccountaddress(self::$bitcoin_account_name);
      /*
      try{
        $bitcoin_client_instance = MyBitcoinClient::get_instance();
        //if account not exist - create it
        self::$bitcoin_address = $bitcoin_client_instance->getaccountaddress(self::$bitcoin_account_name);
        //if cant't create/get - exception

        if (!self::$bitcoin_address){
         throw new BitcoinClientException("Can't get/create slot account in bitcoind");
        } 

      }
      catch (Exception $e){
        dump_it($e->getTraceAsString());
        //throw new BitcoinClientException("Can't connect to bitcoind server");
      }*/
      self::$log_every_spin = true;
      //if (self::$user->get_from_db())
      //self::$slot->slot_filling();
      
      $weight_table = WeightTable::get_instance();
      self::$slot->reel1 = new Reel('reel1');
      self::$slot->reel1->reel_line = $weight_table->get_symbols_reel_line($weight_table->symbol_weight_reel1);
      self::$slot->reel2 = new Reel('reel2');
      self::$slot->reel2->reel_line = $weight_table->get_symbols_reel_line($weight_table->symbol_weight_reel2);
      self::$slot->reel3 = new Reel('reel3');
      self::$slot->reel3->reel_line = $weight_table->get_symbols_reel_line($weight_table->symbol_weight_reel3);
      
      self::$slot->reels[0] = self::$slot->reel1;
      self::$slot->reels[1] = self::$slot->reel2;
      self::$slot->reels[2] = self::$slot->reel3;
      return self::$slot;
    }
    return self::$slot;
  }
  
  protected $last_payline;//, $reels = array(3);
  public $reel1,$reel2,$reel3, $reels;
  public $currentBet, $currentUserBalance, $lastBet, $state;
  

  //validate client's bet 
  public function is_valid_bet($bet_from_client){
    //not a number
    if (!is_numeric($bet_from_client)){
      return false;
    }
    self::$user->update_from_db();
    if ($bet_from_client > 0 && $bet_from_client <= self::$user->money_balance){
      return true;
    }
    else {
      return false;
    }
  }
  public function get_total_spin_number(){
    $db = DBconfig::get_instance();
    $query = 'SELECT COUNT(id) FROM spins';
    $total_spin_number = $db->mysql_fetch_array($query);
    return $total_spin_number[0];
  }
  
  public function save_spin_in_db($uid, $user_bet, $payline, $won_money){
    $db = DBconfig::get_instance();
    $res = $db->query("INSERT INTO 
      spins(uid, user_bet, payline, won_money, spin_time) 
      VALUES('$uid', '$user_bet', '$payline', '$won_money', NOW())
    ");
    if (!$res) {
      return FALSE;
    }
    return true;
  }
  
  //make spin
  public function spin($bet_from_client){
    //todo: limit the number of spins for the same uid (e.g.: 1 spin per 6 second
    if (!$this->is_valid_bet($bet_from_client)){
      echo '[Bet <= 0 or Bet not number.]';
      return false;
    }
    /*
    //already started
    if ($this->state == 'started'){
      //console.log('[Slot started already. Wait while it have stoped! ]');
      echo '[Slot started already. Wait while it have stoped! ]';
      return false;
    }
    //slot started
    //$this->getStateStarted();
    $this->state = 'started';
    //$this->getStateStop();
    $this->state = 'stop';
    */
    $this->currentBet = $bet_from_client;
    //bet was 
    $this->lastBet = $this->currentBet;
    self::$user->money_balance -= $this->currentBet;
    $this->currentBet = 0;
    $new_payline = $this->get_new_payline();
    $paytable = Paytable::get_instance();
    $win_combination_name = $paytable->paylines_matching_with_wins($new_payline);
    //user gets money he won
    $won_money = $paytable->payoff_value($new_payline) * $bet_from_client;
    self::$user->money_balance += $won_money;
    $this->last_payline = $new_payline;
    $s = self::$user->save_in_db();
    self::$user->update_from_db();
    //logging every spin (by default)
    if (self::$log_every_spin)
      $this->save_spin_in_db(self::$user->uid, $bet_from_client, $win_combination_name, $won_money );
    return json_encode($new_payline);
  }

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
      self::$weight_table->total_weight_table_filling();
      return self::$weight_table;
    }
    return self::$weight_table;
  }
  
  //protected
          public $reel1,$reel2,$reel3;
  public $symbol_weight_reel1,$symbol_weight_reel2,$symbol_weight_reel3;
  public function total_weight_table_filling(){
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
    /*
    $slot = Slot::get_instance();
    
    $this->reel1 = new Reel('reel1');
    $this->reel1->reel_line = $this->get_symbols_reel_line($this->symbol_weight_reel1);
    $this->reel2 = new Reel('reel2');
    $this->reel2->reel_line = $this->get_symbols_reel_line($this->symbol_weight_reel2);
    $this->reel3 = new Reel('reel3');
    $this->reel3->reel_line = $this->get_symbols_reel_line($this->symbol_weight_reel3);
    
    $slot->reels[0] = $this->reel1;
    $slot->reels[1] = $this->reel2;
    $slot->reels[2] = $this->reel3;
    
    //weight table filling, not total weight
    $this->weight_table_filling();
    */
    
    //$this->total_weight_table_filling();
    /*
    $this->reel1_line = $this->get_symbols_reel_line($this->reel1);
    $this->reel2_line = $this->get_symbols_reel_line($this->reel2);
    $this->reel3_line = $this->get_symbols_reel_line($this->reel3);
    */
  }
  
  public function weight_table_filling(){
    //for reel1 
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$bitcoin, 3);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 2);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anonymous, 3);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 2);

    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$onion, 3);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 2);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anarchy, 4);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 2);
    
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$peace, 4);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 5);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$pyramid, 4);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 5);
    
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$bitcoin, 2);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 2);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anonymous, 3);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 2);
    
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$onion, 3);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 2);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anarchy, 3);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 2);
    
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$peace, 4);
    $this->reel1->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 2);
    
    //for reel2
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$bitcoin, 2);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anonymous, 2);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);

    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$onion, 3);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anarchy, 3);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$peace, 3);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 5);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$pyramid, 3);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 5);
    
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$bitcoin, 2);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anonymous, 2);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$onion, 2);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anarchy, 2);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$peace, 3);
    $this->reel2->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    
    //for reel3
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$bitcoin, 1);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anonymous, 2);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);

    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$onion, 2);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anarchy, 3);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$peace, 3);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 8);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$pyramid, 1);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 7);
    
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$bitcoin, 1);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anonymous, 1);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$onion, 2);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$anarchy, 3);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$peace, 3);
    $this->reel3->filling_by_given_symbol_specifin_number_of_cells(Symbol::$blank, 3);
    
    
  }

  //$this->reel1
  //return the filled line (array) that consists of 64 symbols with considering the weight table
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
?>