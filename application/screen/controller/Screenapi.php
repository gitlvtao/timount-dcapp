<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/13
 * Time: 14:51
 */

namespace app\screen\controller;

use app\common\controller\Api;

class Screenapi extends Api
{
	/**
	 * 获取筛选条件接口
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function screen()
	{
		$info = db("product_attr_config")->select();
		foreach ($info as $key => $value){
			if($value['config_pid'] == 0){
				$data[$key]['value'] = $value['config_title'];
				$data[$key]['id']    = $value['config_id'];
			}
			//删除属性选择   暂时不需要
			if ($value['config_pid'] == 0 && $value['config_id'] > 3){
				unset($data[$key]);
			}
			foreach ($data as $k => $v){
				if($v['id'] == $value['config_pid']){
					$list[$v['value']][] = $value;
				}
			}
		}
		$this->response['response'] = $list;
		return $this->response;
	}

	/**
	 * 贷款测一测 数据筛选
	 * @return array
	 */
	public function listTest()
	{
		$data = config('tmy.loan_test');
		$this->response['response'] = $data;
		return $this->response;
	}

	/**
	 * 贷款测一测  数据添加
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function userTest()
	{
		$param = input('param.data');
		$data['test_user_id']  = $param['userId'];
		$data['test_money']    = $param['moneyRange'];
		$data['test_term']     = $param['termRange'];
		$data['test_use']      = $param['useRange'];
		$data['test_follow']   = $param['followRange'];

		$test_id = db("loan_test")->where("test_user_id",$param['userId'])->value("test_id");
		if ($test_id){
			//更新
			$data['test_update_time'] = time();
			$res = db("loan_test")->where("test_id",$test_id)->update($data);
		}else{
			//新增
			$data['test_create_time'] = time();
			$data['test_update_time'] = time();
			$res = db("loan_test")->insert($data);
		}
		if (!$res){
			$this->response['code'] = 'error';
			$this->response['msg'] = '操作失败!';
		}
		return $this->response;
	}


}