$(function(){
    var act = {};
    act._switching = false;
    /**
     * apps下载页 效果js
     */
    //change tab start
     $('.other_nav .jp').click(function(){
        $(this).addClass("active");
        $(".other_nav .jky").removeClass("active");
        $(".tabjp").show();
        $(".tabjky").hide();
    })
    $('.other_nav .jky').click(function(){
        $(this).addClass("active");
        $(".other_nav .jp").removeClass("active");
        $(".tabjky").show();
        $(".tabjp").hide();
	})
    //change tab end
    var setH = function(){
        $(".stage").height($(window).height());
        $(".fixed_right").css("top",($(window).height()+$(".header").height()-$(".fixed_right").height())/2);
        $(".stage_in").each(function(){
            $(this).css("margin-top",($(window).height()+$(".header").height()-$(this).height())/2);
        });
    }
    setH();
    $('html,body').scrollTop(0);
    $(window).resize(function(){
        setH();
    });
    $("#j_switch_nav a").removeClass("active").eq(0).addClass("active");
    TweenLite.fromTo( $("#stage_1").find('.goods_01'),1, {top:"291px",autoAlpha:0,ease:Power1.easeOut},{top:"100px",autoAlpha:1});
    var i = 1;
    // using the event helper
    $('body').mousewheel(function(event) {
        if(act._switching){
            return;
        }
        if(event.deltaY > 0){   //往上
            i = i<=1?1:i-1;
        }else{
            i = i>=$(".stage").size()?$(".stage").size():i+1;
        }
        $("#j_switch_nav a").eq(i-1).click();
    });

    $("#next_arrow").click(function(){
        var index = $("#j_switch_nav a").index($("#j_switch_nav a.active"));
        $("#j_switch_nav a").eq(index+1).click();
    });

    $("#j_switch_nav a").click(function(){
        if(act._switching){
            return;
        }

        var index = $(this).index();
        if(index == $("#j_switch_nav a").index($("#j_switch_nav a.active"))){
            return;
        }
        act._switching = true;
        if(index == $(".stage").size()-1){
            $("#next_arrow").hide();
        }else{
            $("#next_arrow").show();
        }

        $(".fixed_right .step_text").stop().animate({opacity: '0'},800);
        if(index == 0){
            TweenLite.fromTo( $("#stage_2").find('.demo2'),1, {top:"90px",autoAlpha:1,ease:Power1.easeOut},{top:"-64px",autoAlpha:0});
        }else if(index == 1){
            TweenLite.fromTo( $("#stage_1").find('.goods_01'),1, {top:"100px",autoAlpha:1,ease:Power1.easeOut},{top:"291px",autoAlpha:0});
            TweenLite.fromTo( $("#stage_3").find('.demo3'),1, {left:"50px",autoAlpha:1,ease:Power1.easeOut},{left:"-129px",autoAlpha:0});
        }else if(index == 2){
            TweenLite.fromTo( $("#stage_2").find('.demo2'),1, {top:"90px",autoAlpha:1,ease:Power1.easeOut},{top:"-64px",autoAlpha:0});
            TweenLite.fromTo( $("#stage_4").find('.demo4'),1, {top:"60px",autoAlpha:1,ease:Power1.easeOut},{top:"460px",autoAlpha:0});
        }else if(index == 3){
            TweenLite.fromTo( $("#stage_3").find('.demo3'),1, {left:"50px",autoAlpha:1,ease:Power1.easeOut},{left:"-129px",autoAlpha:0});
        }
        $("#j_switch_nav a").removeClass("active").eq(index).addClass("active");
        $(".opa").removeAttr("style");
        $('html,body').stop(true,true).animate({'scrollTop':$(window).height()*index},800,function(){
            $(".fixed_right .step_text").removeClass('step_01').removeClass('step_02').removeClass('step_03').removeClass('step_04').addClass('step_0'+(index+1));
            $(".fixed_right .step_text").stop().animate({opacity: '1'},800);
            if(index == 0){
                TweenLite.fromTo( $("#stage_1").find('.goods_01'),1, {top:"291px",autoAlpha:0,ease:Power1.easeOut},{top:"100px",autoAlpha:1,onComplete:function(){
                    act._switching=false;
                }});
            }else if(index == 1){
                TweenLite.fromTo( $("#stage_2").find('.demo2'),1, {top:"-64px",autoAlpha:0,ease:Power1.easeOut},{top:"90px",autoAlpha:1,onComplete:function(){
                    act._switching=false;
                }});
            }else if(index == 2){
                TweenLite.fromTo( $("#stage_3").find('.demo3'),1, {left:"-129px",autoAlpha:0,ease:Power1.easeOut},{left:"50px",autoAlpha:1,onComplete:function(){
                    act._switching=false;
                }});
            }else if(index == 3){
                TweenLite.fromTo( $("#stage_4").find('.demo4'),1, {top:"460px",autoAlpha:0,ease:Power1.easeOut},{top:"60px",autoAlpha:1,onComplete:function(){
                    act._switching=false;
                }});
            }
        })
    });
    var contentJp = '<div class="mobile_select jp_select clear">'
        +'<a href="javascript:;" class="n-android">Android</a>'
        +'<a href="javascript:;" class="n-iphone">iPhone</a>'
        +'<a href="javascript:;" class="n-ipad">iPad</a>'
        +'<img src="http://s.juancdn.com/juanpi/images/app/move-cur.gif" class="move_img">'
        +'</div>'
        +'<div class="mobile_con clear"><ul class="fix_box">'
        +'<li><div class="n-wechat fl"><img src="http://s.juancdn.com/juanpi/images/app/jp-n-android.png?20141224"><br>手机扫描二维码下载</div><div class="n-download fr"><a href="http://m.juanpi.com/download/zhe/android" class="jp_download"></a>直接下载到电脑</div></li>'
        +'<li><div class="n-wechat fl"><img src="http://s.juancdn.com/juanpi/images/app/jp-n-android.png?20141224"><br>手机扫描二维码下载</div><div class="n-download fr"><a href="https://itunes.apple.com/cn/app/juan-pi/id639388447?mt=8" class="jp_download"></a>前往appstore下载</div></li>'
        +'<li><div class="n-wechat fl"><img src="http://s.juancdn.com/juanpi/images/app/jp-n-android.png?20141224"><br>手机扫描二维码下载</div><div class="n-download fr"><a href="https://itunes.apple.com/cn/app/juan-pi-zhe-kouhd-quan-chang1zhe/id888530819?mt=8" class="jp_download"></a>前往appstore下载</div></li>'
        +'</ul></div>';
    var contentJky = '<div class="mobile_select clear">' +
        '<a class="n-android hover" href="javascript:;">Android</a>' +
        '<a class="n-iphone" href="javascript:;">iPhone</a>' +
        '<a class="n-ipad" href="javascript:;">iPad</a>' +
        '<img class="move_img" src="http://s.juancdn.com/juanpi/images/app/move-cur.gif" style="left: 80px;">' +
        '</div>' +
        '<div class="mobile_con clear"><ul class="fix_box" >' +
        '<li><div class="n-wechat fl"><img src="http://s.juancdn.com/juanpi/images/app/jky-n-android.png?20141224"><br>手机扫描二维码下载</div><div class="n-download fr"><a class="jky_download" href="http://m.juanpi.com/download/jiu/android" ></a>直接下载到电脑</div></li>' +
        '<li><div class="n-wechat fl"><img src="http://s.juancdn.com/juanpi/images/app/jky-n-android.png?20141224"><br>手机扫描二维码下载</div><div class="n-download fr"><a class="jky_download" href="https://itunes.apple.com/cn/app/jiu-kuai-you/id590973130" ></a>前往appstore下载</div></li>' +
        '<li><div class="n-wechat fl"><img src="http://s.juancdn.com/juanpi/images/app/jky-n-android.png?20141224"><br>手机扫描二维码下载</div><div class="n-download fr"><a class="jky_download" href="https://itunes.apple.com/cn/app/jiu-kuai-youhd-quan-chang9.9bao/id883469528?mt=8"></a>前往appstore下载</div></li>' +
        '</ul></div>';
    $(".code_area .code_btn").click(function(){
        var index = $(this).index();
        if($('.jp').hasClass('active')){
            var content =contentJp;
        }else{
            var content =contentJky;
        }
        b = new XDLightBox({
            title: "",
            lightBoxId: "alert_download",
            contentHtml: content,
            scroll: false
        });
        b.init();
        $(".mobile_select a").mouseover(function(){
            var index_alert = $(".mobile_select a:visible").index($(this));
            $(".mobile_select a").removeClass("hover");
            $(this).addClass("hover");
            $(".fix_box").stop(true,true).animate({left: ($(".mobile_con").width())*index_alert*(-1)},800);
            $(".move_img").stop(true,true).animate({left: 80+index_alert*(170)},800);
//                console.log($(".mobile_select a:visible").index($(this)));
        });
        $("#alert_download .mobile_select a:visible").eq(index).mouseover();
    });
})
