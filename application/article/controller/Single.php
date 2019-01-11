<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-12
 */

namespace app\article\controller;


use app\admin\controller\Base;
use app\article\model\Article as  TmyArticle;

class Single extends Base {
    public function index() {
        if (request()->isAjax()) {
            $param = input('param.');
            $queryWhere = [
                ['article_status', 'eq', 1],
                ['article_is_single', 'eq', 1],
            ];
            if(isset($param['name']) && !empty($param['name'])) {
                $queryWhere[] = ['article_title', 'like', '%'.$param['name'].'%'];
            }
            $this->response['count'] = db('article')->where($queryWhere)->count();
            $data = db('article')->where($queryWhere)->page($param['page'])->limit($param['limit'])->order('article_id', 'desc')->select();
            $this->response['data'] = $data;
            return $this->response;
        } else {
            return view();
        }
    }

    /**
     * 单页编辑/添加
     * @return \think\response\View
     */
    public function updateAdd() {
        $param = input('param.');
        if (request()->isAjax()) {
            $saveData = $saveWhere = [];
            // 编辑、删除都可以修改的数据
            $article = new TmyArticle();
            $saveData = [
                'article_title' => $param['title'],
                'article_single_ident' => $param['ident'],
                'article_content' => $param['content'],
                'article_is_single' => 1,
            ];

            // 如果是添加就要单独处理了
            if (isset($param['aid']) && !empty($param['aid'])) {
                $saveWhere['article_id'] = $param['aid'];
            }/* else {
                $saveData['positions_create_time'] = time();
            }*/

            $result = $article->allowField(true)->save($saveData, $saveWhere);

            if (!$result) {
                $this->response['code'] = -1;
                $this->response['msg'] = '操作失败';
            }

            return $this->response;
        } else {
            $info = [];
            if (isset($param['aid'])) {
                $info = db('article')->where(['article_id' => $param['aid']])->find();
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
     * 删除用户组
     * @return array
     */
    public function delete() {
        if (request()->isAjax()) {
            $param = request()->only('aid');
            db('article')->where(['article_id' => $param['aid']])->update(['article_status' => -1]);
            return $this->response;
        }
    }
}