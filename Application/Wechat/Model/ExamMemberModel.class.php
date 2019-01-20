<?php

namespace Wechat\Model;
use Common\Model\BaseModel;

class ExamMemberModel extends BaseModel
{

    public function _initialize()
    {
        parent::_initialize();
    }

    protected $tableName = 'exam_member';


    public function _before_insert(&$data, $options) {
        $data['created_time'] = time();
        $data['updated_time'] = time();
        $data['is_deleted'] = 0;
    }


    /**
     * 根据条件取考试数据
     * @DateTime 2019-01-10T13:17:10+0800
     */
    public function findData($where) {

        return $this -> where($where) -> find();
    }

	/**
	 * 查询成绩列表
	 * @DateTime 2019-01-20T12:47:10+0800
	 */
    public function getUserScoreList($account_id){

    	//连表查询
		return $this-> alias('m')->field('m.score, m.created_time,c.name')->where(['account_id' => $account_id])->join('course c on m.course_id=c.id', 'left')->select();
	}
}