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
	 * @author cuiruijun
	 * @date   2018/12/10 下午11:59
	 * @url    manage/company
	 * @method get
	 *
	 * @return  array
	 */
	public function index() {
		$companys = $this -> company -> getCompanys('id,code,company_name,created_time,status');

		$data['list'] = $companys;
		$this -> assign($data);
		$this -> display();
	}

	/**
	 * 开启/禁止公司账号
	 * @author cuiruijun
	 * @date   2018/12/10 下午11:59
	 * @url    manage/company/changeStatus
	 * @method post
	 *
	 * @param  int status 1-启用,0-禁止
	 * @return  array
	 */
	public function changeStatus(){
		//判断当前传的参数和数据库中是否相同,如果相同则报错
		$where['id'] = I('post.id');
		$data['status'] = I('post.status');
		$result = $this->company->updateData($where, $data);
		if($result){
			$this->e(0, '修改成功');
		}else{
			$this->el($result, '修改失败,请重试');
		}
	}

}