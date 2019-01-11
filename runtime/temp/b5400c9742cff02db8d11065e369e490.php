<?php /*a:1:{s:55:"D:\DCAPP\application\ads\view\positions\update_add.html";i:1542340452;}*/ ?>
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
    <link rel="stylesheet" type="text/css" href="/static/layuiadmin/layui/css/layui.css" /><link rel="stylesheet" type="text/css" href="/static/layuiadmin/style/admin.css" /><link rel="stylesheet" type="text/css" href="/static/plugin/jstree/themes/default/style.min.css" /><link rel="stylesheet" type="text/css" href="/static/css/font-awesome.min.css" />
    <title></title>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="" lay-filter="layui-business-form">
                <blockquote class="layui-elem-quote">固定标识：首页Banner（banner_index）</blockquote>
                <input type="hidden" name="id" value=""/>
                <input type="hidden" name="createUserId" value=""/>
                <input type="hidden" name="createTime" value=""/>
                <!-- 可切换的tab -->
                <div class="layui-tab layui-tab-brief">
                    <div class="layui-tab-content">
                        <!-- 基本信息 -->
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="required">*</span>位置标识</label>
                            <div class="layui-input-block">
                                <input type="text" name="ident" lay-verify="required" autocomplete="off" lay-vertype="tips" placeholder="请输入位置标识" class="layui-input" value="<?php if(!(empty($info['positions_ident']) || (($info['positions_ident'] instanceof \think\Collection || $info['positions_ident'] instanceof \think\Paginator ) && $info['positions_ident']->isEmpty()))): ?><?php echo htmlentities($info['positions_ident']); else: ?><?php echo randStr(); endif; ?>">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="required">*</span>位置名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="name" lay-verify="required" autocomplete="off" lay-vertype="tips" placeholder="请输入位置名称" class="layui-input" value="<?php if(!(empty($info['positions_title']) || (($info['positions_title'] instanceof \think\Collection || $info['positions_title'] instanceof \think\Paginator ) && $info['positions_title']->isEmpty()))): ?><?php echo htmlentities($info['positions_title']); endif; ?>">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">简要描述</label>
                            <div class="layui-input-block">
                                <textarea name="brief" placeholder="请输入简要描述" class="layui-textarea"><?php if(!(empty($info['positions_brief']) || (($info['positions_brief'] instanceof \think\Collection || $info['positions_brief'] instanceof \think\Paginator ) && $info['positions_brief']->isEmpty()))): ?><?php echo htmlentities($info['positions_brief']); endif; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 提交 -->
                <div class="layui-form-item layui-layout-admin">
                    <div class="layui-input-block">
                        <div class="layui-footer" style="left: 0;">
                            <input type="hidden" name="pid" value="<?php if(!(empty($info['positions_id']) || (($info['positions_id'] instanceof \think\Collection || $info['positions_id'] instanceof \think\Paginator ) && $info['positions_id']->isEmpty()))): ?><?php echo htmlentities($info['positions_id']); endif; ?>"/>
                            <button class="layui-btn" lay-submit="" lay-filter="layui-business-submit">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/tools.js"></script><script type="text/javascript" src="/static/js/jquery.min.js"></script><script type="text/javascript" src="/static/plugin/jstree/jstree.min.js"></script>
<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'form'], function(){
        var $ = layui.$,
            form = layui.form;
        /* 监听提交 */
        form.on('submit(layui-business-submit)', function(data){
            layer.load(1);
            // 发送请求
            $.post('<?php echo url("ads/positions/updateadd"); ?>',data.field,function(result){
                layer.closeAll('loading');
                if (result.code == 0) {
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                    parent.tableIns.reload();
                    parent.layer.msg("操作成功",{icon: 1});
                } else {
                    layer.msg(result.msg,{icon: 2});
                }
            },"json");
            return false;
        });
    });
</script>
</body>
</html>