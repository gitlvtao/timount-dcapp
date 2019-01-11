<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-12-05
 */

namespace app\pagedata\logic;


use think\facade\Cache;

class Logdata {
    public function today($data = []) {
        
        // 当天数据
        $group = cache('?today_page_group') ? cache('today_page_group') : [];

        foreach ($data as $key => $item) {
            $item = json_decode($item, true);
            if (strpos($item['pos'], '-')) {
                $pos = explode('-', $item['pos']);
                $house = date('H', $item['create_time']);

                // 当前位置的用户是否点击过
                $groupip = cache('?today_groupip_'.$pos[0].'_'.$house.'_'.$pos[1].'_'.$item['oid']) ? cache('today_groupip_'.$pos[0].'_'.$house.'_'.$pos[1].'_'.$item['oid']) : [];

                // 排除当前时间段用户的重复点击
                $uv = 0;
                if (!in_array($item['ip'], $groupip)) {
                    $uv = 1;
                    $groupip[] = $item['ip'];
                    cache('today_groupip_'.$pos[0].'_'.$house.'_'.$pos[1].'_'.$item['oid'], $groupip, null, 'pagelog');
                }

                // 开始统计
                $page = isset($group[$pos[0]][$house][$pos[1]][$item['oid']]) ? $group[$pos[0]][$house][$pos[1]][$item['oid']]: [];
                $click = [
                    'tm' => $item['tm'],
                    'pv' => 1, // 点击次数
                    'uv' => $uv, // 点击人数
                ];
                if (!empty($page)) {
                    $click['tm'] += $page['tm'];
                    $click['pv'] += $page['pv'];
                    $click['uv'] += $page['uv'];
                }
                $group[$pos[0]][$house][$pos[1]][$item['oid']] = $click;
            }
        }
        cache('today_page_group', $group, null, 'pagelog');
        return $group;
    }

    public function tongji($data = []) {
        $group = [];
        foreach ($data as $key => $item) {
            $item = json_decode($item, true);
            if (strpos($item['pos'], '-')) {
                $pos = explode('-', $item['pos']);
                $day = date('Ymd', $item['create_time']);

                // 当前位置的用户是否点击过
                $groupip = cache('?today_page_groupip_'.$day.'_'.$pos[1].'_'.$item['oid']) ? cache('today_page_groupip_'.$day.'_'.$pos[1].'_'.$item['oid']) : [];

                // 排除当前时间段用户的重复点击
                $uv = 0;
                if (!in_array($item['ip'], $groupip)) {
                    $uv = 1;
                    $groupip[] = $item['ip'];
                    cache('today_page_groupip_'.$day.'_'.$pos[1].'_'.$item['oid'], $groupip, null, 'pagelog_tongji');
                }

                // 开始统计
                $page = isset($group[$pos[0]][$day][$pos[1]][$item['oid']]) ? $group[$pos[0]][$day][$pos[1]][$item['oid']]: [];
                $click = [
                    'tm' => $item['tm'],
                    'pv' => 1, // 点击次数
                    'uv' => $uv, // 点击人数
                ];
                if (!empty($page)) {
                    $click['tm'] += $page['tm'];
                    $click['pv'] += $page['pv'];
                    $click['uv'] += $page['uv'];
                }
                $group[$pos[0]][$day][$pos[1]][$item['oid']] = $click;
            }
        }

        // 统计完成之后就删除
        Cache::clear('pagelog_tongji');

        return $group;
    }

    /**
     * 获取banner
     * @return array
     */
    public static function getBanner() {
        $ads = [];
        $data = db('ads', [], false)->field(['ad_id', 'ad_title'])->select();
        foreach ($data as $item) {
            $ads[$item['ad_id']] = $item['ad_title'];
        }
        return $ads;
    }

    /**
     * 获取栏目
     * @return array
     */
    public static function getColumn() {
        $ads = [];
        $data = db('column', [], false)->field(['column_id', 'column_name'])->select();
        foreach ($data as $item) {
            $ads[$item['column_id']] = $item['column_name'];
        }
        return $ads;
    }

    /**
     * 获取产品
     * @return array
     */
    public static function getProduct() {
        $ads = [];
        $data = db('product', [], false)->field(['product_id', 'product_title'])->select();
        foreach ($data as $item) {
            $ads[$item['product_id']] = $item['product_title'];
        }
        return $ads;
    }
}