<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/12/11
 * Time: 14:54
 */

namespace app\user\validate;

use think\Validate;

class UserValidate extends Validate
{

	//验证字段
	protected $rule =   [
		'phone'  => 'require|mobile',
		'type'    => 'require|in:1,2',
		'nonce'   => 'require',
		'time'    => 'require',
		'sign'    => 'require',
	];

	//错误提示
	protected $message  =   [
		'phone.require'  => '用户参数缺失',
		'phone.mobile'   => '手机号非法',
		'type.require'   => '类型参数缺失',
		'type.in'        => '参数错误',
		'nonce.require'  => 'nonce参数缺失',
		'time.require'   => '参数缺失',
		'sign.require'   => 'sign参数缺失',
	];

	//验证场景
	protected $scene = [
		'pull'  =>  ['phone','type','nonce','time','sign'],
	];

}