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
        return $this->where('is_deleted = 0')->getField('id, name');
    }
}