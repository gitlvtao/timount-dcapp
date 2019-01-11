<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/13
 * Time: 15:13
 */

namespace app\goods\controller;

use app\common\controller\Api;

use app\goods\validate\Goods as validateGoods;
use think\facade\Log;

class Goodsapi extends Api
{
	/**
	 * 收藏产品接口
	 * @return array
	 */
	public function collect()
	{
		$param = request()->param('data');
		$validate = new validateGoods();
		if (!$validate->scene('browse')->check($param)){
			$this->response['code'] = 'error';
			$this->response['msg']  = $validate->getError();
			return $this->response;exit();
		}
		$id = $param['collect_id'];
		if($id == 1){
			$saveData = [
				'collect_user_id' => $param['userId'],
				'collect_object_id' => $param['productId'],
				'collect_type' => 1,
			];
			$id = db("collect_log")->where($saveData)->value("collect_id");
			if(!$id){
				$saveData['collect_create_time'] = time();
				db('collect_log')->insert($saveData);
			}else{
				$this->response['code'] = 'error';
				$this->response['msg']  = '该产品已收藏';
			}
		}else{
			$saveData = [
				'collect_user_id' => $param['userId'],
				'collect_object_id' => $param['productId'],
				'collect_type' => 1,
			];
			$res = db("collect_log")->where($saveData)->delete();
			if (!$res){
				$this->response['code'] = 'error';
				$this->response['msg']  = '操作失败';
			}
		}
		return $this->response;
	}

	/**
	 * 用户浏览
	 * @return array
	 */
	public function browse()
	{
		$param = request()->param('data');

		$validate = new validateGoods();
		if (!$validate->scene('browse')->check($param)){
			$this->response['code'] = 'error';
			$this->response['msg']  = $validate->getError();
			return $this->response;exit();
		}
		$data['view_user_id']     = $param['userId'];
		$data['view_object_id']   = $param['productId'];
		$data['view_type']        = 1;
		$data['view_create_time'] = time();
		$view_id = db("view_log")->where([['view_user_id','=',$data['view_user_id']],['view_object_id','=',$data['view_object_id']],['view_type','=',1]])->value("view_id");
		if (empty($view_id)){
			db("view_log")->insert($data);
		}else{
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误';
		}
		return $this->response;
	}

	/**
	 * 产品列表
	 * @return array
	 */
	/*public function product()
	{
		$param = request()->param('data');

		$column_id = $param['columnId']; //栏目id
		$uid       = $param['userId'];			//用户id
		$page      = $param['page']?$param['page']:1;     //分页

		$collect = db("collect_log")->where([["collect_user_id","=",$uid],['collect_type',"=",1]])->column("collect_object_id");  //是否收藏
		$column  = db("column")->where("column_id","=",$column_id)->field("column_name,column_banner,column_summary")->find();
		$this->response['response']['column'] = $column;
		if(!empty($column_id)){
			$product = db("product")->field("product_id,product_title,product_logo,product_interest_type,product_interest,product_term,product_max_amount,product_tag,product_url,product_info")->where("product_column_id","=",$column_id)->limit($page-1,10)->select();
			if(empty($product)){
				$this->response['response']['data'] = '';
				return $this->response;exit();
			}
		}else{
			//产品大全  $type = 1 全部产品
			$type      = $param['type'];			// 产品大全（属性）| 热门推荐
			$orders    = $param['order'];		    // 产品大全（搜索条件）-排序
			$tag       = $param['tag'];             //产品大全（搜索）标签
			$limit_range = $param['limit_range'];   //产品大全（搜索）额度范围
			$interest_range = $param['interest_range']; //产品大全（搜索） 利息范围

			if($type == 1){
				$screenList = [];
				//排序筛选
				switch ($orders){
					case 1:
						$order = "product_id desc";
						break;
					case 2:
						$order = "product_max_amount desc";
						break;
					case 3:
						$order = "product_interest asc";
						break;
					default:
						$order = "product_id desc";
						break;
				}
				//条件筛选
				//①标签
				if($tag){
					$screenList[] = ['product_tag',"like","%".$tag."%"];
				}
				//②额度范围
				if($limit_range){
					$screenList[] = ['product_limit_range',"=",$limit_range];
				}
				//③利息范围
				if($interest_range){
					$screenList[] = ['product_interest_range',"=",$interest_range];
				}
				$product = db("product")->field("product_id,product_title,product_logo,product_interest_type,product_interest,product_term,product_max_amount,product_tag,product_url,product_info")->limit($page-1,10)->where($screenList)->order($order)->select();
			}else{
				$product = db("product")->field("product_id,product_title,product_logo,product_interest_type,product_interest,product_term,product_max_amount,product_tag,product_url,product_info")->limit($page-1,10)->where("product_attr","=",27)->select();
			}
		}
		foreach ($product as $key => $value){
			if (empty($collect)){
				$product[$key]['collect'] = 0;
			}else{
				if (in_array($value['product_id'],$collect)){
					$product[$key]['collect'] = 1;
				}
			}
		}
		$this->response['response']['data'] = $product;
		return $this->response;
	}*/

