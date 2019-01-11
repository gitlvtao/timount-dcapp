<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use \app\admin\model\Column as model;

class Column extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(request()->isPost()){
            $key = input('post.key');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = db('column')
                ->where('column_name', 'like', "%" . $key . "%")
				->where("column_status",'<>',-1)
                ->order("column_id asc")
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();

            return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];exit();
        }
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function columnAdd()
    {
        if(request()->isPost()){
            $param = input('param.');
            $param['column_create_time'] = time();
            $model = new model();
            $res = $model->allowField(true)->save($param);
            if($res){
                $result['code'] = 1;
                $result['msg'] = '添加成功!';
                $result['url'] = url('index');
                return $result;
            }else{
                $result['code'] = -1;
                $result['msg'] = '添加失败!';
                return $result;
            }
        }
        $this->assign('column_list','');
        $this->assign('column_id','null');
        return view("columnAdd");
    }

    /**
     * 显示编辑资源表单页.
     *
     * @return \think\Response
     */
    public function columnEdit()
    {
		$model = new model();
        if(request()->isPost()){
            $param = input('param.');
            $res = $model->allowField(true)->save($param,['column_id'=>$param['column_id']]);
            if($res){
                $result['code'] = 1;
                $result['msg'] = '编辑成功!';
                cache('adList', NULL);
                $result['url'] = url('index');
                return $result;
            }else{
                $result['code'] = -1;
                $result['msg'] = '编辑失败!';
                return $result;
            }
        }
        $column_id = input('param.column_id');
        $column_list = $model->where("column_id",$column_id)->find();
        return view("columnAdd",['column_list'=>$column_list,'column_id'=>$column_id,'column_image'=>json_encode($column_list,true)]);
    }

    /**
     * 删除指定资源
     *
     * @return \think\Response
     */
    public function columnDel()
    {
        db('column')->where('column_id',input('param.column_id'))->update(['column_status'=>'-1']);
        return ['code'=>1,'msg'=>'删除成功！'];
    }

    /**
     * 模块排序
     * @return mixed
     */
    public function sort(){
        $data = input('param.');
        $res = db("column")->update($data);
        if($res){
            $result['code'] = 1;
            $result['msg']  = '操作成功';
            return $result;
        }else{
            $result['code'] = 0;
            $result['msg']  = '操作失败';
            return $result;
        }
    }

}
