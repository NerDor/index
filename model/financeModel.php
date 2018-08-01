<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/4
 * Time: 14:02
 */

namespace model;

use core\mysqlPdo;

class financeModel extends Model
{
    public function balance($appid)
    {
        $db = new mysqlPdo('fxhapi_balance');
        $db->get(['id', 'appid', 'money', 'spent_money'], ['appid' => $appid], 1);
        if (empty($db->data['0'])) {
            return ['code' => 3042, 'message' => '余额查询失败'];
        }
        return [
            'appid' => $db->data['0']['appid'],
            'balance' => $db->data['0']['money'],
            'spent' => $db->data['0']['spent_money'],
        ];
    }
}