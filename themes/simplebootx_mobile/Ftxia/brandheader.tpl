<div class="app-other">
    <ul>
        <li class="normal <if condition='$nav_curr eq index'> active</if> ">
        <a href="{:C('ftx_header_html')}">
        <em class="home"></em>网站首页</a></li>
        <li class="normal  <if condition='$nav_curr eq brand'> active</if>"><a href="{:U('brand/index')}"><em></em>品牌首页</a></li>		
        <volist name="acats" id="bcate">
		<li class="normal <if condition="$bcate['id'] eq $cid"> active </if>"><a href="{:U('brand/cate',array('cid'=>$bcate['id']))}"><em></em>{$bcate.name}</a></li>
		</volist>
        </ul>
    <p><a href="{:U('user/index')}"><em  class="icon-user"></em><br>个人中心</a><a href="{:U('about/index')}"><em class="icon-about"></em><br>关于我们</a></p> 
</div>