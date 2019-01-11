<?php /*a:1:{s:50:"D:\DCAPP\application\reward\view\reward\index.html";i:1545112839;}*/ ?>
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
                <a href="<?php echo url('index'); ?>" class="layui-btn">显示全部</a>
                <div style="float: right">
                    <div class="layui-inline">
                        <input class="layui-input" name="key" id="key" autocomplete="off" placeholder="请输入用户手机号" style="width: 400px">
                    </div>
                    <button class="layui-btn" id="search" data-type="reload">搜索</button>
                    <button class="layui-btn" id="query" data-type="reload">查询</button>
                </div>
                <table class="layui-table" id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="action">
    <a class="layui-btn layui-btn-xs layui-btn" lay-event="info">查看详情</a>
    <a class="layui-btn layui-btn-xs layui-btn-warm" id="sendCode" lay-event="sendCode">发送验证码</a>
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
                {field: 'pull_phone', title: '用户账号',align: 'center', minWidth: 120,templet: '#open'},
                {field: 'pull_money', title: '已奖励金额',align: 'center', minWidth: 160,templet: '#open'},
                {field: 'pull_code', title: '领奖验证码',align: 'center', minWidth: 160,templet: '#open'},
                {minWidth: 200,title: '操作', align: 'center', toolbar: '#action',fixed:'right'}
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

        //查询
        $('#query').on('click', function () {
            layer.open({
                type: 2,
                title: '奖励详情',
                content: '<?php echo url("info"); ?>',
                area: layerArea(), //计算页面大小
            });
        });

        table.on('tool(list)', function(obj) {
            var data = obj.data;
            if (obj.event === 'info'){
                layer.open({
                    type: 2,
                    title: '奖励详情',
                    content: '<?php echo url("rewardInfo"); ?>?phone='+data.pull_phone,
                    area: layerArea(), //计算页面大小
                });
            }else if(obj.event === 'sendCode'){
                layer.confirm('确定要发送验证码吗？', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    console.log(data.pull_phone);
                    $.post("<?php echo url('sendCode'); ?>",{phone:data.pull_phone},function(res){
                        layer.close(loading);
                        if(res.code===1){
                            layer.msg(res.msg,{time:1000,icon:1});
                            $("#sendCode").attr("style","disable");
                            tableIn.reload();
                        }else{
                            layer.msg('验证码发送失败！',{time:1000,icon:2});
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