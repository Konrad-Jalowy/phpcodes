<?php
class Arr{
    public static function take($array, $limit)
    {
        if ($limit < 0) {
            return array_slice($array, $limit, abs($limit));
        }

        return array_slice($array, 0, $limit);
    }

    public static function takeRight($array, $limit){
        return array_slice(array_reverse($array), 0, abs($limit));
    }

    public static function wrap($value)
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    public static function where($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    public static function whereNotNull($array)
    {
        return static::where($array, fn ($value) => ! is_null($value));
    }

    public static function whereScalar($array)
    {
        return static::where($array, fn ($value) => is_scalar($value));
    }

    public static function removeKey($array, $key){
        if(!array_key_exists($key, $array))
            return;

        return array_filter($array, function($k) use($key) {
            return $k !== $key;
        }, ARRAY_FILTER_USE_KEY);
        
    }

    public static function keys($array){
        return array_keys($array);
    }

    public static function values($array){
        return array_values($array);
    }

    public static function divide($array){
        return [array_keys($array), array_values($array)];
    }

    public static function flip($array){
        return array_flip($array);
    }

    public static function isAssoc(array $array)
    {
        return ! array_is_list($array);
    }

    public static function isList($array)
    {
        return array_is_list($array);
    }

    public static function firstKey($array){
        return array_is_list($array) ? 0 : array_key_first($array);
    }

    public static function lastKey($array){
        return array_is_list($array) ? count($array) - 1 : array_key_last($array);
    }

    public static function first($array){
        foreach($array as $element){
            return $element;
        }
    }

    public static function last($array){
        foreach(array_reverse($array) as $element){
            return $element;
        }
    }

    private static function quote($value){
        return '"'.$value.'"';
    }

    private static function openingTag($content){
        return "<$content>";
    }

    private static function closingTag($content){
        return "</$content>";
    }

    public static function toClasslist($array){
        $class_str = "class=";
        $class_str .= static::quote(implode(" ", $array));
        return $class_str;
    }

    public static function toAttributes($array){
        $attribute_string = "";
        foreach($array as $attribute => $value){
            $attribute_string .= $attribute;
            $attribute_string .= "=";
            $attribute_string .= static::quote($value);
            if($attribute === array_key_last($array))
                break;
            $attribute_string .= " ";
        }
        return $attribute_string;
    }

    public static function isRecordList($array){
        return array_reduce(array_keys($array), function($acc, $k){
            return $acc && is_int($k);
        }, true) && array_reduce($array, function($acc, $v){
            return $acc && is_array($v);
        }, true);
    }
    public static function isRecordAssoc($array){
        return !array_is_list($array) && array_reduce($array, function($acc, $v){
            return $acc && is_array($v);
        }, true);
    }

    public static function column($array, $col){
        if(static::isRecordList($array)){
            return array_column($array, $col);
        }
        if(static::isRecordAssoc($array)){
            return array_column(array_values($array), $col);
        }
    }

    public static function mapType($array, $type){
        return array_walk($array, function(&$value) use($type){
            $value = (object) $value;
        });
    }

    public static function toSqlCNames($array){
        return "(".implode(', ', $array).')';
    }

    public static function toSqlPlaceholders($array, $placeholder="?"){
        $array = array_map(fn()=> $placeholder, $array);
        return "(".implode(', ', $array).')';
    }

    public static function toPDOPlaceholders($array){
        $array = array_map(fn($el)=> ":$el", $array);
        return "(".implode(', ', $array).')';
    }

    public static function toSqlVals($array_assoc){
        return "(".implode(", ", array_values($array_assoc)).")";
    }

    public static function toSQLRaw($array){
        $keys = static::toSqlCNames(array_keys($array));
        $vals = static::toSqlVals($array);
        return [$keys, $vals];
    }

    public static function toSqlEscaped($array){
        $keys = static::toSqlCNames(array_keys($array));
        $placeholders = static::toSqlPlaceholders(array_keys($array));
        $real_vals = static::toSqlVals($array);
        return [$keys, $placeholders, $real_vals];
    }

    public static function toPDOEscaped($array){
        $keys = static::toSqlCNames(array_keys($array));
        $placeholders = static::toPDOPlaceholders(array_keys($array));
        $real_vals = static::toSqlVals($array);
        return [$keys, $placeholders, $real_vals];
    }

    public static function merge2($arr1, $arr2){
        return [...$arr1, ...$arr2];
    }

    public static function merge(){
        $merged = Arr::merge2(func_get_arg(0), func_get_arg(1));
        $num_args = func_num_args();

        if($num_args > 2){
            $cnt = 2;
            while($cnt < $num_args){
                $merged = static::merge2($merged, func_get_arg($cnt++));
            }
        }
        return $merged;
    }

    public static function exceptKeys($assoc, $excluded_fields) {
        return array_diff_key($assoc, array_flip($excluded_fields));
    }

    public static function withoutNumericKeys($arr){
        return array_filter($arr, function($k){
            return !is_numeric($k);
        }, ARRAY_FILTER_USE_KEY);
    }
}

