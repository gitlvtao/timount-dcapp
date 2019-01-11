<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-08
 */

namespace app\blocks\controller;


use app\admin\controller\Base;
use app\blocks\model\BlockData;

class Data extends Base {
    /**
     * 列表信息
     * @return array|\think\response\View
     */
    public function index() {
        $param = input('param.');
        // 获取是哪个模块
        $block = db('blocks')->where(['block_id' => $param['bid']])->field(['block_ident', 'block_id', 'block_title'])->find();
        if (request()->isAjax()) {
            // 计算条数
            $this->response['count'] = db('block_data')->where(['data_block_id' => $param['bid']])->count();
            // 查询条件
            $queryWhere['d.data_block_id'] = $param['bid'];
            $data = [];
            switch ($block['block_ident']) {
                case 'column_index':
                    $queryWhere['c.column_type'] = 1;
                    $data = db('block_data')->alias('d')
                        ->join('column c', 'd.data_object_id = c.column_id')
                        ->where($queryWhere)
                        ->field(['d.data_id', 'd.data_create_time', 'd.data_sort', 'c.column_name'])
                        ->page($param['page'])->limit($param['limit'])->order('d.data_sort', 'asc')->select();
                    break;
                case 'product_index':
                    $queryWhere['p.product_status'] = 1;
                    $data = db('block_data')->alias('d')
                        ->join('product p', 'd.data_object_id = p.product_id')
                        ->where($queryWhere)
                        ->field(['d.data_id', 'd.data_create_time', 'd.data_sort', 'p.product_title'])
                        ->page($param['page'])->limit($param['limit'])->order('d.data_sort', 'asc')->select();
                    break;
                default: break;
            }

            foreach ($data as $key => $item) {
                $data[$key]['block_title'] = $block['block_title'];
            }

            $this->response['data'] = $data;
            return $this->response;
        } else {
            // 不同的模块使用不同的模板
            $templet = '';
            switch ($block['block_ident']) {
                case 'column_index': $templet = 'column_index'; break;
                case 'product_index': $templet = 'product_index'; break;
                default: $templet = '';
            }

            return view($templet, [
                'bid' => $param['bid'],
            ]);
        }
    }

    /**
     * 绑定数据
     * @return \think\response\View
     */
    public function batch() {
        if (request()->isAjax()) {
            $param = input('param.');
            $selectIds = explode(',', $param['selectIds']);
            $saveData = [];
            foreach ($selectIds as $kery => $value) {
                $saveData[] = [
                    'data_block_id' => $param['bid'],
                    'data_object_id' => $value,
                    'data_create_time' => time(),
                ];
            }

            // 绑定数据
            $blockData = new BlockData();
            $result = $blockData->allowField(true)->saveAll($saveData);

            if (!$result) {
                $this->response['code'] = -1;
                $this->response['msg'] = '操作失败';
            }

            // 更新缓存
            $this->updateBlockCache();

            return $this->response;
        }
    }

    public function bindColumn() {
        $param = input('param.');
        if (request()->isAjax()) {
            $param = input('param.');
            $queryWhere[] = ['column_status', '=', 1];
            if(isset($param['name']) && !empty($param['name'])) {
                $queryWhere[] = ['column_name', 'like', '%'.$param['name'].'%'];
            }
            $this->response['count'] = db('column')->where($queryWhere)->whereNotIn('column_id', $param['bindid'])->count();
            $data = db('column')->where($queryWhere)->whereNotIn('column_id', $param['bindid'])->page($param['page'])->limit($param['limit'])->order('column_sort', 'desc')->select();

            $this->response['data'] = $data;
            return $this->response;
        } else {
            // 获取绑定的数据
            $objectId = db('block_data')->where(['data_block_id' => $param['bid']])->column('data_object_id');
            return view('', [
                'bindid' => implode(',', $objectId),
            ]);
        }
    }

    public function bindProduct() {
        $param = input('param.');
        if (request()->isAjax()) {
            $param = input('param.');
            $queryWhere[] = ['product_status', '=', 1];
            if(isset($param['name']) && !empty($param['name'])) {
                $queryWhere[] = ['product_title', 'like', '%'.$param['name'].'%'];
            }
            $this->response['count'] = db('product')->where($queryWhere)->whereNotIn('product_id', $param['bindid'])->count();
            $data = db('product')->where($queryWhere)->whereNotIn('product_id', $param['bindid'])->page($param['page'])->limit($param['limit'])->order('product_id', 'desc')->select();

            $this->response['data'] = $data;
            return $this->response;
        } else {
            // 获取绑定的数据
            $objectId = db('block_data')->where(['data_block_id' => $param['bid']])->column('data_object_id');
            return view('', [
                'bindid' => implode(',', $objectId),
            ]);
        }
    }

    /**
     * 删除数据
     * @return array
     */
    public function delete() {
        if (request()->isAjax()) {
            $param = request()->only('did');
            db('block_data')->where(['data_id' => $param['did']])->delete();

            // 更新缓存
            $this->updateBlockCache();

            return $this->response;
        }
    }

    /**
     * 排序
     * @return array
     */
    public function sort() {
        if (request()->isAjax()) {
            $param = request()->only(['did', 'sort']);
            db('block_data')->where(['data_id' => $param['did']])->update(['data_sort' => $param['sort']]);

			// 更新缓存
			$this->updateBlockCache();

            return $this->response;
        }
    }


}