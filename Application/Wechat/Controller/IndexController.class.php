<?php

/**
 * @Dec    主页控制器
 * @Auther QiuXiangCheng
 * @Date   2019/01/13
 */

namespace Wechat\Controller;
use Wechat\Model\AdminModel;

class IndexController extends CommonController {

	public function _initialize() {

		parent::_initialize();
	}

	public function test() {

		$this -> _get($p);
	}
}