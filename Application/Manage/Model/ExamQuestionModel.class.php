<?php

namespace Manage\Model;
use Common\Model\BaseModel;

class ExamQuestionModel extends BaseModel
{

    public function _initialize()
    {
        parent::_initialize();
    }

    protected $tableName = 'exam_questions';

    public function _before_insert(&$data, $options)
    {
        $data['created_time'] = time();
        $data['status'] = 1;
    }

    /**
     * 根据条件取考试题目数据
     * @DateTime 2019-01-10T13:17:10+0800
     */
    public function findExamQuestion($where) {

        return $this -> where($where) -> find();
    }

}
