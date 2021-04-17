<?php
namespace app\service\landlord;

/**
 * Class Card
 * @package app\service\landlord
 */
class Card
{
    /**
     * config('landlord')
     * @var array
     */
    private $config = [];

    const single        = 1;
    const pair          = 2;
    const three         = 3;
    const threeSingle   = 4;
    const threePair     = 5;
    const bomb          = 6;
    const bombTwoSingle = 7;
    const bombTwoPair   = 8;
    const straight      = 9;
    const company       = 10;
    const plane         = 11;
    const planeSingle   = 12;
    const planePair     = 13;
    const kingBomb      = 14;

    public function __construct()
    {
        $this->config = config('landlord');
    }

    /**
     * 是否单牌
     * @param array $cards
     * @return array
     */
    public function isSingle(array $cards) :array
    {
        return [
            'check'=>count($cards) === 1,
            'data'=>[
                'type' => self::single,
                'length' => count($cards),
                'value' => $this->config['value'][substr($cards[0],1)
                ]
            ]
        ];
    }

    /**
     * 是否对子
     * @param array $cards
     * @return array
     */
    public function isPair(array $cards) :array
    {
        return [
            'check'=>count($cards) === 2 && substr($cards[0],1) === substr($cards[1],1),
            'data'=>[
                'type' => self::pair,
                'length' => count($cards),
                'value' => $this->config['value'][substr($cards[0],1)
                ]
            ]
        ];
    }

    /**
     * 是否三张
     * @param array $cards
     * @return array
     */
    public function isThree(array $cards) :array
    {
        return [
            'check'=> count($cards) === 3 && substr($cards[0],1) === substr($cards[1],1) && substr($cards[0],1) === substr($cards[2],1),
            'data'=>[
                'type' => self::three,
                'length' => count($cards),
                'value' => $this->config['value'][substr($cards[0],1)
                ]
            ]
        ];
    }

    /**
     * 是否三带一
     * @param array $cards
     * @return array
     */
    public function isThreeSingle(array $cards) :array
    {
        if (count($cards) != 4) return ['check'=>false,'data'=>[]];
        $substr = [];
        foreach ($cards as $card){
            $substr[] = substr($card,1);
        }
        $cardTimes = array_values(array_count_values($substr));
        if (count($cardTimes) != 2) return ['check'=>false,'data'=>[]];

        $value = 0;
        foreach ($cardTimes as $k => $v){
            if ($v != 3) continue;
            $value = $k;
            break;
        }
        return [
            'check'=> $cardTimes == [1,3] || $cardTimes == [3,1],
            'data'=>[
                'type' => self::threeSingle,
                'length' => count($cards),
                'value' => $this->config['value'][$value]
            ]
        ];
    }

    /**
     * 是否三带二
     * @param array $cards
     * @return array
     */
    public function isThreePair(array $cards) :array
    {
        if (count($cards) != 5) return ['check'=>false,'data'=>[]];
        $substr = [];
        foreach ($cards as $card){
            $substr[] = substr($card,1);
        }
        $cardTimes = array_count_values($substr);
        if (count($cardTimes) != 2) return ['check'=>false,'data'=>[]];
        $value = 0;
        foreach ($cardTimes as $k => $v){
            if ($v != 3) continue;
            $value = $k;
            break;
        }
        return [
            'check'=> array_values($cardTimes) == [2,3] || array_values($cardTimes) == [3,2],
            'data'=>[
                'type' => self::threePair,
                'length' => count($cards),
                'value' => $this->config['value'][$value]
            ]
        ];
    }

    /**
     * 是否顺子   不能包含2大小王
     * @param array $cards
     * @return array
     */
    public function isStraight(array $cards) :array
    {
        if (count($cards) < 5) return ['check'=>false,'data'=>[]];
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_values(array_count_values($substr));
        if (max($cardTimes) > 1) return ['check'=>false,'data'=>[]];
        rsort($substr);
        $bool = true;
        foreach ($substr as $k => $v){
            if (in_array($v,[13,14,15])){
                $bool = false;
                break;
            }
            if (isset($substr[$k+1])) {
                if ($v - $substr[$k + 1] > 1){
                    $bool = false;
                    break;
                }
            }
        }
        return [
            'check'=> $bool,
            'data'=>[
                'type' => self::straight,
                'length' => count($cards),
                'value' => $substr[0]
            ]
        ];
    }

