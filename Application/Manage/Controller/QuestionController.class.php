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

class QuestionController extends BaseController {

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

        $this -> question = new \Manage\Model\QuestionsModel;
        $this -> course = new \Manage\Model\CourseModel;
    }

    /**
     * 新增或修改题目
     *
     * {
        "course_id": 1,
        "type": 1,
        "title": "what is you sex?",
        "answer": "1",
        "option": [
        "girlssss",
        "boy"
        ],
        "id": 4
     * }
     * **/
    public function operate()
    {
        if (IS_POST) {
            $data = $this->postFetch($_POST);
            $this->_post($data, ['course_id', 'type', 'title', 'answer']);

            if ($data['type'] == 1 || $data['type'] == 3) {
                if (strlen($data['answer']) != 1) {
                    $this->el(0, '答案只能有一个');
                }
            }elseif ($data['type'] == 2) {
                if (!count($data['answer'])) {
                    $this->el(0, '复选答案至少要有一个');
                }
                $data['answer'] = json_encode($data['answer']);
            } elseif ($data['type'] == 4) {
                if (strlen($data['answer']) == '') {
                    $this->el(0, '答案不能为空');
                }
            }

            if (!$data['course_id']) {
                $this->el(0, '科目不能为空');
            }

            $data['created_time'] = time();
            $data['updated_time'] = time();
            $data['option'] = json_encode($data['option']);

            $Question = M('Questions');
            $final = $Question->create($data);

            if (!empty($final['id'])) {
                $model = new QuestionsModel();
                $record = $model->getOne('is_deleted = 0 AND id = ' . $final['id']);
                if (empty($record)) {
                    $this->el(0, 'The record does not exist');
                }

                unset($final['created_time']);
                $result = $Question->save($final);
            } else {
                $result = $Question->add($final);
            }

            if ($result) {
                $this->e();
            } else {
                $this->el($result, 'fail');
            }
        }

        //参数
        if (!empty($_GET['id'])) {
            $exist = $this -> question -> getOne('id = ' . $_GET['id']);
            $data['record'] = $exist;
            $data['type'] = $exist['type'];
            if ($exist['type'] < 3) $data['record']['option'] = json_decode($data['record']['option'], true);
            if ($exist['type'] == 2) {
                $data['record']['answer'] = json_decode($data['record']['answer'], true);
                $answer = [];var_dump($data['record']['answer']);
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
        $this->assign($data);
        $this->display();
    }

    /*
     * 删除题目
     *
     * @param int $id
     * **/
    public function del()
    {
        $id = $_GET['id'];

        $Question = new QuestionsModel();
        $record = $Question->getOne('is_deleted = 0 ANd id = ' . $id);
        if (empty($record)) {
            $this->el($record, 'The record does not exist');
        }

        //删除
        $result = $Question->del($id);
        if ($result) {
            $this->e();
        } else {
            $this->el(0, 'fail');
        }
    }

    /*
     *获取列表
     * **/
    public function index()
    {
        $params = $this->_get($_GET);

        $Question = new QuestionsModel();
        $list = $Question->getAll('*', 'is_deleted = 0', $params['page'], $params['pageNum']);

        $Course = new CourseModel();
        $courseList = $Course->getList();
        foreach ($list as &$val) {
            $val['course_id'] = $courseList[$val['course_id']];
            $option = json_decode($val['option'], true);
            $val['option'] = implode('|', $option);
        }

        $this->assign(['data' => $list]);
        $this->display('question/list');
    }


    public function test(){
        var_dump("ddddd");exit;
    }

    // 生成字母随机数
    // @param MD5之后的密码
    public function fetchRandPwd(&$pwdMD5, $len = 6) {

        $str = '';
        $mod = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for ($i = 0; $i < $len; $i ++) {
            $str .= $mod[mt_rand(0, 35)];
        }
        $pwdMD5 = $this -> _encrypt($str);
        return $str;
    }
}