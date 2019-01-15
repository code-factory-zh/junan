<?php

/**
 * @Dec    主页控制器
 * @Auther QiuXiangCheng
 * @Date   2019/01/13
 */

namespace Wechat\Controller;
use Manage\Model\AccountModel;
use Wechat\Model\UserModel;
class LoginController extends CommonController {

	private $user;
	private $account;

	const token_salt = 'junan.com:';

	public function _initialize() {

		parent::_initialize();
		$this -> account = new \Manage\Model\AccountModel;
		$this -> user = new \Wechat\Model\UserModel;
	}


	/**
	 * 登录功能
	 * @Author   邱湘城
	 * @DateTime 2019-01-15T21:25:40+0800
	 */
	public function dologin() {

		$this -> ignore_token() -> _post($p, ['company_id', 'mobile', 'open_id']);
		$this -> isint(['company_id', 'mobile']);
		$this -> phoneCheck($p['mobile']);

		$where = ['company_id' => $p['company_id'], 'mobile' => $p['mobile']];
		$user = $this -> user -> getCompanyUserByWhere($where);
		if (!count($user)) {
			$this -> e('登录失败!');
		}

		if (isset($user['open_id'])) { // 绑定用户OPEN_ID
			$this -> user -> where($where) -> save(['open_id' => $p['open_id']]);
		}

		$token = md5(self::token_salt . $p['open_id'] . $p['company_id']);
		$this -> save_openid_token($token, $user);
		// pr($this -> get_openid_token($token));
		$this -> rel(['token' => $token]) -> e();
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