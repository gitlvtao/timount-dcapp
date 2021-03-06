<?php /*a:1:{s:56:"D:\DCAPP\application\blocks\view\data\product_index.html";i:1542091872;}*/ ?>
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
        <!-- datatables页 -->
        <div class="layui-card-body">
            <div style="padding-bottom: 10px;">
                <button class="layui-btn layui-btn-sm" id="btn-add">数据绑定</button>
            </div>
            <table id="layui-datatables" lay-filter="layui-datatables"></table>
        </div>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="operate-state">
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
            url: '<?php echo url("blocks/data/index"); ?>',
            where: {
                bid: '<?php echo htmlentities($bid); ?>',
            },
            method: 'post',
            page: true,
            loading: true,
            limit: 20,
            height: 'full-170',
            cols: [[
                {type: 'numbers'},
                {field: 'block_title', minWidth: 140, title: '模块名称'},
                {field: 'product_title', minWidth: 180, title: '产品名称'},
                {field: 'data_create_time', width: 130, align: 'center', title: '创建日期', templet: function(d) {
                    return new Date(d.data_create_time*1000).Format("yyyy-MM-dd");
                }},
                {field: 'data_sort', width: 80, align: 'center', title: '排序', templet: function (d) {
                    return '<input name="sort" data-did="' + d.data_id +'" class="layui-input fuk-list-sort" value="' + d.data_sort + '" size="10"/>';
                }},
                {templet: '#operate-state', width: 250, align: 'center', title: '操作'}
            ]],
            done: function () {
                layer.closeAll('loading');
            }
        });
        // 绑定事件
        $('#btn-add').click(function () {
            layer.open({
                type: 2,
                title: '批量添加产品',
                content: '<?php echo url("blocks/data/bindproduct"); ?>?bid=<?php echo htmlentities($bid); ?>',
                area: layerArea(), //计算页面大小
            });
        });
        //监听单元格事件
        table.on('tool(layui-datatables)', function(obj){
            var data = obj.data;
            var layEvent = obj.event;
            // 处理事件
            if (layEvent == "del") {
                layer.confirm('你确定要删除该数据?', {icon: 3, title:'提示'}, function(index){
                    layer.load(1);
                    $.post("<?php echo url('blocks/data/delete'); ?>",{did:data.data_id}, function(result){
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
            }
        });
        batchBrokerArea = function (broker) {
            // 根据ID获取信息
            if (broker && broker != null && broker != "") {
                layer.load(1);
                $.post("<?php echo url('blocks/data/batch'); ?>",{bid:'<?php echo htmlentities($bid); ?>',selectIds:broker}, function(result){
                    layer.closeAll('loading');
                    if (result.code == 0) {
                        tableIns.reload();
                        layer.msg("操作成功",{icon: 1});
                    } else {
                        layer.msg(result.msg,{icon: 2});
                    }
                },"json");
            }
        };

        // 排序
        $('body').on('change','.fuk-list-sort',function() {
            var did = $(this).data('did');
            var sort = $(this).val();
            $.post('<?php echo url("blocks/data/sort"); ?>',{did:did, sort:sort},function(result){
                if(result.code == 0){
                    tableIns.reload();
                    layer.msg("操作成功",{icon: 1});
                }else{
                    layer.msg(result.msg,{icon: 2});
                }
            })
        });
    });
</script>
</body>
</html>