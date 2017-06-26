define(function(require,exports,module){
    return function(){
        /**
     * xiaoxiong，首页价格字数超出，隐藏折扣标签
     * @param {}
     * @time 2014-09-09
     */ 
        var F_hidden_zhe = function(){
            $("li").each(function(){
                  if($(this).find('span.price-current').width() >= 125){
                   $(this).find('.discount').hide();
                }
            })
         }
         F_hidden_zhe();
         
         /**
          * 卷皮九块邮内页点击x关闭
          * @time 2014-12-20
          */
       $(".arrow-up").click(function(){
           $('.bady-tips').hide();
       });
       
        /**
         * fangfang_notclear 此函数在其他地方没有被引用到
         * @time 2014-02-17
         */
        //json对象转换成json串
        var Obj2str = function(o) {
            if (o == undefined) {
                return "";
            }
            var r = [];
            if (typeof o == "string") return "\"" + o.replace(/([\"\\])/g, "\\$1").replace(/(\n)/g, "\\n").replace(/(\r)/g, "\\r").replace(/(\t)/g, "\\t") + "\"";
            if (typeof o == "object") {
                if (!o.sort) {
                    for (var i in o)
                        r.push("\"" + i + "\":" + Obj2str(o[i]));
                    r = "{" + r.join() + "}"
                } else {
                    for (var i = 0; i < o.length; i++)
                        r.push(Obj2str(o[i]))
                    r = "[" + r.join() + "]";
                }
                return r;
            }
            return o.toString().replace(/\"\:/g, '":""');
        }


        //举报 弹框
        XD.JUBAO = function (e) {
            if (XDPROFILE.uid == "") {
                XD.user_handsome_login_init();
                XD.user_handsome_login();
            } else {
                $("#otherReasons").val("");
                $(".other").hide();
                $("#reportAn ").get(0).selectedIndex=0;
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

        /**
         * fangfang 商品内页，用户撰写评论的输入框'#pub_content'
         * @time 2014-02-17
         */
        var str="快来和大伙儿一起讨论吧！";
        $("#pub_content").focus(function(){
             var content =  $("#pub_content").val();
             if(content == str){
                 $("#pub_content").val('');
             }
         }).blur(function(){
            var content =  $("#pub_content").val();
            if(content == ''){
                $("#pub_content").val('快来和大伙儿一起讨论吧！');
            }
        });
        $("#pub_content").keydown(function(){
            var strlen = $("#pub_content").val().length;
            if(strlen>=1){
                $(".pub-area-ft .cur").css('color','#ff6600');
                if(strlen>=140){
                $(".pub-area-ft .counts").css('color','#ff6600');
                }
                else{
                    $(".pub-area-ft .counts").css('color','#999');
                }
            }
            else{
                $(".pub-area-ft .cur").css('color','#999')  
            }
            
        }).keyup(function(){
                var strlen = $("#pub_content").val().length;
                $(".pub-area-ft .cur").text(strlen);
                if(strlen>=1){
                $(".pub-area-ft .cur").css('color','#ff6600');
                if(strlen>=140){
                $(".pub-area-ft .counts").css('color','#ff6600');
                }
                else{
                    $(".pub-area-ft .counts").css('color','#999');
                }
            }
            else{
                $(".pub-area-ft .cur").css('color','#999')  
            }
            });
        /**
         * fangfang 商品内页，举报选择项下拉框是否选择最后一项(即val为8)，显示或隐藏'.other'即其他原因输入框
         * @time 2014-02-17
         */
        $("#reportAn").change( function() {
            if($(this).val()==8) {
                $(".other").show();
            } else {
                $(".other").hide();
            }
        });
        //ajax发送举报内容到后台
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


        var myCarousel = function(){
            var playTimer = null;
            var index = 0;
            var img = $("#slide_index dl dd img");
            var btn = $("#slide_index .slide_page span");
            var oList = $("#slide_index dl");

            //按钮点击切换
            btn.mouseover(function(){
                index = $(this).index();
                var i = $(this).index() - btn.index($("#slide_index .slide_page span.cur"));
                i = i-1;
                if(i>0){
                    for (i; i > 0; i--){
                        oList.find("dd:first").insertAfter(oList.find("dd:last"));
                    }
                }else{
                    for (i; i < 0; i++){
                        oList.find("dd:last").insertBefore(oList.find("dd:first"));
                    }
                }
                oList.stop(false,true);
                oList.css("margin-left",0);
                cutover(function(){
                    oList.find("dd:first").insertAfter(oList.find("dd:last"));
                    oList.css("margin-left",0);
                });

            });

            function next(){
                index++;
                if(index > btn.length - 1){
                    index = 0;
                }
                cutover(function(){
                    oList.find("dd:first").insertAfter(oList.find("dd:last"));
                    oList.css("margin-left",0);
                });
            }

            playTimer = setInterval(next, 3000);

            function cutover(func){
                oList.stop(false,true);
                btn.removeClass("cur");
                btn.eq(index).addClass("cur");
                $("#slide_index .slide_title").html('<a target="_blank" href="'+oList.find("dd:eq(1) a").attr("href")+'">'+oList.find("dd:eq(1) img").data("title")+'</a>');
                $("#slide_index .slide_desc").html('<a target="_blank" href="'+oList.find("dd:eq(1) a").attr("href")+'">'+oList.find("dd:eq(1) img").attr("alt")+'</a>');
                oList.animate({marginLeft:"-310px"},func);
            }

            //鼠标移入展示区停止自动播放
            $("#slide_index").mouseover(function (){
                    clearInterval(playTimer)
                }
            )

            //鼠标离开展示区开始自动播放
            $("#slide_index").mouseout(function (){
                clearInterval(playTimer);
                playTimer = setInterval(next, 3000)
            });
        }

        if($("#slide_index").size() > 0 && $("#slide_index dd").size() > 1){
            $("#slide_index a").click(function(){
                var title = $(".slide_desc a").text();
                if (ga) ga('send', 'event', '首页广告位', title, ' ');
            });
            myCarousel();
        }




        //卷皮列表抢光了弹窗
        var jump_alert = function(){
            var server_time = $(this).parents("li").attr("ntime");
            //双十二活动期间可以出站
            if((server_time<1418263200)&&(server_time>1418400000)) {
                $(".goods-list li.dojump").each(function () {
                    $(this).find("a").attr("href", "javascript:;");
                });
            }
            if(typeof(server_time) =="undefined"){
                server_time = XDTOOL.getCookie('server_time');
            }
            if((server_time<1418263200)&&(server_time>1418400000)) {
                if(parseFloat($(".show_body").find(".jd-current").text()) >= 10 && $(".show_body .item-btn .btn").hasClass("start")){
                    $(".show_body .show_bgimg a,.show_body .item-btn .btn").attr("href","javascript:;");
                }
            }

            $(".goods-list .btn.gone").click(function(){
                if($(this).parents("li").find(".go-quan").length != 0){
					//优惠卷跳出
                    return true;
                }
                
                if($(this).find('a').attr('href').match(/url/i)){
					//采集出站
					return true;
				}

				if($(this).find('a').attr('href').match(/jifen/i)){
					//积分兑换抽奖去内页
					return true;
				}

                var server_time = $(this).parents("li").attr("ntime");
                //双十二活动期间可以出站
                if((server_time>1418263200)&&(server_time<1418400000)) {
					return true;
				}

                var link = $(this).parents("li").find(".good-title a").attr("href");
                var gid = $(this).parents("li").attr("id");
                var gtype = $(this).parents("li").attr("gtype");
                var desc = '<p><em class="over">商品抢光了，看<a href="'+link+'" target="_blank">商品详情</a>！</em></p>';
                //如果是锁定商品就去掉查看商品详情
                if(!$(this).find('a').attr('href').match(/http/i)){
                	desc = '<p><em class="over">商品抢光了</em></p>';
				}
                if(gtype==3){
                    var content = '<div class="top_tips">商品抢光了<p class="tips01">因商品已经抢光，无法再次购买</p><p class="tips01">好商品要眼疾手快！不然就被别人抢走了~</p></div>';
                }else{
                	 if($(this).parents("li").find(".like-ico").hasClass("l-active") || $(this).parents("li").find(".del-ico").length != 0){
                         var content = '<div class="top_tips">'+desc+'<p class="tips01">因商品已经抢光,无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a  gtype="'+gtype+'" data="'+gid+'" class="item-like active" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">已收藏</p></a><p class="like-o"><span class="fl">随时留意您的手机哦</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
                     }else{
                         var content = '<div class="top_tips">'+desc+'<p class="tips01">因商品已经抢光，无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a gtype="'+gtype+'" data="'+gid+'" class="item-like" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">收藏</p></a><p class="like-o"><span class="fl">下次开抢可提醒您</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
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
            
            
            //卷皮内页抢光了弹窗
            $(".show_body .gone").click(function(){
                if($(this).parents(".show_body").find(".quan").length != 0){
                    return true;
                }

                
                if($(this).attr('href').match(/url/i)){
					//采集出站
					return true;
				}
                var gid = $(this).parents(".show_body").find(".item-like").attr("data");
                var capture =  $(this).parents(".show_body").find(".item-like").attr("capture");
                var gtype = capture == 2 ? 1 : 2; //1，普通商品 2采集商品
                
                if($(this).parents(".show_body").find(".item-like").hasClass("active")){
                    var content = '<div class="top_tips"><p><em class="over">商品抢光了！</em></p><p class="tips01">因商品已经抢光，无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a gtype="'+gtype+'" data="'+gid+'" class="item-like active" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">已收藏</p></a><p class="like-o"><span class="fl">随时留意您的手机哦</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
                }else{
                    var content = '<div class="top_tips"><p><em class="over">商品抢光了！</em></p><p class="tips01">因商品已经抢光，无法跳转到淘宝下单</p></div><div class="item-btn clear"><div class="collect-box  fl"><a gtype="'+gtype+'" data="'+gid+'" class="item-like" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">收藏</p></a><p class="like-o"><span class="fl">下次开抢可提醒您</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
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
            
            //卷皮内页提醒
            $(".show_body .start").click(function(){
            	if($(this).parents(".show_body").find(".item-btn a:eq(0)").text() != "明日10:00" || parseFloat($(this).parents(".show_body").find(".jd-current").text()) < 10){
                    return true;
                }
                var gid = $(this).parents(".show_body").find(".item-like").attr("data");
                if($(this).parents(".show_body").find(".item-like").hasClass("active")){
                    var content = '<div class="top_tips"><p><em class="over">您已收藏，商品开抢前会在手机提醒您开抢！</em></p></div><div class="item-btn clear"><div class="collect-box  fl"><a data="'+gid+'" class="item-like active" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">已收藏</p></a><p class="like-o"><span class="fl">随时留意您的手机哦</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
                }else{
                    var content = '<div class="top_tips"><p><em class="over">商品还未开始，收藏享开抢提醒！</em></p></div><div class="item-btn clear"><div class="collect-box  fl"><a data="'+gid+'" class="item-like" href="javascript:void(0)"> <em class="heart"></em><p class="like-l">收藏</p></a><p class="like-o"><span class="fl">下次开抢可提醒您</span><a href="'+__URL_JUANPI__+'/apps#jky" target="_blank" class="phone fr">手机端下载</a></p></div></div>';
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
        }
//        jump_alert();
    }
});

