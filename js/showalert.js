//弹出确定
function myalert(title, msg,backfunc) {
    var htm = "<div class='popup_box' id='alertjs'><div class='popup_bg'></div><div class='popup_box_main'><strong>" + title + "</strong><p>" + msg + "</p><div class='btn_popup_box clearfix'><a class='btn_popup_cancel taptap' id=\"confirmok\">确定</a></div></div></div>";
    $("body").append(htm);
    $('#confirmok').click(function () {
        cleanalert("alertjs");
        if (typeof (backfunc) == 'function')
            backfunc();
    });
}//弹出确定并跳转
function myalerttoone(title, msg, url) {
    if (url == undefined) {
        myalert(title, msg);
        return;
    }
    url = url.indexOf("#") == -1 ? url : url.substring(0, url.indexOf("#"));
    var htm = "<div class='popup_box' id='alertjs'><div class='popup_bg'></div><div class='popup_box_main'><strong>" + title + "</strong><p>" + msg + "</p><div class='btn_popup_box clearfix'><a class='btn_popup_cancel taptap' href='javascript:void(0)' onclick=\"document.location='" + url + "'\">确定</a></div></div></div>";
    $("body>div").eq(0).append(htm);
}
//弹出取消和确定并跳转
function myalertto(title, msg, url,butyes,butno) {
    if (url == undefined) {
        myalert(title, msg);
        return;
    }
    if (!butyes) butyes = "确定";
    if (!butno) butno = "取消";
    url = url.indexOf("#") == -1 ? url : url.substring(0, url.indexOf("#"));
    var htm = "<div class='popup_box' id='alertjs'><div class='popup_bg'></div><div class='popup_box_main'><strong>" + title + "</strong><p>" + msg + "</p><div class='btn_popup_box clearfix'><a class='btn_popup_cancel taptap' href=\"javascript:cleanalert('alertjs');\">" + butno + "</a><a href='javascript:void(0)' class='taptap' onclick=\"document.location='" + url + "'\">" + butyes + "</a></div></div></div>";
    $("body>div").eq(0).append(htm);
}
//弹出取消和确定并返回False或True
function myconfirm(title, msg, backfunc,funccancle) {
    var htm = "<div class='popup_box' id='alertjs'><div class='popup_bg'></div><div class='popup_box_main'><strong>" + title + "</strong><p>" + msg + "</p><div class='btn_popup_box clearfix'><a class='btn_popup_cancel taptap' href='javascript:void(0)' id='confirmno'>取消</a><a href='javascript:void(0)' class='taptap' id=\"confirmok\" >确定</a></div></div></div>";
    $("body").append(htm);
    $('#confirmok').click(function () {
        backfunc();
    });
    $('#confirmno').click(function () {
        cleanalert("alertjs");
        if (typeof (funccancle) == 'function')
		{
			funccancle();
		}
    });
}
function cleanalert(obj) {
    $("#" + obj).remove();
}
//任何内容，无按钮
function showload(msg, objid) {
    var htm = "<div class='popup_box' id='" + objid + "'><div class='popup_bg'></div><div class='popup_box_main midbox'><div>" + msg + "</div></div></div>";
    $("body>div").eq(0).append(htm);
} //任何内容，确认按钮回调
function showloadok(msg, objid,backfunc) {
    var htm = "<div class='popup_box' id='" + objid + "'><div class='popup_bg'></div><div class='popup_box_main midbox'><div>" + msg + "</div><div class='btn_popup_box clearfix'><a class='btn_popup_cancel taptap' href=\"javascript:cleanalert('" + objid + "');\">取消</a><a href='javascript:void(0)' class='taptap' id=\"confirmok\" >确定</a></div></div></div>";
    $("body>div").eq(0).append(htm);
    $('#confirmok').click(function () {
        backfunc();
    });
}

//格式化时间 xdate:时间对象或JSON格式下的字符串; format:转化格式,默认为:yyyy-MM-dd HH:mm:ss
function getFormatDate(xdate, format) {
    try {
        var format = format || 'yyyy-MM-dd HH:mm:ss';
        var date = (xdate instanceof Date) ? xdate : new Date(parseInt(xdate.replace('/Date(', '').replace(')/', ''), 10));
        var lang = { 'M+': date.getMonth() + 1, 'd+': date.getDate(),
            'H+': date.getHours(), 'm+': date.getMinutes(), 's+': date.getSeconds()
        };
        if (/(y+)/.test(format)) {
            format = format.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length));
        }
        for (var key in lang) {
            if (new RegExp('(' + key + ')').test(format)) {
                format = format.replace(RegExp.$1, RegExp.$1.length == 1 ?
                    lang[key] : ('00' + lang[key]).substr(('' + lang[key]).length));
            }
        }
        return format;
    } catch (e) {
        return '-';
    }
}


//替换因输入汉字不完整时引出的问题
//调用举例: var x = 'oooo'; x = x.replaceInput();
String.prototype.replaceInput = function() {
    return this.replace(eval('(/' + String.fromCharCode(8198) + '/g)'), '');
};

