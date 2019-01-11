<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18-10-23
 * Time: 下午4:37
 */

namespace app\admin\controller;


class Lable extends Base
{
    protected $lable;
    public function __construct(){
        $this->lable = db("config")->where("config_key","=","lable")->value("config_value");
    }

    /**
     * 标签展示
     * @return \think\response\View
     */
    public function index()
    {
        return view("index",['list'=>json_decode($this->lable,true)]);
    }

    /**
     * 标签修改
     * @return mixed
     */
    public function editLable()
    {
        $lable = json_decode($this->lable,true);
        $data = input('param.');
        foreach($lable as $key => $value){
            if($value == $data['value']){
                $lable[$key] = $data['tag'];
            }
        }
        $res = $this->update(json_encode($lable,true));
        if($res){
            $result['code'] = 1;
            $result['msg']  = '操作成功!';
        }else{
            $result['code'] = -1;
            $result['msg']  = '操作失败!';
        }
        return $result;
    }

    /**
     * 标签添加
     * @return mixed
     */
    public function addLable(){
        $data = input('param.');
        $lable = json_decode($this->lable,true);

        if(empty($lable) || !isset($lable)){
            $lable = [];
        }
        array_push($lable,$data['tag']);
        $res = $this->update(json_encode($lable,true));
        if($res){
            $result['code'] = 1;
            $result['msg']  = '操作成功!';
        }else{
            $result['code'] = -1;
            $result['msg']  = '操作失败!';
        }
        return $result;
    }

    /**
     * 标签删除
     * @return mixed
     */
    public function delLable(){
        $lable = json_decode($this->lable,true);
        $data = input('param.');
        foreach($lable as $key => $value){
            if($value == $data['value']){
                unset($lable[$key]);
            }
        }
        $list = array_values($lable);

        $res = $this->update(json_encode($list,true));
        if($res){
            $result['code'] = 1;
            $result['msg']  = '操作成功!';
        }else{
            $result['code'] = -1;
            $result['msg']  = '操作失败!';
        }
        return $result;

    }

    /**
     * 操作数据库的更新
     * @param $data
     */
    public function update($data){
        $res = db('config')->where("config_key","=","lable")->update(["config_value"=>$data]);
        if($res){
            return true;
        }else{
            return false;
        }
    }

} 