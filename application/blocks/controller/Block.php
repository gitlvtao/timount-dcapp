<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-08
 */

namespace app\blocks\controller;


use app\admin\controller\Base;
use app\blocks\model\Blocks;

class Block extends Base {
    public function index() {
        if (request()->isAjax()) {
            $param = input('param.');
            $queryWhere[] = ['block_status', 'egt', 0];
            if(isset($param['name']) && !empty($param['name'])) {
                $queryWhere[] = ['block_title', 'like', '%'.$param['name'].'%'];
            }
            $this->response['count'] = db('blocks')->where($queryWhere)->count();
            $data = db('blocks')->where($queryWhere)->page($param['page'])->limit($param['limit'])->order('block_id', 'desc')->select();

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
            $block = new Blocks();
            $saveData = [
                'block_ident' => $param['ident'],
                'block_title' => $param['title'],
                'block_brief' => $param['brief'],
            ];

            // 如果是添加就要单独处理了
            if (isset($param['bid']) && !empty($param['bid'])) {
                $saveWhere['block_id'] = $param['bid'];
            } else {
                $saveData['block_create_time'] = time();
            }

            $result = $block->allowField(true)->save($saveData, $saveWhere);

            if (!$result) {
                $this->response['code'] = -1;
                $this->response['msg'] = '操作失败';
            }

            return $this->response;
        } else {
            $info = [];
            if (isset($param['bid'])) {
                $info = db('blocks')->where(['block_id' => $param['bid']])->find();
            }

            return view('', [
                'info' => $info,
            ]);
        }
    }

    /**
     * 删除用户组
     * @return array
     */
    public function delete() {
        if (request()->isAjax()) {
            $param = request()->only('bid');
            db('blocks')->where(['block_id' => $param['bid']])->update(['block_status' => -1]);
			// 更新缓存
            $this->updateBlockCache();
            return $this->response;
        }
    }
}