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
/**
 * 首页
 */
class ItemController extends FtxiabaseController {
	public function _initialize() {
		parent::_initialize();
		$this->_mod = D('Items');
		$this->_brandmod = D('Brand');
		$this->_cate_mod = D('ItemsCate');
		$this->site_setting = get_site_setting();
		//C('DATA_CACHE_TIME',$this->site_setting['ftx_site_cachetime']);
	}
	
    //首页
	public function index() {

		$appname = C("SITE_APPNAME");
		$proxyid = $_GET['u'];

		$id = $_GET['id'];

		$site = M("TbkqqProxy")->where(array("proxy"=>$appname . $proxyid))->find();

		if (false === $cate_data = F('cate_data')) {
			$cate_data = $this->_cate_mod->cate_data_cache();
		}
		$this->assign('cate_data', $cate_data); //分类


		$item = $this->_mod->where(array("id"=>$id))->find();
		$item['class']	= $this->_mod->status($item['status'],$item['coupon_start_time'],$item['coupon_end_time']);
		$where = array("cate_id"=>$item['cate_id']);
		$order = "ordid asc, volume DESC";
		$items_list = $this->_mod->where($where)->order($order)->limit(10)->select();
		$this->assign("items_list",$items_list);

		//	$data = get_url_data($item['quanurl']);
		//$item['click_url'] = "http://uland.taobao.com/coupon/edetail?activityId=" .$data['activityId'] ."&pid=" . $site['pid'] ."&itemId=" . $item['num_iid'] ."&src=qhkj_dtkp&dx=1";
		$item['click_url'] = str_replace($this->site_setting['ftx_yhq_pid'],$site['pid'],$item['quanurl']);
		$item['quanurl'] = $item['click_url'];
		$this->assign("item",$item);
		$this->assign('site', $site);
			$this->display(":item");


	}

	public function test() {
		$appname = C("SITE_APPNAME");
		$proxyid = $_GET['u'];
		$cid = $_GET['cid'];
		$id = $_GET['id'];
		$dataoke_model = M('Items','cmf_','DB_DATAOKE');
		$site = M("TbkqqProxy")->where(array("proxy"=>$appname . $proxyid))->find();
		$this->assign("site",$site);


		if($id == '')
		{

			$class_model =  M('TbkqqClass','cmf_','DB_DATAOKE');
			$class = $class_model->select();
			$content['class'] = $class;

			if($cid == '' || $cid == '0')		$where_cid= "1=1";
			else $where_cid = "cid=$cid";

			$where_ands = array($where_cid);
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
			);
			if(IS_POST){

				foreach ($fields as $param =>$val){
					if (isset($_POST[$param]) && !empty($_POST[$param])) {
						$operator=$val['operator'];
						$field   =$val['field'];
						$get=$_POST[$param];
						$_GET[$param]=$get;
						if($operator=="like"){
							$get="%$get%";
						}
						array_push($where_ands, "$field $operator '$get'");
					}
				}
			}else{
				foreach ($fields as $param =>$val){
					if (isset($_GET[$param]) && !empty($_GET[$param])) {
						$operator=$val['operator'];
						$field   =$val['field'];
						$get=$_GET[$param];
						if($operator=="like"){
							$get="%$get%";
						}
						array_push($where_ands, "$field $operator '$get'");
					}
				}
			}

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();
			$page = $_REQUEST['page'];
			$pagesize = 100;
			if($page == ''){
				import('Page');


				$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));

