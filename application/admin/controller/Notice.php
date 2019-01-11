<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/10/30
 * Time: 13:51
 */

namespace app\admin\controller;

use think\facade\Log;

class Notice extends Base
{
	/**
	 * 公告列表
	 * @return array|\think\response\View
	 */
	public function index(){
		if(request()->isPost()){
			$key = input('post.key');
			$this->assign('testkey', $key);
			$page =input('page')?input('page'):1;
			$pageSize =input('limit')?input('limit'):config('pageSize');
			$list = db("notice")
				->where('notice_title', 'like', "%" . $key . "%")
				->where("notice_status","<>",'-1')
				->paginate(array('list_rows'=>$pageSize,'page'=>$page))
				->toArray();
			return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];

		}
		return view();
	}

	/**
	 * 公告添加
	 * @return \think\response\View
	 */
	public function addNotice(){
		if(request()->isPost()){
			$data = input('param.');
			$addData['notice_title']       = $data['title'];
			$addData['notice_content']     = $data['content'];
			$addData['notice_url']         = $data['url'];
			$addData['notice_create_time'] = time();
			$res = db("notice")->insert($addData);
			if($res){
				Log::write('测试日志信息');
				$result['code'] = 1;
				$result['msg']  = '添加成功';
				$result['url']  = url('index');
			}else{
				$result['code'] = -1;
				$result['msg']  = '添加失败';
			}
			return $result;
		}
		return view("noticeAdd",['data'=>[],'notice_id'=>0]);
	}

	/**
	 * 公告修改
	 * @return \think\response\View
	 */
	public function editNotice(){
		if(request()->isPost()){
			$data = input('param.');
			if(isset($data['id']) && !empty($data['id'])){
				$ediDate['notice_id'] = $data['id'];
				$ediDate['notice_title'] = $data['title'];
				$ediDate['notice_content'] = $data['content'];
				$ediDate['notice_url'] = $data['url'];
				$res = db("notice")->update($ediDate);
			}else{
				$result['code'] = -2;
				$result['msg']  = '操作失败';
				return $result;exit();
			}
			if($res){
				$result['code'] = 1;
				$result['msg']  = '修改成功';
				$result['url']  = url('index');
			}else{
				$result['code'] = -1;
				$result['msg']  = '修改失败';
			}
			return $result;
		}
		$notice_id = input('param.id');
		$data = db("notice")->where("notice_id",$notice_id)->find();
		return view("noticeAdd",['data'=>$data,'notice_id'=>$notice_id]);
	}

	/**
	 * 公告删除
	 * @return array
	 */
	public function delNotice(){
		$data['notice_lower_time'] = time();
		$data['notice_status']     = -1;
		db('notice')->where("notice_id",input('param.id'))->update($data);
		return ['code'=>1,'msg'=>'删除成功！'];
	}

	/**
	 * 公告发布
	 * @return array
	 */
	public function relNotice(){
		$data['notice_release_time'] = time();
		$data['notice_status']       = 2;
		db('notice')->where("notice_id",input('param.id'))->update($data);
		return ['code'=>1,'msg'=>'删除成功！'];
	}

}