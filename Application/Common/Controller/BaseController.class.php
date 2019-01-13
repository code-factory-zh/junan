<?php

	/**
	 * @Dec    控制器总基类
	 * @Auther QiuXiangCheng
	 * @Date   2017/12/12
	 */
	namespace Common\Controller;
	use Think\Controller;
	header('Access-Control-Allow-Origin:*');
	header('content-type:application:json;charset=utf8');
	header('Access-Control-Allow-Headers:x-requested-with,content-type');
	class BaseController extends Controller {

		/**
		 * REDIS实例
		 */
		private static $redis = false;

		// 返回的数据
		protected $rel = [];

		// 当前登录者相关信息
		protected $u;

		// module/ctrl/action
		protected $mca;

		// 无论是POST还是GET请求 都取得它当前传入的TOKEN
		protected $token_str;

		// 默认输出rel字段
		protected $output = 0;

		/**
		 * 是否经过TOKEN认证的标识
		 * 通过反射获取它当前真实的值
		 * 如果它的值为0 则不认证TOKEN
		 * 它与use_token的作用一样 但当前的是针对一个控制器的认证
		 */
		protected static $token = 1;

		// 默认使用TOKEN才被视为合法的请求
		// 当为0时不验证token
		protected $use_token = 1;

		// 开启验证用户是否已进入养殖基地
		protected $use_bsid = true;

		// 监测加入到get或post中的数据
		// 通常是$this -> u
		protected $setUsr = 0;
		protected $usr = [];

		protected $baseModel;

		// 追加用户数据
		// 当为$_POST或$_GET加入自定义的数据时
		// user信息将默认不被加入
		// 将本属性设置为1则将追加用户数据到$_POST或$_GET
		protected $append = 0;

		// 认证公司登录页
		const login_page = 'manage/admin/login';

		// 学习公司登录页
		const login_page_company = 'manage/company/login';

		// 学习公司注册页
		const register_page_company = 'manage/company/register';

		// 学习公司默认跳转主页
		const company_index = 'manage/company/index';

		// 认证公司默认跳转主页
		const admin_index = 'manage/account/list';

		protected $userinfo;






		/**
		 * 微信小程序登录态
		 */
		protected $wxtoken;

		public function _initialize() {

			$this -> mca = trim($_SERVER['REQUEST_URI'], '/');
			$this -> uri = strtolower(MODULE_NAME. '/' .CONTROLLER_NAME. '/' .ACTION_NAME);
			$this -> baseModel = new \Common\Model\BaseModel;
			$this -> userinfo = self::getLoginSession();
			$this -> assign('menu', $this -> menu($this -> select_domain()));
		}


		/**
		 * 取当前登录态
		 * @Author   邱湘城
		 * @DateTime 2019-01-05T14:11:46+0800
		 */
		private static function getLoginSession() {

			$tmp = session('userinfo');
			if ($tmp && $tmp = json_decode($tmp, true)) {
				return $tmp;
			}
			return [];
		}


		/**
		 * 根据域名前缀分两个端
		 * @DateTime 2019-01-03T22:39:57+0800
		 */
		protected function select_domain() {

			$type = ['admin' => 1, 'course' => 2];
			$tmp = explode('.', $_SERVER['HTTP_HOST']);
			if (!count($tmp)) {
				return $type['admin'];
			}
			if (!in_array($tmp[0], ['admin', 'course'])) {
				return $type['admin'];
			}
			return $type[$tmp[0]];
		}


		/**
		 * 保存当前用户SESSION
		 * @Author   邱湘城
		 * @DateTime 2019-01-13T14:29:58+0800
		 */
		protected function save_openid_token($openid, $data) {

			// return session($openid, serialize($data));
			return session($openid, serialize($data));
		}


		/**
		 * 取得用户数据
		 * @Author   邱湘城
		 * @DateTime 2019-01-13T14:31:06+0800
		 */
		protected function get_openid_token($openid) {

			if (is_null(session($openid))) {
				return false;
			}
			return unserialize(session($openid));
		}


		/**
		 * Redis 单例
		 */
		protected static function redisInstance() {

			if(!self::$redis){
				self::$redis = new \redis;
				self::$redis -> connect('0.0.0.0', 6366);
			}
			return self::$redis;
		}

		// 如果调用了本函数 则返回rel字段
		protected function rel($arr = []) {

			$this -> output = 1;
			!empty($arr) && $this -> rel = $arr;
			return $this;
		}

		// 当$bool的返回值等于$u时 输出$msg
		// 默认$bool等于false时输出$msg
		protected function el($bool, $msg, $u = false) {

			if (intval($bool) == intval($u)) {
				$this -> e($msg);
			}
		}

		/**
		 * $type = 1 表示课程公司菜单
		 * $type = 2 表示上课公司菜单
		 * @DateTime 2018-12-23T22:25:40+0800
		 */
		protected function menu($type = 1) {

			$str = '';
			$icons = [
				'company' => 'fa-home',
				'course' => 'fa-graduation-cap',
				'question' => 'fa-book',
				'exam' => 'fa-desktop',
				'account' => 'fa-trophy',
			];
			$menu = $this -> baseModel -> getMenu(['is_show' => 0, 'type' => $type]);

			foreach ($menu as $items) {
				$icon = '';
				$current = '';
				$urilist = explode('/', $items['uri']);
				if ($urilist[1] == strtolower(CONTROLLER_NAME)) {
					$current = ' class="current-page"';
					isset($icons[$urilist[1]]) && $icon = $icons[$urilist[1]];
				}
				$str .= '<li' . $current . '><a href="/' . $items['uri'] . '"><i class="fa ' . $icon . '"></i> ' . $items['name'] . ' </a></li>' . PHP_EOL;
			}

			return $str;
		}


		/**
		 * 如果什么都不传 则默认为成功 code = 0
		 * @param $param 如果传输了编号 code = $param
		 * @param $msg 完成或失败后的提示语
		 * @param 在返回成功时，如果$type=1将直接输出jsonp格式的文件 否则默认不输出文件
		 * @param $code = 0 成功 $code = 1 失败 $code = 2 权限导致的失败
		 */
		protected function e($param = 0, $msg = 'Success') {

			$r['code'] = $param;
			$r['msg'] = $msg;
			if ($this -> output == 1) {
				$r['rel'] = [];
			}
			if (!is_numeric($param)) {
				$r['msg']  = $param;
				$r['code'] = is_numeric($msg) ? $msg : 1;
			}
			if (is_null($this -> rel) || !is_array($this -> rel)) {
				$r['code'] = 1;
				$r['msg'] = 'Field';
			} else {
				if (count($this -> rel) != 0) {
					$r['rel'] = $this -> rel;
				}
			}
			return $this -> ajaxReturn($r);
		}

		// 指定是否要验证TOKEN
		// 该函数与_post或_get函数结合
		// 当调用本函数时 将不验证TOKEN
		protected function ignore_token($p = 0) {

			$this -> use_token = $p;
			return $this;
		}

		// 取消验证用户的ID
		// 该函数与_post或_get函数结合
		// 即 当用户未进入养殖基地时也可以跳过对用户养殖基地ID的验证
		protected function ignore_bsid() {

			$this -> use_bsid = false;
			return $this;
		}

		// 将当前用户的基本ID放入到变量中
		// 该函数与_post或_get函数结合
		// 指定$append_user = 1时将默认为POST接收的值加入当前登录者信息
		// 当$arr有数据时 默认不追加用户数据
		protected function usr($arr = [], $append_user = 0) {

			$this -> setUsr = 1;
			$this -> append = $append_user;
			if (count($arr) != 0) {
				foreach ($arr as $k => $item) {
					$this -> usr[$k] = $item;
				}
			}
			return $this;
		}

		// 当$_POST或$_GET没有传输某些参数时
		// 如果未转输某些参数 使用本函数指定 并为其加入默认值
		// 指定$append_user = 1时 将默认为$_POST或$_GET接收的值加入当前登录者信息
		// 该函数与_post或_get函数结合
		/**** 使用本函数后 函数usr()失效 它已经包含了usr()的部分功能 ****/
		protected function deft($arr, $append_user = 0) {

			$this -> setUsr = 1;
			$this -> append = $append_user;
			foreach($arr as $k => $v) {
				if (!$this -> requests($k)) {
					$this -> usr[$k] = $v;
				}
			}
			return $this;
		}

		// POST请求方式校验
		// 默认对TOKEN进行验证
		protected function _post(&$arr = [], $pc = []) {

			if (!IS_POST) {
				$this -> e("非法请求", 4);
			}
			$this -> token_auth();
			if (!empty($pc)) {
				$this -> paramCheck($pc);
			}
			$this -> jsonDecode($arr);
			// 如果临时加入了一些数据 从这里加入
			if ($this -> setUsr == 1) {
				if (count($this -> usr) == 0 || $this -> append) {
					$this -> setUsr();
				}
				$arr = array_merge($arr, $this -> usr);
			}
			return 0;
		}

		// GET请求方式校验
		// 默认对TOKEN进行验证
		protected function _get(&$arr = [], $pc = []) {

			if (IS_POST) {
				$this -> e("非法请求", 4);
			}
			$this -> token_auth();
			if (count($pc) != 0) {
				$this -> paramCheck($pc);
			}
			$arr = array_map('trim', I());
			// 如果临时加入了一些数据 从这里加入
			if ($this -> setUsr == 1) {
				if (count($this -> usr) == 0 || $this -> append) {
					$this -> setUsr();
				}
				$arr = array_merge($arr, $this -> usr);
			}
			return 0;
		}

		// 为$_REQUEST增加新推入的默认数据
		// 当_get()或_post()指定了$this -> usr()时 默认为其加入用户当前数据
		private function setUsr() {

			if (isset($this -> u['base_id'])) {
				$this -> usr['base_id'] = $this -> u['base_id'];
			}
			if (isset($this -> u['id'])) {
				$this -> usr['uid'] = $this -> u['id'];
			}
			if (isset($this -> u['phone'])) {
				$this -> usr['phone'] = $this -> u['phone'];
			}
		}

		/**
		 * 验证TOKEN是否已传入
		 * 并验证是否合法
		 * 如果已通过验证 存入通用变量
		 * 且根据它取得当前登录者信息
		 */
		protected function token_auth() {

			// 反射
			$reflex = get_called_class();
			if ($reflex::$token != 1 || !$this -> use_token) {
				return;
			}
			if (!($this -> token_str = $this -> requests('token'))) {
				$this -> e('Invalid Token!');
			}
			if (!($this -> u = $this -> getUserByToken($this -> token_str)) || !isset($this -> u['id'])) {
				$this -> e('Invalid Token!');
			}
			// 续期
			$this -> save_token($this -> token_str, $this -> u);
			// pr($this -> u);

			// if ($this -> use_bsid && !isset($this -> u['base_id'])) {
			// 	pr($this -> u);
			// 	$this -> e('当前您没有进入任何养殖基地，无法继续下一步操作...');
			// }
			// 验证当前用户权限
			// $this -> ctrl_auth();
		}

		// 取得POST过来的JSON或者GET的数据
		protected function requests($param = '') {

			if (IS_POST) {
				$r = json_decode(file_get_contents("php://input"), true);
				if (is_null($r)) {
					$r = I('post.');
				}
			} else {
				$r = I();
			}
			$r = array_map('trim', $r);
			if ($param != '') {
				if (isset($r[$param])) {
					return $r[$param];
				}
				return false;
			}
			return $r;
		}

		// 判断数组$request的某些字段的值是否为整型
		protected function isInt($arr) {

			$p = $this -> requests();
			foreach ($arr as $value) {
				if (isset($p[$value]) && !is_numeric($p[$value])) {
					if ($confName = conf('dictionaries,' . $value)) {
						$value = $confName;
					}
					$this -> e($value . '必须是数字');
				}
			}
		}

		/**
		 * 根据用户登录数据生成TOKEN
		 * @param $data['account']
		 * @param $data['password']
		 */
		protected function token_fetch(&$data, $outTime = 0) {

			// 加密用户信息以生成TOKEN
			return $this -> _encrypt('NBtech!' . $data['account'] . $data['password'] . time());
		}

		/**
		 * 保存用户到TOKEN的关联
		 */
		protected function save_token($token, $data, $outTime = 0) {

			$outTime == 0 && $outTime = C('TOKEN_OUT_TIME');
			session('token', serialize($data));
			return unserialize(session('token'));

//			return self::redisInstance() -> setEx($token, $outTime, serialize($data));
    	}

    	protected function islogin() {

    		$format = $this -> select_domain();
    		if (!$this -> get_token_value('token')) {
    			if ($format == 1 && $this -> uri != self::login_page) {
					$this -> redirect(self::login_page);
				}
				if ($this -> uri != self::login_page_company && $this -> uri != self::register_page_company) {
					$this -> redirect(self::login_page_company);
				}
				return;
			}

			if ($format == 1) {
				if ($this -> uri == self::login_page) {
					$this -> redirect(self::admin_index);
				}
				return;
			}
			if ($this -> uri == self::login_page_company) {
				$this -> redirect(self::company_index);
			}
    	}

    	// 根据TOKEN取值
    	protected function get_token_value($token) {

			return unserialize(session($token));
    	}

    // 权限验证
    // 当前登录者在本基地的权限验证
    // 如果登录者还没有选择进入基地 将无法取得他的权限
    // 返回错误给登录者
    private function ctrl_auth()
    {

        // Login模块不做验证
        if (CONTROLLER_NAME == "Login") {
            return;
        }
        $msg = '你没有权限';
        if (!isset($this->u['id']) || !isset($this->u['base_id'])) {
            $this->e($msg);
        }
        $count = M()->query('SELECT count(*) c FROM us_user_role ur
			JOIN us_role_auth ra ON ur.role_id = ra.role_id
			JOIN us_auth a ON ra.auth_id = a.id
			WHERE ur.user_id = ' . $this->u['id'] . ' AND ur.base_id = ' . $this->u['base_id'] . ' AND a.route = "' . $this->mca . '"');
        if ($count[0]['c'] == 0) {
            $this->e($msg);
        }
    }

    // 删除TOKEN
    // 当注销用户时
    protected function token_flush($token) {

        return self::redisInstance()->delete($token);
    }

    /**
     * 根据TOKEN取得用户信息
     * @param $token
     * @return Array/bool
     */
    protected function getUserByToken($token) {

    	$tk = session($token);
    	if (is_null($tk)) {
    		return false;
    	}
    	return unserialize($tk);

        // if (false !== ($token = self::redisInstance()->get($token))) {
        //     return unserialize($token);
        // }
        // return false;
    }

    // 检测字符串长度 如果小于$len则跳出程序
    // $this -> lenCheck('pn_name,养殖池的名称', 3);
    protected function lenCheck($str, $len, $end = 0) {

        if (false == ($value = $this->requests($str))) {
            return;
        }
        $len_value = mb_strlen($value);
        if (false == ($confName = conf('dictionaries,' . $str))) {
            $confName = $str . '的值';
        }
        if ($end > $len) {
            if ($len_value < $len || $len_value > $end) {
                $msg = '必须在' . $len . '到' . $end . '个字之间';
            }
        } else if ($end == $len && $len != $len_value) {
            $msg = '必须是' . $len . '个字符';
        } else if ($len_value < $len) {
            $msg = '不得少于' . $len . '个字';
        }
        if (isset($msg)) {
            $this->e($confName . $msg);
        }
    }

    // 验证得到的数据中 参数$param所允许的值
    // 如：suiValue("type", [1,2]) 验证type的值是否为1或2
    protected function suiValue($param, $psValue = [])
    {

        if ($str = $this->requests($param)) {
            foreach ($psValue as $item) {
                if ($str == $item) {
                    return;
                }
            }
            $this->e($param . '的值不合法');
        }
    }

    /**
     * 过滤参数（带个性化提示）
     * @param $param 需要传输的参数 如果param的形式是关联数组，则数组值均为提示语；如果是索引数组，仅做参数判断
     * @param $type 当未传输时，是否输出本参数
     * @param $option 默认将符号“-”当成未传输
     */
    protected function paramCheck($param = [], $option = '-')
    {

        $s = '';
        $msg = '缺少参数';
        $get = $this->requests();
        foreach ($param as $key => $values) {
            if (is_numeric($key)) {
                if (!array_key_exists($values, $get) || $get[$values] === '') {
                    $s .= $values . ', ';
                }
            } else {
                if (!array_key_exists($key, $get) || $get[$key] === '') {
                    $this->e($values . '/' . $key, 5);
                }
            }
        }
        if (!APP_DEBUG) $this->e($msg); // 关闭调试的时候不再显示缺省的参数名
        if (!empty($s)) $this->e($msg . ': ' . rtrim($s, ', '), 5);
        return 0;
    }

    /**
     * 通用加密类（TOKEN生成不能使用这一类）
     */
    protected function _encrypt($str, $password_type = PASSWORD_DEFAULT)
    {
        return password_hash($str, $password_type);
//			return strtoupper(md5("NBI,nongbo_tech.com!" . $str));
    }

    /**
     * 检测图片验证码是否正确
     * @param $code 验证码
     * @return boolean
     */
    protected function check_img_verify($code, $id = '')
    {

        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    // 检查手机号是否合法
    protected function phoneCheck($phone, $msg = '')
    {

        if (empty($msg)) $msg = '手机号码不规范';
        $this->isInt($phone);
        if (strlen($phone) != 11) {
            $this->e($msg);
        }
        if (!preg_match("/^[1]{1}[3578]{1}\d{9}$/", $phone)) {
            $this->e($msg);
        }
    }

    // 转换POST到服务器的JSON为数组 如果JSON不合法 将直接返回错误数据
    protected function jsonDecode(&$arr)
    {

        $arr = json_decode(file_get_contents("php://input"), true);
        if (is_null($arr)) {
            $arr = I();
            if (!count($arr)) {
                $this->e("数据格式有误", 3);
            }
        }
        return 0;
    }

    /**
     * 发送信息到某个手机
     * @param $phone 接收信息的手机
     * @param $smsId 短信模板
     * @param $tempLateMsg 短信变量内容替换
     */
    protected function sendShortMessage($phone, $smsId, $tempLateMsg = '')
    {

        $ali = new \Ali\Msg\TopClient;
        $ali->appkey = '23556318';
        $ali->secretKey = 'f55f1a76af7304a8e1e4da6345729f0e';
        $req = new \Ali\Msg\AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("农博创新");
        $req->setSmsParam($tempLateMsg);
        $req->setRecNum($phone);
        $req->setSmsTemplateCode($smsId);
        $resp = $ali->execute($req);
        return $resp->code == 0 && 1;
    }

    /**
     * @param $sms 阿里云验证码模板编号
     */
    protected function sendMessage($phone, $verifyCode, $sms)
    {

        $c = new \Ali\Msg\TopClient;
        $c->appkey = '23556318';
        $c->secretKey = 'f55f1a76af7304a8e1e4da6345729f0e';
        $req = new \Ali\Msg\AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("农博创新");
        $req->setSmsParam("{code:'" . $verifyCode . "'}");
        $req->setRecNum($phone);
        $req->setSmsTemplateCode($sms);
        $resp = $c->execute($req);
        return $resp->code == 0 && 1;
    }

    // CURL GET
    protected function httpGet($url)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $out = curl_exec($ch);
        curl_close($ch);
        $out = $this->not_json($out) ? $out : json_decode($out, true);
        return $out;
    }

    // CURL POST
    protected function httpPost($url, $post_data)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1); //设置post方式提交
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json;charset=utf-8'));
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

	/**
	 * 订单号生成
	 * @Author   邱湘城
	 * @DateTime 2019-01-09T00:57:33+0800
	 */
	protected function fetch_order_num($str = '') {

		// 取当前用户当前页面订单号 key
		if (!empty($str)) {
			$company = session($str);
			if (!is_null($company)) {
				return ['orderNum' => $str, 'company' => $company];
			}
			return false;
		}

		$session_key_order = md5('ORDER_NUM:' . $this -> userinfo['id']);
		$orderNum = 100 . mt_rand(100, 999) . date('YmdHis') . mt_rand(100000, 999999);
		session($orderNum, $session_key_order);
		return ['orderNum' => $orderNum, 'company' => $session_key_order];
	}
}