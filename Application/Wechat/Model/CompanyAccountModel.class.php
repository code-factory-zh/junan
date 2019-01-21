<?php

/**
 * @Dec    课程模型
 * @Auther cuiruijun
 * @Date   2019/01/21
 */
namespace Wechat\Model;
class CompanyAccountModel extends CommonModel {

	protected $tableName = 'company_account_course';

    public function _initialize() {

        parent::_initialize();
    }

    public function _before_insert(&$data, $options){
		$data['created_time'] = time();
		$data['updated_time'] = time();
	}

	public function _before_update(&$data, $options)
	{
		$data['updated_time'] = time();
	}
}