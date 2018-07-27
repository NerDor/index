<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/20
 * Time: 12:40
 */

namespace plugin\address\model;

use core\redis;

class getModel
{
    public function init()
    {
        $re = new redis();
        $d = file_get_contents("plugin/address/data/jd.json");
        $d = json_decode($d, 1);
        $bt = [];
        $a = 0;
        foreach ($d as $k => $v) {
            foreach ($v as $kk => $vv) {
                $bt[$vv] = $kk;
            }
        }
        foreach ($bt as $k => $v) {
            $a += $re->set_hash('jd_address', $k, $v);
        }
        echo 'OK' . $a;
    }

    public function jd($name)
    {
        $re = new redis();
        return $re->get_hash('jd_address', $name);
    }

    public function jdVF($fw, $ft, $fs, $fd = null)
    {
        $d = file_get_contents("plugin/address/data/jd.json");
        $d = json_decode($d, 1);
        $dw = $this->jd($fw);
        $dt = $this->jd($ft);
        $ds = $this->jd($fs);
        $dd=null;
        if ($fd != null) {
            $dd = $this->jd($fd);
        }
        $ar = $d[$dw];
        if (empty($ar[$dt])) {
            return ['code' => 3021, 'message' => '地址校验失败(请验证地址市)'];
        }
        $ar = $d[$dt];
        if (empty($ar[$ds])) {
            return ['code' => 3021, 'message' => '地址校验失败(请验证地址区)'];
        }
        if (empty($d[$ds])) {
            return true;
        }
        $ar = $d[$ds];
        if (empty($dd)&&empty($ar[$dd])) {
            return ['code' => 3021, 'message' => '地址校验失败(请验证地址街道)'];
        }
        return true;
    }
}