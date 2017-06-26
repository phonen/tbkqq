/**
 * Created with JetBrains WebStorm.
 * User: jimi
 * Date: 12-17
 * Time: 下午15:11
 *
 */
(function(a) {
   

    // 顶部 ctr+d 收藏start
    var F_totoptips = function(){
        var F_isMac = function(){
            if(navigator.userAgent.indexOf("Mac") > -1){
                $(".totop-tips strong").html("Commond+d");
            }else{
                $('body').on("keydown",function(e){
                    if(e.ctrlKey && 68 == e.keyCode) {
                        ever_open();
                        return true;
                    }
                });
            }
        }
        var tips_close = function(){
            $("div.totop-tips").hide();
        }
        var ever_open = function(){
            XDTOOL.setCookie("toptips","1", {
                expires: 999,
                path: "/"
            });
            tips_close();
        }
        if(XDTOOL.getCookie("toptips").length == 0){
            $("div.totop-tips").show();
            $(".jiu-side-nav").css("top","196px");
            var $tips_a = $("div.totop-tips a")
            $tips_a.on("click",ever_open);
            $("div.totop-tips .closet").on("click",tips_close);
            F_isMac();
        }else{
            $("div.totop-tips").hide();
        }
    }
    if($(".main").data("isnew") == 1){
        F_totoptips();
    }
    // 顶部 ctr+d 收藏end

    //新版2014-12-17 改版提示换装banner start
    var alertBar = function(){
        var timestamp = Date.parse(new Date());
        var banDiv ='<div class="alert-activity">'+
                        '<p class="activity-ceng"></p>'+
                        '<div class="links" href="/">'+
                        '<a class="go-new"  target=""></a>'+
                        '<a class="back-old" href=""></a>'+
                        '<p>5s自动关闭</p>'+
                        '</div>'+
                    '</div>';
        //老用户并且没点过并且一个星期有效
        if($(".main").data("isnew") == 0 && !XDTOOL.getCookie("newbar") && timestamp < 1419955200000){
            $('.side_right').after(banDiv);
            $(".alert-activity .go-new ,.activity-ceng").click(function(){
                $(".alert-activity").hide();
            });
            XDTOOL.setCookie("newbar", '1', {
                expires: 86400*7,
                path: "/"
            });
            var int=self.setInterval(function(){
                $(".alert-activity").hide();
            },5000)

        }
    }
    alertBar();
    //新版2014-12-17 改版banner end
})(jQuery);