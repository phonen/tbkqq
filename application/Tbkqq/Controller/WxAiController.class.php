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
class WxAiController extends HomebaseController {
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
            $fcrate = C('YONGJIN_RATE');
            $msg = $_POST['msg'];
            $proxywx = $_POST['proxywx'];
            $wxgroup = $_POST['group'];
            if($_POST['debug'] == '') $debug = false;
            else $debug = true;
            $iid = "";
            $qq ='0';
            if($wxgroup != ''){
                $proxy = M("TbkqqProxy")->where(array("wxgroup"=>$wxgroup))->find();
                if($proxy) {$g ="1";}
                else $proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            }
            else 	$proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            if($proxy){
                $fcrate = get_yongjin_by_proxy($proxy['proxy']);
                //preg_match('/https?:\/\/.*/',$msg,$match);$p = '/https?:\/\/[\w=.?&\/;]+/';
                preg_match('/https?:\/\/[\w=.?&\/;]+/',$msg,$match);
                $s = $match[0];
                if (!empty($s)) {
                    $host = parse_url($s, PHP_URL_HOST);

//					if($host == 'e22a.com' || $host == 'c.b1yq.com'|| $host == 'c.b1wt.com' || $host == 'c.b1za.com'|| $host == 'c.b1wv.com'){
                    if($host != 'a.m.taobao.com' && $host != 'uland.taobao.com' && $host != 'item.taobao.com' && $host != 'detail.tmall.com' && $host != 'detail.m.tmall.com' && $host != 'item.m.taobao.com' && $host != 'h5.m.taobao.com' && $host != 's.click.taobao.com'){
                        //preg_match('/(https?:\/\/.*)\??/',$s,$match);
                        //$s = $match[1];

                        $str = file_get_contents($s);
                        preg_match('/var url = \'(https?:\/\/.*)\';/',$str,$match);
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
                        $activityId = $data['activityId'];

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
                        $media = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy'],"username"=>$username,"status"=>'1'))->find();
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
                                        \Think\Log::write("title找不到！  " . $u . "   title:" . $title,'WARN');
                                        echo "@" . $proxywx . ": 找不到链接，可能商家做了调整，请联系人工客服确认!!";
                                        exit();
                                    }
                                    $price = $arr['data']['pageList'][0]['zkPrice'];
                                    if($price == "")$price = $arr['data']['pageList'][0]['reservePrice'];



                                    $u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
                                    $str = openhttp_header($u, '', $cookie);
                                    $arr = json_decode($str, true);

                                    if ($arr['ok'] == '1' && $arr['data']) {
                                        $rate = $tkRate;

                                        $cid = '';
                                        $keeperid = '';
                                        $post = array();

                                        foreach ($arr['data'] as $data) {
                                            if($data['existStatus'] == '2')$existCid = $data['campaignID'];
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
                                            $reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" . $s;
                                            sleep(1);
                                            $ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
                                        }
                                        else {
                                            //exit campaign
                                            if($existCid != ""){
                                                $u = "http://pub.alimama.com/campaign/exitCampaign.json";
                                                $post['pubCampaignid'] = $existCid;
                                                $post['t'] = time();
                                                $cookie_data = excookie($cookie);
                                                $post['_tb_token_'] = $cookie_data['_tb_token_'];
                                                $post_str = "pubCampaignid=" . $post['pubCampaignid'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
                                                $ret = openhttp_header($u, $post_str, $cookie, '', '1');
                                            }
                                            if($eventRate != ''){
                                                if($rate<$eventRate){
                                                    $rate = $eventRate;
                                                    $qq = '1';
                                                }
                                            }


                                        }

                                    }
                                    else {
                                        $rate = $tkRate;
                                        if($eventRate != ''){
                                            if($rate<$eventRate){
                                                $rate = $eventRate;
                                                $qq = '1';
                                            }
                                        }
                                    }


                                    if($activityId != ""){
                                        $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $sellerId ."&activity_id=". $activityId;
                                        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
                                        $ch = curl_init();
                                        curl_setopt($ch, CURLOPT_URL, $quan_link);
                                        //curl_setopt($ch, CURLOPT_REFERER, $tu);
                                        curl_setopt($ch, CURLOPT_HEADER, true);
                                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                                        //curl_setopt($ch, CURLOPT_NOBODY,1);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
                                        curl_setopt($ch, CURLOPT_MAXREDIRS,2);
                                        $out = curl_exec($ch);
                                        curl_close($ch);


                                        if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match)){
                                            $quan_surpluse = $match[1];
                                            if($quan_surpluse == "")$activityId="";
                                        }

                                    }

                                    if($activityId == ""){
                                        $quan_api = "http://taotehui.co/?g=Tbkqq&m=Page&a=find_quan&iid=$iid";
                                        $activityId = file_get_contents($quan_api);

                                        if($activityId == ""){


                                            $qtk_url = "http://www.qingtaoke.com/api/UserPlan/UserCouponList?sid=$sellerId&gid=$iid";

                                            $qtk_cookie = '0f4242763305a0552f39b61843503c26=00f0a62ca24de37b108a0eb6e6912654c0e682cda%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; __cfduid=d9298988d33cf5d025e50f0800d2423041458637207; pgv_pvi=2078657173; PHPSESSID=ihq951b8fbu2nadenrid8bol90; CNZZDATA1256913876=1220425662-1458636544-%7C1472479995; PHPSESSID=beqahligcpq4lpvr5vq7pgatb7; 525d2e01d0d4d2fb427b9926b3856822=9f8b7d357c8e2e558451a99314f15895e633cea9a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; qtk_sin=4527me5sgs6muvtbu97frl0f10; CNZZDATA1258823063=1707445734-1471852188-null%7C1484523769; qtkwww_=a855d0ebd8c88a93f8a96697521de895919b9ea7a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D';
                                            $ch = curl_init();
                                            curl_setopt($ch, CURLOPT_URL, $qtk_url);
                                            curl_setopt($ch, CURLOPT_COOKIE,$qtk_cookie);
                                            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

                                            $str = curl_exec($ch);
                                            curl_close($ch);
                                            $arr = json_decode($str,true);
                                            if($arr['data']){

                                                $quan_str = "";
                                                $quan_arr = array();
                                                foreach($arr['data'] as $data){

                                                    if($data['remain']>0) {
                                                        if($price>$data['applyAmount']){
                                                            if($data['activity_id'] == "")$quan_arr[] = $data['activityId'];
                                                            else $quan_arr[] = $data['activity_id'];

                                                            $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?activityId=" . $data['activityId'] . "&sellerId=" . $sellerId;
                                                            $header = array();
                                                            $header[] = "Accept-Language: zh-CN,zh;q=0.8";
                                                            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
                                                            $ch = curl_init();
                                                            curl_setopt($ch, CURLOPT_URL, $quan_link);
                                                            //curl_setopt($ch, CURLOPT_REFERER, $tu);
                                                            curl_setopt($ch, CURLOPT_HEADER, true);
                                                            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                                                            //curl_setopt($ch, CURLOPT_NOBODY,1);
                                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
                                                            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
                                                            $out = curl_exec($ch);
                                                            curl_close($ch);
                                                            if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
                                                                $quan_str .=  $match[1] ."：" .$quan_link . "\n";
                                                        }
                                                    }
                                                }

                                            }
                                            else $quan_str = "";

                                        }
                                    }

                                    if($activityId == ""){
                                        if($qq == '1')
                                            $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=3&channel=tk_qqhd&t=$t";
                                        else
                                            $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1&t=$t";
                                        $str = openhttp_header($u,'',$cookie);
                                        if($str != ""){
                                            $arr = json_decode($str,true);
                                            if($arr['data']['couponLink'] != "") $link = $arr['data']['couponLink'];
                                            else {
                                                $link = $arr['data']['shortLinkUrl'];
                                                if($quan_str == "")$quan_str = " 没找到券，价格合适可以直接购买";
                                            }

                                            if($arr['data']['couponLinkTaoToken'] != "") $taotoken = $arr['data']['couponLinkTaoToken'];
                                            else $taotoken = $arr['data']['taoToken'];
                                            if($appname == "yhg" || $g == "1")$yongjin = "";
                                            else{
                                                if($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'],2);
                                                else $yongjin =  round($rate * $fcrate,2);
                                            }
                                            if($link == ""){
                                                \Think\Log::write("link找不到！taokeinfo  " . $u,'WARN');

                                                $ldata = array();
                                                $ldata['url'] = "https://uland.taobao.com/coupon/edetail?pid=" . $pid . "&itemId=" . $iid . "&src=cd_cdll";
                                                $ldata['logo'] = $img;
                                                $ldata['text'] = $title;

                                                $taotoken = get_taotoken($ldata);
                                                $quan_str = "券被抢光，请在购买时留意领券！";
                                                echo "@" . $proxywx . ": 【" . $title . "】".  $yongjin ." 淘口令：" . $taotoken . $quan_str;
                                            }
                                            else{

                                                echo "@" . $proxywx . ": 【" . $title . "】" .$yongjin . "下单链接：".$link . " 淘口令：" . $taotoken . $quan_str;
                                            }
                                        }
                                        else echo "@" . $proxywx . ": 找不到t这个链接，可能机器人掉线了，请联系人工客服";
                                    }
                                    else {
                                        if($qq == '1')
                                            $dx = "0";
                                        else $dx = "1";
                                        $link = "https://uland.taobao.com/coupon/edetail?activityId=" .$activityId ."&pid=" . $pid ."&itemId=" . $iid ."&src=qhkj_dtkp&dx=" . $dx;
                                        if($appname == "yhg" || $g == "1")$yongjin = "";
                                        else {
                                            if($proxy['fcrate'] != "")$yongjin = round($rate * $fcrate * $proxy['fcrate'],2);
                                            else $yongjin =  round($rate * $fcrate,2);
                                        }
                                        $token_data = array();
                                        $token_data['logo'] = $img;
                                        $token_data['text'] = $title;
                                        $token_data['url'] = $link;
                                        $taotoken = get_taotoken($token_data);

                                        if($taotoken == '')$taotoken = get_taotoken($token_data);
                                        if($taotoken == '')$taotoken = get_taotoken($token_data);
                                        if($taotoken == '')$taotoken = get_taotoken($token_data);

                                        echo "@" . $proxywx . ": 【" . $title . "】" .$yongjin . "下单链接：".$link . $quan_str . "口令：" .$taotoken;
                                    }
                                }
                            }

                        }

                    }
                    else echo "@" . $proxywx . ": 这个链接格式机器人认不到，请手工打开然后通过手机淘宝分享";
                }
            }
            else {
                echo "@" . $proxywx . ": 你没有登记代理编号，请@我，并输入代理帐号：" . $appname . "001  这种格式进行登记";
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

	public function isproxy(){
		if(IS_POST){
			$group = $_POST['group'];
			$proxywx = $_POST['proxywx'];
			//$data['proxywx'] = $proxywx;
			if(M("TbkqqProxy")->where(array("proxywx"=>$proxywx,"wxgroup"=>$group))->find()){
				echo "ok";
			}
			else echo "error";
		}
	}


	public function single_json(){

		if(IS_POST){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
			$no = $_POST['no'];
			$group = $_POST['group'];
			$temp = $_POST['temp'];
			$from = $_POST['from'];
			$send = $_POST['send'];
			$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";

			$mm1 = "  \n-------------------
长按复制这条消息，打开→手机淘宝→即可领卷下单";
			$mm2 = "";
			$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
			if($item){
				if($item['imgmemo'] == '')		$imgurl = $item['img']  . "_290x290.jpg";
				else {
					$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
					preg_match($preg, $item['imgmemo'], $imgArr);
					$imgurl = $imgArr[1];
				}
				if($group != ''){
					$proxy = M("TbkqqProxy")->where(array("wxgroup"=>$group,"sendwx"=>$send,"wxstatus"=>'1'))->find();
					if($proxy){
						//$proxyid = substr($proxy['proxy'], -3, 3);
						$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
						if($proxyid == '001') $www = 'www';
						else $www = $proxyid;
						$mm3 = "\n-------------------";
//省钱网站：http://" . $www . "." .  C("BASE_DOMAIN") ;
						$mm4 = "";
						$itemurl = M("TbkqqTaokeItemurl")->where(array("iid"=>$item['iid'],"proxyid"=>$proxyid))->find();
						$urlid = $itemurl['id'];
						$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
						if($kouling == ""){
							$token_data = array();
						$token_data['logo'] = $item['img'];
						$token_data['text'] = $item['item'];
						$token_data['url'] = $itemurl['qurl'];
									$kouling = get_taotoken($token_data);
							M("TbkqqTaokeItemurl")->where(array("iid"=>$item['iid'],"proxyid"=>$proxyid))->save(array("quankl"=>$kouling));
						}
						if($temp == '1'){
							$aftprice = $item['aftprice'] != ''?$item['aftprice']:$item['price']-$item['coupon_price'];
							$memo = "【VIP独享】" .$item['d_title']." \n【 原 价 】 ".$item['price']." 元\n【券后价】 ".$aftprice." 元\n【 亮 点 】".$item['intro']."\n━┉┉┉┉∞┉┉┉┉━\n【 下 单 】:复制整段信息，打开→手机淘宝→即可领券下单内部码：".$kouling;

						}

						else
							$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
								return convert_dwz($matches[0]) . "&uid=" . $urlid;
							},$item['memo']) . $mm1 . $kouling . $mm2 . $mm3 . $mm4;

						if(C('SITE_APPNAME') != 'yhg')$memo = "下单链接:" .$baseurl.$urlid . "\n" .$memo;
						$item_arr[]  = array('no'=>$no,'group'=>$proxy['wxgroup'],'memo'=> $memo,'img'=>$imgurl);
					}

				}
				else {
					$proxys = M("TbkqqProxy")->where(array("sendwx"=>$send,"wxstatus"=>'1'))->select();
					if($proxys){
						foreach($proxys as $proxy){
							//$proxyid = substr($proxy['proxy'], -3, 3);
							$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
							if($proxyid == '001') $www = 'www';
							else $www = $proxyid;
							$mm3 = "\n-------------------";
//省钱网站：http://" . $www . "." .  C("BASE_DOMAIN") ;
							$mm4 = "";
							$itemurl = M("TbkqqTaokeItemurl")->where(array("iid"=>$item['iid'],"proxyid"=>$proxyid))->find();
							$urlid = $itemurl['id'];
							$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];

							if($temp == '1'){
								$aftprice = $item['aftprice'] != ''?$item['aftprice']:$item['price']-$item['coupon_price'];
								$memo = "【VIP独享】" .$item['d_title']." \n【 原 价 】 ".$item['price']." 元\n【券后价】 ".$aftprice." 元\n【 亮 点 】".$item['intro']."\n━┉┉┉┉∞┉┉┉┉━\n【 下 单 】:复制整段信息，打开→手机淘宝→即可领券下单内部码：".$kouling;

							}

							else
								$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
										return convert_dwz($matches[0]) . "&uid=" . $urlid;
									},$item['memo']) . $mm1 . $kouling . $mm2 . $mm3 . $mm4;

							if(C('SITE_APPNAME') != 'yhg')$memo = "下单链接:" .$baseurl.$urlid . "\n" .$memo;
							$item_arr[]  = array('no'=>$no,'group'=>$proxy['wxgroup'],'memo'=>$memo ,'img'=>$imgurl);
						}

					}

				}
			}
			if($item_arr) echo $json = json_encode($item_arr);
		}
	}

	public function order_json(){
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


	public	function ajjl(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$no = $_GET['no'];
		$send = $_GET['wx'];
		$p = $_GET['p'];
		//$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";

		$mm1 = "  ||-------------------
||长按复制这条消息，打开→手机淘宝→即可领卷下单";
		$mm2 = "";
		$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
		if($item){
			if($item['imgmemo'] == '')		{

                $imgurl = $item['img']  . "_290x290.jpg";

            }
			else {
				$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
				preg_match($preg, $item['imgmemo'], $imgArr);
				$imgurl = $imgArr[1];
			}
			echo $imgurl . "[]";

			if($p != "")$proxys = M("TbkqqProxy")->where(array("sendwx"=>$send,"wxstatus"=>'1','proxy'=>$p))->select();
				$proxys = M("TbkqqProxy")->where(array("sendwx"=>$send,"wxstatus"=>'1'))->select();
				if($proxys){
					foreach($proxys as $proxy){
						//$proxyid = substr($proxy['proxy'], -3, 3);
                        $site = get_siteurl_by_login($proxy['proxy']);
                        $baseurl = "http://dwz." . $site["base_url"];
                        $imgurl = "http://img." . $site["base_url"];

                        $www = $site['url'];
						$mm3 = "||-------------------
||省钱网站：http://" . $www;
						$mm4 = "";
						$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
						$urlid = $itemurl['id'];
						$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
						$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
								return convert_dwz($matches[0]) . "&uid=" . $urlid;
							},$item['memo']) . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
						if(substr($proxy['proxy'],0,strlen('yhg')) != 'yhg')$memo = "下单链接:" .$baseurl. "/?id=" .$urlid . "||" .$memo;
						$item_arr[]  = array('no'=>$no,'group'=>$proxy['wxgroup'],'memo'=>$memo ,'img'=>$imgurl);
					}

				}


		}
		if($item_arr) {
			$i = 1;
			foreach($item_arr as $item){
				echo $i  . "{}" . $item['group'] . "{}" . $item['no'] . "{}" . $item['memo'] . "[]";
			}
		}

	}


    public function taoke_info_v1(){
        if(IS_POST){
            $appname = C("SITE_APPNAME");

            $msg = $_POST['msg'];
            $proxywx = $_POST['proxywx'];
            $wxgroup = $_POST['group'];
            $iid = $_POST['iid'];
            $qq ='0';
            if($wxgroup != ''){
                $proxy = M("TbkqqProxy")->where(array("wxgroup"=>$wxgroup))->find();
                if($proxy) {$g ="1";}
                else $proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            }
            else {
                $g = "1";
                if($proxywx != '')$proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
                else{

                    $proxy = M("TbkqqProxy")->where("proxy like '%001'")->find();
                }
            }
            if($proxy){
                $fcrate = get_yongjin_by_proxy($proxy['proxy']);
                if($iid == ''){
                    preg_match('/https?:\/\/[\w=.?&\/;]+/',$msg,$match);
                    $s = $match[0];
                    if (!empty($s)) {
                        $host = parse_url($s, PHP_URL_HOST);
                        if ($host != 'a.m.taobao.com' && $host != 'uland.taobao.com' && $host != 'item.taobao.com' && $host != 'detail.tmall.com' && $host != 'detail.m.tmall.com' && $host != 'item.m.taobao.com' && $host != 'h5.m.taobao.com' && $host != 's.click.taobao.com') {
                            $str = file_get_contents($s);
                            preg_match('/var url = \'(https?:\/\/.*)\';/', $str, $match);
                            $s = $match[1];
                        }
                        $host = parse_url($s, PHP_URL_HOST);
                        if ($host == 'a.m.taobao.com') {
                            preg_match('/\/i(\d+)\.htm/', $s, $match);
                            $iid = $match[1];
                        } elseif ($host == "uland.taobao.com") {
                            $data = get_url_data($s);
                            $iid = $data['itemId'];
                            $activityId = $data['activityId'];

                        } elseif ($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com') {
                            $data = get_url_data($s);
                            $iid = $data['id'];
                        } elseif ($host == 's.click.taobao.com') {
                            $data = get_item($s);
                            $iid = $data['id'];
                        }
                    }
                }
                if($iid != "") {

                    $username = C("ROBOT_USERNAME");//"szh166888";
                    $media = M("TbkqqTaokeMedia")->where(array("proxy" => $proxy['proxy'], "username" => $username, "status" => '1'))->find();
                    if ($media) {
                        $mediaid = $media['mediaid'];
                        $adid = $media['adid'];
                        $pid = $media['pid'];

                        $t = time();

                        $cookie = get_cookie_by_username($username);

                        if ($cookie != "") {
                            $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
                            $str = openhttp_header($u, '', $cookie);
                            $arr = json_decode($str, true);
                            $sellerId = $arr['data']['pageList'][0]['sellerId'];
                            $tkRate = $arr['data']['pageList'][0]['tkRate'];
                            $eventRate = $arr['data']['pageList'][0]['eventRate'];
                            $img = $arr['data']['pageList'][0]['pictUrl'];
                            $title = $arr['data']['pageList'][0]['title'];
                            if ($title == "") {
                                echo "找不到链接，可能商家做了调整，请联系人工客服确认！";
                                \Think\Log::write("title找不到！  " . $u, 'WARN');
                                exit();
                            }
                            $price = $arr['data']['pageList'][0]['zkPrice'];
                            if ($price == "") $price = $arr['data']['pageList'][0]['reservePrice'];
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
                                            $rate = $eventRate;
                                            $qq ='1';
                                        }
                                    }


                                }

                            } else {
                                $rate = $tkRate;
                                if ($eventRate != '') {
                                    if ($rate < $eventRate) {
                                        $rate = $eventRate;
                                        $qq = '1';
                                    }
                                }
                            }


                            if ($activityId != "") {

                                $url = "http://uland.taobao.com/cp/coupon?activityId={$activityId}&itemId={$iid}";
                                $coupon = get_coupon_info_v1($url);
                                if (!$coupon) {
                                    $activityId = "";
                                }

                            }

                            if ($activityId == "") {
                                $quan = get_activeid_by_iid($iid);
                                if ($quan) {
                                    $activityId = $quan['id'];
                                    $quan_str = $quan['str'];
                                }
                            }
                            if ($activityId == "") {
                                $qtk_url = "http://www.qingtaoke.com/api/UserPlan/UserCouponList?sid=$sellerId&gid=$iid";

                                $qtk_cookie = '0f4242763305a0552f39b61843503c26=00f0a62ca24de37b108a0eb6e6912654c0e682cda%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; __cfduid=d9298988d33cf5d025e50f0800d2423041458637207; pgv_pvi=2078657173; PHPSESSID=ihq951b8fbu2nadenrid8bol90; CNZZDATA1256913876=1220425662-1458636544-%7C1472479995; PHPSESSID=beqahligcpq4lpvr5vq7pgatb7; 525d2e01d0d4d2fb427b9926b3856822=9f8b7d357c8e2e558451a99314f15895e633cea9a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; qtk_sin=4527me5sgs6muvtbu97frl0f10; CNZZDATA1258823063=1707445734-1471852188-null%7C1484523769; qtkwww_=a855d0ebd8c88a93f8a96697521de895919b9ea7a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D';
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $qtk_url);
                                curl_setopt($ch, CURLOPT_COOKIE, $qtk_cookie);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                                $str = curl_exec($ch);
                                curl_close($ch);
                                $arr = json_decode($str, true);
                                if ($arr['data']) {

                                    $quan_str = "";
                                    $quan_arr = array();
                                    foreach ($arr['data'] as $data) {

                                        if ($data['remain'] > 0) {
                                            if ($price > $data['applyAmount']) {
                                                if ($data['activity_id'] == "") $quan_arr[] = $data['activityId'];
                                                else $quan_arr[] = $data['activity_id'];

                                                $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?activityId=" . $data['activityId'] . "&sellerId=" . $sellerId;
                                                $header = array();
                                                $header[] = "Accept-Language: zh-CN,zh;q=0.8";
                                                $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $quan_link);
                                                //curl_setopt($ch, CURLOPT_REFERER, $tu);
                                                curl_setopt($ch, CURLOPT_HEADER, true);
                                                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                                                //curl_setopt($ch, CURLOPT_NOBODY,1);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                                curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
                                                $out = curl_exec($ch);
                                                curl_close($ch);
                                                if (preg_match('/<dd>(.*)<\/dd>/', $out, $match))
                                                    $quan_str .= $match[1] . "：" . $quan_link . "\n";
                                            }
                                        }
                                    }

                                } else $quan_str = "";

                            }

                            if ($activityId == "") {
                                if ($qq == '1')
                                    $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=3&channel=tk_qqhd&t=$t";
                                else
                                    $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1&t=$t";
                                $str = openhttp_header($u, '', $cookie);
                                if ($str != "") {
                                    $arr = json_decode($str, true);
                                    if ($arr['data']['couponLink'] != "") $link = $arr['data']['couponLink'];
                                    else {
                                        $link = $arr['data']['shortLinkUrl'];
                                        if ($quan_str == "") $quan_str = " 没找到券，价格合适可以直接购买";
                                    }

                                    if ($arr['data']['couponLinkTaoToken'] != "") $taotoken = $arr['data']['couponLinkTaoToken'];
                                    else $taotoken = $arr['data']['taoToken'];
                                    if ($appname == "yhg" || $g == "1") $yongjin = "";
                                    else {
                                        if ($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'], 2);
                                        else $yongjin = round($rate * $fcrate, 2);
                                    }

                                    if ($link == "") {
                                        \Think\Log::write("link找不到！v1  " . $u, 'WARN');

                                        $ldata = array();
                                        $ldata['url'] = "https://uland.taobao.com/coupon/edetail?pid=" . $pid . "&itemId=" . $iid . "&src=cd_cdll";
                                        $ldata['logo'] = $img;
                                        $ldata['text'] = $title;

                                        $taotoken = get_taotoken($ldata);
                                        $quan_str = "券被抢光，请在购买时留意领券！";
                                        echo "【" . $title . "】". $yongjin . " 淘口令：" . $taotoken . $quan_str;
                                    } else {
                                        echo "【" . $title . "】" . $yongjin . "下单链接：" . $link . " 淘口令：" . $taotoken . $quan_str;
                                    }
                                } else echo "找不到这个链接，可能机器人掉线了，请联系人工客服c!";
                            } else {
                                if ($qq == '1')
                                    $dx = "0";
                                else $dx = "1";
                                $link = "https://uland.taobao.com/coupon/edetail?activityId=" . $activityId . "&pid=" . $pid . "&itemId=" . $iid . "&src=qhkj_dtkp&dx=" . $dx;
                                if ($appname == "yhg" || $g == "1") $yongjin = "";
                                else {
                                    if ($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'], 2);
                                    else $yongjin = round($rate * $fcrate, 2);
                                }
                                $token_data = array();
                                $token_data['logo'] = $img;
                                $token_data['text'] = $title;
                                $token_data['url'] = $link;
                                $taotoken = get_taotoken($token_data);

                                if ($taotoken == '') $taotoken = get_taotoken($token_data);
                                if ($taotoken == '') $taotoken = get_taotoken($token_data);
                                if ($taotoken == '') $taotoken = get_taotoken($token_data);

                                echo "【" . $title . "】" . $yongjin . "下单链接：" . $link . $quan_str . "口令：" . $taotoken;
                            }
                        }
                    }

                }
                else echo "这个链接格式机器人认不到，请手工打开然后通过手机淘宝分享";

            }
            else {
                echo "你没有登记代理编号，请@我，并输入代理帐号：" . $appname . "001  这种格式进行登记";
            }
        }
    }


    public function taoke_info_openid(){
        if(IS_POST){
            $appname = C("SITE_APPNAME");

            $msg = $_POST['msg'];
            $openid = $_POST['openid'];

            $iid = $_POST['iid'];
            $qq ='0';



                if($openid != '')$proxy = M("TbkqqProxy")->where(array("openid"=>$openid))->find();
                else{

                    $proxy = M("TbkqqProxy")->where("proxy like '%001'")->find();
                }

            if($proxy){
                $fcrate = get_yongjin_by_proxy($proxy['proxy']);
                if($iid == ''){
                    preg_match('/https?:\/\/[\w=.?&\/;%]+/',$msg,$match);
                    $s = $match[0];

                    if (!empty($s)) {

                        $host = parse_url($s, PHP_URL_HOST);
                        if ($host != 'a.m.taobao.com' && $host != 'uland.taobao.com' && $host != 'item.taobao.com' && $host != 'detail.tmall.com' && $host != 'detail.m.tmall.com' && $host != 'item.m.taobao.com' && $host != 'h5.m.taobao.com' && $host != 's.click.taobao.com') {
                            $str = file_get_contents($s);
                            preg_match('/var url = \'(https?:\/\/.*)\';/', $str, $match);
                            $s = $match[1];
                        }
                        $host = parse_url($s, PHP_URL_HOST);
                        if ($host == 'a.m.taobao.com') {
                            preg_match('/\/i(\d+)\.htm/', $s, $match);
                            $iid = $match[1];
                        } elseif ($host == "uland.taobao.com") {
                            $data = get_url_data($s);
                            $iid = $data['itemId'];
                            $activityId = $data['activityId'];

                        } elseif ($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com') {
                            $data = get_url_data($s);
                            $iid = $data['id'];
                        } elseif ($host == 's.click.taobao.com') {
                            $data = get_item($s);
                            $iid = $data['id'];
                        }
                    }
                }
                if($iid != "") {

                    $username = C("ROBOT_USERNAME");//"szh166888";
                    $media = M("TbkqqTaokeMedia")->where(array("proxy" => $proxy['proxy'], "username" => $username, "status" => '1'))->find();
                    if ($media) {
                        $mediaid = $media['mediaid'];
                        $adid = $media['adid'];
                        $pid = $media['pid'];

                        $t = time();

                        $cookie = get_cookie_by_username($username);

                        if ($cookie != "") {
                            $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
                            $str = openhttp_header($u, '', $cookie);
                            $arr = json_decode($str, true);
                            $sellerId = $arr['data']['pageList'][0]['sellerId'];
                            $tkRate = $arr['data']['pageList'][0]['tkRate'];
                            $eventRate = $arr['data']['pageList'][0]['eventRate'];
                            $img = $arr['data']['pageList'][0]['pictUrl'];
                            $title = $arr['data']['pageList'][0]['title'];
                            if ($title == "") {
                                echo "找不到链接，可能商家做了调整，请联系人工客服确认！";
                                \Think\Log::write("title找不到！  " . $u, 'WARN');
                                exit();
                            }
                            $price = $arr['data']['pageList'][0]['zkPrice'];
                            if ($price == "") $price = $arr['data']['pageList'][0]['reservePrice'];
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
                                            $rate = $eventRate;
                                            $qq ='1';
                                        }
                                    }


                                }

                            } else {
                                $rate = $tkRate;
                                if ($eventRate != '') {
                                    if ($rate < $eventRate) {
                                        $rate = $eventRate;
                                        $qq = '1';
                                    }
                                }
                            }


                            if ($activityId != "") {

                                $url = "http://uland.taobao.com/cp/coupon?activityId={$activityId}&itemId={$iid}";
                                $coupon = get_coupon_info_v1($url);
                                if (!$coupon) {
                                    $activityId = "";
                                }

                            }

                            if ($activityId == "") {
                                $quan = get_activeid_by_iid($iid);
                                if ($quan) {
                                    $activityId = $quan['id'];
                                    $quan_str = $quan['str'];
                                }
                            }
                            if ($activityId == "") {
                                $qtk_url = "http://www.qingtaoke.com/api/UserPlan/UserCouponList?sid=$sellerId&gid=$iid";

                                $qtk_cookie = '0f4242763305a0552f39b61843503c26=00f0a62ca24de37b108a0eb6e6912654c0e682cda%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; __cfduid=d9298988d33cf5d025e50f0800d2423041458637207; pgv_pvi=2078657173; PHPSESSID=ihq951b8fbu2nadenrid8bol90; CNZZDATA1256913876=1220425662-1458636544-%7C1472479995; PHPSESSID=beqahligcpq4lpvr5vq7pgatb7; 525d2e01d0d4d2fb427b9926b3856822=9f8b7d357c8e2e558451a99314f15895e633cea9a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; qtk_sin=4527me5sgs6muvtbu97frl0f10; CNZZDATA1258823063=1707445734-1471852188-null%7C1484523769; qtkwww_=a855d0ebd8c88a93f8a96697521de895919b9ea7a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D';
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $qtk_url);
                                curl_setopt($ch, CURLOPT_COOKIE, $qtk_cookie);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                                $str = curl_exec($ch);
                                curl_close($ch);
                                $arr = json_decode($str, true);
                                if ($arr['data']) {

                                    $quan_str = "";
                                    $quan_arr = array();
                                    foreach ($arr['data'] as $data) {

                                        if ($data['remain'] > 0) {
                                            if ($price > $data['applyAmount']) {
                                                if ($data['activity_id'] == "") $quan_arr[] = $data['activityId'];
                                                else $quan_arr[] = $data['activity_id'];

                                                $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?activityId=" . $data['activityId'] . "&sellerId=" . $sellerId;
                                                $header = array();
                                                $header[] = "Accept-Language: zh-CN,zh;q=0.8";
                                                $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $quan_link);
                                                //curl_setopt($ch, CURLOPT_REFERER, $tu);
                                                curl_setopt($ch, CURLOPT_HEADER, true);
                                                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                                                //curl_setopt($ch, CURLOPT_NOBODY,1);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                                curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
                                                $out = curl_exec($ch);
                                                curl_close($ch);
                                                if (preg_match('/<dd>(.*)<\/dd>/', $out, $match))
                                                    $quan_str .= $match[1] . "：" . $quan_link . "\n";
                                            }
                                        }
                                    }

                                } else $quan_str = "";

                            }

                            if ($activityId == "") {
                                if ($qq == '1')
                                    $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=3&channel=tk_qqhd&t=$t";
                                else
                                    $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1&t=$t";
                                $str = openhttp_header($u, '', $cookie);
                                if ($str != "") {
                                    $arr = json_decode($str, true);
                                    if ($arr['data']['couponLink'] != "") $link = $arr['data']['couponLink'];
                                    else {
                                        $link = $arr['data']['shortLinkUrl'];
                                        if ($quan_str == "") $quan_str = " 没找到券，价格合适可以直接购买";
                                    }

                                    if ($arr['data']['couponLinkTaoToken'] != "") $taotoken = $arr['data']['couponLinkTaoToken'];
                                    else $taotoken = $arr['data']['taoToken'];
                                    if ($appname == "yhg" || $g == "1") $yongjin = "";
                                    else {
                                        if ($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'], 2);
                                        else $yongjin = round($rate * $fcrate, 2);
                                    }

                                    if ($link == "") {
                                        \Think\Log::write("link找不到！v1  " . $u, 'WARN');

                                        $ldata = array();
                                        $ldata['url'] = "https://uland.taobao.com/coupon/edetail?pid=" . $pid . "&itemId=" . $iid . "&src=cd_cdll";
                                        $ldata['logo'] = $img;
                                        $ldata['text'] = $title;

                                        $taotoken = get_taotoken($ldata);
                                        $quan_str = "券被抢光，请在购买时留意领券！";
                                        echo "【" . $title . "】". $yongjin . " 淘口令：" . $taotoken . $quan_str;
                                    } else {
                                        echo "【" . $title . "】" . $yongjin . "下单链接：" . $link . " 淘口令：" . $taotoken . $quan_str;
                                    }
                                } else echo "找不到这个链接，可能机器人掉线了，请联系人工客服c!";
                            } else {
                                if ($qq == '1')
                                    $dx = "0";
                                else $dx = "1";
                                $link = "https://uland.taobao.com/coupon/edetail?activityId=" . $activityId . "&pid=" . $pid . "&itemId=" . $iid . "&src=qhkj_dtkp&dx=" . $dx;
                                if ($appname == "yhg" || $g == "1") $yongjin = "";
                                else {
                                    if ($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'], 2);
                                    else $yongjin = round($rate * $fcrate, 2);
                                }
                                $token_data = array();
                                $token_data['logo'] = $img;
                                $token_data['text'] = $title;
                                $token_data['url'] = $link;
                                $taotoken = get_taotoken($token_data);

                                if ($taotoken == '') $taotoken = get_taotoken($token_data);
                                if ($taotoken == '') $taotoken = get_taotoken($token_data);
                                if ($taotoken == '') $taotoken = get_taotoken($token_data);

                                echo "【" . $title . "】" . $yongjin . "下单链接：" . $link . $quan_str . "口令：" . $taotoken;
                            }
                        }
                    }

                }
                else echo "这个链接格式机器人认不到，请手工打开然后通过手机淘宝分享";

            }
            else {
                echo "你没有登记代理编号，请@我，并输入代理帐号：" . $appname . "001  这种格式进行登记";
            }
        }
    }


    public function taoke_info_proxy(){
        if(IS_POST){
            $appname = C("SITE_APPNAME");

            $msg = $_POST['msg'];
            $proxyid = $_POST['proxy'];

            $iid = $_POST['iid'];
            $qq ='0';



            if($proxyid != '')$proxy = M("TbkqqProxy")->where(array("proxy"=>$proxyid))->find();
            else{

                $proxy = M("TbkqqProxy")->where("proxy like '%001'")->find();
            }

            if($proxy){
                $fcrate = get_yongjin_by_proxy($proxy['proxy']);
                if($iid == ''){
                    preg_match('/https?:\/\/[\w=.?&\/;%]+/',$msg,$match);
                    $s = $match[0];

                    if (!empty($s)) {

                        $host = parse_url($s, PHP_URL_HOST);
                        if ($host != 'a.m.taobao.com' && $host != 'uland.taobao.com' && $host != 'item.taobao.com' && $host != 'detail.tmall.com' && $host != 'detail.m.tmall.com' && $host != 'item.m.taobao.com' && $host != 'h5.m.taobao.com' && $host != 's.click.taobao.com') {
                            $str = file_get_contents($s);
                            preg_match('/var url = \'(https?:\/\/.*)\';/', $str, $match);
                            $s = $match[1];
                        }
                        $host = parse_url($s, PHP_URL_HOST);
                        if ($host == 'a.m.taobao.com') {
                            preg_match('/\/i(\d+)\.htm/', $s, $match);
                            $iid = $match[1];
                        } elseif ($host == "uland.taobao.com") {
                            $data = get_url_data($s);
                            $iid = $data['itemId'];
                            $activityId = $data['activityId'];

                        } elseif ($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com') {
                            $data = get_url_data($s);
                            $iid = $data['id'];
                        } elseif ($host == 's.click.taobao.com') {
                            $data = get_item($s);
                            $iid = $data['id'];
                        }
                    }
                }
                if($iid != "") {

                    $username = C("ROBOT_USERNAME");//"szh166888";
                    $media = M("TbkqqTaokeMedia")->where(array("proxy" => $proxy['proxy'], "username" => $username, "status" => '1'))->find();
                    if ($media) {
                        $mediaid = $media['mediaid'];
                        $adid = $media['adid'];
                        $pid = $media['pid'];

                        $t = time();

                        $cookie = get_cookie_by_username($username);

                        if ($cookie != "") {
                            $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
                            $str = openhttp_header($u, '', $cookie);
                            $arr = json_decode($str, true);
                            $sellerId = $arr['data']['pageList'][0]['sellerId'];
                            $tkRate = $arr['data']['pageList'][0]['tkRate'];
                            $eventRate = $arr['data']['pageList'][0]['eventRate'];
                            $img = $arr['data']['pageList'][0]['pictUrl'];
                            $title = $arr['data']['pageList'][0]['title'];
                            if ($title == "") {
                                echo "找不到链接，可能商家做了调整，请联系人工客服确认！";
                                \Think\Log::write("title找不到！  " . $u, 'WARN');
                                exit();
                            }
                            $price = $arr['data']['pageList'][0]['zkPrice'];
                            if ($price == "") $price = $arr['data']['pageList'][0]['reservePrice'];
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
                                            $rate = $eventRate;
                                            $qq ='1';
                                        }
                                    }


                                }

                            } else {
                                $rate = $tkRate;
                                if ($eventRate != '') {
                                    if ($rate < $eventRate) {
                                        $rate = $eventRate;
                                        $qq = '1';
                                    }
                                }
                            }


                            if ($activityId != "") {

                                $url = "http://uland.taobao.com/cp/coupon?activityId={$activityId}&itemId={$iid}";
                                $coupon = get_coupon_info_v1($url);
                                if (!$coupon) {
                                    $activityId = "";
                                }

                            }

                            if ($activityId == "") {
                                $quan = get_activeid_by_iid($iid);
                                if ($quan) {
                                    $activityId = $quan['id'];
                                    $quan_str = $quan['str'];
                                }
                            }
                            if ($activityId == "") {
                                $qtk_url = "http://www.qingtaoke.com/api/UserPlan/UserCouponList?sid=$sellerId&gid=$iid";

                                $qtk_cookie = '0f4242763305a0552f39b61843503c26=00f0a62ca24de37b108a0eb6e6912654c0e682cda%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; __cfduid=d9298988d33cf5d025e50f0800d2423041458637207; pgv_pvi=2078657173; PHPSESSID=ihq951b8fbu2nadenrid8bol90; CNZZDATA1256913876=1220425662-1458636544-%7C1472479995; PHPSESSID=beqahligcpq4lpvr5vq7pgatb7; 525d2e01d0d4d2fb427b9926b3856822=9f8b7d357c8e2e558451a99314f15895e633cea9a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; qtk_sin=4527me5sgs6muvtbu97frl0f10; CNZZDATA1258823063=1707445734-1471852188-null%7C1484523769; qtkwww_=a855d0ebd8c88a93f8a96697521de895919b9ea7a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D';
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $qtk_url);
                                curl_setopt($ch, CURLOPT_COOKIE, $qtk_cookie);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                                $str = curl_exec($ch);
                                curl_close($ch);
                                $arr = json_decode($str, true);
                                if ($arr['data']) {

                                    $quan_str = "";
                                    $quan_arr = array();
                                    foreach ($arr['data'] as $data) {

                                        if ($data['remain'] > 0) {
                                            if ($price > $data['applyAmount']) {
                                                if ($data['activity_id'] == "") $quan_arr[] = $data['activityId'];
                                                else $quan_arr[] = $data['activity_id'];

                                                $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?activityId=" . $data['activityId'] . "&sellerId=" . $sellerId;
                                                $header = array();
                                                $header[] = "Accept-Language: zh-CN,zh;q=0.8";
                                                $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $quan_link);
                                                //curl_setopt($ch, CURLOPT_REFERER, $tu);
                                                curl_setopt($ch, CURLOPT_HEADER, true);
                                                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                                                //curl_setopt($ch, CURLOPT_NOBODY,1);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                                curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
                                                $out = curl_exec($ch);
                                                curl_close($ch);
                                                if (preg_match('/<dd>(.*)<\/dd>/', $out, $match))
                                                    $quan_str .= $match[1] . "：" . $quan_link . "\n";
                                            }
                                        }
                                    }

                                } else $quan_str = "";

                            }

                            if ($activityId == "") {
                                if ($qq == '1')
                                    $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=3&channel=tk_qqhd&t=$t";
                                else
                                    $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1&t=$t";
                                $str = openhttp_header($u, '', $cookie);
                                if ($str != "") {
                                    $arr = json_decode($str, true);
                                    if ($arr['data']['couponLink'] != "") $link = $arr['data']['couponLink'];
                                    else {
                                        $link = $arr['data']['shortLinkUrl'];
                                        if ($quan_str == "") $quan_str = " 没找到券，价格合适可以直接购买";
                                    }

                                    if ($arr['data']['couponLinkTaoToken'] != "") $taotoken = $arr['data']['couponLinkTaoToken'];
                                    else $taotoken = $arr['data']['taoToken'];
                                    if ($appname == "yhg" || $g == "1") $yongjin = "";
                                    else {
                                        if ($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'], 2);
                                        else $yongjin = round($rate * $fcrate, 2);
                                    }

                                    if ($link == "") {
                                        \Think\Log::write("link找不到！v1  " . $u, 'WARN');

                                        $ldata = array();
                                        $ldata['url'] = "https://uland.taobao.com/coupon/edetail?pid=" . $pid . "&itemId=" . $iid . "&src=cd_cdll";
                                        $ldata['logo'] = $img;
                                        $ldata['text'] = $title;

                                        $taotoken = get_taotoken($ldata);
                                        $quan_str = "券被抢光，请在购买时留意领券！";
                                        echo "【" . $title . "】". $yongjin . " 淘口令：" . $taotoken . $quan_str;
                                    } else {
                                        echo "【" . $title . "】" . $yongjin . "下单链接：" . $link . " 淘口令：" . $taotoken . $quan_str;
                                    }
                                } else echo "找不到这个链接，可能机器人掉线了，请联系人工客服c!";
                            } else {
                                if ($qq == '1')
                                    $dx = "0";
                                else $dx = "1";
                                $link = "https://uland.taobao.com/coupon/edetail?activityId=" . $activityId . "&pid=" . $pid . "&itemId=" . $iid . "&src=qhkj_dtkp&dx=" . $dx;
                                if ($appname == "yhg" || $g == "1") $yongjin = "";
                                else {
                                    if ($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'], 2);
                                    else $yongjin = round($rate * $fcrate, 2);
                                }
                                $token_data = array();
                                $token_data['logo'] = $img;
                                $token_data['text'] = $title;
                                $token_data['url'] = $link;
                                $taotoken = get_taotoken($token_data);

                                if ($taotoken == '') $taotoken = get_taotoken($token_data);
                                if ($taotoken == '') $taotoken = get_taotoken($token_data);
                                if ($taotoken == '') $taotoken = get_taotoken($token_data);

                                echo "【" . $title . "】" . $yongjin . "下单链接：" . $link . $quan_str . "口令：" . $taotoken;
                            }
                        }
                    }

                }
                else echo "这个链接格式机器人认不到，请手工打开然后通过手机淘宝分享";

            }
            else {
                echo "你没有登记代理编号，请@我，并输入代理帐号：" . $appname . "001  这种格式进行登记";
            }
        }
    }

	public function search_item_by_key(){
        if(IS_POST) {
            $appname = C("SITE_APPNAME");
            $fcrate = C('YONGJIN_RATE');
            $key = $_POST['key'];
            $proxywx = $_POST['proxywx'];
            $wxgroup = $_POST['group'];
            if($_POST['debug'] == '') $debug = false;
            else $debug = true;
            if($wxgroup != ''){
                $proxy = M("TbkqqProxy")->where(array("wxgroup"=>$wxgroup))->find();
                if($proxy) {$g ="1";}
                else $proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            }
            else 	$proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            if($proxy){
                $dataoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
                $where = "title like '%" . $key . "%'";
                $item = $dataoke_model->where($where)->order("commission desc")->find();
                if($item){
                    echo "@" . $proxywx . $item['iid'];
                }
                else echo "@" . $proxywx . "没找到";
            }
            else echo "@" . $proxywx . "没找到";


        }
    }

    public function search_temai_by_key_proxy(){
        if(IS_POST) {

            $proxyid = $_POST['proxy'];

            $key = $_POST['kw'];

            $proxy = M("TbkqqProxy")->where(array("proxy"=>$proxyid))->find();
            if($proxy){
                $pid = $proxy['pid'];
            }
            else {
                $proxy = M("TbkqqProxy")->where(array("proxy"=>"taotehui001"))->find();
                if($proxy){
                    $pid = $proxy['pid'];
                }
                else $pid = "mm_49479257_13814911_56052501";
            }
            $url = "https://temai.m.taobao.com/search.htm?q=" .urlencode(trim($key)) . "&pid={$pid}";


            $token_data = array();
            $token_data['logo'] = "";
            $token_data['text'] = $key;
            $token_data['url'] = $url;
            $taotoken = get_taotoken($token_data);

            if ($taotoken == '') $taotoken = get_taotoken($token_data);
            if ($taotoken == '') $taotoken = get_taotoken($token_data);
            if ($taotoken == '') $taotoken = get_taotoken($token_data);


            echo "为你找到跟" . $key . "相关的产品，详情请复制淘口令:{$taotoken}。";



        }
    }

    public function test_temai_by_key_proxy(){
        $pid = "mm_49479257_13814911_56052501";
           $key = "衣服";
            //$url = "https://temai.m.taobao.com/search.htm?q=" .urlencode(trim($key)) . "&pid={$pid}";
        $url = "https://s.click.taobao.com/pd80jZw";
        $token_data = array();
            $token_data['logo'] = "";
            $token_data['text'] = "要找的宝贝：" .$key;
            $token_data['url'] = $url;
            $taotoken = get_taotoken($token_data);

            if ($taotoken == '') $taotoken = get_taotoken_all($token_data);

            echo "为你找到跟" . $key . "相关的产品，详情请复制淘口令:{$taotoken}。";


    }

    public function search_temai_by_key_openid(){
        if(IS_POST) {

            $openid = $_POST['openid'];

            $key = $_POST['kw'];

            $proxy = M("TbkqqProxy")->where(array("openid"=>$openid))->find();
            if($proxy){
                $pid = $proxy['pid'];
            }
            else {
                $proxy = M("TbkqqProxy")->where(array("proxy"=>"taotehui001"))->find();
                if($proxy){
                    $pid = $proxy['pid'];
                }
                else $pid = "mm_49479257_13814911_56052501";
            }
            $url = "https://temai.m.taobao.com/search.htm?q=" .urlencode(trim($key)) . "&pid={$pid}";
            $token_data = array();
            $token_data['logo'] = "";
            $token_data['text'] = $key;
            $token_data['url'] = $url;
            $taotoken = get_taotoken($token_data);

            if ($taotoken == '') $taotoken = get_taotoken($token_data);
            if ($taotoken == '') $taotoken = get_taotoken($token_data);
            if ($taotoken == '') $taotoken = get_taotoken($token_data);
            echo "为你找到跟" . $key . "相关的产品，详情请复制淘口令:{$taotoken}。";

        }
    }


    public function search_temai_by_key(){
        if(IS_POST) {

            $proxywx = $_POST['proxywx'];
            $wxgroup = $_POST['group'];
            $key = $_POST['kw'];

            if($wxgroup != ''){
                $proxy = M("TbkqqProxy")->where(array("wxgroup"=>$wxgroup))->find();
                if($proxy) {$g ="1";}
                else $proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            }
            else 	$proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            if($proxy){
                $pid = $proxy['pid'];
            }
            else {
                $proxy = M("TbkqqProxy")->where(array("proxy"=>"taotehui001"))->find();
                if($proxy){
                    $pid = $proxy['pid'];
                }
                else $pid = "mm_49479257_13814911_56052501";
            }
            $url = "https://temai.m.taobao.com/search.htm?q=" .urlencode(trim($key)) . "&pid={$pid}";
            $token_data = array();
            $token_data['logo'] = "";
            $token_data['text'] = $key;
            $token_data['url'] = $url;
            $taotoken = get_taotoken($token_data);

            if ($taotoken == '') $taotoken = get_taotoken($token_data);
            if ($taotoken == '') $taotoken = get_taotoken($token_data);
            if ($taotoken == '') $taotoken = get_taotoken($token_data);
            echo "为你找到跟" . $key . "相关的产品，详情请复制淘口令:{$taotoken}。";

        }
    }

    public function search_items_by_key(){
        if(IS_POST) {
            $appname = C("SITE_APPNAME");
            $fcrate = C('YONGJIN_RATE');
            $proxywx = $_POST['proxywx'];
            $wxgroup = $_POST['group'];
            $key = $_POST['kw'];

            if($wxgroup != ''){
                $proxy = M("TbkqqProxy")->where(array("wxgroup"=>$wxgroup))->find();
                if($proxy) {$g ="1";}
                else $proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            }
            else 	$proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            if($proxy){
                $proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
                $url = "http://" . $proxyid . "." .  C("BASE_DOMAIN");
                $site = get_siteurl_by_login($proxy['proxy']);
                $url = "http://". $site['url'];
                $dataoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
                $where = "title like '%" . $key . "%'";
                $item_count = $dataoke_model->where($where)->count();
                if($item_count>0){
                    echo "共为你找到" . $item_count . "个跟" . $key . "相关的产品，详情请点击：" . $url . '/?r=l&kw=' . urlencode(trim($key));
                }
                else echo "没找到跟" . $key . "相关的产品，请重新换一个关键词！";
            }
            else echo "无法为你找产品！";

        }
    }

    public function get_proxy_url(){
        if(IS_POST) {
            $appname = C("SITE_APPNAME");
            $fcrate = C('YONGJIN_RATE');
            $proxywx = $_POST['proxywx'];
            $wxgroup = $_POST['group'];
            if($wxgroup != ''){
                $proxy = M("TbkqqProxy")->where(array("wxgroup"=>$wxgroup))->find();
                if($proxy) {
                    $g ="1";
                    $proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
                    $site = get_siteurl_by_login($proxy['proxy']);
                    $url = "http://". $site['url'];
                    echo "http://" . $url;
                }
                else {
                    $proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
                    if($proxy){
                        $site = get_siteurl_by_login($proxy['proxy']);
                        $url = "http://". $site['url'];
                        //$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
                        echo "http://" . $url;
                    }
                }
            }
        }
    }

    public function get_taoke_by_iid(){
        if(IS_POST) {
            $appname = C("SITE_APPNAME");
            $fcrate = C('YONGJIN_RATE');
            $iid = $_POST['iid'];
            $proxywx = $_POST['proxywx'];
            $wxgroup = $_POST['group'];

            $qq = '0';
            if($wxgroup != ''){
                $proxy = M("TbkqqProxy")->where(array("wxgroup"=>$wxgroup))->find();
                if($proxy) {$g ="1";}
                else $proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            }
            else 	$proxy = M("TbkqqProxy")->where(array("proxywx"=>$proxywx))->find();
            if($proxy){
                if($iid != ""){
                    $s = "http://item.taobao.com/item.htm?id=$iid";
                    $username = C("ROBOT_USERNAME");//"szh166888";
                    $fcrate = get_yongjin_by_proxy($proxy['proxy']);

                    $media = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy'],"username"=>$username,"status"=>'1'))->find();
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
                                    \Think\Log::write("title找不到！  " . $u,'WARN');
                                    echo "@" . $proxywx . ": 找不到链接，可能商家做了调整，请联系人工客服确认!!";
                                    exit();
                                }
                                $price = $arr['data']['pageList'][0]['zkPrice'];
                                if($price == "")$price = $arr['data']['pageList'][0]['reservePrice'];



                                $u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
                                $str = openhttp_header($u, '', $cookie);
                                $arr = json_decode($str, true);

                                if ($arr['ok'] == '1' && $arr['data']) {
                                    $rate = $tkRate;

                                    $cid = '';
                                    $keeperid = '';
                                    $post = array();

                                    foreach ($arr['data'] as $data) {
                                        if($data['existStatus'] == '2')$existCid = $data['campaignID'];
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
                                        $reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" . $s;
                                        sleep(1);
                                        $ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
                                    }
                                    else {
                                        //exit campaign
                                        if($existCid != ""){
                                            $u = "http://pub.alimama.com/campaign/exitCampaign.json";
                                            $post['pubCampaignid'] = $existCid;
                                            $post['t'] = time();
                                            $cookie_data = excookie($cookie);
                                            $post['_tb_token_'] = $cookie_data['_tb_token_'];
                                            $post_str = "pubCampaignid=" . $post['pubCampaignid'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
                                            $ret = openhttp_header($u, $post_str, $cookie, '', '1');
                                        }
                                        if($eventRate != ''){
                                            if($rate<$eventRate){
                                                $rate = $eventRate;
                                                $qq ='1';
                                            }
                                        }


                                    }

                                }
                                else {
                                    $rate = $tkRate;
                                    if($eventRate != ''){
                                        if($rate<$eventRate){
                                            $rate = $eventRate;
                                            $qq = '1';
                                        }
                                    }
                                }



                                $quan_api = "http://taotehui.co/?g=Tbkqq&m=Page&a=find_quan&iid=$iid";
                                $activityId = file_get_contents($quan_api);

                                if($activityId == ""){


                                    $qtk_url = "http://www.qingtaoke.com/api/UserPlan/UserCouponList?sid=$sellerId&gid=$iid";

                                    $qtk_cookie = '0f4242763305a0552f39b61843503c26=00f0a62ca24de37b108a0eb6e6912654c0e682cda%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; __cfduid=d9298988d33cf5d025e50f0800d2423041458637207; pgv_pvi=2078657173; PHPSESSID=ihq951b8fbu2nadenrid8bol90; CNZZDATA1256913876=1220425662-1458636544-%7C1472479995; PHPSESSID=beqahligcpq4lpvr5vq7pgatb7; 525d2e01d0d4d2fb427b9926b3856822=9f8b7d357c8e2e558451a99314f15895e633cea9a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; qtk_sin=4527me5sgs6muvtbu97frl0f10; CNZZDATA1258823063=1707445734-1471852188-null%7C1484523769; qtkwww_=a855d0ebd8c88a93f8a96697521de895919b9ea7a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D';
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $qtk_url);
                                    curl_setopt($ch, CURLOPT_COOKIE,$qtk_cookie);
                                    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

                                    $str = curl_exec($ch);
                                    curl_close($ch);
                                    $arr = json_decode($str,true);
                                    if($arr['data']){

                                        $quan_str = "";
                                        $quan_arr = array();
                                        foreach($arr['data'] as $data){

                                            if($data['remain']>0) {
                                                if($price>$data['applyAmount']){
                                                    if($data['activity_id'] == "")$quan_arr[] = $data['activityId'];
                                                    else $quan_arr[] = $data['activity_id'];

                                                    $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?activityId=" . $data['activityId'] . "&sellerId=" . $sellerId;
                                                    $header = array();
                                                    $header[] = "Accept-Language: zh-CN,zh;q=0.8";
                                                    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
                                                    $ch = curl_init();
                                                    curl_setopt($ch, CURLOPT_URL, $quan_link);
                                                    //curl_setopt($ch, CURLOPT_REFERER, $tu);
                                                    curl_setopt($ch, CURLOPT_HEADER, true);
                                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                                                    //curl_setopt($ch, CURLOPT_NOBODY,1);
                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
                                                    curl_setopt($ch, CURLOPT_MAXREDIRS,2);
                                                    $out = curl_exec($ch);
                                                    curl_close($ch);
                                                    if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
                                                        $quan_str .=  $match[1] ."：" .$quan_link . "\n";
                                                }
                                            }
                                        }

                                    }
                                    else $quan_str = "";

                                }


                                if($activityId == ""){
                                    if($qq=='1')
                                        $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=3&channel=tk_qqhd&t=$t";
                                    else
                                        $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1&t=$t";
                                    $str = openhttp_header($u,'',$cookie);
                                    if($str != ""){
                                        $arr = json_decode($str,true);
                                        if($arr['data']['couponLink'] != "") $link = $arr['data']['couponLink'];
                                        else {
                                            $link = $arr['data']['shortLinkUrl'];
                                            if($quan_str == "")$quan_str = " 没找到券，价格合适可以直接购买";
                                        }

                                        if($arr['data']['couponLinkTaoToken'] != "") $taotoken = $arr['data']['couponLinkTaoToken'];
                                        else $taotoken = $arr['data']['taoToken'];
                                        if($appname == "yhg" || $g == "1")$yongjin = "";
                                        else{
                                            if($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'],2);
                                            else $yongjin =  round($rate * $fcrate,2);
                                        }

                                        if($link == ""){
                                            \Think\Log::write("link找不到！getbyiid  " . $u,'WARN');

                                            $ldata = array();
                                            $ldata['url'] = "https://uland.taobao.com/coupon/edetail?pid=" . $pid . "&itemId=" . $iid . "&src=cd_cdll";
                                            $ldata['logo'] = $img;
                                            $ldata['text'] = $title;

                                            $taotoken = get_taotoken($ldata);
                                            $quan_str = "券被抢光，请在购买时留意领券！";
                                            echo "@" . $proxywx . ": 【" . $title . "】". $yongjin . " 淘口令：" . $taotoken . $quan_str;
                                        }
                                        else{
                                            echo "@" . $proxywx . ": 【" . $title . "】" .$yongjin . "下单链接：".$link . " 淘口令：" . $taotoken . $quan_str;
                                        }
                                    }
                                    else echo "@" . $proxywx . ": 找不到t这个链接，可能机器人掉线了，请联系人工客服";
                                }
                                else {
                                    if($qq == '1')
                                        $dx = "0";
                                    else $dx = "1";
                                    $link = "https://uland.taobao.com/coupon/edetail?activityId=" .$activityId ."&pid=" . $pid ."&itemId=" . $iid ."&src=qhkj_dtkp&dx=" . $dx;
                                    if($appname == "yhg" || $g == "1")$yongjin = "";
                                    else {
                                        if($proxy['fcrate'] != "")$yongjin = round($rate * $fcrate * $proxy['fcrate'],2);
                                        else $yongjin =  round($rate * $fcrate,2);
                                    }
                                    $token_data = array();
                                    $token_data['logo'] = $img;
                                    $token_data['text'] = $title;
                                    $token_data['url'] = $link;
                                    $taotoken = get_taotoken($token_data);

                                    if($taotoken == '')$taotoken = get_taotoken($token_data);
                                    if($taotoken == '')$taotoken = get_taotoken($token_data);
                                    if($taotoken == '')$taotoken = get_taotoken($token_data);

                                    echo "@" . $proxywx . ": 【" . $title . "】" .$yongjin . "下单链接：".$link . $quan_str . "口令：" .$taotoken;
                                }
                            }
                        }

                    }

                }
            }
            else {
                echo "@" . $proxywx . ": 你没有登记代理编号，请@我，并输入代理帐号：" . $appname . "001  这种格式进行登记";
            }
        }

    }

    public function get_taoke_by_iid_proxy(){
        if(IS_POST) {
            $appname = C("SITE_APPNAME");
            $fcrate = C('YONGJIN_RATE');
            $iid = $_POST['iid'];
            $proxyid = $_POST['proxy'];

            $qq = '0';
            $proxy = M("TbkqqProxy")->where(array("proxy"=>$proxyid))->find();
            if($proxy){
                if($iid != ""){
                    $s = "http://item.taobao.com/item.htm?id=$iid";
                    $username = C("ROBOT_USERNAME");//"szh166888";
                    $fcrate = get_yongjin_by_proxy($proxy['proxy']);
                    $media = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy'],"username"=>$username))->find();
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
                                    echo "找不到链接，可能商家做了调整，请联系人工客服确认";
                                    exit();
                                }
                                $price = $arr['data']['pageList'][0]['zkPrice'];
                                if($price == "")$price = $arr['data']['pageList'][0]['reservePrice'];



                                $u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
                                $str = openhttp_header($u, '', $cookie);
                                $arr = json_decode($str, true);

                                if ($arr['ok'] == '1' && $arr['data']) {
                                    $rate = $tkRate;

                                    $cid = '';
                                    $keeperid = '';
                                    $post = array();

                                    foreach ($arr['data'] as $data) {
                                        if($data['existStatus'] == '2')$existCid = $data['campaignID'];
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
                                        $reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" . $s;
                                        sleep(1);
                                        $ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
                                    }
                                    else {
                                        //exit campaign
                                        if($existCid != ""){
                                            $u = "http://pub.alimama.com/campaign/exitCampaign.json";
                                            $post['pubCampaignid'] = $existCid;
                                            $post['t'] = time();
                                            $cookie_data = excookie($cookie);
                                            $post['_tb_token_'] = $cookie_data['_tb_token_'];
                                            $post_str = "pubCampaignid=" . $post['pubCampaignid'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
                                            $ret = openhttp_header($u, $post_str, $cookie, '', '1');
                                        }
                                        if($eventRate != ''){
                                            if($rate<$eventRate){
                                                $rate = $eventRate;
                                                $qq = '1';
                                            }
                                        }


                                    }

                                }
                                else {
                                    $rate = $tkRate;
                                    if($eventRate != ''){
                                        if($rate<$eventRate){
                                            $rate = $eventRate;
                                            $qq = '1';
                                        }
                                    }
                                }



                                $quan_api = "http://taotehui.co/?g=Tbkqq&m=Page&a=find_quan&iid=$iid";
                                $activityId = file_get_contents($quan_api);

                                if($activityId == ""){


                                    $qtk_url = "http://www.qingtaoke.com/api/UserPlan/UserCouponList?sid=$sellerId&gid=$iid";

                                    $qtk_cookie = '0f4242763305a0552f39b61843503c26=00f0a62ca24de37b108a0eb6e6912654c0e682cda%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; __cfduid=d9298988d33cf5d025e50f0800d2423041458637207; pgv_pvi=2078657173; PHPSESSID=ihq951b8fbu2nadenrid8bol90; CNZZDATA1256913876=1220425662-1458636544-%7C1472479995; PHPSESSID=beqahligcpq4lpvr5vq7pgatb7; 525d2e01d0d4d2fb427b9926b3856822=9f8b7d357c8e2e558451a99314f15895e633cea9a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; qtk_sin=4527me5sgs6muvtbu97frl0f10; CNZZDATA1258823063=1707445734-1471852188-null%7C1484523769; qtkwww_=a855d0ebd8c88a93f8a96697521de895919b9ea7a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D';
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $qtk_url);
                                    curl_setopt($ch, CURLOPT_COOKIE,$qtk_cookie);
                                    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

                                    $str = curl_exec($ch);
                                    curl_close($ch);
                                    $arr = json_decode($str,true);
                                    if($arr['data']){

                                        $quan_str = "";
                                        $quan_arr = array();
                                        foreach($arr['data'] as $data){

                                            if($data['remain']>0) {
                                                if($price>$data['applyAmount']){
                                                    if($data['activity_id'] == "")$quan_arr[] = $data['activityId'];
                                                    else $quan_arr[] = $data['activity_id'];

                                                    $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?activityId=" . $data['activityId'] . "&sellerId=" . $sellerId;
                                                    $header = array();
                                                    $header[] = "Accept-Language: zh-CN,zh;q=0.8";
                                                    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
                                                    $ch = curl_init();
                                                    curl_setopt($ch, CURLOPT_URL, $quan_link);
                                                    //curl_setopt($ch, CURLOPT_REFERER, $tu);
                                                    curl_setopt($ch, CURLOPT_HEADER, true);
                                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                                                    //curl_setopt($ch, CURLOPT_NOBODY,1);
                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
                                                    curl_setopt($ch, CURLOPT_MAXREDIRS,2);
                                                    $out = curl_exec($ch);
                                                    curl_close($ch);
                                                    if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
                                                        $quan_str .=  $match[1] ."：" .$quan_link . "\n";
                                                }
                                            }
                                        }

                                    }
                                    else $quan_str = "";

                                }


                                if($activityId == ""){
                                    if($qq == '1')
                                        $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=3&channel=tk_qqhd&t=$t";
                                    else
                                        $u = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=$iid&adzoneid=$adid&siteid=$mediaid&scenes=1&t=$t";
                                    $str = openhttp_header($u,'',$cookie);
                                    if($str != ""){
                                        $arr = json_decode($str,true);
                                        if($arr['data']['couponLink'] != "") $link = $arr['data']['couponLink'];
                                        else {
                                            $link = $arr['data']['shortLinkUrl'];
                                            if($quan_str == "")$quan_str = " 没找到券，价格合适可以直接购买";
                                        }

                                        if($arr['data']['couponLinkTaoToken'] != "") $taotoken = $arr['data']['couponLinkTaoToken'];
                                        else $taotoken = $arr['data']['taoToken'];
                                        if($link == "") echo "找不到链接，可能商家做了调整，请联系人工客服确认";
                                        else{
                                            if($appname == "yhg" || $g == "1")$yongjin = "";
                                            else{
                                                if($proxy['fcrate'] != "") $yongjin = round($rate * $fcrate * $proxy['fcrate'],2);
                                                else $yongjin =  round($rate * $fcrate,2);
                                            }
                                            echo "【" . $title . "】" .$yongjin . "下单链接：".$link . " 淘口令：" . $taotoken . $quan_str;
                                        }
                                    }
                                    else echo "找不到t这个链接，可能机器人掉线了，请联系人工客服";
                                }
                                else {
                                    if($qq == '1')
                                        $dx = "0";
                                    else $dx = "1";
                                    $link = "https://uland.taobao.com/coupon/edetail?activityId=" .$activityId ."&pid=" . $pid ."&itemId=" . $iid ."&src=qhkj_dtkp&dx=" . $dx;
                                    if($appname == "yhg" || $g == "1")$yongjin = "";
                                    else {
                                        if($proxy['fcrate'] != "")$yongjin = round($rate * $fcrate * $proxy['fcrate'],2);
                                        else $yongjin =  round($rate * $fcrate,2);
                                    }
                                    $token_data = array();
                                    $token_data['logo'] = $img;
                                    $token_data['text'] = $title;
                                    $token_data['url'] = $link;
                                    $taotoken = get_taotoken($token_data);

                                    if($taotoken == '')$taotoken = get_taotoken($token_data);
                                    if($taotoken == '')$taotoken = get_taotoken($token_data);
                                    if($taotoken == '')$taotoken = get_taotoken($token_data);

                                    echo "@" . $proxywx . ": 【" . $title . "】" .$yongjin . "下单链接：".$link . $quan_str . "口令：" .$taotoken;
                                }
                            }
                        }

                    }

                }
            }
            else {
                echo "@" . $proxywx . ": 你没有登记代理编号，请@我，并输入代理帐号：" . $appname . "001  这种格式进行登记";
            }
        }

    }

    public function save_openid(){
        if(IS_POST){
            $msg = $_POST['msg'];
            $openid = $_POST['openid'];

            $data['openid'] = $openid;
            if(M("TbkqqProxy")->where(array("proxy"=>$msg))->save($data)>=0){
                echo "@" . $msg . ": 设置成功！";
            }
            else echo "@" . $msg . ": 设置错误，请联系老板！";
        }
    }

}
