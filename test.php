<?php
include_once 'db.class.php';

$config=[
    'dbtype'=>'mysql',
    'dbhost'=>'localhost',
    'dbname'=>'test',
    'dbuser'=>'root',
    'dbpwd' =>'',
    'pre'   =>'',
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