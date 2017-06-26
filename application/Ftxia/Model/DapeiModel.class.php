<?php
namespace Common\Model;
use Common\Model\CommonModel;
class DapeiModel extends RelationModel
{
    //自动完成
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
    );
    //自动验证
    protected $_validate = array(
        array('title', 'require', '{%dapei_title_empty}'),
    );

    public function addtime()
    {
        return date("Y-m-d H:i:s",time());
    }
	
	public function hits($id){
		$this->where(array('id'=>$id))->setInc('hits',1);
	}
	
	
	
	public function ajax_tb_publish($item) {
		$result['title']			    = $item['title'];
		$result['itemnum']			    = $item['itemnum'];
		$result['one_title']			= $item['one_title'];
		$result['two_title']			= $item['two_title'];
		$result['three_title']			= $item['three_title'];
		$result['four_title']			= $item['four_title'];
		$result['itemtag']			    = $item['itemtag'];
		$result['one_itemtag']		    = $item['one_itemtag'];
		$result['two_itemtag']			= $item['two_itemtag'];
		$result['three_itemtag']	    = $item['three_itemtag'];
		$result['four_itemtag']			= $item['four_itemtag'];
		$result['pic_url']			    = $item['pic_url'];
		$result['one_pic_url']			= $item['one_pic_url'];
		$result['two_pic_url']			= $item['two_pic_url'];
		$result['three_pic_url']		= $item['three_pic_url'];
		$result['four_pic_url']			= $item['four_pic_url'];
		$result['onepic']			    = $item['onepic'];
		$result['twopic']			    = $item['twopic'];
		$result['threepic']			    = $item['threepic'];
		$result['fourpic']			    = $item['fourpic'];
		$result['fivepic']			    = $item['fivepic'];
		$result['cate_id']			    = $item['cate_id'];
		$result['zprice']		        = $item['zprice'];
		$result['one_zprice']		    = $item['one_zprice'];
		$result['two_zprice']		    = $item['two_zprice'];
		$result['three_zprice']		    = $item['three_zprice'];
		$result['fourzprice']		    = $item['fourzprice'];
		$result['price']	            = $item['price'];
		$result['one_price']			= $item['one_price'];
		$result['two_price']			= $item['two_price'];
		$result['three_price']			= $item['three_price'];
		$result['four_price']			= $item['four_price'];
		$result['author']			    = $item['author'];		
		$result['avatar']			    = $item['avatar'];
		$result['info']			        = $item['info'];
		$result['hits']			        = $item['hits'];
		$result['styleid']		        = $item['styleid'];
		$result['item_url']	            = $item['item_url'];		
		$result['click_url']		    = $item['click_url'];
		$result['one_click_url']		= $item['one_click_url'];
		$result['two_click_url']		= $item['two_click_url'];
		$result['three_click_url']		= $item['three_click_url'];
		$result['four_click_url']		= $item['four_click_url'];	
		$robot_setting = F('robot_setting');
        if ($this->where(array('styleid'=>$item['styleid']))->count()) {
		   $result['msg']	= '已经添加!';
           return $result;
        }   
		$item['status'] = 1;
        $this->create($item);
        $item_id = $this->add();
        if ($item_id) {
            return 1;
        } else {
            return 0;
        }
    }
	public function get_tags_by_title($title, $num=8){
        vendor('pscws4.pscws4', '', '.class.php');
        $pscws = new PSCWS4();
        $pscws->set_dict(FTX_DATA_PATH . 'scws/dict.utf8.xdb');
        $pscws->set_rule(FTX_DATA_PATH . 'scws/rules.utf8.ini');
        $pscws->set_ignore(true);
        $pscws->send_text($title);
        $words = $pscws->get_tops($num);
        $pscws->close();
        $tags = array();
        foreach ($words as $val) {
            $tags[] = $val['word'];
        }
        return $tags;
    }
}