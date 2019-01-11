<?php /*a:1:{s:50:"D:\DCAPP\application\column\view\column\index.html";i:1543484693;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <!-- 下面是定义标签库 -->
    <!-- 下面是定义通用的meta头信息 -->
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="0">
    <!-- 下面是定义通用的变量信息 -->
    <link rel="stylesheet" type="text/css" href="/static/layuiadmin/layui/css/layui.css" /><link rel="stylesheet" type="text/css" href="/static/layuiadmin/style/admin.css" />
    <title></title>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-card">
            <div class="layui-btn-group">
                <fieldset class="layui-elem-field layui-field-title">
                    <legend>分类管理</legend>
                </fieldset>
            </div>
            <div class="layui-btn-table">
                <a href="<?php echo url('columnAdd'); ?>" class="layui-btn layui-btn-normal" style="float:left;"><i class="fa fa-plus" aria-hidden="true"></i>添加分类</a>
                <a href="<?php echo url('index'); ?>" class="layui-btn">显示全部</a>
                <div style="float: right">
                    <div class="layui-inline">
                        <input class="layui-input" name="key" id="key" autocomplete="off" placeholder="请输入分类名称" style="width: 400px">
                    </div>
                    <button class="layui-btn" id="search" data-type="reload">搜索</button>
                </div>
                <table class="layui-table" id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="sort">
    <input name="{{d.column_id}}" data-id="{{d.column_id}}" class="list_order layui-input" value=" {{d.column_sort}}" size="10"/>
</script>
<script type="text/html" id="index">
    <img src="{{d.column_thumb}}">
</script>
<script type="text/html" id="info">
    <img src="{{d.column_banner}}">
</script>
<script type="text/html" id="action">
    <!--{{#  if(d.preview_url != 'null'){ }}
    <a href="{{d.preview_url}}" class="layui-btn layui-btn-normal layui-btn-xs " target="_blank">页面预览</a>
    {{#  }else{ }}
    <a href="#" class="layui-btn layui-btn-normal layui-btn-xs">无法预览</a>
    {{#  } }}-->

    <a href="<?php echo url('columnEdit'); ?>?column_id={{d.column_id}}" class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>

</script>
<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/tools.js"></script>
<script>
    layui.use(['table','form'], function() {
        var table = layui.table,form = layui.form,$ = layui.jquery;
        var tableIn = table.render({
            id: 'ad',
            elem: '#list',
            url: '<?php echo url("index"); ?>',
            method: 'post',
            page:true,
            cols: [[
                {field: 'column_name', title: '分类名称',align: 'center', width: 160,templet: '#media_balance'},
                {field: 'column_summary', title: '分类介绍',align: 'center', width: 500,templet: '#open'},
                {field: 'column_thumb', title: '缩略图',align: 'center', width: 200,templet: '#index'},
                {field: 'column_banner', title: '详情图',align: 'center', width: 200,templet: '#info'},
                // {field: 'column_sort', title: '排序',align: 'center', width: 100,templet: '#sort',sort:true},
                {width: 200,title: '操作', align: 'center', toolbar: '#action',fixed:'right'}
            ]],
            limit:10
        });

        //搜索
        $('#search').on('click', function () {
            var key = $('#key').val();
            if ($.trim(key) === '') {
                layer.msg('<?php echo lang("pleaseEnter"); ?>关键字！', {icon: 0});
                return;
            }
            tableIn.reload({
                where: {key: key}
                ,page: {
                    curr: 1 //重新从第 1 页开始
                }
            });
        });

        table.on('tool(list)', function(obj) {
            var data = obj.data;
            if (obj.event === 'del'){
                layer.confirm('您确定要删除该分类吗？', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    $.post("<?php echo url('columnDel'); ?>",{column_id:data.column_id},function(res){
                        layer.close(loading);
                        if(res.code===1){
                            layer.msg(res.msg,{time:1000,icon:1});
                            tableIn.reload();
                        }else{
                            layer.msg('操作失败！',{time:1000,icon:2});
                        }
                    });
                    layer.close(index);
                });
            }else if(obj.event === 'edit'){

            }
        });

        $('body').on('blur','.list_order',function() {
            var column_id = $(this).attr('data-id');
            var sort = $(this).val();
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.post('<?php echo url("sort"); ?>',{column_id:column_id,column_sort:sort},function(res){
                layer.close(loading);
                if(res.code === 1){
                    layer.msg(res.msg, {time: 1000, icon: 1});
                    tableIn.reload();
                }else{
                    layer.msg(res.msg,{time:1000,icon:2});
                }
            })
        });

        /*$('#delAll').click(function(){
            layer.confirm('确认要删除选中的广告吗？', {icon: 3}, function(index) {
                layer.close(index);
                var checkStatus = table.checkStatus('ad'); //test即为参数id设定的值
                var ids = [];
                $(checkStatus.data).each(function (i, o) {
                    ids.push(o.ad_id);
                });
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.post("<?php echo url('delall'); ?>", {ids: ids}, function (data) {
                    layer.close(loading);
                    if (data.code === 1) {
                        layer.msg(data.msg, {time: 1000, icon: 1});
                        tableIn.reload();
                    } else {
                        layer.msg(data.msg, {time: 1000, icon: 2});
                    }
                });
            });
        })*/
    })
</script>
</body>
</html>