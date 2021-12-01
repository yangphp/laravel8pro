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
		
		<script type="text/html" id="is_on">
           <input type="checkbox" name="@{{d.id}}"  value="@{{d.is_on}}" lay-skin="switch" lay-text="上架|下架" lay-filter="isOnFilter"  @{{ d.is_on==1?'checked':''}}/>
        </script>
		
        <script type="text/html" id="currentTableBar">
            <a class="layui-btn layui-btn-normal layui-btn-xs data-count-edit" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-xs layui-btn-danger data-count-delete" lay-event="delete">删除</a>
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
            url: '/admin/coupon/get_coupons',
            toolbar: '#toolbarDemo',
            defaultToolbar: ['filter', 'exports', 'print', {
                title: '提示',
                layEvent: 'LAYTABLE_TIPS',
                icon: 'layui-icon-tips'
            }],
            cols: [[
                {type: "checkbox", width: 50},
                {field: 'id', width: 80, title: 'ID', sort: true},
                {field: 'coupon_name', width: 180, title: '优惠券名称'},
                {field: 'coupon_fee', width: 150, title: '优惠券金额'},
				{field: 'total_fee', width: 150, title: '满多少可用'},
				{field: 'from_time', width: 130, title: '开始日期',templet:function(d){
					return layui.util.toDateString(d.from_time*1000,'yyyy-MM-dd')
				}},
				{field: 'to_time', width: 130, title: '结束日期',templet:function(d){
					return layui.util.toDateString(d.to_time*1000,'yyyy-MM-dd')
				}},
                {field: 'is_on', width: 180, title: '是否上架',templet:"#is_on"},
                {title: '操作', minWidth: 150, toolbar: '#currentTableBar', align: "center"}
            ]],
            limits: [10, 15, 20, 25, 50, 100],
            limit: 10,
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
		
		//监听切换对象
		form.on('switch(isOnFilter)',function(obj){
			if(this.checked)
			{
				is_on = 1;
			}else{
				is_on = 0;
			}
			 
			 $.ajaxSetup({
                headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'}
            });
            $.post('/admin/coupon/status',{id:this.name,is_on:is_on},function(res){
   
            })
		});

        /**
         * toolbar监听事件
         */
        table.on('toolbar(currentTableFilter)', function (obj) {
            if (obj.event === 'add') {  // 监听添加操作
                var index = layer.open({
                    title: '添加优惠券',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: '/admin/coupon/create',
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
                    title: '编辑优惠券',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: '/admin/coupon/'+data.id+'/edit',
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
                        url: "/admin/coupon/"+data.id,
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
            }
        });

    });
</script>

</body>
@endsection
