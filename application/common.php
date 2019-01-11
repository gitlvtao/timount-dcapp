<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件



//选中标签
function getLable($arr=[]){
    $str = "";
    $lable = getLableList();
//    dump($lable);exit;
    if(!empty($arr)){
        foreach($arr as $value){
            foreach($lable as $v){
                if($value == $v['config_id'] ){
                    $str .= " | ".$v['config_title'];
                }
            }
        }
    }
    $str .= " | ";
    return $str;
}

//标签库
function getLableList(){
    $lable = db('product_attr_config')->where("config_pid","1")->where("config_status","<>",-1)->select();
    return $lable;
}

/**
 * 随机生成字符串
 * @param int $len
 * @param string $format
 * @return string
 */
function randStr($length = 6, $format = 'NUMBER') {
	$format = strtoupper($format);
	switch($format) {
		case 'ALL':
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
			break;
		case 'NUMLET':
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			break;
		case 'CHAR':
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
			break;
		case 'UPPER':
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
		case 'LOWER':
			$chars = 'abcdefghijklmnopqrstuvwxyz';
			break;
		case 'NUMBER':
			$chars = '0123456789';
			break;
		default :
			$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
	}

	$password = '';
	while(strlen($password) < $length) {
		$password .= substr($chars, (mt_rand() % strlen($chars)), 1);
	}
	return $password;
}

/**
 * 前端转标签
 * @param array $arr
 * @return string
 */
function productLabel($arr = []){
	$str = "";
	$label = getLableList();
	if (!empty($arr)) {
		foreach ($arr as $value) {
			foreach ($label as $v) {
				if ($value == $v['config_id']) {
					$str .= "," . $v['config_title'];
				}
			}
		}
	}
	$str = trim($str,',');
	return $str;
}