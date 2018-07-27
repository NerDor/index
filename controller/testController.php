<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/20
 * Time: 9:49
 */

namespace controller;
use core\redis;
use plugin\address\controller\getController;
class testController extends Controller
{
    protected $auth = [
        'verify' => false
    ];
    public function index(){
        $da=new getController();
        $da->jdVF();
    }
    public function jsq(){
        $da=new redis();
        var_dump($da->get('jsq'));
    }
}