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
     * @param int $common_course_id 通用课程id
     * return array
     * **/
    public function getIds($dx, $fx, $pd, $courseId, $common_course_id)
    {
        $count = create_exam_question($dx, $fx, $pd);

        $fxCount = (int)$count['fx'];
        $dxCount = (int)$count['dx'];
        $pdCount = (int)$count['pd'];

		$dxMajor = $this -> getList(['course_id' => $courseId, 'is_deleted' => 0, 'type' => 1], 'id');
		$data['dxMajor'] = array_rand_value(array_column($dxMajor, 'id'), $dxCount);

		$dxCommon = $this -> getList(['course_id' => $common_course_id, 'is_deleted' => 0, 'type' => 1], 'id');
		$data['dxCommon'] = array_rand_value(array_column($dxCommon, 'id'), ($dx - $dxCount));

		$fxMajor = $this -> getList(['course_id' => $courseId, 'is_deleted' => 0, 'type' => 2], 'id');
		$data['fxMajor'] = array_rand_value(array_column($fxMajor, 'id'), $fxCount);

		$fxCommon = $this -> getList(['course_id' => $common_course_id, 'is_deleted' => 0, 'type' => 2], 'id');
		$data['fxCommon'] = array_rand_value(array_column($fxCommon, 'id'), ($fx - $fxCount));

		$pdMajor = $this -> getList(['course_id' => $courseId, 'is_deleted' => 0, 'type' => 3], 'id');
		$data['pdMajor'] = array_rand_value(array_column($pdMajor, 'id'), $pdCount);

		$pdCommon = $this -> getList(['course_id' => $common_course_id, 'is_deleted' => 0, 'type' => 3], 'id');
		$data['pdCommon'] = array_rand_value(array_column($pdCommon, 'id'), ($pd - $pdCount));

        $return = [];
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                foreach ($value as $v) {
                    $return[] = $v;
                }
            }
        }

        return $return;
    }

	/**
	 * 根据条件查找用户表
	 * @Author   邱湘城
	 * @DateTime 2019-01-15T21:36:56+0800
	 */
	public function getList($where, $fields = '*') {

		return $this -> where($where) -> field($fields) -> select();
	}
}