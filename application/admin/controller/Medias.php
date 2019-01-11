<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18-9-29
 * Time: 上午9:26
 */

namespace app\admin\controller;


use think\Db;

class Medias extends Base
{
    /**
     * 媒体首页数据展示
     * @return array|mixed
     */
    public function index(){
        if(request()->isPost()){
            $key = input('post.key');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = db("media")
                ->where('media_name|media_ident', 'like', "%" . $key . "%")
                ->order("media_id asc")
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach($list['data'] as $key => $value){
                $list['data'][$key]['media_register_api'] = "http://daikuan.bangwoya.com/#/index?mediaId=".$value['media_ident'];
            }
            return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    /**
     * 添加媒体
     * @return mixed
     */
    public function addMedia(){
        if(request()->isPost()){
            $param = input("param.");
            $param['media_ident'] = strtolower($param['media_ident']);
            $param['media_create_time'] = time();
            $res = db("media")->insert($param);
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
        $this->assign("info",'');
        $this->assign("media_id",'-1');
        return $this->fetch("mediaAdd");
    }

    /**
     * 编辑媒体
     * @return mixed
     */
    public function editMedia(){
        if(request()->isPost()){
            $param = input('param.');
            $res = db("media")->update($param);
            if($res){
                $result['code'] = 1;
                $result['msg'] = '编辑成功!';
                cache('adList', NULL);
                $result['url'] = url('index');
                return $result;
            }else{
                $result['code'] = -1;
                $result['msg'] = '编辑失败!';
                return $result;
            }
        }
        $media_id = input('param.media_id');
        $info = db("media")->where("media_id",$media_id)->find();
        if(empty($info)){
            $info = 'null';
        }
        $this->assign('info',$info);
        $this->assign('media_id',$media_id);
        return $this->fetch("mediaAdd");
    }

    /**
     * 删除媒体
     * @return array
     */
    public function delMedia(){
        db("media")->where('media_id',input('param.media_id'))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }

    /**
     * 媒体商品列表
     * @return array|\think\response\View
     */
    public function goodsList(){
        if(request()->isPost()){
            $key = input('post.key');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $media_id = input('param.media_id');
            $media_name = db("media")->where("media_id",$media_id)->value("media_name");
            $goodsInfo = db("media_bind_product")->alias("A")
                        ->join("product B","A.bind_product_id = B.product_id")
                        ->join("column C","A.bind_column_id = C.column_id")
                        ->field("A.bind_id,A.bind_media_id,A.bind_column_id,A.bind_product_id,A.bind_status,A.bind_sort,B.*,C.column_name")
                        ->where('B.product_name|C.column_name', 'like', "%" . $key . "%")
                        ->where("A.bind_media_id",$media_id)
                        ->where('A.bind_status',1)
                        ->where('B.product_status',1)
                        ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                        ->toArray();
            if(!empty($goodsInfo['data'])){
                foreach($goodsInfo['data'] as $key=>$value){
                    $goodsInfo['data'][$key]['media_name'] = $media_name;
                    $goodsInfo['data'][$key]['product_tags'] = getLable(explode(",",$value['product_tag']));
                    $goodsInfo['data'][$key]['product_interests'] = ($value['product_interest']*100)."%";
                    $goodsInfo['data'][$key]['product_adopts'] = ($value['product_adopt']*100)."%";
                }
            }else{
                $goodsInfo['data'] = [];
                $goodsInfo['total'] = 0;
            }
            return ['code'=>0,'msg'=>'获取成功!','data'=>$goodsInfo['data'],'count'=>$goodsInfo['total'],'rel'=>1];
        }
        $media_id = input('param.media_id');
        $this->assign('media_id',$media_id);
        return view('goodsList');
    }

    /**
     * 媒体产品排序
     * @return mixed
     */
    public function sort(){
        $data = input('param.');
        $res = db("media_bind_product")->update($data);
        if($res){
            $result['code'] = 1;
            $result['msg']  = '操作成功';
            return $result;
        }else{
            $result['code'] = 0;
            $result['msg']  = '操作失败';
            return $result;
        }
    }

    /**
     * 删除绑定的媒体商品
     * @return array
     */
    public function delBindGoods(){
        db("media_bind_product")->where("bind_id",input('param.bind_id'))->update(['bind_status'=>0]);
        return ['code'=>1,'msg'=>'删除成功！'];
    }


} 