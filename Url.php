<?php
class Url {
    public static function redirectTo($path)
    {
        if(static::isURL($path)){
            header("Location: {$path}");
            http_response_code(302);
        }
    exit;
    }

    public static function isURL($str){
        return (bool) filter_var($str, FILTER_VALIDATE_URL);
    }

    public static function requestUri(){
        return parse_url($_SERVER['REQUEST_URI']);
    }

    public static function requestPath(){
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public static function requestQuery(){
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    }

    
    public static function hasQuery(){
        if(!func_get_args())
            return !empty(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY));
        $key = func_get_arg(0);
        $has_key = isset($_GET[$key]) && !empty($_GET[$key]);
        return static::hasQuery() && $has_key;
    }

    public static function build_query($array){
        return http_build_query($array);
    }


}