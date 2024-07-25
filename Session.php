<?php 

class Session
{
 
  public static function start()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  
  public static function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  
  public static function get($key, $default = null)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }

  
  public static function has($key)
  {
    return isset($_SESSION[$key]);
  }

  
  public static function clear($key)
  {
    if (isset($_SESSION[$key])) {
      unset($_SESSION[$key]);
    }
  }

 
  public static function clearAll()
  {
    session_unset();
    session_destroy();
  }

  
  public static function setFlashMessage($key, $message)
  {
    self::set('flash_' . $key, $message);
  }

  
  public static function getFlashMessage($key, $default = null)
  {
    $message = self::get('flash_' . $key, $default);
    self::clear('flash_' . $key);
    return $message;
  }

  public static function clearFlashMessages(){
    foreach ($_SESSION as $key => $value){
        if(str_starts_with($key, "flash_")){
            static::clear($key);
        }
    }
  }

  public static function sessionsCount(){
    $session_path = session_save_path();

    $handle = opendir($session_path);
    $sessions = 0;
        while (($file = readdir($handle)) != FALSE) {
           
            if(in_array($file, ['.', '..']))
                continue;
            if(!preg_match("/^sess/", $file))
                continue;
            $sessions++;
        }
        return $sessions;
  }


  public static function sessions5Minutes(){
    
    $session_path = session_save_path();
    $handle = opendir($session_path);
    $sessions = 0;
    clearstatcache();
    static::set("_cache", (string)time());

        while (($file = readdir($handle)) != FALSE) {
           
            if(in_array($file, ['.', '..']))
                continue;
            if(!preg_match("/^sess/", $file))
                continue;
           
            $file_path = "{$session_path}/{$file}";
            $last_access = date("Y-m-d H:i:s", fileatime($file_path));
            $now = date("Y-m-d H:i:s", strtotime("-5 minutes"));
            
            if($now <= $last_access)
                $sessions++;
            
        }
    return $sessions;
}

public static function onEveryRequestMiddleware(){
    static::start();
    static::set("_cache", (string)time());
}
}