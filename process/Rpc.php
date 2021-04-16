<?php
namespace process;

use support\Instance\RpcController;
use Workerman\Connection\TcpConnection;
class Rpc
{
    public function onMessage(TcpConnection $connection, $data)
    {

        $data = json_decode($data, true);
        $class = $data['class'];
        $method = $data['method'];
        $args = $data['args'];
        $connection->send(call_user_func([RpcController::getInstance()->get($class), $method], $args));
    }
}