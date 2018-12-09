<?php

/**
 * 子帐户模块
 * @Auther QiuXiangCheng
 * @Date 2018/12/08
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
class AccountController extends BaseController {


	private $job;
	private $account;

	public function _initialize() {

		parent::_initialize();
		$this -> job = new \Manage\Model\JobModel;
		$this -> account = new \Manage\Model\AccountModel;
	}


	/**
	 * 子帐户列表管理
	 * @DateTime 2018-12-08T17:58:00+0800
	 */
	public function list() {

		$data = [];
		$jobs = $this -> job -> getJobs('id, name');
		$list = $this -> account -> getAccount();

		$rel = [];
		foreach ($list as &$items) {
			if (isset($jobs[$items['job_id']])) {
				$items['job_name'] = $jobs[$items['job_id']];
			}

			if (!isset($rel[$items['account_id']])) {
				$rel[$items['account_id']] = $items;
			} else {
				$rel[$items['account_id']]['course_name'] .= ', ' . $items['course_name'];
			}
		}
// pr($rel);
		$data['list'] = $rel;
		$this -> assign($data);
		$this -> display('account/list');
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

			$job_id = $p['job_id'];
			$p['company_id'] = $this -> company_id;

			unset($p['job_id']);
			if (!($id = $this -> account -> add($p))) {
				$this -> e('增加子帐户失败!');
			}

			// 插入关系数据
			$time = time();
			// $account_job = M('account_job');
			$done = M('account_job') -> table('account_job') -> add([
				'company_id' => $this -> company_id,
				'account_id' => $id,
				'job_id' => $job_id,
				'created_time' => $time,
				'updated_time' => $time,
			]);

			if (!$done) {
				$this -> e('失败!');
			}
			$this -> e();
		}

		$data = [];
		$data['jobs'] = $this -> job -> getJobs('id, name');

		$this -> assign($data);
		$this -> display('account/edit');
	}
}