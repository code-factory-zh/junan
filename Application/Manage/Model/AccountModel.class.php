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
	 * 根据条件查找子帐户
	 * @DateTime 2019-01-05T14:51:46+0800
	 */
	public function findAccount($where) {

		return $this -> where($where) -> find();
	}

	/**
	 * 取得所有的子帐户
	 * @DateTime 2018-12-08T18:09:05+0800
	 */
	public function getAccount($where = []) {

		return $this -> field('a.id account_id, a.name account_name, a.mobile, aj.job_id') ->
		table('account a') -> where($where) ->
		join('LEFT JOIN account_job aj ON a.id = aj.account_id') ->
		select();
	}


	public function getCourse($where = [], $fields = '*') {

		return $this -> table('course') -> where($where) -> getField('id, name');
	}


	/**
	 * 取得子帐户
	 * @DateTime 2019-01-05T16:04:22+0800
	 */
	public function getAccountColumn($where = []) {

		return $this -> where($where) -> getField('mobile, name');
	}


	public function getCourses($fields = 'c.id course_id, cac.account_id, c.name course_name') {

		return $this -> field($fields) ->
		table('company_account_course cac') ->
		join('left join course c on cac.course_id = c.id') ->
		where(['c.is_deleted' => 0, 'cac.status' => 0]) ->
		select();
	}
}