				$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();


			}
			else{
				$data = "";
				$items = $dataoke_model->where($where)->order("id desc")->limit(($page-1)*$pagesize . ',' . $pagesize)->select();
				foreach($items as $item){
					$data .= " <div class=\"goods-item\">\r\n        <a data-transition=\"slide\" data-qtk-url=\"/index.php?id=" . $item['id'] . "\" class=\"img QtkSelfClick cnzzCounter\" data-cnzz-type=\"1\" data-cnzz=\"" . $item['id'] . "\">\r\n <span class=\"today-wrapper\">\r\n  <span>今日</span>\r\n <span>上新</span>\r\n </span>\r\n  \r\n            <span class=\"coupon-wrapper\">\r\n                    <span class=\"coupon\" style=\"color: #EDFF00;\">独家券</span>\r\n                    <span class=\"price\">" . $item['quan_price'] . "元</span>\r\n                </span>\r\n            <img\r\n                src=\"" . $item['img'] ."_230x230.jpg\"\r\n                alt=\"\">\r\n        </a>\r\n        <a data-transition=\"slide\"  data-qtk-url=\"/index.php?id=" . $item['id'] ."\" class=\"title QtkSelfClick cnzzCounter\" data-cnzz-type=\"1\" data-cnzz=\"".$item['id'] . "\">\r\n            <div class=\"text\">" . $item['title'] . "</div>\r\n        </a>\r\n        <div class=\"price-wrapper\">\r\n            <span class=\"text\">券后</span>\r\n            <span class=\"price\">￥" . $item['price'] . "</span>\r\n            <div class=\"sold-wrapper\">\r\n                <span class=\"sold-num\" style=\"font-size: 10px;\">" .$item['sales_num'] . "</span>\r\n                <span class=\"text\" style=\"font-size: 10px;\">人已买</span>\r\n            </div>\r\n        </div>\r\n    </div>\r\n";
				}
				$result = array("status"=>0,"data"=>$data);
				echo $json = json_encode($result);
				exit();
			}
			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			$this->display();
		}
		else {

			$item = $dataoke_model->where(array("id"=>$id))->find();
			$data = get_url_data($item['quan_link']);
			$item['quan_link'] = "http://uland.taobao.com/coupon/edetail?activityId=" .$data['activity_id'] ."&pid=" . $site['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=1";
			$this->assign("item",$item);
			$this->display("item");
		}

    }

	public function mpage(){
		$appname = C("SITE_APPNAME");
		$id = $_GET['id'];
		$proxyid = $_GET['u'];
		$dataoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
		$site = M("TbkqqProxy")->where(array("proxy"=>$appname . $proxyid))->find();
		$this->assign("site",$site);
		$item = $dataoke_model->where(array("id"=>$id))->find();
		$this->assign("item",$item);
		$this->display("mpage");
	}

	public function dmpage(){
		$appname = C("SITE_APPNAME");
		$id = $_GET['id'];
		$proxyid = $_GET['u'];
		$dataoke_model = M('TbkqqTaokeItem');
		$site = M("TbkqqProxy")->where(array("proxy"=>$appname . $proxyid))->find();
		$this->assign("site",$site);

		$item = $dataoke_model->where(array("id"=>$id))->find();
		preg_match('/(https?\:\/\/.*)/',$item['memo'],$quan_link);
		$item['quan_link'] = $quan_link[0];

		/*
        $item['memo'] = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches) {
            return convert_a($matches[0]);
        },$item['memo']);
*/
		$itemurl = M("TbkqqTaokeItemurl")->where(array("iid"=>$item['iid'],"proxyid"=>$proxyid))->find();
//		$item = array_merge($item,$itemurl);
		$item['shorturl'] = $itemurl['shorturl'];
		$item['taokl'] = $itemurl['taokl'];
		$this->assign("item",$item);
		$this->display("dmpage");
	}

	public function dingzhi() {
		$appname = C("SITE_APPNAME");
		$proxyid = $_GET['u'];

		$id = $_GET['id'];
		$dataoke_model = M('TbkqqTaokeItem');
		$site = M("TbkqqProxy")->where(array("proxy"=>$appname . $proxyid))->find();
		$this->assign("site",$site);
		$class_model =  M('TbkqqClass','cmf_','DB_DATAOKE');
		$class = $class_model->select();
		$cname = array();
		foreach($class as $cdata){
			$cname[$cdata['cid']] = $cdata['class'];
		}
		$this->assign("class",$cname);
		if($id == '')
		{

			$items = $dataoke_model->where("no<=100 and status='1'")->order("id desc")->select();

			$content['items']=$items;

			$this->assign("lists",$content);
			$this->display();
		}
		else {

			$item = $dataoke_model->where(array("id"=>$id))->find();
			preg_match('/(https?\:\/\/.*)/',$item['memo'],$quan_link);
			$item['quan_link'] = $quan_link[0];
			/*
			$item['memo'] = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches) {
				return convert_a($matches[0]);
			},$item['memo']);
*/
			$itemurl = M("TbkqqTaokeItemurl")->where(array("iid"=>$item['iid'],"proxyid"=>$proxyid))->find();
			//$item = array_merge($item,$itemurl);
			$item['shorturl'] = $itemurl['shorturl'];
			$item['taokl'] = $itemurl['taokl'];
			$this->assign("item",$item);
			$this->display("dingzhi_item");
		}

	}

}


