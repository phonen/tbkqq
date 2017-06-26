<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Tbkqq\Controller;
use Common\Controller\AdminbaseController;
class AdminCuntaoController extends AdminbaseController {
	function _initialize() {
//		parent::_initialize();
	}

	public function item(){

		$uid = sp_get_current_admin_id();
		$where_ands =array("uid='" . $uid . "'");
		$fields=array(
			'startdate'=> array("field"=>"add_time","operator"=>">="),
			'enddate'=> array("field"=>"add_time","operator"=>"<="),

			'item'=> array("field"=>"dtitle","operator"=>"like"),

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
		}
		else{
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
		$count=M("CunItems")
			->where($where)
			->count();

		$page = $this->page($count, 20);

		$items = M("CunItems")->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

		$this->assign("items",$items);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->display();

	}

	public function items(){

			$where_ands =array("status='underway'");
			$fields=array(
				'startdate'=> array("field"=>"add_time","operator"=>">="),
				'enddate'=> array("field"=>"add_time","operator"=>"<="),

				'item'=> array("field"=>"dtitle","operator"=>"like")
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
			}
			else{
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


		$count=M("CunItems")
			->where($where)
			->count();

		$page = $this->page($count, 20);

			$items = M("CunItems")->where($where)
				->limit($page->firstRow . ',' . $page->listRows)
				->select();

			$this->assign("items",$items);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
			$this->assign("formget",$_GET);
			$this->display();

	}

	public function item_dsh(){

		$where_ands =array("status='0'");
		$fields=array(
			'startdate'=> array("field"=>"add_time","operator"=>">="),
			'enddate'=> array("field"=>"add_time","operator"=>"<="),

			'item'=> array("field"=>"dtitle","operator"=>"like")
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
		}
		else{
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
		$items = M("CunItems")->where($where)->select();

		$this->assign("items",$items);
		$this->assign("formget",$_GET);
		$this->display();

	}

	public function item_audit(){
		$status = $_REQUEST['status'];
		if($status == '1') $status = 'underway';
		$data['status'] = $status;
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));


			if (M("CunItems")->where("id=$id")->save($data)) {
				$this->success("审核成功！");
			} else {
				$this->error("审核失败！");
			}

		}

		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);

