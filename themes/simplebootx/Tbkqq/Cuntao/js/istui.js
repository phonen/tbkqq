$(function(){
        var list="";
        $(".quan_add_u").each(function(index,ele){
            if(list==''){
                list=$(this).attr('data-gid');
            }else{
                list=list+','+$(this).attr('data-gid');
            }
        });
        $.ajax({
            type:'post',
            url:'/userisfor',
            data:{'gid':list},
            dataType:'json',
            cache:false,
            success:function(res){
                //console.log(res);
                if(res.status==1){
                    $.each(res.data,function (k,w) {

                        if($("#quan_add_u_"+w).attr('data-page') === 'quanPage' || $("#quan_add_u_"+w).attr('data-page') === 'quanPage2')
                        {
                            //$("#quan_add_u_"+w).html('<p>√已加入推广</p><span class="had-tg"></span>');
                                $('#quan_add_u_'+w).html('<p>取消推广</p><span></span>');
                                $('#quan_add_u_'+w).parents('div.goods-item').addClass('goods-tg');
                                $('#quan_add_u_'+w).siblings('a').append('<div class="add_tg_status had-tg"><b><i></i><w>已加入</w></b><span></span></div>');
                        }
                        else if($("#quan_add_u_"+w).attr('data-page') === 'quanPage2')
                        {
                            //$("#quan_add_u_"+w).html('<div class="tg-status had-tg"><p><span><b>√</b>已加入推广</span></p><span class="cover"></span></div>');
                        }
                        else
                        {
                            // $("#quan_add_u_"+w).text('已加入');
                                $('#quan_add_u_'+w).html('<p>取消推广</p><span></span>');
                                $('#quan_add_u_'+w).siblings('a').append('<div class="add_tg_status had-tg"><b><i></i><w>已加入</w></b><span></span></div>');
                        }

                    });
                }

            }
        });

    $('.num-class').each(function (name, value) {
       // alert(name);
        var object = this.getAttribute('id');
        var id = object.replace('cid_num_', '');
        $.get($('#get_cate_url').attr('value'), {'cid':id}, function (response) {
            //console.log(response);
            $('#cid_num_'+response.id).html(response.num);
        });
        //qq = replace("show_line_","");
        //alert(value.match('/cid=\d+/'));
        //alert(this.match('/cid=\d+/'));
    });
 


    // $.get($('#get_cate_url').attr('value'), {'cid':$('#get_cid').attr('value')}, function (response) {
    //     //console.log(response);
    //     var str_a = '';
    //     if (typeof response == 'object' ) {
    //         $.each(response, function (index, item) {
    //             //alert(item.id);
    //             //alert(item.num);
    //             $('#cid_num_'+item.id).html(item.num);
    //             $('#url_cid_'+item.id).attr('href', item.url);
    //             // if (item.id == item.now_cid) {
    //             //     str_a += "<a class='quan_ajax_cid_on' href='"+item.url+"'>"+item.title+"（<span style='display: inline-block;' class='Arial_num' id='cid_num_"+item.id+"'>"+item.num+"</span>）</a>"
    //             // } else {
    //             //     str_a += "<a class='quan_ajax_cid_off' href='"+item.url+"'>"+item.title+"（<span style='display: inline-block;' class='Arial_num' id='cid_num_"+item.id+"'>"+item.num+"</span>）</a>"
    //             // }
    //         });
    //         //$('#show_quan_cid').append(str_a);
    //     }
    //
    // });
    $('.save_data').on('click', function () {
        alert($(this).data('id'));
    });


    //判断是否支持一键复制 0 不支持 1 支持
    var ClipboardSupport = 0;
    if(typeof Clipboard != "undefined"){
        ClipboardSupport = 1;
    }else{
        ClipboardSupport = 0;
    }

    $('.copy_text').click(function(e){
        if(ClipboardSupport == 0){
            layer.msg('浏览器版本太低，不支持一键复制',{
                    time: 2000
                }
            );
        }else{
            if($(this).find('.copyText').length>0){
                if(document.getElementById('copyContent')){
                    //存在复制内容框重置值
                    $('#copyContent').html($(this).find('.copyText').html());
                    $('#copyContent').find('img').attr('src',$('#copyContent').find('img').data('src'));
                }else{
                    //不存在复制内容框设置
                    var copy = document.createElement('div');
                    copy.id = "copyContent";
                    copy.innerHTML = $(this).find('.copyText').html();
                    document.body.appendChild(copy);
                    $('#copyContent').find('img').attr('src',$('#copyContent').find('img').data('src'));

                }
                if(!$(this).hasClass('copy_text_btn')){
                    $(this).addClass('copy_text_btn');
                }
                var copy = document.getElementById('copyContent');
                copyFunction(copy);
            }else{
                layer.msg('太快了，请重新复制！');
            }
        }
    });
    //设置一键复制
    var copyFunction = function(copy){
        var clipboard = new Clipboard('.copy_text_btn', {
            target: function() {
                return copy;
            }
        });

        clipboard.on('success', function(e) {
            layer.msg('已复制',{
                    time: 2000
                }
            );
            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            layer.msg('复制失败',{
                    time: 2000
                }
            );
            e.clearSelection();
        });

    }

    //设置复制内容框显示方向
    function resetleft(obj) {
        var parL = obj.parents('.goods-item').offset().left,
            parW = obj.parents('.goods-item').width(),
            parH = obj.prev('.goods-item-content').outerHeight(),
            selfW = obj.outerWidth(),
            selfH = obj.parent().outerHeight(),

            copyH = obj.outerHeight();
            //复制容器的宽度
                if(parH > 450 ){
                    //榜单
                    parW -= 15;
                }
        var copyTextTop = parseInt((selfH - copyH + 12)/2);
        if(parW + parL + selfW > $(document).width()){
            obj.removeClass('copyRight');
            obj.addClass('copyLeft');
            obj.css({'top':copyTextTop});
        }else{
            obj.removeClass('copyLeft');
            obj.addClass('copyRight');
            obj.css({'top':copyTextTop});
        }

    }


    if(ClipboardSupport == 1){
        $('.goods-item .copy_text').mouseenter(function(){
            var obj = $(this);
                copyFun(obj)
        });
    }

    var copyFun = function (obj){
        var str = "点击复制",
            strHtml = obj.html().toString().replace("复制文案",str);
        obj.html(strHtml);

        if(obj.find('.copyText').length>0){
            resetleft(obj.find('.copyText'));
        }else{
            var url='/?g=tbkqq&m=cuntao&a=gettpl&id='+ obj.siblings('.go_info').data('gid') ;
            $.ajax({
                type:'get',
                url:url,
                cache:false,
                success:function(res){
                    obj.append('<div class="copyText">'+res+'</div>');
                    // obj.find('img').attr('src',obj.siblings('a').find('img').attr('src'));
                    resetleft(obj.find('.copyText'));
                },
                errer:function(){
                }
            });
        }
    }

    $('.goods-item .copy_text').mouseleave(function(){
        var str = "复制文案",
            strHtml = $(this).html().toString().replace("点击复制",str);
        $(this).html(strHtml);
    });
});

