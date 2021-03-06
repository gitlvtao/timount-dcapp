<?php /*a:1:{s:52:"D:\DCAPP\application\article\view\article\index.html";i:1542338347;}*/ ?>
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
                        <label class="layui-form-label">位置名称</label>
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
                <button class="layui-btn layui-btn-sm" id="btn-add">新增文章</button>
            </div>
            <table id="layui-datatables" lay-filter="layui-datatables"></table>
        </div>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="operate-state">
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="comment">评论管理</a>
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
            url: '<?php echo url("article/article/index"); ?>',
            method: 'post',
            page: true,
            loading: true,
            limit: 20,
            height: 'full-170',
            cols: [[
                {type: 'numbers'},
                {field: 'column_name', minWidth: 140, title: '栏目'},
                {field: 'article_title', minWidth: 180, title: '文章标题'},
                {field: 'article_comment_num', minWidth: 180, title: '评论数'},
                {field: 'article_attr', width: 100,  title: '是否推荐', align: 'center', templet: function (d) {
                    // 显示的名称
                    var _show = "";

                    if("1" == d.article_attr){
                        _show = "checked";
                    }
                    //返回值
                    return '<input type="checkbox" name="recom" value="' + d.article_id + '" lay-skin="switch" lay-filter="recom" lay-text="是|否" ' + _show + '>';
                }},
                {field: 'article_create_time', width: 130, align: 'center', title: '创建日期', templet: function(d) {
                    return new Date(d.article_create_time*1000).Format("yyyy-MM-dd");
                }},
                {field: 'article_sort', width: 80, align: 'center', title: '排序', templet: function (d) {
                    return '<input name="sort" data-aid="' + d.article_id +'" class="layui-input fuk-list-sort" value="' + d.article_sort + '" size="10"/>';
                }},
                {templet: '#operate-state', width: 250, align: 'center', title: '操作'}
            ]],
            done: function () {
                layer.closeAll('loading');
            }
        });
        // 新增事件
        $('#btn-add').click(function () {
            var add = layer.open({
                type: 2,
                title: '新增文章',
                content: '<?php echo url("article/article/updateadd"); ?>',
                area: ['100%', '100%'], //计算页面大小
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
                    title: '修改文章信息',
                    content: '<?php echo url("article/article/updateadd"); ?>?aid='+data.article_id,
                    area: ['100%', '100%'], //计算页面大小
                });
            } else if (layEvent == "del") {
                layer.confirm('你确定要删除该文章?', {icon: 3, title:'提示'}, function(index){
                    layer.load(1);
                    $.post("<?php echo url('article/article/delete'); ?>",{aid:data.article_id}, function(result){
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
            }  else if (layEvent == "comment") {
                layer.open({
                    type: 2,
                    title: '评论管理 - '+data.article_title,
                    content: '<?php echo url("article/comment/index"); ?>?aid='+data.article_id,
                    area: layerArea(), //计算页面大小
                });
            }
        });

        // 排序
        $('body').on('change','.fuk-list-sort',function() {
            var aid = $(this).data('aid');
            var sort = $(this).val();
            $.post('<?php echo url("article/article/sort"); ?>',{aid:aid, sort:sort},function(result){
                if(result.code == 0){
                    tableIns.reload();
                    layer.msg("操作成功",{icon: 1});
                }else{
                    layer.msg(result.msg,{icon: 2});
                }
            })
        });

        // 是否推荐
        form.on('switch(recom)', function(obj){
            loading =layer.load(1, {shade: [0.1,'#fff']});
            var id = this.value;
            var recom = obj.elem.checked===true ? 1 : 0;
            $.post('<?php echo url("article/article/recom"); ?>',{'aid':id,'recom':recom},function (result) {
                layer.close(loading);
                if(result.code == 0){
                    tableIns.reload();
                    layer.msg("操作成功",{icon: 1});
                }else{
                    layer.msg(result.msg,{icon: 2});
                    return false;
                }
            })
        });
    });
</script>
</body>
</html>