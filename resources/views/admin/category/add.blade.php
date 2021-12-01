@extends('admin.layout.index')
@section('content')
<style>
    body {
        background-color: #ffffff;
    }
</style>
<body>
<div class="layui-form layuimini-form">
    <div class="layui-form-item">
        <label class="layui-form-label required">分类名称</label>
        <div class="layui-input-block">
            <input type="text" name="cat_name" lay-verify="required" lay-reqtext="分类名称不能为空" placeholder="请输入分类名称" value="" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label required">排序</label>
        <div class="layui-input-block">
            <input type="text" name="cat_sort" lay-verify="required" lay-reqtext="排序不能为空" placeholder="请输入1-100" value="" class="layui-input">
            <tip>排序的值越小越靠前</tip>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">显示状态</label>
        <div class="layui-input-block">
            <input type="radio" name="cat_status" value="1" title="显示" checked="">
            <input type="radio" name="cat_status" value="0" title="隐藏">
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="saveBtn">确认保存</button>
        </div>
    </div>
</div>
<script>


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

            $.ajaxSetup({
                headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'}
            });
            $.post('/admin/category',data.field,function(res){
                if(res.status == 'success'){
                    layer.msg(res.msg, function () {
                         var iframeIndex = parent.layer.getFrameIndex(window.name);
                         parent.layer.close(iframeIndex);
                         parent.location.reload();
                     });

                }else{
                    layer.alert(res.msg, {
                        title: '提示'
                    });
                }
            })

            return false;
        });

    });
</script>
</body>
@endsection
