<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-20
 */

namespace app\user\controller;


use app\admin\controller\Base;

class Test extends Base {
    public function index() {
        if (request()->isAjax()) {
            $param = input('param.');
            $queryWhere = [];
            if(isset($param['mobile']) && !empty($param['mobile'])) {
                $queryWhere[] = ['u.user_mobile', 'like', '%'.$param['mobile'].'%'];
            }
            $this->response['count'] = db('loan_test')->count();
            $data = db('loan_test')->alias('t')
                    ->join('users u', 'u.user_id = t.test_user_id', 'left')
                    ->where($queryWhere)
                    ->page($param['page'])
                    ->limit($param['limit'])
                    ->order('test_create_time', 'desc')
                    ->field(['t.*', 'u.user_mobile'])
                    ->select();

            $loanTest = config('tmy.loan_test');
            foreach ($data as $key => $item) {
                $item['test_money'] = $loanTest['moneyRange'][$item['test_money']];
                $item['test_term'] = $loanTest['termRange'][$item['test_term']];
                $item['test_use'] = $loanTest['useRange'][$item['test_use']];
                $item['test_follow'] = $loanTest['followRange'][$item['test_follow']];
                $data[$key] = $item;
            }

            $this->response['data'] = $data;
            return $this->response;
        } else {
            return view();
        }
    }
}