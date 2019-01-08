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

		$data = ['created_time' => 1, 'updated_time' => 1];
		M('pay') -> add($data);
	}
}