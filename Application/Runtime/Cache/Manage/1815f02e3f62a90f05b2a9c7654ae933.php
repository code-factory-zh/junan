<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/static/assets/images/favicon.ico" type="image/ico" />

    <title>后台管理系统</title>

    <!-- Bootstrap -->
    <link href="/static/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/static/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress加载进度条 -->
    <link href="/static/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- 样式 -->
    <link href="/static/build/css/custom.css" rel="stylesheet">
    <!-- web Uploader -->
    <link rel="stylesheet" href="/static/build/css/webuploader.css">
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>首页</span></a>
                </div>

                <div class="clearfix"></div>

                <!-- 侧边栏 -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <ul class="nav side-menu">
                            <li><a href="index.html"><i class="fa fa-home"></i> 接入公司管理 </a></li>
                            <li class="current-page"><a href="course.html"><i class="fa fa-graduation-cap"></i> 课程管理 </a></li>
                            <li><a href="question.html"><i class="fa fa-book"></i> 题目管理 </a></li>
                            <li><a href="exam.html"><i class="fa fa-desktop"></i> 考试管理 </a></li>
                            <li><a href="certificate.html"><i class="fa fa-trophy"></i> 证书管理 </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- 顶部导航栏 -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav>
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>
                    <!-- 用户名文件在template文件夹下的top_nav.html -->
                    <div class="top_nav_user">
                    </div>
                </nav>
            </div>
        </div>
        <!-- /顶部导航栏 -->

        <!-- 页面内容 -->
        <div class="right_col" role="main">
            <div id="add_edit_course">
                <h3 class="title">新增章节</h3>
                <div action="" class="form-horizontal form-label-left" id="submit" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="course_name" class="control-label col-md-3 col-sm-3 col-xs-12">章节名称</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" name="title" id="course_name" value="<?php echo ($record["chapter"]); ?>" class="form-control col-md-7 col-xs-12 parsley-success" placeholder="请输入章节名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="course_price" class="control-label col-md-3 col-sm-3 col-xs-12">章节顺序</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="number" min="1" value="<?php echo ($record["sort"]); ?>" name="sort" id="course_price" class="form-control col-md-7 col-xs-12 parsley-success" placeholder="请填写章节顺序">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">文件类型</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="radio" name="type" id="file_type1" value="1" <?php if($record["id"] == '' || $record["type"] == 1): ?>checked<?php endif; ?> style="margin-top: 10px"> <label for="file_type1">文字</label>
                            <br>
                            <input type="radio" name="type" id="file_type2" value="2" <?php if($record["type"] == 2): ?>checked<?php endif; ?> > <label for="file_type2">PPT</label>
                            <br>
                            <input type="radio" name="type" id="file_type3" value="3" <?php if($record["type"] == 3): ?>checked<?php endif; ?>> <label for="file_type3">视频</label>
                        </div>
                    </div>

                    <!-- 新建章节 -->
                    <?php if($record["id"] == ''): ?><div class="form-group toggleShow">
                            <label for="detail" class="control-label col-md-3 col-sm-3 col-xs-12">详细内容</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <textarea name="detail" id="detail" rows="10" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group toggleShow" style="display: none">
                            <label for="file_upload" class="control-label col-md-3 col-sm-3 col-xs-12">文件上传</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <form action="./upload?type=2" enctype="multipart/form-data" method="post" id="upload">
                                    <input name="file" type="file" id="file_upload" style="padding-top: 5px;">
                                    <input type="submit" value="提交" >
                                </form>
                                <!--<input name="file_upload" type="file" id="file_upload" style="padding-top: 5px;">-->
                            </div>
                        </div>
                        <div class="form-group toggleShow" style="display: none">
                            <label for="video_upload" class="control-label col-md-3 col-sm-3 col-xs-12">视频上传</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <!-- <form action="./upload?type=3" enctype="multipart/form-data" method="post" id="upload">
                                    <input name="file" type="file" id="video_upload" style="padding-top: 5px;">
                                    <input type="submit" value="提交" >
                                </form> -->
                                <div id="uploader" class="wu-example">
                                    <!--用来存放文件信息-->
                                    <div id="thelist" class="uploader-list"></div>
                                    <div class="btns" style="position: relative;height: 40px;">
                                        <div id="picker">选择文件</div>
                                        <button id="ctlBtn" class="btn btn-sm btn-success">开始上传</button>
                                        <button class="btn btn-sm btn-default stop">暂停</button>
                                        <button class="btn btn-sm btn-default upload">继续</button>
                                        <button class="btn btn-sm btn-danger cancel">取消</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- 修改章节 -->
                    <?php else: ?>
                        <?php if($record["type"] == 1): ?><div class="form-group toggleShow">
                                <label for="detail" class="control-label col-md-3 col-sm-3 col-xs-12">详细内容</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea name="detail" id="detail" rows="10" class="form-control"><?php echo ($record["detail"]); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group toggleShow" style="display: none">
                                <label for="file_upload" class="control-label col-md-3 col-sm-3 col-xs-12">文件上传</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <form action="./upload?type=2" enctype="multipart/form-data" method="post" id="upload">
                                        <input name="file" type="file" id="file_upload" style="padding-top: 5px;">
                                        <input type="submit" value="提交" >
                                    </form>
                                </div>
                            </div>
                            <div class="form-group toggleShow" style="display: none">
                                <label for="video_upload" class="control-label col-md-3 col-sm-3 col-xs-12">视频上传</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <form action="./upload?type=3" enctype="multipart/form-data" method="post" id="upload">
                                        <input name="file" type="file" id="video_upload" style="padding-top: 5px;">
                                        <input type="submit" value="提交" >
                                    </form>
                                </div>
                            </div>
                        <?php elseif($record["type"] == 2): ?>
                            <div class="form-group toggleShow" style="display: none">
                                <label for="detail" class="control-label col-md-3 col-sm-3 col-xs-12">详细内容</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea name="detail" id="detail" rows="10" class="form-control"><?php echo ($record["detail"]); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group toggleShow">
                                <label for="file_upload" class="control-label col-md-3 col-sm-3 col-xs-12">文件上传</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <form action="./upload?type=2" enctype="multipart/form-data" method="post" id="upload">
                                        <input name="file" type="file" id="file_upload" style="padding-top: 5px;" value="<?php echo ($_SERVER['SCRIPT_NAME']); echo ($record["content"]); ?>">
                                        <input type="submit" value="提交" >
                                    </form>
                                </div>
                            </div>
                            <div class="form-group toggleShow" style="display: none">
                                <label for="video_upload" class="control-label col-md-3 col-sm-3 col-xs-12">视频上传</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <form action="./upload?type=3" enctype="multipart/form-data" method="post" id="upload">
                                        <input name="file" type="file" id="video_upload" style="padding-top: 5px;">
                                        <input type="submit" value="提交" >
                                    </form>
                                </div>
                            </div>
                        <?php elseif($record["type"] == 3): ?>
                            <div class="form-group toggleShow" style="display: none">
                                <label for="detail" class="control-label col-md-3 col-sm-3 col-xs-12">详细内容</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea name="detail" id="detail" rows="10" class="form-control"><?php echo ($record["detail"]); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group toggleShow" style="display: none">
                                <label for="file_upload" class="control-label col-md-3 col-sm-3 col-xs-12">文件上传</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <form action="./upload?type=2" enctype="multipart/form-data" method="post" id="upload">
                                        <input name="file" type="file" id="file_upload" style="padding-top: 5px;">
                                        <input type="submit" value="提交" >
                                    </form>
                                </div>
                            </div>
                            <div class="form-group toggleShow">
                                <label for="video_upload" class="control-label col-md-3 col-sm-3 col-xs-12">视频上传</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <form action="./upload?type=3" enctype="multipart/form-data" method="post" id="upload">
                                        <input name="file" type="file" style="padding-top: 5px;" value="<?php echo ($_SERVER['SCRIPT_NAME']); echo ($record["content"]); ?>">
                                        <input type="submit" value="提交" >
                                    </form>
                                </div>
                            </div><?php endif; endif; ?>


                    <div calss="btns col-xs-12" style="text-align: center;margin-top: 20px;">
                        <a href="chapter_detail.html"><button class="btn btn-default btn-sm" type="button">取消</button></a>
                        <input type="hidden" name="course_id" value="<?php echo ($course_id); ?>">
                        <input type="hidden" id="content" name="content">
                        <button class="btn btn-success btn-sm" type="submit" id="save">保存</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /页面内容 -->

        <!-- 底部 -->
        <footer>
            <div class="pull-right" style="text-align:center;width: 100%;">
                后台管理系统
            </div>
            <div class="clearfix"></div>
        </footer>
        <!-- /底部 -->
    </div>
