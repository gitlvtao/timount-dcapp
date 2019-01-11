<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/13
 * Time: 16:08
 */

namespace app\notice\controller;

use app\common\controller\Api;
use app\notice\validate\Notice as noticeValidate;

class Noticeapi extends Api
{
	/**
	 * 消息列表
	 * @param $type  1-新消息  2-已读
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function notice()
	{
		$param = request()->param('data');
		$validate = new noticeValidate();
		if (!$validate->scene('notice')->check($param)){
			$this->response['code'] = 0;
			$this->response['msg'] = $validate->getError();
			return $this->response;exit();
		}
		$uid  = $param['userId'];
		$type = $param['type'];
		$where = [];
		if ($type == 1){
			$msg = db("bind_user_notice")->where("bind_user_id",$uid)->column("bind_notice_id");
			if (!empty($msg)){
				$where[] = ['notice_id',"notin",$msg];
			}
			$this->response['response'] = db("notice")->where($where)->field("notice_id,notice_title,notice_short_title,notice_create_time")->order('notice_create_time desc')->select();
		}else{
			$this->response['response'] = "";
			$msg = db("bind_user_notice")->where("bind_user_id",$uid)->where("bind_type",1)->column("bind_notice_id");
			if (!empty($msg)){
				$where[] = ['notice_id',"in",$msg];
				$this->response['response'] = db("notice")->where($where)->field("notice_id,notice_title,notice_short_title,notice_create_time")->order('notice_create_time desc')->select();
			}
		}
		return $this->response;
	}

	/**
	 * 消息用户操作
	 * @param $type 操作类型  1-已读  2- 删除
	 * @param $ids 数组集(字符串  切割转数组)
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function delNotice()
	{
		$param = request()->param('data');

		$validate = new noticeValidate();
		if (!$validate->scene('del')->check($param)){
			$this->response['code'] = 0;
			$this->response['msg'] = $validate->getError();
			return $this->response;exit();
		}
		$uid  = $param['userId'];
		$type = $param['type'];
		if (empty($param['data'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误';
			return $this->response;exit();
		}
		$ids  = explode(',',$param['data']);

		$where = [];
		switch ($type){
			case 1:
				//消息数据过滤
				$ids_1 = db("bind_user_notice")->where("bind_user_id",$uid)->where("bind_type",1)->column("bind_notice_id");
				if (!empty($ids_1)){
					//去重（是否已读过的）
					$ids_2 = array_diff($ids,$ids_1);
					if (empty($ids_2)){
						//为空已操作  直接返回成功
						return $this->response;exit();
					}
					$ids_new = array_values($ids_2);
				}else{
					$ids_new = $ids;
				}
				//操作消息--已读
				foreach ($ids_new as $key=>$value){
					$data[$key]['bind_user_id']     = $uid;
					$data[$key]['bind_notice_id']   = $value;
					$data[$key]['bind_type']        = 1;
					$data[$key]['bind_create_time'] = time();
				}
				$res = db("bind_user_notice")->insertAll($data);
				break;
			case 2:
				//消息数据过滤
				$ids_1 = db("bind_user_notice")->where("bind_user_id",$uid)->where("bind_type",2)->column("bind_notice_id");
				if (!empty($ids_1)){
					//去重（是否已读过的）
					$ids_2 = array_diff($ids,$ids_1);
					if (empty($ids_2)){
						//为空已操作  直接返回成功
						return $this->response;exit();
					}
					$ids_new = array_values($ids_2);
				}else{
					$ids_new = $ids;
				}
				//操作消息--删除 (未读直接删除)
				foreach ($ids_new as $key=>$value){
					$data[$key]['bind_user_id']     = $uid;
					$data[$key]['bind_notice_id']   = $value;
					$data[$key]['bind_type']        = 2;
					$data[$key]['bind_update_time'] = time();
				}
				$res = db("bind_user_notice")->insertAll($data);
				break;
			default:
				//消息数据过滤
				$ids_1 = db("bind_user_notice")->where("bind_user_id",$uid)->where("bind_type",2)->column("bind_notice_id");
				if (!empty($ids_1)){
					//去重（是否已读过的）
					$ids_2 = array_diff($ids,$ids_1);
					if (empty($ids_2)){
						//为空已操作  直接返回成功
						return $this->response;exit();
					}
					$ids_new = array_values($ids_2);
				}else{
					$ids_new = $ids;
				}
				//操作消息--删除 (已读直接删除)
				$where[] = ['bind_user_id','=',$uid];
				$where[] = ['bind_notice_id','in',$ids_new];
				$res = db("bind_user_notice")->where($where)->update(['bind_type'=>2,'bind_update_time'=>time()]);
				break;
		}
		if (!$res){
			$this->response['code'] = 'error';
			$this->response['msg']  = '操作失败!';
		}
		return $this->response;
	}


	/**
	 * 消息详情
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function noticeInfo()
	{
		$param = request()->param('data');

		$validate = new noticeValidate();
		if (!$validate->scene('info')->check($param)){
			$this->response['code'] = 0;
			$this->response['msg'] = $validate->getError();
			return $this->response;exit();
		}
		$id = $param['id'];
		$info = db("notice")->where("notice_id",$id)->find();
		$this->response['response'] = $info;

		return $this->response;
	}

}