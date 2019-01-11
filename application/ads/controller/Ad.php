<?php
/**
 * 广告控制器.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-07
 */

namespace app\ads\controller;


use app\admin\controller\Base;
use app\ads\model\AdPositions;
use app\ads\model\Ads;

class Ad extends Base {
    public function index() {
        if (request()->isAjax()) {
            $param = input('param.');
            $queryWhere[] = ['ad_status', 'egt', 0];
            if(isset($param['name']) && !empty($param['name'])) {
                $queryWhere[] = ['ad_title', 'like', '%'.$param['name'].'%'];
            }
            $this->response['count'] = db('ads')->where($queryWhere)->count();
            $data = db('ads')->where($queryWhere)->page($param['page'])->limit($param['limit'])->order('ad_id', 'desc')->select();
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
            $role = new Ads();
            $saveData = [
                'ad_position_id' => $param['positionId'],
                'ad_title' => $param['title'],
                'ad_brief' => $param['brief'],
                'ad_url' => $param['url'],
                'ad_image' => $param['adimage'],
                'ad_sort' => $param['sort'],
            ];

            // 如果是添加就要单独处理了
            if (isset($param['aid']) && !empty($param['aid'])) {
                $saveWhere['ad_id'] = $param['aid'];
            } else {
                $saveData['ad_create_time'] = time();
            }

            $result = $role->allowField(true)->save($saveData, $saveWhere);

            if (!$result) {
                $this->response['code'] = -1;
                $this->response['msg'] = '操作失败';
            }

            return $this->response;
        } else {
            $info = [];
            if (isset($param['aid'])) {
                $info = db('ads')->where(['ad_id' => $param['aid']])->find();
            }
//            $adpositions = new AdPositions();
            $positions = AdPositions::all();

            return view('', [
                'info' => $info,
                'positions' => $positions,
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
            db('ads')->where(['ad_id' => $param['aid']])->update(['ad_status' => -1]);
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
            db('ads')->where(['ad_id' => $param['aid']])->update(['ad_sort' => $param['sort']]);
            return $this->response;
        }
    }
}