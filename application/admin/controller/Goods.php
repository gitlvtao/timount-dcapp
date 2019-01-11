<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18-9-29
 * Time: 上午11:09
 */

namespace app\admin\controller;


use think\Db;
use \app\admin\model\Product as goodsInfo;

class Goods extends Base
{
    /**
     * 商品列表首页
     * @return array|\think\response\View
     */
    public function index(){
        if(request()->isPost()){
            $key = input('post.key');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = db("product")
                ->where('product_name', 'like', "%" . $key . "%")
                ->where("product_status","<>",-1)
                ->order("product_id asc")
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
        $column = db("column")->field("column_id,column_name")->select();
        $option = "<option value=''>请选择模块</option>";
        foreach($column as $value){
            $option .=  "<option value=".$value['column_id'].">".$value['column_name']."</option>";
        }
        $this->assign('option',$option);
        return view("index");
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
        $lable = getLableList();
        return view('goodsInfo',['labelList'=>$lable]);
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
            $data['product_tag']           = implode(",",$param['product_tag']);
            $model = new goodsInfo();
			$res = $model->allowField(true)->save($data,['product_id'=>$param['product_id']]);
            if($res){
                $result['code'] = 1;
                $result['msg'] = '编辑成功!';
                cache('adList', NULL);
                $result['url'] = url('index');
            }else{
                $result['code'] = -1;
                $result['msg'] = '编辑失败!';
            }
            return $result;
        }
        $product_id = input('param.product_id');
        $info = db("product")->where("product_id",$product_id)->find();
//        $info['product_tag'] = explode(",",$info['product_tag']);
        $this->assign("data",$info);
//        dump($info);exit;
		$lable = getLableList();
        $this->assign("labelList",$lable);
        return view('form');
    }

    /**
     * 删除商品
     * @return array
     */
    public function delGoods(){
        db("product")->where("product_id",input("param.product_id"))->update(['product_status'=>-1]);
        return ['code'=>1,'msg'=>'删除成功！'];
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
     * 获取栏目
     * @return mixed
     */
    /*public function getColumn(){
        $media_id = input('media_id');
        //查询分类信息
        $column = db("column")
            ->field("column_id,column_name")
            ->where('column_media_id',$media_id)
            ->select();
        $option = "";
        foreach($column as $value){
            $option .= "<option value=".$value['column_id'].">".$value['column_name']."</option>";
        }
        $result['code'] = 1;
        $result['msg']  = $option;
        return $result;
    }*/

} 