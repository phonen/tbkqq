<script>
$(function (){
    var aa = $(".goods-list li").innerWidth();
    $(".goods-list li img").css('height',aa*0.655);
})
</script>
<volist name="items_list" id="val" key="i"> 
		<li>        
		<a class="goods-a" href="{:U('brand/item',array('id'=>$val['id']))}" target="_self">            
		<img src="{:attach(get_thumb($val['pic_url'], '_b'),'item')}">
		
		</a>        
		<a isconvert="1" data-itemid="{$val.num_iid}" target="_self">			
		<h3>{$val.title}</h3>            
		<div class="list-price buy">                
		<span class="price-new">
		<i>￥</i>{$val.coupon_price}
		</span>
		<i class="del">￥{$val.price}</i>                
		<span class="good-btn">去抢购</span>            
		</div>        
		</a>    
		</li> 
		
</volist>