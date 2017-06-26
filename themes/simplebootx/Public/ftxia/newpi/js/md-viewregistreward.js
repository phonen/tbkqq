/**
 * Created with JetBrains WebStorm.
 * User: leilei
 * Date: 12-11-27
 * Time: 下午10:06
 * To change this template use File | Settings | File Templates.
 */
(function($){
    $.fn.viewregistreward = function(){
        var getCookie = function (a) {
            a = document.cookie.match(RegExp("(^| )" + a + "=([^;]*)(;|$)"));
            if (a != null){
                return unescape(a[2]);
            } else {
                return "";
            }
        };
        return this.each(function(){
			var nomoreviewreward = getCookie("nomoreviewreward");
			var u = getCookie("recomU");
			//
            if(nomoreviewreward==''&&u!=''){
                $.ajax({
                    url:__URL_MEMBER__+'/public/viewreward',
                    dataType:'jsonp',
                    data:'',
                    jsonp:'callback',
                    complete:function(result) {
                           if(result.code=1001){
                               //console.log("reward ok");
                           } else {
                               //console.log("reward fail");
                           }
                    }
                });
            }
        });
    }
})(jQuery);
setTimeout(function(){$("body").
    viewregistreward();},500);