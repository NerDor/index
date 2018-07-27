<?php
use core\redis;
if (!OPEN) {
    exit('接口关闭');
}
require_once("core/autoload.php");
if (empty($_GET['c']) || empty($_GET['a'])) {
    exit('非法访问');
}
if ($_GET['c'] == 'plugin') {
    $fun = explode('.', $_GET['a']);
    $a = '\plugin\\' . $fun[0] . '\controller\\' . $fun[1] . 'Controller';
    $func=$fun[2];
} else {
    $controller = $_GET['c'] . 'Controller';
    $func = $_GET['a'];
    $a = '\controller\\' . $controller;
}
global $config;
$config = include_once("config/db.php");
$temp = new $a;
if (method_exists($temp, $func)) {
    $temp->$func();
    $a=new redis();
    $d=$a->get('jsq');
    if($d){
        $a->set('jsq',$d+1);
    }else{
        $a->set('jsq',1);
    }
} else {
    exit('非法访问');
}
