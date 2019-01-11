<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/13
 * Time: 14:27
 */

namespace app\user\controller;

use app\admin\controller\Base;
use fuk\Excel;

class User extends Base
{
	/**
	 * 用户列表
	 * @return array|\think\response\View
	 * @throws \think\exception\DbException
	 */
	public function index()
	{
		if(request()->isPost()){
			$key = input('post.key');
			$this->assign('testkey', $key);
			$page =input('page')?input('page'):1;
			$pageSize =input('limit')?input('limit'):config('pageSize');
			$list = db('users')
				->where('user_nickname|user_mobile', 'like', "%" . $key . "%")
				->order("user_id desc")
				->paginate(array('list_rows'=>$pageSize,'page'=>$page))
				->toArray();
			$phone = db("users")->field("user_id,user_pid,user_mobile")->select();
			foreach ($list['data'] as $key => $value){
				$list['data'][$key]['user_sex'] = '保密';
				if ($value['user_sex'] == 1){
					$list['data'][$key]['user_sex'] = '男';
				}elseif ($value['user_sex'] == 1){
					$list['data'][$key]['user_sex'] = '女';
				}
				$list['data'][$key]['user_reg_time'] = date("Y-m-d H:i:s",$value['user_reg_time']);
				$list['data'][$key]['user_login_time'] = date("Y-m-d H:i:s",$value['user_login_time']);
				$list['data'][$key]['count'] = 0;
				$list['data'][$key]['platform'] = "自己";
				if (!empty($phone)){
					foreach ($phone as $k => $v){
						if ($value['user_id'] == $v['user_pid']){
							$list['data'][$key]['count'] += 1;
						}
						if ($value['user_pid'] == $v['user_id']){
							$list['data'][$key]['platform'] = $v['user_mobile'];
						}
					}
				}
			}
			return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];exit();
		}
		return view();
	}

	/**
	 * @param $param 用户id
	 * @param $type  操作类型   1-冻结，2-恢复
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function delUser()
	{
		$param = input('param.id');
		$type  = input('param.type');
		if($type == 1){
			$update['user_status'] = -1;
		}else{
			$update['user_status'] = 1;
		}
		db("users")->where("user_id","=",$param)->update($update);
		return ['code'=>1,'msg'=>'操作成功！'];
	}

	/**
	 * 导出excel表格
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function phpExcel()
	{
		$param = input('param.time');
		$data = db("users")->field("user_id,user_pid,user_mobile")->select();
		$list = [];
		foreach ($data as $key => $value){
			if ($value['user_pid'] > 0){
				$list[] = $value;
			}
		}
		foreach ($list as $k => $v){
			foreach ($data as $n => $m){
				if ($v['user_pid'] == $m['user_id']){
					$list[$k]['platform'] = $m['user_mobile'];
				}
			}
		}
		$phpExcel = new Excel();
		$phpExcel->daoChu($list);
	}



}