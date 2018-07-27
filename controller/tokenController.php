<?php

namespace controller;

use core\mysqlPdo;
use model\tokenModel;

/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/4
 * Time: 10:10
 */
class tokenController extends Controller
{
    protected $auth = [
        'verify' => false
    ];

    public function getToken()
    {
        if (empty($this->post_data['appid']) || empty($this->post_data['appsec'])) {
            $this->showJson(3001, 'error', '需要appid以及appsec!');
        }
        $access_token = new tokenModel();
        $temp = $access_token->getToken($this->post_data['appid'], $this->post_data['appsec']);
        if (empty($temp['code'])) {
            $data = [
                'access_token' => $temp,
                'expires_in' => '7200'
            ];
            $this->showJson(1001, 'success', $data);
        } else {
            $this->showJson($temp['code'], 'error', $temp['message']);
        }
    }


}