<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/20
 * Time: 9:49
 */

namespace controller;
use core\redis;
use model\shopModel;
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
    public function test(){
        $a=new shopModel();
        $a->addAddress('111','测试姓名','18126206030','黑龙江','哈尔滨市','群力区','城区','测试详细地址');
    }
}