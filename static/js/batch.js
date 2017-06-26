function getRandomNum(Min,Max){
    Min = parseInt(Min);
    Max = parseInt(Max);
    var Range = Max - Min;
    var Rand = Math.random();
    return(Min + Math.round(Rand * Range));
}
function showLog (msg, status) {
    var color = '';
    if (status != null) {
        color = ' class="log' + status + '"';
    }
    var html = '<p' + (color ? color : '') + '>【'+new Date().format('yyyy-MM-dd HH:mm:ss')+'】' + msg + '</font>';
    $(".log_list").append(html);
}
$('.J_cate_select').cate_select('请选择');
var list = [],
i = 0;
function dealItem () {
    i++;
    if (i > list.length) {
        showLog('恭喜，采集任务处理完毕！', 1);
        return false;
    }
    showLog('正在采集第 <b>'+i+'</b> 个宝贝...');
    var data = {
        url: list[i-1],
        cate_id: $("#J_cate_id").val(),
        volume: getRandomNum($("#volume1").val(), $("#volume2").val()),
        hits: getRandomNum($("#hits1").val(), $("#hits2").val()),
        seo_title: '',
        seo_keys: '',
        seo_desc: '',
        add_time: new Date().getTime(),
        pass: 1
    };
    $.post(batchUrl, data, function(result){
        $("#nProcess").html(+$("#nProcess").html()-1);
        if(result.status == 1){
            showLog('第 <b>'+i+'</b> 个宝贝采集成功：'+result.msg, result.status);
            $("#nOk").html(+$("#nOk").html()+1);
        }else{
            if (result.status == 2) {
                $("#nExists").html(+$("#nExists").html()+1);
                showLog('第 <b>'+i+'</b> 个宝贝已存在：'+result.msg, 1);
            } else {
                $("#nFail").html(+$("#nFail").html()+1);
                if (result.msg != '')
                    showLog('第 <b>'+i+'</b> 个宝贝采集失败：'+result.msg, 0);
                else
                    showLog('第 <b>'+i+'</b> 个宝贝采集失败：'+result.status.info, 0);
            }
        }
        dealItem();
    });
}
$(function() {
    showLog('请粘贴宝贝链接，每个宝贝链接占一行，然后开始批量采集...');
    $('#J_getclick_url').live('click', function() {
        var iid = $.trim($('#J_num_iid').val());
        if(iid == ''){
            $.ftxia.tip({content:lang.iid_empty, icon:'alert'});
            return false;
        }
        $.getJSON(ajaxUrl, {iid:iid}, function(result){
            if(result.status == 1){
                $('#J_click_url').val(result.data);
            }else{
                $.ftxia.tip({content:result.msg});
            }
        });
    });
    $.formValidator.initConfig({formid:"info_form",autotip:true});
    $("#good_link").formValidator({onshow:'请粘贴宝贝链接，每个宝贝链接占一行',onfocus:'请粘贴宝贝链接，每个宝贝链接占一行'}).inputValidator({min:1,onerror:'请正确粘贴宝贝链接，每个宝贝链接占一行'});
    $("#volume1,#volume2,#hits1,#hits2").formValidator({onshow:"请填写数字",onfocus:"请填写数字",oncorrect:"填写正确",onempty:"请填写数字"}).inputValidator({min:2,max:9,onerror:"应该为2-9位之间的数字"}).regexValidator({regexp:"^[1-9][0-9]*$",onerror:"请填写整数"});

    $('#J_get_info').live('click', function() {
        $(".log_list").html('');
        var link = $("#good_link").val(),
            cateId = parseInt($("#J_cate_id").val());
        if (cateId == 0) {
            showLog('请先选择分类。', 0);
            return false;
        }
        if (link.trim() != '') {
            $("#nTotal,#nProcess,#nOk,#nExists,#nFail").html(0);
            list = [];
            var ix = 0;
            $.each(link.split('\n'), function(ii, url){
                if (url.trim() != '') {
                    list[ix++] = url.trim();
                    $("#nTotal,#nProcess").html(+$("#nTotal").html()+1);
                }
            });
            if (list.count == 0) {
                showLog('请正确粘贴宝贝链接，每个宝贝链接占一行', 0);
                return false;
            }
            i = 0;
            dealItem();
        } else {
            showLog('请正确粘贴宝贝链接，每个宝贝链接占一行', 0);
        }
    });
});