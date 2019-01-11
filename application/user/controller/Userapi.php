<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/13
 * Time: 15:52
 */

namespace app\user\controller;

use app\common\controller\Api;
use think\facade\Cache;
use fuk\Qiniu;
use fuk\Sms;
use app\user\validate\UserValidate;
use think\facade\Log;

class Userapi extends Api
{
	/**
	 * 用户收藏列表
	 * @return array
	 */
	public function userCollect()
	{
		$param = request()->param('data');
		if (empty($param['userId']) || empty($param['type']) || !isset($param['userId']) || !isset($param['type'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误!';
			return $this->response;exit();
		}
		$uid = $param['userId'];
		$type = $param['type'];
		if ($type == 1) {
			//收藏产品
			$list = db("collect_log")->alias("A")->join("product B", "A.collect_object_id = B.product_id", "left")
				->where("A.collect_type", "=", 1)->where("A.collect_user_id", "=", $uid)->where("B.product_status",1)
				->field("B.product_id,B.product_title,B.product_logo,B.product_interest_type,B.product_interest,B.product_term,B.product_max_amount,B.product_url,B.product_tag,B.product_attr,B.product_status,B.product_grant")->select();

			$collect = db("collect_log")->where([["collect_user_id","=",$uid],['collect_type',"=",1]])->column("collect_object_id");
			if (!empty($list)){
				foreach ($list as $key=>$value){
					$list[$key]['product_tag'] = productLabel(explode(',',$value['product_tag']));
					$list[$key]['collect'] = 0;
					if ( !empty($collect) && in_array($value['product_id'],$collect)){
						$list[$key]['collect'] = 1;
					}
				}
			}
		} else {
			//收藏攻略
			$list = db("collect_log")->alias("A")->join("article B", "A.collect_object_id = B.article_id", "left")
				->where("A.collect_type", "=", 2)->where("B.article_status",1)->where("A.collect_user_id", "=", $uid)->select();
		}
		$this->response['response'] = $list;
		return $this->response;
	}

	/**
	 * 用户浏览记录
	 * @return array
	 */
	public function userBrowse()
	{
		$param = request()->param('data');
		if (empty($param['userId']) || empty($param['type']) || !isset($param['userId']) || !isset($param['type'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误!';
			return $this->response;exit();
		}
		$uid = $param['userId'];
		$type = $param['type'];
		$list = db("view_log")->alias("A")->join("product B", "A.view_object_id = B.product_id", "left")
			->where("A.view_user_id", "=", $uid)->where("A.view_type", "=", $type)->where("B.product_status",1)
			->field("B.product_id,B.product_title,B.product_logo,B.product_interest_type,B.product_interest,B.product_term,B.product_max_amount,B.product_url,B.product_tag,B.product_attr,B.product_status,B.product_grant,A.view_create_time as time")->select();
		$collect = db("collect_log")->where("collect_user_id", $uid)->where("collect_type", 1)->column("collect_object_id");
		$info = [];
		if (!empty($list)) {
			foreach ($list as $key => $value) {
				$value['collect'] = 0;
				if (!empty($collect) && in_array($value['product_id'], $collect)) {
					$value['collect'] = 1;
				}
				$value['product_tag'] = productLabel(explode(',',$value['product_tag']));
				$data = date("Y年m月d日", $value['time']);
				$info[$data][] = $value;
			}
		}
		$this->response['response'] = $info;
		return $this->response;
	}

	/**
	 * 用户信息
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function userInfo()
	{
		$param = request()->param('data');
		if (empty($param['userId']) || !isset($param['userId'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误!';
			return $this->response;exit();
		}
		$uid = $param['userId'];
		$info = db("users")->where("user_id", "=", $uid)->field("user_id,user_mobile,user_headpic,user_nickname,user_sex,user_age")->find();
		$this->response['response'] = $info;
		return $this->response;
	}

	/**
	 * 用户登录
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 */
	public function login()
	{
		$param = request()->param('data');
		if (empty($param['username']) || !isset($param['username']) || empty($param['password']) || !isset($param['password'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误!';
			return $this->response;exit();
		}
		$user = $param['username'];
		$pass = $param['password'];
		if (empty($user) || empty($pass) || !isset($user) || !isset($pass)) {
			$this->response['code'] = 'error';
			$this->response['msg'] = "参数错误";
			return $this->response;exit();
		}
		$userInfo = db("users")->where("user_mobile", $user)->field("user_id,user_mobile,user_headpic,user_password,user_nickname,user_sex,user_age,user_status")->find();
		if (empty($userInfo)) {
			$this->response['code'] = 'error';
			$this->response['msg'] = "账号错误，用户不存在!";
			return $this->response;exit();
		}
		if (empty($userInfo['user_password'])) {
			$this->response['code'] = 'error';
			$this->response['msg'] = "请先设置密码";
			return $this->response;exit();
		}
		if ($userInfo['user_password'] != md5($pass)) {
			$this->response['code'] = 'error';
			$this->response['msg'] = "密码错误";
			return $this->response;exit();
		}
		if ($userInfo['user_status'] < 1) {
			$this->response['code'] = 'error';
			$this->response['msg'] = "用户被冻结";
			return $this->response;exit();
		}
		$res = db("users")->where("user_id", $userInfo['user_id'])->update(['user_login_time' => time(), 'user_login_ip' => $param['ip']]);
		unset($userInfo['user_password']);
		$this->response['response'] = $userInfo;
		return $this->response;
	}

	/**
	 * 用户注册
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function register()
	{
		$param = request()->param('data');
		if (empty($param['phone']) || !isset($param['phone'])) {
			$this->response['code'] = 'error';
			$this->response['msg'] = "参数错误";
			return $this->response;exit();
		}
		$phone = $param['phone'];
		if (empty($param['pass'])) {
			$code = $param['code'];
			$sms_code = Cache::store('redis')->get("sms_code_" . $phone);
			if (empty($sms_code)) {
				$this->response['code'] = 'error';
				$this->response['msg'] = "验证码过期";
				return $this->response;exit();
			}
			if ($code != $sms_code) {
				$this->response['code'] = 'error';
				$this->response['msg'] = "验证码错误";
				return $this->response;exit();
			}
			return $this->response;exit();
		} else {
			if (empty($phone) || !isset($phone) || empty($param['pass']) || !isset($param['pass']) || empty($param['ip']) || !isset($param['ip'])) {
				$this->response['code'] = 'error';
				$this->response['msg'] = "参数错误";
				return $this->response;exit();
			}
			$res = db('users')->where("user_mobile",$phone)->find();
			if ($res){
				$this->response['code'] = 'error';
				$this->response['msg']  = '手机号已注册!';
				return $this->response; exit();
			}
			$pid = 0;
			if (!empty($param['uid']) || isset($param['uid'])){
				//分享链接注册的
				//验证验证码
				$code = $param['code'];
				$sms_code = Cache::store('redis')->get("sms_code_" . $phone);
				if (empty($sms_code)) {
					$this->response['code'] = 'error';
					$this->response['msg'] = "验证码过期";
					return $this->response;exit();
				}
				if ($code != $sms_code) {
					$this->response['code'] = 'error';
					$this->response['msg'] = "验证码错误";
					return $this->response;exit();
				}

				/**$user_pid = db("users")->where("user_id",$param['uid'])->value("user_id");
				if ($user_pid){
					$pid = $user_pid;
				}*/
				$pid = $param['uid'];
			}
			$data = [
				'user_mobile' => $phone,
				'user_pid'    => $pid,
				'user_password' => md5($param['pass']),
				'user_nickname' => '用户_'.substr(md5($phone),5,6),
				'user_headpic'  => 'http://static.hgqb.bangwoya.com/hand_portrait_img.png',
				'user_reg_time' => time(),
				'user_reg_ip' => $param['ip'],
				'user_login_time' => 0,
				'user_login_ip' => ''
			];
			if ($pid == 0){
				$data['user_login_time'] = time();
				$data['user_login_ip']   = $param['ip'];
			}
			$uid = db("users")->insertGetId($data);
		}
		$this->response['response'] = $uid;
		return $this->response;
	}

	/**
	 * @param $type  发送验证码类型  空-注册  非空-修改信息
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function sendCode()
	{
		$param = request()->param('data');
		if (empty($param['phone']) || !isset($param['phone'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误!';
			return $this->response;exit();
		}
		$info = db("users")->where("user_mobile", $param['phone'])->find();
		if (empty($param['type']) || !isset($param['type'])){
			if ($info) {
				$this->response['code'] = 'error';
				$this->response['msg'] = '手机号已注册!';
				return $this->response;exit();
			}
		}else{
			if (empty($info)){
				$this->response['code'] = 'error';
				$this->response['msg']  = '注册用户不存在';
				return $this->response;exit();
			}
		}
		$sendTime = Cache::store('redis')->get("sms_code_sendTime" . $param['phone']);
		if ($sendTime){
			$this->response['code'] = 'error';
			$this->response['msg']  = '60s内不能重复发送短信';
			return $this->response;exit();
		}
		$code = randStr();
		$res = Sms::ali($param['phone'],$code);
		if ($res){
			Cache::store('redis')->set("sms_code_".$param['phone'],$code,900);
			Cache::store('redis')->set("sms_code_sendTime".$param['phone'],time(),60);
		}else{
			$this->response['code'] = 'error';
			$this->response['msg']  = '短信验证码发送失败,请重试';
		}
		return $this->response;
	}

	/**
	 * 用户更换手机号
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 */
	public function setPhone()
	{
		$param = request()->param('data');
		if (empty($param['userId']) || !isset($param['userId']) || empty($param['phone']) || !isset($param['phone'])){
			$this->response['code'] = 'error';
			$this->response['msg'] = "参数错误";
			return $this->response;exit();
		}
		$uid = $param['userId'];
		$phone = $param['phone'];
		$code = $param['code'];   //更换手机号的验证码验证

		$sms_code = Cache::store('redis')->get("sms_code_".$phone);

		if (!$sms_code) {
			$this->response['code'] = 'error';
			$this->response['msg'] = "验证码过期";
			return $this->response;exit();
		}
		if ($sms_code != $code){
			$this->response['code'] = 'error';
			$this->response['msg'] = "验证码错误";
			return $this->response;exit();
		}
		$res = db("users")->where("user_id", $uid)->update(['user_mobile' => $phone]);
		if (!$res) {
			$this->response['code'] = 'error';
			$this->response['msg'] = '操作失败';
			return $this->response;exit();
		}
		return $this->response;
	}

	/**
	 * 修改密码
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 */
	public function setPass()
	{
		$param = request()->param('data');

		if (empty($param['phone']) || !isset($param['phone']) || empty($param['userId']) || !isset($param['userId'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误';
			return $this->response;exit();
		}
		$password = $param['password']?$param['password']:'';
		$phone    = $param['phone'];
		$uid      = $param['userId'];

		if (empty($password)){
			$pass     = $param['pass'];
			$user = db("users")->where("user_mobile",$phone)->find();
			if (empty($user)){
				$this->response['code'] = 'error';
				$this->response['msg']  = '用户不存在';
				return $this->response;exit();
			}
			if ($uid != $user['user_id']){
				$this->response['code'] = 'error';
				$this->response['msg']  = '只能修改当前用户的密码';
				return $this->response;exit();
			}
			if (empty($user['user_password'])){
				$this->response['code'] = 'error';
				$this->response['msg']  = '请先设置用户密码';
				return $this->response;exit();
			}
			if ($user['user_password'] != md5($pass)){
				$this->response['code'] = 'error';
				$this->response['msg']  = '用户密码错误';
				return $this->response;exit();
			}

			unset($user['user_password']);

			$this->response['response'] = $user;
			return $this->response;exit();
		}else{
			$userPass = db("users")->where("user_id",$uid)->value("user_password");
			if ($userPass == md5($password)){
				$this->response['code'] = 'error';
				$this->response['msg']  = '新密码不能与旧密码一致';
				return $this->response;exit();
			}
			$res = db("users")->where("user_id",$uid)->update(['user_password'=>md5($password)]);
			if (!$res){
				$this->response['code'] = 'error';
				$this->response['msg']  = '操作失败';
			}
			return $this->response;
		}
	}

	/**
	 * 修改用户信息
	 * @param $type 修改类型
	 * @return array|int|string
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function setUserInfo()
	{
		$param = request()->param('data');
		if (empty($param['type']) || !isset($param['type']) || empty($param['data']) || !isset($param['data']) || empty($param['userId'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误';
			return $this->response;exit();
		}
		switch ($param['type']){
			case 2:   //修改用户昵称
				$user['user_nickname'] = $param['data'];
				break;
			case 3:   //修改用户性别
				$user['user_sex'] = $param['data'];
				if ($param['data'] != 1 && $param['data'] != 2){
					$user['user_sex'] = 0;
				}
				break;
			case 4:  //修改用户年龄
				$user['user_age'] = $param['data'];
				break;
			default;  //修改头像
//				$res = $this->image64($param['data']);
				$user['user_headpic'] = $param['data'];
				break;
		}
		$res = db("users")->where("user_id",$param['userId'])->update($user);
		if (!$res){
			$this->response['code'] = 'error';
			$this->response['msg']  = '操作失败';
		}
		return $this->response;exit();
	}

	/**
	 * 忘记密码
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 */
	public function forgetPass()
	{
		$param = input('param.data');
		if (empty($param['phone']) || !isset($param['phone'])){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误';
			return $this->response;exit();
		}
		if (empty($param['pass']) || !isset($param['pass'])){
			if (empty($param['code']) || !isset($param['code'])){
				$this->response['code'] = 'error';
				$this->response['msg']  = '参数错误';
				return $this->response;exit();
			}
			$sms_code = Cache::store('redis')->get("sms_code_" . $param['phone']);
			if (empty($sms_code)) {
				$this->response['code'] = 'error';
				$this->response['msg'] = "验证码过期";
				return $this->response;exit();
			}
			if ($param['code'] != $sms_code) {
				$this->response['code'] = 'error';
				$this->response['msg'] = "验证码错误";
				return $this->response;exit();
			}
			return $this->response;exit();
		}else{
			if (empty($param['phone']) || !isset($param['phone']) || empty($param['pass']) || !isset($param['pass'])){
				$this->response['code'] = 'error';
				$this->response['msg'] = "参数错误";
				return $this->response;exit();
			}
			db("users")->where("user_mobile",$param['phone'])->update(['user_password'=>md5($param['pass'])]);
			return $this->response;
		}
	}


	/**
	 * 新手上路
	 * @return array
	 */
	public function userRoad()
	{
		$this->response['response'] = "http://static.hgqb.bangwoya.com/userinfo.png";
		return json_encode($this->response,true);
	}

	/**
	 * 用户中心 广告接口
	 * @return array
	 */
	public function userBanner()
	{
		$columnWhere['a.ad_status'] = 1;
		$banner = db('ads')->alias('a')
			->join('ad_positions p', 'p.positions_id = a.ad_position_id')
			->where($columnWhere)
			->where("p.positions_ident","=","banner_user")
			->field(['a.*'])
			->order('a.ad_sort', 'desc')->find();
		$this->response['response'] = $banner;
//		dump($this->response);exit;
		return $this->response;
	}


	/**
	 * 对接拉新的api接口
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function pullNew()
	{
		$param = input('param.');

		$res = $this->commonPull($param);
		if ($res){
			$this->response['code'] = 0;
			$this->response['msg']  = '操作成功';
			$this->response['response']  = '';
			return $this->response;exit();
		}else{
			$this->response['code'] = 10000;
			$this->response['msg']  = '操作失败';
			return $this->response;exit();
		}
	}


	/**
	 * 公共方法  用户奖励登记
	 * @param $param
	 * @param int $type
	 * @return array|bool
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function commonPull($param,$type=2)
	{
		if ($type == 2){
			$UserValidate = new UserValidate();
			if (!$UserValidate->scene('pull')->check($param)){
				$this->response['code'] = 10000;
				$this->response['msg'] = $UserValidate->getError();
				return $this->response;exit();
			}
			//验证签名
			$arr = [$param['nonce'],$param['time'],config('tmy.key')];
			sort($arr,SORT_STRING);
			$sign = md5(implode($arr));
			if ($sign != $param['sign']){
				$this->response['code'] = 10000;
				$this->response['msg']  = '签名错误';
				return $this->response;exit();
			}
		}
		$reg = db("pull_new")->where(["pull_phone"=>$param['phone'],"pull_type"=>$param['type']])->find();
		if ($reg){
//			$this->response['code'] = 10000;
//			$this->response['msg']  = '重复请求';
//			return $this->response;exit();
			return false;
		}
		$pid   = db("users")->where("user_mobile",$param['phone'])->value("user_pid");
		$phone = db("users")->where("user_id",$pid)->value("user_mobile");
//		$money = ($param['type'] == 1)?2:3;
		if ($phone){
			//存在上级
			$saveData = [
				[
					'pull_phone' => $param['phone'],
					'pull_type'  => $param['type'],
					'pull_platform'  => 0,
					'pull_create_time' => time()
				],
				[
					'pull_phone' => $phone,
					'pull_type'  => $param['type'],
					'pull_platform'  => $param['phone'],
					'pull_create_time' => time()
				]
			];
			$res =  db("pull_new")->insertAll($saveData);
		}else{
			$saveData = [
				[
					'pull_phone' => $param['phone'],
					'pull_type'  => $param['type'],
					'pull_platform'  => 0,
					'pull_create_time' => time()
				]
			];
//			$res = db("pull_new")->insert($saveData);
			$res = true;
		}
		//日志记录
		Log::write("拉新活动对接记录:".json_encode($saveData,true),'tmy');
		if ($res){
			return true;
		}else{
			return false;
		}
	}

}