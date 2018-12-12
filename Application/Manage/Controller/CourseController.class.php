<?php

/**
 * 课程基本信息模块
 * @Auther cuiruijun
 * @Date 2018/12/11
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
class CourseController extends BaseController {


	private $course;
	private $job;

	public function _initialize() {

		parent::_initialize();
		$this->course = new \Manage\Model\CourseModel;
		$this->job = new \Manage\Model\JobModel;
	}


	/**
	 * 课程-列表
	 * @DateTime 2018-12-08T17:58:00+0800
	 */
	public function index() {
		$jobs = $this->course->getList();

		$data['list'] = $jobs;
		$this->assign($data);
		$this->display();
	}

	/**
	 * 编辑岗位
	 * @author cuirj
	 * @date   2018/12/10 下午11:59
	 * @url    manage/job/edit
	 * @return  array
	 */
	public function edit(){
		if (IS_POST) {
			$data = ($_POST);
//			$this->_post($data, ['name']);

			if (!$data['name']) {
				$this->el(0, '课程名称不能为空!');
			}

			if(!$data['id']){
				//新增
				if($result = $this->course->add($data)){
					$this->e();
				}else{
					$this->el($result, 'fail');
				}
			}else{
				//修改
				if($result = $this->course->save($data)){
					$this->e();
				}else{
					$this->el($result, 'fail');
				}
			}
		}

		//参数
		if (!empty(I('get.id'))){
			$course_info = $this->course->getOne('id = ' . I('get.id'));
		}

		$data['list'] = $course_info;
		$data['jobs'] = $this->job->getJobs('id, name');
		$this->assign($data);
		$this->display();
	}

}