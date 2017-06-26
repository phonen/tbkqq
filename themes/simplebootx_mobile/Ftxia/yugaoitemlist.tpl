<script>
$(function (){
    var aa = $(".goods-list li").innerWidth();
    $(".goods-list li img").css('height',aa);
})
</script>
<volist name='items_list' id='item'>        
        <li>        
		<a class="goods-a" href="{:U('item/index',array('id'=>$item['id']))}" target="_self">            
		<img src="{:attach(get_thumb($item['pic_url'], '_b'),'item')}">
		{$item.add_time|wapnewicon}
		</a>        
		<a isconvert="1" data-itemid="{$item.num_iid}" target="_self">			
		<h3>{$item.title}</h3>            
		<div class="list-price start">                
		<span class="price-new">
		<i>￥</i>{$item.coupon_price}
		</span>
		<i class="del">￥{$item.price}</i>                
		<span class="good-btn">{$item.coupon_start_time|date="n-j H:i",###}</span>            
		</div>        
		</a>    
		</li> 		
</volist>