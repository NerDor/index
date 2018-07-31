<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/3
 * Time: 13:41
 */

namespace controller;

use model\tokenModel;
class Controller
{
    protected $auth = [
        //是否开启token验证
        'verify' => true
    ];
    //自动验证必传参数
    protected $mustPassBase = [
        'timestamp' => ['code' => 3004, 'message' => '请传入timestamp!'],
        'appid' => ['code' => 3005, 'message' => '请传入appid!'],
        'sign' => ['code' => 3006, 'message' => '签名未传入!'],

    ];
    protected $mustPass = [];
    protected $post_data = null;

    public function __construct()
    {
        $func = $_GET['a'];
        if (!empty($this->mustPass[$func])) {
            foreach ($this->mustPass[$func] as $k => $v) {
                $this->mustPassBase[$this->mustPass[$func][$k]['value']] = [
                    'code' => $this->mustPass[$func][$k]['code'],
                    'message' => $this->mustPass[$func][$k]['message']
                ];
            }
        }
        $this->post_data = $this->sqlCheck($_POST);
        if (!$this->auth['verify']) {
            return;
        } else {
            foreach ($this->mustPassBase as $k => $v) {
                if (!isset($this->post_data[$k])) {
                    $this->showJson($v['code'], 'error', $v['message']);
                }
            }
            $auth = new tokenModel();
            $access_token = $auth->verify_token($this->post_data['appid']);
            if (!empty($access_token['code'])) {
                $this->showJson($access_token['code'], 'error', $access_token['message']);
            }
            $time = time();
            $maxtime = $time + 300;
            $mintime = $time - 300;
            if (!($this->post_data['timestamp'] < $maxtime && $this->post_data['timestamp'] > $mintime)) {
                $this->showJson(3008, 'error', '时间戳不正确');
            }
            $sign = $this->post_data['sign'];
            unset($this->post_data['sign']);
            $str = null;
            ksort($this->post_data);
            $this->post_data['access_token'] = $access_token;
            $temp = count($this->post_data);
            foreach ($this->post_data as $k => $v) {
                if ($temp > 1) {
                    $str .= $k . '=' . $v . '&';
                    $temp--;
                    continue;
                }
                $str .= $k . '=' . $v;
            }
            if ($sign != md5($str)) {
                $this->showJson(3010, 'error', '签名错误');
            }
            return;
        }

    }

    public function showJson($num, $str, $data = null)
    {
        $echo = [
            "code" => $num,
            "type" => $str,
            "data" => $data
        ];
        if ($data == null) {
            unset($echo['data']);
        }
        echo json_encode($echo, true);
        exit;
    }

    public function sqlCheck($paramater)
    {
//        检测sql注入
        $arr = array();
        foreach ($paramater as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $u) {
                    $arr[$k][] = $u;
                }
            } else {
                $arr[$k] = sprintf("%s", preg_replace('/\b(=|<|>|and|or|;|where|from|not|HAVING|select)\b/im', '', $v));
            }
        }
        return $arr;
    }
}
