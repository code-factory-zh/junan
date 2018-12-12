<?php

/**
 * @Dec    Upload模块主模型
 * @Auther cuiruijun
 * @Date   2018/12/10
 */
namespace Manage\Model;
use Common\Model\BaseModel;

class UploadModel extends BaseModel {

    protected $tableName = 'upload';

    public function _initialize() {

        parent::_initialize();
    }

    /**
     * 上传文件类型控制
     * @param  string   $path    字符串 保存文件路径示例： /Upload/image/
     * @param  integer  $id     图片ID
     * @param  string   $format  文件格式限制
     * @param  integer  $maxSize 允许的上传文件最大值 52428800
     * @param  integer  $aas   判断返回方式  1为ajax返回   2为rerurn
     * @return booler   返回ajax的json格式数据
     */
    public function ajaxUpload($path='file', $id='', $format='empty', $maxSize='52428800' ,$aas=2){

        ini_set('max_execution_time', '0');
        // 去除两边的/
        $path=trim($path,'/');
        // 添加Upload根目录
        $path=strtolower(substr($path, 0,6))==='Uploads' ? ucfirst($path) : 'Uploads/'.$path;
        // 上传文件类型控制
        $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
        if(!empty($_FILES)){
            // 上传文件配置
            $config=array(
                'maxSize'   =>  0,               // 上传文件最大为50M
                'rootPath'  =>  './',                   // 文件上传保存的根路径
                'savePath'  =>  '/'.$path.'/',         // 文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',time()),     // 上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  false,                   // 自动使用子目录保存上传文件 默认为true
                'exts'      =>    isset($ext_arr[$format]) ? $ext_arr[$format] : '',
            );
            // 实例化上传
            $upload=new \Think\Upload($config);
            // 调用上传方法
            $info=$upload->upload();

            $data=array();
            if(!$info){
                return $upload->getError();
               /* // 返回错误信息
                $error=$upload->getError();file_put_contents('error.txt', var_export($error, true));die;
                $data['error_info'] = $error;
                return $data;*/
            }else{
                foreach($info as $file){
                    $image['savename']=$file['savename'];
                    $image['path']=$file['savepath'];
                    $image['created_time']=time();
                    $image_id = D('upload')->add($image);
                    $data['name']=trim($file['savepath'].$file['savename'],'.');
                    $data['ImagesId']=$image_id;
                    return $data;
                }
            }
        }
    }
}