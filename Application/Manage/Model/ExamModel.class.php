<?php

/**
 * @Dec    试题模块
 * @Auther QiuXiangCheng
 * @Date   2018/12/16
 */
namespace Manage\Model;
use Common\Model\BaseModel;

class ExamModel extends BaseModel {

	protected $tableName = 'exam';

	public function _initialize() {

		parent::_initialize();
	}

	/**
	 * 根据条件取试题数据
	 * @DateTime 2018-12-16T13:17:10+0800
	 */
	public function getlist($where = '', $fields = '*', $limit = '0, 10', $order = 'id desc') {

		return $this -> field($fields) -> where($where) -> select();
	}
}