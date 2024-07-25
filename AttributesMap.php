<?php 

//actually old class ive written long time ago
//now iwouldve done it better i guess

interface AttributesMapInterface
{

    public function getAttribute($attr);
    public function setAttribute($attr, $val);
    public function removeAttribute($attr);
    public function hasAttribute($attr);
    public function output();
}

trait Wrap {
    public function wrap($text){
        return '"'.$text.'"';
    }
    public function attribute_string($attributes){
        $output = "";
        foreach($attributes as $key => $value){
            $output .= $key;
            $output .= "=";
            $output .= $this->wrap($value);
            if($key === array_key_last($attributes))
                break;
            $output .= " ";
         }
         return $output;
    }
}
class AttributesMap implements AttributesMapInterface {
    use Wrap;
    public $attrs = array();

    public function getAttribute($attr){
        if(array_key_exists($attr, $this->attrs))
            return $this->attrs[$attr];
        return null;
    }
    public function setAttribute($attr, $val){
        $this->attrs[$attr] = $val;
        return $this;
    }
    public function removeAttribute($attr){
        $this->attrs = array_filter($this->attrs, function($key) use($attr) {
            return $key !== $attr;
        }, ARRAY_FILTER_USE_KEY);
        return $this;
    }
    public function hasAttribute($attr){
        return array_key_exists($attr, $this->attrs);
    }
    public function output(){
        return $this->attribute_string($this->attrs);
    }

    public static function equal($a, $b){
        return $a->attrs === array_intersect_assoc($a->attrs, $b->attrs);
    }
}

/*
$attrs = new AttributesMap();
$attr2 = new AttributesMap();
$attrs->setAttribute("href", "#")->setAttribute("src", "script.js")->setAttribute("alt", "picture");
$attr2->setAttribute("href", "#")->setAttribute("src", "script.js")->setAttribute("alt", "picture");
var_dump(AttributesMap::equal($attrs, $attr2));
echo $attrs->output();
echo "<br>";
echo $attrs->getAttribute("href");
echo "<br>";
var_dump($attrs->hasAttribute("for"));
var_dump($attrs->getAttribute("for"));
$attrs->removeAttribute("href");
echo $attrs->output();
*/