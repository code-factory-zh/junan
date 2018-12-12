<?php

/**
 * 岗位模块
 * @Auther cuiruijun
 * @Date 2018/12/11
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
class JobController extends BaseController {


	private $job;

	public function _initialize() {

		parent::_initialize();
		$this->job = new \Manage\Model\JobModel;
	}


	/**
	 * 岗位-列表
	 * @DateTime 2018-12-08T17:58:00+0800
	 */
	public function index() {
		$jobs = $this->job->getJobs('id,name,created_time,is_deleted');

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
			$data = I('post.');
//			$this->_post($data, ['name']);

			if (!$data['name']) {
				$this->el(0, '岗位名称不能为空!');
			}

			if(!$data['id']){
				//新增
				if($result = $this->job->add($data)){
					$this->e();
				}else{
					$this->el($result, 'fail');
				}
			}else{
				//修改
				if($result = $this->job->save($data)){
					$this->e();
				}else{
					$this->el($result, 'fail');
				}
			}
		}

		//参数
		if (!empty(I('get.id'))){
			$jobs = $this->job->getOne('id = ' . I('get.id'));
		}

		$data['list'] = $jobs;
		$this->assign($data);
		$this->display();
	}

	/**
	 * 删除岗位
	 * @author cuiruijun
	 * @date   2018/12/10 下午11:59
	 * @url    manage/job/del
	 * @return  array
	 */
	public function del()
	{
		if (!empty(I('post.id'))){
			$result = $this->job->del('id = ' . I('post.id'));
			if($result){
				$this->e();
			}else{
				$this->el($result, '删除失败');
			}
		}
	}

}