<?php

/**
 * 子帐户模块
 * @Auther QiuXiangCheng
 * @Date 2018/12/08
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
class AccountController extends BaseController {

	public function _initialize() {

		parent::_initialize();
		$this -> job = new \Manage\Model\JobModel;
	}


	/**
	 * 增加修改子帐号
	 * @DateTime 2018-12-08T17:14:45+0800
	 */
	public function edit() {

		if (IS_POST) {
			$this -> ignore_token() -> _post($p, ['name', 'mobile']);
			$this -> phoneCheck($p['mobile']);
			$this -> isInt(['job_id']);
			unset($p['job_id'], $p['mobile']);
			$this -> job -> table('job') -> add($p);
		}

		$data = [];
		$data['jobs'] = $this -> job -> getJobs('id, name');

		$this -> assign($data);
		$this -> display('account/edit');
	}
}