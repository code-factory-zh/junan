<?php

/**
 * @Dec    登录控制器
 * @Auther yangzhengyuan
 * @Date   2018/12/10
 */

namespace Manage\Controller;

use Manage\Model\AdminModel;

class AdminController extends CommonController
{

    private $user;

    // 不需要验证TOKEN
    protected static $token = 0;

    public function _initialize()
    {
        parent::_initialize();
        $this->user = new AdminModel();
    }

    public function index()
    {
        $this->_post();
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
            $user = $this->user->getAdmin(['account' => $post['account']]);
            if (!($u = $this->user->check($post, $user))) {
                $this->e('用户名或密码不正确');
            }
            $token = $this->token_fetch($u);
            $this -> save_token('token', 1);
            if (!$this->save_token($token, $u)) {
                $this->e('无法生成TOKEN');
            }
            $this->user->login($user); // 记录用户登录情况
            $this->rel(['token' => $token])->e();
        }
        $this->display('admin/login');
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
        $this->ignore_token()->_post($p, ['phone', 'verify_code', 'pwd', 'verify_pwd']);
        $this->baseRegisterCheck($p);
        $this->lenCheck('pwd', 6, 16);
        $this->checkVerifyCodeByPhoneNum($p);
        if ($p['pwd'] != $p['verify_pwd']) {
            $this->e('两次输入的密码不一样');
        }
        if (!preg_match("/^[\w\d\_]+$/si", $p['pwd'])) {
            $this->e('密码不规范');
        }
        self::redisInstance()->delete('PHONE_' . $p['phone']);
        $p['pwd'] = $this->_encrypt($p['pwd']);
        $login = 1; // 默认为用户登录
        // 插入数据库
        if (!($id = $this->user->addUser($p))) {
            $this->e('失败,未知错误');
        }
        // $p['login'] = 1时 注册完成后登录
        if (isset($p['login'])) {
            $this->suiValue('login', [1, 2]);
            $login = $p['login'];
        }
        // 为用户执行登录
        if ($login == 1 && $token = $this->token_fetch($p)) {
            $u = ['id' => $id, 'phone' => $p['phone']];
            if (!$this->save_token($token, $u)) {
                $this->e('用户登录失败，无法生成TOKEN');
            }
            $rel['us_id'] = $id;
            $rel['token'] = $token;
        }
        $this->rel($rel)->e();
    }

    // 注册时的基础检查
    private function baseRegisterCheck($p)
    {

        $this->phoneCheck($p['phone']);
        $this->isInt(['verify_code']);
        $this->lenCheck('verify_code', 4, 4);
        $this->el($this->user->registerCheck($p['phone']), '该手机号已被注册', true);
    }

    // 根据用户手机检查验证码
    private function checkVerifyCodeByPhoneNum($p)
    {

        $verify = self::redisInstance()->get('PHONE_' . $p['phone']);
        if (!$verify || $p['verify_code'] != $verify) {
            $this->e('验证码错误');
        }
    }
    /*********************** 注册功能 END *************************/
}