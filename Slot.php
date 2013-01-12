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
  public static $bitcoin_account_name = 'SlotBank';
  public static $bitcoin_address;
  private function __construct(){}
  private function __clone(){} 
  private function __wakeup(){} 
  public static function get_instance($user = null){
    if (is_null(self::$slot)){
      self::$slot = new Slot();
      //if user not exist it will create him!
      if ($user == null){
        self::$user = User::get_instance();
      }
      else{
        self::$user = $user;
      }
      //common account for all money in slot
      self::$bitcoin_account_name = 'SlotBank';
      
      if (!empty($_SESSION['server_seeds'])){
        self::$slot->server_seeds = $_SESSION['server_seeds'];
      }
      
      


      //todo: move getting bitcoin instance to separate method and use it only for the payment operations
      //uncomment
      
      $bitcoin_client_instance = MyBitcoinClient::get_instance();
      if ($bitcoin_client_instance->can_connect() === true){
        try{

          self::$bitcoin_address = $bitcoin_client_instance->getaccountaddress(self::$bitcoin_account_name);
        }
        catch (Exception $e){
          dump_it($e->getTraceAsString());
        }
      }
      

      
      
      
      
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
      self::$slot->playing = 'on';
      self::$slot->paying_out = 'on';
      return self::$slot;
    }
    return self::$slot;
  }
  
  protected $last_payline;//, $reels = array(3);
  public $reel1,$reel2,$reel3, $reels;
  public $currentBet, $currentUserBalance, $lastBet, $state;
  public $playing, $paying_out;// = true;
  public $result_seeds, $server_seeds;


  public function is_valid_client_seed($client_seed){
    /* old
    if (!is_numeric($client_seed)){
      return false;
    }
    if ($client_seed >= 0 && $client_seed <= 1000000){
      return true;
    }
    else{
      return false;
    }
     * 
     */
    if (!is_string($client_seed)){
      return false;
    }
    else{
      return true;
    }
  }

  //validate client's bet 
  public function is_valid_bet($bet_from_client){
    //not a number
    if (!is_numeric($bet_from_client)){
      return false;
    }
    self::$user->update_from_db();
    if ($bet_from_client >= 0 && $bet_from_client <= self::$user->money_balance){
      return true;
    }
    else {
      return false;
    }
  }
  public function get_total_spin_number(){
    $db = DBconfig::get_instance();
    $query = 'SELECT COUNT(id) FROM spins';
    $total_spin_number = $db->mysqli_fetch_array($query);
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
  public function save_options_in_db(){
    $db = DBconfig::get_instance();
    $res = $db->query("UPDATE slot_options SET 
        `option_value` = '$this->playing'
        WHERE `option_name` = 'playing'
      ");
    if (!$res) {
      return FALSE;
    }
    $res = $db->query("UPDATE slot_options SET 
        `option_value` = '$this->paying_out'
        WHERE `option_name` = 'paying_out'
      ");
    if (!$res) {
      return FALSE;
    }
    return true;
  }
  public function get_option_from_db(){
    $db = DBconfig::get_instance();
    $options['playing'] = $db->mysqli_fetch_array("SELECT option_value FROM slot_options WHERE option_name = 'playing' ");
    $options['paying_out'] = $db->mysqli_fetch_array("SELECT option_value FROM slot_options WHERE option_name = 'paying_out' ");
    $this->playing = $options['playing']['option_value'];
    $this->paying_out = $options['paying_out']['option_value'];
    return true;
  }

  public function get_option($option_name){
    if ($option_name != 'playing' && $option_name != 'paying_out'){
      return false;
    }
    $this->get_option_from_db();
    switch ($option_name) {
      case 'paying_out':
        return $this->paying_out;
        break;
      case 'playing':
        return $this->playing;
        break;
      default:
        return false;
        break;
    }
  }
          
  public function set_option($option_name, $option_value){
    if (!$_SESSION['admin']){
      return false;
    }
    switch ($option_name) {
      case 'paying_out':
        switch ($option_value) {
          case 'on':
            $this->paying_out = 'on';
            break;
          case 'off':
            $this->paying_out = 'off';
            break;
          default:
            $this->paying_out = 'on';
            break;
        }
        break;
      case 'playing':
        switch ($option_value) {
          case 'on':
            $this->playing = 'on';
            break;
          case 'off':
            $this->playing = 'off';
            break;
          default:
            $this->playing = 'on';
            break;
        }
        break;
      default:
        break;
    }
    
    if ($this->save_options_in_db()){
      return $option_value;
    }
    else{
      return false;
    }
    
  }

  //make spin
  public function spin($bet_from_client, $client_seed){
    $this->get_option_from_db();
    if ($this->playing === 'off'){
      //echo 'Slot-machine powered off';
      //return 'Slot-machine powered off';
      return -1;
    }
    //todo: limit the number of spins for the same uid (e.g.: 1 spin per 6 second
    if (!$this->is_valid_bet($bet_from_client)){
      //echo '[Bet <= 0 or Bet not number.]';
      //return '[Bet <= 0 or Bet not number.]';
      return -2;
    }
    if (!$this->is_valid_client_seed($client_seed)){
      return -3;
    }
    $this->currentBet = $bet_from_client;
    //bet was 
    $this->lastBet = $this->currentBet;
    self::$user->money_balance -= $this->currentBet;
    $this->currentBet = 0;
    
    //genearte array of 3 server seeds
    //$server_seeds[$i] = mt_rand(0, 1000000);
    //$new_payline = $this->get_new_payline($client_seed);
    $arr_of_new_payline_and_servers_seeds = $this->get_new_payline_and_servers_seeds($client_seed);
    $new_payline = $arr_of_new_payline_and_servers_seeds['new_payline'];
    //$result_hashed_seeds = $arr_of_new_payline_and_servers_seeds['result_hashed_seeds'];
//    dump_it($server_seeds);
    $paytable = Paytable::get_instance();
    $win_combination_name = $paytable->paylines_matching_with_wins($new_payline);
    //user gets money he won
    $won_money = $paytable->payoff_value($new_payline) * $bet_from_client;
    self::$user->money_balance += $won_money;
    //...
    
    $new_payline->bet_from_client = $bet_from_client;
    $new_payline->multiplier = $paytable->payoff_value($new_payline);
    //keep last payline//not used(?)
    $this->last_payline = $new_payline;
    $s = self::$user->save_in_db();
    self::$user->update_from_db();
    //logging every spin (by default)
    if (self::$log_every_spin)
      $this->save_spin_in_db(self::$user->uid, $bet_from_client, $win_combination_name, $won_money );
    //return $new_payline;
    $arr_of_new_payline_and_servers_seeds['new_payline']->bet_from_client = $new_payline->bet_from_client;
    $arr_of_new_payline_and_servers_seeds['new_payline']->multiplier = $new_payline->multiplier;
    return $arr_of_new_payline_and_servers_seeds;
  }

  //return array [ new randomly generated payline; and array of 3 server seeds ]
  //get new random number for all 3 reel..
  public function get_new_payline_and_servers_seeds($client_seed){
    //hash client seed
//    dump_it($client_seed);
    $client_seed = sha1($client_seed);
//    dump_it($client_seed);
    //generate new server seeds
    $server_seeds = $this->getServerSeeds();
    //$server_seeds = generateServerSeeds();
    for ($i = 0; $i < 3; $i++){
      //hash all 3 server seeds
      //$server_seeds[$i] = sha1(mt_rand());
      
      //$result_hashed_seeds[$i] = sha1($client_seed.$server_seeds[$i]);
      //..using client and server seed
      $syms[$i] = $this->reels[$i]->get_new_randomly_choosed_symbol($client_seed, $server_seeds[$i]);
    }
//    dump_it($result_hashed_seeds);
    $new_payline = new Payline($syms[0], $syms[1], $syms[2]);
    //save last result seeds and server seeds
    //$_SESSION['server_seeds'] = $server_seeds;
    //$this->result_seeds = $result_hashed_seeds;
    //$this->server_seeds = $server_seeds;
    //return $new_payline;
    
    //generate new seeds HERE, because last seeds was used for getting payline above in this func
    $this->generateServerSeeds();
    $res_arr = array('new_payline' => $new_payline, 'server_seeds' => $server_seeds);
    return $res_arr;
  }
  
//  public function getResultSeeds(){
//    return $this->result_seeds;
//  }

  //get random generated 3 server seeds
  public function generateServerSeeds(){
    mt_srand(microtime(true));
    for ($i = 0; $i < 3; $i++){
      $server_seeds[$i] = sha1(mt_rand());
    }
    $this->server_seeds = $server_seeds;
    $_SESSION['server_seeds'] = $server_seeds;
    return $server_seeds;
  }
  //just get 3 current server seeds
  public function getServerSeeds(){
    if (!empty($this->server_seeds)){// && empty($_SESSION['server_seeds'])){
      return $this->server_seeds;
    }
    elseif (!empty($_SESSION['server_seeds'])){
      $this->server_seeds = $_SESSION['server_seeds'];
      //return $this->server_seeds;
    }
    else{
      $this->generateServerSeeds();
    }
    return $this->server_seeds;
  }
  public function getHashedServerSeeds(){
    $server_seeds = $this->getServerSeeds();
    $json_server_seeds = json_encode($server_seeds);
//    for ($i = 0; $i < 3; $i++){
//      $server_seeds[$i] = sha1($server_seeds[$i]);
//    }
    //wanna string? got it!
    return sha1($json_server_seeds);
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