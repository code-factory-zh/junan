<?php

namespace Manage\Model;
use Common\Model\BaseModel;

class QuestionsModel extends BaseModel {

    public function _initialize() {
        parent::_initialize();
    }

    protected $tableName = 'questions';
    /*protected $fields = array('id', 'course_id', 'type', 'title', 'answer', 'option', 'created_time', 'updateed_time', 'is_deleted');
    protected $pk     = 'id';*/

    //ɾ����¼
    public function del($id) {
        return $this->where('id = ' . $id)->save(['is_deleted' => 1]);
    }

    //��ȡ������¼
    public function getOne($where)
    {
        return $this->where($where)->find();
    }

    //��ȡ������¼
    public function getAll($select = '*', $where = '', $page = 1, $pageNum = 20, $order = 'id desc')
    {
        return $this->field($select)->where($where)->order($order)->page($page, $pageNum)->select();
    }
}