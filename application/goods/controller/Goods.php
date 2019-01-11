<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18-9-29
 * Time: 上午11:09
 */

namespace app\goods\controller;

use \app\admin\controller\Base;
use \app\goods\model\Product as goodsInfo;

class Goods extends Base
{
    /**
     * 商品列表首页
     * @return array|\think\response\View
     */
    public function index(){
        if(request()->isPost()){
            $key = input('post.key');
            $select = input('param.select');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = db("product")->alias("A")->join("column B","A.product_column_id = B.column_id",'left')
                ->where('A.product_title', 'like', "%" . $key . "%")
                ->where("A.product_status","<>",-1)
				->where('A.product_column_id', 'like', "%" . $select . "%")
				->field("A.*,B.column_name")
                ->order("A.product_sort asc")
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach($list['data'] as $key=>$value){
                $arr = explode(",",$value['product_tag']);
                $list['data'][$key]['product_tag'] = getLable($arr);
                $list['data'][$key]['product_interest'] = ($value['product_interest']*100)."%";
                $list['data'][$key]['product_adopt'] = ($value['product_adopt']*100)."%";
				$list['data'][$key]['product_interest_type'] = "月";
                if($value['product_interest_type'] == 1){
                	$list['data'][$key]['product_interest_type'] = "日";
				}
            }
            return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        $column = db("column")->where("column_type",1)->where("column_status","<>",-1)->field("column_id,column_name")->select();
        return view("index",['column'=>$column]);
    }

    /**
     * 添加商品
     * @return \think\response\View
     */
    public function addGoods(){
        if(request()->isPost()){
            $param = input('param.');
            if(count($param['product_tag']) > 2){
                $result['code'] = -1;
                $result['msg'] = '只能添加两个标签!';
                return $result;exit();
            }
            $param['product_tag'] = implode(",",$param['product_tag']);
            $param['product_create_time'] = time();
            $model = new goodsInfo();
            $res = $model->allowField(true)->save($param);
            if($res){
                $result['code'] = 1;
                $result['msg'] = '添加成功!';
                cache('adList', NULL);
                $result['url'] = url('index');
                return $result;
            }else{
                $result['code'] = -1;
                $result['msg'] = '添加失败!';
                return $result;
            }
        }
        //模块
		$column = db("column")->where("column_status","<>",-1)->where("column_type",1)->field("column_id,column_name")->select();
		//标签
        $lable = getLableList();
        //属性
		$attr = db("product_attr_config")->where("config_pid",24)->where("config_status","<>",-1)->select();
        //额度范围
		$limit_range = db("product_attr_config")->where("config_pid",2)->where("config_status","<>",-1)->select();
		//利息范围
		$interest_range = db("product_attr_config")->where("config_pid",3)->where("config_status","<>",-1)->select();
        return view('goodsInfo',['labelList'=>$lable,'limit_range'=>$limit_range,'interest_range'=>$interest_range,'column'=>$column,'attr'=>$attr]);
    }

    /**
     * 编辑商品
     * @return \think\response\View
     */
    public function editGoods(){
        if(request()->isPost()){
            $param = input('param.');
            if(count($param['product_tag']) > 2){
                $result['code'] = -1;
                $result['msg'] = '只能添加两个标签!';
                return $result;exit();
            }
			$param['product_tag'] = implode(",",$param['product_tag']);
            $model = new goodsInfo();
			$res = $model->allowField(true)->save($param,['product_id'=>$param['product_id']]);
            if($res){
                $result['code'] = 1;
                $result['msg'] = '编辑成功!';
				// 更新缓存
				$this->updateBlockCache();
                $result['url'] = url('index');
            }else{
                $result['code'] = -1;
                $result['msg'] = '编辑失败!';
            }
            return $result;
        }
        $product_id = input('param.product_id');
        $info = db("product")->where("product_id",$product_id)->find();
        $info['product_tag'] = explode(",",$info['product_tag']);
		//模块
		$column = db("column")->where("column_status","<>",-1)->where("column_type",1)->field("column_id,column_name")->select();
		//标签
		$lable = getLableList();
		//属性
		$attr = db("product_attr_config")->where("config_pid",24)->where("config_status","<>",-1)->select();
		//额度范围
		$limit_range = db("product_attr_config")->where("config_pid",2)->where("config_status","<>",-1)->select();
		//利息范围
		$interest_range = db("product_attr_config")->where("config_pid",3)->where("config_status","<>",-1)->select();
		return view('form',['data'=>$info,'labelList'=>$lable,'limit_range'=>$limit_range,'interest_range'=>$interest_range,'column'=>$column,'attr'=>$attr]);
    }

    /**
     * 绑定商品
     * @return mixed
     */
    public function bindGoods(){
        $data = input('param.');
        $product = $data['data'];
        $product_list = db("column_bind_product")
                        ->where(['bind_column_id'=>['=',$data['column']],'bind_status'=>['<>',-1]])
                        ->column("bind_product_id");
        if(!empty($product)){
            $product = array_diff($product,$product_list);
        }
        if(!empty($product)){
            foreach($product as $key=>$value){
                $info[$key]['bind_column_id']   = $data['column'];
                $info[$key]['bind_product_id']  = $value;
                $info[$key]['bind_create_time'] = time();
            }
            $res = db("column_bind_product")->insertAll($info);
            if($res){
                $result['code'] = 1;
                $result['msg'] = '加入成功!';
                cache('adList', NULL);
                $result['url'] = url('index');
                return $result;
            }else{
                $result['code'] = -1;
                $result['msg'] = '加入失败!';
                return $result;
            }
        }else{
            $listInfo = db("column_bind_product")
                        ->where(['bind_column_id'=>['=',$data['column']],'bind_product_id'=>['in',$data['data']]])
                        ->select();
            $arr = [];
            foreach($listInfo as $k=>$v){
                if($v['bind_status'] != 1){
                    $arr[] = $v['bind_id'];
                }
            }
            if(empty($arr)){
                $result['code'] = -1;
                $result['msg'] = '已加入该商品!';
                return $result;exit();
            }
            $res = db("column_bind_product")->where("bind_id",'in',$arr)->update(['bind_status'=>1]);
            if($res){
                $result['code'] = 1;
                $result['msg'] = '加入成功!';
                cache('adList', NULL);
                $result['url'] = url('index');
                return $result;
            }else{
                $result['code'] = -1;
                $result['msg'] = '加入失败!';
                return $result;
            }

        }

    }

	/**
	 * 排序
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function sort()
	{
		$id   = input('param.id');
		$sort = input('param.sort');
		$res = db("product")->where("product_id",$id)->update(['product_sort'=>$sort]);
		if ($res){
			return ['code'=>1,'msg'=>'操作成功!'];
		}else{
			return ['code'=>1,'msg'=>'操作失败!'];
		}
	}

	/**
	 * 删除 ｜ 下线
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function delProduct()
	{
		if (input('param.type') == 1){
			db("product")->where("product_id",input('param.id'))->update(['product_status'=>-1]);
		}elseif(input('param.type') == 2){
			db("product")->where("product_id",input('param.id'))->update(['product_status'=>0]);
		}else{
			db("product")->where("product_id",input('param.id'))->update(['product_status'=>1]);
		}
		// 更新缓存
		$this->updateBlockCache();
		return ['code'=>1,'msg'=>'操作成功!'];
	}


	public function editAttr()
	{
		if (input('param.type') == 1){
			db("product")->where("product_id",input('param.id'))->update(['product_attr'=>26]);
		}elseif(input('param.type') == 2){
			db("product")->where("product_id",input('param.id'))->update(['product_attr'=>27]);
		}else{
			db("product")->where("product_id",input('param.id'))->update(['product_attr'=>25]);
		}
		// 更新缓存
		$this->updateBlockCache();
		return ['code'=>1,'msg'=>'操作成功!'];
	}



} 