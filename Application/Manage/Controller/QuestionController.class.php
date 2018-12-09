<?php

/**
 * Questionģ�����
 * @Auther Cuiruijun
 * @Date 2018/12/9
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
use Manage\Model\CourseModel;
use Manage\Model\QuestionsModel;

class QuestionController extends BaseController {

    // ���HTTP�ӿڵĹ̶�TOKEN
    CONST HTTP_TOKEN_N1 = '8FA02B017FCDE7836A6FDB5D00AC638F';

    protected $base_url = 'http://192.168.1.220';

    // ����һ������Ϻõ�JSON����
    protected function postFetch(&$data) {
        $data['token'] = self::HTTP_TOKEN_N1;
        return $data;
    }

    public function _initialize() {

        parent::_initialize();
    }

    /**
     * �������޸���Ŀ
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

    /*
     * ��ȡ���пγ��б�
     *
     * **/
    public function course()
    {
        $Course = new CourseModel();
        $list = $Course->getList();
        $this->e(0, $list);
    }

    /*
     * ɾ����Ŀ
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

        //ɾ��
        $result = $Question->del($id);
        if ($result) {
            $this->e();
        } else {
            $this->el(0, 'fail');
        }
    }

    /*
     *��ȡ�б�
     * **/
    public function index() {
        $params = $this->_get($_GET);

        $Question = new QuestionsModel();
        $list = $Question->getAll('*', 'is_deleted = 0', $params['page'], $params['pageNum']);

        $Course = new CourseModel();
        $courseList = $Course->getList();
        foreach ($list as &$val) {
            $val['course_id'] = $courseList[$val['course_id']];
            $val['option'] = json_decode($val['option'], true);
        }

        $this->e(0, $list);
    }


    public function test(){
        var_dump("ddddd");exit;
    }

    // ������ĸ�����
    // @param MD5֮�������
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