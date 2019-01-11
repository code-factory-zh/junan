<?php

namespace Manage\Model;
use Common\Model\BaseModel;

class ExamMemberModel extends BaseModel
{

    public function _initialize()
    {
        parent::_initialize();
    }

    protected $tableName = 'exam_member';

    /**
     * 根据条件取考试数据
     * @DateTime 2019-01-10T13:17:10+0800
     */
    public function findData($where) {

        return $this -> where($where) -> find();
    }

}