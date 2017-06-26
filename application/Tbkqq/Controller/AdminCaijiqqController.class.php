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
class AdminCaijiqqController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}

	public function item(){
		$where_ands =array("1=1");
		$fields=array(
			'startdate'=> array("field"=>"FROM_UNIXTIME(add_time)","operator"=>">="),
			'enddate'=> array("field"=>"FROM_UNIXTIME(add_time)","operator"=>"<="),
			'item'=> array("field"=>"title","operator"=>"like"),
			'qtime'=>array("field"=>"FROM_UNIXTIME(coupon_end_time)","operator"=>"<")
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
		$dataoke_model = M('CaijiqqItems','cmf_','DB_DATAOKE');

		//if($_REQUEST["cid"] == "all")

		$count=$dataoke_model->where($where)
			->count();

		$page = $this->page($count, 100);
		$items=$dataoke_model->where($where)
			->order("id desc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		$media = M("TbkqqTaokeMedia")->field("username")->group("username")->select();
		$this->assign("media",$media);
		$this->assign("items",$items);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->display();
	}

	public function item_getinfo(){
		if(IS_POST){
			if(isset($_POST['ids'])){
				$taoke_model = M('CaijiqqItems','cmf_','DB_DATAOKE');
				foreach($_POST['ids'] as $iid){
					$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
					$str = openhttp_header($u, '', '');
					$arr = json_decode($str, true);
					$item = $arr['data']['pageList'][0]['title'];
					$img = $arr['data']['pageList'][0]['pictUrl'];
					$taoke_model->where(array("iid"=>$iid))->save(array("title"=>$item,"pic_url"=>$img));
				}
				$this->success("补采信息成功");
			}
		}
	}


	public function item_tuiguang(){
		if(IS_POST){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
			$username = $_POST['username'];
			$proxys = M("TbkqqTaokeMedia")->where(array("username"=>$username,'status'=>'1'))->select();
			if(isset($_POST['ids'])){
				//$ids=join(",",$_POST['ids']);
				$taoke_model = M('CaijiqqItems','cmf_','DB_DATAOKE');
				foreach($_POST['ids'] as $iid){
					$item = $taoke_model->where(array("num_iid"=>$iid))->find();
					if($item){
						$item['status'] = '0';
						$item['itemurl'] = "http://item.taobao.com/item.htm?id=" . $iid;
						$item['type'] = '1';
						$item['item'] = $item['title'];
						$item['iid'] = $item['num_iid'];
						$item['quan_link'] = $item['quanurl'];
						$item['img'] = $item['pic_url'];

						unset($item['id']);

						if($item_model->where(array("iid"=>$iid))->find())
							$item_model->save($item);
						else
							$item_model->add($item);

					}
				}
				$this->success("添加成功！");
			}
		}

	}

	public function item_campaign_post(){
		set_time_limit(0);
		if (IS_POST) {
			if(isset($_POST['ids'])) {
				$ids = join(",", $_POST['ids']);
			}
			$username=I("post.username");
			$taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
			$options_model = M("Options");
			$option=$options_model->where("option_name='cookie_options'")->find();
			if($option){
				$options = (array)json_decode($option['option_value'],true);
				foreach($options as $data) {
					if($data['username'] == $username) $cookie = $data['cookie'];
				}
			}
			$ret = "";
			if($cookie != ""){
				$items = $taoke_model->where("iid in ($ids)")->select();
				if($items) {
					foreach ($items as $item) {
						$t = time();
						$iid = $item['iid'];
						$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" .$iid . "&auctionTag=&perPageSize=40&shopTag=";
						$str = openhttp_header($u,'',$cookie);
						$arr = json_decode($str,true);
						$sellerId = $arr['data']['pageList'][0]['sellerId'];
						$tkRate = $arr['data']['pageList'][0]['tkRate'];
						$eventRate = $arr['data']['pageList'][0]['eventRate'];
						$type = '1';
						$u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
						$str = openhttp_header($u, '', $cookie);
						$arr = json_decode($str, true);
						if ($arr['ok'] == '1' && $arr['data']) {
							$rate = $tkRate;
							if($eventRate != ''){
								if($rate<$eventRate){
									$rate = $eventRate;
									$type = '0';
								}
							}
							$cid = '';
							$keeperid = '';
							$post = array();

							foreach ($arr['data'] as $data) {
								if($data['manualAudit'] == '1') continue;
								if ($data['commissionRate'] > $rate) {
									$rate = $data['commissionRate'];
									$cid = $data['CampaignID'];
									$keeperid = $data['ShopKeeperID'];
								}
							}
							if($cid != ""){
								$post['campId'] = $cid;
								$post['keeperid'] = $keeperid;
								$post['applyreason'] = "淘特惠淘客推广申请";
								$cookie_data = excookie($cookie);
								$post['_tb_token_'] = $cookie_data['_tb_token_'];
								$post['t'] = $t;
								$type = '1';
								$post_str = "campId=" . $post['campId'] . "&keeperid=" . $post['keeperid'] . "&applyreason=" . $post['applyreason'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
								//print_r($post);
								$u = "http://pub.alimama.com/pubauc/applyForCommonCampaign.json";
								$reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" . $item['itemurl'];
								sleep(1);
								$ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
								sleep(1);

							}
							if($type == '0')$taoke_model->where(array("iid"=>$iid))->save(array("type"=>$type));
						}
					}
				}
			}

			$this->success($ret);
		}
	}

}