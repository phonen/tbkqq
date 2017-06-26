$(function() {
    var $navFun = function() {
        var st = $(document).scrollTop(), 
            headh = $("#fnav").offset().top;           
            $nav1_classify = $(".nav-s");	
        if(st > headh){
            $nav1_classify.addClass("fixed");
			
        } else {
            $nav1_classify.removeClass("fixed");
			
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
	
});
