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
class AiController extends HomebaseController {
    //文章内页
    public function index() {
    	$_GET = array_merge($_GET, $_POST);
		$k = I("get.keyword");
		
		if (empty($k)) {
			$this -> error("关键词不能为空！请重新输入！");
		}
		$this -> assign("keyword", $k);
		$this -> display(":search");
    }


	public function taoke_info(){
		if(IS_POST){
			$appname = C("SITE_APPNAME");
			$msg = $_POST['msg'];
			$proxywx = $_POST['proxywx'];
			$iid = "";
			$proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
			if($proxy){
				preg_match('/https?:\/\/.*/',$msg,$match);
				$s = $match[0];
				if (!empty($s)) {
					$host = parse_url($s, PHP_URL_HOST);
					if($host == 'e22a.com'){
						preg_match('/(https?:\/\/.*)\?/',$s,$match);
						$s = $match[1];
						$str = file_get_contents($s);
						preg_match('/var url = \'(http.*)\';/',$str,$match);
						$s = $match[1];
					}
					$host = parse_url($s,PHP_URL_HOST);

					if($host == 'a.m.taobao.com'){
						preg_match('/\/i(\d+)\.htm/',$s,$match);

						$iid = $match[1];
					}
					elseif($host == "uland.taobao.com"){
						$data = get_url_data($s);
						$iid = $data['itemId'];
					}
					/*
                    elseif($host == 'item.taobao.com' || $host == 'detail.tmall.com'){
                        $data = get_url_data($url);

                        $iid = $data['id'];
                    }
                */

					elseif($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com'){
						//preg_match('/(https?:\/\/.*)/',$s,$match);
						//$s = $match[1];
						$data = get_url_data($s);

						$iid = $data['id'];
					}

					elseif($host == 's.click.taobao.com'){
						$data = get_item($s);
						$iid = $data['id'];
					}

					if($iid != ""){

						$username = C("ROBOT_USERNAME");//"szh166888";
						$media = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy'],"username"=>$username))->find();
						if($media){
							$mediaid = $media['mediaid'];
							$adid = $media['adid'];
							$t = time();

							$options_model = M("Options");
							$option=$options_model->where("option_name='cookie_options'")->find();
							if($option){
								$options = (array)json_decode($option['option_value'],true);
								foreach($options as $data) {
									if($data['username'] == $username) $cookie = $data['cookie'];
								}
								if($cookie != ""){
									$u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
									$str = openhttp_header($u, '', $cookie);
									$arr = json_decode($str, true);

									if ($arr['ok'] == '1' && $arr['data']) {
										$rate = 0;
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
										$post['campId'] = $cid;
										$post['keeperid'] = $keeperid;
										$post['applyreason'] = "淘特惠淘客推广申请";
										$cookie_data = excookie($cookie);
										$post['_tb_token_'] = $cookie_data['_tb_token_'];
										$post['t'] = $t;


										$post_str = "campId=" . $post['campId'] . "&keeperid=" . $post['keeperid'] . "&applyreason=" . $post['applyreason'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
										//print_r($post);
										$u = "http://pub.alimama.com/pubauc/applyForCommonCampaign.json";
										$reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" . $s;
										sleep(1);
										$ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
									}


									$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" .$iid . "&auctionTag=&perPageSize=40&shopTag=";
									$str = openhttp_header($u,'',$cookie);
									$arr = json_decode($str,true);
									$sellerId = $arr['data']['pageList'][0]['sellerId'];
									$tkRate = $arr['data']['pageList'][0]['tkRate'];

									$dtk_api = "http://api.dataoke.com/index.php?r=port/index&appkey=bnsdd1etil&v=2&id=$iid";
									$str = file_get_contents($dtk_api);
									$json = json_decode($str,true);
									if($json['result']){
										$quan = $json['result'];
										$quan_str = "券面额：" . $quan['Quan_price'] . "券使用条件：" . $quan['Quan_condition'] . "券链接：" . $quan['Quan_link'];
									}

									else {
										$qtk_url = "http://www.qingtaoke.com/api/UserPlan/UserCouponList?sid=$sellerId&gid=$iid";
										$str = file_get_contents($qtk_url);
										$arr = json_decode($str,true);
										if($arr['data']){
											$quan_str = "";
											foreach($arr['data'] as $data){
												if($data['requisitioned']>0) $quan_str .= "券：http://shop.m.taobao.com/shop/coupon.htm?activityId=" . $data['activityId'] . "&sellerId=" . $sellerId . "\n";
											}
										}
										else $quan_str = " 请联系人工客服找券";
									}




									$u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1&t=$t";
									$str = openhttp_header($u,'',$cookie);
									if($str != ""){
										$arr = json_decode($str,true);
										if($arr['data']['couponLink'] != "") $link = $arr['data']['couponLink'];
										else $link = $arr['data']['shortLinkUrl'];
										if($arr['data']['couponLinkTaoToken'] != "") $taotoken = $arr['data']['couponLinkTaoToken'];
										else $taotoken = $arr['data']['taoToken'];
										if($link == "") echo "@" . $proxywx . ": 找不到链接，可能商家做了调整，请联系人工客服确认";
										else{
											if($appname == "yhg")$rate = "";
											else $rate = $rate>0?$rate:$tkRate;
											echo "@" . $proxywx . ": " . $rate . "下单链接：".$link . " 淘口令：" . $taotoken . $quan_str;
										}
									}
									else echo "@" . $proxywx . ": 找不到t这个链接，可能机器人掉线了，请联系人工客服";
								}
							}

						}

					}
					else echo "@" . $proxywx . ": 找不到i这个链接，请联系人工客服";
				}
			}
			else {
				echo "@" . $proxywx . ": 你没有登记代理编号，请@我，并输入代理帐号：" . $appname . "001  这种格式进行登记";
			}
		}
	}

	public function qq_taoke_info(){
		if(IS_POST){
			$appname = C("SITE_APPNAME");
			$msg = $_POST['msg'];
			$proxyqq = $_POST['proxyqq'];
			$iid = "";
			$proxy = M("TbkqqProxy")->where(array("proxyqq"=>$proxyqq))->find();
			if($proxy){
				preg_match('/https?:\/\/.*/',$msg,$match);
				$s = $match[0];
				if (!empty($s)) {
					$host = parse_url($s, PHP_URL_HOST);
					if($host == 'e22a.com'){
						preg_match('/(https?:\/\/.*)\?/',$s,$match);
						$s = $match[1];
						$str = file_get_contents($s);
						preg_match('/var url = \'(http.*)\';/',$str,$match);
						$s = $match[1];
					}
						$host = parse_url($s,PHP_URL_HOST);

						if($host == 'a.m.taobao.com'){
							preg_match('/\/i(\d+)\.htm/',$s,$match);

							$iid = $match[1];
						}
						elseif($host == "uland.taobao.com"){
							$data = get_url_data($s);
							$iid = $data['itemId'];
						}
						/*
						elseif($host == 'item.taobao.com' || $host == 'detail.tmall.com'){
							$data = get_url_data($url);

							$iid = $data['id'];
						}
					*/

					elseif($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com'){
						//preg_match('/(https?:\/\/.*)/',$s,$match);
						//$s = $match[1];
						$data = get_url_data($s);

						$iid = $data['id'];
					}

					if($iid != ""){

						$username = C("ROBOT_USERNAME");//"szh166888";
						$media = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy'],"username"=>$username))->find();
						if($media){
							$mediaid = $media['mediaid'];
							$adid = $media['adid'];
							$t = time();

							$options_model = M("Options");
							$option=$options_model->where("option_name='cookie_options'")->find();
							if($option){
								$options = (array)json_decode($option['option_value'],true);
								foreach($options as $data) {
									if($data['username'] == $username) $cookie = $data['cookie'];
								}
								if($cookie != ""){
									$u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
									$str = openhttp_header($u, '', $cookie);
									$arr = json_decode($str, true);

									if ($arr['ok'] == '1' && $arr['data']) {
										$rate = 0;
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
										$post['campId'] = $cid;
										$post['keeperid'] = $keeperid;
										$post['applyreason'] = "淘特惠淘客推广申请";
										$cookie_data = excookie($cookie);
										$post['_tb_token_'] = $cookie_data['_tb_token_'];
										$post['t'] = $t;


										$post_str = "campId=" . $post['campId'] . "&keeperid=" . $post['keeperid'] . "&applyreason=" . $post['applyreason'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
										//print_r($post);
										$u = "http://pub.alimama.com/pubauc/applyForCommonCampaign.json";
										$reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" . $s;
										sleep(1);
										$ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
									}


									$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" .$iid . "&auctionTag=&perPageSize=40&shopTag=";
									$str = openhttp_header($u,'',$cookie);
									$arr = json_decode($str,true);
									$sellerId = $arr['data']['pageList'][0]['sellerId'];
									$tkRate = $arr['data']['pageList'][0]['tkRate'];

									$dtk_api = "http://api.dataoke.com/index.php?r=port/index&appkey=bnsdd1etil&v=2&id=$iid";
									$str = file_get_contents($dtk_api);
									$json = json_decode($str,true);
									if($json['result']){
										$quan = $json['result'];
										$quan_str = "券面额：" . $quan['Quan_price'] . "券使用条件：" . $quan['Quan_condition'] . "券链接：" . $quan['Quan_link'];
									}

									else {
										$qtk_url = "http://www.qingtaoke.com/api/UserPlan/UserCouponList?sid=$sellerId&gid=$iid";
										$str = file_get_contents($qtk_url);
										$arr = json_decode($str,true);
										if($arr['data']){
											$quan_str = "";
											foreach($arr['data'] as $data){
												if($data['requisitioned']>0) $quan_str .= "券：http://shop.m.taobao.com/shop/coupon.htm?activityId=" . $data['activityId'] . "&sellerId=" . $sellerId . "\n";
											}
										}
										else $quan_str = " 请联系人工客服找券";
									}




									$u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1&t=$t";
									$str = openhttp_header($u,'',$cookie);
									if($str != ""){
										$arr = json_decode($str,true);
										if($arr['data']['couponLink'] != "") $link = $arr['data']['couponLink'];
										else $link = $arr['data']['shortLinkUrl'];
										if($arr['data']['couponLinkTaoToken'] != "") $taotoken = $arr['data']['couponLinkTaoToken'];
										else $taotoken = $arr['data']['taoToken'];
										if($link == "") echo " 找不到链接，可能商家做了调整，请联系人工客服确认";
										else{
											if($appname == "yhg")$rate = "";
											else $rate = $rate>0?$rate:$tkRate;

											echo  $rate . "下单链接：".$link . " 淘口令：" . $taotoken . $quan_str;
										}
									}
									else echo " 找不到t这个链接，可能机器人掉线了，请联系人工客服";
								}
							}

						}

					}
					else echo " 找不到i这个链接，请联系人工客服";
				}
			}
			else {
				echo " 你没有登记代理编号，请@我，并输入代理帐号：taotehui001  这种格式进行登记";
			}
		}
	}

	public function save_link(){
		if(IS_POST){
			$msg = $_POST['msg'];
			$proxywx = $_POST['proxywx'];
			$appname = C("SITE_APPNAME");
			$data['proxywx'] = $proxywx;
			if(M("TbkqqProxy")->where(array("proxy"=>$msg))->save($data)>=0){
				echo "@" . $proxywx . ": 设置成功！";
			}
			else echo "@" . $proxywx . ": 设置错误，请联系老板！";
		}
	}


	public function qq_save_link(){
		if(IS_POST){
			$msg = $_POST['msg'];
			$proxyqq = $_POST['proxyqq'];
			$appname = C("SITE_APPNAME");
			$data['proxyqq'] = $proxyqq;
			if(M("TbkqqProxy")->where(array("proxy"=>$msg))->save($data)>=0){
				echo  "设置成功！";
			}
			else echo "设置错误，请联系老板！";
		}
	}


	public function qq_order_json(){

		$orderid = $_POST['oid'];


		//$proxy = M("TbkqqProxy")->where(array("wxgroup"=>$group))->find();

		$startdate=date("Y-m-d",time()-86400);
		$options_model = M("Options");

		$enddate = date("Y-m-d");
		$item = M("TbkqqTaokeDetails")->where(array("orderid"=>$orderid))->find();
		if($item){
			echo $item['status'];
		}
		else {

			$u = "http://pub.alimama.com/report/getTbkPaymentDetails.json?startTime=$startdate&endTime=$enddate&payStatus=&queryType=1&toPage=1&perPageSize=1000&total=&_input_charset=utf-8";
			$option=$options_model->where("option_name='cookie_options'")->find();
			if($option) {
				$options = (array)json_decode($option['option_value'], true);
				foreach ($options as $data) {
					$cookie = $data['cookie'];
					$str = openhttp_header($u, '', $cookie);
					if(strpos($str,$orderid)!==false) {
						echo "订单存在";
						exit();
					}
					sleep(1);
				}
			}
			echo " 订单不存在，请确认机器人是否掉线（详见公告），如果机器人掉线，请联系公司客服处理，如未掉线，请立即撤单重拍！";
		}
	}


	public function get_taoke_info(){
		$iid = $_GET['id'];
		$isq = $_GET['isq'];
		$proxyid = $_GET['pid'];
		if($proxyid == "")$proxyid = '001';
		if($iid != ""){

			$username = C("ROBOT_USERNAME");//"szh166888";
			$proxy = C("SITE_APPNAME") . $proxyid;
			$media = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy,"username"=>$username))->find();
			if($media){
				$mediaid = $media['mediaid'];
				$adid = $media['adid'];
				$pid = $media['pid'];

				$t = time();

				$options_model = M("Options");
				$option=$options_model->where("option_name='cookie_options'")->find();
				if($option){
					$options = (array)json_decode($option['option_value'],true);
					foreach($options as $data) {
						if($data['username'] == $username) $cookie = $data['cookie'];
					}
					if($cookie != ""){
						$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" .$iid . "&auctionTag=&perPageSize=40&shopTag=";
						$str = openhttp_header($u,'',$cookie);
						$arr = json_decode($str,true);
						$sellerId = $arr['data']['pageList'][0]['sellerId'];
						$tkRate = $arr['data']['pageList'][0]['tkRate'];
						$eventRate = $arr['data']['pageList'][0]['eventRate'];
						$img = $arr['data']['pageList'][0]['pictUrl'];
						$title = $arr['data']['pageList'][0]['title'];
						if($title == ""){

							exit();
						}
						$price = $arr['data']['pageList'][0]['zkPrice'];
						if($price == "")$price = $arr['data']['pageList'][0]['reservePrice'];



						$u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
						$str = openhttp_header($u, '', $cookie);
						$arr = json_decode($str, true);

						if ($arr['ok'] == '1' && $arr['data']) {
							$rate = $tkRate;
							if($eventRate != ''){
								if($rate<$eventRate){
									$rate = $eventRate;
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


								$post_str = "campId=" . $post['campId'] . "&keeperid=" . $post['keeperid'] . "&applyreason=" . $post['applyreason'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
								//print_r($post);
								$u = "http://pub.alimama.com/pubauc/applyForCommonCampaign.json";
								//sleep(1);
								$ret = openhttp_header($u, $post_str, $cookie,"", '1');
							}

						}
						else {
							$rate = $tkRate;
							if($eventRate != ''){
								if($rate<$eventRate){
                                    $rate_type = "event";
								}
							}
						}







							if($rate_type == "event")
								$u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=3&channel=tk_qqhd&t=$t";
							else
								$u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1&t=$t";
							$str = openhttp_header($u,'',$cookie);
							if($str != ""){
								$arr = json_decode($str,true);
								if($arr['data']['couponLink'] != "" && $isq == '1') $link = $arr['data']['couponLink'];
								else {
									$link = $arr['data']['shortLinkUrl'];

								}
echo $link;

							}



					}
				}

			}

		}
	}



    public function get_taoke_by_iid(){

            $appname = C("SITE_APPNAME");
            $fcrate = C('YONGJIN_RATE');
            $iid = $_GET['id'];
            $proxyid = $_GET['pid'];
            $isq = $_GET['isq'];
        //$username = C("ROBOT_USERNAME");//"szh166888";


        $proxy = C("SITE_APPNAME") . $proxyid;
        //$media = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy,"username"=>$username))->find();


                if($iid != "") {
                    $s = "http://item.taobao.com/item.htm?id=$iid";
                    $username = C("ROBOT_USERNAME");//"szh166888";

                    $media = M("TbkqqTaokeMedia")->where(array("proxy" => $proxy, "username" => $username))->find();
                    if ($media) {
                        $mediaid = $media['mediaid'];
                        $adid = $media['adid'];
                        $pid = $media['pid'];

                        $t = time();

                        $options_model = M("Options");
                        $option = $options_model->where("option_name='cookie_options'")->find();
                        if ($option) {
                            $options = (array)json_decode($option['option_value'], true);
                            foreach ($options as $data) {
                                if ($data['username'] == $username) $cookie = $data['cookie'];
                            }
                            if ($cookie != "") {
                                $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";

                                $str = openhttp_header($u, '', $cookie,'',"Upgrade-Insecure-Requests:1");
                                $arr = json_decode($str, true);
                                $sellerId = $arr['data']['pageList'][0]['sellerId'];
                                $tkRate = $arr['data']['pageList'][0]['tkRate'];
                                $eventRate = $arr['data']['pageList'][0]['eventRate'];

                                $title = $arr['data']['pageList'][0]['title'];

                                if ($title == "") {
                                    echo "title is null";
                                    exit();
                                }



                                $u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
                                $str = openhttp_header($u, '', $cookie);
                                $arr = json_decode($str, true);

                                if ($arr['ok'] == '1' && $arr['data']) {
                                    $rate = $tkRate;

                                    $cid = '';
                                    $keeperid = '';
                                    $post = array();

                                    foreach ($arr['data'] as $data) {
                                        if ($data['existStatus'] == '2') $existCid = $data['campaignID'];
                                        if ($data['manualAudit'] == '1') continue;
                                        if ($data['commissionRate'] > $rate) {
                                            $rate = $data['commissionRate'];
                                            $cid = $data['CampaignID'];
                                            $keeperid = $data['ShopKeeperID'];
                                        }
                                    }
                                    if ($cid != "") {
                                        $post['campId'] = $cid;
                                        $post['keeperid'] = $keeperid;
                                        $post['applyreason'] = "淘特惠淘客推广申请";
                                        $cookie_data = excookie($cookie);
                                        $post['_tb_token_'] = $cookie_data['_tb_token_'];
                                        $post['t'] = $t;


                                        $post_str = "campId=" . $post['campId'] . "&keeperid=" . $post['keeperid'] . "&applyreason=" . $post['applyreason'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
                                        //print_r($post);
                                        $u = "http://pub.alimama.com/pubauc/applyForCommonCampaign.json";
                                        $reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" . $s;
                                        sleep(1);
                                        $ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
                                    } else {
                                        //exit campaign
                                        if ($existCid != "") {
                                            $u = "http://pub.alimama.com/campaign/exitCampaign.json";
                                            $post['pubCampaignid'] = $existCid;
                                            $post['t'] = time();
                                            $cookie_data = excookie($cookie);
                                            $post['_tb_token_'] = $cookie_data['_tb_token_'];
                                            $post_str = "pubCampaignid=" . $post['pubCampaignid'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
                                            $ret = openhttp_header($u, $post_str, $cookie, '', '1');
                                        }
                                        if ($eventRate != '') {
                                            if ($rate < $eventRate) {
                                                $rate_type = "event";
                                            }
                                        }


                                    }

                                } else {
                                    $rate = $tkRate;
                                    if ($eventRate != '') {
                                        if ($rate < $eventRate) {
                                            $rate_type = "event";
                                        }
                                    }
                                }






                                $cookie_data = excookie($cookie);


                                    if ($rate_type == "event")
                                        $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=3&channel=tk_qqhd";
//
                                    else
                                        $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1";

                                    $str = openhttp_header($u, '', $cookie);

                                    if ($str != "") {
                                        $arr = json_decode($str, true);
                                        if ($arr['data']['couponLink'] != "") $link = $arr['data']['couponLink'];
                                        else {
                                            $link = $arr['data']['shortLinkUrl'];

                                        }

                                        if ($arr['data']['couponLinkTaoToken'] != "") $taotoken = $arr['data']['couponLinkTaoToken'];
                                        else $taotoken = $arr['data']['taoToken'];
                                        if ($link == "") echo "@" . $proxywx . ": 找不到链接，可能商家做了调整，请联系人工客服确认";
                                        else {
                                            if ($appname == "yhg" || $g == "1") $yongjin = "";
                                            else {
                                                if ($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'], 2);
                                                else $yongjin = round($rate * $fcrate, 2);
                                            }
                                            echo "@" . $proxywx . ": 【" . $title . "】" . $yongjin . "下单链接：" . $link . " 淘口令：" . $taotoken . $quan_str;
                                        }
                                    } else echo "@" . $proxywx . ": 找不到t这个链接，可能机器人掉线了，请联系人工客服";

                            }
                            else echo "no cookie";
                        }
                        else echo "no option";

                    }
                    else echo "no media";

                }
                else echo "no iid";


    }



    public function wx_order_json(){
		$proxywx = $_POST['proxywx'];

		$orderid = $_POST['oid'];


		//$proxy = M("TbkqqProxy")->where(array("wxgroup"=>$group))->find();

		$startdate=date("Y-m-d",time()-86400);
		$options_model = M("Options");

		$enddate = date("Y-m-d");
		$item = M("TbkqqTaokeDetails")->where(array("orderid"=>$orderid))->find();
		if($item){
			echo "@" . $proxywx . $item['ostatus'];
		}
		else {

			$u = "http://pub.alimama.com/report/getTbkPaymentDetails.json?startTime=$startdate&endTime=$enddate&payStatus=&queryType=1&toPage=1&perPageSize=1000&total=&_input_charset=utf-8";
			$option=$options_model->where("option_name='cookie_options'")->find();
			if($option) {
				$options = (array)json_decode($option['option_value'], true);
				foreach ($options as $data) {
					$cookie = $data['cookie'];
					$str = openhttp_header($u, '', $cookie);
					if(strpos($str,$orderid)!==false) {
						echo "@" . $proxywx . "订单存在";
						exit();
					}
					sleep(1);
				}
			}
			echo "@" . $proxywx . " 订单不存在，请确认机器人是否掉线（详见公告），如果机器人掉线，请联系公司客服处理，如未掉线，请立即撤单重拍！";
		}
	}

}
