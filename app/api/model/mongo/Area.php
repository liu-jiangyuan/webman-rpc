<?php
namespace app\api\model\mongo;


use Jenssegers\Mongodb\Eloquent\Model;

class Area extends Model
{
    protected $collection = 'area';
    protected $connection = 'mongodb';

    public function add(array $data)
    {
        return $this->insert($data);
    }
}