<!DOCTYPE html>
<html>
<head>
<include file='public:head'/>
<style>
.main_wrap {_padding-left:22px; background: url(image/left_cur.jpg) repeat-y;padding-bottom: 15px;margin-top: 15px;}
/*跳转提示*/
.msg_wrap{ min-height: 220px; height: 220px;}
.message_box{margin:60px auto;width:100%;border:3px solid #F2F2F2;background:#FFF;}
.message_box a{ color: #369;}
.msg_right,.msg_error,.msg_alert{padding:20px 0 ;min-height:40px;height:auto !important;height:40px;line-height:160%;background:url(images/msg_bg.png) no-repeat 10px -592px;font-size:14px;}
.msg_error{ background-position: 10px -492px;}
.msg_alert{ background-position: 10px -392px;}
.msg_btnleft{margin-top:8px;}
</style>
</head>
<body>
<include file="public:header" />
<div class="main" style="text-align: center;">
<div class="app" style="text-align: center;">
<div id="head">

                <div class="fixtop">
                    <span id="t-find"><a href="{:U('index/index')}" class="btn btn-left btn-back02" title="返回网站首页"></a></span>
                    <span id="t-index">{$message}</span>
                    <span id="t-user"></span>
                </div>
            </div>
    <div class="msg_wraps">
        <div class="message_box">
            <div class="msg_error">
                <p>{$message}</p>
                <p class="msg_btnleft"><a href="{$jumpUrl}">{:L('msg_jump_desc')}</a></p>
            </div>
            <script language="javascript">
                setTimeout("location.href='{$jumpUrl}';",{$waitSecond}*1000);
            </script>
        </div>
    </div>
	<include file="public:footer" />
</div>
</div>

</body>
</html>