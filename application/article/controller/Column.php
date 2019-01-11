<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-16
 */

namespace app\article\controller;


use app\admin\controller\Base;
use app\article\model\Column as TmyColumn;

class Column extends Base {
    /**
     * 栏目列表
     * @return array|\think\response\View
     */
    public function index() {
        if (request()->isAjax()) {
            $param = input('param.');
            $queryWhere = [
                ['column_status', 'egt', 0],
                ['column_type', 'eq', 2]
            ];
            if(isset($param['name']) && !empty($param['name'])) {
                $queryWhere[] = ['column_name', 'like', '%'.$param['name'].'%'];
            }
            $this->response['count'] = db('column')->where($queryWhere)->count();
            $data = db('column')->where($queryWhere)->page($param['page'])->limit($param['limit'])->order('column_id', 'asc')->select();
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
            $article = new TmyColumn();
            $saveData = [
                'column_pid' => $param['columnId'],
                'column_name' => $param['title'],
                'column_thumb' => $param['thumbimage'],
                'column_banner' => $param['bannerimage'],
                'column_summary' => $param['brief'],
                'column_type' => 2,

            ];

            // 如果是添加就要单独处理了
            if (isset($param['cid']) && !empty($param['cid'])) {
                $saveWhere['column_id'] = $param['cid'];
            } else {
                $saveData['column_create_time'] = time();
            }

            $result = $article->allowField(true)->save($saveData, $saveWhere);

            if (!$result) {
                $this->response['code'] = -1;
                $this->response['msg'] = '操作失败';
            }

            return $this->response;
        } else {
            $info = [];
            if (isset($param['cid'])) {
                $info = db('column')->where(['column_id' => $param['cid']])->find();
            }

            $columnWhere = [
                'column_type' => 2,
                'column_status' => 1,
            ];
            $column = db('column')->where($columnWhere)->field(['column_id', 'column_name'])->select();

            return view('', [
                'info' => $info,
                'column' => $column,
            ]);
        }
    }

    /**
     * 删除文章
     * @return array
     */
    public function delete() {
        if (request()->isAjax()) {
            $param = request()->only('cid');
            db('column')->where(['column_id' => $param['cid']])->update(['column_status' => -1]);
            return $this->response;
        }
    }
}