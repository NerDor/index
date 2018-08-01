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
    //订单表相关定义
    const order_status_pending_payment = 100;       //待付款
    const order_status_pending_delivery = 200;      //待发货
    const order_status_pending_received = 300;     //待收货
    const order_status_ransaction_closure = 400;   //交易关闭
    const order_status_refunded = 900;              //已退款
    const order_refound_unreturned_goods = 100;       //未退货
    const order_refound_application = 200;      //申请退货
    const order_refound_through = 300;     //申请通过,退货中
    const order_refound_finnish = 400;   //退款成功
    const order_refound_refuse = 900;              //拒绝申请

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
            "ewei_shopv2_member_hash" => md5($db->data['0']['pwd'] . $db->data['0']['salt']),
        ];
        return base64_encode(json_encode($ar));
    }
    public function queryOrder($orderId){
        $db=new mysqlPdo('ewei_shop_order');
        $db->get(['id','openid','ordersn','price','dispatchprice','createtime'],['id'=>$orderId]);
        return $db->data['0'];
    }
    public function cheekSn($sn){
        $db=new mysqlPdo('fxhapi_order');
        $db->get(['sn'],['sn'=>$sn]);
        if(empty($db->data['0'])){
            return true;
        }else{
            return false;
        }
    }
    public function setOrder($appid, $order, $province, $city, $area, $town, $address, $name, $phone, $sn, $orderid, $total, $dispatchprice,$time)
    {
        $db = new mysqlPdo('fxhapi_order');
        $return=$db->insert([
            'appid' => $appid,
            'name' => $name,
            'phone' => $phone,
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'town' => $town,
            'address' => base64_encode($address),
            'order_new' => base64_encode($order),
            'orderid' => $orderid,
            'total' => $total,
            'dispatchprice' => $dispatchprice,
            'sn' => $sn,
            'refound' => self::order_refound_unreturned_goods,
            'submit_time' => $time,
            'status' => self::order_status_pending_payment
        ]);
        if($return!=1){
            return false;
        }
        return [
            'orderid'=>$orderid,
            'total'=>$total,
            'dispatchprice'=>$dispatchprice,
            'sn'=>$sn,
            'order'=>$order,
        ];
    }
    public function selectOrder($appid,$orderid,$sn){
        $db=new mysqlPdo('fxhapi_order');
        $db->get(['id','appid','orderid','sn','submit_time','total','status'],['orderid'=>$orderid,'sn'=>$sn],1);
        if(empty($db->data['0'])){
            return ['code' => 3036, 'message' => '订单不存在.'];
        }else if((time()-7200)>$db->data['0']['submit_time']){
            return ['code' => 3037, 'message' => '订单已过期,请重新提交.'];
        }else if($db->data['0']['status']!=self::order_status_pending_payment){
            return ['code' => 3041, 'message' => '订单已付款,请勿重复提交'];
        }
        $balance=new mysqlPdo('fxhapi_balance');
        $balance->get(['id','appid','money','spent_money'],['appid'=>$appid],1);
        $balance->data['0']['money']-=$db->data['0']['total'];
        $balance->data['0']['spent_money']+=$db->data['0']['total'];

        if(!$balance->data['0']['money']>0){
            return ['code' => 3038, 'message' => '余额不足,请充值'];
        }
        if($balance->save()<1){
            return ['code' => 3039, 'message' => '订单支付失败,请联系技术人员'];
        }
        $dba=new mysqlPdo('ewei_shop_order');
        $dba->get(['id','price','paytype','status','paytime','isparent',],['id'=>$db->data['0']['orderid']],1);
        if($dba->data['0']['isparent']){
            $dbc=new mysqlPdo('ewei_shop_order');
            $dbc->get(['id','paytype','status','paytime','isparent',],['parentid'=>$db->data['0']['orderid']]);
            $new=[];
            foreach ($dbc->data as $key=>$value){
                $value['status']='1';
                $value['paytype']='28';
                $value['paytime']=$db->data['0']['submit_time'];
                $new[]=$value;
            }
            $dbc->data=$new;
            $dbc->save() or exit("系统错误(保存子表数据失败)");
        }
        $dba->data['0']['status']='1';
        $dba->data['0']['paytype']='28';
        $dba->data['0']['paytime']=$db->data['0']['submit_time'];
        $dba->save() or exit("系统错误(保存分表数据失败)");
        $db->data['0']['status']=self::order_status_pending_delivery;
        $db->save() or exit("系统错误(保存主表数据失败)");
        return [
            'orderid'=>$db->data['0']['orderid'],
            'total'=>$db->data['0']['total'],
            'sn'=>$db->data['0']['sn'],
        ];
    }
}