			$items = M("CunItems")->where("id in ($ids)")->save($data);
			if($items){
					$this->success("审核成功！");
			}
			else $this->error("审核失败！");
		}
	}


	public function item_add(){
		$this->display();
	}


	public function item_add_post(){
		if (IS_POST) {
			$item_model = M("CunItems");

			$item=I("post.item");
			$iid = $item['num_iid'];
			$item['status'] = '0';
			$item['add_time'] = time();
			$item['uid'] = sp_get_current_admin_id();
			$item['uname'] = $_SESSION['name'];
			$item['quanurl'] = str_replace('&amp;', '&', $item['quanurl']);
			$data = get_url_data($item['quanurl']);
			if($data['activity_id'] == "")$quan_id = $data['activityId'];
			else $quan_id = $data['activity_id'];

			$item['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_21582792_72310921&itemId=" . $iid ."&src=qhkj_dtkp&dx=1";
			if($iid != ""){
				$taoke = $item_model->where(array("num_iid"=>$iid))->find();

				if(!$taoke){
					$info = get_item_info($iid);
					//unset($item['id']);
					$item['num_iid'] = $iid;
					$item['title'] = $info->title;
					$item['pic_url'] = $info->pict_url;
					$item['price'] = $info->zk_final_price;
					$item['nick'] = $info->nick;
					$item['sellerId'] = $info->seller_id;
					$item['volume'] = $info->volume;

					$item['quanurl'] = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $item['sellerId'] . "&activity_id=" . $quan_id;
					$item['type'] = '1';
					$item['source'] = '2690';
					$item['cun'] = '1';
					$coupon_info = get_coupon_info($item['quanurl']);
					if($coupon_info) $item1 = array_merge($item,$coupon_info);

					$result=$item_model->add($item1);

					if ($result) {
						$this->success("添加成功！");
					} else {
						$this->error("添加失败！");
					}
				}
				else{
					if($taoke['source'] == '2690')
						$this->error("添加失败！已经存在该商品id");
					else {
						$info = get_item_info($iid);
						//unset($item['id']);
						$item['num_iid'] = $iid;
						$item['title'] = $info->title;
						$item['pic_url'] = $info->pict_url;
						$item['price'] = $info->zk_final_price;
						$item['nick'] = $info->nick;
						$item['sellerId'] = $info->seller_id;
						$item['volume'] = $info->volume;

						$item['type'] = '1';
						$item['source'] = '2690';
						$item['cun'] = '1';
						$coupon_info = get_coupon_info($item['quanurl']);
						if($coupon_info) $item1 = array_merge($item,$coupon_info);
						$item1['id'] = $taoke['id'];
						$result=$item_model->save($item1);

						if ($result) {
							$this->success("添加成功！");
						} else {
							$this->error("添加失败！");
						}
					}
				}

			}
			else $this->error("添加失败！");
		}
	}




	public function item_edit(){
		$id=  intval(I("get.id"));
		$item = M("CunItems")->where("id=$id")->find();
		$this->assign("item",$item);
		$this->display();
	}

	public function item_edit_post(){
		if (IS_POST) {
			$item=I("post.item");
			$result=M("CunItems")->save($item);
			if ($result!==false) {
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		}
	}

	public function item_dsh_post(){
		set_time_limit(0);
		if(IS_POST){
			$ids = $_POST['ids'];
			$username=I("post.username");
			$proxys = M("TbkqqTaokeMedia")->where(array("username"=>$username,'status'=>'1'))->select();
			$proxys1 = M("TbkqqTaokeMedia")->where(array("username"=>'15219198262','status'=>'1'))->select();
			if($proxys1)$proxys = array_merge($proxys,$proxys1);
			foreach($ids as $id){
				$no = M("TbkqqTaokeItem")->where(array("status"=>"1"))->max("no");
				$no = $no?$no:0;
				$no = $no+1;
				$item = M("TbkqqTaokeItem")->where(array("id"=>$id))->find();
//				$no1 = M("TbkqqTaokeItem")->where(array("id"=>$id))->getField("no");
				$no1 = $item['no'];
				$iid = $item['iid'];
				if($no1)$data['no'] = $no1;
				else $data['no'] = $no;
				$data['status'] = '1';
				M("TbkqqTaokeItem")->where(array("id"=>$id))->save($data);

				foreach($proxys as $proxy){
					$itemurl = array();
					$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
					$itemurl = M("TbkqqTaokeItemurl")->where(array("iid"=>$iid,"proxyid"=>$proxyid))->find();
					if($itemurl){
						continue;
					}
					else {
						$itemurl['iid'] = $iid;
						$data = get_url_data($item['quan_link']);
						if($data['activity_id'] == "")$quan_id = $data['activityId'];
						else $quan_id = $data['activity_id'];
						$itemurl['qurl'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=".$item['type'];
						$itemurl['shorturl'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=" . $item['type'];

						//if($proxyid == '001' || $proxyid == '0001'){
                            $token_data = array();
                            $token_data['logo'] = $item['img'];
                            $token_data['text'] = $item['item'];
                            $token_data['url'] = $itemurl['qurl'];
						$taotokenstr = '';
						$taotokenstr = get_taotoken($token_data);
						if($taotokenstr == '')$taotokenstr = get_taotoken($token_data);
						if($taotokenstr == '')$taotokenstr = get_taotoken($token_data);
						if($taotokenstr == '')$taotokenstr = get_taotoken($token_data);

						//   $itemurl['quankl'] = get_taotoken($token_data);
						$itemurl['quankl'] = $taotokenstr;
                        //    $itemurl['quankl'] = get_taotoken($token_data);
                        //}

						$itemurl['proxyid'] = $proxyid;
						$itemurl['itime'] = date("Y-m-d H:i:s",time());
						unset($itemurl['id']);
						M("TbkqqTaokeItemurl")->add($itemurl);
					}

				}
			}
			$this->success("正式推广成功！");
		}
	}

	public function item_post(){
		if(IS_POST){
			$ids = $_POST['nos'];
			foreach ($ids as $id => $r) {
				$data['no'] = $r;
				M("CunItems")->where(array("id" => $id))->save($data);
			}
			$this->success("编号更新成功！");
		}
	}


	public function item_delete(){
        set_time_limit(0);
		$data['status']='-1';
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));


				if (M("CunItems")->where("id=$id")->delete()) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}

		}

		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);


				if (M("CunItems")->where("id in ($ids)")->delete()) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
		}
	}


	protected function get_coupon_info($url){

		$header[] = "Accept-Language: zh-CN,zh;q=0.8";
		$header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_REFERER, $tu);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($ch, CURLOPT_NOBODY,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
		curl_setopt($ch, CURLOPT_MAXREDIRS,2);
		$out = curl_exec($ch);
		$dd =  curl_getinfo($ch);
		curl_close($ch);
		$host = parse_url($dd['url'], PHP_URL_HOST);
		if($host == 'login.taobao.com'){
			$urldata = get_url_data($dd['url']);
			$url = urldecode($urldata['redirectURL']);

			$url = "http://shop.m.taobao.com/shop/coupon.htm?" . parse_url($url, PHP_URL_QUERY);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			//curl_setopt($ch, CURLOPT_REFERER, $tu);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			//curl_setopt($ch, CURLOPT_NOBODY,1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
			curl_setopt($ch, CURLOPT_MAXREDIRS,2);
//				$out = curl_exec($ch);
//				$dd =  curl_getinfo($ch);
			curl_close($ch);
		}
		$quanurl = $dd['url'];

		$out = http_get_content($url);
		preg_match_all('/([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]|[0-9][1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8])))/', $html, $effectdate);
		if (empty($effectdate)) {
			$this->error("添加券日期失败！");
			exit;
		}
		$item['coupon_start_time'] = strtotime($effectdate[0][0]);
		$item['coupon_end_time'] = strtotime($effectdate[0][1]);

		if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
			$item['Quan_surplus'] = $match[1];
		if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
			$item['Quan_receive'] = $match[1];

		if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
			$item['Quan_condition'] = $match[1];

		//$item['quanurl'] = $quanurl;
		//$item['Quan_surplus'] = $quan_surplus;
		//$item['Quan_receive'] = $quan_receive;
		preg_match('/<dt>\d*/', $out, $quan);
		if (empty($quan)) {
			$this->error("券failed");
			exit;
		}
	}
}