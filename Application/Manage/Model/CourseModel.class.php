<?php

namespace Manage\Model;
use Common\Model\BaseModel;

class CourseModel extends BaseModel {

    public function _initialize() {
        parent::_initialize();
    }

    protected $tableName = 'course';

    public function getList()
    {
        return $this->where('is_deleted = 0')->getField('id, name, job_id, amount, detail, created_time');
    }

	public function _before_insert(&$data, $options)
	{
		$data['created_time'] = time();
		$data['updated_time'] = time();
	}

	public function _before_update(&$data, $options)
	{
		$data['updated_time'] = time();
	}

	public function getCourseAmount($where = []) {

		return $this -> where($where) -> getField('amount');
	}
}