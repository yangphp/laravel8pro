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
        <label class="layui-form-label required">优惠券名称</label>
        <div class="layui-input-block">
            <input type="text" name="coupon_name" lay-verify="required" lay-reqtext="优惠券名称不能为空" placeholder="请输入优惠券名称" value="" class="layui-input">
        </div>
    </div>
	<div class="layui-form-item">
        <label class="layui-form-label required">优惠券金额</label>
        <div class="layui-input-block">
            <input type="text" name="coupon_fee" lay-verify="required" lay-reqtext="金额不能为空" placeholder="请输入金额" value="" class="layui-input">
        </div>
    </div>
	<div class="layui-form-item">
        <label class="layui-form-label required">满多少可用</label>
        <div class="layui-input-block">
            <input type="text" name="total_fee" lay-verify="required" lay-reqtext="金额不能为空" placeholder="请输入金额" value="" class="layui-input">
        </div>
    </div>
	<div class="layui-form-item">
        <label class="layui-form-label required">有效期</label>
        <div class="layui-input-block">
            <input type="text" name="from_to" id="lay_date" lay-verify="required"  value="" class="layui-input">
        </div>
    </div>

   

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="saveBtn">确认保存</button>
        </div>
    </div>
</div>
<script>


    layui.use(['form','laydate'], function () {
        var form = layui.form,
            layer = layui.layer,
            $ = layui.$;
			
		var laydate = layui.laydate;
		laydate.render({
			elem:'#lay_date',
			range:true
		})

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
            $.post('/admin/coupon',data.field,function(res){
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
