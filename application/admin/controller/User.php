<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/6
 * Time: 16:53
 */

namespace app\admin\controller;


class User extends Base
{
	/**
	 * 用户管理列表
	 * @param $key 查询信息
	 * @param $page 分页数据
	 * @param $pageSize 分页显示的数据
	 * @return array|\think\response\View
	 * @throws \think\exception\DbException
	 */
	public function index()
	{
		if(request()->isPost()){
			$key = input('param.key');
			$page = input('param.page')?input('param.page'):1;
			$pageSize =input('param.limit')?input('param.limit'):config('pageSize');
			$list = db("user")->where("user_nickname|user_name","like","%".$key."%")
					->paginate(array('list_rows'=>$pageSize,'page'=>$page))
					->toArray();
			foreach ($list['data'] as $key => $value){
				$list['data'][$key]['time'] = date("Y-m-d H:i:s",$value['user_create_time']);
			}

			return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];exit();
		}
		return view("index");
	}

	/**
	 * @param $param 用户id
	 * @param $type  操作类型   1-冻结，2-恢复
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function userDel()
	{
		$param = input('param.uid');
		$type  = input('param.type');
		if($type == 1){
			$update['user_status'] = -1;
		}else{
			$update['user_status'] = 1;
		}
		db("user")->where("user_id","=",$param)->update($update);
		return ['code'=>1,'msg'=>'操作成功！'];
	}

}