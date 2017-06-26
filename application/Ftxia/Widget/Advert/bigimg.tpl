<div class="top_bar">	
<ul id="banner_list" class="banner" style=""> 
	<volist name="ad_list" id="ad">
	<li style="background-image: url({:attach($ad['content'],'banner')}); z-index: 1; opacity: 1; background-position: 50% 50%; background-repeat: no-repeat no-repeat;"><a target="_blank" title="{$ad.name}" href="{$ad.url}" class="pic center"></a></li>  
	</volist>
</ul>
</div>