<link type="text/css" rel="stylesheet" href="__STATIC__/jwap/css/articlelist.css" />
<div class="index">
<div class="main center clear mt1">
<div class="pleft pleft2" style="margin-right:10px">
<div class="latestnews listmain">
   <dl class="tbox light tbox2">    
	<dd class="l">	
	<volist name="article_list" id="item">
	<div class="onem">
		 <div class="item-top">
				<a href="{:U('article/read',array('id'=>$item['id']))}" target="_self"><img width="100" src="{$item.mpic}"></a>
				<strong><a href="{:U('article/read',array('id'=>$item['id']))}" target="_self">{$item.title}</a></strong>
				<p><a href="{:U('article/read',array('id'=>$item['id']))}" target="_self">{$item.seo_desc}...</a></p>
		 </div>
		 </div>
	</volist>	   
    </dd>
   </dl>
</div>
 </div>

</div>
</div>