<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/4
 * Time: 14:02
 */

namespace model;

use core\mysqlPdo;
class tokenModel extends Model
{
    public function getToken($appid, $appsec)
    {
        $db = new mysqlPdo('fxhapi_user');
        $db_sql = [
            'appid' => $appid,
            'appsec' => $appsec
        ];
        $login = $db->get(null,$db_sql);
        if (!$login) {
            return ['code' => 3002, 'message' => 'appid或appsec不正确!'];
        } else {
            $access_db = new mysqlPdo('fxhapi_access_token');
            $access_db->get(null,['pid' => $appid]);
            $access_token = strtoupper(md5(md5(rand(100000, 999999)) . md5(rand(100000, 999999))));
            $expiry_time = time() + 7500;
            if ($access_db->byte) {
                $access_db->data[0]['access_token'] = $access_token;
                $access_db->data[0]['expiry_time'] = $expiry_time;
                $access_db->save() or exit('刷新access token失败');
            } else {
                $access_db = new mysqlPdo('fxhapi_access_token');
                $access_db->insert(['pid' => $appid, 'access_token' => $access_token, 'expiry_time' => $expiry_time]) or exit("无法生成access token!");
            }
        }
        return $access_token;
    }

    public function verify_token($pid)
    {
        $db = new mysqlPdo('fxhapi_access_token');
        $db->get(null,['pid' => $pid]);
        if ($db->byte) {
            if (time() > $db->data[0]['expiry_time']) {
                return ['code'=>3006,'message'=>'access token过期'];
            }else{
                return $db->data[0]['access_token'];
            }
        }
        return ['code'=>3007,'message'=>'ID不存在或已过期'];
    }
}
