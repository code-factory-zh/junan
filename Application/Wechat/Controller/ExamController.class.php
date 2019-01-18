<?php

/**
 * 小程序考试
 * @Auther cuiruijun
 * @Date 2019/01/08
 */
namespace Wechat\Controller;

class ExamController extends CommonController
{
    private $exam;
    private $member;
    private $question;
    private $course_model;
    private $detail;
    private $examQuestion;
    private $account_id = 1;
    private $company = 1;

    public function _initialize()
    {
        parent::_initialize();
        $this -> ignore_token(0);

        $this -> exam = new \Manage\Model\ExamModel;
        $this -> member = new \Manage\Model\ExamMemberModel;
        $this -> course_model = new \Manage\Model\CourseModel;
        $this -> question = new \Manage\Model\QuestionsModel;
        $this -> detail = new \Wechat\Model\ExamDetailModel;
        $this -> examQuestion = new \Wechat\Model\ExamQuestionModel;

        $this -> account_id = 1;
    }

    /**
     * 前台考试列表
     * */
    public function list()
    {
        $data = ['list' => []];
        $data['list'] = $this -> exam -> getlist(['is_deleted' => 0], 'id, name');

        $this -> e(0, $data['list']);
    }

    /**
     * 前台题目获取
     *
     * @param int $id 课程ID
     * return array
     * */
    public function questions()
    {
//        $this->_get($g, 'course_id');
		$g = I('get.');

//        $this->isInt(['course_id']);
		$account_id = $this->u['id'];

        //是否已学习完成

		//是否已经考过试
        $done = $this->member->findData(['account_id' => $account_id, 'course_id' => $g['course_id']]);

        if (!empty($done['score'])) {
            $this->e('该考试您已填写');
        }

        //判断exam_questions表是否有记录
        $exist = $this->examQuestion->findExamQuestion(['course_id' => $g['course_id'], 'account_id' => $account_id, 'status' => 1]);

        if ($exist) {
        	//查询是否已经过期
			$expired_time = $exist['created_time'] + ($exist['exam_time'] * 60);
			if(time() - $expired_time > 0){
				//没过期,则取exam_question中的记录,下面统一处理

				//如果这时候没有答题,则重新生成一套
				$is_answerd_info = $this->detail->getRecord('id', ['exam_question_id' => $exist['id']]);

				if(!$is_answerd_info){
					//重新生成题库
					$exam_info = $this->exam->getOne('course_id = '. $g['course_id']);
					$radioNum = $exam_info['dx_question_amount'];
					$checkboxNum = $exam_info['fx_question_amount'];
					$judgeNum = $exam_info['pd_question_amount'];

					$questionIds = $this->question->getIds($radioNum, $checkboxNum, $judgeNum, $g['course_id']);

					$data = [
						'exam_id' => $exam_info['id'],
						'account_id' => $account_id,
						'exam_time' => $exam_info['time'],
						'status' => 1,
						'course_id' => $g['course_id'],
						'question_ids' => implode(',', $questionIds),
					];
					if (!$this->examQuestion->add($data))
					{
						$this->e('重新生成题库失败');
					}

				}else{
					//则将分数算出来
					$score = $this->detail->getSumScore(['account_id' => $account_id, 'exam_question_id' => $exist['id']]);

					$data = [
						'score' => $score
					];

					//这时候要插入分数表
					$exam_score_data = [
						'account_id' => $account_id,
						'exam_question_id' => $exist['id'],
						'company_id' => $account_id,
						'course_id' => $g['course_id'],
						'score' => $score,
					];

					$result = $this->member->add($exam_score_data);
					$this->rel($data)->e();
				}
			}
        } else {
            //计算需要得出的考试类型题目数量
			//查询课程对应的exam信息
			$exam_info = $this->exam->getOne('course_id = '. $g['course_id']);

            $radioNum = $exam_info['dx_question_amount'];
            $checkboxNum = $exam_info['fx_question_amount'];
            $judgeNum = $exam_info['pd_question_amount'];

            $questionIds = $this->question->getIds($radioNum, $checkboxNum, $judgeNum, $g['course_id']);

			$data = [
				'exam_id' => $exam_info['id'],
				'account_id' => $account_id,
				'exam_time' => $exam_info['time'],
				'status' => 1,
				'course_id' => $g['course_id'],
				'question_ids' => implode(',', $questionIds),
			];
            if (! $this->examQuestion->add($data)) {

				$this->e('生成题库失败');
            }
        }

        //取最新一条考试题目信息
		$last_exam_questions = $this->examQuestion->findExamQuestion(['course_id' => $g['course_id'], 'account_id' => $account_id, 'status' => 1]);
		$question_ids = explode(',', $last_exam_questions['question_ids']);
		//返回第一题的信息
		$first_question = $this->question->getOne(['id' => $question_ids[0]]);
		unset($first_question['answer']);

		//返回是否做了以及做对还是做错的状态
		$is_answerd_info = $this->detail->getRecord('status', ['exam_question_id' => $last_exam_questions['id'], 'question_id' => $question_ids[0], 'account_id' => $account_id]);

		$return_res = [
			'count' => count($question_ids),
			'first_question_info' => $first_question,
			'is_answer' => $is_answerd_info ? 1 : 0,
			'answer_result' => (int)$is_answerd_info,
			'exam_question_id' => (int)$last_exam_questions['id'],
		];

		$this->rel($return_res)->e();
    }

