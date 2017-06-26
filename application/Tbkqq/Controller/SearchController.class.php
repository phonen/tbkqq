<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
/**
 * 搜索结果页面
 */
namespace Tbkqq\Controller;
use Common\Controller\HomebaseController;
class SearchController extends HomebaseController {
    //文章内页
    public function index() {
    	$_GET = array_merge($_GET, $_POST);
		$k = I("get.keyword");
		
		if (empty($k)) {
			$this -> error("关键词不能为空！请重新输入！");
		}

		if(sp_is_user_login()){

			$proxyid = substr($_SESSION['user']['user_login'],strlen(C('SITE_APPNAME')));
			$taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
			$dataoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
			$baseurl = "http://dwz." . C("BASE_DOMAIN");
			$imgurl = "http://img." . C("BASE_DOMAIN");
			$order = "id desc";
			$proxy = M("TbkqqTaokeMedia")->where(array("proxy"=>$_SESSION['user']['user_login']))->order("rand()")->find();
			$mm1 = "";
			$mm2 = "";

			if($proxyid == '001' || $proxyid == '0001') $www = 'www';
			else $www = $proxyid;
			$mm3 = "\n------------------------------
省钱网站：http://" . $www . "." .  C("BASE_DOMAIN") ;
			$mm4 = "";
			$where = "item like '%" . $k . "%'";
			$items = $taoke_model->where($where)->select();
			if(!$items)$items = $dataoke_model->where("title like '%" . $k . "%'")->select();
			foreach ($items as $key=>$item) {
				$data = get_url_data($item['quan_link']);
				if($data['activity_id'] == "")$quan_id = $data['activityId'];
				else $quan_id = $data['activity_id'];
				$url = "http://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=" . $item['type'];


				$item['urls'] = "下单链接：" .  convert_dwz($url) . "<br>";
				$kouling = "";

				$item['memo'] = $item['memo'] . $mm1 . $kouling . $mm2  . $mm4;

				$item['imgmemo'] = $imgurl. "/dtk/" .$item['id'] . ".jpg";

				$items[$key] = $item;
			}
			$content['items']=$items;

			$this->assign("lists",$content);
			if($t == '1')	$this->display(":list_mobile");
			else $this->display(":list_pc");
		}
		else {
			redirect(__ROOT__."/");
		}
    }
    
    
}
