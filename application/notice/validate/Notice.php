<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/30
 * Time: 9:24
 */

namespace app\notice\validate;

use think\Validate;

class Notice extends Validate
{
	//验证字段
	protected $rule =   [
		'userId'  => 'require|number',
		'type'    => 'number',
		'id'      => 'require|number',
		'data'    => 'require',
	];

	//错误提示
	protected $message  =   [
		'userId.require' => '参数错误',
		'userId.number'  => '参数类型错误',
		'type.number'    => '参数类型错误',
		'id.require'     => '参数错误',
		'id.number'      => '参数类型错误',
		'id.data'        => '参数错误',
	];

	//验证场景
	protected $scene = [
		'notice'  =>  ['userId','type'],
		'del'     =>  ['userId','type','data'],
		'info'    => ['id'],
	];


}