    /**
     * 是否连对
     * @param array $cards
     * @return array
     */
    public function isCompany(array $cards) :array
    {
        if (count($cards) < 6) return ['check'=>false,'data'=>[]];
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_values(array_count_values($substr));
        if (max($cardTimes) > 2 || min($cardTimes) < 2) return ['check'=>false,'data'=>[]];
        $unique = array_unique($substr);
        rsort($unique);
        $bool = true;
        foreach ($unique as $k => $v){
            if (in_array($v,[13,14,15])){
                $bool = false;
                break;
            }
            if (isset($unique[$k+1])) {
                if ($v - $unique[$k + 1] > 1){
                    $bool = false;
                    break;
                }
            }

        }
        return [
            'check'=> $bool,
            'data'=>[
                'type' => self::company,
                'length' => count($cards),
                'value' => $unique[0]
            ]
        ];
    }

    /**
     * 是否飞机
     * @param array $cards
     * @return array
     */
    public function isPlane(array $cards) :array
    {
        if (count($cards) < 6) return ['check'=>false,'data'=>[]];
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_values(array_count_values($substr));
        if (max($cardTimes) > 3 || min($cardTimes) < 3) return ['check'=>false,'data'=>[]];
        $unique = array_unique($substr);
        rsort($unique);
        $bool = true;
        foreach ($unique as $k => $v){
            if (in_array($v,[13,14,15])){
                $bool = false;
                break;
            }
            if (isset($unique[$k+1])) {
                if ($v - $unique[$k + 1] > 1){
                    $bool = false;
                    break;
                }
            }
        }
        return [
            'check'=> $bool,
            'data'=>[
                'type' => self::plane,
                'length' => count($cards),
                'value' => $unique[0]
            ]
        ];
    }

    /**
     * 飞机带单
     * @param array $cards
     * @return array
     */
    public function isPlaneSingle(array $cards) :array
    {
        if (count($cards) < 8 || count($cards) % 4) return ['check'=>false,'data'=>[]];
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_count_values($substr);
        if (max($cardTimes) > 3) return ['check'=>false,'data'=>[]];

        $waitCheck = [];
        foreach ($cardTimes as $cardValue => $times){
            if ($times != 3) continue;
            $waitCheck[] = $cardValue;
        }
        rsort($waitCheck);
        $bool = true;
        foreach ($waitCheck as $k => $v){
            if (in_array($v,[13,14,15])){
                $bool = false;
                break;
            }
            if (isset($waitCheck[$k+1])) {
                if ($v - $waitCheck[$k + 1] > 1){
                    $bool = false;
                    break;
                }
            }
        }
        return [
            'check'=> $bool,
            'data'=>[
                'type' => self::planeSingle,
                'length' => count($cards),
                'value' => $waitCheck[0]
            ]
        ];
    }

    /**
     * 飞机带对
     * @param array $cards
     * @return array
     */
    public function isPlanePair(array $cards) :array
    {
        if (count($cards) < 10 || count($cards) % 5) return ['check'=>false,'data'=>[]];
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_count_values($substr);
        if (max($cardTimes) > 3 || min($cardTimes) < 2) return ['check'=>false,'data'=>[]];

        $waitCheck = [];
        foreach ($cardTimes as $cardValue => $times){
            if ($times != 3) continue;
            $waitCheck[] = $cardValue;
        }
        rsort($waitCheck);
        $bool = true;
        foreach ($waitCheck as $k => $v){
            if (in_array($v,[13,14,15])){
                $bool = false;
                break;
            }
            if (isset($waitCheck[$k+1])) {
                if ($v - $waitCheck[$k + 1] > 1){
                    $bool = false;
                    break;
                }
            }
        }
        return [
            'check'=> $bool,
            'data'=>[
                'type' => self::planePair,
                'length' => count($cards),
                'value' => $waitCheck[0]
            ]
        ];
    }

