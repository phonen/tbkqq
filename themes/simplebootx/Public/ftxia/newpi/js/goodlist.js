(function($){
    $.fn.slowShow = function(ele,time){
        time = time == undefined?100:time;
        var timer=null;
        clearInterval(timer);
        this.hover(function(){
                clearTimeout(timer);
                timer=setTimeout(function(){
                    ele.show();
                },time);
            },
            function(){
                clearTimeout(timer);
                timer=setTimeout(function(){
                    ele.hide();
                },time);
            }
        )
    }



})(jQuery);
function selAll(obj){
    $(obj).attr("checked",'true');//全选
}
function selNone(obj){
    $(obj).removeAttr("checked");//取消全选
}
function selfan(obj){
    $(obj).each(function(){
		if($(this).attr("checked")){
			$(this).removeAttr("checked");
		}
		else{
		    $(this).attr("checked",'true');
		}
	})
}
(function($){
    $("img.lazy").lazyload({threshold:200,failure_limit:30});

    /**
     * 搜索
     * @author xueli@juanpi.com
     * @date   2014-12-05
     * @return {[type]}   [description]
     */
    

   

    /**
     * fangfang，绑定滚动函数
     * @param {}
     * @time 2014-02-12
     */
    




    var carousel_index = function(){
        if($(".banner li").size() == 1) $(".banner li").eq(0).css("opacity", "1");
        if($(".banner li").size() <= 1) return;
        var i = 0,max = $(".banner li").size()- 1,playTimer;
        $(".banner li").each(function(){
            $(".adType").append('<a></a>');
        });
    //初始化
        $(".adType a").eq(0).addClass("current");
    $(".banner li").eq(0).css({"z-index":"2","opacity":"1"});
    var playshow=function(){
        $(".adType a").removeClass("current").eq(i).addClass("current");
        $(".top_bar .banner li").eq(i).css("z-index", "2").fadeTo(500, 1, function(){
        $(".top_bar .banner li").eq(i).siblings("li").css({
                  "z-index": 0,
                  opacity: 0
        }).end().css("z-index", 1);
        });
    }
    var next = function(){
      i = i>=max?0:i+1;
      playshow()
    }
    var prev = function(){
      i = i<=0?max:i-1;
      playshow()
    }
        var play = setInterval(next,3000);
        $(".banner li a,.banner_arrow").hover(function(){
            clearInterval(play);
            $(".banner_arrow").css("display","block");
        },function(){
            clearInterval(play);
            play = setInterval(next,3000);
            $(".banner_arrow").css("display","none");
        });
        $(".banner_arrow .arrow_prev").on('click', _.throttle(function(event) {
          prev();
        },600) );
        $(".banner_arrow .arrow_next").on('click', _.throttle(function(event) {
          next();
        },600) );
        $(".adType a").mouseover(function(){
            if($(this).hasClass("current")) return;
            var index = $(this).index()-1;
            var playTimer = setTimeout(function(){
                clearInterval(play);
                i = index;
                next();
            },500)
        }).mouseout(function(){
                clearTimeout(playTimer);
            });
    }
    carousel_index();

    /**
     * 将文字信息上下滚动
     * Author: zhuwenfang
     * Date: 14-01-10
     * Time: PM 16:55
     * Function: scrolling the dom 'li' up&down
     **/
    var liAutoScroll = function(){
        var liScrollTimer;
        var $obj = $('.links_list_box');
        $obj.hover(function(){
            clearInterval(liScrollTimer);
        }, function(){
            liScrollTimer = setInterval(function(){
                $obj.find(".links_list").animate({
                    marginTop : -20 + 'px'
                }, 500, function(){
                    $(this).css({ marginTop : "0px"}).find("li:first").appendTo(this);
                });

            }, 3000);
        }).trigger("mouseleave");

    }
    liAutoScroll();
})(jQuery);
(function($){
    $(".goods-list li").hover(function(){
        $(this).find(".list-good").hasClass("gone") && $(this).find(".like-ceng").size() != 0 && $(this).find(".like-ico").hasClass("l-active") && $(this).find(".like-ceng").show();
        if($(this).find(".list-good").hasClass("gone")) return;
        if($(this).find(".brand-bd").size() != 0) return;
        $(this).addClass("hover");
        $(this).find(".btn span").html() == "淘宝" && $(this).find(".btn span").html("去淘宝");
        $(this).find(".btn span").html() == "天猫" && $(this).find(".btn span").html("去天猫");
		$(this).find(".btn span").html() == "品牌" && $(this).find(".btn span").html("去抢购");
        $(this).find(".btn span").html() == "我要兑换" && $(this).find(".btn span").html("去兑换");
        $(this).find(".btn span").html() == "卷皮" && $(this).find(".btn span").html("去卷皮");
    },function(){
        $(this).find(".like-ceng").hide();
        $(this).removeClass("hover");
        $(this).find(".btn span").html() == "去淘宝" && $(this).find(".btn span").html("淘宝");
        $(this).find(".btn span").html() == "去天猫" && $(this).find(".btn span").html("天猫");
		$(this).find(".btn span").html() == "去抢购" && $(this).find(".btn span").html("品牌");
        $(this).find(".btn span").html() == "去兑换" && $(this).find(".btn span").html("我要兑换");
        $(this).find(".btn span").html() == "去卷皮" && $(this).find(".btn span").html("卷皮");
    });

    $(".goods-list li").each(function(){
        if($(this).find('span.price-old').width()>= 65||$(this).find('span.price-current').width()>= 125){
            $(this).find('span.discount').hide();
        }
        if($(this).find('.list-good').hasClass("gone")){
            $(this).find(".btn a").attr("href","javascript:;");
            $(this).find(".btn a").removeAttr("target");

        }
    })

    $(".goods-list .buy-over a").click(function(e){
        if (e && e.stopPropagation) {
            e.stopPropagation();
        }else {//IE浏览器
            window.event.cancelBubble = true;
        }
    });

    //卷皮列表页提醒
    $(".goods-list li .good-pic,.goods-list li .good-title,.goods-list li .good-price,.goods-list .mask-bg,.goods-list .buy-over").click(function(){
        if($(this).parents("li").hasClass("noalert")){
            return true;
        }
//        if($(".header_user").length == 0 && $(".advance-nav").lenght == 0){
//            return true;
//        }
        if($(this).parents("li").find(".btn.start_1").size() == 0 && !$(this).parents("li").find(".list-good").hasClass("gone")){
            return true;
        }

        var link = $(this).parents("li").find(".good-title a").attr("href");
        var gid = $(this).parents("li").attr("id");		
        var gtype = $(this).parents("li").attr("gtype");		
        //积分兑换  采集   优惠券
        if(link.match(/jifen/i) || link.match(/url/i) || ($(this).parents("li").find(".go-quan").length != 0 && !$(this).parents("li").find(".list-good").hasClass("gone"))){   //积分兑换抽奖去内页
            return true;
        }

        if($(this).parents("li").find(".list-good").hasClass("gone")){
            if($(this).hasClass("good-title") || $(this).hasClass("good-price")) return true;
            if($(this).parents("li").find(".like-ico").hasClass("l-active") || $(this).parents("li").find(".del-ico").length != 0){
                var content = '<div class="top_tips"><p><em class="over">商品抢光了！</em></p><p class="tips01">因商品已经抢光，无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a data-gtype="'+gtype+'" data-gid="'+gid+'" class="y-like item-like active" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">已收藏</p></a></div></div>';
            }else{
                var content = '<div class="top_tips"><p><em class="over">商品抢光了！点击收藏随时关注</em></p><p class="tips01">因商品已经抢光，无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a class="y-like item-like" data-pid="'+gid+'" href="javascript:;"> <em class="heart"></em><p class="like-l">收藏</p></a></div></div>';
            }
        }else{
            if($(this).parents("li").find(".like-ico").hasClass("l-active") || $(this).parents("li").find(".del-ico").length != 0){
                var content = '<div class="top_tips"><p><em class="over">您已收藏，商品开抢前会在手机提醒您开抢！</em></p></div><div class="item-btn clear"><div class="collect-box  fl"><a data-gtype="'+gtype+'" data-gid="'+gid+'" class="y-like item-like active" href="javascript:void(0)"><em class="heart"></em><p class="like-l">已收藏</p></a><p class="like-o"><span class="fl">开抢前5分钟手机提醒</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
            }else{
                var content = '<div class="top_tips"><p><em class="over">商品还未开始，点击收藏随时关注！</em></p></div><div class="item-btn clear"><div class="collect-box  fl"><a class="y-like item-like" data-pid="'+gid+'" href="javascript:;"><em class="heart"></em><p class="like-l">收藏</p></a></div></div>';
            }
        }

        b = new XDLightBox({
            title: "温馨提示",
            lightBoxId: "alert_remind",
            contentHtml: content,
            scroll: false
        });
        b.init();
        return false;
    });

})(jQuery);
(function($){
    $(".zc-list .boxt").hover(function(){
        $(this).find(".list-good").hasClass("gone") && $(this).find(".like-ceng").size() != 0 && $(this).find(".like-ico").hasClass("l-active") && $(this).find(".like-ceng").show();
        if($(this).find(".list-good").hasClass("gone")) return;
        if($(this).find(".brand-bd").size() != 0) return;
        $(this).addClass("hover");
        $(this).find(".btn span").html() == "淘宝" && $(this).find(".btn span").html("去淘宝");
        $(this).find(".btn span").html() == "天猫" && $(this).find(".btn span").html("去天猫");		
    },function(){
        $(this).find(".like-ceng").hide();
        $(this).removeClass("hover");
        $(this).find(".btn span").html() == "去淘宝" && $(this).find(".btn span").html("淘宝");
        $(this).find(".btn span").html() == "去天猫" && $(this).find(".btn span").html("天猫");
    });

    $(".zc-list .boxt").each(function(){
        if($(this).find('span.price-old').width()>= 65||$(this).find('span.price-current').width()>= 125){
            $(this).find('span.discount').hide();
        }
        if($(this).find('.list-good').hasClass("gone")){
            $(this).find(".btn a").attr("href","javascript:;");
            $(this).find(".btn a").removeAttr("target");

        }
    })

    $(".zc-list .buy-over a").click(function(e){
        if (e && e.stopPropagation) {
            e.stopPropagation();
        }else {//IE浏览器
            window.event.cancelBubble = true;
        }
    });

    //卷皮列表页提醒
    $(".zc-list li .good-pic,.zc-list li .good-title,.zc-list li .good-price,.zc-list .mask-bg,.zc-list .buy-over").click(function(){
        if($(this).parents("li").hasClass("noalert")){
            return true;
        }
//        if($(".header_user").length == 0 && $(".advance-nav").lenght == 0){
//            return true;
//        }
        if($(this).parents("li").find(".btn.start_1").size() == 0 && !$(this).parents("li").find(".list-good").hasClass("gone")){
            return true;
        }

        var link = $(this).parents("li").find(".good-title a").attr("href");
        var gid = $(this).parents("li").attr("id");		
        var gtype = $(this).parents("li").attr("gtype");		
        //积分兑换  采集   优惠券
        if(link.match(/jifen/i) || link.match(/url/i) || ($(this).parents("li").find(".go-quan").length != 0 && !$(this).parents("li").find(".list-good").hasClass("gone"))){   //积分兑换抽奖去内页
            return true;
        }

        if($(this).parents("li").find(".list-good").hasClass("gone")){
            if($(this).hasClass("good-title") || $(this).hasClass("good-price")) return true;
            if($(this).parents("li").find(".like-ico").hasClass("l-active") || $(this).parents("li").find(".del-ico").length != 0){
                var content = '<div class="top_tips"><p><em class="over">商品抢光了！</em></p><p class="tips01">因商品已经抢光，无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a data-gtype="'+gtype+'" data-gid="'+gid+'" class="y-like item-like active" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">已收藏</p></a></div></div>';
            }else{
                var content = '<div class="top_tips"><p><em class="over">商品抢光了！点击收藏随时关注</em></p><p class="tips01">因商品已经抢光，无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a class="y-like item-like" data-pid="'+gid+'" href="javascript:;"> <em class="heart"></em><p class="like-l">收藏</p></a></div></div>';
            }
        }else{
            if($(this).parents("li").find(".like-ico").hasClass("l-active") || $(this).parents("li").find(".del-ico").length != 0){
                var content = '<div class="top_tips"><p><em class="over">您已收藏，商品开抢前会在手机提醒您开抢！</em></p></div><div class="item-btn clear"><div class="collect-box  fl"><a data-gtype="'+gtype+'" data-gid="'+gid+'" class="y-like item-like active" href="javascript:void(0)"><em class="heart"></em><p class="like-l">已收藏</p></a><p class="like-o"><span class="fl">开抢前5分钟手机提醒</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
            }else{
                var content = '<div class="top_tips"><p><em class="over">商品还未开始，点击收藏随时关注！</em></p></div><div class="item-btn clear"><div class="collect-box  fl"><a class="y-like item-like" data-pid="'+gid+'" href="javascript:;"><em class="heart"></em><p class="like-l">收藏</p></a></div></div>';
            }
        }

        b = new XDLightBox({
            title: "温馨提示",
            lightBoxId: "alert_remind",
            contentHtml: content,
            scroll: false
        });
        b.init();
        return false;
    });

})(jQuery);
(function($) {
	$(".y-like").live('click', function() {
		var pid = $(this).attr("data-pid");
		if(!$.ftxia.dialog.islogin()) return ;
		$.ajax({
			url: FTXIAER.root + '/?m=ajax&a=like',
				type: 'POST',
				data: {
				pid: pid
			},
			dataType: 'json',
			success: function(result){
				if(result.status == 1){
					$.ftxia.tip({content:result.msg, icon:'success'});
				}else if(result.status == 2){
					$.ftxia.tip({content:result.msg, icon:'error'});
				}else{
					$.ftxia.tip({content:result.msg, icon:'error'});
				}
			}
		});
		  
	});

})(jQuery);
$("#sbox").hover(function(){
		$(this).find(".wholike").show();
		
	},function(){
		$(this).find(".wholike").hide();
	});
	
$("#share").hover(function(){
		$(this).find(".share").show();
		
	},function(){
		$(this).find(".share").hide();
	});