<ul class="slide_box" id="sellerSlide">
<volist name="ad_list" id="ad">
<li>
<a href="{$ad.url}" target="_blank">
<img alt="" src="{:attach($ad['content'],'banner')}" alt="{$ad.name}" title="{$ad.desc}">
</a>
</li>
</volist>
</ul>
<div class="slide_prev slide_way" id="sellerPerv"></div>
<div class="slide_next slide_way" id="sellerNext"></div>
<div class="slide_page" id="sellerPage">
<volist name="ad_list" id="ad">
<span class=""></span>
</volist>
</div>