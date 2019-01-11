<?php
/**
 * 文章控制器.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-11-12
 */

namespace app\article\controller;


use app\common\controller\Api;
use app\article\validate\Article as ArticleValidate;

class Articleapi extends Api {
    /**
     * 获取文章列表
     * @return array
     */
    public function index() {
        $param = request()->param('data');
        $validate = new ArticleValidate();
        if (!$validate->scene('list')->check($param)) {
            $this->response['code'] = 0;
            $this->response['msg'] = $validate->getError();
            return $this->response;
        }
        $queryWhere = [
            ['article_is_single', 'eq', 0],
            ['article_column_id', 'eq', 8],
        ];
        if (isset($param['keyword'])) {
            $queryWhere[] = ['article_title', 'like', '%'.$param['keyword'].'%'];
        }
        if (!isset($param['limit'])) {
            $param['limit'] = 10;
        }
        if (!isset($param['page'])) {
            $param['page'] = 1;
        }
        if (isset($param['attr']) && !empty($param['attr'])) {
            $queryWhere[] = ['article_attr', 'eq', 1];
        }
        $data = db('article')->where($queryWhere)->limit($param['limit'])->page($param['page'])->field(['article_id', 'article_title', 'article_thumb', 'article_brief', 'article_comment_num', 'article_zan_num'])->order("article_sort asc, article_create_time desc")->select();
		$map = [
			'fabulous_user_id' => $param['userId'],
			'fabulous_type'    => 1,
		];
		$collect = db("fabulous_log")->where($map)->column("fabulous_object_id");
		foreach ($data as $key => $value){
			$data[$key]['fabulous'] = 0;
			if (in_array($value['article_id'],$collect)){
				$data[$key]['fabulous'] = 1;
			}
		}
        $this->response['response'] = $data;
        return $this->response;
    }

    /**
     * 获取文章详情
     * @return array
     */
    public function item() {
        $param = request()->param('data');
        // 数据验证
        $validate = new ArticleValidate();
        if (!$validate->scene('item')->check($param)) {
            $this->response['code'] = 0;
            $this->response['msg'] = $validate->getError();
            return $this->response;
        }
        $data = [
            'article' => [],
            'comment' => [],
        ];
        // 获取文章详情
        $article = db('article')->where(['article_id' => $param['articleId']])->find();
        //获取文章点赞
		$article['fabulous'] = 0;
		$map_1 = [
			'fabulous_user_id'   => $param['userId'],
			'fabulous_object_id' => $param['articleId'],
			'fabulous_type'      => 1,
		];
		$article_fabulous = db("fabulous_log")->where($map_1)->find();  //获取该用户点赞数据
		if (!empty($article_fabulous)){
			$article['fabulous'] = 1;
		}
        $data['article'] = $article;
        $data['comment'] = $this->discuss($param['articleId']);
        //评论点赞
		$map = [
			'fabulous_user_id' => $param['userId'],
			'fabulous_type'    => 2,
		];
		$article_fabulous_1 = db('fabulous_log')->where($map)->column("fabulous_object_id");
		if (!empty($data['comment'])){
			foreach ($data['comment'] as $k=>$v){
				$data['comment'][$k]['fabulous'] = 0;
				if (!empty($article_fabulous_1) && in_array($v['comment_id'],$article_fabulous_1)){
					$data['comment'][$k]['fabulous'] = 1;
				}
			}
		}
        // 是否收藏
        $data['article']['is_collect'] = 0;
        if (!empty($param['userId'])) {
            $collectWhere = [
                'collect_object_id' => $article['article_id'],
                'collect_user_id' => $param['userId'],
                'collect_type' => 2,
            ];
            $isCollect = db('collect_log')->where($collectWhere)->count();
            $data['article']['is_collect'] = $isCollect;
        }
		$this->response['response'] = $data;
        return $this->response;
    }

	/**
	 * 评论列表
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function order()
	{
		$param = request()->param('data');
		// 数据验证
		$validate = new ArticleValidate();
		if (!$validate->scene('order')->check($param)) {
			$this->response['code'] = 0;
			$this->response['msg'] = $validate->getError();
			return $this->response;
		}
		$data = $this->discuss($param['articleId'],$param['order']);

		$this->response['response'] = $data;
		return $this->response;
	}

	//用户回复用户的评论列表 （共用方法）
	function discuss($article,$orders=1) {
		$param['articleId'] = $article;

		if ($orders == 2){
			$order = "comment_zan_num desc";
		}else{
			$order = "comment_create_time desc";
		}

		// 获取评论
		if ($param['articleId']) {
			$commentWhere = [
				'comment_article_id' => $param['articleId'],
				'comment_status' => 1,
			];
			$comment = db('article_comment')->alias("A")
				->join("users B","A.comment_user_id = B.user_id",'left')
				->field("A.*,B.user_headpic,B.user_nickname")
				->where($commentWhere)->where("B.user_status",1)->order($order)->select();
		}
		if (is_array($comment)){
			$data = $this->tree($comment);
		}
		$data = array_values($data);
		return $data;
	}

	/**
	 * 无限分类(用户评论回复)
	 * @param $arr
	 * @param int $parent_id
	 * @return array
	 */
	public function tree($arr,$parent_id=0){
		$tree = [];
		foreach ($arr as $key => $value){
			if ($value['comment_pid'] == $parent_id){
				$value['comment_list'] = $this->tree($arr,$value['comment_id']);
				$tree[] = $value;
			}
		}
		return $tree;
	}


