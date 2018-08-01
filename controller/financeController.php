<?php

namespace controller;

use model\financeModel;

class financeController extends Controller
{
    protected $mustPass = [
        //添加公用必传参数
    ];

    public function balance()
    {
        $model=new financeModel();
        $temp=$model->balance($this->post_data['appid']);
        if(!empty($temp['code'])){
            $this->showJson($temp['code'],'error',$temp['message']);
        }
        $this->showJson(1007,'success',$temp);
    }
}