@extends('admin.layout.index')
@section('content')

<link rel="stylesheet" href="{{asset('/admin/lib/jq-module/zyupload/zyupload-1.0.0.min.css')}}" media="all">
<script src="{{asset('/admin/lib/jquery-3.4.1/jquery-3.4.1.min.js')}}" charset="utf-8"></script>
<script src="{{asset('/admin/lib/layui-v2.5.5/layui.js')}}" charset="utf-8"></script>
<script src="{{asset('/admin/lib/jq-module/zyupload/zyupload-1.0.0.min.js')}}" charset="utf-8"></script>
<style>
    body {
        background-color: #ffffff;
    }
</style>
<body>
<div class="layui-form layuimini-form">
    <div class="layui-form-item">
        <label class="layui-form-label required">幻灯片标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" lay-verify="required" lay-reqtext="幻灯片标题不能为空" placeholder="请输入幻灯片标题" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">跳转地址</label>
        <div class="layui-input-block">
            <input type="text" name="jumpurl" lay-verify="required" lay-reqtext="跳转地址不能为空" placeholder="请输入跳转地址" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">排序</label>
        <div class="layui-input-block">
            <input type="text" name="swiper_sort" lay-verify="required" lay-reqtext="排序不能为空" placeholder="请输入1-100" value="" class="layui-input">
            <tip>排序的值越小越靠前</tip>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">显示状态</label>
        <div class="layui-input-block">
            <input type="radio" name="swiper_status" value="1" title="显示" checked="">
            <input type="radio" name="swiper_status" value="0" title="隐藏">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">幻灯片图片</label>
        <div class="layui-input-block">
            <div id="zyupload" class="zyupload"></div>
        </div>
    </div>


    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="saveBtn">确认保存</button>
        </div>
    </div>
</div>
<script>

    // 初始化插件
    $("#zyupload").zyUpload({
        width: "650px",                 // 宽度
        height: "400px",                 // 宽度
        itemWidth: "140px",                 // 文件项的宽度
        itemHeight: "115px",                 // 文件项的高度
        url: "/admin/swiper/upload",  // 上传文件的路径
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
            $("#uploadInf").append("<input type='hidden' name='imgurl' value='"+res.url+"' >");
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
            var imgurl = data.field.imgurl;
            if(undefined==imgurl){
                layer.alert('请上传幻灯片图片', {
                    title: '上传提示'
                });
                return false
            }
            $.ajaxSetup({
                headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'}
            });
            $.post('/admin/swiper',data.field,function(res){
                if(res.status == 'success'){
                    layer.msg(res.msg, function () {
                         var iframeIndex = parent.layer.getFrameIndex(window.name);
                         parent.layer.close(iframeIndex);
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
