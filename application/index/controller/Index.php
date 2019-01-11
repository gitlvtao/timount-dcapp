<?php
namespace app\index\controller;

use app\common\controller\Api;
use fuk\DataCrypt;
use think\facade\Cache;
use think\facade\Log;

class Index extends Api {
    public function index() {
        // 首页banner
//		$start = microtime(true);

        $columnWhere['a.ad_status'] = 1;
        $banner = db('ads')->alias('a')
            ->join('ad_positions p', 'p.positions_id = a.ad_position_id')
            ->where($columnWhere)
			->where("p.positions_ident","=","banner_index")
            ->field(['a.*'])
            ->order('a.ad_sort', 'desc')->select();

        $this->response['response'] = [
            'banner' => $banner,
            'notice' => $this->getNotice(), // 公告
            'column' => $this->columnIndex(), // icon
            'product' => $this->productIndex(),  // 热门推荐
        ];

//		$end = microtime(true);
//		Log::write("首页运行时间为：".($end - $start),'tmy');

        return $this->response;
    }

    /**
     * 首页ICON模块
     * @return array
     */
    public function columnIndex() {
        // 绑定以后需要放入缓存
        if (!cache('?hgqb_column_index')) {
            $bid = db('blocks')->where(['block_ident' => 'column_index'])->value('block_id');
            // 如果没有这个模块直接返回为空
            if (!$bid) return [];

            // 获取数据
            $columnWhere = [
                'c.column_type' => 1,
				'c.column_status' => 1,
                'd.data_block_id' => $bid,
            ];
            $column = db('block_data')->alias('d')
                ->join('column c', 'd.data_object_id = c.column_id')
                ->where($columnWhere)
                ->field(['c.column_id', 'c.column_name', 'c.column_thumb'])
                ->order('d.data_sort', 'desc')->select();

            // 数据放入缓存
            cache('hgqb_column_index', $column);
        }
        return cache('hgqb_column_index');
    }

    /**
     * 首页产品模块
     * @return array
     */
    public function productIndex() {
        // 绑定以后需要放入缓存
        if (!cache('?hgqb_product_index')) {
            $bid = db('blocks')->where(['block_ident' => 'product_index'])->value('block_id');

            // 如果没有这个模块直接返回为空
            if (!$bid) return [];

            // 获取数据
            $productWhere = [
                'p.product_status' => 1,
                'd.data_block_id' => $bid,
            ];
            $product = db('block_data')->alias('d')
                ->join('product p', 'd.data_object_id = p.product_id')
                ->where($productWhere)
				->field(['p.product_id', 'p.product_title', 'p.product_logo', 'p.product_max_amount','p.product_url'])
                ->order('d.data_sort', 'desc')->select();

            // 数据放入缓存
            cache('hgqb_product_index', $product);
        }
        return cache('hgqb_product_index');
    }

    /**
     * 获取手机轮播
     * @return array
     */
    public function getNotice(){
        $arr = [];
        $phone = ['130','131','132','133','134','135','135','137','138','139','150','151','152','153','154','155','156',
            '157','158','159','180','181','182','183','184','185','186','187','188','189','177','178','179'];
        $queryWhere = [
            'product_status' => 1,
        ];
        $info = db('product')->where($queryWhere)->select();
        for($i = 0; $i < 20; $i ++){
            $arr[$i] = $phone[rand(0, sizeof($phone)-1)]."****".rand(1000,9999)." 刚刚申请了 ".$info[rand(0,count($info)-1)]['product_title'];
        }
        return $arr;
    }


    public function en() {
        $param = input('param.');
        $data = $param;
        /*$data = [
            'articleId' => 1,
        ];*/

        dump(http_build_query($data).'&openId='.config('app.crypt.opendId'));exit();

        $sign = md5(http_build_query($data).'&openId='.config('app.crypt.opendId'));
        $data['sign'] = $sign;
        return DataCrypt::encrypt(json_encode($data), config('app.crypt.key'), config('app.crypt.iv'));
    }

    public function de() {
        $param = request()->only('data');
        $data = $param['data'];
        //$data = 'AdsuCsC/YfYZ0SH/H2IApVNwJk8Jhep7UzladsBaz3W2RlL/t+gHdIO2w5nTGqOWyGMclW+j8OPSjDTH+/yPdEnM+PAzXsNuOuRNLod+iWt4mp6VYit/eTNqGL/XelbGr/9NPOCOYeZUPpOS9PqU4TvOAHvlh06upUAyhoqGuG0nFP4+Q7dby/yvjlWOGZpD6lRqREASsstO7UuKhuem8bd2TmSiLyYqZ8ec0ceqhsthlxn/Bc8Lj2WLoG+Y5WOv';
        return DataCrypt::decrypt($data, config('app.crypt.key'), config('app.crypt.iv'));
    }

    public function ajax() {
        return view();
    }


   /* public function sign()
	{
		$param = input('param.');
		//验证签名
		$arr = [$param['nonce'],$param['time'],config('tmy.key')];
		sort($arr,SORT_STRING);
		$sign = md5(implode($arr));
		return json_encode($sign);
	}*/


}
