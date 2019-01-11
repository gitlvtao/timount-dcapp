<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/13
 * Time: 15:07
 */

namespace app\column\controller;

use app\common\controller\Api;

class Columnapi extends Api
{
	/**
	 * 分类接口
	 * @param $type 分类类型
	 * @return array
	 */
	public function column()
	{
		$list = db("column")->field("column_id,column_name,column_thumb")->where("column_type","=",1)->order("column_sort")->select();
		$this->response['response'] = $list;

		return $this->response;
	}

}