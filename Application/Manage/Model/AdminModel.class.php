<?php

/**
 * @Dec    Acount模块
 * @Auther QiuXiangCheng
 * @Date   2018/12/08
 */

namespace Manage\Model;

use Common\Model\BaseModel;

class AdminModel extends BaseModel
{

    protected $tableName = 'admin';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 取得账号信息
     * @DateTime 2018-12-08T18:09:05+0800
     */
    public function getAdmin($where)
    {
        return $this->table('admin')->where($where)->find();
    }

    public function check($data, $args)
    {
        if ($data['account'] != $args['account'] or $data['password'] != $args['password']) {
            return false;
        }
        return true;
    }

    public function login($user)
    {
        session('userinfo', json_encode($user));
        return session('userinfo');
    }
}