</div>

<!-- jQuery -->
<script src="/static/vendors/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="/static/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick解决移动端点击事件的偏移问题需要 -->
<script src="/static/vendors/fastclick/lib/fastclick.js"></script>
<!-- NProgress加载进度条需要 -->
<script src="/static/vendors/nprogress/nprogress.js"></script>
<!-- Custom Theme Scripts -->
<script src="/static/build/js/custom.js"></script>
<!-- web Uploader.js -->
<script src="/static/build/js/webuploader.min.js"></script>
<!-- 新增、编辑章节js -->
<script src="/static/build/js/chapter_add_edit.js"></script>
<script>
    $('#upload').submit(function () {
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            cache: false,
            data: new FormData($('#upload')[0]),
            processData: false,
            contentType: false
        }).done(function(res) {
            if (res.code != 0) {
                alert(res.msg);
            } else {
                $('#content').val(res.msg.name);
                alert('上传成功');
            }
        }).fail(function(res) {
            alert('fail');
            return false;
        });
        return false;
    })

    $("#save").click(function () {
        var data = {};
        data['chapter'] = $('#course_name').val();
        data['type'] = $('input[name="type"]:checked').val();
        data['sort'] = $('input[name="sort"]').val();
        data['detail'] = $('#detail').val();
        data['course_id'] = $('input[name="course_id"]').val();
        data['content'] = $('#content').val();
        data['id'] = "<?php echo ($record["id"]); ?>";

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: data,
            success: function (e) {
                if (e.code != 0) {
                    alert(e.msg)
                } else {
                    window.location.href = '../course_detail/list?id=' + data['course_id'];
                }
            }
        })
        return false
    })
</script>
</body>
</html>