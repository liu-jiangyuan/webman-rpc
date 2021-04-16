<?php
namespace app\api\model\mysql;


use support\Model;

class Area extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'cnarea';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;


    public function getSome(array $where=[])
    {
        return $this->where($where)->get()->toArray();
    }
}