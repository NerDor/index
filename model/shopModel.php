<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/4
 * Time: 14:02
 */

namespace model;

use core\mysqlPdo;
use plugin\address\controller\getController;

class shopModel extends Model
{
    public function classify($parentid = 0)
    {
        $db = new mysqlPdo('ewei_shop_category');
        $db->get(['id', 'name', 'thumb', 'parentid', 'description', 'level'], ['enabled' => '1', 'parentid' => $parentid, 'uniacid' => 7]);
        if ($db->data == null) {
            return ['code' => 3012, 'message' => '商品分类不存在'];
        }
        return $db->data;
    }

    public function getGoods($id, $page = 1)
    {
        if ($page < 1) {
            return ['code' => 3014, 'message' => '商品分页不存在'];
        }
        $db = new mysqlPdo('ewei_shop_goods');
        $db->get(['id']);
        if ($db->data == null) {
            return ['code' => 0000, 'message' => '系统错误'];
        }
        $db = new mysqlPdo('ewei_shop_goods');
        $db->get(['deduct3', 'cannotrefund', 'labelname ', 'weight',
            'createtime', 'total', 'marketprice',
            'productsn', 'goodssn', 'unit',
            'thumb', 'title', 'type',
            'tcate', 'ccate', 'pcate',
            'id'], ['tcate' => $id, 'uniacid' => 7]);
        if ($db->data != null) {
            $return['count'] = ceil(count($db->data) / 50);
        }
        $db->data = null;
        $db->get(['deduct3', 'cannotrefund', 'labelname ', 'weight',
            'createtime', 'total', 'marketprice',
            'productsn', 'goodssn', 'unit',
            'thumb', 'title', 'type',
            'tcate', 'ccate', 'pcate',
            'id'], ['tcate' => $id, 'uniacid' => 7], 50, $page);
        if ($db->data == null) {
            $db->get(['deduct3', 'cannotrefund', 'labelname ', 'weight',
                'createtime', 'total', 'marketprice',
                'productsn', 'goodssn', 'unit',
                'thumb', 'title', 'type',
                'tcate', 'ccate', 'pcate',
                'id'], ['ccate' => $id, 'uniacid' => 7]);
            if ($db->data != null) {
                $return['count'] = ceil(count($db->data) / 50);
            }
            $db->data = null;
            $db->get(['deduct3', 'cannotrefund', 'labelname ', 'weight',
                'createtime', 'total', 'marketprice',
                'productsn', 'goodssn', 'unit',
                'thumb', 'title', 'type',
                'tcate', 'ccate', 'pcate',
                'id'], ['ccate' => $id, 'uniacid' => 7], 50, $page);
            if ($db->data == null) {
                $db->get(['deduct3', 'cannotrefund', 'labelname ', 'weight',
                    'createtime', 'total', 'marketprice',
                    'productsn', 'goodssn', 'unit',
                    'thumb', 'title', 'type',
                    'tcate', 'ccate', 'pcate',
                    'id'], ['pcate' => $id, 'uniacid' => 7]);
                if ($db->data != null) {
                    $return['count'] = ceil(count($db->data) / 50);
                }
                $db->data = null;
                $db->get(['deduct3', 'cannotrefund', 'labelname ', 'weight',
                    'createtime', 'total', 'marketprice',
                    'productsn', 'goodssn', 'unit',
                    'thumb', 'title', 'type',
                    'tcate', 'ccate', 'pcate',
                    'id'], ['pcate' => $id, 'uniacid' => 7], 50, $page);
            }
        }
        if ($db->data == null) {
            return ['code' => 3014, 'message' => '商品分页或商品类目不存在'];
        }
        $return['now_in'] = $page;
        $return['list'] = $db->data;
        return $return;
    }

    public function getGoodsContent($id = 1)
    {
        if ($id < 1) {
            return ['code' => 3015, 'message' => '商品不存在'];
        }
        $db = new mysqlPdo('ewei_shop_goods');
        $db->get(['deduct3', 'cannotrefund', 'labelname ', 'weight',
            'createtime', 'total', 'marketprice',
            'productsn', 'goodssn', 'unit',
            'thumb', 'title', 'type',
            'tcate', 'ccate', 'pcate',
            'content', 'id'], ['id' => $id, 'uniacid' => 7]);
        if ($db->data == null) {
            return ['code' => 3015, 'message' => '商品不存在'];
        }
        return $db->data;
    }

    public function addAddress($appid, $name, $phone, $province, $city, $area, $town, $address)
    {
        $db = new mysqlPdo('ewei_shop_member_address');
        $sql = [
            'uniacid' => 7,
            'openid' => 'API_USER' . $appid,
            'realname' => $name,
            'mobile' => $phone,
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'town' => $town,
            'address' => $address,
            'fxh_api' => $appid . '[' . time() . ']' . substr(md5(rand(5555, 9999)), 5, 10),
        ];
        $return = $db->insert($sql);
        if (!$return) {
            exit('创建地址失败');
        }
        $db->get(['id'], ['fxh_api' => $sql['fxh_api']]);
        return $db->data['0']['id'];
    }

    public function getCookie($appid)
    {
        $db = new mysqlPdo('ewei_shop_member');
        $db->get(['id', 'openid', 'mobile', 'pwd', 'salt'], ['openid' => 'API_USER' . $appid]);
        if (empty($db->data['0'])) {
            return ['code' => 3030, 'message' => '下单失败(用户信息错误)'];
        }
        $ar = [
            "id" => $db->data['0']['id'],
            "openid" => $db->data['0']['openid'],
            "mobile" => $db->data['0']['mobile'],
            "pwd" => $db->data['0']['pwd'],
            "salt" => $db->data['0']['salt'],
            "ewei_shopv2_member_hash" => md5($db->data['0']['pwd'].$db->data['0']['salt']),
        ];
        return base64_encode(json_encode($ar));
    }
}
