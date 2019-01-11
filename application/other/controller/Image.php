<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/11/23
 * Time: 14:13
 */

namespace app\other\controller;


use think\Controller;
use fuk\Qiniu;

class Image extends Controller
{

// api接口返回格式
	protected $response = [
		'code' => 'success',
		'msg' => '',
		// 'response' => []
	];

	/**
	 * 用户头像上传
	 * @return array
	 */
	public function image64()
	{
		$image64 = input('param.data');
		if (empty($image64)){
			$this->response['code'] = 'error';
			$this->response['msg']  = '参数错误!';
			return $this->response;exit();
		}
		preg_match('/^(data:\s*image\/(\w+);base64,)/', $image64, $result);

		$image_format = end($result); //获取图片类型

		if (!$image_format){
			$this->response['code'] = 'error';
			$this->response['msg']  = '图片格式错误!';
			return $this->response;exit();
		}
		// 允许上传格式
		$allowed_format = ['png', 'jpg', 'gif', 'jpeg'];
		if (!in_array($image_format, $allowed_format)) {
			$this->response['code'] = 'error';
			$this->response['msg']  = '图片格式只允许png、jpg、gif';
			return $this->response;exit();
		}
		// 限制图片大小不能超过1M
		$image_size = mb_strlen($image64);
		if($image_size > 1024 * 1024) {
			$this->response['code'] = 'error';
			$this->response['msg'] = '图片大小超过1M';
			return $this->response;exit();
		}

		$image = base64_decode(str_replace($result[1], '', $image64));  //解码
		$filename = date('YmdHis') . '-' . uniqid() . '.' . $image_format;   //图片名称
		$image_url = 'uploads/'.date('Ymd').'/'.$filename ; //图片地址

		if(!is_dir(dirname('./'.$image_url))){
			mkdir(dirname('./'.$image_url));
			chmod(dirname('./'.$image_url), 0777);
		}
		$res = file_put_contents('./'.$image_url,$image);
		if ($res){
			//成功上传七牛云
			$qiniu = new Qiniu();
			$path = $qiniu->upload(config('tmy.upload_path').date('Ymd').'/'.$filename,$filename);
			// 成功上传后 获取上传信息
			if ($path) {
				$this->response['response'] = $path;
			} else {
				$this->response['response'] = request()->domain().'/uploads/'.$image_url;
			}
		}else{
			$this->response['code'] = 'error';
			$this->response['msg'] = '图片上传失败';
			return $this->response;exit();
		}
//		dump($this->response);exit;
		return json_encode($this->response,true);
	}
}