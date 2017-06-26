<div class="app-other">
    <ul>
        <li class="normal <if condition='$nav_curr eq index'> active</if> ">
        <a href="{:C('ftx_header_html')}">
        <em class="home"></em>首页</a></li>
        <li class="normal  <if condition='$nav_curr eq fushi'> active</if>">
        <a href="{:U('fushi/index')}">
        <em class=" fushi"></em>服饰折扣</a></li>
        <li class="normal  <if condition='$nav_curr eq muying'> active</if>">
        <a href="{:U('muying/index')}">
        <em class=" muying"></em>母婴折扣</a></li>
        <li class="normal  <if condition='$nav_curr eq jujia'> active</if>">
        <a href="{:U('jujia/index')}">
        <em class=" jujia"></em>居家折扣</a></li>
        <li class="normal  <if condition='$nav_curr eq qita'> active</if>">
        <a href="{:U('qita/index')}">
        <em class=" other"></em>其他折扣</a></li>
		<li class="normal  <if condition='$nav_curr eq special'> active</if>">
        <a href="{:U('special/index')}">
        <em class=" special"></em>精彩专题</a></li>
		<li class="normal  <if condition='$nav_curr eq tehui'> active</if>">
        <a href="{:U('tehui/index')}">
        <em class=" tehui"></em>特惠精品</a></li>
		<li class="normal  <if condition='$nav_curr eq article'> active</if>">
        <a href="{:U('article/index')}">
        <em class=" article"></em>时尚资讯</a></li>
		<li class="normal  <if condition='$nav_curr eq gift'> active</if>">
        <a href="{:U('gift/index')}">
        <em class=" gift"></em>积分商城</a></li>
		<li class="normal  <if condition='$nav_curr eq tag'> active</if>">
        <a href="{:U('tag/index')}">
        <em class=" tag"></em>热门标签</a></li>
		<li class="normal  <if condition='$nav_curr eq meilishuo'> active</if>">
        <a href="{:U('meilishuo/index')}">
        <em class=" meilishuo"></em>逛美丽说</a></li>
        </ul>
    <p><a href="{:U('user/index')}"><em  class="icon-user"></em><br>个人中心</a><a href="{:U('about/index')}"><em class="icon-about"></em><br>关于我们</a></p> 
</div>