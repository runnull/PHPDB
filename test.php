<?php
//加载核心类文件
include_once 'db.class.php';

$config=[
    'dbtype'=>'mysql',
    'dbhost'=>'localhost',
    'dbname'=>'test',
    'dbuser'=>'root',
    'dbpwd' =>'',
    'pre'   =>'',//数据库表前缀
    'long'  =>false
];
$db=new Db($config);

// $result = $db->table('test')->field(['time','vaule'])->where(['name'=>'testname'])->select();

// $result = $db->table('test')->field(['time','vaule'])->where(['name'=>'testname'])->find();

// $result = $db->table('test')->insert(['name'=>'testname2','vaule'=>'testvaule2','time'=>time()]);

// $result = $db->table('test')->where(['id'=>'12'])->update(['name'=>'testname3','vaule'=>'testvaule3','time'=>time()]);

// $result = $db->table('test')->where(['id'=>'13'])->delete();

// $result = $db->table('test')->field(['time','vaule'])->where(['name'=>'testname'])->order('id ASC')->select();

// $result = $db->table('test')->field(['time','vaule'])->where(['name'=>'testname'])->limit('3,6')->select();

// $result = $db->query("INSERT INTO test (name, vaule)
// VALUES ('testname3', 'testvaule3');");

$result = $db->quote('GitHub@runnull');

var_dump($result);

//----------------------------------------这里是一个分割线----------------------------------------

//不使用table指定表名的方法 当然也可以在使用table更换表进行操作，建议使用上面方法

//具体使用与上方相同=.=不过少了个table方法

// $d=new Db($config,'test');

// $result = $d->field(['time','vaule'])->where(['name'=>'testname'])->select();
// $result = $d->insert(['name'=>'testname2','vaule'=>'testvaule2','time'=>time()]);
// $result = $d->where(['id'=>'12'])->update(['name'=>'testname3','vaule'=>'testvaule3','time'=>time()]);
// $result = $d->where(['id'=>'13'])->delete();

// var_dump($result);