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
		$account_id = 1;

        //是否已学习完成
        $done = $this -> member -> findData(['account_id' => $account_id, 'course_id' => $g['course_id']]);

        if (!empty($done['score'])) {
            $this->e('该考试您已填写');
        }

//        $exam = $this -> exam -> findExam(['course_id' => $g['course_id'], 'is_deleted' => 0, 'account_id' => $account_id]);
//        if (empty($exam)) {
//            $this->e('考试不存在');
//        }

		//判断是否已经提交过答案

        //判断exam_questions表是否有记录
        $exist = $this -> examQuestion -> findExamQuestion(['course_id' => $g['course_id'], 'account_id' => $account_id, 'status' => 1]);

        if (!empty($exist)) {
            $questionIds = json_decode($exist['question_ids']);

            //查询已经打完的题目
            $return = [];
            foreach ($questionIds as $value) {
                $record = $this -> detail -> getField(['account_id' => $this -> account_id, 'exam_questions_id' => $exist['id'], 'question_id' => $value], 'id, type');
                if (empty($record)) {
                    $return[$value] = 0;
                } else {
                    $return[$value] = 1;
                }
            }
            $this->e(200, ['id' => $exist['id'], 'rows' => $return]);
        } else {
            //计算需要得出的考试类型题目数量
			//查询课程对应的exam信息
			$exam_info = $this->exam->getOne('course_id = '. $g['course_id']);

            $radioNum = $exam_info['dx_question_amount'];
            $checkboxNum = $exam_info['fx_question_amount'];
            $judgeNum = $exam_info['pd_question_amount'];

            $questionIds = $this -> question -> getIds($radioNum, $checkboxNum, $judgeNum, $g['course_id']);

			$data = [
				'exam_id' => $exam_info['id'],
				'account_id' => $account_id,
				'exam_time' => $exam_info['time'],
				'status' => 1,
				'course_id' => $g['course_id'],
				'question_ids' => implode(',', $questionIds),
			];
            if ($result = $this ->examQuestion -> add($data)) {

				//返回第一题的信息
				$first_question = $this->question->getOne(['id' => $questionIds[0]]);
				unset($first_question['answer']);

            	$return_res = [
            		'count' => count($questionIds),
            		'first_question_info' => $first_question,
				];

                $this->e(200, $return_res);
            } else {
                $this->el($result, 'fail');
            }
        }
    }

    /**
     * 前台获取单条题目信息
     *
     * @param int $id 题目ID
     * return array
     * */
    public function detail()
    {
        $this->_get($g, 'id');
        $this->isInt(['id']);

        $question = $this -> question -> getQuestion(['id' => $g['id']], 'id, type, title, option');
        if (empty($question)) {
            $this -> e('题目不存在');
        }

        $question['option'] = json_decode($question['option'], true);
        //查看是否有答题记录
        $record = $this -> detail -> findDetail(['account_id' => $this ->account_id, 'question_id' => $g['id']]);
        if (!empty($record)) {
            $question['answer_id'] = $record['answer_id'];
            $question['status'] = $record['status'];
        }

        $this -> e(0, $question);
    }

    /**
     * 前台提交题目答案
     *
     * @pram int $exam_id 考试ID
     * @param int $question_id 题目ID
     * @param int|array $answer_id 答案ID
     * return bool
     * */
    public function submit()
    {
        $this->_post($g, ['id', 'question_id', 'answer_id']);
        $this->isInt(['id', 'question_id']);

        //该考生题库是否存在
        $examQuestion = $this -> examQuestion -> findExamQuestion(['id' => $g['id'], 'status' => 1, 'account_id' => $this -> account_id]);
        if (empty($examQuestion)) {
            $this -> e('非法访问');
        }

        $exam = $this -> exam -> findExam(['id' => $examQuestion['exam_id'], 'is_deleted' => 0]);
        if (empty($exam)) {
            $this -> e('考试不存在');
        }

        //是否已学习完成
        $done = $this -> member -> findData(['account_id' => $this -> account_id, 'exam_id' => $examQuestion['exam_id'], 'is_deleted' => 0]);

        if (!empty($done['score'])) {
            $this -> e('该考试您已填写');
        }

        //是否已答过
        $isAnswer = $this -> detail -> getField(['account_id' => $this -> account_id, 'question_id' => $g['question_id'], 'exam_questions_id' => $g['id']], 'id');
        if (!empty($isAnswer)) {
            $this -> e('请勿重复提交');
        }

        //查看该题是否在课程ID下
        $questions = json_decode($examQuestion['question_ids']);
        if (!in_array($g['question_id'], $questions)) {
            $this -> e('题目不存在');
        }

        $question = $this -> question -> getQuestion(['id' => $g['question_id']]);
        if (empty($question)) {
            $this -> e('题目不存在哦');
        }

        //开始写入数据
        $data['account_id'] = $this -> account_id;
        $data['exam_questions_id'] = $g['id'];
        $data['question_id'] = $g['question_id'];
        $data['type'] = $question['type'];
        $data['answer_id'] = ($question['type'] == 2) ? json_encode($g['answer_id']) : $g['answer_id'];
        if ($question['type'] == 2) {
            $correct = implode(',', json_decode($question['answer'], true));
            $data['status'] = ($correct != $g['answer_id']) ? 0 : 1;
        } else {
            $data['status'] = ($g['answer_id'] != $question['answer']) ? 0 : 1;
        }

        if ($data['status']) {
            $data['score'] = $exam['dx_question_score'];
            if ($question['type'] == 2) $data['score'] = $exam['fx_question_score'];
            if ($question['type'] == 3) $data['score'] = $exam['pd_question_score'];
        }

        //判断时间是否在范围内或者是最后一题,如果是，则返回分数
        $time = $examQuestion['start_time'] + $examQuestion['exam_time'] >= time();
        $num = $this -> detail -> getField(['account_id' => $this -> account_id, 'exam_questions_id' => $g['id'], 'status' => 1], 'count(1) as num');

        $end = count($questions) == (!empty($num['num'])) ? $num['num'] + 1 : 0;

        if (empty($num['num'])) {
            $this -> examQuestion -> save(['id' => $g['id'], 'start_time' => time()]);

            $result = $this -> detail -> add($data);
        } elseif ((!$examQuestion['exam_time'] && $time) || $end) {
            if ($end) {
                $this -> detail -> add($data);
            }

            $score['exam_id'] = $examQuestion['exam_id'];
            $score['course_id'] = $exam['course_id'];
            $score['company_id'] = $this -> company;
            $score['account_id'] = $this -> account_id;

            $total = $this -> detail -> getField(['account_id' => $this -> account_id, 'exam_questions_id' => $g['id'], 'status' => 1], 'sum(score) as total');
            $score['score'] = (!empty($total['total'])) ? $total['total'] : 0;
            $result = $this -> member -> add($score);
            if ($result) {
                $this -> e(0, $total);
            }
        } else {
            $result = $this -> detail -> add($data);
        }

        if ($result) {
            $this -> e();
        } else {
            $this -> e('系统异常');
        }
    }
}