    /**
     * 前台获取单条题目信息
     *
     * @param int question_id 题目ID
     * @param int exam_question_id 试题ID
     * @param int $id 题目ID
     * return array
     * */
    public function detail()
    {
    	$account_id = $this->u['id'];

//        $this->_get($g, I('get.'));
//        $this->isInt(['question_id']);

		$g = I('get.');

        $question = $this->question-> getQuestion(['id' => $g['question_id']], 'id, type, title, option');
        if (empty($question)) {
            $this->e('题目不存在');
        }

        $question['option'] = json_decode($question['option'], true);
        //查看是否有答题记录
		$is_answerd_info = $this->detail->getRecord('status', ['exam_question_id' => $g['exam_question_id'], 'question_id' => $g['question_id'], 'account_id' => $account_id]);

		$question['is_answer'] = $is_answerd_info ? 1 : 0;
		$question['answer_result'] = (int)$is_answerd_info;

        $this->rel($question)->e();
    }

    /**
     * 前台提交题目答案
     *
     * @pram int $exam_question_id 考试ID
     * @param int $question_id 题目ID
     * @param int|array $answer_id 答案ID,用逗号分隔开
     * return bool
     * */
    public function answer()
    {
//		$account_id = $this->u['id'];
		$account_id = 1;
//        $this->_post($g, ['exam_question_id', 'question_id', 'answer_id']);
//        $this->isInt(['id', 'question_id']);

		$g = I('post.');

        //该考生题库是否存在
        $examQuestion = $this -> examQuestion -> findExamQuestion(['id' => $g['exam_question_id'], 'status' => 1, 'account_id' => $account_id]);
        if (!$examQuestion) {
            $this->e('该套试题已经下架或者删除');
        }else{
        	//查看该套试题中是否有这道题
			if(!in_array($g['question_id'], explode(',', $examQuestion['question_ids']))){
				$this->e('未找到该题目');
			}else{
				$question = $this->question->getQuestion(['id' => $g['question_id']]);
				if (empty($question)) {
					$this -> e('题目不存在哦');
				}
			}
		}

        $exam = $this -> exam -> findExam(['id' => $examQuestion['exam_id'], 'is_deleted' => 0]);
        if (empty($exam)) {
            $this -> e('考试不存在');
        }

        //是否已经过期
		$expired_time = $examQuestion['created_time'] + ($examQuestion['exam_time'] * 60);
		if(time() - $expired_time > 0){
			$this -> e('考试已经结束');
		}

//        //是否已学习完成
//        $done = $this -> member -> findData(['account_id' => $this -> account_id, 'exam_id' => $examQuestion['exam_id'], 'is_deleted' => 0]);
//
//        if (!empty($done['score'])) {
//            $this -> e('该考试您已填写');
//        }

        //是否已答过
		$is_answerd_info = $this->detail->getRecord('id', ['exam_question_id' => $g['exam_question_id'], 'question_id' => $g['question_id'], 'account_id' => $account_id]);
        if ($is_answerd_info) {
            $this -> e('已经回答过这道题了');
        }

        //开始写入数据
        $data['account_id'] = $account_id;
        $data['exam_question_id'] = $g['exam_question_id'];
        $data['question_id'] = $g['question_id'];
        $data['type'] = $question['type'];

		if($question['type'] == 2)
		{
			$answer_id = explode(',' , $g['answer_id']);
			$answer_id = json_encode($answer_id);
		}
		else
		{
			$answer_id = $g['answer_id'];
		}

        $data['answer_id'] = $answer_id;
        $data['status'] = $answer_id == $question['answer'] ? 1 : 0;

        if ($data['status']) {
            $data['score'] = $exam['dx_question_score'];
            if ($question['type'] == 2) $data['score'] = $exam['fx_question_score'];
            if ($question['type'] == 3) $data['score'] = $exam['pd_question_score'];
        }else{
			$data['score'] = 0;
		}

		$result = $this -> detail -> add($data);

//        //判断时间是否在范围内或者是最后一题,如果是，则返回分数
//        $time = $examQuestion['start_time'] + $examQuestion['exam_time'] >= time();
//        $num = $this -> detail -> getFieldByCondition(['account_id' => $this -> account_id, 'exam_questions_id' => $g['id'], 'status' => 1], 'count(1) as num');
//
//        $end = count($questions) == (!empty($num['num'])) ? $num['num'] + 1 : 0;
//
//        if (empty($num['num'])) {
//            $this -> examQuestion -> save(['id' => $g['id'], 'start_time' => time()]);
//
//            $result = $this -> detail -> add($data);
//        } elseif ((!$examQuestion['exam_time'] && $time) || $end) {
//            if ($end) {
//                $this -> detail -> add($data);
//            }
//
//            $score['exam_id'] = $examQuestion['exam_id'];
//            $score['course_id'] = $exam['course_id'];
//            $score['company_id'] = $this -> company;
//            $score['account_id'] = $this -> account_id;
//
//            $total = $this -> detail -> getFieldByCondition(['account_id' => $this -> account_id, 'exam_questions_id' => $g['id'], 'status' => 1], 'sum(score) as total');
//            $score['score'] = (!empty($total['total'])) ? $total['total'] : 0;
//            $result = $this -> member -> add($score);
//            if ($result) {
//                $this -> e(0, $total);
//            }
//        } else {
//            $result = $this -> detail -> add($data);
//        }

        if ($result) {
        	//返回答题的状态
			$data = [
				'answer_result' => $data['status'],
				'answer' => $g['answer_id'],
			];
            $this->rel($data)->e();
        } else {
            $this->e('系统异常');
        }
    }
}