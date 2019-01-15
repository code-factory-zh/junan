<?php

/**
 * @Dec    主页控制器
 * @Auther QiuXiangCheng
 * @Date   2019/01/13
 */

namespace Wechat\Controller;
use Wechat\Model\AdminModel;

class IndexController extends CommonController {

	private $company;
	private $user;

	public function _initialize() {

		parent::_initialize();
		$this -> company = new \Wechat\Model\CompanyModel;
		$this -> user = new \Wechat\Model\UserModel;
	}


	/**
	 * 取得企业数据
	 * @Author   邱湘城
	 * @DateTime 2019-01-15T23:26:23+0800
	 */
	public function get_companys() {

		$list = $this -> company -> getList(['status' => 0], ['id', 'company_name']);
		$this -> rel($list) -> e();
	}


	/**
	 * 主页获取课程列表
	 * @Author   邱湘城
	 * @DateTime 2019-01-15T23:40:18+0800
	 */
	public function course_list() {

		$this -> _get($p);

		// $jobs = $this -> user -> getUserJobs($this -> u['id']);
		// if (!count($jobs)) {
		// 	$this -> e('您没有课程!');
		// }

		$list = [
			'banner' => 'http://5b0988e595225.cdn.sohucs.com/images/20171018/828c39a02b7d4aee9b579c1df3ceb30c.jpeg',
			'list' => [
				[
					'id' 			=> 1,
					'icon' 			=> '',
					'url' 			=> '',
					'name' 			=> '安全生产意识',
					'total_chapter' => 10,
					'studied' 		=> 3,
					'finished' 		=> 0,
					'btn' 			=> '去考试',
				],
				[
					'id' 			=> 2,
					'icon' 			=> '',
					'url' 			=> '',
					'name' 			=> '安全生产意识',
					'total_chapter' => 10,
					'studied' 		=> 2,
					'finished' 		=> 0,
					'btn' 			=> '去考试',
				],
				[
					'id' 			=> 3,
					'icon' 			=> '',
					'url' 			=> '',
					'name' 			=> '安全生产意识',
					'total_chapter' => 10,
					'studied' 		=> 10,
					'finished' 		=> 1,
					'btn' 			=> '已完成',
				],
			],
		];

		$this -> rel(['list' => $list]) -> e();
	}
}