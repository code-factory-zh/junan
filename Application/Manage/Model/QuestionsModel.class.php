<?php

namespace Manage\Model;
use Common\Model\BaseModel;

class QuestionsModel extends BaseModel {

    const STATUS_ABLE = 0;

    public function _initialize() {
        parent::_initialize();
    }

    protected $tableName = 'questions';

    /**
     * 获取多条记录
     * return array
     * */
    public function getAll($select = '*', $where = '', $page = 1, $pageNum = 20, $order = 'id desc')
    {
        $list = $this -> field($select) -> where($where) -> order($order) -> page($page, $pageNum) -> select();
        if (empty($list)) {
            return [];
        } else {
            return $list;
        }
    }

    /**
     * 取得题目信息
     * @DateTime 2019-01-08T18:09:05+0800
     */
    public function getQuestion($where, $select = '*')
    {
        if (!isset($where['is_deleted'])) {
            $where['is_deleted'] = self::STATUS_ABLE;
        }
        return $this -> field($select) -> where($where) -> find();
    }



    /**
     * 计算公用和专业题目数
     *
     * @param int $dx
     * @param int $fx
     * @param int $pd
     * @param int $courseId
     * return array
     * **/
    public function getIds($dx, $fx, $pd, $courseId)
    {
        $count = create_exam_question($dx, $fx, $pd);

        $fxCount = $count['fx'];
        $dxCount = $count['dx'];
        $pdCount = $count['pd'];

        $data['dxMajor'] = $this -> getAll('id', ['course_id' => $courseId, 'is_deleted' => 0, 'type' => 1, 'common' => 2], 1, $dxCount);
        $data['dxCommon'] = $this -> getAll('id', ['course_id' => $courseId, 'is_deleted' => 0, 'type' => 1, 'common' => 1], 1, $dx - $dxCount);

        $data['fxMajor'] = $this -> getAll('id', ['course_id' => $courseId, 'is_deleted' => 0, 'type' => 2, 'common' => 2], 1, $fxCount);
        $data['fxCommon'] = $this -> getAll('id', ['course_id' => $courseId, 'is_deleted' => 0, 'type' => 2, 'common' => 1], 1, $fx - $fxCount);

        $data['pdMajor'] = $this -> getAll('id', ['course_id' => $courseId, 'is_deleted' => 0, 'type' => 3, 'common' => 2], 1, $pdCount);
        $data['pdCommon'] = $this -> getAll('id', ['course_id' => $courseId, 'is_deleted' => 0, 'type' => 3, 'common' => 1], 1, $pd - $pdCount);

        $return = [];
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                foreach ($value as $v) {
                    $return[] = $v['id'];
                }
            }
        }

        return $return;
    }
}