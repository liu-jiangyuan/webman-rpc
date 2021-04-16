<?php
namespace app\api\model\redis;

use support\bootstrap\Redis as RedsM;
class Redis extends RedsM
{
    public static function hSet($key,$name,$val,$encode = false) :bool
    {
        return $encode ? parent::hSet($key,$name,json_encode($val,320)) : parent::hSet($key,$name,$val);
    }
    public static function hMSet($key,$val,$encode = false) :bool
    {
        if (!empty($val) && $encode){
            foreach ($val as $k => $v){
                $val[$k] = json_encode($v,320);
            }
        }
        return parent::hMSet($key,$val);
    }

    public static function hGet($key, $name,$encode = false) :string
    {
        return $encode ? json_decode(parent::hGet($key,$name),true) : parent::hGet($key,$name);
    }
    public static function hMGet($key, $memberKeys,$encode = false) :array
    {
        $arr = parent::hMGet($key,$memberKeys);
        if ($encode) {
            foreach ($arr as $k => $v){
                $arr[$k] = json_decode($v,true);
            }
        }
        return $arr;
    }

    public static function hGetAll($key,$encode = false) :array
    {
        $arr = parent::hGetAll($key);
        if ($encode) {
            foreach ($arr as $k => $v){
                $arr[$k] = json_decode($v,true);
            }
        }
        return $arr;
    }
}