	public function products()
	{
		$start = microtime(true);
		$param = request()->param('data');
		$column_id = $param['columnId']; //栏目id
		$uid       = $param['userId'];			//用户id
		$page      = $param['page']?$param['page']:1;     //分页

		$collect = db("collect_log")->where([["collect_user_id","=",$uid],['collect_type',"=",1]])->column("collect_object_id");
		$column  = db("column")->where("column_id","=",$column_id)->field("column_name,column_banner,column_summary")->find();
		$this->response['response']['column'] = $column;
		$product = [];
		if(!empty($column_id)){
			$product = db("product")->field("product_id,product_title,product_logo,product_interest_type,product_interest,product_term,product_max_amount,product_tag,product_url,product_grant")->where("product_column_id","=",$column_id)->where("product_status",1)->page($page,10)->select();
			if(empty($product)){
				$this->response['response']['data'] = '';
				return $this->response;exit();
			}
		}else{
			//产品大全  $type = 1 全部产品
			$type      = $param['type'];			// 产品大全（属性）| 热门推荐
			if($type == 1){
				$screenList = [];
				//条件筛选 （字符串转数组）
				if(empty($param['screen']) || !isset($param['screen'])){
					$orders    = (empty($param['order']) || !isset($param['order']))?1:$param['order'];		    // 产品大全（搜索条件）-排序
					switch ($orders){
						case 1:
							$order = "product_id desc";
							break;
						case 2:
							$order = "product_max_amount desc";
							break;
						case 3:
							$order = "product_interest asc";
							break;
						default:
							$order = "product_id desc";
							break;
					}
					$product = db("product")->field("product_id,product_title,product_logo,product_interest_type,product_interest,product_term,product_max_amount,product_tag,product_url,product_grant")->where("product_status",1)->order($order)->page($page,10)->select();
				}else{
					$orders    = (empty($param['order']) || !isset($param['order']))?1:$param['order'];		    // 产品大全（搜索条件）-排序
					//排序筛选
					switch ($orders){
						case 1:
							$order = "product_id desc";
							break;
						case 2:
							$order = "product_max_amount desc";
							break;
						case 3:
							$order = "product_interest asc";
							break;
						default:
							$order = "product_id desc";
							break;
					}
					$screen_1    = explode(",",$param['screen']);
					foreach ($screen_1 as $key => $value){
						$screen_data =  explode("_",$value);
						$screen[$key]['parId']   = $screen_data[0];
						$screen[$key]['childId'] = $screen_data[1];
					}
					foreach ($screen as $k => $v){
						switch ($v['parId']){
							case 1: //①标签
								$screenList[] = ['product_tag',"like","%".$v['childId']."%"];
								break;
							case 2: //②额度范围
								$screenList[] = ['product_limit_range',"=",$v['childId']];
								break;
							case 3: //③利息范围
								$screenList[] = ['product_interest_range',"=",$v['childId']];
								break;
							default:
								break;
						}
					}
					$product = db("product")->field("product_id,product_title,product_logo,product_interest_type,product_interest,product_term,product_max_amount,product_tag,product_url,product_grant")->where("product_status",1)->where($screenList)->order($order)->page($page,10)->select();
				}
			}else{
				$product = db("product")->field("product_id,product_title,product_logo,product_interest_type,product_interest,product_term,product_max_amount,product_tag,product_url,product_grant")->where("product_status",1)->where("product_attr","=",27)->page($page,10)->select();
			}
		}
		if (empty($product)){
			$this->response['code'] = 'error';
			$this->response['msg']  = '没有更多数据了';
			return $this->response;exit();
		}
		foreach ($product as $key => $value){
			$product[$key]['product_tag'] = productLabel(explode(',',$value['product_tag']));
			$product[$key]['collect'] = 0;
			if ( !empty($collect) && in_array($value['product_id'],$collect)){
				$product[$key]['collect'] = 1;
			}
		}
		$end = microtime(true);
		Log::write("运行时间为：".($end - $start),'tmy');

		$this->response['response']['data'] = $product;
		return $this->response;
	}


	/**
	 * 贷款测一测跳转产品列表
	 * @return array
	 */
	public function measuring()
	{
		$param = input('param.data');
		if (empty($param['userId']) || !isset($param['userId'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误';
			return $this->response;exit();
		}
		(empty($param['page']) || !isset($param['page']))?1:$param['page'];
		$money = db("loan_test")->where("test_user_id",$param['userId'])->value("test_money");
		$map = [];
		switch ($money){
			case 1:
				$map[] = ['product_max_amount','between','0,2000'];
				break;
			case 2:
				$map[] = ['product_max_amount','between','2000,5000'];
				break;
			case 3:
				$map[] = ['product_max_amount','between','5000,10000'];
				break;
			case 4:
				$map[] = ['product_max_amount','>','10000'];
				break;
			default;
				break;
		}
		$product = db("product")->where($map)->where("product_status",1)->order("product_sort asc")->limit(10)->page($param['page'])->select();
		if (!empty($product)){
			foreach ($product as $key=>$value){
				$product[$key]['product_tag'] = productLabel(explode(',',$value['product_tag']));
			}
		}
		$this->response['response']['data'] = $product;
		return $this->response;
	}


}