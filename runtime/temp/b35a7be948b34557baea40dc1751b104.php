<?php /*a:1:{s:46:"D:\DCAPP\application\user\view\user\index.html";i:1545042321;}*/ ?>
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
                    <legend>用户管理</legend>
                </fieldset>
            </div>
            <div class="layui-btn-table">
                <!--<a href="#" class="layui-btn layui-btn-normal" style="float:left;"><i class="fa fa-plus" aria-hidden="true"></i>添加用户</a>-->
                <a href="<?php echo url('index'); ?>" class="layui-btn">显示全部</a>
                <a href="#" class="layui-btn" id="phpExcel">导出excel</a>
                <div style="float: right">
                    <div class="layui-inline">
                        <input class="layui-input" name="key" id="key" autocomplete="off" placeholder="请输入用户昵称或手机号" style="width: 400px">
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
    {{#  if(d.user_status === 1){ }}
    <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">冻结</a>
    {{# }else{ }}
    <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="register">已冻结</a>
    {{#  } }}
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
                {field: 'user_id', title: '用户ID',align: 'center', width: 160,templet: '#media_balance'},
                {field: 'user_mobile', title: '受邀人',align: 'center', width: 120,templet: '#open'},
                {field: 'count', title: '用户下级人数',align: 'center', width: 120,templet: '#open'},
                {field: 'platform', title: '邀请人(上级)',align: 'center', width: 120,templet: '#open'},
                {field: 'user_nickname', title: '用户昵称',align: 'center', width: 200,templet: '#open'},
                {field: 'user_sex', title: '用户性别',align: 'center', width: 120,templet: '#open'},
                {field: 'user_age', title: '用户年龄',align: 'center', width: 120,templet: '#open'},
                {field: 'user_reg_time', title: '用户注册时间',align: 'center', width: 160,templet: '#open'},
                {field: 'user_login_time', title: '最后一次登录时间',align: 'center', width: 160,templet: '#open'},
                {width: 120,title: '操作', align: 'center', toolbar: '#action',fixed:'right'}
            ]],
            limit:10
        });

        //搜索
        $('#search').on('click', function () {
            var key = $('#key').val();
            if ($.trim(key) === '') {
                layer.msg('请输入关键字！', {icon: 0});
                return;
            }
            tableIn.reload({
                where: {key: key}
                ,page: {
                    curr: 1 //重新从第 1 页开始
                }
            });
        });

        //导出表格
        $("#phpExcel").on('click',function (res) {
            var startTime = '我是时间 ';
            $("#phpExcel").attr("href", "<?php echo url('phpExcel'); ?>?time=" + startTime);
        });

        table.on('tool(list)', function(obj) {
            var data = obj.data;
            if (obj.event === 'del'){
                layer.confirm('您确定要冻结该用户吗？', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    $.post("<?php echo url('delUser'); ?>",{id:data.user_id,type:1},function(res){
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
            }else if(obj.event === 'register'){
                layer.confirm('确定恢复该用户吗？', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    $.post("<?php echo url('delUser'); ?>",{id:data.user_id,type:2},function(res){
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
            }
        });

    })
</script>
</body>
</html>