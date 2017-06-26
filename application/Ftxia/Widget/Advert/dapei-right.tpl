<volist name="ad_list" id="ad">
<div style="overflow:hidden;">
    <a href="{$ad.url}" target="_blank">
      <img src="{:attach($ad['content'],'banner')}" width="350px" height="280px" alt="{$ad.name}" title="{$ad.desc}">
    </a>
  </div>
</volist>
