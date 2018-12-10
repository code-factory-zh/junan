<?php

/**
 * @Dec    Manage模块主模型
 * @Auther QiuXiangCheng
 * @Date   2018/12/08
 */
namespace Manage\Model;
use Common\Model\BaseModel;

class JobModel extends BaseModel {

	protected $tableName = 'job';

	public function _initialize() {

		parent::_initialize();
	}

	/**
	 * 取得所有岗位
	 * @DateTime 2018-12-08T17:27:08+0800
	 */
	public function getJobs($fields, $where = []) {

		$where['is_deleted'] = 0;
		return $this -> where($where) -> getField($fields);
	}

}