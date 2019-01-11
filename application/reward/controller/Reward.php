<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/12/11
 * Time: 16:27
 */

namespace app\reward\controller;


use app\admin\controller\Base;
use app\user\controller\Userapi;
use fuk\Sms;

class Reward extends Base
{
	/**
	 * 奖励列表
	 * @return array|\think\response\View
	 */
	public function index()
	{
		if (request()->isPost()){
			$key = input('post.key');
			$this->assign('testkey', $key);
			$page =input('page')?input('page'):1;
			$pageSize =input('limit')?input('limit'):config('pageSize');
			$list = db("pull_new")->field("pull_phone,pull_type,pull_time,sum(pull_money) as pull_money,pull_code")
					->where('pull_phone', 'like', "%" . $key . "%")
					->group("pull_phone")
					->paginate(array('list_rows'=>$pageSize,'page'=>$page))
					->toArray();
			return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];exit();
		}
		return view();
	}

	/**
	 * 奖励查询
	 * @return array|\think\response\View
	 */
	public function info()
	{
		if (request()->isPost()){
			$key = input('post.key');
			$this->assign('testkey', $key);
			$where = [];
			if (strpos($key,',') !== false){
				$ids = explode(",",$key);
				$where[] = ['user_mobile','in',$ids];
			}else{
				$where[] = ['user_mobile','=',$key];
			}
			$page =input('page')?input('page'):1;
			$pageSize =input('limit')?input('limit'):config('pageSize');

			$users = db("users")->field("user_id,user_pid,user_mobile")->select();
			$data = db("users")->field("user_id,user_pid,user_mobile")
					->where($where)
					->paginate(array('list_rows'=>$pageSize,'page'=>$page))
					->toArray();
			if (empty($data['data'])){
				return ['code'=>0,'msg'=>'获取成功!','data'=>[],'count'=>0,'rel'=>1];exit();
			}
			$list = [];
			foreach ($data['data'] as $k => $v){
				$v['platform'] = "自己";
				array_push($list,$v) ;
				foreach ($users as $key => $value){
					if ($v['user_pid'] == $value['user_id']){
						$value['platform'] = $v['user_mobile'];
						array_push($list,$value) ;
					}
				}
			}
			return ['code'=>0,'msg'=>'获取成功!','data'=>$list,'count'=>count($list),'rel'=>1];exit();
		}
		return view("info");
	}

	/**
	 * 发放奖励
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function sendMoney()
	{
		$param = input('param.id');
		$res = db("pull_new")->where("pull_id",'in',$param)->update(['pull_time'=>time(),'pull_money'=>3]);
		if ($res){
			return ['code'=>1,'msg'=>'操作成功!'];
		}
		return ['code'=>0,'msg'=>'操作失败!'];
	}

	/**
	 * 奖励详情
	 * @return array|\think\response\View
	 */
	public function rewardInfo()
	{
		$phone = input('param.phone');
		if (request()->isPost()){
			$key = input('post.key');
			$this->assign('testkey', $key);
			$page =input('page')?input('page'):1;
			$pageSize =input('limit')?input('limit'):config('pageSize');
			$list = db("pull_new")->field("pull_id,pull_phone,pull_platform,pull_type,pull_money,pull_time,pull_status")
					->where("pull_phone",'=',$phone)
					->where("pull_platform",'like',"%".$key."%")
					->order("pull_time ASC")
					->paginate(array('list_rows'=>$pageSize,'page'=>$page))
					->toArray();
			foreach ($list['data'] as $key => $value){
				$list['data'][$key]['type'] = '注册';
				if ($value['pull_type'] != 1){
					$list['data'][$key]['type'] = '其他';
				}
				$list['data'][$key]['time'] = '未发放';
				if ($value['pull_time'] > 0){
					$list['data'][$key]['time'] = date("Y-m-d H:i:s",$value['pull_time']);
				}
			}
			return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];exit();
		}
		return view('reward',['phone'=>$phone]);
	}


	/**
	 * 删除奖励
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function delReward()
	{
		$id = input('param.id');
		$res = db("pull_new")->where("pull_id",$id)->update(['pull_status'=>-1]);
		if ($res){
			return ['code'=>1,'msg'=>'操作成功!'];exit();
		}
		return ['code'=>-1,'msg'=>'操作失败!'];exit();
	}

	/**
	 * 发送短信
	 * @return array
	 */
	public function sendCode()
	{
		$phone = input('param.phone');
		$code = randStr(8);
		$sms = new Sms();
		$reg = $sms->ali($phone,$code);
		if ($reg){
			db('pull_new')->where("pull_phone",$phone)->update(['pull_code'=>$code]);
			return ['code'=>1,'msg'=>'验证码发送成功!'];
		}
		return ['code'=>-1,'msg'=>'验证码发送失败!'];
 	}

	/**
	 * 奖励手动录入
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
 	public function pull()
	{
		$data = input('param.data');
		$model = new Userapi();
		$param = [
			'phone' => 0,
			'type'  => 0
		];
		foreach ($data as $key => $value){
			$param['phone'] = $value;
			$param['type']  = 1;
			$model->commonPull($param,1);
		}
		return ['code'=>1,'msg'=>'操作成功!'];
	}


}