<?php
//spade 黑桃 heart 红心 wintersweet 梅花 diamond 方块
$client = stream_socket_client('tcp://127.0.0.1:9705');
$request = [
    'class'   => 'Test',
    'method'  => 'test',
    'args'    => ['card'=>['WJ','CJ']], // 100 是 $uid
];
fwrite($client, json_encode($request)."\n"); // text协议末尾有个换行符"\n"
$result = fgets($client, 10240000);
$result = json_decode($result, true);
var_dump($result);