<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Tbkqq\Controller;
use Common\Controller\HomebaseController; 
/**
 * 首页
 */
class IndexController extends HomebaseController {
	
    //首页
	public function index() {
		if(sp_is_user_login()){
			$proxyid = substr($_SESSION['user']['user_login'], 8);
			$items = M("TbkqqTaokeItem")->where(array("status" => "1"))->select();
			foreach ($items as $item) {
				$where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
				$itemurls = M("TbkqqTaokeItemurl")->where($where)->select();
				$urls = "";
				foreach ($itemurls as $itemurl) {
					$urls .= $itemurl['proxyid'] . "  " . $itemurl['shorturl'] . "<br>";
				}
				$item['urls'] = $urls;
				$data[] = $item;
			}
			$this->assign("items", $data);
			$this->display(":index");
		}
    }

	public function item_view(){
		if(sp_is_user_login()){
			$proxyid = strstr($_SESSION['user']['user_login'], 'i');
			$items = M("TbkqqTaokeItem")->where(array("status" => "1"))->select();
			foreach ($items as $item) {
				$where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
				$itemurls = M("TbkqqTaokeItemurl")->where($where)->select();
				$urls = "";
				foreach ($itemurls as $itemurl) {
					$urls .= $itemurl['proxyid'] . "  " . $itemurl['shorturl'] . "<br>";
				}
				$item['urls'] = $urls;
				$data[] = $item;
			}
			$this->assign("items", $data);
			$this->display();
		}
	}

}


