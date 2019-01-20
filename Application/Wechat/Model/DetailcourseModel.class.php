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

    /**
     * 将某个章节置为已学习
     * @Author   邱湘城
     * @DateTime 2019-01-20T15:27:31+0800
     */
    public function check($data) {

        $where = ['account_id' => $data['account_id'], 'course_id' => $data['course_id'], 'chapter_id' => $data['chapter_id']];
        return $this -> table('company_account_course_chapter') -> where($where) -> count();
    }
}