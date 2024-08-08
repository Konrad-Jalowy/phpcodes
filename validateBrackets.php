<?php

function validateBrackets($str){

function matchBrackets($chr){
    return match ($chr) {
        '(' => ')',
        '[' => ']',
        '{' => '}',
    };
}

$stack = [];
$length = strlen($str);

for($i = 0; $i < $length; $i++){

    $char = $str[$i];

    if(in_array($char, ["(", "[", "{"]))
        array_push($stack, $char);
    elseif(in_array($char, [")", "]", "}"])){

        if(count($stack) === 0)
            return false;

        $lastOne = array_pop($stack);
        $needed = matchBrackets($lastOne);

        if($needed !== $char)
            return false;
    }
}

return count($stack) === 0;

}