<?php

/**
 * @Dec    课程模型
 * @Auther QiuXiangCheng
 * @Date   2019/01/17
 */
namespace Wechat\Model;
class DetailcourseModel extends CommonModel {

	protected $tableName = 'course_detail';

    public function _initialize() {

        parent::_initialize();
    }


    /**
     * 取得章节列表
     * @Author   邱湘城
     * @DateTime 2019-01-18T01:36:25+0800
     */
    public function getCourseList($where, $fields = '*', $order = 'cd.sort asc') {

        $where['cd.is_deleted'] = 0;
        return $this -> table('course_detail cd') -> field($fields) -> where($where) ->
        join('join course c ON c.id = cd.course_id') -> 
        order($order) -> select();
    }
}