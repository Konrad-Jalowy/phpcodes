<?php

class ConfigINI {

    public static $iniPath = __DIR__ . "/config.ini";

    public static function getPDOCredentials(){
        $ini_array = parse_ini_file(static::$iniPath, true);
        return $ini_array['DB_CREDENTIALS'];
    }
}