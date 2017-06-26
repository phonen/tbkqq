<volist name="ad_list" id="ad">
<div style="float:left;margin-right:10px;">
    <a href="{$ad.url}" target="_blank">
      <img src="{:attach($ad['content'],'banner')}" width="840px" height="280px" alt="{$ad.name}" title="{$ad.desc}">
    </a>
  </div>
</volist>
