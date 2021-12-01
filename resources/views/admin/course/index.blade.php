@extends('admin.layout.index')
@section('content')
<style>
    .layui-table-cell{
        height: 60px;
        line-height: 60px;
    }
    .layui-table td{
        height: 60px;
        line-height: 60px;
    }
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">



        <script type="text/html" id="toolbarDemo">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-normal layui-btn-sm data-add-btn" lay-event="add"> 添加 </button>
                <button class="layui-btn layui-btn-sm layui-btn-danger data-delete-btn" lay-event="delete"> 删除 </button>
            </div>
        </script>

        <table class="layui-hide" id="currentTableId" lay-filter="currentTableFilter"></table>

        <script type="text/html" id="currentTableBar">
         <a class="layui-btn layui-btn-normal layui-btn-xs data-count-edit" lay-event="catlog">课程目录</a>
            <a class="layui-btn layui-btn-normal layui-btn-xs data-count-edit" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-xs layui-btn-danger data-count-delete" lay-event="delete">删除</a>
        </script>
        <script type="text/html" id="imgShow">
            <img src="@{{d.course_img}}" style="height: 60px;" />
        </script>


    </div>
</div>

<script>
    layui.use(['form', 'table'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table;

        table.render({
            elem: '#currentTableId',
            url: '/admin/course/get_course',
            toolbar: '#toolbarDemo',
            defaultToolbar: ['filter', 'exports', 'print', {
                title: '提示',
                layEvent: 'LAYTABLE_TIPS',
                icon: 'layui-icon-tips'
            }],
            cols: [[
                {type: "checkbox", width: 50},
                {field: 'id', width: 80, title: 'ID', sort: true},
                {field: 'course_title', width: 240, title: '课程标题'},
                {field: 'cat_name', width: 120, title: '课程分类'},
                {field: 'course_img', width: 150, title: '课程图片',templet:"#imgShow"},
                {field: 'ori_price', title: '原价', width: 100},
                {field: 'pro_price', title: '促销价', width: 100},
                {title: '操作', minWidth: 150, toolbar: '#currentTableBar', align: "center"}
            ]],
            limits: [10, 15, 20, 25, 50, 100],
            limit: 2,
            page: true,
            skin: 'line'
        });

        // 监听搜索操作
        form.on('submit(data-search-btn)', function (data) {
            var result = JSON.stringify(data.field);
            layer.alert(result, {
                title: '最终的搜索信息'
            });

            //执行搜索重载
            table.reload('currentTableId', {
                page: {
                    curr: 1
                }
                , where: {
                    searchParams: result
                }
            }, 'data');

            return false;
        });

        /**
         * toolbar监听事件
         */
        table.on('toolbar(currentTableFilter)', function (obj) {
            if (obj.event === 'add') {  // 监听添加操作
                var index = layer.open({
                    title: '添加课程',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: '/admin/course/create',
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
            } else if (obj.event === 'delete') {  // 监听删除操作
                var checkStatus = table.checkStatus('currentTableId')
                    , data = checkStatus.data;
                layer.alert(JSON.stringify(data));
            }
        });

        //监听表格复选框选择
        table.on('checkbox(currentTableFilter)', function (obj) {
            console.log(obj)
        });

        table.on('tool(currentTableFilter)', function (obj) {
            var data = obj.data;
            if (obj.event === 'edit') {

                var index = layer.open({
                    title: '编辑课程',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: '/admin/course/'+data.id+'/edit',
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
                return false;
            } else if (obj.event === 'delete') {
                layer.confirm('真的删除么', function (index) {

                    $.ajaxSetup({
                        headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'}
                    });
                    $.ajax({
                        type:'DELETE',
                        data:data,
                        url: "/admin/course/"+data.id,
                        success:function(res){
                            if(res.status == 'success'){
                                layer.msg(res.msg);
 		                        obj.del();
                    	        layer.close(index);
                            }else{
                                layer.alert(res.msg, {
                                    title: '提示'
                                });
                            }
                        }
                    });
                });
            }else if (obj.event === 'catlog') {
	            var index = layer.open({
                    title: '课程目录',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: '/admin/course/catlog/'+data.id,
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
                return false;
          }
        });

    });
</script>

</body>
@endsection
