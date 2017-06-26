 <div class="area">
        <ul class="banner">
		<volist name="ad_list" id="ad">
		<li><a href="{$ad.url}" target="_blank"><img src="{:attach($ad['content'],'banner')}"></a></li>
		</volist>        
        </ul>
        <div class="adType">
        </div>
</div>