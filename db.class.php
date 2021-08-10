<?php
/**
 * https://github.com/runnull/phpdb
 * @version 1.0.0
 * 2021.8.10
 */
class Db 
{
    private $dbtype;//数据库类型
    private $dbhost;//数据库地址
    private $dbuser;//数据库用户名
    private $dbname;//数据库名字
    private $dbpwd;//密码
    private $dsn;
    private $prefix='pre_';//数据库前缀
    private $long=false;//是否需要长连接
    private $table;
    static private $db;
    //sql语句拼装
    private $where;
    private $sql=array(
        'where'=>'',
        'field'=>"`*` FROM ",
        'limit'=>'',
        'order'=>'',
    );
    //构造函数，设置基本信息
    function __construct($config,$table='') {
        $this->dbtype = $config['dbtype'];
        $this->dbhost = $config['dbhost'];
        $this->dbuser = $config['dbuser'];
        $this->dbname = $config['dbname'];
        $this->dsn = $this->dbtype.":host=" . $this->dbhost.";dbname=" . $this->dbname;
        $this->dbpwd = $config['dbpwd'];
        if(isset($config['pre'])){
            $this->prefix = $config['pre'];
        }
        if(isset($config['long'])){
            $this->long = $config['long'];
        }
        //设置需要操作的表
        $this->table = $table;
        //链接数据库
        $this->contectSQL();
    }
    private function contectSQL(){
        //开始连接数据库
        try{
            if($this->long){
                $dbh=new PDO($this->dsn,$this->dbuser,$this->dbpwd,array(PDO::ATTR_PERSISTENT=>true));
            }else{
                $dbh=new PDO($this->dsn,$this->dbuser,$this->dbpwd);
            }
            
        } catch (PDOException $ex) {
            die("ERROR!:" . $ex->getMessage()."<br>");
        }
        self::$db = $dbh;
    }

    //执行原生SQL语句 可配合quote一起使用
    public function query($query)
    {
        try {
            return self::$db->exec($query);
        } catch (PDOException $ex) {
            die("ERROR!:" . $ex->getMessage()."<br>");
        }
    }

    //为SQL语句中的字符串添加引号或者转义特殊字符串
    public function quote($quote)
    {
        try {
            return self::$db->quote($quote);
        } catch (PDOException $ex) {
            die("ERROR!:" . $ex->getMessage()."<br>");
        } 
    }

    //返回pdo对象
    public function pdo()
    {
        return self::$db;
    }

    //设置需要操作的表
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }
    
    //where条件
    function where($arr){
        $sql = '';
        foreach ($arr as $key=>$val){
            $sql .= " WHERE `" . $key . "`=:" . $key;
        }
        $this->sql['where'] = $sql;
        $this->where = $arr;
        return $this;
    }
    
    //order排序
    function order($str){
        $sql="ORDER BY " . $str;
        $this->sql['order'] = $sql;
        return $this;
    }

    //limit条件
    function limit($str){
        $sql=" LIMIT " . $str;
        $this->sql['limit'] = $sql;
        return $this;
    }
    
    //查询字段内容
    public function field($param = '*'){
        $temp = '';
        if (is_array($param)) {

            $count = count($param);
            $i = 1;
            foreach($param as $val){
                if($i < $count){
                   $temp .= "`" . $val . "`,"; 
                }else{
                    $temp .= "`" . $val . "`";
                }
                $i++;
            }

        }else {
            $temp .= "`" . $param . "`";
        }

        $sql = $temp . " FROM ";
        $this->sql['field'] = $sql;
        return $this;
    }

    //查询数据,查询多条数据
    function select(){
        $sql="SELECT " . $this->sql['field'] . "`" . $this->prefix . $this->table."` "  . $this->sql['where']." " . $this->sql['order']." " . $this->sql['limit'];
        foreach ($this->where as $key=>$val){
            $array[$key] = $val;
        }
        return $this->doSql($sql, $array,2);
    }
    
    //查询数据，查询单条数据
    function find(){
        $sql="SELECT " . $this->sql['field']."`" . $this->prefix . $this->table."` "  . $this->sql['where']." " . $this->sql['order']." " . $this->sql['limit'];
        foreach ($this->where as $key=>$val){
            $array[$key] = $val;
        }
        return $this->doSql($sql, $array,1);
    }
    
    //更新数据
    function update($array){
        $sql = $this->updateSql($array);
        foreach ($this->where as $key=>$val){
            $array[$key] = $val;
        }
        return $this->doSql($sql, $array);
    }
    
    function updateSql($array){
        $field='';
        $count=count($array);
        $i=1;
        foreach($array as $key=>$val){
            if($i<$count){
                $field.="`" . $key . "`=:" . $key.","; 
            }else{
                $field.="`" . $key . "`=:" . $key; 
            }
            $i++;
        }
        $sql="UPDATE `" . $this->prefix . $this->table."` SET " . $field." " . $this->sql['where'];
        return $sql;
    }
    
    //删除数据
    function delete(){
        $sql="DELETE FROM `" . $this->prefix . $this->table."` " . $this->sql['where'];
        $array = array();
        foreach ($this->where as $val=>$key){
            $array[$val] = $key;
        }
        return $this->doSql($sql, $array);
    }

    //数据插入方法，返回最终插入ID；
    function insert($array){
        $sql = $this->insertSql($array);
        return $this->doSql($sql, $array);
    }

    private function insertSql($array){
        $field = '';
        $value = '';
        $i = 1;
        $count = count($array);
        foreach($array as $key=>$val){
            if($i<$count){
                $field.="`" . $key."`,";
                $value.=":" . $key.",";
            }else{
                $field.="`" . $key."`";
                $value.=":" . $key;
            }
            $i++;
        }
        $sql="INSERT INTO `" . $this->prefix . $this->table."` (" . $field.") VALUES (" . $value.");";
        return $sql;
    }
    
    private function doSql($sql,$data,$echo=0){
        $stmt = self::$db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        foreach($data as $key=>$val){
            $stmt->bindParam(':' . $key, $data[$key]);
        }
        if(!$stmt->execute()){
            echo $stmt->debugDumpParams().'<br>';
            $errorinfo = $stmt->errorInfo();
            die('ERROR:' . $errorinfo[2].'<br>');
        }else{
           // print_r($stmt->fetchAll());
           switch ($echo){
               case 0:
                    return $stmt->rowCount(); 
                    break;
               case 1:
                   return $stmt->fetch(PDO::FETCH_ASSOC);
                   break;
               case 2:
                   return $stmt->fetchAll(PDO::FETCH_ASSOC);
                   break;
           }
           
        }
        
    }

    function __destruct() {
        if(!$this->long){
            self::$db = null;
        }  
    }
}