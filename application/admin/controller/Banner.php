<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use \app\admin\model\Banner as model;

class Banner extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(request()->isPost()){
            $key = input('post.key');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = db('banner')
                ->order("banner_id asc")
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach($list['data'] as $key=>$value){
                switch($value['banner_addres']){
                    case 1:
                        $list['data'][$key]['banner_addres'] = "首页Banner轮播图";
                        break;
                    default:
                        break;
    			}
            }

            return ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return view();
    }

    /**
     * 添加banner.
     *
     * @return \think\Response
     */
    public function addBanner()
    {
        if(request()->isPost()){
            $param = input("param.");
            $param['banner_create_time'] = time();
            $model = new model();
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
        return view('bannerAdd');
    }

    /**
     * 修改banner.
     *
     * @return \think\Response
     */
    public function editBanner()
    {
        if(request()->isPost()){
            $param = input('param.');
            $data['banner_url']      = $param['banner_url'];
            $data['banner_addres']   = $param['banner_addres'];
            $data['banner_image']    = $param['banner_image'];
            $data['banner_id']       = $param['banner_id'];

            $res = db("banner")->update($data);
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
        $banner_id = input('param.banner_id');
        $list = db("banner")->where("banner_id",$banner_id)->find();
        $this->assign('data',$list);
        return view('form');
    }

    /**
     * 删除指定资源
     *
     * @return \think\Response
     */
    public function delBanner()
    {
        db('banner')->where('banner_id',input('param.banner_id'))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }

	/**
	 * 广告排序
	 * @return mixed
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
    public function sortBanner()
	{
		$data = input('param.');
//		dump($data);exit();
		$list['banner_id']   = $data['id'];
		$list['banner_sort'] = $data['sort'];
		$res = db("banner")->update($list);
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
