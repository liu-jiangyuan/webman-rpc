<?php
namespace app\websocket\controller;


class Index
{
    public function index(array $data)
    {
        return ['notice'=>'this is websocket index/index'];
    }

    public function test(array $data)
    {
        return array_merge(['notice'=>'this is websocket index/test','time'=>date('Y/m/d H:i:s')],$data);
    }
}