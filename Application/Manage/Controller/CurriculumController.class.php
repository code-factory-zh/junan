<?php

/**
 * 课程模块
 * @Auther QiuXiangCheng
 * @Date 2018/12/09
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
class CurriculumController extends BaseController {

	private $account;
	private $curriculum;

	public function _initialize() {

		parent::_initialize();
		$this -> islogin();
		$this -> ignore_token();

		$this -> course = new \Manage\Model\CourseModel;
		$this -> account = new \Manage\Model\AccountModel;
		$this -> curriculum = new \Manage\Model\CurriculumModel;
	}

	/**
	 * 岗位课程列表
	 * @DateTime 2018-12-11T23:56:15+0800
	 */
	public function list() {

		// $session_key = 'company_id:' . $this -> userinfo['id'];
		// pr(session($session_key));
		$this -> _get($g);
		$data = [];
		// $where = "company_id = {$this -> company_id}";
		$where = 1;
		$data['list'] = $this -> curriculum -> getCourseListByWhere($where, 'c.id, c.name, c.job_id, cac.amount');
		$data['course_id'] = $g['course_id'];
		$data['job_id'] = $g['job_id'];

		$data['buy'] = 0;
		$s = session('company_id:' . $this -> userinfo['id']);
		if (!is_null($s)) {
			$data['buy'] = 1;
		}
		$this -> assign($data);
		$this -> display('Curriculum/list');
	}


	/**
	 * 添加课程 将当前企业下的用户绑定到课程课程课程去
	 * @DateTime 2019-01-05T13:17:26+0800
	 */
	public function addCourse() {

		$this -> _post($p);
		if ((($count = count($p)) && $count < 2) || !isset($p['course_id'])) {
			$this -> e('参数错误！');
		}

		$session_key = 'company_id:' . $this -> userinfo['id'];
		$accounts = [];
		$list = $this -> account -> getAccount(['a.company_id' => $this -> userinfo['id']]);
		foreach ($list as $job) {
			$accounts[$job['mobile']][] = $job['job_id'];
		}
		$data = ['course_id' => $p['course_id'], 'job_id' => $p['job_id'], 'price' => 0, 'phone_list' => []];

		$course_price = $this -> course -> getCourseAmount(['id' => $p['course_id']]);
		if (is_null($course_price)) {
			$this -> e('异常，无法取得课程价格！');
		}

		unset($p['course_id'], $p['job_id']);
		foreach ($p as $items) {
			if (empty($items)) {
				$this -> e('输入框内的手机号码不得为空');
			}

			$this -> phoneCheck($items, '手机号码"' . $items . '"不规范！');
			if (!isset($accounts[$items])) {
				$this -> e('手机号码"' . $items . '"不在您的帐户下，请检查！');
			}

			if (!in_array($data['job_id'], $accounts[$items])) {
				$this -> e('用户"' . $items . '"的工作岗位不适用于该课程！');
			}
			$data['price'] += intval($course_price);
			$data['phone_list'][] = $items;
		}

		// 保存课程
		$session_list = session($session_key);
		if (is_null($session_list)) {
			session($session_key, [$data['course_id'] => $data]);
		} else {
			if (isset($session_list[$data['course_id']])) {
				$tmp = array_merge($session_list[$data['course_id']]['phone_list'], $data['phone_list']);
				$session_list[$data['course_id']]['phone_list'] = array_unique($tmp);
			} else {
				$session_list[$data['course_id']] = $data;
			}
			session($session_key, $session_list);
		}

		// print_r(session($session_key));
		$this -> e(0);
	}


	/**
	 * 订单列表
	 * @DateTime 2019-01-05T14:17:50+0800
	 */
	public function order_list() {

		$session_key = 'company_id:' . $this -> userinfo['id'];
		$list = session($session_key);
		$data = [];
		$total = 0;

		// 生成订单号
		$this -> fetch_order_num();
		if (count($list)) {
			$jobs = $this -> account -> getCourse();
			$users = $this -> account -> getAccountColumn(['company_id' => $this -> userinfo['id']]);
			foreach ($list as $k => $items) {
				$tmp = [
					'job_name' => $jobs[$items['job_id']],
					'users' => [],
					'price' => $items['price'],
				];
				$total == $items['price'];
				foreach ($items['phone_list'] as $v) {
					$tmp['users'][] = ['mobile' => $v, 'name' => $users[$v]];
				}
				$data[$k] = $tmp;
			}
		}

		$this -> assign('total', $total);
		$this -> assign('list', $data);
		$this -> display('Curriculum/buy');
	}
}