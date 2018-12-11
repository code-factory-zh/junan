<?php

/**
 * 章节模块
 * @Auther cuiruijun
 * @Date 2018/12/11
 */
namespace Manage\Controller;
use Common\Controller\BaseController;

class CourseDetailController extends BaseController
{
    private $courseDetail;
    private $course;

    public function _initialize() {

        parent::_initialize();
        $this -> courseDetail = new \Manage\Model\CourseDetailModel;
        $this -> course = new \Manage\Model\CourseModel;
    }

    /**
     * 课程章节-列表
     * @DateTime 2018-12-08T17:58:00+0800
     */
    public function list()
    {
        $this -> _get($p, ['id']);

        $chapters = $this -> courseDetail -> getChapter('course_id = ' . $p['id'] . ' and is_deleted = 0', 'id,chapter,sort');

        $this -> assign(['data' => $chapters, 'id' => $p['id']]);
        $this -> display();
    }

    /**
     * 添加章节
     * @author cuirj
     * @date   2018/12/11 下午11:59
     * @url    manage/course_detail/operate
     * @method post
     *
     * @param  int status 1-启用,0-禁止
     * @return  array
     */
    public function operate(){
        if (IS_GET) {
            $this -> _get($a, ['course_id']);
            $data['course_id'] = $a['course_id'];
            $id = $_GET['id'];
        }

        if (IS_POST) {
            $this -> _post($p, ['chapter', 'type', 'course_id', 'sort', 'detail', 'course_id']);
            if ($p['type'] != 1) {
                $p['content'] = $p['detail'];
                $p['detail'] = '';
            }

            $id = $_POST['id'];
            if (!empty($id)) {
                $exist = $this -> courseDetail -> getDetail('sort = ' . $p['sort'] . ' and course_id = ' . $p['course_id'] . ' and id != ' . $id);
                if ($exist) {
                    $this -> e('章节[' . $p['sort'] . ']已存在');
                }

                $p['updated_time'] = time();
                if (!$this -> courseDetail -> updateData('id = ' . $id, $p)) {
                    $this -> e('失败');
                }
                $this -> e();
            } else {
                //查询章节是否已存在
                $exist = $this -> courseDetail -> getDetail('sort = ' . $p['sort'] . ' and course_id = ' . $p['course_id']);
                if (!empty($exist)) {
                    $this -> e('章节[' . $p['sort'] . ']已存在');
                }

                $p['created_time'] = time();
                $p['updated_time'] = time();
                if (!$this -> courseDetail -> add($p)) {
                    $this -> e('失败');
                }
            }

            $this -> e();
        }

        if (!empty($id)) {
            $data['record'] = $this -> courseDetail -> getDetail('id = ' . $id);
            $data['course_id'] = $data['record']['course_id'];
        }

        $this -> assign($data);
        $this->display();
    }
}