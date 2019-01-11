<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-14
 */

namespace app\article\validate;


use think\Validate;

class Article extends Validate {
    // 验证规则
    protected $rule = [
        'page'  => 'number',
        'limit'   => 'number',
        'keyword' => 'chsAlphaNum',
        'attr' => ['in' => '0,1'],
        'userId' => 'number',
        'articleId' => 'require|number',
        'type' => ['require', 'in' => 'view,zan'],
        'commentId' => 'require|number',
        'content' => 'require',
		'order' => 'require|number',
    ];

    // 验证信息
    protected $message = [
        'page.number' => '分页格式错误',
        'limit.number' => '偏移量格式错误',
        'keyword.chsAlphaNum' => '关键词格式错误',
        'attr.number' => '属性参数错误',
        'userId.number' => '用户ID格式错误',
        'articleId.require' => '文章ID为空',
        'articleId.number' => '文章ID格式错误',
        'type.require' => '计数类型为空',
        'type.in' => '计数类型不合法',
        'commentId.require' => '评论ID为空',
        'commentId.number' => '评论ID不合法',
        'content.require' => '评论内容为空',
		'order.require' => '筛选类型为空',
		'order.number' => '筛选类型不合法',
    ];

    // 验证场景
    protected $scene = [
        'list' => ['page', 'limit', 'keyword', 'attr'],
        'item' => ['userId', 'articleId'],
        'count' => ['articleId', 'type'],
        'comment' => ['userId', 'articleId', 'commentId', 'content'],
        'commentZan' => ['commentId'],
		'order' =>['articleId','order'],
    ];
}