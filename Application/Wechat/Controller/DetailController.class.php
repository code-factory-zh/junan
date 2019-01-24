<?php

/**
 * @Dec    章节详情控制器
 * @Auther QiuXiangCheng
 * @Date   2019/01/18
 */

namespace Wechat\Controller;
use Wechat\Model\AdminModel;

class DetailController extends CommonController {

	private $account_course;
	private $host;

	public function _initialize() {

		parent::_initialize();
		$this -> host = 'https://study.joinersafe.com/';
		$this -> account_course = new \Wechat\Model\DetailcourseModel;
	}


	/**
	 * 取得课程章节列表
	 * @Author   邱湘城
	 * @DateTime 2019-01-18T01:39:24+0800
	 */
	public function courseDetail() {

		$this -> _get($p, ['course_id']);

		// 取章节目录
		$list = $this -> account_course -> getCourseList(['cd.course_id' => $p['course_id']], 'cd.id, cd.type, cd.sort num, cd.chapter chapter_name');
		if (!count($list)) {
			$this -> e('没有章节数据！');
		}

		foreach ($list as &$items) {
			$items['num'] = '第' . $items['num'] . '章';
		}


		// 默认取第一条数据
		$fields = ['cd.id', 'cd.course_id', 'c.detail course_detail', 'c.name course_name', 'cd.chapter chapter_name', 'cd.type', 'cd.content'];
		$data = $this -> account_course -> getCourseList(['cd.id' => $list[0]['id']], $fields);
		if (!count($data)) {
			$this -> e('没有章节数据！');
		}

		$this -> rel(['detail' => $data[0], 'list' => $list]) -> e();
	}


	/**
	 * 根据章节ID取数据
	 * @Author   邱湘城
	 * @DateTime 2019-01-18T02:12:01+0800
	 */
	public function detailById() {

		$this -> _get($p, ['id', 'course_id']);

		// 默认取第一条数据
		$fields = ['cd.id', 'cd.type', 'cd.type', 'cd.sort num', 'cd.course_id', 'cd.detail course_detail', 'c.name course_name', 'cd.chapter chapter_name', 'cd.content'];
		$data = $this -> account_course -> getCourseList(['cd.id' => $p['id']], $fields);
		if (!count($data)) {
			$this -> e('没有章节数据！');
		}
		$data = $data[0];

		// 取得上一章和下一章的ID
		$prevNext = $this -> account_course -> getPrevNext($data['id'], $p['course_id']);
		$data['next'] = $prevNext['next'];
		$data['prev'] = $prevNext['prev'];

		$arr = [
			'company_id'  => $this -> u['company_id'],
			'account_id'  => $this -> u['id'],
			'course_id'   => $data['course_id'],
			'chapter_id'  => $p['id'],
			'cource_type' => $data['type'],
			'created_time'=> time(),
			'updated_time'=> time(),
		];

		// 检查是否已学习过 塞数据
		$amount = $this -> account_course -> check($arr);
		if (!$amount) {
			M('company_account_course_chapter') -> add($arr);
		}
		// pr($data);

		// ppt、视频
		if (in_array($data['type'], [2, 3])) {
			$data['content'] = $this -> host . 'Uploads/' . $data['content'];
		}
		$this -> rel($data) -> e();
	}
}