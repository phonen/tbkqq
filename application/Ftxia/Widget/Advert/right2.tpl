<volist name="ad_list" id="ad">
<dd class="last">
<a href="{$ad.url}" target="_blank" class="'right" title="{$ad.name}">
<img src="{:attach($ad['content'],'banner')}" alt="{$ad.desc}">
</a>
</dd>
</volist>  