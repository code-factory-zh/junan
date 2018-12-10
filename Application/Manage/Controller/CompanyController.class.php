<?php

/**
 * 子帐户模块
 * @Auther cuiruijun
 * @Date 2018/12/10
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
class CompanyController extends BaseController {


	private $company;

	public function _initialize() {

		parent::_initialize();
		$this -> company = new \Manage\Model\CompanyModel;
	}


	/**
	 * 接入公司管理-列表
	 * @DateTime 2018-12-08T17:58:00+0800
	 */
	public function list() {
		$companys = $this -> company -> getCompanys('id,code,company_name,created_time,status');

		$data['list'] = $companys;
		$this -> assign($data);
		$this -> display('company/list');
	}

	/**
	 * 开启/禁止公司账号
	 * @author cuirj
	 * @date   2018/12/10 下午11:59
	 * @url    manage/company/changeStatus
	 * @method post
	 *
	 * @param  int status 1-启用,0-禁止
	 * @return  array
	 */
	public function changeStatus(){

	}

}