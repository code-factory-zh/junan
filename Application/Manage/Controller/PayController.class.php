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

		$str = 1;
		$tmp = file_get_contents('/webser/log/test.txt');
		if ($tmp) {
			$str = intval($tmp) + 1;
		}
		if(file_put_contents('/webser/log/test.txt', $str)) {
			$this -> e(0, $str);
		}
		$this -> e('插入失败');
	}

	public function getpay() {

		pr(file_get_contents('/webser/log/test.txt'));
	}
}