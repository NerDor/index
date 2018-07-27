<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/3
 * Time: 13:44
 */

namespace core;
class redis
{
    private $redis;                //套接字

    public function __construct()
    {
        $config = [
            'redis_ip' => '127.0.0.1',           //设置IP
            'redis_port' => '6379',      //设置端口
        ];
        $this->redis = new \Redis();
        $this->redis->connect($config['redis_ip'], $config['redis_port']);
        if ($this->redis->ping() != '+PONG') {
            exit('系统错误“redis”');
        }
    }

    public function set($name, $data)
    {
        return $this->redis->set($name,$data);
    }

    public function get($name)
    {
        return $this->redis->get($name);
    }

    public function set_hash($table, $name, $data, $time=null)
    {
        if (empty($time)) {
            return $this->redis->hSet($table, $name, $data);
        }
        $indata = [
            'data' => $data,
            'time' => $time
        ];
        return $this->redis->hSet($table, $name, json_encode($indata, 1));
    }

    public function get_hash($table, $name)
    {
        $data = $this->redis->hGet($table, $name);
        $json = json_decode($data, 1);
        if (is_array($json)&&count($json)>0) {
            if ($json) {
                if ($json['time'] < time()) {
                    $this->redis->hDel($table, $name);
                    return false;
                }
                return $json['data'];
            }
        }else{
            return $data;
        }
    }


}