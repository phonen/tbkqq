<?php
namespace Common\Model;
use Common\Model\CommonModel;
class BrandModel extends CommonModel {
    protected $connection = 'DB_DATAOKE';
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
		array('pass',0),
    );
    
      
/**
     * ajax发布店铺
     */
    public function ajax_bcj_publish($item) {		
		$result['title']			= $item['title'];
		$result['logo']			    = $item['logo'];
		$result['pic']				= $item['pic'];
		$result['sellerId']		    = $item['sellerId'];
		$result['banner']			= $item['banner'];
		$result['newpic']			= $item['xy'];
		$result['desc']				= $item['desc'];
		$result['activityId']	    = $item['activityId'];		
        $result['cate_id']	        = $item['cate_id'];	
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