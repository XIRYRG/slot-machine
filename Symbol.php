<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Symbol
 *
 * @author vadim24816
 */
class Symbol {
  public $name;
  public $pic;
  public static $pyramid = 'pyramid';//bars=anti new world order
  public static $bitcoin = 'bitcoin';//cherries=bitcoin
  public static $anonymous = 'anonymous';//plums=anonymous
  public static $onion = 'onion';//watermelons=onion
  public static $anarchy = 'anarchy';//oranges=anarchy
  public static $peace = 'peace';//lemons=peace logo
  public static $blank = 'blank';//
  public static $any = 'any';//any symbol [from blank to pyramid]
  //if symbol was given return true, false else.
  public static function is_symbol($sym){
    $all_symbols = self::get_array_of_all_symbols();
    for ($i=0;$i<8;$i++){
      if ($sym == $all_symbols[$i]){
        return TRUE;
      }
    }
    //if not a symbol
    return FALSE;
  }
  protected static function get_array_of_all_symbols(){
    $symbols = array(8);
    $symbols[0] = self::$pyramid;
    $symbols[1] = self::$bitcoin;
    $symbols[2] = self::$anonymous;
    $symbols[3] = self::$onion;
    $symbols[4] = self::$anarchy;
    $symbols[5] = self::$peace;
    $symbols[6] = self::$blank;
    $symbols[7] = self::$any;
    return $symbols;
  }
}

?>
