<?php
namespace app\rpc\controller;

use app\service\landlord\Card;

class Test
{
    public function test(array $data)
    {
        $card = $data['card'];

        return json_encode((new Card())->cardType($card),320);
    }
}