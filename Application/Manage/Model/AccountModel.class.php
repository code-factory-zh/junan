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

		return $this -> field('a.name account_name, cac.account_id, a.mobile, c.name course_name, aj.job_id') ->
		table('company_account_course cac') ->
		join('LEFT JOIN account_job aj ON aj.account_id = cac.account_id') ->
		join('LEFT JOIN course c ON c.id = cac.course_id') ->
		join('LEFT JOIN account a ON a.id = cac.account_id') ->
		select();
	}
}