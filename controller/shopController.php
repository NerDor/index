<?php

namespace controller;

use model\shopModel;
use plugin\address\controller\getController;
use core\curl;
class shopController extends Controller
{
    protected $mustPass = [
        //添加公用必传参数
        'classify' => [
            ['value' => 'parentid', 'code' => 3011, 'message' => '请传入parentid'],
        ],
        'getGoods' => [
            ['value' => 'classid', 'code' => 3017, 'message' => '请传入类目classid'],
        ],
        'getGoodsContent' => [
            ['value' => 'goodsId', 'code' => 3016, 'message' => '请输入商品ID'],
        ],
        'submitOrder' => [
            ['value' => 'order', 'code' => 3022, 'message' => '请输入下单信息'],
            ['value' => 'province', 'code' => 3023, 'message' => '请输入省份'],
            ['value' => 'city', 'code' => 3024, 'message' => '请输入市'],
            ['value' => 'area', 'code' => 3025, 'message' => '请输入区'],
            ['value' => 'town', 'code' => 3026, 'message' => '请输入街道'],
            ['value' => 'address', 'code' => 3027, 'message' => '请输入详细地址'],
            ['value' => 'name', 'code' => 3028, 'message' => '请输入姓名'],
            ['value' => 'phone', 'code' => 3029, 'message' => '请输入电话'],
            ['value' => 'sn', 'code' => 3033, 'message' => '请输入商家订单号'],
        ]
    ];

    public function address()
    {
        $list = new shopModel();
        $parentid = $this->post_data['parentid'];
        $data = $list->classify($parentid);
        if (empty($data['code'])) {
            $this->showJson(1002, 'success', $data);
        }
        $this->showJson(3012, 'eroor', '商品分类不存在');
    }

    public function classify()
    {
        $list = new shopModel();
        $parentid = $this->post_data['parentid'];
        $data = $list->classify($parentid);
        if (empty($data['code'])) {
            $this->showJson(1002, 'success', $data);
        }
        $this->showJson(3012, 'eroor', '商品分类不存在');
    }

    public function getGoods()
    {
        $goods = new shopModel();
        if (empty($this->post_data['page'])) {
            $page = 1;
        } else {
            $page = $this->post_data['page'];
        }
        $data = $goods->getGoods($this->post_data['classid'], $page);
        if (empty($data['code'])) {
            $this->showJson(1003, 'success', $data);
        }
        $this->showJson($data['code'], 'eroor', $data['message']);
    }

    public function getGoodsContent()
    {
        $goods = new shopModel();
        $data = $goods->getGoodsContent($this->post_data['goodsId']);
        if (empty($data['code'])) {
            $this->showJson(1004, 'success', $data);
        }
        $this->showJson($data['code'], 'eroor', $data['message']);
    }

    public function submitOrder()
    {
        $verfy_address = new getController();
        $temp = $verfy_address->jdVF($this->post_data['province'], $this->post_data['city'], $this->post_data['area'], $this->post_data['town']);
        if (!empty($temp['code'])) {
            $this->showJson($temp['code'], 'eroor', $temp['message']);
        }
        unset($verfy_address);
        $temp=new shopModel();
        if(!$temp->cheekSn($this->post_data['sn'])){
            $this->showJson(3034,'error','商家订单号重复');
        }
        $address_id=$temp->addAddress($this->post_data['appid'],$this->post_data['name'],$this->post_data['phone'],$this->post_data['province'],$this->post_data['city'],$this->post_data['area'],$this->post_data['town'],$this->post_data['address']);
        $suborder=json_decode($this->post_data['order'],1);
        $post_data=[];
        if(empty($suborder)){
            $this->showJson(3031,'error','下单失败(订单信息错误)');
        }
        foreach($suborder as $key=>$value){
            if(empty($value['goodsid'])||empty($value['num'])){
                $this->showJson(3031,'error','下单失败(订单信息错误)');
            }
            $post_data['goods['.$key.'][goodsid]']=$value['goodsid'];
            $post_data['goods['.$key.'][total]']=$value['num'];
            $post_data['goods['.$key.'][type]']=1;
        }
        $post_data['addressid']=$address_id;
        $cookie=$temp->getCookie($this->post_data['appid']);
        if(!empty($cookie['code'])){
            $this->showJson($cookie['code'], 'eroor', $cookie['message']);
        }
        $curl=new curl('https://www.vipfxh.com/app/index.php?i=7&c=entry&m=ewei_shopv2&do=mobile&r=order.create.submit');
        $curl->setCookie('e69a___ewei_shopv2_member_session_7='.$cookie);
        $curl->set(CURLOPT_SSL_VERIFYPEER,0);
        $curl->set(CURLOPT_SSL_VERIFYHOST,0);
        $curl->set(CURLOPT_FOLLOWLOCATION,1);
        $return=$curl->post($post_data);
        $return=json_decode($return,1);
//        var_dump($return,$curl->getError());
        if($return['status']!=1){
            $this->showJson(3032,'eroor','下单失败(商品不合规)');
        }
        $orderid=$return['result']['orderid'];
        $temp=new shopModel();
        $orderSubmit=$temp->queryOrder($orderid);
//        var_dump($orderSubmit);
        $subid=$temp->setOrder($this->post_data['appid'],$this->post_data['order'],
            $this->post_data['province'],$this->post_data['city'],$this->post_data['area'],$this->post_data['town'],
            $this->post_data['address'],$this->post_data['name'],$this->post_data['phone'],
            $this->post_data['sn'],$orderid,$orderSubmit['price'],$orderSubmit['dispatchprice'],$orderSubmit['createtime']);
        if(!$subid){
            $this->showJson(3035,'eroor','订单保存失败');
        }
        $this->showJson(1005,'success',$subid);
    }
}