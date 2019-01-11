<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-12-05
 */

namespace app\pagedata\controller;


use app\admin\controller\Base;
use app\pagedata\logic\Logdata;
use fuk\LogFlow;
use think\facade\Cache;

class Page extends Base {
    public function index() {
        $between = input('param.between', '');
        if (request()->isAjax()) {
            $treedata = [
                [
                    'icon' => '',
                    'id' => 1,
                    'pid' => -1,
                    'name' => "首页轮播图",
                    'tm' => '',
                    'pv' => '',
                    'uv' => '',
                ],
                [
                    'icon' => '',
                    'id' => 2,
                    'pid' => -1,
                    'name' => "首页分类导航",
                    'tm' => '',
                    'pv' => '',
                    'uv' => '',
                ],
                [
                    'icon' => '',
                    'id' => 3,
                    'pid' => -1,
                    'name' => "首页九宫格",
                    'tm' => '',
                    'pv' => '',
                    'uv' => '',
                ],
            ];

            $pagedata = [];
            if(isset($between) && !empty($between)) {
                $betweenArr = explode(' - ', $between);
                $pagedata = $this->between($betweenArr);
            } else {
                $pagedata = $this->today();
            }
            $this->response['data'] = array_merge($treedata, $pagedata);

            return $this->response;
        } else {
            return view('index', [
                'between' => $between,
            ]);
        }
    }

    public function today() {
        $banner = Logdata::getBanner();
        $column = Logdata::getColumn();
        $product = Logdata::getProduct();
        // 处理缓存是否当天的数据
        $todayTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        if (cache('?today_pagedata_time')) {
            $cacheTime = cache('today_pagedata_time');
            // 如果是昨天的数据就清空数据
            if ($todayTime > $cacheTime) {
                Cache::clear('pagelog');
            }
        } else {
            cache('today_pagedata_time', $todayTime, null, 'pagelog');
        }

        $filepath = env('runtime_path')."logflow/".date('Ym').'/'.date('Ymd').'.log';

        // 判断文件是否存在
        if (!file_exists($filepath)) return $this->response;

        $logflow_line_num = 1;
        if (cache('?logflow_line_num')) {
            $logflow_line_num = cache('logflow_line_num');
        }

        // 读取文件
        $data = LogFlow::getFileLines($filepath, $logflow_line_num);
        $content = $data['content'];

        // 记录读取行数
        cache('logflow_line_num', $data['line'], null, 'pagelog');

        $log = new Logdata();
        $logdata = $log->today($content);

        $treedata = [];

        // 首页九宫格
        if (!empty($logdata['index_column'])) {
            foreach ($logdata['index_column'] as $ickey => $icitem) {
                $ictmDay = $icuvDay = $icpvDay = 0;
                // 位置
                foreach ($icitem as $icpkey => $icpitem) {
                    $ictm = $icuv = $icpv = 0;
                    // 数据
                    foreach ($icpitem as $icdatakey => $icdataitem) {
                        $treedata[] = [
                            'icon' => '',
                            'id' => randStr(),
                            'pid' => $ickey * 3 * 24 + $icpkey + 1,
                            'name' => $product[$icdatakey],
                            'tm' => $icdataitem['tm'],
                            'pv' => $icdataitem['pv'],
                            'uv' => $icdataitem['uv'],
                        ];
                        $ictm += $icdataitem['tm'];
                        $icuv += $icdataitem['uv'];
                        $icpv += $icdataitem['pv'];
                    }
                    $treedata[] = [
                        'icon' => '',
                        'id' => $ickey * 3 * 24 + $icpkey + 1,
                        'pid' => $ickey * 3 * 24,
                        'name' => '第 '.($icpkey + 1).' 单元格',
                        'tm' => $ictm,
                        'pv' => $icpv,
                        'uv' => $icuv,
                    ];
                    $ictmDay += $ictm;
                    $icuvDay += $icuv;
                    $icpvDay += $icpv;
                }
                // 时间段
                $startBetween = mktime($ickey, 0, 0, date('m'), date('d'), date('Y'));
                $endBetween = mktime($ickey + 1, 0, 0, date('m'), date('d'), date('Y'));
                $treedata[] = [
                    'icon' => '',
                    'id' => $ickey * 3 * 24,
                    'pid' => 3,
                    'name' => date('H:i', $startBetween).' ~ '.date('H:i', $endBetween),
                    'tm' => $ictmDay,
                    'pv' => $icpvDay,
                    'uv' => $icuvDay,
                ];
            }
        }

        // 首页分类导航
        if (!empty($logdata['index_nav'])) {
            foreach ($logdata['index_nav'] as $inkey => $initem) {
                $intmDay = $inuvDay = $inpvDay = 0;
                // 位置
                foreach ($initem as $inpkey => $inpitem) {
                    $intm = $inuv = $inpv = 0;
                    // 数据
                    foreach ($inpitem as $indatakey => $indataitem) {
                        $treedata[] = [
                            'icon' => '',
                            'id' => randStr(),
                            'pid' => $inkey * 2 * 24 + $inpkey + 1,
                            'name' => $column[$indatakey],
                            'tm' => $indataitem['tm'],
                            'pv' => $indataitem['pv'],
                            'uv' => $indataitem['uv'],
                        ];
                        $intm += $indataitem['tm'];
                        $inuv += $indataitem['uv'];
                        $inpv += $indataitem['pv'];
                    }
                    $treedata[] = [
                        'icon' => '',
                        'id' => $inkey * 2 * 24 + $inpkey + 1,
                        'pid' => $inkey * 2 * 24,
                        'name' => '第 '.($inpkey + 1).' 单元格',
                        'tm' => $intm,
                        'pv' => $inpv,
                        'uv' => $inuv,
                    ];
                    $intmDay += $intm;
                    $inuvDay += $inuv;
                    $inpvDay += $inpv;
                }
                // 时间段
                $startBetween = mktime($inkey, 0, 0, date('m'), date('d'), date('Y'));
                $endBetween = mktime($inkey + 1, 0, 0, date('m'), date('d'), date('Y'));
                $treedata[] = [
                    'icon' => '',
                    'id' => $inkey * 2 * 24,
                    'pid' => 2,
                    'name' => date('H:i', $startBetween).' ~ '.date('H:i', $endBetween),
                    'tm' => $intmDay,
                    'pv' => $inpvDay,
                    'uv' => $inuvDay,
                ];
            }
        }

        // 首页轮播图
        if (!empty($logdata['index_banner'])) {
            foreach ($logdata['index_banner'] as $ibkey => $ibitem) {
                $ibtmDay = $ibuvDay = $ibpvDay = 0;
                // 数据
                foreach ($ibitem as $ibpkey => $ibpitem) {
                    $ibtm = $ibuv = $ibpv = 0;
                    // 数据
                    foreach ($ibpitem as $ibdatakey => $ibdataitem) {
                        $treedata[] = [
                            'icon' => '',
                            'id' => randStr(),
                            'pid' => $ibkey * 24 + $ibpkey + 1,
                            'name' => $banner[$ibdatakey],
                            'tm' => $ibdataitem['tm'],
                            'pv' => $ibdataitem['pv'],
                            'uv' => $ibdataitem['uv'],
                        ];
                        $ibtm += $ibdataitem['tm'];
                        $ibuv += $ibdataitem['uv'];
                        $ibpv += $ibdataitem['pv'];
                    }
                    $treedata[] = [
                        'icon' => '',
                        'id' => $ibkey * 24 + $ibpkey + 1,
                        'pid' => $ibkey * 24,
                        'name' => '第 '.($ibpkey + 1).' 单元格',
                        'tm' => $ibtm,
                        'pv' => $ibpv,
                        'uv' => $ibuv,
                    ];
                    $ibtmDay += $ibtm;
                    $ibuvDay += $ibuv;
                    $ibpvDay += $ibpv;
                }
                // 时间段
                $startBetween = mktime($ibkey, 0, 0, date('m'), date('d'), date('Y'));
                $endBetween = mktime($ibkey + 1, 0, 0, date('m'), date('d'), date('Y'));
                $treedata[] = [
                    'icon' => '',
                    'id' => $ibkey * 24,
                    'pid' => 1,
                    'name' => date('H:i', $startBetween).' ~ '.date('H:i', $endBetween),
                    'tm' => $ibtmDay,
                    'pv' => $ibpvDay,
                    'uv' => $ibuvDay,
                ];
            }
        }

        return $treedata;
    }

