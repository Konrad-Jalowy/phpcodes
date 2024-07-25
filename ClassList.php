<?php 
class ClassList {
    public $classlist = array();

    public function add($item){
        $this->classlist[] = $item;
        return $this;
    }
    public function remove($item){
        $this->classlist = array_filter($this->classlist, function($el) use ($item) {
            return $el !== $item;
        });
        return $this;
    }

    public function has($item){
        return in_array($item, $this->classlist);
    }

    public function toggle($item){
        if($this->has($item)){
            $this->remove($item);
        } else {
            $this->add($item);
        }
        return $this;
    }

    public function output(){
        return '"' . implode(" ", $this->classlist) . '"';
    }

    public function empty(){
        return count($this->classlist) === 0;
    }
}

/*
$cls = new ClassList();
$cls->add("btn");
$cls->add("btn-danger");
$cls->toggle("btn");
$cls->toggle("hide");
$cls->add("d-flex");
echo $cls->output();
var_dump($cls->empty());
*/