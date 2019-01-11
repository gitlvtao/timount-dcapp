<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/8
 * Time: 16:36
 */

namespace app\screen\controller;


use app\admin\controller\Base;
use app\screen\model\ProductAttrConfig as model;

class Screen extends Base
{
	/**
	 * 筛选菜单展示
	 * @return array|\think\response\View
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function index()
	{
		if(request()->isAjax()){
			$menu = [];
			$data = db("product_attr_config")->where("config_status","<>",-1)->select();
			foreach ($data as $key => $value){
				$menu[] = [
					'id'    => $value['config_id'],
					'pid'   => $value['config_pid'] ? : -1,
					'name' => $value['config_title']
				];
			}
			$count = db("product_attr_config")->where("config_status","<>",-1)->count();
			return ['code'=>0,'msg'=>'获取成功!','data'=>$menu,'count'=>$count,'rel'=>1];exit();
		}
		return view();
	}

	/**
	 * 新增|编辑 筛选项
	 * @return \think\response\View
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function addScreen()
	{
		$id = input('param.aid');
		$model = new model();
		if(request()->isAjax()){
			$upwhere = [];
			$data['config_title'] = input('param.name');
			$data['config_pid']   = input('param.pid');
			if (input('param.id')) {
				$upwhere = ['config_id' => input('param.id')];
			}
			$res = $model->save($data,$upwhere);
			if($res){
				$result['code'] = 0;
				$result['msg']  = '操作成功';
			}else{
				$result['code'] = -1;
				$result['msg']  = '操作失败';
			}
			return $result;
		}
		$info = ['config_pid'=>'','config_title'=>''];
		$screen = db("product_attr_config")->where("config_pid",0)->select();
		if(!empty($id)){
			$info = db("product_attr_config")->where("config_id",$id)->find();
		}
		return view('addscreen',['screen'=>$screen,'info'=>$info]);
	}

	/**
	 * 删除筛选项
	 * @return mixed
	 */
	public function delScreen()
	{
		$data = input('param.');
		$res = db("product_attr_config")->where("config_id",$data['aid'])->update(["config_status"=>$data['status']]);
		if($res){
			$result['code'] = 0;
			$result['msg']  = '操作成功';
		}else{
			$result['code'] = -1;
			$result['msg']  = '操作失败';
		}
		return $result;
	}

}