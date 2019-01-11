<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/14
 * Time: 11:05
 */

namespace app\other\controller;


use think\facade\Cache;
use think\Controller;

class Send extends Controller
{
	public function sendCode($phone)
	{
//		$code = rand(100000,999999);
		$code = '123456';
		Cache::store('redis')->set("sms_code_".$phone,$code,900);

//		$res = Sms::ali('13419646504', '123456');

		return  $code;
	}
}