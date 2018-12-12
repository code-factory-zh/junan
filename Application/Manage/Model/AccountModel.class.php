<?php

/**
 * @Dec    Acount模块
 * @Auther QiuXiangCheng
 * @Date   2018/12/08
 */
namespace Manage\Model;
use Common\Model\BaseModel;

class AccountModel extends BaseModel {

	protected $tableName = 'account';

	public function _initialize() {

		parent::_initialize();
	}

	/**
	 * 取得所有的子帐户
	 * @DateTime 2018-12-08T18:09:05+0800
	 */
	public function getAccount() {

		return $this -> field('a.id account_id, a.name account_name, a.mobile, aj.job_id') ->
		table('account a') ->
		join('LEFT JOIN account_job aj ON a.id = aj.account_id') ->
		select();
	}


	public function getCourses($fields = 'c.id course_id, cac.account_id, c.name course_name') {

		return $this -> field($fields) ->
		table('company_account_course cac') ->
		join('left join course c on cac.course_id = c.id') ->
		where(['c.is_deleted' => 0, 'cac.status' => 0]) ->
		select();
	}
}