    public function abc(){
        $date = input('get.date');
        if (empty($date)) {
            return '没有找到统计的日期';
        }
        dump($this->tongji($date));
    }

    public function between($data = []){
        $logdata = [];
        $pagepath = env('runtime_path')."pagedata/";
        $start = strtotime($data[0]);
        $end = strtotime($data[1]);

        $between[] = $start;
        if ($start != $end) {
            $diff = $end - $start;
            $diffDay = $diff / (24 * 3600);
            for ($i =1; $i <= $diffDay; $i ++) {
                $between[] = $start + (24 * 3600) * $i;
            }
        }

        foreach ($between as $key => $item) {
            $filepath = $pagepath .'/'.date('Ymd', $item).'.log';
            // 判断文件是否存在
            if (file_exists($filepath)) {
                // 读取文件
                $log = LogFlow::getFileLines($filepath);
                $content = json_decode($log['content'][0], true);
                $logdata =  array_merge($logdata, $content);
            } else {
                $tongji = $this->tongji(date('Ymd', $item));
                if (!empty($tongji)) {
                    $logdata = array_merge($logdata, $tongji);
                }
            }
        }

        return $logdata;
    }

    public function tongji($date) {
        $banner = Logdata::getBanner();
        $column = Logdata::getColumn();
        $product = Logdata::getProduct();
        $filepath = env('runtime_path')."logflow/".date('Ym').'/'.$date.'.log';

        // 判断文件是否存在
        if (!file_exists($filepath)) return [];

        // 读取文件
        $data = LogFlow::getFileLines($filepath);
        $content = $data['content'];

        if (empty($content)) {
            return [];
        } else {
            $log = new Logdata();
            $logdata = $log->tongji($content);
        }

        $treedata = [];

        // 首页九宫格
        if (!empty($logdata['index_column'])) {
            foreach ($logdata['index_column'] as $ickey => $icitem) {
                $ictmDay = $icuvDay = $icpvDay = 0;
                // 位置
                foreach ($icitem as $icpkey => $icpitem) {
                    $ictm = $icuv = $icpv = 0;
                    // 数据
                    foreach ($icpitem as $icdatakey => $icdataitem) {
                        $treedata[] = [
                            'icon' => '',
                            'id' => randStr(),
                            'pid' => $ickey * 3 + $icpkey + 1,
                            'name' => $product[$icdatakey],
                            'tm' => $icdataitem['tm'],
                            'pv' => $icdataitem['pv'],
                            'uv' => $icdataitem['uv'],
                        ];
                        $icuv += $icdataitem['uv'];
                        $icpv += $icdataitem['pv'];
                        $ictm += $icdataitem['tm'];
                    }

                    $treedata[] = [
                        'icon' => '',
                        'id' => $ickey * 3 + $icpkey + 1,
                        'pid' => $ickey * 3,
                        'name' => '第 '.($icpkey + 1).' 单元格',
                        'tm' => $ictm,
                        'pv' => $icpv,
                        'uv' => $icuv,
                    ];
                    $ictmDay += $ictm;
                    $icpvDay += $icpv;
                    $icuvDay += $icuv;
                }
            }
            // 时间段
            $treedata[] = [
                'icon' => '',
                'id' => $ickey * 3,
                'pid' => 3,
                'name' => $ickey,
                'tm' => $ictmDay,
                'pv' => $icpvDay,
                'uv' => $icuvDay,
            ];
        }

        // 首页分类导航
        if (!empty($logdata['index_nav'])) {
            foreach ($logdata['index_nav'] as $inkey => $initem) {
                $intmDay = $inuvDay = $inpvDay = 0;
                // 位置
                foreach ($initem as $inpkey => $inpitem) {
                    $intm = $inuv = $inpv = 0;
                    // 数据
                    foreach ($inpitem as $indatakey => $indataitem) {
                        $treedata[] = [
                            'icon' => '',
                            'id' => randStr(),
                            'pid' => $inkey * 2 + $inpkey + 1,
                            'name' => $column[$indatakey],
                            'tm' => $indataitem['tm'],
                            'pv' => $indataitem['pv'],
                            'uv' => $indataitem['uv'],
                        ];
                        $inuv += $indataitem['uv'];
                        $inpv += $indataitem['pv'];
                        $intm += $indataitem['tm'];
                    }
                    $treedata[] = [
                        'icon' => '',
                        'id' => $inkey * 2 + $inpkey + 1,
                        'pid' => $inkey * 2,
                        'name' => '第 '.($inpkey + 1).' 单元格',
                        'tm' => $intm,
                        'pv' => $inpv,
                        'uv' => $inuv,
                    ];
                    $inuvDay += $inuv;
                    $inpvDay += $inpv;
                    $intmDay += $intm;
                }
            }
            // 时间段
            $treedata[] = [
                'icon' => '',
                'id' => $inkey * 2,
                'pid' => 2,
                'name' => $inkey,
                'tm' => $intmDay,
                'pv' => $inpvDay,
                'uv' => $inuvDay,
            ];
        }

        // 首页轮播图
        if (!empty($logdata['index_banner'])) {
            foreach ($logdata['index_banner'] as $ibkey => $ibitem) {
                $ibtmDay = $ibuvDay = $ibpvDay = 0;
                // 数据
                foreach ($ibitem as $ibpkey => $ibpitem) {
                    $ibtm = $ibuv = $ibpv = 0;
                    // 数据
                    foreach ($ibpitem as $ibdatakey => $ibdataitem) {
                        $treedata[] = [
                            'icon' => '',
                            'id' => randStr(),
                            'pid' => $ibkey + $ibpkey + 1,
                            'name' => $banner[$ibdatakey],
                            'tm' => $ibdataitem['tm'],
                            'pv' => $ibdataitem['pv'],
                            'uv' => $ibdataitem['uv'],
                        ];
                        $ibuv += $ibdataitem['uv'];
                        $ibpv += $ibdataitem['pv'];
                        $ibtm += $ibdataitem['tm'];
                    }
                    $treedata[] = [
                        'icon' => '',
                        'id' => $ibkey + $ibpkey + 1,
                        'pid' => $ibkey,
                        'name' => '第 '.($ibpkey + 1).' 单元格',
                        'tm' => $ibtm,
                        'pv' => $ibpv,
                        'uv' => $ibuv,
                    ];
                    $ibuvDay += $ibuv;
                    $ibpvDay += $ibpv;
                    $ibtmDay += $ibtm;
                }
            }
            // 时间段
            $treedata[] = [
                'icon' => '',
                'id' => $ibkey,
                'pid' => 1,
                'name' => $ibkey,
                'tm' => $ibtmDay,
                'pv' => $ibpvDay,
                'uv' => $ibuvDay,
            ];
        }
        
        // 写到文件中
        LogFlow::writePage($treedata, $date.'.log');

        return $treedata;
    }
}