<?php

/**
 * @Dec    登录控制器
 * @Auther yangzhengyuan
 * @Date   2018/12/10
 */

namespace Manage\Controller;

use Manage\Model\CompanyModel;

class CompanyController extends CommonController
{

    private $user;

    // 不需要验证TOKEN
    protected static $token = 0;

    public function _initialize()
    {
        parent::_initialize();
        $this->user = new CompanyModel();
    }

    public function index()
    {
        $this->_post();
    }

    public function search(){
        $post = I('post.');
        $this->ignore_token();
        $companyList = $this->user->searchCompany(['company_name' => $post['company_name']]);
        $this->rel(['company_list' => $companyList])->e();
    }

    /**
     * 用户登录
     * @param md5 ($verify)
     * @param $phone
     * @param $pwd
     */
    public function login()
    {
        if (IS_POST) {
            $post = I('post.');
            $this->ignore_token();
            $user = $this->user->getCompany(['company_name' => $post['company_name']]);
            if (!($u = $this->user->check($post, $user))) {
                $this->e('公司名称或密码不正确');
            }
            $token = $this->token_fetch($u);
            $this -> save_token('token', 1);
            if (!$this->save_token($token, $u)) {
                $this->e('无法生成TOKEN');
            }
            $this->user->login($user); // 记录用户登录情况
            $this->rel(['token' => $token])->e();
        }
        $this->display();
    }

    /**
     * 验证用户是否已登录
     * @param $token
     */
    public function lc()
    {
        $this->ignore_bsid()->_post($p);
        if (!($u = $this->getUserByToken($p['token']))) {
            $this->e('Token Invalid!');
        }
        $this->e(0, 'on-line');
    }

    /*********************** 注册功能 BEGIN *************************/
    // 注册新用户
    // 最后一步
    public function register()
    {
        if (IS_POST) {
            $post = I('post.');
            $this->ignore_token();
            if(!$this->user->registerCheck($post)){
                $this->e('此公司已被注册');
            }
            $this->lenCheck('password', 6, 16);
            if ($post['password'] != $post['verify_password']) {
                $this->e('两次输入的密码不一样');
            }
            if (!preg_match("/^[\w\d\_]+$/si", $post['password'])) {
                $this->e('密码不规范');
            }
            $post['password'] = $this->_encrypt($post['password']);
            // 插入数据库
            if (!($id = $this->user->addUser($post))) {
                $this->e('失败,未知错误');
            }
            $this->e();
        }
        $this->display();
    }
    /*********************** 注册功能 END *************************/
}