    /**
     * 文章计数器
     * @return array
     */
    public function count() {
        $param = request()->param('data');

        // 数据验证
        $validate = new ArticleValidate();
        if (!$validate->scene('count')->check($param)) {
            $this->response['code'] = 0;
            $this->response['msg'] = $validate->getError();
            return $this->response;
        }

        // 对什么字段进行计数
        $inc = '';
        switch ($param['type']) {
            case 'view': $inc = 'article_view_num'; break;
            case 'zan': $inc = 'article_zan_num'; break;
            default: break;
        }

        // 没有计数对象直接返回
        if (empty($inc)) return $this->response;

        $queryWhere = [
            'article_id' => $param['articleId'],
        ];
        db('article')->where($queryWhere)->setInc($inc);

        return $this->response;
    }

    /**
     * 收藏攻略
     * @return array
     */
    public function collect() {
        $param = request()->param('data');

        // 数据验证
        $validate = new ArticleValidate();
        if (!$validate->scene('item')->check($param)) {
            $this->response['code'] = 0;
            $this->response['msg'] = $validate->getError();
            return $this->response;
        }

        $saveData = [
            'collect_user_id' => $param['userId'],
            'collect_object_id' => $param['articleId'],
            'collect_type' => 2,
        ];

        $res = db("collect_log")->where($saveData)->find();
        if ($res){
			$this->response['code'] = 'error';
			$this->response['msg']  = '已收藏';
			return $this->response;exit();
		}
		$saveData['collect_create_time'] = time();
        db('collect_log')->insert($saveData);

        // 增加收藏计数
        db('article')->where(['article_id' => $param['articleId']])->setInc('article_collect_num');

        return $this->response;
    }

    /**
     * 取消收藏
     * @return array
     */
    public function rmCollect() {
        $param = request()->param('data');

        // 数据验证
        $validate = new ArticleValidate();
        if (!$validate->scene('item')->check($param)) {
            $this->response['code'] = 0;
            $this->response['msg'] = $validate->getError();
            return $this->response;
        }

        $queryWhere = [
            'collect_user_id' => $param['userId'],
            'collect_object_id' => $param['articleId'],
            'collect_type' => 2,
        ];
        db('collect_log')->where($queryWhere)->delete();

        // 减少收藏计数
        db('article')->where(['article_id' => $param['articleId']])->setDec('article_collect_num');

        return $this->response;
    }

    /**
     * 回复评论
     * @return array
     */
    public function comment() {
        $param = request()->param('data');

        // 数据验证
        $validate = new ArticleValidate();
        if (!$validate->scene('comment')->check($param)) {
            $this->response['code'] = 0;
            $this->response['msg'] = $validate->getError();
            return $this->response;
        }

        $saveData = [
            'comment_user_id' => $param['userId'],
            'comment_article_id' => $param['articleId'],
            'comment_pid' => $param['commentId'],
            'comment_create_time' => time(),
            'comment_content' => $param['content'],
        ];
        db('article_comment')->insert($saveData);

        // 增加评论计数
        db('article')->where(['article_id' => $param['articleId']])->setInc('article_comment_num');

        return $this->response;
    }

    /**
     * 常见问题
     * @return array
     */
    public function faq(){
        $queryWhere = [
            'article_column_id' => 9,
        ];

        $data = db('article')->where($queryWhere)->field(['article_id', 'article_title', 'article_brief'])->select();
        $this->response['response'] = $data;

        return $this->response;
    }


	/**
	 * 评论点赞
	 * @return array
	 */
	public function commentZan() {
		$param = request()->param('data');

		// 数据验证
		$validate = new ArticleValidate();
		if (!$validate->scene('commentZan')->check($param)) {
			$this->response['code'] = 0;
			$this->response['msg'] = $validate->getError();
			return $this->response;
		}

		// 增加评论计数
		db('article_comment')->where(['comment_id' => $param['commentId']])->setInc('comment_zan_num');

		return $this->response;
	}

	/**
	 * 用户点赞文章|评论
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function fabulous()
	{
		$param = request()->param('data');

		// 数据验证
		$validate = new ArticleValidate();
		if (!$validate->scene('item')->check($param)) {
			$this->response['code'] = 0;
			$this->response['msg'] = $validate->getError();
			return $this->response;
		}

		(empty($param['type1']) || !isset($param['type1']))?1:$param['type1'];
		(empty($param['type2']) || !isset($param['type2']))?1:$param['type2'];

		if ($param['type2'] == 1){
			$saveData = [
				'fabulous_user_id'     => $param['userId'],
				'fabulous_object_id'   => $param['articleId'],
				'fabulous_type'        => $param['type1'],
			];

			$reg = db("fabulous_log")->where($saveData)->find();
			if ($reg){
				$this->response['code'] = 'error';
				$this->response['msg']  = '已点赞!';
				return $this->response;exit();
			}
			$saveData['fabulous_create_time'] = time();
			$res = db("fabulous_log")->insert($saveData);
			if ($res){
				//计数加一
				if ($param['type1'] == 1){
					db('article')->where(['article_id' => $param['articleId']])->setInc('article_zan_num');
				}else{
					// 增加评论计数
					db('article_comment')->where(['comment_id' => $param['articleId']])->setInc('comment_zan_num');
				}
			}
		}else{
			$where = [
				'fabulous_user_id'     => $param['userId'],
				'fabulous_object_id'   => $param['articleId'],
				'fabulous_type'        => $param['type1'],
			];
			$res = db("fabulous_log")->where($where)->delete();
			if ($res){
				//计数减一
				if ($param['type1'] == 1){
					db('article')->where(['article_id' => $param['articleId']])->setDec('article_zan_num');
				}else{
					// 增加评论计数
					db('article_comment')->where(['comment_id' => $param['articleId']])->setDec('comment_zan_num');
				}
			}
		}
		if (!$res){
			$this->response['code'] = 'error';
			$this->response['msg']  = '操作失败';
		}
//		dump($this->response);exit;
		return $this->response;
	}



}