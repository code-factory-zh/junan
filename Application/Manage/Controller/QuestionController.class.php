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
            $this->_post($data, ['course_id', 'type', 'title', 'answer', 'option']);

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
        } else {
            $data['type'] = $_GET['type'];
        }

        $data['course'] = $this -> course -> getList();
        $this->assign($data);
        $this->display();
    }

    /*
     * 获取所有课程列表
     *
     * **/
    public function course()
    {
        $Course = new CourseModel();
        $list = $Course->getList();
        $this->e(0, $list);
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
    public function list()
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
        $this->display();
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