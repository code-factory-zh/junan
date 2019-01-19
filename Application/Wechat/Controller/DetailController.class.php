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
		$this -> host = 'http://admin.joinersafe.com/';
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

		$this -> _get($p, ['id']);

		// 默认取第一条数据
		$fields = ['cd.id', 'cd.type', 'cd.course_id', 'cd.detail course_detail', 'c.name course_name', 'cd.chapter chapter_name', 'cd.content'];
		$data = $this -> account_course -> getCourseList(['cd.id' => $p['id']], $fields);
		if (!count($data)) {
			$this -> e('没有章节数据！');
		}

		// ppt、视频
		if (in_array($data[0]['type'], [2, 3])) {
			$data[0]['content'] = $this -> host . $data[0]['content'];
		}
		$this -> rel($data[0]) -> e();
	}
}