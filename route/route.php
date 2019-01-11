<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
Route::post('en', 'index/index/en');
Route::post('de', 'index/index/de');
Route::post('sign', 'index/index/sign');
Route::get('hello/:name', 'index/hello');
//Route::rule('hello/[:txt]', 'index/PageData/addMkdirLogTxt','GET|POST');

Route::post('api/upload','other/image/image64');   //图片上传
Route::post('api/userRoad','user/userapi/userRoad');   //新手上路（静态图）
Route::post('/api/user/pullnew','user/userapi/pullNew');   //拉新活动对接

Route::group('api', [
	'article/comment/zan' => 'article/articleapi/commentZan',
	'article/collect/rm'  => 'article/articleapi/rmCollect',
	'article/item'   => 'article/articleapi/item',
	'article/count'   => 'article/articleapi/count',
	'article/collect'   => 'article/articleapi/collect',
	'article/comment'   => 'article/articleapi/comment',
	'article'   => 'article/articleapi/index',
	'order' => 'article/articleapi/order',   //评论数据

	'screen'  => 'screen/screenapi/screen',  //筛选条件
	'collect/product' => 'goods/goodsapi/collect', //产品收藏
	'column'  => 'column/columnapi/column',  //分类接口
	'browse' => 'goods/goodsapi/browse', //浏览记录
	'product' => 'goods/goodsapi/products', //产品列表

	'notice' => 'notice/noticeapi/notice',   //消息列表
	'delNotice' => 'notice/noticeapi/delNotice', //消息操作
	'noticeInfo' => 'notice/noticeapi/noticeInfo', //消息详情

	'userCollect' => 'user/userapi/userCollect',  // 用户收藏列表
	'userBrowse' => 'user/userapi/userBrowse',    //用户浏览列表
	'userInfo' => 'user/userapi/userInfo',    //用户信息
	'login'  => 'user/userapi/login',    //用户登录
	'register'  => 'user/userapi/register',    //用户注册
	'sendCode'  => 'user/userapi/sendCode',    //发送验证码
	'setPhone' =>  'user/userapi/setPhone',   //更换手机号
	'setPass' =>  'user/userapi/setPass',   //修改密码
	'forgetPass' => 'user/userapi/forgetPass',   //忘记密码
	'setUserInfo' => 'user/userapi/setUserInfo',   //修改用户信息
	'userBanner' => 'user/userapi/userBanner',   //用户中心广告

	'index'   => 'index/index/index',   //首页接口
	'faq' => 'article/articleapi/faq',  //常见问题

	'listTest' => 'screen/screenapi/listTest',  //贷款测一测数据
	'userTest' => 'screen/screenapi/userTest',  //贷款测一测数据添加
	'measuring' => 'goods/goodsapi/measuring',  //贷款测一测数据列表
	'fabulous'  => 'article/articleapi/fabulous',   //用户点赞记录

	'page' => 'pagedata/pageapi/index',  //数据埋点

])->middleware('aes');

return [

];