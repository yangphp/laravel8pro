@extends('admin.layout.index')
@section('content')
    <style>
        body {
            background-color: #ffffff;
        }
    </style>

<script src="{{asset('admin/lib/jquery-3.4.1/jquery-3.4.1.min.js')}}" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="{{asset('admin/oss/style.css')}}"/>
<body>
 <div class="layui-tab">
   <ul class="layui-tab-title">
     <li class="layui-this">添加章</li>
     <li>添加节</li>
   </ul>
   <div class="layui-tab-content">
     <div class="layui-tab-item layui-show">
          <div class="layui-form layuimini-form">
              <div class="layui-form-item">
                  <label class="layui-form-label required">课程标题</label>
                  <div class="layui-input-block">
                      <input type="text" name="catlog_title" lay-verify="required" lay-reqtext="课程标题不能为空" placeholder="请输入课程标题" value="" class="layui-input">
                     
                  </div>
              </div>
              <div class="layui-form-item">
                  <div class="layui-input-block">
					  <input type="hidden" name="course_id" value="{{$course_id}}"/>
                      <button class="layui-btn layui-btn-normal" lay-submit lay-filter="saveCatlog1">确认保存</button>
                  </div>
              </div>
           </div>
     </div>
     <div class="layui-tab-item">
         <div class="layui-form layuimini-form">
             <div class="layui-form-item">
                 <label class="layui-form-label required">课程标题</label>
                 <div class="layui-input-block">
                     <input type="text" name="catlog_title" id="catlog_title" lay-verify="required" lay-reqtext="课程标题不能为空" placeholder="请输入课程标题" value="" class="layui-input">
                   
                 </div>
             </div>

             <div class="layui-form-item">
                 <label class="layui-form-label required">课程分类</label>
                 <div class="layui-input-block">
                     <select name="pid">
                         <option value="">请选择</option>
                         @foreach($chapter as $cha)
                          <option value="{{$cha->id}}">{{$cha->catlog_title}}</option>
						@endforeach
                     </select>
                 </div>
             </div>

			<div class="layui-form-item">
                 <label class="layui-form-label required">视频上传</label>
                 <div class="layui-input-block">
					 <div id="container">
						<a id="selectfiles" href="javascript:void(0);" class='btn'>选择文件</a>
						<a id="postfiles" href="javascript:void(0);" class='btn'>开始上传</a>
					</div>
					<input type="hidden" name="video_url" id="video_url" />
					<div id="ossfile"></div>
					<pre id="console"></pre>
                 </div>
             </div>


             <div class="layui-form-item">
                 <label class="layui-form-label required">是否免费</label>
                 <div class="layui-input-block">
                     <input type="radio" name="is_free" value="1" title="是" >
                     <input type="radio" name="is_free" value="0" title="否" checked="checked" >
                 </div>
             </div>

             <div class="layui-form-item">
                 <div class="layui-input-block">
					<input type="hidden" name="course_id" value="{{$course_id}}"/>
                     <button class="layui-btn layui-btn-normal" lay-submit lay-filter="saveCatlog2">确认保存</button>
                 </div>
             </div>
          </div>

     </div>

   </div>
 </div>
<script type="text/javascript" src="{{asset('admin/oss/lib/plupload-2.1.2/js/plupload.full.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/oss/upload.js')}}"></script>
<script>

    layui.use(['form','element'], function () {
        var form = layui.form,
            layer = layui.layer,
            $ = layui.$;
        //监听提交
        form.on('submit(saveCatlog1)', function (data) {

            // var index = layer.alert(JSON.stringify(data.field), {
            //     title: '最终的提交信息'
            // }, function () {

            //     // 关闭弹出层
            //     layer.close(index);

            //     var iframeIndex = parent.layer.getFrameIndex(window.name);
            //     parent.layer.close(iframeIndex);

            // });

             $.ajaxSetup({
                 headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'}
             })
            $.post('/admin/course/saveChapter',data.field,function(res){
                if(res.status=='success'){
				   layer.msg(res.msg,function(){
					  location.href='/admin/course/catlog/'+'{{$course_id}}' 
				   });
                  
                }
            });

            return false;
        });

        form.on('submit(saveCatlog2)', function (data) {
				console.log(data)
               $.ajaxSetup({
                  headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'}
              })
              $.post('/admin/course/saveVideo',data.field,function(res){
                 if(res.status=='success'){
					layer.msg(res.msg);
					$('#ossfile').html('');
				    $("#catlog_title").val('')
                 }
             });
                //location.href='/admin/course/catlog/2'
            return false;
        });

    });
</script>
</body>
@endsection
