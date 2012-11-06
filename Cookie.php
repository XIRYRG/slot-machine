<?php

/**
 * Description of Cookie
 *
 * @author vadim24816
 */
class Cookie {
  public static function get_instance(){
    if (is_null(self::$cookie)){
      self::$cookie = new Cookie();
      //self::$cookie->config_filling();
      return self::$cookie;
    }
    return self::$cookie;
  }
  private function __construct(){  }
  private function __clone(){} 
  private function __wakeup(){} 
  protected static $cookie;
  
  public function set_cookie($name, $value, $expire, $path, $domain, $secure, $httponly){
    /*
    $name = htmlentities($name);
    $value = htmlentities($value);
    $expire = htmlentities($expire);
    $path = htmlentities($path);
    $domain = htmlentities($domain);
    $secure = htmlentities($secure);
    $httponly = htmlentities($httponly);
*/
    setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
  }
  public function get_cookie($name){
    $name = htmlentities($name);
    //if there is no cookie with given name
    if (empty($_COOKIE[$name]) || (!$_COOKIE[$name])){
      echo 'no cookie named '.$name;
      return FALSE;
    }
    else {
      return $_COOKIE[$name];
    }
  }
}

?>
