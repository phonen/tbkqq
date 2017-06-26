<div class="app-other">
    <ul>
        <li class="normal <if condition='$nav_curr eq index'> active</if> ">
        <a href="{:C('ftx_header_html')}">
        <em class="home"></em>网站首页</a></li>
        <li class="normal  <if condition='$nav_curr eq gift'> active</if>"><a href="{:U('gift/index')}"><em></em>积分商城首页</a></li>		
        <volist name="cate_list" id="cate">
		<li class="normal <if condition="$cate['id'] eq $cid"> active</if>"><a href="{:U('gift/cate',array('cid'=>$cate['id']))}"><em></em>{$cate.name}</a></li>
		</volist>
        </ul>
    <p><a href="{:U('user/index')}"><em  class="icon-user"></em><br>个人中心</a><a href="{:U('about/index')}"><em class="icon-about"></em><br>关于我们</a></p> 
</div>