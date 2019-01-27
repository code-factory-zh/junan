<?php

/**
 * Question模块基类
 * @Auther Cuiruijun
 * @Date 2018/12/9
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
use Manage\Model\CourseModel;
use Manage\Model\QuestionsModel;

class QuestionController extends CommonController {

    private $question;
    private $course;

    // 针对HTTP接口的固定TOKEN
    CONST HTTP_TOKEN_N1 = '8FA02B017FCDE7836A6FDB5D00AC638F';

    protected $base_url = 'http://192.168.1.220';

    // 生成一个被组合好的JSON数据
    protected function postFetch(&$data) {
        $data['token'] = self::HTTP_TOKEN_N1;
        return $data;
    }

    public function _initialize() {
        parent::_initialize();

		$this -> islogin();
        $this -> question = new \Manage\Model\QuestionsModel;
        $this -> course = new \Manage\Model\CourseModel;
    }

    /**
     * 接入公司管理-列表
     * @author cuiruijun
     * @date   2018/12/08 下午10:20
     * @url    manage/question
     * @method get
     *
     * @return  array
     */
    public function index()
    {
//        $params = $this->_get($_GET);
        $params = I('get.');

        $list = $this -> question -> getAll('*', 'is_deleted = 0', $params['page'], $params['pageNum']);

        $courseList = $this -> course -> getList();
        $array = array_column($courseList, 'name', 'id');
        foreach ($list as &$val) {
            $val['course_id'] = $array[$val['course_id']];
            $option = json_decode($val['option'], true);
            $val['option'] = implode('|', $option);
        }

        $this -> assign(['data' => $list]);
        $this -> display('Question/index');
    }

    /**
     * 新增/修改题目
     * @author cuiruijun
     * @date   2018/12/8 下午21：03
     * @url    manage/question/edit
     * @method post
     *
     * @param  int course_id 课程ID
     * @param int type 类型
     * @param string title 标题
     * @param string answer 答案
     * @return  array
     */
    public function edit()
    {
        if (IS_POST) {
//            $data = $this -> postFetch($_POST);
//            $this -> _post($data, ['course_id', 'type', 'title', 'answer']);

			$data = I('post.');

            if ($data['type'] == 1 || $data['type'] == 3) {
                if (strlen($data['answer']) != 1) {
                    $this -> e('答案只能有一个');
                }
            }elseif ($data['type'] == 2) {
                if (!count($data['answer'])) {
                    $this -> e('复选答案至少要有一个');
                }
                $data['answer'] = json_encode($data['answer']);
            } elseif ($data['type'] == 4) {
                if (strlen($data['answer']) == '') {
                    $this -> e('答案不能为空');
                }
            }

            if (!$data['course_id']) {
                $this -> e('科目不能为空');
            }

            $data['created_time'] = time();
            $data['updated_time'] = time();
            $data['option'] = json_encode($data['option']);

            $Question = M('Questions');
            $final = $this -> question -> create($data);

            if (!empty($final['id'])) {
                $record = $this -> question -> getOne('is_deleted = 0 AND id = ' . $final['id']);
                if (empty($record)) {
                    $this -> e('记录为空');
                }

                unset($final['created_time']);
                $result = $this -> question -> save($final);
            } else {
                $result = $this -> question -> add($final);
            }

            if ($result) {
                $this->e();
            } else {
                $this->e('fail');
            }
        }

        //参数
        if (!empty(I('get.id'))) {
            $exist = $this -> question -> getOne('id = ' . I('get.id'));
            $data['record'] = $exist;
            $data['type'] = $exist['type'];
            if ($exist['type'] < 3) $data['record']['option'] = json_decode($data['record']['option'], true);
            if ($exist['type'] == 2) {
                $data['record']['answer'] = json_decode($data['record']['answer'], true);
                $answer = [];
                foreach ($data['record']['answer'] as $v) {
                    $answer[$v] = $v;
                }

                $option = [];
                foreach ($data['record']['option'] as $key => $value) {
                    $tmp['value'] = $value;
                    $tmp['answer'] = 1;
                    if (isset($answer[$key+1])) {
                        $tmp['answer'] = 2;
                    }
                    $option[] = $tmp;
                }
                $data['record']['option'] = $option;
            }
        } else {
            $data['type'] = $_GET['type'];
        }

        $types = [1 => '单选', 2 => '复选', 3 => '判断', 4 => '填空'];
        $data['type_name'] = $types[$data['type']];

        $data['course'] = $this -> course -> getList();
        $this -> assign($data);
        $this -> display('Question/edit');
    }

    /**
     * 删除题目
     * @author cuiruijun
     * @date   2018/12/09 下午10:20
     * @url    manage/question/del
     * @method get
     *
     * @return  array
     */
    public function del()
    {
//        $this -> _get($p, ['id']);
		$p = I('post.');

        $record = $this -> question -> getOne('is_deleted = 0 AND id = ' . $p['id']);
        if (empty($record)) {
            $this -> el($record, '记录不存在');
        }

        //删除
        $result = $this -> question -> del('id = ' .$p['id']);
        if ($result) {
            $this -> e();
        } else {
			$this->el($result, '删除失败');
        }
    }

	/**
	 * 导入题库
	 * @author cuiruijun
	 * @date   2019/1/27 上午10:34
	 * @url    question/batch_add_questions
	 * @method post
	 *
	 * @param  int param
	 * @return  array
	 */
    public function batch_add_questions(){

	}
}