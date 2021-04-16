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
    const bomd          = 6;
    const bomb          = 7;
    const bomdTwoSingle = 8;
    const bombTwoPair   = 9;
    const straight      = 10;
    const company       = 11;
    const plane         = 12;
    const planeSingle   = 13;
    const planePair     = 14;
    const kingBomb      = 15;

    public function __construct()
    {
        $this->config = config('landlord');
    }

    /**
     * 是否单牌
     * @param array $cards
     * @return bool
     */
    public function isSingle(array $cards) :bool
    {
        return count($cards) === 1;
    }
    public function singleCardValue(array $cards) :array
    {
        return ['type' => self::single,'length' => count($cards),'value' => $this->config['value'][substr($cards[0],1)]];
    }

    /**
     * 是否对子
     * @param array $cards
     * @return bool
     */
    public function isPair(array $cards) :bool
    {
        return count($cards) === 2 && substr($cards[0],1) === substr($cards[1],1);
    }
    public function pairCardValue(array $cards) :array
    {
        return ['type' => self::pair,'length' => count($cards),'value' => $this->config['value'][substr($cards[0],1)]];
    }

    /**
     * 是否三张
     * @param array $cards
     * @return bool
     */
    public function isThree(array $cards) :bool
    {
        return count($cards) === 3 && substr($cards[0],1) === substr($cards[1],1) && substr($cards[0],1) === substr($cards[2],1);
    }
    public function threeCardValue(array $cards) :array
    {
        return ['type' => self::three,'length' => count($cards),'value' => $this->config['value'][substr($cards[0],1)]];
    }

    /**
     * 是否三带一
     * @param array $cards
     * @return bool
     */
    public function isThreeSingle(array $cards) :bool
    {
        if (count($cards) != 4) return false;
        $substr = [];
        foreach ($cards as $card){
            $substr[] = substr($card,1);
        }
        $cardTimes = array_values(array_count_values($substr));
        if (count($cardTimes) != 2) return false;
        return $cardTimes == [1,3] || $cardTimes == [3,1];
    }
    public function threeSingleCardValue(array $cards) :array
    {
        $substr = [];
        foreach ($cards as $card){
            $substr[] = substr($card,1);
        }
        $cardTimes = array_values(array_count_values($substr));
        $value = 0;
        foreach ($cardTimes as $k => $v){
            if ($v != 3) continue;
            $value = $k;
            break;
        }
        return ['type' => self::threeSingle,'length' => count($cards),'value' => $this->config['value'][$value]];
    }

    /**
     * 是否三带二
     * @param array $cards
     * @return bool
     */
    public function isThreePair(array $cards) :bool
    {
        if (count($cards) != 5) return false;
        $substr = [];
        foreach ($cards as $card){
            $substr[] = substr($card,1);
        }
        $cardTimes = array_values(array_count_values($substr));
        if (count($cardTimes) != 2) return false;
        return $cardTimes == [2,3] || $cardTimes == [3,2];
    }
    public function threePairCardValue(array $cards) :array
    {
        $substr = [];
        foreach ($cards as $card){
            $substr[] = substr($card,1);
        }
        $cardTimes = array_values(array_count_values($substr));
        $value = 0;
        foreach ($cardTimes as $k => $v){
            if ($v != 3) continue;
            $value = $k;
            break;
        }
        return ['type' => self::threePair,'length' => count($cards),'value' => $this->config['value'][$value]];
    }

    /**
     * 是否顺子   不能包含2大小王
     * @param array $cards
     * @return bool
     */
    public function isStraight(array $cards) :bool
    {
        if (count($cards) < 5) return false;
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_values(array_count_values($substr));
        if (max($cardTimes) > 1) return false;
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
        return $bool;
    }
    public function straightCardValue(array $cards) :array
    {
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        rsort($substr);
        return ['type' => self::straight,'length' => count($cards),'value' => $substr[0]];
    }

    /**
     * 是否连对
     * @param array $cards
     * @return bool
     */
    public function isCompany(array $cards) :bool
    {
        if (count($cards) < 6) return false;
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_values(array_count_values($substr));
        if (max($cardTimes) > 2 || min($cardTimes) < 2) return false;
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
        return $bool;
    }
    public function companyCardValue(array $cards) :array
    {
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        rsort($substr);
        return ['type' => self::company,'length' => count($cards),'value' => $substr[0]];
    }

    /**
     * 是否飞机
     * @param array $cards
     * @return bool
     */
    public function isPlane(array $cards) :bool
    {
        if (count($cards) < 6) return false;
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_values(array_count_values($substr));
        if (max($cardTimes) > 3 || min($cardTimes) < 3) return false;
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
        return $bool;
    }
    public function planeCardValue(array $cards) :array
    {

        return ['type' => self::plane,'length' => count($cards),'value' => ''];
    }

    /**
     * 飞机带单
     * @param array $cards
     * @return bool
     */
    public function isPlaneSingle(array $cards) :bool
    {
        if (count($cards) < 8 || count($cards) % 4) return false;
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_count_values($substr);
        if (max($cardTimes) > 3) return false;

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
        return $bool;
    }

    /**
     * 飞机带对
     * @param array $cards
     * @return bool
     */
    public function isPlanePair(array $cards) :bool
    {
        if (count($cards) < 10 || count($cards) % 5) return false;
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_count_values($substr);
        if (max($cardTimes) > 3 || min($cardTimes) < 2) return false;

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
        return $bool;
    }

    /**
     * 是否炸弹
     * @param array $cards
     * @return bool
     */
    public function isBomb(array $cards) :bool
    {
        return count($cards) === 4 && substr($cards[0],1) === substr($cards[1],1) && substr($cards[0],1) === substr($cards[2],1) && substr($cards[0],1) === substr($cards[4],1);
    }

    /**
     * 四带二单
     * @param array $cards
     * @return bool
     */
    public function isBombTwoSingle(array $cards) :bool
    {
        if (count($cards) != 6) return false;
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_count_values($substr);
        rsort($cardTimes);
        return $cardTimes == [4,1,1];
    }

    /**
     * 四带两对
     * @param array $cards
     * @return bool
     */
    public function isBombTwoPair(array $cards) :bool
    {
        if (count($cards) != 8) return false;
        $substr = [];
        foreach ($cards as $card){
            $substr[] = $this->config['value'][substr($card,1)];
        }
        $cardTimes = array_count_values($substr);
        rsort($cardTimes);
        return $cardTimes == [4,2,2];
    }

    /**
     * 是否王炸
     * @param array $cards
     * @return bool
     */
    public function isKingBomb(array $cards) :bool
    {
        return  count($cards) === 2 && ($cards == ['M0','M1'] || $cards == ['M1','M0']);
    }
}