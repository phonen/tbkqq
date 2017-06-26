<div class="app-other">
    <ul>
        <li class="normal <if condition='$nav_curr eq index'> active</if> "> <a href="{:C('ftx_header_html')}"><em class="home"></em>网站首页</a></li>
        <li class="normal  <if condition='$nav_curr eq quan'> active</if>"><a href="{:U('quan/index')}"><em></em>优惠劵</a></li>
      
		<volist name="cate_data" id="bcate">	
		 <if condition="$bcate['pid'] eq 34">
		<li class="normal <if condition="$cid eq $bcate['id']"> active</if>"><a href="{:U('quan/index', array('cid'=>$bcate['id']))}" title="{$bcate.name}"><em></em>{$bcate.name}</a></li>
	</if>
		    </volist> 
		
        </ul>
    <p><a href="{:U('user/index')}"><em  class="icon-user"></em><br>个人中心</a><a href="{:U('about/index')}"><em class="icon-about"></em><br>关于我们</a></p> 
</div>