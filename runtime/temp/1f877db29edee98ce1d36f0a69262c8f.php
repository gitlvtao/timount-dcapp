<?php /*a:1:{s:48:"D:\DCAPP\application\ads\view\ad\update_add.html";i:1542340452;}*/ ?>
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
        <div class="layui-card-body">
            <form class="layui-form" action="" lay-filter="layui-business-form">
                <div class="layui-form-item layui-row">
                    <div class="layui-col-sm6">
                        <div class="layui-col-sm6">
                            <label class="layui-form-label"><span class="required">*</span>广告位置</label>
                            <div class="layui-input-block">
                                <select name="positionId" lay-verify="required" lay-vertype="tips" lay-filter="select-broker-level">
                                    <option value="">请选择</option>
                                    <?php if(is_array($positions) || $positions instanceof \think\Collection || $positions instanceof \think\Paginator): $i = 0; $__LIST__ = $positions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$positions): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo htmlentities($positions['positions_id']); ?>" <?php if(!(empty($info['ad_position_id']) || (($info['ad_position_id'] instanceof \think\Collection || $info['ad_position_id'] instanceof \think\Paginator ) && $info['ad_position_id']->isEmpty()))): if($positions['positions_id'] == $info['ad_position_id']): ?>selected<?php endif; endif; ?>><?php echo htmlentities($positions['positions_title']); ?></option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-sm6">
                        <label class="layui-form-label"><span class="required">*</span>广告排序</label>
                        <div class="layui-input-block">
                            <input type="text" name="sort" lay-verify="required" lay-vertype="tips" autocomplete="off" placeholder="请输入广告排序" class="layui-input" value="<?php if(!(empty($info['ad_sort']) || (($info['ad_sort'] instanceof \think\Collection || $info['ad_sort'] instanceof \think\Paginator ) && $info['ad_sort']->isEmpty()))): ?><?php echo htmlentities($info['ad_sort']); else: ?>99<?php endif; ?>">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item layui-row" >
                    <div class="layui-col-sm6">
                        <label class="layui-form-label"><span class="required">*</span>广告名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="title" lay-verify="required" lay-vertype="tips" autocomplete="off" placeholder="请输入广告名称" class="layui-input" value="<?php if(!(empty($info['ad_title']) || (($info['ad_title'] instanceof \think\Collection || $info['ad_title'] instanceof \think\Paginator ) && $info['ad_title']->isEmpty()))): ?><?php echo htmlentities($info['ad_title']); endif; ?>">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item layui-row">
                    <div class="layui-col-sm6">
                        <label class="layui-form-label">广告网址</label>
                        <input type="hidden" id="frontImg" name="justurl" value=""/>
                        <div class="layui-input-block">
                            <input type="text" name="url" lay-verify="required" lay-vertype="tips" autocomplete="off" placeholder="请输入广告网址" class="layui-input" value="<?php if(!(empty($info['ad_url']) || (($info['ad_url'] instanceof \think\Collection || $info['ad_url'] instanceof \think\Paginator ) && $info['ad_url']->isEmpty()))): ?><?php echo htmlentities($info['ad_url']); endif; ?>">
                        </div>
                    </div>
                    <div class="layui-col-sm6">
                        <label class="layui-form-label">广告图片</label>
                        <input type="hidden" name="adimage" value=""/>
                        <div class="layui-input-block">
                            <div class="layui-upload">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" id="upload_back_img_btn">上传</button>
                                <div class="layui-upload-list">
                                    <img class="layui-upload-img" id="img_url" src="<?php if(!(empty($info['ad_image']) || (($info['ad_image'] instanceof \think\Collection || $info['ad_image'] instanceof \think\Paginator ) && $info['ad_image']->isEmpty()))): ?><?php echo htmlentities($info['ad_image']); endif; ?>" onerror="this.src='/static/img/nopicture.gif';this.onerror=null" style="width: 240px; height: 180px">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">广告简要</label>
                    <div class="layui-input-block">
                        <textarea name="brief" placeholder="请输入广告简要" class="layui-textarea"><?php if(!(empty($info['ad_brief']) || (($info['ad_brief'] instanceof \think\Collection || $info['ad_brief'] instanceof \think\Paginator ) && $info['ad_brief']->isEmpty()))): ?><?php echo htmlentities($info['ad_brief']); endif; ?></textarea>
                    </div>
                </div>

                <!-- 提交 -->
                <div class="layui-form-item layui-layout-admin">
                    <div class="layui-input-block">
                        <div class="layui-footer" style="left: 0;">
                            <input type="hidden" name="aid" value="<?php if(!(empty($info['ad_id']) || (($info['ad_id'] instanceof \think\Collection || $info['ad_id'] instanceof \think\Paginator ) && $info['ad_id']->isEmpty()))): ?><?php echo htmlentities($info['ad_id']); endif; ?>"/>
                            <button class="layui-btn" lay-submit="" lay-filter="layui-business-submit">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/tools.js"></script>
<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'form', 'upload'], function(){
        var $ = layui.$
            ,form = layui.form
            ,upload = layui.upload;
        // 文件上传
        upload.render({
            elem: '#upload_back_img_btn',
            url: '<?php echo url("other/upload/image"); ?>',
            field: 'image',
            accept: 'images',
            acceptMime: 'image/*',
            before: function(obj){
                layer.load(1);
            },
            done: function(res){
                layer.closeAll('loading');
                if (res.code == 0) {
                    $("#img_url").attr("src", res.data);
                    $("input[name='adimage']").val(res.data);
                } else {
                    return layer.msg('上传失败');
                }
            },
            error: function(){
                layer.closeAll('loading');
            }
        });

        /* 监听提交 */
        form.on('submit(layui-business-submit)', function(data){
            layer.load(1);console.log(data.field);
            $.post('<?php echo url("ads/ad/updateadd"); ?>',data.field,function(result){
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