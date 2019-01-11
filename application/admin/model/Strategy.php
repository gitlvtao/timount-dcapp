<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/10/29
 * Time: 14:22
 */

namespace app\admin\model;


use think\Model;

class Strategy extends Model
{
	public function updateType($data=[],$type=0){
		if(!is_array($data) || empty($data)){
			return ['code'=>-1,'msg'=>'参数错误!'];exit();
		}
		$res = $this->where("strategy_id",'in',$data)->update(['strategy_type'=>$type]);
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