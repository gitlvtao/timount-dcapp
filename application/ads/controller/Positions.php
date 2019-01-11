<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-07
 */

namespace app\ads\controller;


use app\admin\controller\Base;
use app\ads\model\AdPositions;

class Positions extends Base {
    public function index() {
        if (request()->isAjax()) {
            $param = input('param.');
            $queryWhere = [];
            if(isset($param['name']) && !empty($param['name'])) {
                $queryWhere[] = ['positions_title', 'like', '%'.$param['name'].'%'];
            }
            $this->response['count'] = db('ad_positions')->where($queryWhere)->count();
            $data = db('ad_positions')->where($queryWhere)->page($param['page'])->limit($param['limit'])->order('positions_id', 'desc')->select();
            $this->response['data'] = $data;
            return $this->response;
        } else {
            return view();
        }
    }

    /**
     * 管理员编辑/添加
     * @return \think\response\View
     */
    public function updateAdd() {
        $param = input('param.');
        if (request()->isAjax()) {
            $saveData = $saveWhere = [];
            // 编辑、删除都可以修改的数据
            $role = new AdPositions();
            $saveData = [
                'positions_title' => $param['name'],
                'positions_ident' => $param['ident'],
                'positions_brief' => $param['brief'],
            ];

            // 如果是添加就要单独处理了
            if (isset($param['pid']) && !empty($param['pid'])) {
                $saveWhere['positions_id'] = $param['pid'];
            } else {
                $saveData['positions_create_time'] = time();
            }

            $result = $role->allowField(true)->save($saveData, $saveWhere);

            if (!$result) {
                $this->response['code'] = -1;
                $this->response['msg'] = '操作失败';
            }

            return $this->response;
        } else {
            $info = [];
            if (isset($param['pid'])) {
                $info = db('ad_positions')->where(['positions_id' => $param['pid']])->find();
            }

            return view('', [
                'info' => $info
            ]);
        }
    }

    /**
     * 删除用户组
     * @return array
     */
    public function delete() {
        if (request()->isAjax()) {
            $param = request()->only('pid');
            db('ad_positions')->where(['positions_id' => $param['pid']])->delete();
            return $this->response;
        }
    }
}