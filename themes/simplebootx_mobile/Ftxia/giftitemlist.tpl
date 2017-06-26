<script>
$(function (){
    var aa = $(".goods-list li").innerWidth();
    $(".goods-list li img").css('height',aa);
})
</script>
<volist name="item_list" id="item" mod="4">
<DL class="rs-item pg1" data-mid="{$item['id']}">

		<li>        
		<a class="goods-a" href="{:U('gift/detail', array('id'=>$item['id']))}" target="_self">            
		<img src="{:attach(str_replace('_s.'.array_pop(explode('.', $item['img'])), '_b.'.array_pop(explode('.', $item['img'])), $item['img']), 'score_item')}">  
			
		</a>        
		<a href="{:U('gift/detail', array('id'=>$item['id']))}" target="_self">			
		<h3>{$item.title}</h3>
		<div class="list-prices start">                
		<div class="price-news">
		<span class="fl">价值：<i class="del">￥{$item.price}</i></span> 
		<span class="fr">份数：<i>{$item.stock}</i></span> 
		</div> 
		<div class="goc">
		<span class="good-score">{$item.score}<em class="unit">积分</em></span> 
		<span class="good-btns start">{$item.start_time|date="n-j H:i",###}</span> 
		</div>
		</div>
		</a>    
		</li> 
		</if>
		<if condition="$item.class eq 'buy'">
		<li>        
		<a class="goods-a" href="{:U('gift/detail', array('id'=>$item['id']))}" target="_self">            
		<img src="{:attach(str_replace('_s.'.array_pop(explode('.', $item['img'])), '_b.'.array_pop(explode('.', $item['img'])), $item['img']), 'score_item')}">
		
		</a> 		
		<a href="{:U('gift/detail', array('id'=>$item['id']))}" target="_self">			
		<h3>{$item.title}</h3>            
		<div class="list-prices buy">                
		<div class="price-news">
		<span class="fl">价值：<i class="del">￥{$item.price}</i></span> 
		<span class="fr">份数：<i>{$item.stock}</i></span> 
		</div> 
		<div class="goc">
		<span class="good-score">{$item.score}<em class="unit">积分</em></span> 
		<span class="good-btns">去兑换</span> 
		</div>
		</div>
		</a>        
		</li> 
		</if>
		<if condition="$item.class eq 'gone'">
		<li>        
		<a class="goods-a" href="javascript:;">            
		<img src="{:attach(str_replace('_s.'.array_pop(explode('.', $item['img'])), '_b.'.array_pop(explode('.', $item['img'])), $item['img']), 'score_item')}">   
		<span class="buy-over"></span>
		</a>        
		<a href="javascript:;" target="_self">			
		<h3>{$item.title}</h3>            
		<div class="list-prices end">                
		<div class="price-news">
		<span class="fl">价值：<i class="del">￥{$item.price}</i></span> 
		<span class="fr">份数：<i>{$item.stock}</i></span> 
		</div> 
		<div class="goc">
		<span class="good-score">{$item.score}<em class="unit">积分</em></span> 
		<span class="good-btns end">已结束</span> 
		</div>
		</div>        
		</a>    
		</li> 
		</if>
</volist>