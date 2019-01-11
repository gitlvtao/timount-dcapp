<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/12/4
 * Time: 17:30
 */

namespace app\jpush\controller;


use app\admin\controller\Base;
use fuk\JgPush;

use app\jpush\model\PushNotice;

class Push extends Base
{
	/**
	 * 首页列表
	 * @return array|\think\response\View
	 */
	public function index()
	{
		if(request()->isPost()){
			$key = input('post.key');
			$this->assign('testkey', $key);
			$page =input('page')?input('page'):1;
			$pageSize =input('limit')?input('limit'):config('pageSize');
			$list = db('push_notice')
					->where('push_title', 'like', "%" . $key . "%")
					->where("push_status",'<>',-1)
					->order("push_id desc")
					->paginate(array('list_rows'=>$pageSize,'page'=>$page))
					->toArray();

			foreach ($list['data'] as $key=>$value){
				$list['data'][$key]['time'] = date("Y-m-d H:i:s",$value['push_update_time']);
				$list['data'][$key]['sendTime'] = date("Y-m-d H:i:s",$value['push_send_time']);
				if ($value['push_send_time'] == 0){
					$list['data'][$key]['sendTime'] = "未推送";
				}

				$list['data'][$key]['push_is_on'] = "否";
				if ($value['push_is_on'] == 1){
					$list['data'][$key]['push_is_on'] = "是";
				}
			}
			return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];exit();
		}
		return view();
	}

	/**
	 * 添加推送消息
	 * @return \think\response\View
	 */
	public function addPush()
	{
		if (request()->isPost()){
			//数据写入数据库
			$param = input('param.');

			$time = [0,0];
			if (!empty($param['push_start']) || !isset($param['push_start'])){
				$time = explode("~",$param['push_start']);
			}
			$saveData = [
				'push_title'       => $param['push_title'],
				'push_content'     => $param['push_content'],
				'push_create_time' => time(),
				'push_update_time' => time(),
				'push_is_on'       => $param['push_is_on'],
				'push_start'       => trim($time[0]),
				'push_end'         => trim($time[1]),
				'push_time'        => $param['push_time'],
			];

			$model = new PushNotice();
			$res = $model->allowField("true")->insert($saveData);
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
		}else{
			return view("addPush");
		}
	}

	/**
	 * 编辑消息推送
	 * @return \think\response\View
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function editPush()
	{
		if (request()->isPost()){
			$param = input('param.');
			$param['push_update_time'] = time();
			$time = [0,0];
			if (!empty($param['push_start']) || !isset($param['push_start'])){
				$time = explode("~",$param['push_start']);
			}
			$saveData = [
				'push_id'          => $param['push_id'],
				'push_title'       => $param['push_title'],
				'push_content'     => $param['push_content'],
				'push_update_time' => time(),
				'push_is_on'       => $param['push_is_on'],
				'push_start'       => trim($time[0]),
				'push_end'         => trim($time[1]),
				'push_time'        => $param['push_time'],
			];
			$res = db("push_notice")->update($saveData);
			if($res){
				$result['code'] = 1;
				$result['msg'] = '编辑成功!';
				$result['url'] = url('index');
				return $result;
			}else{
				$result['code'] = -1;
				$result['msg'] = '编辑失败!';
				return $result;
			}
		}else{
			$id = input('param.id');
			$data = db("push_notice")->where("push_id",$id)->find();
			$data['push_start_end'] = $data['push_start']." ~ ".$data['push_end'];
			return view("editPush",['data'=>$data]);
		}
	}

	/**
	 * 删除通知消息
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function delPush()
	{
		$data = [
			'push_status' => -1,
			'push_update_time' => time()
		];
		$reg =  db("push_notice")->where("push_id",input('param.id'))->update($data);
		if ($reg){
			$this->delPushId(input('param.id'));
		}
		return ['code'=>1,'msg'=>'删除成功'];
	}

	/**
	 * 消息推送
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 */
	public function Push()
	{
		$id = input('param.id');
		$data = db("push_notice")->where("push_id",$id)->find();
		//发送前  删除原先的定时任务
		if ($data['push_is_on'] == 1 && (!empty($data['push_schedule_id'])|| isset($data['push_schedule_id']))){
			$this->delPushId($id);
		}
		$jPush = new JgPush();
		$res = $jPush->sendPush($data);
		if ($res){
			$saveData = [
				'push_send_time'=>time(),
				'push_schedule_id' => $res['body']['schedule_id']?$res['body']['schedule_id']:$data['push_schedule_id'],
			];
			db("push_notice")->where("push_id",$id)->update($saveData);
			return ['code'=>1,'msg'=>'推送成功!'];
		}else{
			return ['code'=>-1,'msg'=>'推送失败!'];
		}
	}

	/**
	 * 删除定时推送任务
	 * @param $id push表id
	 * @return array|bool
	 */
	public function delPushId($id)
	{
		$delScheduleId = db("push_notice")->where("push_id",$id)->value("push_schedule_id");
		if (empty($delScheduleId)){
			return false;exit();
		}
		$jPush = new JgPush();
		$res = $jPush->delSchedule($delScheduleId);
		return $res;
	}

	//获取定时任务列表
	public function list()
	{
		$jPush = new JgPush();
		$data = $jPush->listPush();

		dump($data);exit();
	}

	//删除指定定时任务
	public function delete()
	{
		$id = "d22eca0a-f905-11e8-be49-0021f6b55802";
		$jPush = new JgPush();
		$data = $jPush->delete($id);

		dump($data);exit();
	}

	//获取推送送达情况
	public function pushSuccess()
	{
		$id = "1270189303";
		$jPush = new JgPush();
		$data = $jPush->success($id);

		dump($data);exit;
	}

}