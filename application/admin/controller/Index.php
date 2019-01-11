<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-10-10
 */

namespace app\admin\controller;


class Index extends Base {
    public function index() {
        return view();
    }

    public function home() {
        return '控制台';
    }
}