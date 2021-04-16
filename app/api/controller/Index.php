<?php
namespace app\api\controller;

use app\api\model\redis\Redis;
use support\Request;
class Index
{
    public function index(Request $request)
    {
        Redis::hMSet('test',['server'=>'webman']);
        return json(Redis::hGetAll('test'));
    }
}