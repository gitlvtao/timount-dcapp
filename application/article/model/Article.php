<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-12
 */

namespace app\article\model;


use think\Model;

class Article extends Model {
    protected $pk = 'article_id';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'article_create_time';
    protected $updateTime = 'article_update_time';

}