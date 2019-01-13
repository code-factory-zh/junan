<?php

/**
 * @Dec    主页控制器
 * @Auther QiuXiangCheng
 * @Date   2019/01/13
 */

namespace Wechat\Controller;
use Manage\Model\AccountModel;
class LoginController extends CommonController {

	private $user;
	private $account;

	public function _initialize() {

		parent::_initialize();
		$this -> account = new \Manage\Model\AccountModel;
	}


	/**
	 * 验证登录状态 根据前端微信小程序open_id查询用户表是否存在
	 * @Author   邱湘城
	 * @DateTime 2019-01-13T14:27:17+0800
	 */
	public function loginAuth() {

		$this -> ignore_token() -> _get($p);
		if (!isset($p['token'])) {
			$this -> e('请重新登录.');
		}

		$user = $this -> account -> findAccount(['open_id' => $p['token']]);
		if (count($user)) {
			$this -> save_openid_token($p['token'], $user);
		}

		$data = $this -> get_openid_token($p['token']);
		pr($data);
		$this -> e(0);
	}

	public function t() {

		$this -> _get($p);
		// pr($p);
	}
}