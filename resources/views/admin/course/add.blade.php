@extends('admin.layout.index')
@section('content')

<link rel="stylesheet" href="{{asset('/admin/lib/jq-module/zyupload/zyupload-1.0.0.min.css')}}" media="all">
<script src="{{asset('/admin/lib/jquery-3.4.1/jquery-3.4.1.min.js')}}" charset="utf-8"></script>
<script src="{{asset('/admin/lib/layui-v2.5.5/layui.js')}}" charset="utf-8"></script>
<script src="{{asset('/admin/lib/jq-module/zyupload/zyupload-1.0.0.min.js')}}" charset="utf-8"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/wangeditor@latest/dist/wangEditor.min.js"></script>
<style>
    body {
        background-color: #ffffff;
    }
    .zyupload{
        margin-left: 0;
        height: 320px !important;
    }
    .course-img{
        position: absolute;
        left: 660px;
        top:0;
    }
    .course-img img{
        height: 160px;
    }
</style>
<body>
<div class="layui-form layuimini-form">
    <div class="layui-form-item">
        <label class="layui-form-label required">课程标题</label>
        <div class="layui-input-block">
            <input type="text" name="course_title" lay-verify="required" lay-reqtext="课程标题不能为空" placeholder="请输入课程标题" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">课程分类</label>
        <div class="layui-input-block">
            <select name='cat_id'>
                <option value="">请选择</option>
                @foreach($cat_list as $cat)
                <option value="{{$cat->id}}">{{$cat->cat_name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">课程图片</label>
        <div class="layui-input-block">
            <div id="zyupload" class="zyupload"></div>
            <div class="course-img"></div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">课程原价</label>
        <div class="layui-input-block">
            <input type="text" name="ori_price" lay-verify="required" lay-reqtext="课程原价不能为空" placeholder="请输入课程原价" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">课程促销价</label>
        <div class="layui-input-block">
            <input type="text" name="pro_price" lay-verify="required" lay-reqtext="课程促销价不能为空" placeholder="请输入课程促销价" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">是否推荐</label>
        <div class="layui-input-block">
            <input type="radio" name="is_special" value="1" title="是" >
            <input type="radio" name="is_special" value="0" title="否" checked="">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">是否上架</label>
        <div class="layui-input-block">
            <input type="radio" name="is_on" value="1" title="是" checked="">
            <input type="radio" name="is_on" value="0" title="否" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">课程介绍</label>
        <div class="layui-input-block">
            <div id='intro'></div>
        </div>
    </div>


    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="saveBtn">确认保存</button>
        </div>
    </div>
</div>
<script>

      const E = window.wangEditor
      const editor = new E("#intro")
      editor.config.uploadImgServer = '/admin/course/upIntroImg'
      editor.config.uploadFileName = 'file'
      editor.create()

    // 初始化插件
    $("#zyupload").zyUpload({
        width: "650px",                 // 宽度
        height: "400px",                 // 宽度
        itemWidth: "140px",                 // 文件项的宽度
        itemHeight: "115px",                 // 文件项的高度
        url: "/admin/course/upload",  // 上传文件的路径
        fileType: ["jpg", "png", "jpeg"],// 上传文件的类型
        fileSize: 51200000,                // 上传文件的大小
        multiple: true,                    // 是否可以多个文件上传
        dragDrop: true,                    // 是否可以拖动上传文件
        tailor: true,                    // 是否可以裁剪图片
        del: true,                    // 是否可以删除文件
        finishDel: false,  				  // 是否在上传文件完成后删除预览
        /* 外部获得的回调接口 */

        onSuccess: function (file, response) {          // 文件上传成功的回调方法
            res = JSON.parse(response)

            //console.info(response);
            $("#uploadInf").append("<p>上传成功，文件地址是：" + res.url + "</p>");
            $(".course-img").html('<img src="'+res.url+'" />');
            $("#uploadInf").append("<input type='hidden' name='course_img' value='"+res.url+"' >");
        }
    });

    layui.use(['form'], function () {
        var form = layui.form,
            layer = layui.layer,
            $ = layui.$;

        //监听提交
        form.on('submit(saveBtn)', function (data) {
            /* var index = layer.alert(JSON.stringify(data.field), {
                title: '最终的提交信息'
            }, function () {

                // 关闭弹出层
                layer.close(index);

                var iframeIndex = parent.layer.getFrameIndex(window.name);
                parent.layer.close(iframeIndex);

            }); */

            //获取富文本的内容
            data.field['intro'] = editor.txt.html();

            $.ajaxSetup({
                headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'}
            });
            $.post('/admin/course',data.field,function(res){
                if(res.status == 'success'){
                    layer.msg(res.msg, function () {
                         var iframeIndex = parent.layer.getFrameIndex(window.name);
                         parent.layer.close(iframeIndex);
                         parent.location.reload();
                     });

                }else{
                    layer.alert(res.msg, {
                        title: '上传提示'
                    });
                }
            })

            return false;
        });

    });
</script>
</body>
@endsection
