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
		$this -> islogin();
		$this->course = new \Manage\Model\CourseModel;
		$this->job = new \Manage\Model\JobModel;
	}

	/**
	 * 课程-列表
	 * @author cuiruijun
	 * @date   2018/12/10 下午11:59
	 * @url    manage/job
	 * @return  array
	 */
	public function index() {
		$jobs = $this->course->getList();

		$data['list'] = $jobs;
		$this->assign($data);
		$this->display();
	}

	/**
	 * 编辑课程
	 * @author cuiruijun
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
				//通用课程只能有一个
				$res = $this->course->getOne('type = 1 and is_deleted = 0');
				if($res){
					$this->e('通用课程只能有一个');
				}

				if($result = $this->course->add($data)){
					$this->e();
				}else{
					$this->e('fail');
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