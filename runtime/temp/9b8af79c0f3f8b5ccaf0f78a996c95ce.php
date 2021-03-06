<?php /*a:1:{s:51:"D:\DCAPP\application\reward\view\reward\reward.html";i:1544868453;}*/ ?>
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
                    <legend>用户奖励发放</legend>
                </fieldset>
            </div>
            <div class="layui-btn-table">
                <!--<a href="#" class="layui-btn layui-btn-normal" style="float:left;"><i class="fa fa-plus" aria-hidden="true"></i>添加用户</a>-->
                <a class="layui-btn" id="ALL">批量操作</a>
                <!--<div style="float: right">
                    <div class="layui-inline">
                        <input class="layui-input" name="key" id="key" autocomplete="off" placeholder="请输入用户手机号" style="width: 400px">
                    </div>
                    <button class="layui-btn" id="search" data-type="reload">搜索</button>
                </div>-->
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

    {{#  if(d.pull_time == 0){ }}
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="send">发放奖励</a>
    {{# }else{ }}
        <a class="layui-btn layui-btn-xs layui-btn-disabled" lay-event="">发放奖励</a>
    {{#  } }}
    {{#  if(d.pull_status == -1){ }}
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="">冻结</a>
    {{# }else{ }}
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="del">冻结</a>
    {{#  } }}
</script>
<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/tools.js"></script>
<script>
    layui.use(['table','form'], function() {
        var table = layui.table,form = layui.form,$ = layui.jquery;
        var phone = "<?php echo htmlentities($phone); ?>";
        var tableIn = table.render({
            id: 'ad',
            elem: '#list',
            url: '<?php echo url("rewardInfo"); ?>',
            method: 'post',
            where:{phone:phone},
            page:true,
            cols: [[
                {type:'checkbox'},
                {field: 'pull_phone', title: '用户账号',align: 'center', width: 120,templet: '#open'},
                {field: 'pull_money', title: '奖励金额',align: 'center', width: 120,templet: '#open'},
                {field: 'pull_platform', title: '奖励来源账号',align: 'center', width: 160,templet: '#open'},
                {field: 'type', title: '奖励类型',align: 'center', width: 120,templet: '#open'},
                {field: 'time', title: '奖励发放时间',align: 'center', width: 160,templet: '#open'},
                {width: 200,title: '操作', align: 'center', toolbar: '#action',fixed:'right'}
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
                where: {key: key,phone:phone}
                ,page: {
                    curr: 1 //重新从第 1 页开始
                }
            });
        });

        //批量操作
        $("#ALL").on('click',function(res){
            var checkStatus = table.checkStatus('ad'); //test即为参数id设定的值
            var ids = [];
            if (checkStatus.data.length <1){
                layer.alert("请选择发放的信息");
                return false;
            }
            $(checkStatus.data).each(function (i, o) {
                ids.push(o.pull_id);
            });
            $.post("<?php echo url('sendMoney'); ?>",{id:ids},function(res){
                if(res.code===1){
                    layer.msg(res.msg,{time:1000,icon:1});
                    tableIn.reload();
                }else{
                    layer.msg(res.msg,{time:2000,icon:0});
                }
            });
        });


        table.on('tool(list)', function(obj) {
            var data = obj.data;
            if (obj.event === 'send'){
                layer.confirm('确定给该用户发放奖励吗？', function(index){
                    var checkStatus = table.checkStatus('ad'); //test即为参数id设定的值
                    var ids = [];
                    console.log(checkStatus.data,'aaaa');
                    if (checkStatus.data.length <1){
                        layer.alert("请选择发放的信息");
                        return false;
                    }
                    $(checkStatus.data).each(function (i, o) {
                        ids.push(o.pull_id);
                    });

                    $.post("<?php echo url('sendMoney'); ?>",{id:ids},function(res){
                        layer.close(loading);
                        if(res.code===1){
                            layer.msg(res.msg,{time:1000,icon:1});
                            tableIn.reload();
                        }else{
                            layer.msg(res.msg,{time:2000,icon:0});
                        }
                    });
                    layer.close(index);
                });
            }else if(obj.event === 'del'){
                layer.confirm('确定删除该条奖励信息吗？', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    $.post("<?php echo url('delReward'); ?>",{id:data.pull_id},function(res){
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