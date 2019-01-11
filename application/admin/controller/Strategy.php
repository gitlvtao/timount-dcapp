<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/10/29
 * Time: 13:51
 */

namespace app\admin\controller;


use think\Request;
use app\admin\model\Strategy as model;

class Strategy extends Base
{
	/**
	 * 首页展示
	 * @return array|\think\response\View
	 */
	public function index()
	{
		if (request()->isPost()){
			$key = input('param.key');
			$page = input('param.page')?input('param.page'):1;
			$pageSize =input('param.limit')?input('param.limit'):config('pageSize');
			$type = input('param.type')?input('param.type'):0;

			$limit = ($page-1)*$pageSize;
			$list['total'] = db("strategy")->count();
			switch ($type){
				case 1:
					$order = "B.num DESC";
					break;
				case 2:
					$order = "A.strategy_love DESC";
					break;
				default:
					$order = "A.strategy_id DESC";
					break;
			}
			$sql = "select A.*,B.num from dcapp_strategy A left join(select count(discuss_id) as num,discuss_strategy_id from dcapp_discuss where discuss_status <> -1 group by discuss_strategy_id ) B on B.discuss_strategy_id = A.strategy_id order by $order limit $limit,$pageSize";
			$list['data']  = db()->query($sql);

			foreach ($list['data'] as $key => $value){
				$list['data'][$key]['time'] = date("Y-m-d H:i:s",$value['strategy_create_time']);
				if(!$value['num']){
					$list['data'][$key]['num'] = 0;
				}
			}
			return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
		}
		return view();
	}

	/**
	 * 攻略添加
	 * @return \think\response\View
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function addStrategy()
	{
		if(request()->isPost()){
			$data = input('param.');
			$data['strategy_create_time'] = time();
			$model = new model();
			$res = $model->allowField(true)->save($data);
			if($res){
				$result['code'] = 1;
				$result['msg'] = '添加成功!';
				$result['url'] = url('index');
			}else{
				$result['code'] = -1;
				$result['msg'] = '添加失败!';
			}
			return $result;
		}
		$column = db('product')->where("product_status","<>","-1")->field("product_id,product_name")->select();
		$option = "";
		foreach ($column as $value){
			$option .= '<option value="'.$value['product_id'].'">'.$value['product_name']."</option>";
		}
		return view('strategyAdd',['data'=>[],'option'=>$option,'strategy'=>0]);
	}

	/**
	 * 攻略编辑
	 * @return array|\think\response\View
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 */
	public function editStrategy()
	{
		if(request()->isPost()){
			$data = input('param.');
			unset($data['file']);
			$res = db('strategy')->update($data);
			if ($res){
				$result['code'] = 1;
				$result['msg']  = '操作成功!';
				$result['url'] = url('index');
			}else{
				$result['code'] = 1;
				$result['msg']  = '操作成功!';
			}
			return $result;exit();
		}
		$strategy_id = input('param.id');
		if(empty($strategy_id)){
			return ['code'=>0,'msg'=>'参数错误!'];exit();
		}
		$data = db('strategy')->where("strategy_id",$strategy_id)->find();
		$media_list = db('product')->where("product_status","<>","-1")->field("product_id,product_name")->select();
		$option = "";
		foreach ($media_list as $value){
			if($value['product_id'] == $data['strategy_product_id']){
				$option .= '<option value="'.$value['product_id'].'" selected = "selected">'.$value['product_name']."</option>";
			}else{
				$option .= '<option value="'.$value['product_id'].'">'.$value['product_name']."</option>";
			}
		}
		return view("strategyAdd",['data'=>$data,'option'=>$option,'strategy'=>$data['strategy_id']]);
	}

	/**
	 * 操作属性
	 * @return array
	 */
	public function operation()
	{
		$model = new model();
		$param = input('param.');
		$result = $model->updateType($param['data'],$param['type']);
		return $result;
	}

	/**
	 * 攻略排序
	 * @return mixed
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function sortStrategy()
	{
		$param = input('param.');
		$res = db("strategy")->where("strategy_id","=",$param['id'])->update(['strategy_sort'=>$param['sort']]);
		if ($res){
			$result['code'] = 1;
			$result['msg']  = '操作成功!';
		}else{
			$result['code'] = -1;
			$result['msg']  = '操作失败!';
		}
		return $result;
	}

	/**
	 * 评论列表
	 * @return \think\response\View
	 * @throws \think\exception\DbException
	 */
	public function discuss()
	{
		$param = input('param.id');
		if (request()->isPost()){
			$key = input('param.key');
			$page = input('param.page')?input('param.page'):1;
			$pageSize =input('param.limit')?input('param.limit'):config('pageSize');
			$discuss  = db("discuss")->where([["discuss_strategy_id","=",$param],['discuss_status',">",-1]])
						->paginate(array('list_rows'=>$pageSize,'page'=>$page))
						->toArray();
			$user = db("user")->field("user_id,user_nickname")->select();
			foreach ($discuss['data'] as $key => $value){
				$discuss['data'][$key]['time'] = date("Y-m-d H:i:s",$value['discuss_create_time']);
				foreach ($user as $k => $v){
					if($v['user_id'] == $value['discuss_user_id']){
						$discuss['data'][$key]['user'] = $v['user_nickname'];
					}
				}
			}

			return ['code'=>0,'msg'=>'获取成功!','data'=>$discuss['data'],'count'=>$discuss['total'],'rel'=>1];exit();
		}

		$strategy = db("strategy")->where("strategy_id","=",$param)
			->field("strategy_product_id,strategy_title,strategy_content,strategy_img,strategy_create_time")
			->find();
		$product  = db("product")->where("product_id","=",$strategy['strategy_product_id'])
			->field("product_name,product_max_amount,product_term")->find();

		$this->assign('strategys',$strategy);
		$this->assign('list',$product);
		return view("discuss",['id'=>$param]);exit;

	}

	/**
	 * 删除评论
	 * @return mixed
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function delDiscuss()
	{
		$param = input('param.id');
		$res = db("discuss")->where("discuss_id","=",$param)->update(['discuss_status'=>-1]);
		if($res){
			$result['code'] = 1;
			$result['msg']  = '操作成功!';
		}else{
			$result['code'] = -1;
			$result['msg']  = '操作失败!';
		}
		return $result;
	}

}