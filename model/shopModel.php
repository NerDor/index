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
            return ['code' => 3015, 'message' => '商品不存在.'];
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

    public function addAddress($name, $phone, $province, $city, $area, $town, $address)
    {
        $vrify = new getController();
        if ($vrify->jd($province)) {
            return ['code' => 3017, 'message' => '省份错误'];
        } else if ($vrify->jd($city)) {
            return ['code' => 3018, 'message' => '市错误'];
        } else if ($vrify->jd($area)) {
            return ['code' => 3019, 'message' => '区域错误'];
        } else if ($vrify->jd($town)) {
            return ['code' => 3020, 'message' => '街道错误'];
        }
        unset($vrify);
        $db=new mysqlPdo('ewei_shop_member_address');
        $sql=[

        ];
        $return=$db->insert($sql) or exit('创建地址失败');
        var_dump($return);
    }
}
