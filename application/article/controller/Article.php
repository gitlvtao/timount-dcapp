<?php
/**
 * 文章控制器.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-12
 */

namespace app\article\controller;


use app\admin\controller\Base;
use app\article\model\Article as  TmyArticle;

class Article extends Base {
    /**
     * 文章列表
     * @return array|\think\response\View
     */
    public function index() {
        if (request()->isAjax()) {
            $param = input('param.');
            $queryWhere = [
                ['article_status', 'egt', 0],
                ['article_is_single', 'eq', 0]
            ];
            if(isset($param['name']) && !empty($param['name'])) {
                $queryWhere[] = ['article_title', 'like', '%'.$param['name'].'%'];
            }
            $this->response['count'] = db('article')->where($queryWhere)->count();
            $data = db('article')->alias('a')
                    ->join('column c', 'c.column_id = a.article_column_id', 'left')
                    ->where($queryWhere)
                    ->page($param['page'])
                    ->limit($param['limit'])
                    ->order('article_sort', 'asc')
                    ->field(['a.*', 'c.column_name'])
                    ->select();
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
            $article = new TmyArticle();
            $saveData = [
                'article_column_id' => $param['columnId'],
                'article_title' => $param['title'],
                'article_thumb' => $param['articleimage'],
                'article_brief' => $param['brief'],
                'article_content' => $param['content'],

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
     * 删除文章
     * @return array
     */
    public function delete() {
        if (request()->isAjax()) {
            $param = request()->only('aid');
            db('article')->where(['article_id' => $param['aid']])->update(['article_status' => -1]);
            return $this->response;
        }
    }

    /**
     * 排序
     * @return array
     */
    public function sort() {
        if (request()->isAjax()) {
            $param = request()->only(['aid', 'sort']);
            db('article')->where(['article_id' => $param['aid']])->update(['article_sort' => $param['sort']]);
            return $this->response;
        }
    }

    /**
     * 推荐
     * @return array
     */
    public function recom() {
        if (request()->isAjax()) {
            $param = request()->only(['aid', 'recom']);
            db('article')->where(['article_id' => $param['aid']])->update(['article_attr' => $param['recom']]);
            return $this->response;
        }
    }
}