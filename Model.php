<?php
abstract class Model {
    
    public $id;
    private $data = array();
    protected static $conn;
    const OPERATORS = ['=', '>=', '>', '<=', '<', '<>'];

    public static function setConn($conn)
    {
        static::$conn = $conn;
    }

    public static function keysString(){
        return "(" . implode(", ", static::$keys) . ")";
    }

    public static function valuesString(){
        $values = array_map(fn($k) => ":$k", static::$keys);
        return "(" . implode(", ", $values) . ")";
    }

  

    public static function insert($data){
        $table = static::$tablename;
        $keys = static::keysString();
        $vals = static::valuesString();
        $query = "INSERT INTO {$table} {$keys} VALUES {$vals}";
        $stmt = static::$conn->prepare($query);   
        return $stmt->execute($data);
    }

    public static function all($type=PDO::FETCH_OBJ){
        $table = static::$tablename;
        $query = "SELECT * FROM $table";
        $stmt = static::$conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll($type);
    }

    public static function startsWith($col, $starts, $type=PDO::FETCH_OBJ){
        $table = static::$tablename;
        if(!in_array($col, static::$keys))
            return;
        $query = "SELECT * FROM $table WHERE {$col} LIKE '{$starts}%'";
        $stmt = static::$conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll($type);
    }

    public static function endsWith($col, $ends, $type=PDO::FETCH_OBJ){
        $table = static::$tablename;
        if(!in_array($col, static::$keys))
            return;
        $query = "SELECT * FROM $table WHERE {$col} LIKE '%{$ends}'";
        $stmt = static::$conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll($type);
    }

    public static function count(){
        $table = static::$tablename;
        $query = "SELECT COUNT(*) FROM {$table}";
        $q = static::$conn->query($query);
        return $q->fetch()[0]; 
    }

    public static function lastPage($perPage){
        $count = static::count();
        $lastPage = ceil($count / $perPage);
        return $lastPage;
    }

    public static function paginate(int $page, int $perPage = 5) {
        $table = static::$tablename;

        $page = max(1, $page);
        $lastPage = static::lastPage($perPage);
        $page = min($page, $lastPage);

        $stmt = static::$conn->prepare("SELECT * 
            FROM {$table} 
            ORDER BY `ID` ASC
            LIMIT :limit OFFSET :offset");

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);

        $stmt->execute();
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $entries;

    }

    public static function avg($col){
        $table = static::$tablename;
        if(in_array($col, static::$keys)){
            $query = "SELECT AVG({$col}) FROM {$table}";
            $q = static::$conn->query($query);
            return $q->fetch()[0]; 
        }
    }

    public static function countWhere($arr){

        [$cname, $operator, $value] = $arr;
        $table = static::$tablename;

        if(!in_array($cname, static::$keys))
            return;
        if(!in_array($operator, self::OPERATORS))
            return;

        $query = "SELECT COUNT(*) FROM {$table} WHERE {$cname} {$operator} {$value}";
        $q = self::$conn->query($query);
        return $q->fetch()[0];
    }

    public static function min($col){
        $table = static::$tablename;
        if(in_array($col, static::$keys)){
            $query = "SELECT MIN({$col}) FROM {$table}";
            $q = static::$conn->query($query);
            return $q->fetch()[0]; 
        }
    }

    public static function max($col){
        $table = static::$tablename;
        if(in_array($col, static::$keys)){
            $query = "SELECT MAX({$col}) FROM {$table}";
            $q = static::$conn->query($query);
            return $q->fetch()[0]; 
        }
    }

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function __set($name, $value) {
        
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if(array_key_exists($name, $this->data)) 
            return $this->data[$name];
    }

    public function save(){
        $data = $this->data;
        if(is_null($this->id)){
            $table = static::$tablename;
            $keys = static::keysString();
            $vals = static::valuesString();
            $query = "INSERT INTO {$table} {$keys} VALUES {$vals}";
            $stmt = static::$conn->prepare($query);
            $stmt->execute($data);
            $this->id = static::$conn->lastInsertId();   
        }
        else {
            $table = static::$tablename;
            $setSTR = "";
            foreach($data as $key => $value){
                $setSTR .= $key;
                $setSTR .= " = ";
                $setSTR .= '"'.$value.'"';
                if($key === array_key_last($data))
                    break;
                $setSTR .= ", ";
            }

            $query = "UPDATE {$table} SET {$setSTR} WHERE id = {$this->id}";
            $stmt = static::$conn->prepare($query);
            $stmt->execute();
            
        }
    }

    public static function find($id){

        $table = static::$tablename;
        $query = "SELECT * from $table WHERE id = {$id}";

        $q = static::$conn->query($query);
        $params = $q->fetch();

        if(!$params)
            return false;

        $model = new static($params['ID']);

        foreach($params as $key => $value){
            if($key === 'ID')
                continue;
            if(is_numeric($key))
                continue;
            $model->$key = $value;
        }

        return $model;
    }
        

}
/*
class PersonModel extends Model {

    protected static $tablename = 'people';
    protected static $keys = ['name', 'age'];
 
}
    */
