<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/3
 * Time: 13:44
 */

namespace core;
class mysqlPdo
{
    private $db;                //套接字
    public $data = null;      //数据
    public $table;          //操作的表
    public $byte = false;   //判断是否取出数据

    public function __construct($table = null)
    {
        global $config;
        $this->table = $config['table_prefix'] . $table;
        $this->db = new \PDO($config['dsn'], $config['username'], $config['password']);
    }

    public function get($condition=null,$array = null,$list=null,$page=null)
    {
        if (empty($condition)) {
            $sql = "select * from `$this->table`";
        } else {
            $str = null;
            $j = count($condition);
            for ($i = count($condition) - 1; $i > -1; $i--) {
                if ($i != 0) {
                    $str .= $condition[$i] . ',';
                    continue;
                }
                $str .= $condition[$i];
            }
            $sql = "select $str from `$this->table`";
        }
        if (!empty($array)) {
            $sqlbase = '';
            $temp = count($array);
            foreach ($array as $k => $v) {
                if ($temp > 1) {
                    $sqlbase .= $k . '="' . $v . '" and ';
                    $temp--;
                } else {
                    $sqlbase .= $k . '="' . $v . '"';
                }
            }
            $sql .= " where " . $sqlbase;
        }
        if(isset($list)&&isset($page)){
            $page=($page-1)*$list;
            $sql.=" order by id asc limit ".$page.','.$list;
        }
        $this->data = $this->db->query($sql)->fetchAll();
        if (count($this->data) < 1) {
            return false;
        }
        $this->byte = true;
        foreach ($this->data as $k => $v) {
            foreach ($this->data[$k] as $num => $ss) {
                if (is_numeric($num)) {
                    unset($this->data[$k][$num]);
                }
            }
        }
        return $this->data;
    }


    public function save()
    {
        if (!$this->byte) {
            return '错误!没有需要保存的数据';
        }
        $up_num = 0;
        foreach ($this->data as $k => $v) {
            $sql = "update `$this->table` set ";
            $id = $this->data[$k]['id'];
            unset($this->data[$k]['id']);
            $temp = count($this->data[$k]);
            foreach ($this->data[$k] as $kv => $vv) {
                if ($temp > 1) {
                    $sql .= "$kv='" . $vv . "',";
                    $temp--;
                } else {
                    $sql .= "$kv='" . $vv . "'";
                }
            }
            $sql .= " where id='" . $id . "'";
            $up_num += $this->db->exec($sql);
        }
        return $up_num;
    }

    public function insert($data)
    {
        $key = '';
        $value = '';
        $num = count($data);
        foreach ($data as $k => $v) {
            if ($num > 1) {
                $key .= $k . ',';
                $value .= '"' . $v . '",';
                $num--;
            } else {
                $key .= $k;
                $value .= '"' . $v . '"';
            }
        }
        $sql = "insert into $this->table ($key) value ($value)";
        return $this->db->exec($sql);
    }
    public function getError(){
        return $this->db->errorInfo();
    }
}
