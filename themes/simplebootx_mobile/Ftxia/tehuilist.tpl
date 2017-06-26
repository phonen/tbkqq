<script>
$(function (){
    var aa = $(".goods-list li").innerWidth();
    $(".goods-list li img").css('height',aa);	
})
</script>
 <volist name="hot_list" id="item" key="i" mod="4">		<li>        
		<a class="goods-a" href="{:U('tehui/view',array('id'=>$item['id']))}" title="{$item.title}" target="_self">            
		<img src="{$item.pic_url}_290x290.jpg" alt="{$item.title}">
		
		</a> 
		<a href="{:U('tehui/view',array('id'=>$item['id']))}" target="_blank" rel="nofollow">			
		<h3>{$item.title}</h3>            
		<div class="list-price buy">                
		<span class="price-new">
		<i>￥</i>{$item.oneprice}</em>.{$item.twoprice}
		</span>
		<i class="del">￥{$item.price}</i>                
		<span class="good-btn">去抢购</span>            
		</div>        
		</a>
		</li>	
</volist>