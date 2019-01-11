<?php

namespace Manage\Model;
use Common\Model\BaseModel;

class ExamDetailModel extends BaseModel
{

    public function _initialize()
    {
        parent::_initialize();
    }

    protected $tableName = 'exam_detail_log';

    public function _before_update(&$data, $options)
    {
        $data['updated_time'] = time();
    }

    /**
     * 根据条件获取记录
     *
     * @param string $fields
     * @param array|string $where
     * return array
     */
    public function getRecord($fields, $where) {
        return $this -> where($where) -> getField($fields);
    }

    /**
     * 根据条件取试题数据
     * @DateTime 2019-01-10T13:17:10+0800
     */
    public function findDetail($where) {

        return $this -> where($where) -> find();
    }

    /**
     * 用户各种类型已答完总数及已答问题ID
     * @param int $uid
     * @param int $examId 考试ID
     * @return bool
     */
    public function answerTotal($uid, $examId)
    {
        $data['radio'] = $this -> getRecord('question_id', ['uid' => $uid, 'exam_id' => $examId, 'type' => 1]);
        $data['checkbox'] = $this -> getRecord('question_id', ['uid' => $uid, 'exam_id' => $examId, 'type' => 2]);
        $data['judge'] = $this -> getRecord('question_id', ['uid' => $uid, 'exam_id' => $examId, 'type' => 3]);

        $data['radioTotal'] = count($data['radio']);
        $data['checkboxTotal'] = count($data['checkbox']);
        $data['judgeTotal'] = count($data['judge']);

        return $data;
    }
}