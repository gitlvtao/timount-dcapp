<?php
/**
 * Created by PhpStorm.
 * User: fukunari<fukunari@163.com>
 * Date: 2018-10-09
 */

namespace app\admin\controller;


use think\Controller;

class Base extends Controller {
    // 后台json返回统一格式
    public $response = [
        'code' => 0,
        'count' => 0,
        'data' => '',
        'msg' => ''
    ];

    public function initialize() {
        if(session('?admin_user')) {
            $user = json_decode(session('admin_user'), true);
            $menu = $this->getRole($user);
            $this->assign('menu', $menu);
            $this->assign('user', $user);
        } else {
            $this->redirect(url('admin/login/index'));
        }
    }

    public function getRole($user) {
        $role = explode(',', $user['user_group']);
        // 管理员不需要验证
        if (in_array(1, $role)) {
            $menu = $this->setMenu(false);
        } else {
            $auth = db('admin_role')->where(['role_id' => $role])->field('role_auth')->select();
            $roleAuth = [];
            foreach ($auth as $key => $item) {
                $roleArr = explode(',', $item['role_auth']);
                foreach ($roleArr as $roleItem) {
                    $roleAuth[] = $roleItem;
                }
            }
            $menu = $this->setMenu(array_unique($roleAuth));
        }

        return $menu;
    }

    public function setMenu($auth){
        $queryWhere = [
            'auth_status' => 1,
        ];
        if ($auth) {
            $queryWhere['auth_id'] = $auth;
            $queryWhere['auth_menu_status'] = 1; // 如果不是管理员就需要处理一下菜单显示的问题
        }
        $auth = db('admin_auth')->where($queryWhere)->order('auth_sort')->select();
        $authData = [];
        foreach ($auth as $key => $item) {
            if ($item['auth_pid'] == 0) {
                $authData[$item['auth_id']] = $item;
                foreach ($auth as $sonKey => $sonItem) {
                    if ($sonItem['auth_pid'] == $item['auth_id']) {
                        $authData[$item['auth_id']]['child'][] = $sonItem;
                    }
                }
            }
        }
        return $authData;
    }

	/**
	 * 更新模块数据缓存
	 */
	public function updateBlockCache() {
		cache('hgqb_column_index', NULL);
		cache('hgqb_product_index', NULL);
	}

}