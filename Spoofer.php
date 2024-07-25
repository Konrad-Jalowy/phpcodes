<?php 
class Spoofer {
    public static function isMethod($method){
        if($method === 'GET' || $method === 'POST'){
            return $method === $_SERVER['REQUEST_METHOD'];
        }
        elseif($_SERVER['REQUEST_METHOD'] !== 'POST'){
            return false;
        } elseif(!isset($_POST["_method"])) {
            return false;
        } else {
            return $method === $_POST["_method"];
        }

    }

    public static function method($method){
        echo '<input type="hidden" id="_method" name="_method" value="' . $method . '"/>';
    }
}