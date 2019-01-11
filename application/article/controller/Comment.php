<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-12
 */

namespace app\article\controller;


use app\admin\controller\Base;

class Comment extends Base {
    public function index() {
        $param = input('param.');
        if (request()->isAjax()) {
            $queryWhere = [
                ['comment_article_id', 'eq', $param['aid']],
                ['comment_status', 'egt', 0],
            ];
            if(isset($param['name']) && !empty($param['name'])) {
                $queryWhere[] = ['positions_title', 'like', '%'.$param['name'].'%'];
            }
            $this->response['count'] = db('article_comment')->where($queryWhere)->count();
            $data = db('article_comment')->alias('ac')
                    ->join('users u', 'ac.comment_user_id = u.user_id', 'left')
                    ->where($queryWhere)
                    ->page($param['page'])
                    ->limit($param['limit'])
                    ->field(['ac.*', 'u.user_nickname'])
                    ->order('comment_id', 'desc')->select();
            $this->response['data'] = $data;
            return $this->response;
        } else {
            return view('', [
                'aid' => $param['aid'],
            ]);
        }
    }

    /**
     * 删除评论
     * @return array
     */
    public function delete() {
        if (request()->isAjax()) {
            $param = request()->only('cid');
            db('article_comment')->where(['comment_id' => $param['cid']])->update(['comment_status' => -1]);
            return $this->response;
        }
    }
}