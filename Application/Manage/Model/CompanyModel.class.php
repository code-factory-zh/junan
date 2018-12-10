<?php

/**
 * @Dec    Manage模块主模型
 * @Auther cuiruijun
 * @Date   2018/12/10
 */
namespace Manage\Model;
use Common\Model\BaseModel;

class CompanyModel extends BaseModel {

	protected $tableName = 'company';

	public function _initialize() {

		parent::_initialize();
	}

	/**
	 * 取得所有公司名称
	 * @DateTime 2018-12-10
	 */
	public function getCompanys($fields, $where = []) {
		return $this -> where($where) -> getField($fields);
	}
}