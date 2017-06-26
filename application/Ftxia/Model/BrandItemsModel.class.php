<?php
namespace Common\Model;
use Common\Model\CommonModel;
class BrandItemsModel  extends CommonModel {

    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
		array('pass',1),
    );
    
      
/**
     * ajax发布店铺
     */
    public function ajax_brand_publish($item) {		
		$result['title']			= $item['title'];
		$result['pic_url']			= $item['pic_url'];
		$result['num_iid']			= $item['num_iid'];
		$result['sellerId']		    = $item['sellerId'];
		$result['click_url']		= $item['click_url'];
		$result['volume']			= $item['volume'];
		$result['cu']				= $item['cu'];
		$result['onetag']			= $item['onetag'];
		$result['twotag']		    = $item['twotag'];
		$result['threetag']	        = $item['threetag'];
        $result['cate_id']	        = $item['cate_id'];	
		$result['intro']	        = $item['intro'];		
		$result['price']	        = $item['price'];
		$result['coupon_price']	    = $item['coupon_price'];
		$result['coupon_rate']	    = $item['coupon_rate'];
		$result['activityId']	    = $item['activityId'];
		
		
        //已经存在？
        if ($this->where(array('num_iid'=>$item['num_iid']))->count()) {
           return 0;
        } 
        $item['pass'] = 1; 
        $this->create($item);
        $item_id = $this->add();
        if ($item_id) {
            return 1;
        } else {
            return 0;
        }
    }
    

}