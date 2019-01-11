<?php /*a:1:{s:57:"D:\DCAPP\application\article\view\article\update_add.html";i:1543198509;}*/ ?>
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
                            <label class="layui-form-label"><span class="required">*</span>栏目</label>
                            <div class="layui-input-block">
                                <select name="columnId" lay-verify="required" lay-vertype="tips" lay-filter="select-broker-level">
                                    <option value="">请选择</option>
                                    <?php if(is_array($column) || $column instanceof \think\Collection || $column instanceof \think\Paginator): $i = 0; $__LIST__ = $column;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$column): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo htmlentities($column['column_id']); ?>" <?php if(!(empty($info['article_column_id']) || (($info['article_column_id'] instanceof \think\Collection || $info['article_column_id'] instanceof \think\Paginator ) && $info['article_column_id']->isEmpty()))): if($column['column_id'] == $info['article_column_id']): ?>selected<?php endif; endif; ?>><?php echo htmlentities($column['column_name']); ?></option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item layui-row" >
                    <div class="layui-col-sm6">
                        <label class="layui-form-label"><span class="required">*</span>文章标题</label>
                        <div class="layui-input-block">
                            <input type="text" name="title" lay-verify="required" lay-vertype="tips" autocomplete="off" placeholder="请输入广告名称" class="layui-input" value="<?php if(!(empty($info['article_title']) || (($info['article_title'] instanceof \think\Collection || $info['article_title'] instanceof \think\Paginator ) && $info['article_title']->isEmpty()))): ?><?php echo htmlentities($info['article_title']); endif; ?>">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item layui-row">
                    <div class="layui-col-sm6">
                        <label class="layui-form-label">主图</label>
                        <input type="hidden" name="articleimage" value=""/>
                        <div class="layui-input-block">
                            <div class="layui-upload">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" id="upload_back_img_btn">上传</button>
                                <div class="layui-upload-list">
                                    <img class="layui-upload-img" id="img_url" src="<?php if(!(empty($info['article_thumb']) || (($info['article_thumb'] instanceof \think\Collection || $info['article_thumb'] instanceof \think\Paginator ) && $info['article_thumb']->isEmpty()))): ?><?php echo htmlentities($info['article_thumb']); endif; ?>" onerror="this.src='/static/img/nopicture.gif';this.onerror=null" style="width: 240px; height: 180px">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">文章简要</label>
                    <div class="layui-input-block">
                        <textarea name="brief" placeholder="请输入文章简要" class="layui-textarea"><?php if(!(empty($info['article_brief']) || (($info['article_brief'] instanceof \think\Collection || $info['article_brief'] instanceof \think\Paginator ) && $info['article_brief']->isEmpty()))): ?><?php echo htmlentities($info['article_brief']); endif; ?></textarea>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">文章内容</label>
                    <div class="layui-input-block">
                        <div id="articleEditor">
                            <?php if(!(empty($info['article_content']) || (($info['article_content'] instanceof \think\Collection || $info['article_content'] instanceof \think\Paginator ) && $info['article_content']->isEmpty()))): ?>
                            <?php echo $info['article_content']; endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 提交 -->
                <div class="layui-form-item layui-layout-admin">
                    <div class="layui-input-block">
                        <div class="layui-footer" style="left: 0; z-index: 10001">
                            <input type="hidden" name="aid" value="<?php if(!(empty($info['article_id']) || (($info['article_id'] instanceof \think\Collection || $info['article_id'] instanceof \think\Paginator ) && $info['article_id']->isEmpty()))): ?><?php echo htmlentities($info['article_id']); endif; ?>"/>
                            <button class="layui-btn" lay-submit="" lay-filter="layui-business-submit">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/tools.js"></script><script type="text/javascript" src="/static/plugin/wangEditor/wangEditor.min.js"></script>
<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'form', 'upload'], function(){
        var $ = layui.$
            ,form = layui.form
            ,upload = layui.upload;
        var E = window.wangEditor;
        var editor = new E('#articleEditor');
        // 配置服务器端地址
        editor.customConfig.uploadImgServer = '<?php echo url("other/upload/image"); ?>';
        // 限制一次最多上传 1 张图片
        editor.customConfig.uploadImgMaxLength = 1;
        // 自定义上传参数
        editor.customConfig.uploadFileName = 'image';
        editor.customConfig.uploadImgHeaders = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };
        editor.customConfig.uploadImgHooks = {
            // 如果服务器端返回的不是 {errno:0, data: [...]} 这种格式，可使用该配置
            // （但是，服务器端返回的必须是一个 JSON 格式字符串！！！否则会报错）
            customInsert: function (insertImg, result, editor, layer) {
                // 图片上传并返回结果，自定义插入图片的事件（而不是编辑器自动插入图片！！！）
                // insertImg 是插入图片的函数，editor 是编辑器对象，result 是服务器端返回的结果

                // 举例：假如上传图片成功后，服务器端返回的是 <?php echo url("","",true,false);?> 这种格式，即可这样插入图片：
                // result 必须是一个 JSON 格式字符串！！！否则报错
                if (result.code == 0) {
                    var url = result.data;
                    insertImg(url);
                } else {
                    layer.msg('上传失败');
                }
            }
        }
        editor.create();

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
                    $("input[name='articleimage']").val(res.data);
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
            layer.load(1);
            data.field.content = editor.txt.html(); console.log(data.field);
            $.post('<?php echo url("article/article/updateadd"); ?>',data.field,function(result){
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