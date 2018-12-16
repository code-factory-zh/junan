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
		$this -> exam = new \Manage\Model\ExamModel;
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
		$this -> display('exam/list');
	}


	/**
	 * 新增、修改
	 * @DateTime 2018-12-16T13:40:08+0800
	 */
	public function edit() {}


	/**
	 * 考生列表
	 * @DateTime 2018-12-16T13:40:27+0800
	 */
	public function mlist() {}
}