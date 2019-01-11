<?php /*a:1:{s:52:"D:\DCAPP\application\admin\view\adminauth\index.html";i:1540187376;}*/ ?>
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
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <title></title>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-card">
            <div class="layui-btn-group">
                <button class="layui-btn layui-btn-warm layui-btn-sm" id="btn-expand">展开</button>
                <button class="layui-btn layui-btn-warm layui-btn-sm" id="btn-fold">折叠</button>
                <button class="layui-btn layui-btn-sm" id="btn-add">新增</button>
            </div>
            <table id="tree-table" class="layui-table" lay-filter="tree-table"></table>
        </div>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="operate-state">
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">修改</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    {{#  if(d.status == 1){ }}
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="forbid">禁用</a>
    {{#  } else { }}
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="reuse">启用</a>
    {{#  } }}
</script>
<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/tools.js"></script>
<script>
    var renderTable;
    //
    layui.config({
        base: '/static/layuiadmin/modules/'
    }).extend({
        treetable: 'treetable-lay/treetable'
    }).use(['table', 'treetable'], function () {
        var $ = layui.jquery;
        var table = layui.table;
        var treetable = layui.treetable;
        // 渲染表格
        renderTable = function () {
            layer.load(2);
            treetable.render({
                treeColIndex: 1,
                treeSpid: -1,
                treeIdName: 'id',
                treePidName: 'pid',
                elem: '#tree-table',
                url: '<?php echo url("index"); ?>',
                page: false,
                cols: [[
                    {type: 'numbers'},
                    {field: 'name', minWidth: 200, title: '菜单名称'},
                    {field: 'url', minWidth: 200, title: '菜单链接'},
                    {field: 'auth_menu_status', width: 100,  title: '菜单状态', align: 'center', templet: function (d) {
                        // 显示的名称
                        var _name = "";

                        if("0" == d.auth_menu_status){
                            _name = "隐藏";
                        }

                        if("1" == d.auth_menu_status){
                            _name = "显示";
                        }

                        // 显示的颜色
                        var _css = "";
                        if (d.auth_menu_status == 1) {
                            _css = "layui-bg-orange";
                        } else {
                            _css = "layui-bg-gray";
                        }
                        //返回值
                        return '<span class="layui-badge ' + _css + '">' + _name + '</span>';
                    }
                    },
                    {field: 'status', width: 80,  title: '状态', align: 'center', templet: function (d) {
                        // 显示的名称
                        var _name = "";

                        if("1" == d.status){
                            _name = "启用";
                        }

                        if("0" == d.status){
                            _name = "禁用";
                        }

                        // 显示的颜色
                        var _css = "";
                        if (d.status == 1) {
                            _css = "layui-bg-green";
                        } else {
                            _css = "layui-bg-red";
                        }
                        //返回值
                        return '<span class="layui-badge ' + _css + '">' + _name + '</span>';
                    }
                    },
                    {field: 'orderIndex', width: 80, align: 'center', title: '排序号'},
                    {templet: '#operate-state', width: 200, align: 'center', title: '操作', fixed: 'right'},
                ]],
                done: function () {
                    layer.closeAll('loading');
                }
            });
        };
        // 渲染表格
        renderTable();
        $('#btn-expand').click(function () {
            treetable.expandAll('#tree-table');
        });
        $('#btn-fold').click(function () {
            treetable.foldAll('#tree-table');
        });
        $('#btn-add').click(function () {
            layer.open({
                type: 2,
                title: '新增菜单',
                content: '<?php echo url("addupdate"); ?>',
                area: layerArea(), //计算页面大小
            });
        });
        // 注册事件
        table.on('tool(tree-table)', function(obj){
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event;
            // 根据data和不同的事件处理
            if (layEvent == "del") {
                // 删除操作
                layer.confirm('你确定要删除此菜单?', {icon: 3, title:'提示'}, function(index){
                    // 执行删除操作
                    $.post("<?php echo url('status'); ?>",{aid:data.id, status: -1}, function(result){
                        layer.close(index);
                        if (result.code ==  0) {
                            renderTable();
                            layer.msg("操作成功",{icon: 1});
                        } else {
                            layer.msg(result.msg,{icon: 2});
                        }
                    },"json");
                });
            } else if (layEvent == "forbid") {
                // 禁用
                layer.confirm('你确定要禁用该菜单?', {icon: 3, title:'提示'}, function(index){
                    $.post("<?php echo url('status'); ?>",{aid:data.id, status: 0}, function(result){
                        layer.close(index);
                        if (result.code ==  0) {
                            renderTable();
                            layer.msg("操作成功",{icon: 1});
                        } else {
                            layer.msg(result.msg,{icon: 2});
                        }
                    },"json");
                });

            } else if (layEvent == "reuse") {
                // 启用
                layer.confirm('你确定要启用该菜单?', {icon: 3, title:'提示'}, function(index){
                    $.post("<?php echo url('status'); ?>",{aid:data.id, status: 1}, function(result){
                        layer.close(index);
                        if (result.code ==  0) {
                            renderTable();
                            layer.msg("操作成功",{icon: 1});
                        } else {
                            layer.msg(result.msg,{icon: 2});
                        }
                    },"json");
                });
            } else if (layEvent == "edit") {
                layer.open({
                    type: 2,
                    title: '修改菜单',
                    content: '<?php echo url("addupdate"); ?>?aid=' + data.id,
                    area: layerArea(), //计算页面大小
                });
            }
        });
    });
</script>
</body>
</html>