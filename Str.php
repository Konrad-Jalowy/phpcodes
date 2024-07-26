<?php 
    class Str {
        public static function formatBytes($bytes, $precision = 2) {
            $kilobyte = 1024;
            $megabyte = $kilobyte * 1024;
            $gigabyte = $megabyte * 1024;
            
            if ($bytes < $kilobyte) {
                return $bytes . ' B';
            } elseif ($bytes < $megabyte) {
                return round($bytes / $kilobyte, $precision) . ' KB';
            } elseif ($bytes < $gigabyte) {
                return round($bytes / $megabyte, $precision) . ' MB';
            } else {
                return round($bytes / $gigabyte, $precision) . ' GB';
            }
        }

        public static function isRegex($pattern) {
            if(@preg_match($pattern, '') === false)
                return false;
            return true;
        }
        public static function isMatch($pattern, $string){
            if(!static::isRegex($pattern))
                return false;
            return (bool)preg_match($pattern, $string);
        }
    }