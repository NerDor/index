<?php

namespace controller;

use model\shopModel;

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
            ['value' => 'oreder', 'code' => 3022, 'message' => '请输入下单信息'],
            ['value' => 'province', 'code' => 3023, 'message' => '请输入省份'],
            ['value' => 'city', 'code' => 3024, 'message' => '请输入市'],
            ['value' => 'area', 'code' => 3025, 'message' => '请输入区'],
            ['value' => 'town', 'code' => 3026, 'message' => '请输入街道'],
            ['value' => 'address', 'code' => 3027, 'message' => '请输入详细地址'],
            ['value' => 'name', 'code' => 3028, 'message' => '请输入姓名'],
            ['value' => 'phone', 'code' => 3029, 'message' => '请输入电话'],
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
        $goods = new shopModel();
        $data = $goods->getGoodsContent($this->post_data['goodsId']);
        if (empty($data['code'])) {
            $this->showJson(1004, 'success', $data);
        }
        $this->showJson($data['code'], 'eroor', $data['message']);
    }
}