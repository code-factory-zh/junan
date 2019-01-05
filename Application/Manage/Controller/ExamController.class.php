<?php

/**
 * 试题模块模块
 * @Auther QiuXiangCheng
 * @Date 2018/12/16
 */
namespace Manage\Controller;
use Common\Controller\BaseController;

class ExamController extends BaseController {

	private $exam;
	private $curri;

	public function _initialize() {

		parent::_initialize();
		$this -> islogin();

		$this -> ignore_token(0);
		$this -> exam = new \Manage\Model\ExamModel;
		$this -> course = new \Manage\Model\CourseModel;
		$this -> curri = new \Manage\Model\CurriculumModel;
	}


	/**
	 * 试题列表
	 * @DateTime 2018-12-16T13:02:07+0800
	 */
	public function list() {

		$data = [];
		$list = $this -> exam -> getlist(['is_deleted' => 0]);

		if (count($list)) {
			$course = $this -> curri -> getCurList(['is_deleted' => 0], 'id, name');
			foreach ($list as &$items) {
				$items['course_name'] = '-';
				$items['total_exam_amount'] = $items['pd_question_amount'] + $items['dx_question_amount'] + $items['fx_question_amount'];
				isset($course[$items['course_id']]) && $items['course_name'] = $course[$items['course_id']];
			}
		}
		$data['list'] = $list;
		// pr($list);

		$this -> assign($data);
		$this -> display('Exam/list');
	}


	/**
	 * 新增、修改
	 * @DateTime 2018-12-16T13:40:08+0800
	 */
	public function edit() {

		if (IS_POST) {
			$needle = ['name', 'time', 'pass_score', 'dx_question_amount', 'fx_question_amount', 'pd_question_amount', 'dx_question_score', 'fx_question_score', 'course_id',  'pd_question_score', 'detail'];
			$this -> _post($p, $needle);
			$this -> isInt(['dx_question_amount', 'fx_question_amount', 'pd_question_amount', 'dx_question_score', 'fx_question_score', 'course_id', 'pd_question_score']);

			$score = $p['dx_question_amount'] * $p['dx_question_score'] + $p['fx_question_amount'] * $p['fx_question_score'] + $p['pd_question_amount'] * $p['pd_question_score'];
			if($score != 100)
			{
				$this -> e('总分固定100分');
			}

			$p['score'] = $score;

			$p['created_time'] = $p['updated_time'] = time();
			if (isset($p['id']) && $p['id'] != '') {
				$id = $p['id'];
				unset($p['id']);
				$done = $this -> exam -> where(['id' => $id]) -> save($p);
			} else {
				$done = $this -> exam -> table('exam') -> add($p);
			}

			if (!$done) {
				$this -> e('失败');
			}

			$this -> e();
		}

		$this -> _get($p);
//		$p = I('get.');
		$data = $this -> exam -> where(['id' => $p['id']]) -> find();

		//取所有的课程
		$data['course'] = $this->course->getList();

		$this -> assign($data);
		$this -> display('Exam/edit');
	}


	/**
	 * 考生列表
	 * @DateTime 2018-12-16T13:40:27+0800
	 */
	public function mlist() {

		$this -> _get($g, 'course_id');
		$this -> isInt(['course_id']);

		$data = ['list' => []];
		$where = ['em.is_deleted' => 0, 'em.company_id' => $this -> company_id, 'course_id' => $g['course_id']];
		$data['list'] = $this -> exam -> getMlist($where);

		if (count($data['list'])) {
			foreach ($data['list'] as &$items) {
				$items['created_time'] = date('Y-m-d H:i:s', $items['created_time']);
			}
		}

		$this -> assign($data);
		$this -> display('Exam/mlist');
	}
}