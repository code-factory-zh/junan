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
    private $upload;

    public function _initialize() {

        parent::_initialize();
        $this -> courseDetail = new \Manage\Model\CourseDetailModel;
        $this -> course = new \Manage\Model\CourseModel;
        $this -> upload = new \Manage\Model\UploadModel;
    }

    /**
     * 课程章节-列表
     * @DateTime 2018-12-08T17:58:00+0800
     */
    public function index()
    {
//        $this -> _get($p, ['id']);
        $this -> _get($p, ['id']);
		$id = I('get.id');

        $chapters = $this -> courseDetail -> getChapter('course_id = ' . $id . ' and is_deleted = 0', 'id,chapter,sort');

        $this -> assign(['data' => $chapters, 'id' => $p['id']]);
        $this -> display();
    }

    /**
     * 添加章节
     * @author cuirj
     * @date   2018/12/11 下午11:59
     * @url    manage/course_detail/edit
     * @method post
     *
     * @param string chapter 标题
     * @param int type 1文本框 2ppt 3 视频
     * @param int course_id
     * @param int sort 排序
     * @param string content ppt或者视频地址
     * @param string detail 文本内容
     * @return  array
     */
    public function edit(){
        if (IS_GET) {
//            $this -> _get($a, ['course_id']);
            $data['course_id'] = I('get.course_id');
            $id = I('get.id');
        }

        if (IS_POST) {
//            $this -> _post($p, ['chapter', 'type', 'course_id', 'sort', 'course_id']);
			$p = I('post.');

            if ($p['type'] != 1) {
                if (empty($p['content'])) {
                    $this->e('请上传文件');
                }
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

    /**
     * 上传列表
     * @DateTime 2018-12-12T17:58:00+0800
     */
    public function upload()
    {
        if (empty($_GET['type'])) {
            $this->e();
        }

        if ($_GET['type'] == 2) {
            $type = 'file';
        } elseif ($_GET['type'] == 3) {
            $type = 'media';
        }

        if (empty($_FILES['file'])) {
            $this->e('上传文件不能为空');
        }

        $result = $this -> upload -> ajaxUpload('/'. $type .'/', $_FILES['file']['name'], $type);
        if (isset($result['name'])) {
            $this->e(0, $result);
        } else {
            $this->e($result);
        }
    }
	
	/**
	 * webuploader 上传文件
	 */
	public function ajaxUpload(){
		// 根据自己的业务调整上传路径、允许的格式、文件大小
        ajaxUpload('/upload/image/');
	}
	/**
	 * webuploader 上传demo
	 */
	public function webuploader(){
		// 如果是post提交则显示上传的文件 否则显示上传页面
		if(IS_POST){
			p($_POST);die;
		}else{
			$this->display();
		}
	}
}