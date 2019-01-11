function layerArea(full) {
    //var w = document.documentElement.scrollWidth || document.body.scrollWidth;
    //var h = document.documentElement.scrollHeight || document.body.scrollHeight;
    var w = document.documentElement.clientWidth || document.body.clientWidth;
    var h = document.documentElement.clientHeight || document.body.clientHeight;
    // default max width is 1000
    var dw = full == true ? w - 100 : Math.min(w - 100, 1000);
    var dh = h - 50;
    return [dw + 'px',dh + 'px'];
}

// 可以指定高度
function layerArea2(assdh) {
    var w = document.documentElement.clientWidth || document.body.clientWidth;
    var h = document.documentElement.clientHeight || document.body.clientHeight;
    var dw = Math.min(w - 100, 700);
    var dh = h - 50;
    if (assdh) {
        dh = Math.min(dh, assdh);
    }
    return [dw + 'px',dh + 'px'];
}

//定义日期的格式化
Date.prototype.Format = function(fmt) {
    if (this.getTime() == 0) {
        return "-";
    }
    var o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
};