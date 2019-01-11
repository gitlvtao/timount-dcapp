<?php /*a:1:{s:49:"D:\DCAPP\application\reward\view\reward\info.html";i:1544863817;}*/ ?>
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
                    <legend>奖励查询</legend>
                </fieldset>
            </div>
            <div class="layui-btn-table">
                <div style="float: left">
                    <button class="layui-btn" id="add" data-type="reload">奖励录入</button>
                </div>
                <div style="float: right">
                    <div class="layui-inline">
                        <input class="layui-input" name="key" id="key" autocomplete="off" placeholder="请输入用户手机号" style="width: 400px">
                    </div>
                    <button class="layui-btn" id="search" data-type="reload">搜索</button>
                </div>
                <table class="layui-table" id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
</div>
<!--<script type="text/html" id="action">
    {{#  if(d.pull_status == -1){ }}
        <a class="layui-btn layui-btn-xs layui-btn-disabled" lay-event="">发放奖励</a>
    {{# }else{ }}
        {{#  if(d.pull_money == 0){ }}
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="send">发放奖励</a>
        {{# }else{ }}
        <a class="layui-btn layui-btn-xs layui-btn-disabled" lay-event="">发放奖励</a>
        {{#  } }}
    {{#  } }}
    {{#  if(d.pull_status == -1){ }}
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="">冻结</a>
    {{# }else{ }}
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="del">冻结</a>
    {{#  } }}
</script>-->
<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/tools.js"></script>
<script>
    layui.use(['table','form'], function() {
        var table = layui.table,form = layui.form,$ = layui.jquery;
        var tableIn = table.render({
            id: 'ad',
            elem: '#list',
            url: '<?php echo url("info"); ?>',
            method: 'post',
            page:true,
            cols: [[
                {type:'checkbox'},
                {field: 'user_mobile', title: '用户账号',align: 'center', width: 120,templet: '#open'},
                {field: 'platform', title: '奖励来源账号',align: 'center', width: 160,templet: '#open'},
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

        $("#add").on('click',function () {
            var checkStatus = table.checkStatus('ad'); //test即为参数id设定的值
            var ids = [];

            if (checkStatus.data.length <1){
                layer.alert("请选择录入的信息");
                return false;
            }
            $(checkStatus.data).each(function (i, o) {
                if (o.platform.length<10) {
                    ids.push(o.user_mobile);
                }
            });
            $.post("<?php echo url('pull'); ?>",{data:ids},function (res) {
                if(res.code===1){
                    layer.msg(res.msg,{time:1000,icon:1});
                    tableIn.reload();
                }else{
                    layer.msg(res.msg,{time:2000,icon:0});
                }
            })
        })
        
        /*table.on('tool(list)', function(obj) {
            var data = obj.data;
            if (obj.event === 'send'){
                layer.confirm('确定给该用户发放奖励吗？', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    $.post("<?php echo url('sendMoney'); ?>",{id:data.pull_id,type:data.pull_type,platform:data.pull_phone},function(res){
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
        });*/

    })
</script>
</body>
</html>