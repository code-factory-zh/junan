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
	const wx_app = 'https://api.weixin.qq.com/sns/jscode2session';

	public function _initialize() {

		parent::_initialize();
		$this -> account = new \Manage\Model\AccountModel;
		$this -> user = new \Wechat\Model\UserModel;
	}

	/**
	 * 根据code取得openid
	 * @Author   邱湘城
	 * @DateTime 2019-01-16T21:40:59+0800
	 */
	public function get_open_id($code) {

		$auth = self::getScreat();
		$url = self::wx_app . "?appid={$auth[0]}&secret={$auth[1]}&js_code={$code}&grant_type=authorization_code";
		return $this -> httpGet($url);
	}

	private static function getScreat() {

		$fi = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/cert/screat');
		return explode("\n", trim($fi));
	}

	/**
	 * 登录功能
	 * @Author   邱湘城
	 * @DateTime 2019-01-15T21:25:40+0800
	 */
	public function dologin() {

		$this -> ignore_token() -> _post($p, ['company_id', 'mobile', 'code']);
		$this -> isint(['company_id', 'mobile']);
		$this -> phoneCheck($p['mobile']);

		$where = ['company_id' => $p['company_id'], 'mobile' => $p['mobile']];
		$user = $this -> user -> getCompanyUserByWhere($where);
		if (!count($user)) {
			$this -> e('登录失败!');
		}

		$rel = $this -> get_open_id($p['code']);
		if (!is_array($rel) || (!isset($rel['openid']) && !isset($rel['session_key']))) {
			$this -> rel([]) -> e($rel['errcode'], '效验获取open_id失败！');
		}

		// 绑定用户OPEN_ID
		if (empty($user['open_id'])) {
			$this -> user -> where($where) -> save(['open_id' => $rel['openid']]);
		}

		if (!empty($user['open_id']) && $user['open_id'] != $rel['openid']) {
			$this -> e('open_id 匹配出错！');
		}
		$user['openid'] = $rel['openid'];

		$token = md5(self::token_salt . $rel['openid'] . $p['company_id']);
		$this -> save_openid_token($token, $user);
		// pr($this -> get_openid_token($token));
		$this -> rel(['token' => $token]) -> e();
	}


	/**
	 * 取用户数据
	 * @Author   邱湘城
	 * @DateTime 2019-01-16T22:11:59+0800
	 */
	public function get_user_inf() {

		$this -> _get($p);
		$rel = $this -> get_openid_token($p['token']);
		if (is_null($rel)) {
			$this -> e('没有数据！');
		}

		unset($rel['openid'], $rel['open_id']);
		$this -> rel($rel) -> e(0, '完成');
	}


	/**
	 * 验证登录状态 根据前端微信小程序open_id查询用户表是否存在
	 * @Author   邱湘城
	 * @DateTime 2019-01-13T14:27:17+0800
	 */
	// public function loginAuth() {

	// 	$this -> ignore_token() -> _get($p);
	// 	if (!isset($p['token'])) {
	// 		$this -> e('请重新登录.');
	// 	}

	// 	$user = $this -> account -> findAccount(['open_id' => $p['token']]);
	// 	if (count($user)) {
	// 		$this -> save_openid_token($p['token'], $user);
	// 	}

	// 	$data = $this -> get_openid_token($p['token']);
	// 	pr($data);
	// 	$this -> e(0);
	// }
}