    /**
     * 是否炸弹
     * @param array $cards
     * @return array
     */
    public function isBomb(array $cards) :array
    {
        return [
            'check'=> count($cards) === 4 && substr($cards[0],1) === substr($cards[1],1) && substr($cards[0],1) === substr($cards[2],1) && substr($cards[0],1) === substr($cards[3],1),
            'data'=>[
                'type' => self::bomb,
                'length' => count($cards),
                'value' => $this->config['value'][substr($cards[0],1)]
            ]
        ];
    }

    /**
     * 四带二单
     * @param array $cards
     * @return array
     */
    public function isBombTwoSingle(array $cards) :array
    {
        if (count($cards) != 6) return ['check'=>false,'data'=>[]];
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_count_values($substr);
        $cardValue = 0;
        foreach ($cardTimes as $value => $times){
            if ($times != 4) continue;
            $cardValue = $value;
            break;
        }
        rsort($cardTimes);

        return [
            'check'=> $cardTimes == [4,1,1],
            'data'=>[
                'type' => self::bombTwoSingle,
                'length' => count($cards),
                'value' => $cardValue
            ]
        ];
    }

    /**
     * 四带两对
     * @param array $cards
     * @return array
     */
    public function isBombTwoPair(array $cards) :array
    {
        if (count($cards) != 8) return ['check'=>false,'data'=>[]];
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_count_values($substr);
        $cardValue = 0;
        foreach ($cardTimes as $value => $times){
            if ($times != 4) continue;
            $cardValue = $value;
            break;
        }
        rsort($cardTimes);

        return [
            'check'=> $cardTimes == [4,2,2],
            'data'=>[
                'type' => self::bombTwoPair,
                'length' => count($cards),
                'value' => $cardValue
            ]
        ];
    }

    /**
     * 是否王炸
     * @param array $cards
     * @return array
     */
    public function isKingBomb(array $cards) :array
    {
        return [
            'check'=> count($cards) === 2 && ($cards == ['M0','M1'] || $cards == ['M1','M0']),
            'data'=>[
                'type' => self::kingBomb,
                'length' => count($cards),
                'value' => 15
            ]
        ];
    }

    /**
     * 确定牌型
     * @param array $cards
     * @return array
     */
    public function cardType(array $cards) :array
    {
        $res = [];
        $cardNum = count($cards);
        switch ($cardNum){
            case 1:
                $check = $this->isSingle($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 2:
                $check = $this->isPair($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isKingBomb($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 3:
                $check = $this->isThree($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 4:
                $check = $this->isThreeSingle($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isBomb($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 5:
                $check = $this->isThreePair($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 6:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isCompany($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlane($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isBombTwoSingle($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 7:
            case 11:
            case 13:
            case 17:
            case 19:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 8:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isCompany($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlaneSingle($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isBombTwoPair($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 9:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlane($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 10:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isCompany($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlanePair($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 12:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isCompany($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlaneSingle($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlane($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 14:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isCompany($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 15:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlane($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlanePair($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 16:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isCompany($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlaneSingle($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 18:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isCompany($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlane($cards);
                if ($check['check']) $res = $check['data'];
                break;
            case 20:
                $check = $this->isStraight($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isCompany($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlaneSingle($cards);
                if ($check['check']) $res = $check['data'];
                $check = $this->isPlanePair($cards);
                if ($check['check']) $res = $check['data'];
                break;
        }
        return $res;
    }

    /**
     * 比较大小
     * @param array $thisCard [type,length,value]
     * @param array $lastCard [type,length,value]
     * @return bool
     */
    public function compare(array $thisCard,array $lastCard) :bool
    {
        if ($thisCard['type'] == self::kingBomb) return true;
        if ($lastCard['type'] == self::kingBomb) return false;
        if (in_array($thisCard['type'],[self::bomb,self::kingBomb]) && !in_array($lastCard['type'],[self::bomb,self::kingBomb])) return true;
        if (!in_array($thisCard['type'],[self::bomb,self::kingBomb]) && in_array($lastCard['type'],[self::bomb,self::kingBomb])) return false;

        if ($thisCard['length'] != $lastCard['length']) return false;
        if ($thisCard['value'] <= $lastCard['value']) return false;
        return true;
    }
}