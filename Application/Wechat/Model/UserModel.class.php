<?php

/**
 * @Dec    User模型
 * @Auther QiuXiangCheng
 * @Date   2019/01/13
 */
namespace Wechat\Model;
// use Common\Model\BaseModel;

class UserModel extends CommonModel {

	protected $tableName = 'account';

    public function _initialize() {

        parent::_initialize();
    }


    /**
     * 根据条件查找用户表
     * @Author   邱湘城
     * @DateTime 2019-01-15T21:36:56+0800
     */
    public function getCompanyUserByWhere($where, $fields = '*') {

    	return $this -> where($where) -> find();
    }

    public function getUserJobs($account_id) {

        return $this -> table('account_job') -> where(['account_id' => $account_id]) -> getField('job_id', true);
    }
}