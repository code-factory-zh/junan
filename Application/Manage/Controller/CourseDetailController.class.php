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
		$this -> islogin();
        $this -> courseDetail = new \Manage\Model\CourseDetailModel;
        $this -> course = new \Manage\Model\CourseModel;
    }

    /**
     * 课程章节-列表
     * @DateTime 2018-12-08T17:58:00+0800
     */
    public function index()
    {
		$id = I('get.course_id');

        $chapters = $this -> courseDetail -> getChapter('course_id = ' . $id . ' and is_deleted = 0', 'id,chapter,sort');

        $this -> assign(['data' => $chapters, 'id' => $id]);
        $this -> display('Course_detail/index');
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

                $ext = substr(strrchr($p['content'], '.'), 1);

                if ($p['type'] == 2 && $ext != 'ppt') {
                    $this -> e('文件必须是PPT');
                } elseif ($p['type'] == 3 && !in_array($ext, ['mp4', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'])) {
                    $this->e('文件必须是视频');
                }
            }

            $id = I('post.id');
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
        $this->display('Course_detail/edit');
    }

	/**
	 * webuploader 上传文件
	 */
	public function upload(){
		// 根据自己的业务调整上传路径、允许的格式、文件大小
		$p = I('post.');
        $ext = substr(strrchr($p['name'], '.'), 1);

        if (in_array($ext, ['ppt', 'pptx'])) {
            $type = 2;
            $dir = 'Uploads/file';
        } elseif (in_array($ext, ['mp4', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'])) {
            $type = 3;
            $dir = '/media';
        } else {
            $this -> e('上传类型必须是PPT或者视频文件');
        }

        $path = $dir .'/';
		if (!file_exists($path))
		{
			mkdir($path, 0777, true);
		}

        ajaxUpload($path, $dir, $type);
	}
}