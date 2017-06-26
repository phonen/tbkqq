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

class JingxuanController extends FtxiabaseController {

	public function _initialize() {
		parent::_initialize();
		$this->_mod = D('Jingxuan');
	}
	public function index(){
		$sort	= I('sort', 'all', 'trim'); //排序
		$p = I('p',1, 'intval');
		$k		= I('k');
		$page_size = 120;
		$start = $page_size * ($p - 1) ;
		$order = 'add_time desc ';
		switch ($sort){
			case 'new':
				$order.= ', coupon_start_time DESC';
				break;
			case 'price':
				$order.= ', coupon_price ASC';
				break;
			case 'hot':
				$order.= ', volume DESC';
				break;
			case '50':
				$map['coupon_price'] = array('elt','50');
				break;
			case 'all':
				$order = 'ordid asc';
				break;
		}
		if($k){
			if(strpos($k,' ')){
				$karr=split(' ',$k);
				foreach($karr as $kw){
					$like[] = array('like', '%' . $kw . '%');
				}
				$map['title'] = $like;
			}else{
				$map['title'] = array('like', '%' . $k . '%');
			}
			$this->assign('k',$k);
		}
		$map['coupon_end_time'] = array('egt',time());
		$hot_list = $this->_mod->where($map)->order($order)->limit($start . ',' . $page_size)->select();
		$items = array();
		foreach($hot_list as $key=>$val){
			$items['hot_list'][$key]			= $val;
			$coupon_price = $val['coupon_price'];
			$zps = explode('.',$coupon_price);
			$items['hot_list'][$key]['oneprice']		= $zps[0];
			$items['hot_list'][$key]['twoprice']		= $zps[1];
			$price = $val['price'];
			$ps = explode('.',$price);
			if(!$ps[1]){
				$items['hot_list'][$key]['price']		= $ps[0];
			}else{
				$items['hot_list'][$key]['price']		= $price;
			}
		}
		$qiao['sort']=$sort;
		$this->assign('qiao',$qiao);
		$this->assign('p',$p);
		$this->assign('hot_list',$items['hot_list']);
		$count = $this->_mod->where($map)->count();
		$pager = $this->_pager($count, $page_size);
		$this->assign('page', $pager->jshow());
		$this->assign('nav_curr', 'tehui');
		if($sort=='new'){
			$seotitle = '最新上架的精品热卖商品';
		}
		if($sort=='hot'){
			$seotitle = '最受欢迎销量最高的精品热卖商品';
		}
		if($sort=='price'){
			$seotitle = '价格从低到高的精品热卖商品';
		}
		if($sort=='50'){
			$seotitle = '50元以下的精品热卖商品';
		}
		if($sort=='all'){
			$seotitle = '全站精品热卖商品';
		}
		if($k){
			$seotitle = '搜索【'.$k.'】相关的精品热卖商品。- 第'.$p.'页';
		}
		$page_seo=array(
			'title' => $seotitle,
			'keywords' => '天天特价,爱淘宝特惠精品,特价商品',
			'description' => '本站为您推荐的近期淘宝网精品热卖排行榜商品，商品为求薄利多销，件件都是物超所值，赶紧来抢购吧！',
		);
		$this->assign('page_seo', $page_seo);
		$this->display();
	}

	public function view(){
		$id = I('id','', 'intval');
		$item = $this->_mod->where(array('id' => $id))->find();
		!$item && $this->_404();
		header("Location:" .$item['click_url']);
		exit();
	}
}


