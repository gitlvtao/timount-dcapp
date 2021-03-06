<?php /*a:1:{s:49:"D:\DCAPP\application\blocks\view\block\index.html";i:1541670992;}*/ ?>
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
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" id="search-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">模块名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" placeholder="模糊搜索" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-sm" lay-submit lay-filter="layui-datatables-search">搜索</button>
                        <button type="reset" class="layui-btn layui-btn-sm layui-btn-primary" lay-submit lay-filter="layui-datatables-reset">重置</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- datatables页 -->
        <div class="layui-card-body">
            <div style="padding-bottom: 10px;">
                <button class="layui-btn layui-btn-sm" id="btn-add">新增模块</button>
            </div>
            <table id="layui-datatables" lay-filter="layui-datatables"></table>
        </div>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="operate-state">
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="data">数据管理</a>
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">修改</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/tools.js"></script>
<script>
    var tableIns;
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table'], function(){
        var $ = layui.$,
            form = layui.form,
            table = layui.table;
        //
        layer.load(1);
        var field2 = 'role_describe';
        tableIns = table.render({
            elem: '#layui-datatables',
            url: '<?php echo url("blocks/block/index"); ?>',
            method: 'post',
            page: true,
            loading: true,
            limit: 20,
            height: 'full-170',
            cols: [[
                {type: 'numbers'},
                {field: 'block_ident', minWidth: 140, title: '模块标识'},
                {field: 'block_title', minWidth: 180, title: '模块名称'},
                {field: 'block_brief', minWidth: 180, title: '模块简要'},
                {field: 'block_create_time', width: 130, align: 'center', title: '创建日期', templet: function(d) {
                    return new Date(d.block_create_time*1000).Format("yyyy-MM-dd");
                }},
                {templet: '#operate-state', width: 250, align: 'center', title: '操作'}
            ]],
            done: function () {
                layer.closeAll('loading');
            }
        });
        // 新增事件
        $('#btn-add').click(function () {
            layer.open({
                type: 2,
                title: '新增模块',
                content: '<?php echo url("blocks/block/updateadd"); ?>',
                area: layerArea(), //计算页面大小
            });
        });
        //监听搜索
        form.on('submit(layui-datatables-search)', function(data){
            var field = data.field;
            layer.load(1);
            //执行重载
            table.reload('layui-datatables', {
                where: field,
                page: {
                    curr: 1 //重新从第 1 页开始
                }
            });
            return false;
        });
        //监听单元格事件
        table.on('tool(layui-datatables)', function(obj){
            var data = obj.data;
            var layEvent = obj.event;
            // 处理事件
            if (layEvent == "edit") {
                layer.open({
                    type: 2,
                    title: '修改模块信息',
                    content: '<?php echo url("blocks/block/updateadd"); ?>?bid='+data.block_id,
                    area: layerArea(), //计算页面大小
                });
            } else if (layEvent == "del") {
                layer.confirm('你确定要删除该广告?', {icon: 3, title:'提示'}, function(index){
                    layer.load(1);
                    $.post("<?php echo url('blocks/block/delete'); ?>",{bid:data.block_id}, function(result){
                        layer.close(index);
                        layer.closeAll('loading');
                        if (result.code == 0) {
                            tableIns.reload();
                            layer.msg("删除成功",{icon: 1});
                        } else {
                            layer.msg(result.msg,{icon: 2});
                        }
                    },"json");
                });
            } else if (layEvent == "data") {
                layer.open({
                    type: 2,
                    title: '模块数据管理',
                    content: '<?php echo url("blocks/data/index"); ?>?bid='+data.block_id,
                    area: layerArea(), //计算页面大小
                });
            }
        });
    });
</script>
</body>
</html>