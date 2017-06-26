<?php
namespace Common\Model;
use Common\Model\CommonModel;
class DianModel extends CommonModel {
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
		array('pass',1),
    );
    
      
/**
     * ajax发布店铺
     */
    public function ajax_tbd_publish($item) {
		//echo(print_r($item));
		$result['title']			= $item['title'];
		$result['pic_url']			= $item['pic_url'];
		$result['nick']				= $item['nick'];
		$result['sellerId']		    = $item['sellerId'];
		$result['type']			    = $item['type'];
		$result['xy']			    = $item['xy'];
		$result['pf']				= $item['pf'];
		$result['hpl']			    = $item['hpl'];
		$result['dz']		        = $item['dz'];
		$result['shop_url']	        = $item['shop_url'];
        $result['cate_id']	        = $item['cate_id'];		
		$robot_setting = F('robot_setting');		
        //已经存在？
        if ($this->where(array('sellerId'=>$item['sellerId']))->count()) {
           return 0;
        } 
        $this->create($item);
        $item_id = $this->add();
        if ($item_id) {
            return 1;
        } else {
            return 0;
        }
    }
    

}