<?php
function __autoload($name)
{
    $name=str_replace('\\','/',$name);
    if (file_exists($name . '.php')) {
        require_once($name . '.php');
        return;
    }else{
        exit('非法访问3');
    }
}