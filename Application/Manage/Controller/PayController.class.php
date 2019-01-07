<?php

/**
 * 子帐户模块
 * @Auther QiuXiangCheng
 * @Date 2018/12/08
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
class PayController extends BaseController {


	public function _initialize() {

		parent::_initialize();
	}


	public function setpay() {

		du(file_put_contents('/data/test.txt', 'data'));
		$this -> e(0);
	}

	public function getpay() {

		$a = file_get_contents('/data/test.txt');
		pr($a);
	}
}