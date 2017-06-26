$(function() {	
	
	//左侧浮动导航
    var $navFun = function() {
        var st = $(document).scrollTop(), 
            headh = $("div.head").height(),           
            $nav_classify = $("div.jiu-side-nav");

        if(st > headh){
            $nav_classify.addClass("fixed");
        } else {
            $nav_classify.removeClass("fixed");
        }
    };

    var F_nav_scroll = function () {
        if(navigator.userAgent.indexOf('iPad') > -1){
            return false;
        }      
        if (!window.XMLHttpRequest) {
           return;          
        }else{
            //默认执行一次
            $navFun();
            $(window).bind("scroll", $navFun);
        }
    }
    F_nav_scroll();
	
		//顶部分类浮动
    var $cateFun = function() {
        var st = $(document).scrollTop(), 
            headh = $("div.head").height(),           
            $nav_classify = $("div.jiu-nav-main");

        if(st > headh){
            $nav_classify.addClass("fixed");
        } else {
            $nav_classify.removeClass("fixed");
        }
    };

    var F_cate_scroll = function () {
        if(navigator.userAgent.indexOf('iPad') > -1){
            return false;
        }      
        if (!window.XMLHttpRequest) {
           return;          
        }else{
            //默认执行一次
            $cateFun();
            $(window).bind("scroll", $cateFun);
        }
    }
    F_cate_scroll();
	 
	//友情链接滚动
	var lh = 0;
	var ih = $('.links_list').height();
	var link_scroll = function(){
		setTimeout(function(){
			lh+=20;
			if(lh + 5>=ih){
				lh=0;
			}
			$('.links_list').animate({top:-lh+'px'}, 500);
			link_scroll();
		}, 5000);
	};
	link_scroll();
});

function showTip(a){
	if(a==0){
		$("#hover_bg").hide();
		$("#hover_two").hide();
		//setCookie("home_tip",1,9999999);
		
	}else if(a==1){
		$("#hover_bg").show();
		$("#hover_one").show();
		$(".search_box").css('z-index','10002');
	
	}else if(a==2){
		$(".search_box").css('z-index','1');
		$("#hover_one").hide();
		$("#hover_bg").show();
		$("#hover_two").show();
		$("#zhe_body").css('z-index','10002');
	}
}