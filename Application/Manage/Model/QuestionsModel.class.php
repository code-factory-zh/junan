<?php

namespace Manage\Model;
use Common\Model\BaseModel;

class QuestionsModel extends BaseModel {

    public function _initialize() {
        parent::_initialize();
    }

    protected $tableName = 'questions';
    
    //删除记录
    public function del($id) {
        return $this->where('id = ' . $id)->save(['is_deleted' => 1]);
    }

    //获取多条记录
    public function getAll($select = '*', $where = '', $page = 1, $pageNum = 20, $order = 'id desc')
    {
        return $this->field($select)->where($where)->order($order)->page($page, $pageNum)->select();
    }
}