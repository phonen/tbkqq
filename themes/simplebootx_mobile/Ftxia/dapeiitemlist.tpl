<volist name="dapei_list" id="item" key="i" mod="5">
		<li class="boxt ks-waterfall">        
		<a class="goods-a" href="{:U('dapei/read',array('id'=>$item['id']))}" title="{$item.info}" target="_self">            
		<img src="{$item['pic_url']}" alt="{$item.title}">
		
		</a>        
		<a href="{$item.click_url}" target="_self" rel="nofollow">			
		<h4>{$item.title}</h4>            
		<div class="list-price buy">                
		<span class="price-new">
		<i>{$item.itemnum}件搭配宝贝</i>
		</span>		             
		<span class="good-btn">去看看</span>            
		</div>        
		</a>    
		</li> 
</volist>