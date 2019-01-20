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
		return $this-> alias('m')->field('m.score, m.use_time, m.created_time,c.name')->where(['account_id' => $account_id])->join('course c on m.course_id=c.id', 'left')->select();
	}

	/**
	 * 答对题答错题总数
	 */
	public function getAnswerResultCount($account_id, $where = null){
		$sql = 'select status, count(id) as count from exam_detail where exam_question_id in (select exam_question_id from exam_member where is_pass_exam = 1 and account_id=' . $account_id . ') group by status';

		return $this->query($sql);
	}
}