<?php

/**
 * 子帐户模块
 * @Auther cuiruijun
 * @Date 2019/01/08
 */
namespace Manage\Controller;
use Common\Controller\BaseController;

class FrontController extends BaseController
{
    private $exam;
    private $member;
    private $question;
    private $detail;
    private $uid = 1;

    public function _initialize()
    {
        parent::_initialize();
        $this -> ignore_token(0);

        $this -> exam = new \Manage\Model\ExamModel;
        $this -> member = new \Manage\Model\ExamMemberModel;
        $this -> question = new \Manage\Model\QuestionsModel;
        $this -> detail = new \Manage\Model\ExamDetailModel;

        $this -> uid = 1;
    }

    /**
     * 前台考试列表
     * */
    public function list()
    {
        $data = ['list' => []];
        $data['list'] = $this -> exam -> getlist(['is_deleted' => 0], 'id, name');

        $this -> e($data['list']);
    }

    /**
     * 前台题目获取
     *
     * @param int $id 考试ID
     * return array
     * */
    public function question()
    {
        $this->_get($g, 'id');
        $this->isInt(['id']);

        //是否已学习完成
        $done = $this -> member -> findData(['account_id' => $this -> uid]);

        if (!empty($done['score'])) {
            $this->e('该考试您已填写');
        }

        $exam = $this -> exam -> findExam(['id' => $g['id'], 'is_deleted' => 0]);
        if (empty($exam)) {
            $this->e('考试不存在');
        }

        $radioNum = $exam['dx_question_amount'] / $exam['dx_question_score'];
        $checkboxNum = $exam['fx_question_amount'] / $exam['fx_question_score'];
        $judgeNum = $exam['pd_question_amount'] / $exam['pd_question_score'];

        $radioWhere = ['course_id' => $exam['course_id'], 'is_deleted' => 0, 'type' => 1];
        $checkboxWhere = ['course_id' => $exam['course_id'], 'is_deleted' => 0, 'type' => 2];
        $judgeWhere = ['course_id' => $exam['course_id'], 'is_deleted' => 0, 'type' => 3];

        $done = [];
        if (!empty($done['score'])) {
            //没有分数，查看记录表，随机生成题目
            $detail = $this -> detail -> answerTotal($this -> uid, $g['id']);

            $radioNum = $radioNum - $detail['radioTotal'];
            $checkboxNum = $checkboxNum - $detail['checkboxTotal'];
            $judgeNum = $judgeNum - $detail['judgeTotal'];

            if (!empty($detail['radio'])) {
                $radioWhere = array_merge($radioWhere, ['id' => ['not in' => $detail['radio']]]);
            }
            if (!empty($detail['checkbox'])) {
                $checkboxWhere = array_merge($radioWhere, ['id' => ['not in' => $detail['checkbox']]]);
            }
            if (!empty($detail['judge'])) {
                $judgeWhere = array_merge($radioWhere, ['id' => ['not in' => $detail['judge']]]);
            }

            $done['radio'] = $this -> question -> getAll('id', ['id' => ['between', $detail['radio']]]);
            $done['checkbox'] = $this -> question -> getAll('id', ['id' => ['between', $detail['checkbox']]]);
            $done['judge'] = $this -> question -> getAll('id', ['id' => ['between', $detail['judge']]]);
        }

        $data['radio'] = $this -> question -> getAll('id', $radioWhere, 1, $radioNum);
        $data['checkbox'] = $this -> question -> getAll('id', $checkboxWhere, 1, $checkboxNum);
        $data['judge'] = $this -> question -> getAll('id', $judgeWhere, 1, $judgeNum);

        $this -> e(['done' => $done, 'data' => $data]);
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
        $record = $this -> detail -> findDetail(['uid' => $this ->uid, 'question_id' => $g['id']]);
        if (!empty($record)) {
            $question['answer_id'] = $record['answer_id'];
            $question['status'] = $record['status'];
        }

        $this -> e($question);
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
        $this->_post($g, ['exam_id', 'question_id', 'answer_id']);
        $this->isInt(['exam_id', 'question_id']);

        //是否已学习完成
        $done = $this -> member -> findData(['account_id' => $this -> uid]);

        if (!empty($done['score'])) {
            $this -> e('该考试您已填写');
        }

        //是否已答过
        $isAnswer = $this -> detail -> getRecord('id', ['uid' => $this -> uid, 'question_id' => $g['question_id'], 'exam_id' => $g['exam_id']]);
        if (!empty($isAnswer)) {
            $this -> e('请勿重复提交');
        }

        //查看该题是否在课程ID下
        $question = $this -> question -> getQuestion(['id' => $g['question_id']]);
        if (empty($question)) {
            $this -> e('题目不存在');
        }

        $exam = $this -> exam -> findExam(['id' => $g['exam_id'], 'is_deleted' => 0]);
        if (empty($exam) || $exam['course_id'] != $question['course_id']) {
            $this -> e('考试不存在或题目不在该考试下');
        }

        //开始写入数据


    }
}