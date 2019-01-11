<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-12-04
 */

namespace app\pagedata\controller;


use app\common\controller\Api;
use fuk\LogFlow;

class Pageapi extends Api {
    public function index() {
        $param = request()->param('data');

        $param['create_time'] = time();
        $param['ip'] = request()->ip();
        LogFlow::write($param, date('Ymd').'.log');

        return $this->response;
    }
}