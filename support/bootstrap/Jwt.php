<?php
namespace support\bootstrap;

use Webman\Bootstrap;
use Firebase\JWT\JWT as JWTBaes;

class Jwt implements Bootstrap
{
    private static $key;
    private static $signType;
    private static $payload = [];
    public static function start($worker)
    {
        $config = config('jwt');
        self::$key = trim($config['key']) == '' ? 'jwt' : trim($config['key']);
        self::$signType = trim($config['signType']) == '' ? 'HS256' : trim($config['signType']);
    }

    /**
     * 生成token
     * @param array $data
     * @return string
     */
    public static function encrypt(array $data) :string
    {
        $time = time();
        $payload = [
            'iss' => 'wdlyt',//该JWT的签发者
            'aud' => 'wdlyt',//接收该JWT的一方
            'sub' => '',//该JWT所面向的用户
            'iat' => $time,//在什么时候签发的
            'exp' => $time+3600,// 什么时候过期，这里是一个Unix时间戳
        ];
        $payload = array_merge($data,$payload);
        return JWTBaes::encode($payload,self::$key,self::$signType);
    }

    /**
     * 解析token
     * @param string $sign
     * @return array
     */
    public static function decrypt(string $sign) :array
    {
        try {
            $parse = JWTBaes::decode($sign, self::$key, array(self::$signType));
            unset($parse->iss);
            unset($parse->aud);
            unset($parse->sub);
            unset($parse->iat);
            unset($parse->exp);
            return ['code'=>1,'data'=>$parse];
        } catch (\Exception $e){
            return ['code'=>0,'data'=>$e->getMessage()];
        }
    }
}