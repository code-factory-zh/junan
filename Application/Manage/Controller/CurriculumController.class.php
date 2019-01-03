<?php

/**
 * 课程模块
 * @Auther QiuXiangCheng
 * @Date 2018/12/09
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
class CurriculumController extends BaseController {

	private $curriculum;

	public function _initialize() {

		parent::_initialize();
		$this -> islogin();
		$this -> curriculum = new \Manage\Model\CurriculumModel;
	}

	/**
	 * 岗位课程列表
	 * @DateTime 2018-12-11T23:56:15+0800
	 */
	public function list() {

		$data = [];

		$where = "company_id = {$this -> company_id}";
		$data['list'] = $this -> curriculum -> getCourseListByWhere($where, 'c.id, c.name, cac.amount');

		$this -> assign($data);
		$this -> display('curriculum/list');
	}
}