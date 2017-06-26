<?php
namespace Ftxia\Model;
use Common\Model\CommonModel;
class TagModel extends CommonModel {
    protected $connection = 'DB_DATAOKE';
    public function ajax_yg_publish($item) {
		$result['name']			= $item['name'];
		$result['ename']			= $item['ename'];
        if ($this->where(array('ename'=>$item['ename']))->count()) {
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


	public function get_tags_by_title($title, $num=1){
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