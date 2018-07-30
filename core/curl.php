<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/3
 * Time: 13:44
 */

namespace core;
class curl
{
    public $url;                //套接字
    public $post_data;          //套接字
    public $data;               //返回数据
    private $sec;               //套接字

    public function __construct($url)
    {
        $this->sec = curl_init();
        curl_setopt($this->sec, CURLOPT_URL, $url);
        curl_setopt($this->sec, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->sec, CURLOPT_HEADER, 0);
    }

    public function set($option, $data)
    {
        curl_setopt($this->sec, $option, $data);
    }

    public function setCookie($data)
    {
        $this->set(CURLOPT_COOKIE, $data);
    }

    public function get()
    {
        $this->set(CURLOPT_POST, 0);
        $this->data = curl_exec($this->sec);
        return $this->data;
    }

    public function post($data)
    {
        $this->set(CURLOPT_POST, 1);
        $this->set(CURLOPT_POSTFIELDS, $data);
        $this->data = curl_exec($this->sec);
        return $this->data;
    }

    public function getError()
    {
        return curl_errno($this->sec);
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        curl_close($this->sec);
    }

}