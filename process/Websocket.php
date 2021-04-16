<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace process;

use support\Instance\WsController;
use Workerman\Connection\TcpConnection;

class Websocket
{
    public function onConnect(TcpConnection $connection)
    {

    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        $connection->send("onWebSocketConnect\n");
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $param = json_decode($data,true);
        $class = !isset($param['c']) ? 'index' : $param['c'];
        $method = !isset($param['a']) ? 'index' : $param['a'];
        $args = !isset($param['args']) || empty($param['args']) ? [] : $param['args'];
        try {
            $result = call_user_func([WsController::getInstance()->get($class),$method],$args);
            $connection->send(json_encode(['errCode'=>0,'msg'=>'success','time'=>time(),'data'=>$result],320));
        } catch (\Exception $e){
            $connection->send(['errCode'=>1,'msg'=>$e->getMessage(),'time'=>time(),'data'=>[]],320);
        }
    }

    public function onClose(TcpConnection $connection)
    {

    }
}