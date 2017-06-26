<volist name="ad_list" id="ad">
<div id="tan_chuang_div">
<div class="gdwin-bg" style=""></div>
    <div class="gdwin-box" style="">
    <div class="gdwin-box-body">
        <div class="position-c">
            <a class="btn-close"></a>
            <a class="a_close" href="{$ad.url}" target="_blank"><img src="{:attach($ad['content'],'banner')}"></a>
        </div>
    </div>
</div>
</div>
</volist>
<script type="text/javascript">
$("#show-qcodes").hover(function(){
    $("#new-qcodes").css('display','block');
},function(){
    $("#new-qcodes").css('display','none');
});
(function($){    
    var F_gdwin = function(){
        var gdwin_close = function(){
            $("#tan_chuang_div").hide();
            $.cookie("gdwin","1", {
                expires: 0.5,
                path: "/"
            });
        }
        if($.cookie("gdwin") == null){
            $("#tan_chuang_div").show();
            $("div.gdwin-box a.btn-close").on("click",gdwin_close);
            $("div.gdwin-box a.a_close").on("click",gdwin_close);
        }else{
            $("#tan_chuang_div").hide();
        }       
        $("div.gdwin-bg").on("click mousedown",function(e){
            return false;
        })
    }
    F_gdwin();	
})(jQuery);
</script>


