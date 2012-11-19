<?php
function show_interesting_facts() {
  $user = User::get_instance();
  $total_cached_out = Transaction::get_total_cached_out_money();
  $slot = Slot::get_instance($user);
  $total_spin_number = $slot->get_total_spin_number();
  $output = "
    <br />
    <table border=\"1px\" style=\"border-collapse: collapse;\">
      <tr>
        <td id='total_cached_out'>Cashed out money: $total_cached_out BTC</td>
        <td id='total_spin_number'>Games played: $total_spin_number </td>
      </tr>
    </table>
  ";
  echo $output;
}

function show_generated_total_weight_table() {
  echo 'generated the number  of appearances table';
  //$w1 = WeightTable::get_instance();
  $user = User::get_instance();
  $w1 = Slot::get_instance($user);
  echo '<table border="1px" style="border-collapse: collapse;">';
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
    echo '<td>';
    echo 'Sum';
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
      foreach ($number_of_symbol[$reel_num] as $key => $value) {
        //e.g.: if (Symbol::$pyramid == $cur_sym)
        if ($key == $cur_sym){
          //e.g.: $number_of_symbol[$reel_num][Symbol::$bitcoin]++;
          $number_of_symbol[$reel_num][$key]++;
        }
      }
    }
    echo '<td>';
    echo 'reel #'.$reel_num;
    echo '</td>';
    
    $sum = 0;
    foreach ($number_of_symbol[$reel_num] as $key => $value) {
      //count total weight
      $sum += $value;
      echo '<td>'.$value;
      echo '</td>';
      /*
      *the same as e.g.:
      *echo '<td>';
      *echo $number_of_symbol[$reel_num][Symbol::$onion];
      *echo '</td>';
      * 
      */
    }
    echo '<td>';
    echo $sum;
    echo '</td>';
    echo '</tr>';
  }
  //dump_it($number_of_symbol);
  echo '</table>';
}

function possible_combinations(){
  $w1 = WeightTable::get_instance();
  $user = User::get_instance();
  $slot = Slot::get_instance($user);
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
  //$N = 1000000;
  echo '<br /><table border="1px" style="border-collapse: collapse;">';
  for($i = 0; $i < $N; $i++){
    $new_payline = $slot->get_new_payline();
    $result = $paytable->paylines_matching_with_wins($new_payline);
    //unset($new_payline);
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
      case 'blank_3': 
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
  echo 'Table of amount of wins for '.$N.' stops (the most combinations (excepts bitcoin_2 - any 2 are bitcoins, bitcoin_1 - any 1 is bitcoin and lose) for 3 symbols )';
  echo '<tr>';
  echo '<td>';
  echo 'win combination';
  echo '</td>';
  echo '<td>';
  echo 'combination occurrence';
  echo '</td>';
  echo '<td>';
  echo 'probability of appear';
  echo '</td>';
  echo '<td>';
  echo 'money ( probability * payoff = )';
  echo '</td>';
  echo '<td>';
  echo 'probability of money returning (to player)';
  echo '</td>';
  echo '<td>';
  echo 'money (occurrence * payoff - occurrence * min spin cost)';
  echo '</td>';
  echo '</tr>';
  $min_spin_cost = 1;
  $total_sum = 0;
  $total_probability_of_apear = 0;
  $total_money = 0;
  $probability_of_occur = 0;
  foreach ($number_of_win_lines as $combination_name => $occurrence) {
    echo '<tr>';
    echo '<td>'.$combination_name.'</td>';
    echo '<td>'.$occurrence.'</td>'; //$number_of_win_lines[$combination_name];
    $probability_of_occur = $occurrence/$N;
    echo '<td>'.$occurrence.' / ' .$N. ' = ' .$probability_of_occur.'</td>';
    $paytable = Paytable::get_instance();
    //get payoff for given combination name (key_...)
    if ($combination_name == 'bitcoin_2' || $combination_name == 'bitcoin_1'){
      $total_probability_of_apear += $probability_of_occur;
      $payoff = $paytable->payoff_value_by_name($combination_name);
    }
    elseif ($combination_name == 'lose'){
      $payoff = 0;
    }
    elseif ($combination_name == 'blank'){
      $payoff = 0;
    }
    else{
      $total_probability_of_apear += $probability_of_occur;
      $payoff = $paytable->payoff_value_by_name($combination_name.'_3');
    }
    
    $res = $probability_of_occur * $payoff;
    if ($res > 0)
      $total_sum += $res;
    //echo '<td>'.$occurrence.' * '.$payoff.' = '.$res.'</td>';
    echo '<td>'.$probability_of_occur.' * '.$payoff.' = </td>';
    echo '<td>'.$res.'</td>';
    $money = $occurrence * $payoff - $occurrence * $min_spin_cost;
    $total_money += $money;
    echo '<td>'.$occurrence .' * '.$payoff.' - '.$occurrence.' * '.$min_spin_cost.'  = '.$money.'</td>';
    echo '</tr>';
   
  }
  echo '<tr>';
  echo '<td>TOTAL:</td>';
  echo '<td>'.$N.'</td>';
  echo '<td>'.$total_probability_of_apear.'</td>';
  echo '<td>sum=</td>';
  echo '<td>'.$total_sum.'</td>';
  echo '<td> profit(deposited - paid) = '.$total_money*(-1).'</td>';
  echo '</tr>';
  echo '</table>';
}
?>
