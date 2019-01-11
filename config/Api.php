<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-09
 */

namespace app\common\controller;


use think\Controller;

class Api extends Controller {
    // api接口返回格式
    protected $response = [
        'code' => 'success',
        'msg' => '',
        // 'response' => []
    ];

}