<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/12/3
 * Time: 11:23
 */

namespace app\goods\validate;


use think\Validate;

class Goods extends Validate
{

	protected $rule = [
		'userId'     => 'require|number',
		'productId'  => 'require|number',
		'collect_id' => 'require|number'
	];

	protected $message = [
		'userId.require' => '用户参数错误',
		'userId.number'  => '用户参数类型错误',
		'productId.require' => '参数错误',
		'productId.number' => '参数类型错误',
		'collect_id' => '参数错误'
	];


	protected $scene = [
		'browse'	=> ['userId','productId']
	];

}