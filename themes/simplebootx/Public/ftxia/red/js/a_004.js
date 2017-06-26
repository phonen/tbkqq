(function($){
    XD.JUBAO = function (e) {
        if (XDPROFILE.uid == "") {
            XD.user_handsome_login_init();
            XD.user_handsome_login();
        } else {
            $("#otherReasons").val("");
            $(".other").hide();
//            $("#reportAn ").get(0).selectedIndex=0;
            $("#report_title").html($(e).attr("title"));
            $("#report_title").attr("re",$(e).attr("re"));
            $('#alert_report').show();
            $('.alert_fullbg').show();
            $("#alert_report").css({   // 定位浮层
                top: (($(window).height() - $("#alert_report").height())/2),
                left: ($(window).width() - $("#alert_report").width())/2
            });
        }
    };

    XD.JUBAO_SMT = function () {
        if (XDPROFILE.uid == "") {
            XD.user_handsome_login_init();
            XD.user_handsome_login();
            return false;
        }
        var iid = $("#report_title").attr("re");
        var gtype = $("#jubao_" + iid).attr("gtype");
        data = {
            'action':"reportPost",
            'iid':iid,
            'rtype':$("#reportAn").val(),
            'demo':$("#otherReasons").val(),
            'uid':XDPROFILE.uid ,
            'gtype' : gtype
        };
        var demo = data['demo'].replace(/\s/g,"");
        if(data['rtype']==0) {
            alert("请选择举报理由");
        }else if(data['rtype'] == 8 && demo == ""){
            alert("请填写其它原因");
        }else if(demo.length > 140){
            alert('请在140字以内说明理由');
        }else{
            $.getJSON( __URL_JUANPI__ + "/shareajax?callback=?",data,function(re){
                alert(re.msg);
                $('.alert_bg').hide();
                $('.alert_fullbg').hide();
            });
        }
    };


    var $items = function() {
        var scr = $(document).scrollTop();
        if( scr < $(".bady-part").offset().top-5){
            $("#bady-tab").removeClass("fixed");
            $(".bady-part .con .information,.bady-part .bady_info").css("padding-top",0);
            $(".gobuy").hide();
        }else{
            $("#bady-tab").addClass("fixed");
            $(".gobuy").show();
            var i = $(".bady-part .information").size();
            for(i=i-1;i>0;i--){
                if($(".bady-part .information").eq(i).offset().top-$(".bady-tab").height() <= scr){
                    break;
                }
            }

            $(".bady-part .bady-tab a").removeClass("active").eq(i).addClass("active");
            i == 0?$(".bady-part .size_info").show():$(".bady-part .size_info").hide();
        }
    };
    $(window).bind("scroll", $items);
    $items();
    $(window).scrollTop(0);
    $(".bady-tab li").each(function(k){
        $(this).is(":hidden") && $(this).remove() && $(".bady-part .con .information").eq(k).remove();
//        $(this).is(":hidden") && $(".bady-part .con .information").eq(k).hide();
    });
    $(".bady-tab .size_info a").each(function(k){
        $(this).is(":hidden") && $(this).remove() && $(".bady-part .con .bady_info").eq(k).remove();
//        $(this).is(":hidden") && $(".bady-part .con .bady_info").eq(k).hide();
    });


    $(".bady-tab li").click(function(){
        if($(this).hasClass("active")) return;
        $(this).index() == 0?$(".bady-part .size_info").show():$(".bady-part .size_info").hide();
        $(".bady-part .information").css("padding-top",0).eq($(this).index()).css("padding-top",$(this).index() == 0?$(".bady-tab").height()+10:$(".bady-tab").height());
        $(window).scrollTop($(".bady-part .con .information").eq($(this).index()).offset().top);
    });


    $(".bady-tab .size_info a").click(function(){
        $(window).scrollTop($(".bady-part .bady_info").eq($(this).index()).offset().top);
        $(".bady-part .bady_info").eq($(this).index()).css("padding-top",$(".bady-tab").height()+10);
    });

    $(".xb_icon a").hover(function(){
        $(".xb_js .steup").hide().eq($(this).index()).show();
    })

    if($(".share_people:eq(0) li ").size() >= 12){
        $(".share_people:eq(0)").css("top","-105px")
    }else{
        $(".share_people:eq(0)").css("top","-65px")
    }
    $(".share_people:eq(1)").css("top","-45px")
    $(".share_box a:eq(0),.share_people:eq(0)").slowShow($(".share_people:eq(0)"),100);
    $(".share_box a:eq(1),.share_people:eq(1)").slowShow($(".share_people:eq(1)"),100);

    if((($(".start").size() >0 && parseFloat($(".show_body").find(".price_f .price").text()) >10) || $(".gone").size() >0) &&  $(".show_body").find(".quan").length <= 0){
        $('.show_box  .btn,.go_btn ').attr("href",'javascript:;');
        $('.show_box  .btn,.go_btn').removeAttr("target");

    }

    $(".go_btn ,.gobuy .btn").click(function(){
        var clickUrl = $(this).attr('href');
        var gid = $(".show_body").find(".y-like").data("gid");
        var capture =  $(".show_body").find(".item-like").attr("capture");
        var gtype = capture == 2 ? 1 : 2; //1，普通商品 2采集商品
        if(!$('.go_btn').hasClass("gone") && !$('.go_btn').hasClass("start")){
            return true;
        }
        if(($(".show_body").find(".quan").length != 0 && !$('.go_btn').hasClass("gone")) || clickUrl.match(/url/i)){
            return true;
        }
        if($('.go_btn').hasClass("gone")){
            if($(".show_body").find(".y-like").hasClass("active")){
                var content = '<div class="top_tips"><p><em class="over">商品抢光了！</em></p><p class="tips01">因商品已经抢光，无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a data-gtype="'+gtype+'" data-gid="'+gid+'" class="y-like item-like active" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">已收藏</p></a><p class="like-o"><span class="fl">随时留意您的手机哦</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
            }else{
                var content = '<div class="top_tips"><p><em class="over">商品抢光了！</em></p><p class="tips01">因商品已经抢光，无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a data-gtype="'+gtype+'" data-gid="'+gid+'" class="y-like item-like" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">收藏</p></a><p class="like-o"><span class="fl">下次开抢可提醒您</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
            }
        }else{
            if($(".show_body").find(".item-like").hasClass("active")){
                var content = '<div class="top_tips"><p><em class="over">您已收藏，商品开抢前会在手机提醒您开抢！</em></p></div><div class="item-btn clear"><div class="collect-box  fl"><a data-gtype="'+gtype+'" data-gid="'+gid+'" class="y-like item-like active" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">已收藏</p></a><p class="like-o"><span class="fl">随时留意您的手机哦</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
            }else if($('.go_btn').hasClass("start") && parseFloat($(".show_body").find(".price_f .price").text()) < 10){
                return true;
            }else if($('.go_btn').hasClass("start")){
                var content = '<div class="top_tips"><p><em class="over">商品还未开始，收藏享开抢提醒！</em></p></div><div class="item-btn clear"><div class="collect-box  fl"><a data-gtype="'+gtype+'" data-gid="'+gid+'" class="y-like item-like" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">收藏</p></a><p class="like-o"><span class="fl">下次开抢可提醒您</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
            }
        }


        b = new XDLightBox({
            title: "温馨提示",
            lightBoxId: "alert_remind",
            contentHtml: content,
            scroll: false
        });
        b.init();
    })
})(jQuery);

