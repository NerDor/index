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
    public function jdVF(){
        $a=new getModel();
        if(empty($this->post_data['d'])){
            $this->post_data['d']=null;
        }
        $c=$a->jdVF($this->post_data['a'],$this->post_data['b'],$this->post_data['c'],$this->post_data['d']);
        var_dump($c);
    }
}
?>

