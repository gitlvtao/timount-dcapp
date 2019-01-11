<?php
/**
 * Created by PhpStorm.
 * User: Timount
 * Date: 2018/10/19
 * Time: 17:04
 */

namespace app\index\controller;

use think\Cache;
use think\Controller;

class PageData extends Controller
{

    //测试
    public function getCs(){
        return "123";
    }

    //创建文件夹
    public function addMkdirLogTxt($txt = "")
    {
        if (!empty($txt)) {
            //获取当前日期
            $folder_name = date("Ym");//文件夹名称
            $file_name = date("Ymd");//文件名称
            $dir = iconv("UTF-8", "GBK", "click_volume/" . $folder_name);
            //文件已经存在就不创建
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            //创建日志文件
            //日志文件目录
            $log_mk = "click_volume/" . $folder_name . "/" . $file_name . ".txt";
            if (file_exists($log_mk) && abs(filesize($log_mk)) > 0) {
                $content = "," . $txt . "\r\n";//多条内容添加（，）
            } else {
                $content = $txt . "\r\n";//第一条数据不需要添加（，）
            }
            $myfile = fopen($log_mk, "a") or die("Unable to open file!");
            fwrite($myfile, $content);
            fclose($myfile);
//            echo "操作成功";
            return json_encode(array("code" => "success", "msg" => "操作成功"));
        } else {
            return json_encode(array("code" => "0", "msg" => "数据格式不正确"));
        }
    }

    /**
     * 读取文件
     * @param string $folder_name 文件名称
     * @param string $file_name 文件名称
     * @return string
     */
    public function regLog($folder_name = "", $file_name = "")
    {
        try {
            $handle = fopen("click_volume/" . $folder_name . "/" . $file_name . ".txt", "r");
            $content = '';
            while (!feof($handle)) {
                $content .= fread($handle, 8080);
            }
            fclose($handle);
            return $content;
        } catch (\Exception $e) {
            //捕获文件不存在异常
            echo "";
        }
    }

    //页面读取数据
    public function getRead($date = "")
    {
        if ($this->is_Date($date)) {//日期格式判断
            //获取日期文件夹
            $mr_file_date = date("Ym", strtotime($date));
            $red = $this->regLog($mr_file_date, $date);//获取数据
            if (!empty($red)) {
                return json_encode(array("code" => "success", "response" => $this->returnData($red)));//返回数据
            } else {
                return "没有数据";
            }
        } else {
            return "日期格式不正确";
        }
    }

    //测试
    public function getReadCont()
    {
        echo "111";
    }

    /**
     * 定时任务（点击）
     * @param string $date 日期 20181008
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAdminTank($date = "")
    {
        if (empty($date)) {
            //date为空时查询前一天数据
            $this_ymd = date("Ymd", strtotime("-1 day"));
        } else {
            //查询输入的日期
            $this_ymd = $date;
        }
//        echo $this_ymd;exit();
//        print_r($this_ymd);
//        exit();
//        $sel = $this->curl_post("http://admin.daikuan.bangwoya.com/home/PageData/getAdminTank?date=".$this_ymd, 0);
//        $sel = $this->curl_post("http://open.shop.maimang.bangwoya.com/log?mediaId=4&date=20181008", 0);
//        set_time_limit(0);
//        $sel=file_get_contents("http://192.168.1.123:47000/aaa");
//        $sel=file_get_contents("http://127.0.0.1:8080/dc/index.php/home/pagedata/getreadcont");
        $sel = $this->getRead($this_ymd);//获取文件信息
        $contenet_sel = json_decode($sel, true);
//        echo "<pre>";
//        print_r(json_decode($contenet_sel['response']));
        if ($contenet_sel['code'] == "success") {
            $arr = array();
            foreach (json_decode($contenet_sel['response'], true) as $v) {
                $itype_ct = explode("-", $v['type']);
                if (empty($itype_ct[0])) {
                    $itype_a = "0";
                } else {
                    $itype_a = $itype_ct[0];
                }
                if (empty($itype_ct[1])) {
                    $itype_b = "0";
                } else {
                    $itype_b = $itype_ct[1];
                }
                if (empty($itype_ct[2])) {
                    $itype_c = "0";
                } else {
                    $itype_c = $itype_ct[2];
                }
                if (empty($itype_ct[3])) {
                    $itype_d = "0";
                } else {
                    $itype_d = $itype_ct[3];
                }
                if (empty($itype_ct[4])) {
                    $itype_e = "0";
                } else {
                    $itype_e = $itype_ct[4];
                }
                if ($itype_b != "start") {
                    $arr[] = array(
                        "iip" => $v['ip'],
                        "itype" => $v['type'],
                        "ilink" => $v['link'],
                        "iduration" => $v['duration'],
                        "isource" => $v['source'],
                        "imediaId" => $v['mediaId'],
                        "icreate_time" => intval(($v['create_time'] . ".0") / 1000),
                        "itype_a" => $itype_a,
                        "itype_b" => $itype_b,
                        "itype_c" => $itype_c,
                        "itype_d" => $itype_d,
                        "itype_e" => $itype_e
                    );
                }
            }
            db::name("maimang_data_index")->insertAll($arr);
        }
    }

    /**
     * 查询所有的媒体
     * @return mixed 媒体信息
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllMedia()
    {
        //查询所有媒体
        $all_media = \Cache::get("all_media");
        if (empty($all_media)) {
            $sel_media = db::name("maimang_media")->select();//查询所有的媒体
            if (!empty($sel_media)) {
                \Cache::set("all_media", $sel_media, 0);//缓存查询媒体
                $all_media = \Cache::get("all_media");//将媒体信息赋值
            } else {
                $all_media = "";
            }
        }
        return $all_media;
    }

    //清除所有缓存
    public function getClear()
    {
        $a = \Cache::clear();
        if ($a) {
            echo "缓存已清空";
        } else {
            echo "清空失败";
        }
    }

    /**
     * curl访问接口
     * @param string $url 地址
     * @param string $curlopt_post 提交方式
     * @param string $post_data 参数
     * @return mixed
     */
    protected function curl_post($url, $curlopt_post = "1", $post_data = "")
    {
        header("Content-type:text/html;charset=utf-8");
        header('Content-type: application/json');
        //初始化
        $ch = curl_init();
        //设置抓取的url
        curl_setopt($ch, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($ch, CURLOPT_POST, $curlopt_post);
        if (!empty($post_data)) {
            //post提交数据
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        $result = curl_exec($ch); //执行命令
        curl_close($ch);//关闭URL请求
        return $result;
    }

    //返回数据格式
    public function returnData($data = "")
    {
        return "[" . $data . "]";
    }

    /**
     * 判断日期格式是否正确
     * @param string $str 日期
     * @param string $format 格式
     * @return bool
     */
    function is_Date($str, $format = 'Ymd')
    {
        $unixTime_1 = strtotime($str);//转时间戳
        if (!is_numeric($unixTime_1)) return false; //如果不是数字格式，则直接返回
        $checkDate = date($format, $unixTime_1);//转为ymd格式
        $unixTime_2 = strtotime($checkDate);//转为时间戳
        if ($unixTime_1 == $unixTime_2) {//比较是否一致
            return true;
        } else {
            return false;
        }
    }
}