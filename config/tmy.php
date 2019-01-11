<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-09
 */

return [
    'upload_path' => env('root_path') . 'public_html/uploads/',

    // 阿里短信配置
    'alisms' => array(
        'appkey' => '23409873',
        'secret' => '68fcd3bd3897744a7ac914ed4e1d0ba2',
        'prefix' => '红光商城', // 短信前缀
        'timeout' => 15 * 60, //验证码过期时间为15分钟
    ),

    // 七牛云配置
    'qiniu' => [
        // 七牛云配置
        'accesskey' => '1QCjVsSW3unUCzzGBTCCDXMqpBxTyIuVnBVDMLiE', // 你的accessKey
        'secretkey' => 'IbYQo_n9P_3wx2gGOxJ7ilr5JZRjS9t3a6vZ9RZ7', // 你的secretKey
        'bucket' => 'hgqb', //上传的空间
        'domain' => 'http://static.hgqb.bangwoya.com/', // 空间绑定的域名
    ],

	//极光推送配置
	'jPush' =>[
		'app_key' => '5beaa30e614d38911bad357e',
		'master_secret' => 'b854643c4364bb230aa85c24'
	],


	//sign签名key
	'key' => '643c4364bb230aa8',


    // 贷款测一测配置
    'loan_test' => [
        // 贷款额度
        'moneyRange' => [
            1 => '2000元以上',
            2 => '2000~5000元',
            3 => '5000~10000元',
            4 => '10000元',
        ],
        // 贷款期限
        'termRange' => [
            1 => '一个月以下',
            2 => '1~3个月',
            3 => '3~6个月',
            4 => '6个月以上',
        ],
        // 贷款用途
        'useRange' => [
            1 => '生活消费',
            2 => '信用卡还款',
            3 => '购车买房',
            4 => '其他',
        ],
        // 关注项
        'followRange' => [
            1 => '秒批秒下',
            2 => '审核简单',
            3 => '额度高',
            4 => '利息低',
        ],
    ],
];