<?php

namespace app\admin\model\brand;

use AlibabaCloud\Client\Traits\MockTrait;
use basic\ModelBasic;
use think\Model;
use traits\model\SoftDelete;
use traits\ModelTrait;

class Brand extends ModelBasic
{
    //

    use ModelTrait;

    use SoftDelete;
    public static function getAll()
    {
        return self::order('sort desc,add_time desc')->field(['title', 'id'])->select();
    }

    public static function getAllList($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->select();
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc');
        if ($where['title'] != '') $model = $model->where('title', 'like', "%$where[title]%");
        // if ($where['cid'] != '') $model = $model->where('id', $where['cid']);
        return $model;
    }


    
}
