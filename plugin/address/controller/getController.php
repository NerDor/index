<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/20
 * Time: 10:04
 */

namespace plugin\address\controller;
use controller\Controller;
use plugin\address\model\getModel;
class getController extends Controller
{
    protected $auth = [
        //是否开启token验证
        'verify' => false
    ];

    public function ta()
    {
        $a=new getModel();
        $a->init();
    }
    public function jd(){
        $a=new getModel();
        $name=$this->post_data['name'];
        var_dump($a->jd($name));
    }
    public function jdVF($province, $city, $area, $town=null){
        $verfy=new getModel();
        return $verfy->jdVF($province, $city, $area, $town);
    }
}

