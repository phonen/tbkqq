<div class="app-other">
    <ul>
        <li class="normal <if condition='$nav_curr eq index'> active</if> ">
        <a href="{:C('ftx_header_html')}">
        <em class="home"></em>网站首页</a></li>
        <li class="normal  <if condition='$nav_curr eq tehui'> active</if>"><a href="{:U('tehui/index')}"><em></em>特惠精品</a></li>
		
       
		<notempty name="subnav"> 
		<volist name="subnav" id="bcate">
		<li class="normal <if condition="($bcate['id'] eq $cid) OR ($cateid eq $bcate['id'])"> active</if>"><a href="{:U('tehui/cate',array('cid'=>$bcate['id']))}"><em></em>{$bcate.name}</a></li>
		</volist>
		<else />
		<volist name="acats" id="vol">
		<li class="normal <if condition="($vol['id'] eq $cid) OR ($cateid eq $vol['id'])"> active</if>"><a href="{:U('tehui/cate',array('cid'=>$vol['id']))}"><em></em>{$vol.name}</a></li>
		</volist>
		</notempty>
        </ul>
    <p><a href="{:U('user/index')}"><em  class="icon-user"></em><br>个人中心</a><a href="{:U('about/index')}"><em class="icon-about"></em><br>关于我们</a></p> 
</div>