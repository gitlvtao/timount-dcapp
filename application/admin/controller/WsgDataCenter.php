<?php
/**
 * 贷款超市数据中心
 * Created by PhpStorm.
 * User: Timount
 * Date: 2018/9/29
 * Time: 10:22
 */

namespace app\admin\controller;

use think\Cache;
use think\Controller;
use think\Db;
use think\Exception;
use think\Loader;
use think\Request;

//use PHPExcel_IOFactory;
//use PHPExcel;

class WsgDataCenter extends Base
{

    /**
     * 首页pv/uv
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getIndexPvUv()
    {
        if (request()->isPost()) {//post请求
            $index_pvuv_select = input("index_content_media");//获取媒体信息
            $index_pvuv_date = input("index_content_data");//查询时间
            $limit = input("limit");//每页显示行数
            $page = input("page");//页码
            //查询首页一天中的pv/uv
            //$count_day:时间为空时为0，时间区间为一天是为0
            $sel_content_index = $this->getIndexPvUvData($index_pvuv_select, $index_pvuv_date, $limit, $page);
            return ['code' => 0, 'msg' => '获取成功!', 'data' => $sel_content_index, 'count' => count($sel_content_index)];
        } else {
//            $mm="421024198504171017";
//            preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/',$mm,$a);
//            print_r($a);
            $this->assign("sel_media", $this->getAllMediaNameOption());//查询媒体名称
            return $this->fetch("getIndexPvUv");
        }
    }

    /**
     * 首页pv/uv导出excel
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getIndexPvUvDownExcel()
    {
        //查询数据
        $sel_content_index = $this->getIndexPvUvData(input("index_content_media"), input("index_content_data"));
        if (!empty($sel_content_index)) {
            foreach ($sel_content_index as $k => $v) {
                $a[$k] = $v['desc_ct'];
                $b[$k] = $v['count_index_pv'];
            }
            array_multisort($a, SORT_ASC, $b, SORT_DESC, $sel_content_index);//排序
        }
        header('Access-Control-Allow-Origin:*');
        $PHPExcel = new \PHPExcel();
        $PHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1", "媒体名称")
            ->setCellValue("B1", "首页PV量")
            ->setCellValue("C1", "首页UV量")
            ->setCellValue("D1", "首页平均停留时长(/s)")
            ->setCellValue("E1", "时间");
        //查询获取数据信息
        $rownum = 1;
        foreach ($sel_content_index as $v) {
            $rownum++;
            $PHPExcel->getActiveSheet()->setCellValue('A' . $rownum, $v['media_name']);
            $PHPExcel->getActiveSheet()->setCellValue('B' . $rownum, $v['count_index_pv']);
            $PHPExcel->getActiveSheet()->setCellValue('C' . $rownum, $v['count_index_uv']);
            $PHPExcel->getActiveSheet()->setCellValue('D' . $rownum, $v['avg_index_time']);
            $PHPExcel->getActiveSheet()->setCellValue('E' . $rownum, $v['time_hour']);
        }
        //重命名表
        $PHPExcel->getActiveSheet()->setTitle('Simple');
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=" . date("Y-m-d-H-i-s") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }

    /**
     * 首页pv/uv查询数据返回
     * @param string $index_pvuv_select 查询媒体
     * @param string $index_pvuv_date 查询时间
     * @param int $limit 每页行数
     * @param int $page 页码
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    protected function getIndexPvUvData($index_pvuv_select = "", $index_pvuv_date = "", $limit = 10000, $page = 1)
    {
        //判断日期信息---日期为空时查询前一天数据信息
        if (empty($index_pvuv_date)) {
            $index_pvuv_date = date("Y-m-d", strtotime("-1 day")) . " ~ " . date("Y-m-d", strtotime("-1 day"));
        }
        $date = explode("~", $index_pvuv_date);
        $start_time = strtotime($date[0] . " 00:00:00");
        $end_time = strtotime($date[1] . " 23:59:59");
        $count_day = count($this->dateBetweenDay(strtotime($date[1]), strtotime($date[0])));
        //查询所有的媒体
        $sel_media_model = db::name("data_index")->alias("a")
            ->field("count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,media_name,
            convert(avg(iduration),decimal(10,2)) as avg_index_time,from_unixtime(icreate_time,'%Y-%m-%d') as time_hour
            ,imediaId as id,concat(imediaId,'1') as desc_ct")
            ->join("media b", "a.imediaId=b.media_ident", "left")
            ->where("itype_a", "index")
            ->where("itype_b", "<>", "start")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_pvuv_select != "all" && !empty($index_pvuv_select)) {//查询媒体
            $sel_media_model->where("imediaId", $index_pvuv_select);
        }
        $sel_media_ct = $sel_media_model->group("imediaId")->select();
        //查询子模块
        $sel_c = db::name("data_index");
        if (empty($count_day)) {
            $sel_c->field("count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,
            convert(avg(iduration),DECIMAL(10,2)) as avg_index_time,hour(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H:%i:%s')) as cret_hour,
            CONCAT(FROM_UNIXTIME(icreate_time,'%H:00'),'~',DATE_FORMAT(date_add(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H'),INTERVAL 1 HOUR),'%H:00')) 
            AS time_hour,imediaId as pid,concat(iid,imediaId) as id,concat(imediaId,'2') as desc_ct");
        } else {
            $sel_c->field("count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,
            convert(avg(iduration),decimal(10,2)) as avg_index_time,from_unixtime(icreate_time,'%Y-%m-%d') as time_hour
            ,imediaId as pid,concat(iid,imediaId) as id,concat(imediaId,'2') as desc_ct");
        }
        $sel_c->where("itype_a", "index")
            ->where("itype_b", "<>", "start")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_pvuv_select != "all" && !empty($index_pvuv_select)) {//查询媒体
            $sel_c->where("imediaId", $index_pvuv_select);
        }
        if (empty($count_day)) {
            //按24小时查询
            $sel_content_index = $sel_c->group("imediaId,hour(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H:%i:%s'))")->select();
        } else {
            //按天查询
            $sel_content_index = $sel_c->group("imediaId,FROM_UNIXTIME(icreate_time,'%Y-%m-%d')")->select();
        }
        //合并数组
        $merge_arr = array_merge_recursive($sel_media_ct, $sel_content_index);
        $arr = array();
        foreach ($merge_arr as $v) {
            if (empty($v['pid'])) {
                $pid = 0;
            } else {
                $pid = $v['pid'];
            }
            if (empty($v['media_name'])) {
                $media_name = "";
            } else {
                $media_name = $v['media_name'];
            }
            $arr[] = array(
                "id" => $v['id'],
                "count_index_pv" => $v['count_index_pv'],
                "count_index_uv" => $v['count_index_uv'],
                "avg_index_time" => $v['avg_index_time'],
                "media_name" => $media_name,
                "time_hour" => $v['time_hour'],
                "desc_ct" => $v['desc_ct'],
                "pid" => $pid,
            );
        }
        return $arr;
    }

    /**
     * 首页点击人数/次数
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getIndexClickPeople()
    {
        if (request()->isPost()) {
            $index_content_media = input("index_content_media");//获取媒体信息
            $list_click_position = input("list_click_position");//获取发起位置
            $index_content_data = input("index_content_data");//时间
            $merge_arr = $this->getIndexClickPeopleContent($index_content_media, $list_click_position, $index_content_data);
            $column = $this->getAllModelName();//读取缓存模块
            $product = $this->getAllProduct();//读取缓存模块
            $arr_match = array();
            foreach ($merge_arr as $v) {
                if (empty($v['pid'])) {
                    //查询子类中商品名称
                    preg_match('/[0-9]+/', $v['itype_txt'], $mat);
                    try{
                        $sel_ct = $column[$mat['0']]['column_name'];
                    }catch (\Exception $e){
                        $sel_ct = "";
                    }
                } else {
                    //查询父类中的模块名称
                    preg_match('/[0-9]+/', $v['itype_txt'], $mat);
                    try{
                        $sel_ct =  $product[$mat['0']]['product_name'];
                    }catch (\Exception $e){
                        $sel_ct = "";
                    }
                }
                //判断pid
                if (empty($v['pid'])) {
                    $pid = 0;
                } else {
                    $pid = $v['pid'];
                }
                //判断媒体名称
                if (empty($v['media_name'])) {
                    $media_name = "";
                } else {
                    $media_name = $v['media_name'];
                }
                $arr_match[] = array(
                    "count_index_pv" => $v['count_index_pv'],//点击次数
                    "count_index_uv" => $v['count_index_uv'],//点击人数
                    "media_name" => $media_name,//媒体名称
                    "itype_txt" => $sel_ct,//发起位置
                    "pid" => $pid,//子类
                    "id" => $v['id'],//父类
                    "index_click_ymd" => $v['index_click_ymd']//时间
                );
            }
            return array("code" => 0, "msg" => "", "data" => $arr_match, "count" => count($merge_arr), "is" => true, "tip" => "操作成功");
        } else {
            $this->assign("sel_model", $this->getAllModelNameOption());//查询模块信息
            $this->assign("sel_media", $this->getAllMediaNameOption());//查询媒体信息
            return $this->fetch("getIndexClickPeople");
        }
    }

    /**
     * 首页点击人数/次数导出excel
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getIndexClickPeopleExcel()
    {
        $index_content_media = input("index_content_media");//获取媒体信息
        $list_click_position = input("list_click_position");//获取发起位置
        $index_content_data = input("index_content_data");//时间
        $merge_arr = $this->getIndexClickPeopleContent($index_content_media, $list_click_position, $index_content_data);
        $column = $this->getAllModelName();//读取缓存模块
        $product = $this->getAllProduct();//读取缓存产品
        if (!empty($merge_arr)) {
            foreach ($merge_arr as $k => $v) {
                $a[$k] = $v['desc_ct'];
            }
            array_multisort($a, SORT_DESC, $merge_arr);//排序
        }
        header('Access-Control-Allow-Origin:*');
//        Loader::import("PHPExcel", EXTEND_PATH);//手动引入PHPExcel.php
//        Loader::import('PHPExcel/IOFactory', EXTEND_PATH);//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
        $PHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1", "媒体名称")
            ->setCellValue("B1", "发起位置")
            ->setCellValue("C1", "点击次数")
            ->setCellValue("D1", "点击人数")
            ->setCellValue("E1", "时间");
        //查询获取数据信息
        $rownum = 1;
        foreach ($merge_arr as $v) {
            $rownum++;
            preg_match('/[0-9]+/', $v['itype_txt'], $pro_id);//获取编号
            preg_match('/[a-zA-Z]+/', $v['itype_txt'], $pro_name);//获取是产品还模块开头
            if ($pro_name[0] == "column") {
                $itype_txt = $column[$pro_id[0]]['column_name'];
            } else {
                $itype_txt = $product[$pro_id[0]]['product_name'];
            }
            //判断媒体名称
            if (empty($v['media_name'])) {
                $media_name = "";
            } else {
                $media_name = $v['media_name'];
            }
            $PHPExcel->getActiveSheet()->setCellValue('A' . $rownum, $media_name);
            $PHPExcel->getActiveSheet()->setCellValue('B' . $rownum, $itype_txt);
            $PHPExcel->getActiveSheet()->setCellValue('C' . $rownum, $v['count_index_pv']);
            $PHPExcel->getActiveSheet()->setCellValue('D' . $rownum, $v['count_index_uv']);
            $PHPExcel->getActiveSheet()->setCellValue('E' . $rownum, $v['index_click_ymd']);
        }
        //重命名表
        $PHPExcel->getActiveSheet()->setTitle('Simple');
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=" . date("Y-m-d-H-i-s") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }

    /**
     * 点击人数次数数据
     * @param string $index_content_media 查询媒体
     * @param string $list_click_position 查询模块
     * @param string $index_content_data 日期
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getIndexClickPeopleContent($index_content_media = "", $list_click_position = "", $index_content_data = "")
    {
        //判断日期信息---日期为空时查询前一天数据信息
        if (empty($index_content_data)) {
            $index_content_data = date("Y-m-d", strtotime("-1 day")) . " ~ " . date("Y-m-d", strtotime("-1 day"));
        } else {
            $index_content_data = input("index_content_data");//获取日期信息
        }
        $date = explode("~", $index_content_data);
        $start_time = strtotime($date[0] . " 00:00:00");
        $end_time = strtotime($date[1] . " 23:59:59");
        $count_day = count($this->dateBetweenDay(strtotime($date[1]), strtotime($date[0])));//查询时间间隔
        //$count_day:时间为空时为0，时间区间为一天是为0
        //查询首页模块
        $sel_index_model = db::name("data_index")->alias("a")
            ->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,media_name,from_unixtime(icreate_time,'%Y-%m-%d') as index_click_ymd,itype_b,itype_b as itype_txt,concat(itype_b,imediaId) as id", "concat(itype_b,imediaId,'1')" => "desc_ct"])
            ->join("currency_media b", "a.imediaId=b.media_ident", "left")
            ->where("itype_a", "index")
            ->where("itype_c", "<>", "0")
            ->where("LEFT (itype_b, 6) = 'column'")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_content_media != "all" && !empty($index_content_media)) {
            //判断媒体
            $sel_index_model->where("imediaId", $index_content_media);
        }
        if ($list_click_position != "all" && !empty($list_click_position)) {
            //判断模块
            $sel_index_model->where("itype_b", "column" . $list_click_position);
        }
        $sel_index_model_count = $sel_index_model->group("itype_b,imediaId")->order("imediaId asc")->select();

        //查询子模块信息
        $sel_index = db::name("data_index");
        //$count_day=0代表一天，就按24小时查询，否则就按天查询数据
        if (empty($count_day)) {
            //查询24小时
            $sel_index->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,hour(from_unixtime(icreate_time,'%Y-%m-%d %H')) as from_hour,itype_b,itype_c as itype_txt,concat(itype_b,imediaId) as pid", "concat(itype_b,itype_c,imediaId)" => "id", "CONCAT(FROM_UNIXTIME(icreate_time,'%H:00'),'~',DATE_FORMAT(date_add(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H'),INTERVAL 1 HOUR),'%H:00'))" => "index_click_ymd", "concat(itype_b,imediaId,'1')" => "desc_ct"]);
        } else {
            //按天查询
            $sel_index->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,hour(from_unixtime(icreate_time,'%Y-%m-%d %H')) as from_hour,itype_b,itype_c as itype_txt,concat(itype_b,imediaId) as pid", "concat(itype_b,itype_c,imediaId)" => "id", "FROM_UNIXTIME(icreate_time,'%Y-%m-%d')" => "index_click_ymd", "concat(itype_b,imediaId,'1')" => "desc_ct"]);
        }
        $sel_index->where("itype_a", "index")
            ->where("itype_c", "<>", "0")
            ->where("LEFT (itype_b, 6) = 'column'")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_content_media != "all" && !empty($index_content_media)) {
            //判断媒体
            $sel_index->where("imediaId", $index_content_media);
        }
        if ($list_click_position != "all" && !empty($list_click_position)) {
            //判断模块
            $sel_index->where("itype_b", "column" . $list_click_position);
        }
        if (empty($count_day)) {
            //查询24小时
            $sel_index_count = $sel_index->group("itype_b,imediaId,itype_c,hour(from_unixtime(icreate_time,'%Y-%m-%d %H'))")->select();
        } else {
            //按天查询
            $sel_index_count = $sel_index->group("itype_b,imediaId,itype_c,from_unixtime(icreate_time,'%Y-%m-%d')")->select();
        }
        //合并数组
        $merge_arr = array_merge_recursive($sel_index_model_count, $sel_index_count);
        return $merge_arr;
    }

    /**
     * 商品详情页pv/uv
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getContentPvUv()
    {
        if (request()->isPost()) {
            $index_content_media = input("index_content_media");//获取媒体信息
            $list_click_position = input("list_click_position");//获取发起位置
            $index_content_data = input("index_content_data");//时间
            $merge_arr = $this->getContentPvUvContent($index_content_media, $list_click_position, $index_content_data);
            $column = $this->getAllModelName();//读取缓存模块
            $product = $this->getAllProduct();//读取缓存模块
            $arr_match = array();
            foreach ($merge_arr as $v) {
                if (empty($v['pid'])) {
                    //查询子类中商品名称
                    preg_match('/[0-9]+/', $v['itype_txt'], $mat);
                    try {
                        $sel_ct = $column[$mat['0']]['column_name'];
                    } catch (\Exception $e) {
                        $sel_ct = "";
                    }
                } else {
                    //查询父类中的模块名称
                    preg_match('/[0-9]+/', $v['itype_txt'], $mat);
                    try {
                        $sel_ct = $product[$mat['0']]['product_name'];
                    } catch (\Exception $e) {
                        $sel_ct = "";
                    }
                }
                //判断pid
                if (empty($v['pid'])) {
                    $pid = "0";
                } else {
                    $pid = $v['pid'];
                }
                //判断媒体
                if (empty($v['media_name'])) {
                    $media_name = "";
                } else {
                    $media_name = $v['media_name'];
                }
                $arr_match[] = array(
                    "count_index_pv" => $v['count_index_pv'],//点击次数
                    "count_index_uv" => $v['count_index_uv'],//点击人数
                    "media_name" => $media_name,//媒体名称
                    "itype_txt" => $sel_ct,//发起位置
                    "pid" => $pid,//子类
                    "id" => $v['id'],//父类
                    "index_click_ymd" => $v['index_click_ymd']//时间
                );
            }
            return array("code" => 0, "msg" => "", "data" => $arr_match, "count" => count($merge_arr), "is" => true, "tip" => "操作成功");
        } else {
            $this->assign("sel_model", $this->getAllModelNameOption());//查询模块信息
            $this->assign("sel_media", $this->getAllMediaNameOption());//查询媒体信息
            return $this->fetch("getContentPvUv");
        }
    }

    public function getContentPvUvExcel()
    {
        $index_content_media = input("index_content_media");//获取媒体信息
        $list_click_position = input("list_click_position");//获取发起位置
        $index_content_data = input("index_content_data");//时间
        $merge_arr = $this->getContentPvUvContent($index_content_media, $list_click_position, $index_content_data);
        $column = $this->getAllModelName();//读取缓存模块
        $product = $this->getAllProduct();//读取缓存产品
        if (!empty($merge_arr)) {
            foreach ($merge_arr as $k => $v) {
                $a[$k] = $v['desc_ct'];
            }
            array_multisort($a, SORT_DESC, $merge_arr);//排序
        }
        header('Access-Control-Allow-Origin:*');
//        Loader::import("PHPExcel", EXTEND_PATH);//手动引入PHPExcel.php
//        Loader::import('PHPExcel/IOFactory', EXTEND_PATH);//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
        $PHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1", "媒体名称")
            ->setCellValue("B1", "发起位置")
            ->setCellValue("C1", "PV量")
            ->setCellValue("D1", "UV量")
            ->setCellValue("E1", "时间");
        //查询获取数据信息
        $rownum = 1;
        foreach ($merge_arr as $v) {
            $rownum++;
            preg_match('/[0-9]+/', $v['itype_txt'], $pro_id);//获取编号
            preg_match('/[a-zA-Z]+/', $v['itype_txt'], $pro_name);//获取是产品还模块开头
            if ($pro_name[0] == "column") {
                $itype_txt = $column[$pro_id[0]]['column_name'];
            } else {
                $itype_txt = $product[$pro_id[0]]['product_name'];
            }
            //判断媒体
            if (empty($v['media_name'])) {
                $media_name = "";
            } else {
                $media_name = $v['media_name'];
            }
            $PHPExcel->getActiveSheet()->setCellValue('A' . $rownum, $media_name);
            $PHPExcel->getActiveSheet()->setCellValue('B' . $rownum, $itype_txt);
            $PHPExcel->getActiveSheet()->setCellValue('C' . $rownum, $v['count_index_pv']);
            $PHPExcel->getActiveSheet()->setCellValue('D' . $rownum, $v['count_index_uv']);
            $PHPExcel->getActiveSheet()->setCellValue('E' . $rownum, $v['index_click_ymd']);
        }
        //重命名表
        $PHPExcel->getActiveSheet()->setTitle('Simple');
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=" . date("Y-m-d-H-i-s") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }

    /**
     * 商品详情页pv/uv数据
     * @param string $index_content_media 查询媒体
     * @param string $list_click_position 查询模块
     * @param string $index_content_data 日期
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getContentPvUvContent($index_content_media = "", $list_click_position = "", $index_content_data = "")
    {
        //判断日期信息---日期为空时查询前一天数据信息
        if (empty($index_content_data)) {
            $index_content_data = date("Y-m-d", strtotime("-1 day")) . " ~ " . date("Y-m-d", strtotime("-1 day"));
        } else {
            $index_content_data = input("index_content_data");//获取日期信息
        }
        $date = explode("~", $index_content_data);
        $start_time = strtotime($date[0] . " 00:00:00");
        $end_time = strtotime($date[1] . " 23:59:59");
        $count_day = count($this->dateBetweenDay(strtotime($date[1]), strtotime($date[0])));//查询时间间隔
        //$count_day:时间为空时为0，时间区间为一天是为0
        //查询首页模块
        $sel_index_model = db::name("data_index")->alias("a")
            ->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,media_name,from_unixtime(icreate_time,'%Y-%m-%d') as index_click_ymd,itype_b,itype_b as itype_txt,concat(itype_b,imediaId) as id", "concat(itype_b,imediaId,'1')" => "desc_ct"])
            ->join("currency_media b", "a.imediaId=b.media_ident", "left")
            ->where("itype_a", "index")
            ->where("LEFT (itype_b, 6) = 'column'")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_content_media != "all" && !empty($index_content_media)) {
            //判断媒体
            $sel_index_model->where("imediaId", $index_content_media);
        }
        if ($list_click_position != "all" && !empty($list_click_position)) {
            //判断模块
            $sel_index_model->where("itype_b", "column" . $list_click_position);
        }
        $sel_index_model_count = $sel_index_model->group("itype_b,imediaId")->select();

        //查询子模块信息
        $sel_index = db::name("data_index");
        //$count_day=0代表一天，就按24小时查询，否则就按天查询数据
        if (empty($count_day)) {
            //查询24小时
            $sel_index->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,hour(from_unixtime(icreate_time,'%Y-%m-%d %H')) as from_hour,itype_b,itype_c as itype_txt,concat(itype_b,imediaId) as pid", "concat(itype_b,itype_c,imediaId)" => "id", "CONCAT(FROM_UNIXTIME(icreate_time,'%H:00'),'~',DATE_FORMAT(date_add(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H'),INTERVAL 1 HOUR),'%H:00'))" => "index_click_ymd", "concat(itype_b,imediaId,'1')" => "desc_ct"]);
        } else {
            //按天查询
            $sel_index->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,hour(from_unixtime(icreate_time,'%Y-%m-%d %H')) as from_hour,itype_b,itype_c as itype_txt,concat(itype_b,imediaId) as pid", "concat(itype_b,itype_c,imediaId)" => "id", "FROM_UNIXTIME(icreate_time,'%Y-%m-%d')" => "index_click_ymd", "concat(itype_b,imediaId,'1')" => "desc_ct"]);
        }
        $sel_index->where("itype_a", "index")
            ->where("LEFT (itype_b, 6) = 'column'")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_content_media != "all") {
            //判断媒体
            $sel_index->where("imediaId", $index_content_media);
        }
        if ($list_click_position != "all") {
            //判断模块
            $sel_index->where("itype_b", $list_click_position);
        }
        if (empty($count_day)) {
            //查询24小时
            $sel_index_count = $sel_index->group("itype_b,imediaId,itype_c,hour(from_unixtime(icreate_time,'%Y-%m-%d %H'))")->select();
        } else {
            //按天查询
            $sel_index_count = $sel_index->group("itype_b,imediaId,itype_c,from_unixtime(icreate_time,'%Y-%m-%d')")->select();
        }
        //合并数组
        $merge_arr = array_merge_recursive($sel_index_model_count, $sel_index_count);
        return $merge_arr;
    }


    /**
     * 详情页点击人数/次数
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getContentPeople()
    {
        if (request()->isPost()) {
            $index_content_media = input("index_content_media");//获取媒体信息
            $list_click_position = input("list_click_position");//获取发起位置
            $index_content_data = input("index_content_data");//获取时间
            $merge_arr = $this->getContentPeopleContent($index_content_media, $list_click_position, $index_content_data);
            $column = $this->getAllModelName();//读取缓存模块
            $product = $this->getAllProduct();//读取缓存产品
            $arr_match = array();
            foreach ($merge_arr as $v) {
                preg_match('/[0-9]+/', $v['itype_b'], $pro_id);//获取模块编号
                preg_match('/[0-9]+/', $v['itype_txt'], $pro_name);//产品
                $pro_id_nam = $column[$pro_id[0]]['column_name'];//模块信息名称转换
                if ($v['itype_txt'] == "btn") {
                    $pro_name_mame = "申请点击";
                } elseif ($v['itype_txt'] == "applybtn") {
                    $pro_name_mame = "提交资料申请";
                } else {
                    try {
                        $pro_name_mame = $product[$pro_name[0]]['product_name'];//产品名称转换
                    } catch (\Exception $e) {
                        $pro_name_mame = "";
                    }
                }
                //判断pid
                if (empty($v['pid'])) {
                    $pid = "";
                } else {
                    $pid = $v['pid'];
                }
                //判断媒体
                if (empty($v['media_name'])) {
                    $media_name = "";
                } else {
                    $media_name = $v['media_name'];
                }
                $arr_match[] = array(
                    "pid" => $pid,
                    "id" => $v['id'],
                    "media_name" => $media_name,
                    "index_click_pv" => $v['count_pv'],
                    "index_click_uv" => $v['count_uv'],
                    "index_click_position" => $pro_id_nam,//发起位置
                    "index_click_name" => $pro_name_mame,//产品名称
                    "index_click_ymd" => $v['cret_ymd']
                );
            }
            return array("code" => 0, "msg" => "", "data" => $arr_match, "count" => count($arr_match), "is" => true, "tip" => "操作成功");
        } else {
            $this->assign("sel_model", $this->getAllModelNameOption());//查询模块信息
            $this->assign("sel_media", $this->getAllMediaNameOption());//查询媒体信息
            return $this->fetch("getContentPeople");
        }
    }

    /**
     * 详情页点击人数/次数导出excel
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getContentPeopleExcel()
    {
        $index_content_media = input("index_content_media");//获取媒体信息
        $list_click_position = input("list_click_position");//获取发起位置
        $index_content_data = input("index_content_data");//获取时间
        $column = $this->getAllModelName();//读取缓存模块
        $product = $this->getAllProduct();//读取缓存产品
        $merge_arr = $this->getContentPeopleContent($index_content_media, $list_click_position, $index_content_data);//获取数据信息
        if (!empty($merge_arr)) {
            foreach ($merge_arr as $k => $v) {
                $a[$k] = $v['desc_ct'];
            }
            array_multisort($a, SORT_DESC, $merge_arr);//排序
        }
        header('Access-Control-Allow-Origin:*');
//        Loader::import("PHPExcel", EXTEND_PATH);//手动引入PHPExcel.php
//        Loader::import('PHPExcel/IOFactory', EXTEND_PATH);//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
        $PHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1", "媒体名称")
            ->setCellValue("B1", "发起位置")
            ->setCellValue("C1", "产品")
            ->setCellValue("D1", "点击次数")
            ->setCellValue("E1", "点击人数")
            ->setCellValue("F1", "时间");
        //查询获取数据信息
        $rownum = 1;
        foreach ($merge_arr as $v) {
            $rownum++;
            preg_match('/[0-9]+/', $v['itype_b'], $pro_id);//获取编号
            preg_match('/[0-9]+/', $v['itype_txt'], $pro_name);//产品
            $pro_id_nam = $column[$pro_id[0]]['column_name'];//模块信息名称转换
            if ($v['itype_txt'] == "btn") {
                $pro_name_mame = "申请点击";
            } elseif ($v['itype_txt'] == "applybtn") {
                $pro_name_mame = "提交资料申请";
            } else {
                $pro_name_mame = $product[$pro_name[0]]['product_name'];//产品名称转换
            }
            //判断媒体
            if (empty($v['media_name'])) {
                $media_name = "";
            } else {
                $media_name = $v['media_name'];
            }
            $PHPExcel->getActiveSheet()->setCellValue('A' . $rownum, $media_name);
            $PHPExcel->getActiveSheet()->setCellValue('B' . $rownum, $pro_id_nam);
            $PHPExcel->getActiveSheet()->setCellValue('C' . $rownum, $pro_name_mame);
            $PHPExcel->getActiveSheet()->setCellValue('D' . $rownum, $v['count_pv']);
            $PHPExcel->getActiveSheet()->setCellValue('E' . $rownum, $v['count_uv']);
            $PHPExcel->getActiveSheet()->setCellValue('F' . $rownum, $v['cret_ymd']);
        }
        //重命名表
        $PHPExcel->getActiveSheet()->setTitle('Simple');
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=" . date("Y-m-d-H-i-s") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }


    /**
     * 详情页点击人数/次数数据
     * @param string $media 媒体标识
     * @param string $column 模块名称
     * @param string $sel_date 查询时间
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getContentPeopleContent($media = "", $column = "", $sel_date = "")
    {
        //判断日期信息---日期为空时查询前一天数据信息
        if (empty($sel_date)) {
            $sel_date = date("Y-m-d", strtotime("-1 day")) . " ~ " . date("Y-m-d", strtotime("-1 day"));
        } else {
            $sel_date = input("index_content_data");//获取日期信息
        }
        $date = explode("~", $sel_date);
        $start_time = strtotime($date[0] . " 00:00:00");
        $end_time = strtotime($date[1] . " 23:59:59");
        $count_day = count($this->dateBetweenDay(strtotime($date[1]), strtotime($date[0])));//查询时间间隔
        //查询父类模块
        $sel_f_m = db::name("data_index")->alias("a")
            ->field(["count(iip) as count_pv,count(DISTINCT iip) as count_uv,imediaId,media_name,itype_b,itype_c as itype_txt,from_unixtime(icreate_time,'%Y-%m-%d') as cret_ymd", "concat(imediaId,itype_b,itype_c)" => "id", "concat(imediaId,itype_b,itype_c,'1')" => "desc_ct"])
            ->join("currency_media b", "a.imediaId=b.media_ident", "left")
            ->where("itype_d", "detail")
            ->where("itype_e", "neq", "0")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if (!empty($media) && $media != "all") {
            //根据媒体查询
            $sel_f_m->where("imediaId", $media);
        }
        if (!empty($column) && $column != "all") {
            //根据模块查询
            $sel_f_m->where("itype_b", "" . $column);
        }
        $sel_f = $sel_f_m->group("imediaId,itype_b,itype_c")
            ->select();

        //查询子模块
        $sel_child = Db::name("data_index");
        if (empty($count_day)) {
            //按小时查询
            $sel_child->field(["count(iip) as count_pv,count(DISTINCT iip) as count_uv,imediaId,itype_b,itype_c,itype_c,itype_e as itype_txt", "CONCAT(FROM_UNIXTIME(icreate_time,'%H:00'),'~',DATE_FORMAT(date_add(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H'),INTERVAL 1 HOUR),'%H:00'))" => "cret_ymd", "concat(imediaId,itype_b,itype_c)" => "pid", "concat(imediaId,itype_b,itype_c,itype_e)" => "id", "concat(imediaId,itype_b,itype_c,'1')" => "desc_ct"]);
        } else {
            //按天查询
            $sel_child->field(["count(iip) as count_pv,count(DISTINCT iip) as count_uv,imediaId,itype_b,itype_c,itype_c,itype_e as itype_txt", "FROM_UNIXTIME(icreate_time, '%Y-%m-%d')" => "cret_ymd", "concat(imediaId,itype_b,itype_c)" => "pid", "concat(imediaId,itype_b,itype_c,itype_e)" => "id", "concat(imediaId,itype_b,itype_c,'1')" => "desc_ct"]);
        }
        $sel_child->where("itype_d", "detail")
            ->where("itype_e", "neq", "0")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if (!empty($media) && $media != "all") {
            //根据媒体查询
            $sel_child->where("imediaId", $media);
        }
        if (!empty($column) && $column != "all") {
            //根据模块查询
            $sel_child->where("itype_b", $media);
        }
        if (empty($count_day)) {
            //按小时查询
            $sel_people_content = $sel_child->group("imediaId,itype_b,itype_c,itype_e,HOUR (from_unixtime(icreate_time, '%Y-%m-%d %H'))")
                ->select();
        } else {
            //按天分组
            $sel_people_content = $sel_child->group("imediaId,itype_b,itype_c,itype_e,FROM_UNIXTIME(icreate_time, '%Y-%m-%d')")
                ->select();
        }
        //合并数组
        $merge_arr = array_merge_recursive($sel_f, $sel_people_content);
        return $merge_arr;
    }

    /**
     * 用户信息
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserDataCenter()
    {
        if (request()->isPost()) {
            $index_media_name = input("index_media_name");//获取媒体
            $index_date = input("index_date");//获取日期
            $limit = input("limit");//每页显示行数
            $page = input("page");//页码
            $getContent = $this->getUserData($index_media_name, $index_date, $limit, $page);
            return ['code' => 0, 'msg' => '获取成功!', 'data' => $getContent['data'], 'count' => $getContent['total']];
//            exit();
//            $start = ($page - 1) * $limit;//开始位置
//            $arrleng = count($new_arr);//总数量
//            $news_new_arr = array();//定义一个接受数组
//            for ($i = $start; $i < ($start + $limit); $i++) {
//                if ($i >= $arrleng) break;
//                $news_new_arr[] = $new_arr[$i];//每页显示的数据信息
//            }
//            $arr = array(
//                "code" => 0,
//                "msg" => "获取成功",
//                "data" => $news_new_arr,
//                "count" => count($new_arr)
//            );
//            return $arr;
        } else {
            $this->assign("sel_media", $this->getAllMediaIdOption());//查询媒体信息
            return $this->fetch("getUserDataCenter");
        }
    }

    /**
     * 用户信息导出excel
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserDownExcel()
    {
        $sel_user = $this->getUserData(input("index_media_name"), input("index_date"));//根据媒体与日期查询用户信息
        header('Access-Control-Allow-Origin:*');
//        Loader::import("PHPExcel", EXTEND_PATH);//手动引入PHPExcel.php
//        Loader::import('PHPExcel/IOFactory', EXTEND_PATH);//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
        $PHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1", "日期")
            ->setCellValue("B1", "消耗渠道")
            ->setCellValue("C1", "媒体")
            ->setCellValue("D1", "模块")
            ->setCellValue("E1", "商品")
            ->setCellValue("F1", "姓名")
            ->setCellValue("G1", "电话")
            ->setCellValue("H1", "身份证");
        //查询获取数据信息
        $rownum = 1;
        foreach ($sel_user['data'] as $v) {
            $rownum++;
            $PHPExcel->getActiveSheet()->setCellValue('A' . $rownum, $v['media_date']);
            $PHPExcel->getActiveSheet()->setCellValue('B' . $rownum, $v['media_where']);
            $PHPExcel->getActiveSheet()->setCellValue('C' . $rownum, $v['media_name']);
            $PHPExcel->getActiveSheet()->setCellValue('D' . $rownum, $v['media_model']);
            $PHPExcel->getActiveSheet()->setCellValue('E' . $rownum, $v['media_commodity']);
            $PHPExcel->getActiveSheet()->setCellValue('F' . $rownum, $v['u_name']);
            $PHPExcel->getActiveSheet()->setCellValue('G' . $rownum, $v['u_tel']);
            $PHPExcel->getActiveSheet()->setCellValue('H' . $rownum, $v['u_no']);
        }
        //重命名表
        $PHPExcel->getActiveSheet()->setTitle('Simple');
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=" . date("Y-m-d-H-i-s") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }

    /**
     * 用户信息数据
     * @param string $index_media_name 查询媒体
     * @param string $index_date 查询时间
     * @param int $limit 页码
     * @param int $page 每页数量
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getUserData($index_media_name = "", $index_date = "", $limit = 10000, $page = 1)
    {
        //查询申请表
        $sel_app = db::name("apply");
        if ($index_media_name != "all" && !empty($index_media_name)) {
            //根据媒体查询
            $sel_app->where("apply_media_id", $index_media_name);
        }
        if (!empty($index_date)) {//根据时间查询
            $date = explode("~", $index_date);//截取时间段
            //将时间转换为时间戳
            $start_date = strtotime($date[0] . " 00:00:00");
            $end_date = strtotime($date[1] . " 23:59:59");
            //根据媒体查询
            $sel_app->where("apply_create_time", "between", [$start_date, $end_date]);
        } else {
            $sel_app->order("apply_create_time", "desc");
        }
        $sel_app_content = $sel_app->paginate(array('list_rows' => $limit, 'page' => $page))
            ->toArray();
        //获取缓存媒体信息
        $media = $this->getAllMediaName();
        //获取栏目信息
        $model = $this->getAllModelName();
        //获取产品信息
        $product = $this->getAllProductA();
        //初始化
        $htm_arr = array();
        $a = array();
        $htm_arr['total'] = $sel_app_content['total'];//行数
        //将数据转为汉字
        foreach ($sel_app_content['data'] as $k => $v) {
            //判断媒体是否存在
            if (empty($media[$v['apply_media_id']]['media_name'])) {
                $media_name = "";
            } else {
                $media_name = $media[$v['apply_media_id']]['media_name'];
            }
            //判断模块是否存在
            if (empty($model[$v['apply_column_id']]['column_name'])) {
                $column_name = "";
            } else {
                $column_name = $model[$v['apply_column_id']]['column_name'];
            }
            //判断商品是否存在
            if (empty($product[$v['apply_product_id']]['product_name'])) {
                $product_name = "";
            } else {
                $product_name = $product[$v['apply_product_id']]['product_name'];
            }
            $htm_arr['data'][] = array(
                "media_date" => date("Y-m-d H:i:s", $v['apply_create_time']),//日期
                "media_where" => "媒体",//消耗渠道
                "media_name" => $media_name,//媒体
                "media_model" => $column_name,//模块
                "media_commodity" => $product_name,//商品
                "u_name" => $v['apply_name'],//名称
                "u_tel" => $v['apply_phone'],//电话
                "u_no" => $v['apply_sfznumber'],//身份证
            );
            $a[$k] = $v['apply_create_time'];
        }
        array_multisort($a, SORT_DESC, $sel_app_content['data']);//排序
        if (empty($htm_arr['data'])) {
            $htm_arr['data'] = array();
        }
        return $htm_arr;
    }

    /**
     * 移动设备点击(首页点击人数/次数)
     * @param Request $request
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMobileIndexPeople(Request $request)
    {
        if ($request->isPost()) {
            $index_content_media = input("index_content_media");//获取媒体信息
            $list_click_position = input("list_click_position");//获取发起位置
            $index_content_data = input("index_content_data");//时间
            $merge_arr = $this->getMobileIndexPeopleContent($index_content_media, $list_click_position, $index_content_data);
            $column = $this->getAllModelName();//读取缓存模块
            $product = $this->getAllProduct();//读取缓存模块
            $arr_match = array();
            foreach ($merge_arr as $v) {
                preg_match('/[0-9]+/', $v['itype_txt'], $pro_id);//获取编号
                preg_match('/[a-zA-Z]+/', $v['itype_txt'], $pro_name);//获取是产品还模块开头
                if ($pro_name[0] == "column") {
                    try {
                        $sel_ct = $column[$pro_id[0]]['column_name'];
                    } catch (\Exception $e) {
                        $sel_ct = "";
                    }
                } elseif ($pro_name[0] == "product") {
                    try {
                        $sel_ct = $product[$pro_id[0]]['product_name'];
                    } catch (\Exception $e) {
                        $sel_ct = "";
                    }
                } else {
                    $sel_ct = "汇总统计";
                }
                if (empty($v['dev_count'])) {
                    $v['dev_count'] = 0;
                }
                if (empty($v['pid'])) {
                    $v['pid'] = 0;
                }
                if (empty($v['media_name'])) {
                    $v['media_name'] = "";
                }
                $arr_match[] = array(
                    "count_index_pv" => $v['count_index_pv'],//点击次数
                    "count_index_uv" => $v['count_index_uv'],//点击人数
                    "media_name" => $v['media_name'],//媒体名称
                    "idevice" => $v['idevice'],//设备
                    "dev_count" => $v['dev_count'],//设备点击次数
                    "itype_txt" => $sel_ct,//发起位置
                    "pid" => $v['pid'],//子类
                    "id" => $v['id'],//父类
                    "index_click_ymd" => $v['index_click_ymd']//时间
                );
            }
            return array("code" => 0, "msg" => "", "data" => $arr_match, "count" => count($merge_arr), "is" => true, "tip" => "操作成功");
        } else {
            $this->assign("sel_model", $this->getAllModelNameOption());//查询模块信息
            $this->assign("sel_media", $this->getAllMediaNameOption());//查询媒体信息
            return $this->fetch("getMobileIndexPeople");
        }
    }

    //移动设备导出excel(首页点击人数/次数)
    public function getMobileIndexPeopleExcel()
    {
        $index_content_media = input("index_content_media");//获取媒体信息
        $list_click_position = input("list_click_position");//获取发起位置
        $index_content_data = input("index_content_data");//时间
        $merge_arr = $this->getMobileIndexPeopleContent($index_content_media, $list_click_position, $index_content_data);
        $column = $this->getAllModelName();//读取缓存模块
        $product = $this->getAllProduct();//读取缓存产品
        if (!empty($merge_arr)) {
            foreach ($merge_arr as $k => $v) {
                $a[$k] = $v['desc_ct'];
            }
            array_multisort($a, SORT_ASC, $merge_arr);//排序
        }
        header('Access-Control-Allow-Origin:*');
//        Loader::import("PHPExcel", EXTEND_PATH);//手动引入PHPExcel.php
//        Loader::import('PHPExcel/IOFactory', EXTEND_PATH);//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
        $PHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1", "媒体名称")
            ->setCellValue("B1", "发起位置")
            ->setCellValue("C1", "设备")
            ->setCellValue("D1", "点击次数")
            ->setCellValue("E1", "点击人数")
            ->setCellValue("F1", "时间");
        //查询获取数据信息
        $rownum = 1;
        foreach ($merge_arr as $v) {
            $rownum++;
            preg_match('/[0-9]+/', $v['itype_txt'], $pro_id);//获取编号
            preg_match('/[a-zA-Z]+/', $v['itype_txt'], $pro_name);//获取是产品还模块开头
            if ($pro_name[0] == "column") {
                $sel_ct = $column[$pro_id[0]]['column_name'];
            } elseif ($pro_name[0] == "product") {
                $sel_ct = $product[$pro_id[0]]['product_name'];
            } else {
                $sel_ct = "汇总统计";
            }
            $PHPExcel->getActiveSheet()->setCellValue('A' . $rownum, $v['media_name']);
            $PHPExcel->getActiveSheet()->setCellValue('B' . $rownum, $sel_ct);
            $PHPExcel->getActiveSheet()->setCellValue('C' . $rownum, $v['idevice']);
            $PHPExcel->getActiveSheet()->setCellValue('D' . $rownum, $v['count_index_pv']);
            $PHPExcel->getActiveSheet()->setCellValue('E' . $rownum, $v['count_index_uv']);
            $PHPExcel->getActiveSheet()->setCellValue('F' . $rownum, $v['index_click_ymd']);
        }
        //重命名表
        $PHPExcel->getActiveSheet()->setTitle('Simple');
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=" . date("Y-m-d-H-i-s") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }

    /**
     * 移动设备数据处理(首页点击人数/次数)
     * @param string $index_content_media 媒体
     * @param string $list_click_position 模块
     * @param string $index_content_data 日期
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getMobileIndexPeopleContent($index_content_media = "", $list_click_position = "", $index_content_data = "")
    {
        //判断日期信息---日期为空时查询前一天数据信息
        if (empty($index_content_data)) {
            $index_content_data = date("Y-m-d", strtotime("-1 day")) . " ~ " . date("Y-m-d", strtotime("-1 day"));
        } else {
            $index_content_data = input("index_content_data");//获取日期信息
        }
        $date = explode("~", $index_content_data);
        $start_time = strtotime($date[0] . " 00:00:00");
        $end_time = strtotime($date[1] . " 23:59:59");
        $count_day = count($this->dateBetweenDay(strtotime($date[1]), strtotime($date[0])));//查询时间间隔
        //$count_day:时间为空时为0，时间区间为一天是为0
        //查询首页模块
        $sel_index_model = Db::name("data_index")->alias("a")
            ->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,media_name,idevice,from_unixtime(icreate_time,'%Y-%m-%d') as index_click_ymd,itype_b,itype_b as itype_txt", "concat(itype_b,imediaId,idevice)" => "id", "concat(itype_b,imediaId,idevice,'1')" => "desc_ct"])
            ->join("currency_media b", "a.imediaId=b.media_ident", "left")
            ->where("itype_a", "index")
            ->where("itype_c", "<>", "0")
            ->where("LEFT (itype_b, 6) = 'column'")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_content_media != "all" && !empty($index_content_media)) {
            //判断媒体
            $sel_index_model->where("imediaId", $index_content_media);
        }
        if ($list_click_position != "all" && !empty($list_click_position)) {
            //判断模块
            $sel_index_model->where("itype_b", "column" . $list_click_position);
        }
        $sel_index_model_count = $sel_index_model->group("itype_b,imediaId,idevice")->order("imediaId asc")->select();

        //查询总媒体下人数次数、不分模块与产品，不重复点击人数
        $sel_index_sum_model = Db::connect("maimang_db")->name("data_index")->alias("a")
            ->field([
                "count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,media_name,idevice,
            from_unixtime(icreate_time,'%Y-%m-%d') as index_click_ymd,itype_b,concat(iid,imediaId) as id",
                "concat(iid,imediaId,1)" => "itype_txt", "concat(itype_b,imediaId,idevice,'3')" => "desc_ct"
            ])
            ->join("currency_media b", "a.imediaId=b.media_ident", "left")
            ->where("itype_a", "index")
            ->where("itype_c", "<>", "0")
            ->where("LEFT (itype_b, 6) = 'column'")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_content_media != "all" && !empty($index_content_media)) {
            //判断媒体
            $sel_index_sum_model->where("imediaId", $index_content_media);
        }
        if ($list_click_position != "all" && !empty($list_click_position)) {
            //判断模块
            $sel_index_sum_model->where("itype_b", "column" . $list_click_position);
        }
        $sel_index_model_sum_count = $sel_index_sum_model->group("imediaId")->order("imediaId asc")->select();

        //查询子模块信息
        $sel_index = Db::connect("maimang_db")->name("data_index");
        //$count_day=0代表一天，就按24小时查询，否则就按天查询数据
        if (empty($count_day)) {
            //查询24小时
            $sel_index->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,hour(from_unixtime(icreate_time,'%Y-%m-%d %H')) as from_hour,idevice,itype_b,itype_c as itype_txt", "concat(itype_b,imediaId,idevice)" => "pid", "concat(itype_b,itype_c,idevice)" => "id", "CONCAT(FROM_UNIXTIME(icreate_time,'%H:00'),'~',DATE_FORMAT(date_add(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H'),INTERVAL 1 HOUR),'%H:00'))" => "index_click_ymd", "concat(itype_b,imediaId,idevice,'2')" => "desc_ct"]);
        } else {
            //按天查询
            $sel_index->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,hour(from_unixtime(icreate_time,'%Y-%m-%d %H')) as from_hour,idevice,itype_b,itype_c as itype_txt", "concat(itype_b,imediaId,idevice)" => "pid", "concat(itype_b,itype_c,idevice)" => "id", "FROM_UNIXTIME(icreate_time,'%Y-%m-%d')" => "index_click_ymd", "concat(itype_b,imediaId,idevice,'2')" => "desc_ct"]);
        }
        $sel_index->where("itype_a", "index")
            ->where("itype_c", "<>", "0")
            ->where("LEFT (itype_b, 6) = 'column'")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_content_media != "all" && !empty($index_content_media)) {
            //判断媒体
            $sel_index->where("imediaId", $index_content_media);
        }
        if ($list_click_position != "all" && !empty($list_click_position)) {
            //判断模块
            $sel_index->where("itype_b", $list_click_position);
        }
        if (empty($count_day)) {
            //查询24小时
            $sel_index_count = $sel_index->group("itype_b,imediaId,itype_c,idevice,hour(from_unixtime(icreate_time,'%Y-%m-%d %H'))")->select();
        } else {
            //按天查询
            $sel_index_count = $sel_index->group("itype_b,imediaId,itype_c,idevice,from_unixtime(icreate_time,'%Y-%m-%d')")->select();
        }
        //合并数组
        $merge_arr = array_merge_recursive($sel_index_model_count, $sel_index_count, $sel_index_model_sum_count);
        return $merge_arr;
    }

    public function getMobileIndexPvUv(Request $request){
        if($request->isPost()){
            $index_pvuv_select = input("index_pvuv_select");//获取媒体信息
            $index_pvuv_date = input("index_pvuv_date");//查询时间
            $limit = input("limit");//每页显示行数
            $page = input("page");//页码
            //查询首页一天中的pv/uv
            //$count_day:时间为空时为0，时间区间为一天是为0
            $sel_content_index = $this->getMobileIndexPvUvContent($index_pvuv_select, $index_pvuv_date, $limit, $page);
            return ['code' => 0, 'msg' => '获取成功!', 'data' => $sel_content_index, 'count' => count($sel_content_index)];
        }else{
            $this->assign("sel_media", $this->getAllMediaNameOption());//查询媒体名称
            return $this->fetch("getMobileIndexPvUv");
        }
    }

    public function getMobileIndexPvUvExcel(){
        //查询数据
        $sel_content_index = $this->getMobileIndexPvUvContent(input("index_pvuv_select"), input("index_pvuv_date"));
        if (!empty($sel_content_index)) {
            foreach ($sel_content_index as $k => $v) {
                $a[$k] = $v['desc_ct'];
//                $b[$k] = $v['count_index_pv'];
            }
            array_multisort($a, SORT_ASC, $sel_content_index);//排序
        }
        header('Access-Control-Allow-Origin:*');
//        Loader::import("PHPExcel", EXTEND_PATH);//手动引入PHPExcel.php
//        Loader::import('PHPExcel/IOFactory', EXTEND_PATH);//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
        $PHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1", "媒体名称")
            ->setCellValue("B1", "设备")
            ->setCellValue("C1", "首页PV量")
            ->setCellValue("D1", "首页UV量")
            ->setCellValue("E1", "首页平均停留时长(/s)")
            ->setCellValue("F1", "时间");
        //查询获取数据信息
        $rownum = 1;
        foreach ($sel_content_index as $v) {
            $rownum++;
            $PHPExcel->getActiveSheet()->setCellValue('A' . $rownum, $v['media_name']);
            $PHPExcel->getActiveSheet()->setCellValue('B' . $rownum, $v['idevice']);
            $PHPExcel->getActiveSheet()->setCellValue('C' . $rownum, $v['count_index_pv']);
            $PHPExcel->getActiveSheet()->setCellValue('D' . $rownum, $v['count_index_uv']);
            $PHPExcel->getActiveSheet()->setCellValue('E' . $rownum, $v['avg_index_time']);
            $PHPExcel->getActiveSheet()->setCellValue('F' . $rownum, $v['time_hour']);
        }
        //重命名表
        $PHPExcel->getActiveSheet()->setTitle('Simple');
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=" . date("Y-m-d-H-i-s") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }

    protected function getMobileIndexPvUvContent($index_pvuv_select = "", $index_pvuv_date = "", $limit = 10000, $page = 1)
    {
        //判断日期信息---日期为空时查询前一天数据信息
        if (empty($index_pvuv_date)) {
            $index_pvuv_date = date("Y-m-d", strtotime("-1 day")) . " ~ " . date("Y-m-d", strtotime("-1 day"));
        } else {
            $index_pvuv_date = input("index_pvuv_date");//获取日期信息
        }
        $date = explode("~", $index_pvuv_date);
        $start_time = strtotime($date[0] . " 00:00:00");
        $end_time = strtotime($date[1] . " 23:59:59");
        $count_day = count($this->dateBetweenDay(strtotime($date[1]), strtotime($date[0])));
        //查询所有的媒体
        $sel_media_model = Db::connect("maimang_db")->name("data_index")->alias("a")
            ->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,media_name,idevice,count(idevice) as dev_count,
            convert(avg(iduration),decimal(10,2)) as avg_index_time,from_unixtime(icreate_time,'%Y-%m-%d') as time_hour
            ,CONCAT(imediaId,idevice) as id","concat(imediaId,idevice,'1')"=>"desc_ct"])
            ->join("currency_media b", "a.imediaId=b.media_ident", "left")
            ->where("itype_a", "index")
            ->where("itype_b", "<>", "start")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_pvuv_select != "all" && !empty($index_pvuv_select)) {//查询媒体
            $sel_media_model->where("imediaId", $index_pvuv_select);
        }
        $sel_media_ct = $sel_media_model->group("imediaId,idevice")->select();

        //查询子模块
        $sel_c = Db::connect("maimang_db")->name("data_index");
        if (empty($count_day)) {
            $sel_c->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,idevice,count(idevice) as dev_count,
            convert(avg(iduration),DECIMAL(10,2)) as avg_index_time,hour(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H:%i:%s')) as cret_hour,
            CONCAT(FROM_UNIXTIME(icreate_time,'%H:00'),'~',DATE_FORMAT(date_add(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H'),INTERVAL 1 HOUR),'%H:00')) 
            AS time_hour,CONCAT(imediaId,idevice) as pid,concat(iid,imediaId) as id","concat(imediaId,idevice,'2')"=>"desc_ct"]);
        } else {
            $sel_c->field(["count(iip) as count_index_pv,count(DISTINCT iip) as count_index_uv,idevice,count(idevice) as dev_count,
            convert(avg(iduration),decimal(10,2)) as avg_index_time,from_unixtime(icreate_time,'%Y-%m-%d') as time_hour
            ,CONCAT(imediaId,idevice) as pid,concat(iid,imediaId) as id","concat(imediaId,idevice,'2')"=>"desc_ct"]);
        }
        $sel_c->where("itype_a", "index")
            ->where("itype_b", "<>", "start")
            ->where("icreate_time", "between", [$start_time, $end_time]);
        if ($index_pvuv_select != "all" && !empty($index_pvuv_select)) {//查询媒体
            $sel_c->where("imediaId", $index_pvuv_select);
        }
        if (empty($count_day)) {
            //按24小时查询
            $sel_content_index = $sel_c->group("imediaId,idevice,hour(FROM_UNIXTIME(icreate_time,'%Y-%m-%d %H:%i:%s'))")->select();
        } else {
            //按天查询
            $sel_content_index = $sel_c->group("imediaId,idevice,FROM_UNIXTIME(icreate_time,'%Y-%m-%d')")->select();
        }
        //合并数组
        $merge_arr = array_merge_recursive($sel_media_ct, $sel_content_index);
        $arr = array();
        foreach ($merge_arr as $v) {
            if(empty($v['pid'])){
                $v['pid']=0;
            }
            if(empty($v['media_name'])){
                $v['media_name']="";
            }
            $arr[] = array(
                "id" => $v['id'],
                "count_index_pv" => $v['count_index_pv'],
                "count_index_uv" => $v['count_index_uv'],
                "avg_index_time" => $v['avg_index_time'],
                "media_name" => $v['media_name'],
                "idevice" => $v['idevice'],
                "dev_count" => $v['dev_count'],
                "time_hour" => $v['time_hour'],
                "desc_ct" => $v['desc_ct'],
                "pid" => $v['pid'],
            );
        }
        return $arr;
    }


    /**
     * 首页pv/uv导出excel
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
//    public function getIndexUvPvDownExcel()
//    {
//        $sel = $this->getCsDataA();//查询数据
//        header('Access-Control-Allow-Origin:*');
//        Loader::import("PHPExcel", EXTEND_PATH);//手动引入PHPExcel.php
//        Loader::import('PHPExcel/IOFactory', EXTEND_PATH);//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
//        $PHPExcel = new \PHPExcel();//实例化
//        $PHPExcel->setActiveSheetIndex(0)
//            ->setCellValue("A1", "媒体名称")
//            ->setCellValue("B1", "首页pv量")
//            ->setCellValue("C1", "首页uv量")
//            ->setCellValue("D1", "首页平均停留时长(秒)")
//            ->setCellValue("E1", "日期");
//        //查询获取数据信息
//        $rownum = 1;
//        foreach ($sel['data'] as $v) {
//            $rownum++;
//            $PHPExcel->getActiveSheet()->getStyle('D' . $rownum)->getNumberFormat()->setFormatCode("0.00");//设置格式保留两位小数
//            $PHPExcel->getActiveSheet()->setCellValue('A' . $rownum, $v['media_name']);
//            $PHPExcel->getActiveSheet()->setCellValue('B' . $rownum, $v['index_pv']);
//            $PHPExcel->getActiveSheet()->setCellValue('C' . $rownum, $v['index_uv']);
//            $PHPExcel->getActiveSheet()->setCellValue('D' . $rownum, $v['avg_pv_iduration']);
//            $PHPExcel->getActiveSheet()->setCellValue('E' . $rownum, $v['time_hour']);
//        }
//        //重命名表
//        $PHPExcel->getActiveSheet()->setTitle('Simple');
//        ob_end_clean();//清除缓冲区,避免乱码
//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header("Content-Disposition: attachment; filename=" . date("Y-m-d-H-i-s") . ".xls");
//        header('Cache-Control: max-age=0');
//        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
//        $objWriter->save("php://output");
//        exit;
//    }

    /**
     * 查询所有的模块栏目名称
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllModelName()
    {
        $sel_all_model = \Cache::get("all_model");//获取栏目缓存信息
        if (empty($sel_all_model)) {
            $sel_arr = db("column")->select();
            //将模块编号id变为数组标识
            $arr = array();
            foreach ($sel_arr as $v) {
                $arr[$v['column_id']] = array(
                    "column_id" => $v['column_id'],
                    "column_media_id" => $v['column_media_id'],
                    "column_name" => $v['column_name'],
                    "column_summary" => $v['column_summary'],
                    "column_create_time" => $v['column_create_time'],
                );
            }
            \Cache::set("all_model", $arr, 0);//缓存栏目信息
            $sel_all_model = \Cache::get("all_model");//获取栏目缓存信息
        }
        return $sel_all_model;
    }

    /**
     * 查询所有的商品不管是删除还是其他状态都查询
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllProductA()
    {
        $sel_all_pro = \Cache::get("all_pro_a");//获取缓存所有的的商品信息
        if (empty($sel_all_pro)) {
            $sel_pro = db::name("product")
                ->select();
            //将模块编号id变为数组标识
            $arr = array();
            foreach ($sel_pro as $v) {
                $arr[$v['product_id']] = array(
                    "product_id" => $v['product_id'],
                    "product_name" => $v['product_name'],
                    "product_logo" => $v['product_logo'],
                    "product_interest_type" => $v['product_interest_type'],
                    "product_interest" => $v['product_interest'],
                    "product_apply_people" => $v['product_apply_people'],
                    "product_term" => $v['product_term'],
                    "product_adopt" => $v['product_adopt'],
                    "product_max_amount" => $v['product_max_amount'],
                    "product_url" => $v['product_url'],
                    "product_tag" => $v['product_tag'],
                    "product_status" => $v['product_status'],
                    "product_info" => $v['product_info'],
                    "product_create_time" => $v['product_create_time']
                );
            }
            \Cache::set("all_pro_a", $arr, 0);
            $sel_all_pro = \Cache::get("all_pro_a");
        }
        return $sel_all_pro;
    }

    /**
     * 查询所有商品并且状态为正常
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllProduct()
    {
        $sel_all_pro = \Cache::get("all_pro");//获取缓存所有的的商品信息
        if (empty($sel_all_pro)) {
            $sel_pro = db::name("product")
                ->where("product_status", "1")
                ->select();
            //将模块编号id变为数组标识
            $arr = array();
            foreach ($sel_pro as $v) {
                $arr[$v['product_id']] = array(
                    "product_id" => $v['product_id'],
                    "product_name" => $v['product_name'],
                    "product_logo" => $v['product_logo'],
                    "product_interest_type" => $v['product_interest_type'],
                    "product_interest" => $v['product_interest'],
                    "product_apply_people" => $v['product_apply_people'],
                    "product_term" => $v['product_term'],
                    "product_adopt" => $v['product_adopt'],
                    "product_max_amount" => $v['product_max_amount'],
                    "product_url" => $v['product_url'],
                    "product_tag" => $v['product_tag'],
                    "product_status" => $v['product_status'],
                    "product_info" => $v['product_info'],
                    "product_create_time" => $v['product_create_time']
                );
            }
            \Cache::set("all_pro", $arr, 0);
            $sel_all_pro = \Cache::get("all_pro");
        }
        return $sel_all_pro;
    }

    /**
     * 查询媒体
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllMediaName()
    {
        $sel_all_media = \Cache::get("all_media");//查询所有的媒体
        if (empty($sel_all_media)) {
            $sel_media = db("media")->select();
            //将媒体id变为数组标识
            $arr = array();
            foreach ($sel_media as $v) {
                $arr[$v['media_id']] = array(
                    "media_id" => $v['media_id'],
                    "media_ident" => $v['media_ident'],
                    "media_name" => $v['media_name'],
                    "media_butt" => $v['media_butt'],
                    "media_create_time" => $v['media_create_time'],
                );
            }
            \Cache::set("all_media", $arr, 0);//缓存媒体信息
            $sel_all_media = \Cache::get("all_media");//查询所有的媒体
        }
        return $sel_all_media;
    }

    /**
     * 查询模块转为下拉列表数据
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllModelNameOption()
    {
        $all_model = $this->getAllModelName();//获取模块信息
        $htm_option = "";
        foreach ($all_model as $v) {
            $htm_option .= "<option value='{$v['column_id']}'>{$v['column_name']}</option>";
        }
        return $htm_option;
    }

    /**
     * 查询媒体信息转换为下拉列表
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllMediaNameOption()
    {
        $sel_media = $this->getAllMediaName();//查询所有的媒体信息
        $html_option = "";
        foreach ($sel_media as $v) {
            $html_option .= "<option value='{$v['media_ident']}'>{$v['media_name']}</option>";
        }
        return $html_option;
    }

    /**
     * 根据媒体id查询
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllMediaIdOption()
    {
        $sel_media = $this->getAllMediaName();//查询所有的媒体信息
        $html_option = "";
        foreach ($sel_media as $v) {
            $html_option .= "<option value='{$v['media_id']}'>{$v['media_name']}</option>";
        }
        return $html_option;
    }

    //清空缓存
    public function getClearCache()
    {
        $sel_all_cache = \Cache::clear();
        if (!empty($sel_all_cache)) {
            echo "缓存已清空";
        } else {
            echo "未清空";
        }
    }

    /**
     * 根据日期区间返回区间内的每一天
     * @param string $start 开始时间（时间戳）
     * @param string $end 结束时间（时间戳）
     * @return array
     */
    public function dateBetweenDay($start, $end)
    {
        // 计算日期段内有多少天
//        $days = ($start - $end) / 86400 + 1;
        $days = ($start - $end) / 86400;
        // 保存每天日期
        $date = array();
        //获取区间内的每一天
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $end + (86400 * $i));
        }
        return $date;
    }

    public function getContentLog(){
        $aa=Db::name("media")->select();
        print_r($aa);
//        $this->write_log(Db::name("media")->fetchSql(true)->select());
        $this->write_log(Db::name("media")->getLastSql());
    }

}