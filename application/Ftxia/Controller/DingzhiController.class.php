<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Ftxia\Controller;
use Common\Controller\FtxiabaseController;

class DingzhiController extends FtxiabaseController {
	
    //首页

	public function _initialize() {
		parent::_initialize();
		$this->_mod = M('TbkqqTaokeItem');

		$this->site_setting = get_site_setting();

	}
	/**
	 ** 首页（全部）
	 **/

	public function _empty() {
		$this->index();
	}
	public function index() {
		$appname = C("SITE_APPNAME");
		$proxyid = $_GET['u'];

		$site = M("TbkqqProxy")->where(array("proxy"=>$appname . $proxyid))->find();

		$items_mod = M('TbkqqTaokeItem');
		$order = "no desc";
		$map = " status='1'";
		$items_list = $items_mod->where($map)->order($order)->select();
		$items = array();
		$pagecount = 0;
		foreach ($items_list as $key => $val) {
			$items[$key] = $val;
			$items[$key]['class'] = $this->_mod->status($val['status'], $val['coupon_start_time'], $val['coupon_end_time']);
			$items[$key]['zk'] = round(($val['coupon_price'] / $val['price']) * 10, 1);
			if (!$val['click_url']) {
				$items[$key]['click_url'] = U('jump/index', array(
					'id' => $val['id']
				));
			}
			if ($val['coupon_start_time'] > time()) {
				$items[$key]['click_url'] = U('item/index', array(
					'id' => $val['id']
				));
				$items[$key]['timeleft'] = $val['coupon_start_time'] - time();
			} else {
				$items[$key]['timeleft'] = $val['coupon_end_time'] - time();
			}
			$items[$key]['ccid'] = $val['cate_id'];
			if (isset($val['cate_id'])) {
				$items[$key]['cname'] = D('ItemsCate')->where(array( 'id' => $val['cate_id']))->getField('name');
			}
			$items[$key]['cate_name'] = $cate_list['p'][$val['cate_id']]['name'];
			//$url = U('item/index', array( 'id' => $val['id'] ));
			//$items[$key]['url'] = urlencode($url);
			$items[$key]['urltitle'] = urlencode($val['item']);
			$items[$key]['price'] = number_format($val['price'], 1);
			$items[$key]['coupon_price'] = number_format($val['coupon_price'], 1);
			$pagecount++;
		}
		$this->assign('pagecount', $pagecount);
		F('items_list', $items);
		$this->assign('items_list', $items);

		$this->assign('index_info', $index_info);
		$count = $items_mod->where($map)->count();
		$page = $this->page($count, 100);
		$this->assign('page', $page->show());

		$this->assign('pager','so');
		$this->assign('site', $site);
		$this->display(":search");

	}
}


