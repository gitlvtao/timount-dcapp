<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/9
 * Time: 14:08
 */

namespace app\notice\controller;


use app\admin\controller\Base;

class Notice extends Base
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
			$list = db('notice')
				->where('notice_title', 'like', "%" . $key . "%")
				->where("notice_status",'<>',-1)
				->order("notice_create_time desc")
				->paginate(array('list_rows'=>$pageSize,'page'=>$page))
				->toArray();

			foreach ($list['data'] as $key => $value){
				$list['data'][$key]['notice_create_time'] = date("Y-m-d H:i:s",$value['notice_create_time']);
			}

			return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];exit();
		}
		return view();
	}

	/**
	 * 添加消息
	 * @return array|\think\response\View
	 */
	public function addNotice()
	{
		if(request()->isPost()){
			$data['notice_title']       = input('param.notice_title');
			$data['notice_short_title']     = input('param.notice_short_title');
			$data['notice_content']     = input('param.notice_content');
			$data['notice_create_time'] = time();
			$res = db("notice")->insert($data);
			if ($res){
				return ['code'=>1,'msg'=>'添加成功!','url'=>url('index')];
			}else{
				return ['code'=>-1,'msg'=>'添加失败!'];
			}
		}
		return view("addNotice");
	}

	/**
	 * 消息编辑
	 * @return array|\think\response\View
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 */
	public function editNotice()
	{
		if (request()->isPost()){
			$id = input('param.id');
			$data['notice_title']       = input('param.notice_title');
			$data['notice_short_title']     = input('param.notice_short_title');
			$data['notice_content']     = input('param.notice_content');
			$res = db("notice")->where("notice_id",$id)->update($data);
			if ($res){
				return ['code'=>1,'msg'=>'编辑成功!','url'=>url('index')];
			}else{
				return ['code'=>-1,'msg'=>'编辑失败!'];
			}
		}
		$id = input('param.id');
		$info = db("notice")->where("notice_id",$id)->find();
		return view('editNotice',['info'=>$info]);
	}

	/**
	 * 删除消息操作
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function delNotice()
	{
		$id = input('param.id');
		db("notice")->where("notice_id",$id)->update(['notice_status'=>-1]);
		return ['code'=>1,'msg'=>'操作成功!'];
	}

}