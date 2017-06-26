<div class="app-other">
    <ul>
        <li class="normal <if condition='$nav_curr eq index'> active</if> ">
        <a href="{:C('ftx_header_html')}">
        <em class="home"></em>首页</a></li>
        <li class="normal  <if condition='$nav_curr eq article'> active</if>"><a href="{:U('article/index')}"><em></em>资讯首页</a></li>
        <ftx:article type="cate">
	    <volist name="data" id="bcate">
		<li class="normal <if condition="$bcate['id'] eq $cat['id']"> active </if>"><a href="{:U('article/cate',array('id'=>$bcate['id']))}"><em></em>{$bcate.name}</a></li>
		</volist>	
        </ftx:article> 
        </ul>
    <p><a href="{:U('user/index')}"><em  class="icon-user"></em><br>个人中心</a><a href="{:U('about/index')}"><em class="icon-about"></em><br>关于我们</a></p> 
</div>