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
class PageController extends HomebaseController{
	public function index() {
		$id=$_GET['id'];
		$content=sp_sql_page($id);
		
		if(empty($content)){
		    header('HTTP/1.1 404 Not Found');
		    header('Status:404 Not Found');
		    if(sp_template_file_exists(MODULE_NAME."/404")){
		        $this->display(":404");
		    }
		     
		    return ;
		}
		
		$this->assign($content);
		$smeta=json_decode($content['smeta'],true);
		$tplname=isset($smeta['template'])?$smeta['template']:"";
		
		$tplname=sp_get_apphome_tpl($tplname, "page");
		
		$this->display(":$tplname");
	}

	public function item_json(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$group = $_GET['group'];
		$no = $_GET['no'];
		$qq = $_GET['qq'];
		$proxy = M("TbkqqProxy")->where(array("qqgroup"=>$group))->find();
		if($proxy){
			//$proxyid = substr($proxy['proxy'], -3, 3);
			$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
			$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
		$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxyid))->find();
		}
		$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
		preg_match($preg, $item['imgmemo'], $imgArr);
		$json_arr['img'] = $imgArr[1];
		$json_arr['memo'] =  "下单链接：" . $itemurl['shorturl'] . "\n" . $item['memo'];//str_replace("\n","<br>",$item['memo']);
		$json = '{"img":"' . $json_arr['img'] . '","memo":"' . $json_arr['memo'] . '"}';
		echo $json;
	}
/*
	public function item_img(){
		$no = $_GET['no'];
		$item = M("TbkqqTaokeItem")->where(array("no"=>$no,"status"=>"1"))->find();
		$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
		preg_match($preg, $item['imgmemo'], $imgArr);
		$imgurl = $imgArr[1];
		header("location:".$imgurl);
	}
*/

	public function item_img(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$no = $_GET['no'];
		$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
		if($item['imgmemo'] == '')		$imgurl = $item['img']  . "_290x290.jpg";
		else {
			$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
			preg_match($preg, $item['imgmemo'], $imgArr);
			$imgurl = $imgArr[1];
		}
		header("location:".$imgurl);
	}
	public function item_aliimg(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$no = $_GET['no'];
		$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
		if($item['imgmemo'] == '')		$imgurl = $item['img']  . "_290x290.jpg";
		else {
			$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
			preg_match($preg, $item['imgmemo'], $imgArr);
			$imgurl = $imgArr[1];
		}
		header("location:".$imgurl);
	}

	public function item_memo(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?id=";
		$no = $_GET['no'];
		$group = $_GET['group'];

		$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
		$shorturl = "";
		$qq = $_GET['qq'];
		if($group != '' && $qq != ''){
			$proxy = M("TbkqqProxy")->where(array("qqgroup"=>$group,"sendqq"=>$qq))->find();

			if($proxy){
				//$proxyid = substr($proxy['proxy'], -3, 3);
				$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
				$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
				$shorturl = $itemurl['shorturl'];
				//$shorturl = $itemurl['id'];
			}
			else {
				$proxy = M("TbkqqProxyQqgrp")->where(array("qqgroup"=>$group,"sendqq"=>$qq))->find();
				if($proxy){
					$proxyid = "taotehui001";
					$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxyid))->find();
					$shorturl = $itemurl['shorturl'];
					//$shorturl = $itemurl['id'];
				}
				else exit();
			}
		}

		if($shorturl == "")
			echo iconv("utf-8","gbk",$item['memo']);
		else
			echo iconv("utf-8","gbk","下单链接：" . $shorturl. "\n"  .$item['memo']);

	}

	public function item_url(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$no = $_GET['no'];
		$qq = $_GET['qq'];
		$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";
		$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
		$proxys = M("TbkqqProxy")->where(array("sendqq"=>$qq,"qqstatus"=>"1"))->select();
		$json_url = "";
		if($proxys) {
			foreach ($proxys as $proxy) {
				//$proxyid = substr($proxy['proxy'], -3, 3);
				$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
				$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
				$json_url .= $proxy['qqgroup'] . "|" . $itemurl['shorturl'] .  ",";
			}
		}
		else {
			$proxys = M("TbkqqProxyQqgrp")->where(array("sendqq"=>$qq,"qqstatus"=>1))->select();
			if($proxys) {
				$proxyid = "taotehui001";
				$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxyid))->find();
				foreach ($proxys as $proxy) {
					$json_url .= $proxy['qqgroup'] . "|" . $itemurl['shorturl']  .  ",";
				}
			}
		}
		echo $json_url;
	}

	public function item_wx_url(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$no = $_GET['no'];
		$wx = $_GET['wx'];
		$group = $_GET['group'];
		$fromwx = $_GET['fromwx'];
		$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";
		$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
		if($group != '' && $fromwx != '')
			$proxys = M("TbkqqProxy")->where(array("sendwx"=>$wx,"wxstatus"=>"1","wxgroup"=>$group,"proxywx"=>$fromwx))->select();
		else $proxys = M("TbkqqProxy")->where(array("sendwx"=>$wx,"wxstatus"=>"1"))->select();
		$json_url = "";
		if($proxys) {
			foreach ($proxys as $proxy) {
				//$proxyid = substr($proxy['proxy'], -3, 3);
				$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
				$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
				$json_url .= $proxy['wxgroup'] . "|下单链接：" . $baseurl.$itemurl['id']   . ",";
				//$json_url .= $proxy['wxgroup'] . "|下单链接：" . $itemurl['shorturl']  . ",";
			}
		}

		echo $json_url;
	}


	public function item_qq_new(){
		$qq = $_GET['qq'];
		$proxys = M("TbkqqProxy")->where(array("sendqq"=>$qq,"qqstatus"=>"1"))->select();
		$json_url = "";
		//$domain = C("BASE_DOMAIN");
		if($proxys) {
			foreach ($proxys as $proxy) {
				//$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
                $site = get_siteurl_by_login($proxy['proxy']);
				$json_url .= $site['url'] . "|" . $proxy['qqgroup'] . ",";
			}
		}
		else {
			$proxys = M("TbkqqProxyQqgrp")->where(array("sendqq" => $qq, "qqstatus" => 1))->select();
			if ($proxys) {
				foreach ($proxys as $proxy) {
					$json_url .= "www.taotehui.co" . "|" . $proxy['qqgroup'] . ",";
				}
			}
		}
		echo $json_url;
	}

	public function item_qq(){
		$qq = $_GET['qq'];
		$proxys = M("TbkqqProxy")->where(array("sendqq"=>$qq,"qqstatus"=>"1"))->select();
		$json_url = "";
		if($proxys) {
			foreach ($proxys as $proxy) {
				$json_url .= $proxy['qqgroup'] . ",";
			}
		}
		else {
			$proxys = M("TbkqqProxyQqgrp")->where(array("sendqq" => $qq, "qqstatus" => 1))->select();
			if ($proxys) {
				foreach ($proxys as $proxy) {
					$json_url .= $proxy['qqgroup'] . ",";
				}
			}
		}
		echo $json_url;
	}

	public function item_group_json() {
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$no = $_GET['no'];
		$qq = $_GET['qq'];
		$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();


		if($item){
			$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
			preg_match($preg, $item['imgmemo'], $imgArr);
			$json_arr['img'] = $imgArr[1];
			$json_arr['memo'] = $item['memo'];
			$json = '{"img":"' . $json_arr['img'] . '","memo":"' . $json_arr['memo'] . '","url":{';
			$proxys = M("TbkqqProxy")->where(array("sendqq"=>$qq))->select();
			if($proxys){
				$i = 0;
				foreach($proxys as $proxy){
					if($proxy['qqgroup'] != ""){
						//$proxyid = substr($proxy['proxy'], -3, 3);
						//$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
						$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
//						$json_arr['url'][$proxy['qqgroup']] = $itemurl['shorturl'];
						$json_url = '"' . $proxy['qqgroup'] . '":"' . $itemurl['shorturl'] . '"';
						if($i == 0) $json .= $json_url;
						else $json .= ',' . $json_url;
						$i++;
					}

				}
				$json .=   '}}';
//				echo $json_abc = json_encode($json_arr);
				echo $json;
//$arr = json_decode($json_abc,true);
//				print_r($arr);
			}
			else exit();
		}
		else exit();
	}



	public function item_wx_memo(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?id=";
		$no = $_GET['no'];
		$group = $_GET['group'];
		$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
		$shorturl = "";
		if($group != ""){
			$proxy = M("TbkqqProxy")->where(array("wxgroup"=>$group))->find();
			if($proxy){
				//$proxyid = substr($proxy['proxy'],-3,3);
				$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
				$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
				$shorturl = $itemurl['id'];
				$taokl = $itemurl['taokl'];
			}
			else {
				exit();
			}
		}
		if($shorturl == "")
			echo $item['memo'];
		else
			echo "下单链接：" . $baseurl.$shorturl ."\n" .$item['memo'];

	}

	public function item_wxnew_memo(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?id=";
		$no = $_GET['no'];
		$group = $_GET['group'];
		$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
		$shorturl = "";

		if($group != ""){
			$proxy = M("TbkqqProxy")->where(array("wxgroup"=>$group))->find();
			if($proxy){
				//$proxyid = substr($proxy['proxy'],-3,3);
				$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
				$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
				$shorturl = $itemurl['id'];
				$taokl = $itemurl['taokl'];
			}
			else {
				exit();
			}
		}

		$item['memo'] = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches) {
			return convert_dwz($matches[0]);
		},$item['memo']);

		if($shorturl == "")
			echo $item['memo'];
		else
			echo "下单链接：" . $baseurl.$shorturl . "\n" .$item['memo'];

	}


	public function get_orderid(){
		$group = $_GET['group'];
		$qq = $_GET['qq'];
//		if($group != '' && $qq != ''){
		//	$proxy = M("TbkqqProxy")->where(array("qqgroup"=>$group,"sendqq"=>$qq))->find();
		//	if($proxy){
				$orderid = $_GET['oid'];

				$startdate=date("Y-m-d",time()-86400);
				$options_model = M("Options");

				$enddate = date("Y-m-d");
				$item = M("TbkqqTaokeDetails")->where(array("orderid"=>$orderid))->find();
				if($item){
					echo  iconv("utf-8","gbk","订单存在a");
					exit();
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
									echo  iconv("utf-8","gbk","订单存在t");
									exit();
								}
								sleep(1);
							}
						}
						sleep(1);
				}

				echo  iconv("utf-8","gbk","订单不存在，请确认机器人是否掉线（详见公告），如果机器人掉线，请联系公司客服处理，如未掉线，请立即撤单重拍！");
		//	}
		//}

	}

	public function test_cookie_v1(){
        $username = $_GET['u'];
        $num = $_GET['num'];
        $cookie_model = M("TbkqqCookies");
        $startdate=date("Y-m-d",time()-86400);

        $enddate = date("Y-m-d");

        $u = "http://pub.alimama.com/report/getTbkPaymentDetails.json?startTime=$startdate&endTime=$enddate&payStatus=&queryType=1&toPage=1&perPageSize=2&total=&_input_charset=utf-8";
        $cookies=$cookie_model->where(array("username"=>$username,"num"=>$num))->find();
        if($cookies){
            $cookie = $cookies['cookie'];
            $str = openhttp_header($u, '', $cookie);

            if($str == "")
                echo "error";
            else echo "ok";
        }
        else echo "error";
    }

	public function test_cookie(){
		$username = $_GET['u'];

		$startdate=date("Y-m-d",time()-86400);
		$options_model = M("Options");

		$enddate = date("Y-m-d");
			$u = "http://pub.alimama.com/report/getTbkPaymentDetails.json?startTime=$startdate&endTime=$enddate&payStatus=&queryType=1&toPage=1&perPageSize=2&total=&_input_charset=utf-8";
			$option=$options_model->where("option_name='cookie_options'")->find();
			if($option) {
				$options = (array)json_decode($option['option_value'], true);
				if($username == ""){
					foreach ($options as $data) {
						$cookie = $data['cookie'];
						$str = openhttp_header($u, '', $cookie);
						echo $data['username'];
						if($str == "")
							echo "error!\n";
						else echo "ok!\n";
						sleep(1);
					}
				}
				else {
					foreach ($options as $data) {
						if($username == $data['username']){
							$cookie = $data['cookie'];
							$str = openhttp_header($u, '', $cookie);

							if($str == "")
								echo "error";
							else echo "ok";
							break;
						}

					}
				}
			}
	}

	public function get_errcoo_json(){
		$startdate=date("Y-m-d",time()-86400);
		$options_model = M("Options");

		$enddate = date("Y-m-d");
		$u = "http://pub.alimama.com/report/getTbkPaymentDetails.json?startTime=$startdate&endTime=$enddate&payStatus=&queryType=1&toPage=1&perPageSize=2&total=&_input_charset=utf-8";
		$option=$options_model->where("option_name='cookie_options'")->find();
		if($option) {
			$options = (array)json_decode($option['option_value'], true);

			foreach ($options as $data) {
				$cookie = $data['cookie'];
				$str = openhttp_header($u, '', $cookie);
				echo $data['username'];
				if($str == "")
					$coo_arr[] = array('username'=>$data['username']);
				sleep(1);
			}

		}
		echo $json = json_encode($coo_arr);
	}

	public function test_cookie_copy(){
		$startdate=date("Y-m-d",time()-86400);
		$options_model = M("OptionsCopy");

		$enddate = date("Y-m-d");
		$u = "http://pub.alimama.com/report/getTbkPaymentDetails.json?startTime=$startdate&endTime=$enddate&payStatus=&queryType=1&toPage=1&perPageSize=2&total=&_input_charset=utf-8";
		$option=$options_model->where("option_name='cookie_options'")->find();
		if($option) {
			$options = (array)json_decode($option['option_value'], true);
			foreach ($options as $data) {
				$cookie = $data['cookie'];
				$str = openhttp_header($u, '', $cookie);
				echo $data['username'];
				if($str == "")
					echo "error!\n";
				else echo "ok!\n";
				sleep(1);
			}
		}
	}

	public function qqgrp_json(){
		$qq = $_GET['qq'];
		$proxys = M("TbkqqProxy")->where(array("sendqq"=>$qq,"qqstatus"=>"1"))->select();
		$json = "";
		$json_arr = array();
		$domain = C("BASE_DOMAIN");
		if($proxys) {
			foreach ($proxys as $proxy) {
				$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
				$site = get_siteurl_by_login($proxy['proxy']);
				$json_arr[] = array("qqgrp"=>$proxy['qqgroup'],"site"=>$site['url']);
			}
		}
		else {
			$proxys = M("TbkqqProxyQqgrp")->where(array("sendqq" => $qq, "qqstatus" => 1))->select();
			if ($proxys) {
				foreach ($proxys as $proxy) {

					$json_arr[] = array("qqgrp"=>$proxy['qqgroup'],"site"=>"www.taotehui.co");
				}
			}
		}
		echo $json = json_encode($json_arr);
	}

	public function group_json(){
		$send = $_GET['send'];
		$t = $_GET['t'];
		if($send != ''){
			$domain = C("BASE_DOMAIN");
			if($t == 'wx'){
				$proxys = M("TbkqqProxy")->where(array("sendwx"=>$send,"wxstatus"=>'1'))->select();
				if($proxys) {
					foreach ($proxys as $proxy) {
						//$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
                        $site = get_siteurl_by_login($proxy['proxy']);
                        $proxy_arr[] = array("group"=>$proxy['wxgroup'],"site"=>$site['url']);
					}
				}
			}
			else {
				$proxys = M("TbkqqProxy")->where(array("sendqq"=>$send,"qqstatus"=>"1"))->select();

				if($proxys) {
					foreach ($proxys as $proxy) {
						//$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
                        $site = get_siteurl_by_login($proxy['proxy']);
                        $proxy_arr[] = array("group"=>$proxy['qqgroup'],"site"=>$site['url']);
					}
				}
				else {
					$proxys = M("TbkqqProxyQqgrp")->where(array("sendqq" => $send, "qqstatus" => 1))->select();
					if ($proxys) {
						foreach ($proxys as $proxy) {
							$proxy_arr[] = array("qqgrp"=>$proxy['qqgroup'],"site"=>"www.taotehui.co");
						}
					}
				}
			}
		}
		if($proxy_arr) echo $json = json_encode($proxy_arr);
	}

	public function range_json(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		if($_GET['start'] == '' || $_GET['end'] == '' || $_GET['send'] == '') exit();
		else {

			//$mm1 = "\n复制本条信息，打开手机淘宝自动会弹出抢购链接:";
			$mm1 = "  \n-------------------
长按复制这条消息，打开→手机淘宝→即可领卷下单";
				$mm2 = "";


				$str_xdlj = "下单链接:";

			$start = intval($_GET['start']);
			$end = intval($_GET['end']);
			$group = $_GET['group'];
			$from = $_GET['from'];
			$send = $_GET['send'];
			$t = $_GET['t'];
			$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";
			for($no=$start;$no<=$end;$no++){
				$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
				if($item){
					if($item['imgmemo'] == '')		$imgurl = $item['img']  . "_290x290.jpg";
					else {
						$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
						preg_match($preg, $item['imgmemo'], $imgArr);
						$imgurl = $imgArr[1];
					}
					if($group != '') {
						if($t == 'wx'){
							$proxy = M("TbkqqProxy")->where(array("wxgroup"=>$group,"sendwx"=>$send,"wxstatus"=>'1'))->find();
							if($proxy){

								//$proxyid = substr($proxy['proxy'], -3, 3);
								$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
								if($proxyid == '001') $www = 'www';
								else $www = $proxyid;
								$site = get_siteurl_by_login($proxy['proxy']);
								$www = $site['url'];
                                $baseurl = "http://dwz." . $site['base_url'] . "/?id=";
								$mm3 = "\n-------------------
省钱网站：http://" . $www;
								$mm4 = "";
								$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
								$urlid = $itemurl['id'];
								$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
								$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
										return convert_dwz($matches[0]) . "&uid=" . $urlid;
									},$item['memo']) . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
								$item_arr[]  = array('no'=>$no,'group'=>$proxy['wxgroup'],'memo'=>$str_xdlj .$baseurl.$urlid . "\n" .$memo ,'img'=>$imgurl);
							}
						}
						else {
							$proxy = M("TbkqqProxy")->where(array("qqgroup"=>$group,"sendqq"=>$send,"qqstatus"=>'1'))->find();
							if($proxy){

								//$proxyid = substr($proxy['proxy'], -3, 3);
								$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
								if($proxyid == '001') $www = 'www';
								else $www = $proxyid;

                                $site = get_siteurl_by_login($proxy['proxy']);
                                $www = $site['url'];

								$mm3 = "\n-------------------
省钱网站：http://" . $www;
								$mm4 = "推荐编码：" . create_password(6);
								$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
								$shorturl = $itemurl['shorturl'];

								$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
								$memo = $item['memo'] . $mm1 . $kouling . $mm2 . $mm3 . $mm4;

								$item_arr[]  = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo'=>$str_xdlj .$shorturl . "\n" .$memo ,'img'=>$imgurl);

							}
							else {
								$proxy = M("TbkqqProxyQqgrp")->where(array("qqgroup" => $group, "sendqq" => $send,"qqstatus"=>'1'))->find();

								if ($proxy) {

									$proxyid = "taotehui001";
									if($proxyid == '001') $www = 'www';
									else $www = $proxyid;
									$www = "www.taotehui.co";
									$mm3 = "\n-------------------
省钱网站：http://" . $www;
									$mm4 = "推荐编码：" . create_password(6);
									$itemurl = M("TbkqqTaokeItemurls")->where(array("iid" => $item['iid'], "proxy" => $proxyid))->find();
									$shorturl = $itemurl['shorturl'];
									$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
									$memo = $item['memo'] . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
									$item_arr[] = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo' => $str_xdlj . $shorturl . "\n" . $memo, 'img' => $imgurl);


								}
							}
						}
					}
					else {
						if($t == 'wx'){
							$proxys = M("TbkqqProxy")->where(array("sendwx"=>$send,"wxstatus"=>'1'))->select();
							if($proxys){
								foreach($proxys as $proxy){
									//$proxyid = substr($proxy['proxy'], -3, 3);
									$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
									if($proxyid == '001') $www = 'www';
									else $www = $proxyid;
                                    $site = get_siteurl_by_login($proxy['proxy']);
                                    $www = $site['url'];
                                    $baseurl = "http://dwz." . $site['base_url'] . "/?id=";
									$mm3 = "\n-------------------
省钱网站：http://" . $www;
									$mm4 = "";
									$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
									$urlid = $itemurl['id'];
									$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
									$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
											return convert_dwz($matches[0]) . "&uid=" . $urlid;
										},$item['memo']) . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
									$item_arr[]  = array('no'=>$no,'group'=>$proxy['wxgroup'],'memo'=>"下单链接:" .$baseurl.$urlid . "\n" .$memo,'img'=>$imgurl);
								}

							}
						}
						else {
							$proxys = M("TbkqqProxy")->where(array("sendqq"=>$send,"qqstatus"=>'1'))->select();
							if($proxys){
								foreach($proxys as $proxy){
									//$proxyid = substr($proxy['proxy'], -3, 3);
									$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
									if($proxyid == '001') $www = 'www';
									else $www = $proxyid;
                                    $site = get_siteurl_by_login($proxy['proxy']);
                                    $www = $site['url'];
									$mm3 = "\n-------------------
省钱网站：http://" . $www;
									$mm4 = "推荐编码：" . create_password(6);
									$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
									$shorturl = $itemurl['shorturl'];
									$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
									$memo = $item['memo'] .$mm1 . $kouling . $mm2 . $mm3 . $mm4;
									$item_arr[]  = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo'=>$str_xdlj .$shorturl . "\n" .$memo,'img'=>$imgurl);
								}

							}
							else {
								$proxys = M("TbkqqProxyQqgrp")->where(array("sendqq" => $send,"qqstatus"=>'1'))->select();

								if ($proxys) {
									foreach($proxys as $proxy){
										$proxyid = "taotehui001";
										if($proxyid == '001') $www = 'www';
										else $www = $proxyid;
										$www = "www.taotehui.co";
										$mm3 = "\n-------------------
省钱网站：http://" . $www;
										$mm4 = "推荐编码：" . create_password(6);
										$itemurl = M("TbkqqTaokeItemurls")->where(array("iid" => $item['iid'], "proxy" => $proxyid))->find();
										$shorturl = $itemurl['shorturl'];
										$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
										$memo = $item['memo'] . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
										$item_arr[] = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo' => $str_xdlj . $shorturl . "\n" . $memo , 'img' => $imgurl);
									}
								}
							}
						}
					}
				}

 			}
		}

		if($item_arr) echo $json = json_encode($item_arr);
	}

	public function bufa_json(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$nos = $_GET['nos'];
		$groups = $_GET['groups'];
		$from = $_GET['from'];
		$send = $_GET['send'];
		$t = $_GET['t'];
		$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";
		if($nos != '' && $send != ''){
			$noa = explode(",",$nos);
			foreach($noa as $no){
				if($no != ''){
					$item = $item_model->where(array("no"=>$no,"status"=>"1"))->find();
					if($item){
						if($item['imgmemo'] == '')		$imgurl = $item['img']  . "_290x290.jpg";
						else {
							$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
							preg_match($preg, $item['imgmemo'], $imgArr);
							$imgurl = $imgArr[1];
						}
						if($groups != ''){
							$groupa = explode(",",$groups);
							foreach($groupa as $group){
								if($group != ''){
									if($t == 'wx'){
										$proxy = M("TbkqqProxy")->where(array("wxgroup"=>$group,"sendwx"=>$send,"wxstatus"=>'1'))->find();
										if($proxy){
											//$proxyid = substr($proxy['proxy'], -3, 3);
											$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
											$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
											$urlid = $itemurl['id'];
											$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
											$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
													return convert_dwz($matches[0]) . "&uid=" . $urlid;
												},$item['memo']) . "\n复制本条信息，打开手机淘宝自动会弹出抢购链接:" . $kouling . "  \n------------------------------
温馨提示：打开领卷失败,异常或者错误,请返回重新复制本信息自带优惠券链接领取";
											$item_arr[]  = array('no'=>$no,'group'=>$proxy['wxgroup'],'memo'=>"下单链接:" .$baseurl.$urlid . "\n" .$memo ,'img'=>$imgurl);
										}
									}
									else {
										$proxy = M("TbkqqProxy")->where(array("qqgroup" => $group, "sendqq" => $send, "qqstatus" => '1'))->find();
										if ($proxy) {
											//$proxyid = substr($proxy['proxy'], -3, 3);
											$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
											$itemurl = M("TbkqqTaokeItemurls")->where(array("iid" => $item['iid'], "proxy"=>$proxy['proxy']))->find();
											$shorturl = $itemurl['shorturl'];
											$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
											$memo = $item['memo'] . "\n复制本条信息，打开手机淘宝自动会弹出抢购链接:" . $kouling . " \n------------------------------
温馨提示：打开领卷失败,异常或者错误,请返回重新复制本信息自带优惠券链接领取";
											$item_arr[] = array('no' => $no, 'group' => $proxy['qqgroup'], 'memo' => "下单链接:" . $shorturl . "\n" . $memo , 'img' => $imgurl);

										} else {
											$proxy = M("TbkqqProxyQqgrp")->where(array("qqgroup" => $group, "sendqq" => $send, "qqstatus" => '1'))->find();
											if ($proxy) {
												$proxyid = "taotehui001";
												$itemurl = M("TbkqqTaokeItemurls")->where(array("iid" => $item['iid'], "proxy"=>$proxyid))->find();
												$shorturl = $itemurl['shorturl'];
												$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
												$memo = $item['memo'] . "\n复制本条信息，打开手机淘宝自动会弹出抢购链接:" . $kouling . " \n------------------------------
温馨提示：打开领卷失败,异常或者错误,请返回重新复制本信息自带优惠券链接领取";
												$item_arr[] = array('no' => $no, 'group' => $proxy['qqgroup'], 'memo' => "下单链接:" . $shorturl . "\n" . $memo , 'img' => $imgurl);
											}
										}
									}
								}
							}
						}
						else {
							if($t == 'wx'){
								$proxys = M("TbkqqProxy")->where(array("sendwx"=>$send,"wxstatus"=>'1'))->select();
								if($proxys){
									foreach($proxys as $proxy){
										//$proxyid = substr($proxy['proxy'], -3, 3);
										$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
										$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
										$urlid = $itemurl['id'];
										$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
										$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
												return convert_dwz($matches[0]) . "&uid=" . $urlid;
											},$item['memo']) . "\n复制本条信息，打开手机淘宝自动会弹出抢购链接:" . $kouling . " \n------------------------------
温馨提示：打开领卷失败,异常或者错误,请返回重新复制本信息自带优惠券链接领取";
										$item_arr[]  = array('no'=>$no,'group'=>$proxy['wxgroup'],'memo'=>"下单链接:" .$baseurl.$urlid . "\n" .$memo ,'img'=>$imgurl);
									}

								}
							}
							else {
								$proxys = M("TbkqqProxy")->where(array("sendqq"=>$send,"qqstatus"=>'1'))->select();
								if($proxys){
									foreach($proxys as $proxy){
										//$proxyid = substr($proxy['proxy'], -3, 3);
										$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
										$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
										$shorturl = $itemurl['shorturl'];
										$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
										$memo = $item['memo'] . "\n复制本条信息，打开手机淘宝自动会弹出抢购链接:" . $kouling . " \n------------------------------
温馨提示：打开领卷失败,异常或者错误,请返回重新复制本信息自带优惠券链接领取";
										$item_arr[]  = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo'=>"下单链接:" .$shorturl . "\n" .$memo ,'img'=>$imgurl);
									}

								}
								else {
									$proxys = M("TbkqqProxyQqgrp")->where(array("sendqq" => $send,"qqstatus"=>'1'))->select();

									if ($proxys) {
										foreach($proxys as $proxy){
											$proxyid = "taotehui001";
											$itemurl = M("TbkqqTaokeItemurls")->where(array("iid" => $item['iid'], "proxy"=>$proxyid))->find();
											$shorturl = $itemurl['shorturl'];
											$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
											$memo = $item['memo'] . "\n复制本条信息，打开手机淘宝自动会弹出抢购链接:" . $kouling . " \n------------------------------
温馨提示：打开领卷失败,异常或者错误,请返回重新复制本信息自带优惠券链接领取";
											$item_arr[] = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo' => "下单链接:" . $shorturl . "\n" . $memo, 'img' => $imgurl);
										}
									}
								}
							}
						}
					}

				}
			}
		}
		if($item_arr) echo $json = json_encode($item_arr);
	}

	public function single_json(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$no = $_GET['no'];
		$group = $_GET['group'];
		$from = $_GET['from'];
		$send = $_GET['send'];
		$t = $_GET['t'];
		$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";
		//$mm1 = "\n复制本条信息，打开手机淘宝自动会弹出抢购链接:";
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
				if($t == 'wx'){
					$proxy = M("TbkqqProxy")->where(array("wxgroup"=>$group,"sendwx"=>$send,"wxstatus"=>'1'))->find();
					if($proxy){
						//$proxyid = substr($proxy['proxy'], -3, 3);
						$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
						if($proxyid == '001') $www = 'www';
						else $www = $proxyid;
                        $site = get_siteurl_by_login($proxy['proxy']);
                        $www = $site['url'];
                        $baseurl = "http://dwz." . $site['base_url'] . "/?id=";
						$mm3 = "\n-------------------
省钱网站：http://" . $www;
						$mm4 = "";
						$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
						$urlid = $itemurl['id'];
						$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
						$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
								return convert_dwz($matches[0]) . "&uid=" . $urlid;
							},$item['memo']) . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
						$item_arr[]  = array('no'=>$no,'group'=>$proxy['wxgroup'],'memo'=>"下单链接:" .$baseurl.$urlid . "\n" .$memo ,'img'=>$imgurl);
					}
				}
				else {
					$proxy = M("TbkqqProxy")->where(array("qqgroup"=>$group,"sendqq"=>$send, "qqstatus" => '1'))->find();
					if($proxy){
						//$proxyid = substr($proxy['proxy'], -3, 3);
						$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
						if($proxyid == '001') $www = 'www';
						else $www = $proxyid;
                        $site = get_siteurl_by_login($proxy['proxy']);
                        $www = $site['url'];
						$mm3 = "\n-------------------
省钱网站：http://" . $www;
						$mm4 = "推荐编码：" . create_password(6);
						$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
						$shorturl = $itemurl['shorturl'];
						$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
						$memo = $item['memo'] . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
						$item_arr[]  = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo'=>"下单链接:" .$shorturl . "\n" .$memo ,'img'=>$imgurl);
					}
					else {
						$proxy = M("TbkqqProxyQqgrp")->where(array("qqgroup" => $group, "sendqq" => $send, "qqstatus" => '1'))->select();

						if ($proxy) {
							$proxyid = "taotehui001";
							if($proxyid == '001') $www = 'www';
							else $www = $proxyid;
							$www = "www.taotehui.co";
							$mm3 = "\n-------------------
省钱网站：http://" . $www;
							$mm4 = "推荐编码：" . create_password(6);
							$itemurl = M("TbkqqTaokeItemurls")->where(array("iid" => $item['iid'], "proxy"=>$proxyid))->find();
							$shorturl = $itemurl['shorturl'];
							$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
							$memo = $item['memo'] . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
							$item_arr[]  = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo'=>"下单链接:" .$shorturl . "\n" .$memo ,'img'=>$imgurl);
						}
					}
				}
			}
			else {
				if($t == 'wx'){
					$proxys = M("TbkqqProxy")->where(array("sendwx"=>$send,"wxstatus"=>'1'))->select();
					if($proxys){
						foreach($proxys as $proxy){
							//$proxyid = substr($proxy['proxy'], -3, 3);
							$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
							if($proxyid == '001') $www = 'www';
							else $www = $proxyid;
                            $site = get_siteurl_by_login($proxy['proxy']);
                            $www = $site['url'];
							$mm3 = "\n-------------------
省钱网站：http://" . $www;
							$mm4 = "";
							$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
							$urlid = $itemurl['id'];
							$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
							$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
									return convert_dwz($matches[0]) . "&uid=" . $urlid;
								},$item['memo']) . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
							$item_arr[]  = array('no'=>$no,'group'=>$proxy['wxgroup'],'memo'=>"下单链接:" .$baseurl.$urlid . "\n" .$memo ,'img'=>$imgurl);
						}

					}
				}
				else {
					$proxys = M("TbkqqProxy")->where(array("sendqq"=>$send,"qqstatus"=>'1'))->select();
					if($proxys){
						foreach($proxys as $proxy){
							//$proxyid = substr($proxy['proxy'], -3, 3);
							$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
							if($proxyid == '001') $www = 'www';
							else $www = $proxyid;
                            $site = get_siteurl_by_login($proxy['proxy']);
                            $www = $site['url'];
							$mm3 = "\n-------------------
省钱网站：http://" . $www;
							$mm4 = "推荐编码：" . create_password(6);
							$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
							$shorturl = $itemurl['shorturl'];
							$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
							$memo = $item['memo'] . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
							$item_arr[]  = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo'=>"下单链接:" .$shorturl . "\n" .$memo ,'img'=>$imgurl);
						}

					}
					else {
						$proxys = M("TbkqqProxyQqgrp")->where(array("sendqq" => $send,"qqstatus"=>'1'))->select();

						if ($proxys) {
							foreach($proxys as $proxy){
								$proxyid = "taotehui001";
								if($proxyid == '001') $www = 'www';
								else $www = $proxyid;
								$www = "www.taotehui.co";
								$mm3 = "\n-------------------
省钱网站：http://" . $www;
								$mm4 = "推荐编码：" . create_password(6);
								$itemurl = M("TbkqqTaokeItemurls")->where(array("iid" => $item['iid'], "proxy"=>$proxyid))->find();
								$shorturl = $itemurl['shorturl'];
								$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
								$memo = $item['memo'] . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
								$item_arr[] = array('no'=>$no,'group'=>$proxy['qqgroup'],'memo' => "下单链接:" . $shorturl . "\n" . $memo, 'img' => $imgurl);
							}
						}
					}
				}
			}
		}

		if($item_arr) echo $json = json_encode($item_arr);
	}

	public function schedule_json(){
		if(IS_POST){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
			$no = $_POST['no'];
			$send = $_POST['send'];
			$group = $_POST['group'];
			$data['wxno'] = $no +1;
			if(M("TbkqqProxy")->where(array("sendwx"=>$send,"wxgroup"=>$group))->save($data)){
				$item_arr = array("status"=>"ok");
			}

		}
		else {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
			$send = $_GET['send'];
			$start = $_GET['start'];
			$end = $_GET['end'];
			//$mm1 = "\n复制本条信息，打开手机淘宝自动会弹出抢购链接:";
			$mm1 = "  \n-------------------
长按复制这条消息，打开→手机淘宝→即可领卷下单";
			$mm2 = "";
			$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";
			$proxy = M("TbkqqProxy")->where(array("sendwx"=>$send,"wxstatus"=>'1',"wxno"=>array('between',"$start,$end")))->order('wxno')->find();
			if($proxy){
				$item = $item_model->where(array("no"=>$proxy['wxno'],"status"=>"1"))->find();
				if($item){
					if($item['imgmemo'] == '')		$imgurl = $item['img']  . "_290x290.jpg";
					else {
						$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
						preg_match($preg, $item['imgmemo'], $imgArr);
						$imgurl = $imgArr[1];
					}
					//$proxyid = substr($proxy['proxy'], -3, 3);
					$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
					if($proxyid == '001') $www = 'www';
					else $www = $proxyid;
                    $site = get_siteurl_by_login($proxy['proxy']);
                    $www = $site['url'];
                    $baseurl = "http://dwz." . $site['base_url'] . "/?id=";
					$mm3 = "\n-------------------
省钱网站：http://" . $www;
					$mm4 = "";
					$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid'],"proxy"=>$proxy['proxy']))->find();
					if($itemurl){
						$urlid = $itemurl['id'];
						$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
						$memo = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($urlid) {
								return convert_dwz($matches[0]) . "&uid=" . $urlid;
							},$item['memo']) . $mm1 . $kouling . $mm2 . $mm3 . $mm4;
						$item_arr  = array('no'=>$proxy['wxno'],'group'=>$proxy['wxgroup'],'memo'=>"下单链接:" .$baseurl.$urlid . "\n" .$memo ,'img'=>$imgurl);
					}

				}
			}
		}

		if($item_arr) echo $json = json_encode($item_arr);
	}

	public function proxy_reset_json(){
		$cmd = $_POST['cmd'];
		$no = $_POST['no'];
		if($no == "") $no = 1;
		$data['wxno'] = $no;
		if($cmd == 'restart'){
			if(M("TbkqqProxy")->where(array("sendwx"=>$_POST['send']))->save($data)>0){
				echo "ok";
			}
		}
	}

	public function order_json(){
		$group = $_GET['group'];
		$send = $_GET['send'];
		$t = $_GET['t'];
		if($group != '' && $send != ''){
			if($t == 'wx'){
				$proxy = M("TbkqqProxy")->where(array("wxgroup"=>$group,"sendwx"=>$send))->find();
			}
			else {
				$proxy = M("TbkqqProxy")->where(array("qqgroup"=>$group,"sendqq"=>$send))->find();
			}

			if($proxy){
		$orderid = $_GET['oid'];

		$startdate=date("Y-m-d",time()-86400);
		$options_model = M("Options");

		$enddate = date("Y-m-d");
		$item = M("TbkqqTaokeDetails")->where(array("orderid"=>$orderid))->find();
		if($item){
			$item_arr = array('status'=>$item['status']);
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
						$item_arr = array("status"=>"订单存在");
						exit();
					}
					sleep(1);
				}
			}
			sleep(1);
		}
				$item_arr = array("status"=>"订单不存在，请确认机器人是否掉线（详见公告），如果机器人掉线，请联系公司客服处理，如未掉线，请立即撤单重拍！");
			}
		}
		if($item_arr) echo $json = json_encode($item_arr);
	}

	public function get_cookie_json(){
		$options_model = M("OptionsCopy");
		$option=$options_model->where("option_name='cookie_options'")->find();
		if($option){
			echo $option['option_value'];
		}
	}

	public function save_cookie(){
		$username = $_POST['u'];
		$cookie = $_POST['c'];
		$num = $_POST['num'];
		$cookie_arr = array();
		if($username != "" && $cookie != ""){
            $startdate=date("Y-m-d",time()-86400);

            $enddate = date("Y-m-d");

            $u = "http://pub.alimama.com/report/getTbkPaymentDetails.json?startTime=$startdate&endTime=$enddate&payStatus=&queryType=1&toPage=1&perPageSize=2&total=&_input_charset=utf-8";
            $str = openhttp_header($u, '', $cookie);

            if($str == "") {
                echo "error";
                return;
            }

                $options_model = M("Options");
			$option=$options_model->where("option_name='cookie_options'")->find();
			if($option){
				$options = (array)json_decode($option['option_value'],true);
				foreach($options as $op_data){
					if($op_data['username'] == $username) $op_data['cookie'] = $cookie;
					$cookie_arr[] = $op_data;
				}
			}

			$data['option_name']="cookie_options";
			$data['option_value']=json_encode($cookie_arr);
			$r=$options_model->where("option_name='cookie_options'")->save($data);

			$cookie_model = M("TbkqqCookies");
			$cookies = $cookie_model->where(array("username"=>$username,"num"=>$num))->find();
			if($cookies)
            $r1=$cookie_model->where(array("username"=>$username,"num"=>$num))->save(array("cookie"=>$cookie));
			else $r1 = $cookie_model->add(array("username"=>$username,"cookie"=>$cookie,"num"=>$num));
			if($r && $r1)echo "ok";
		}

	}

	public function save_goods(){
		if(IS_POST) {
			$appname = C("SITE_APPNAME");
			$msg = $_POST['msg'];

			$arr = explode(']',$msg);
			$arr = explode("\n",$arr[1]);
			$memo = "";
			$i = 0;

			foreach($arr as $str){
				if(preg_match('/https?:\/\/[\w=.?&\/;]+/',$str,$match)){
					$url = str_replace('amp;','',$match[0]);
					$host = parse_url($url, PHP_URL_HOST);
				if($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com'){
						$data = get_url_data($url);

						$iid = $data['id'];
					}

					elseif($host == 's.click.taobao.com'){
						$data = get_item($url);
						$iid = $data['id'];
					}
					else {
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
						$quan_link = $dd['url'];

						if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
							$quan_surpluse = $match[1];
						if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
							$quan_receive = $match[1];
						if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
							$qtime = $match[1];
						if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
							$quan = $match[1];
					}
				}
				else {
					if($i == 0)$memo = trim($str);
					else $memo = $memo . "\n". trim($str) ;
				}
$i++;
			}
			if($iid != ''){

				if($_POST['item'] == 'item')
				$taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
				elseif($_POST['item'] == 'item2')$taoke_model = M('TbkItem2','cmf_','DB_DATAOKE');
				$taoke = $taoke_model->where(array("iid"=>$iid))->find();
				$curtime = date("Y-m-d H:i:s");
				$data = array();

				if(!$taoke){

					$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
					$str = openhttp_header($u, '', '');
					$arr = json_decode($str, true);
					$item = $arr['data']['pageList'][0]['title'];
					$img = $arr['data']['pageList'][0]['pictUrl'];
					$eventRate = $arr['data']['pageList'][0]['eventRate'];
					$tkRate = $arr['data']['pageList'][0]['tkRate'];

					if($item == ""){
						$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
						sleep(1);
						$str = openhttp_header($u, '', '');
						$arr = json_decode($str, true);
						$item = $arr['data']['pageList'][0]['title'];
						$img = $arr['data']['pageList'][0]['pictUrl'];
						$eventRate = $arr['data']['pageList'][0]['eventRate'];
						$tkRate = $arr['data']['pageList'][0]['tkRate'];
						if($item == ""){
							$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
							sleep(1);
							$str = openhttp_header($u, '', '');
							$arr = json_decode($str, true);
							$item = $arr['data']['pageList'][0]['title'];
							$img = $arr['data']['pageList'][0]['pictUrl'];
							$eventRate = $arr['data']['pageList'][0]['eventRate'];
							$tkRate = $arr['data']['pageList'][0]['tkRate'];
						}
					}
					$data['iid'] = $iid;
					$data['item'] = $item;
                    if(strstr($img,"http:")!==false)
                        $img = $img;
                    else $img = 'http:' . $img;
					$data['img'] = $img;
					$data['memo'] = $memo;
					$data['quan_link'] = $quan_link;
					$data['quan_surpluse'] = $quan_surpluse;
					$data['quan_receive'] = $quan_receive;
					$data['qtime'] = $qtime;
					$data['quan'] = $quan;
					$data['itime'] = $curtime;
					$dataoke_result =$taoke_model->add($data);
					echo $memo;
				}

			}

			///////////////////////////////////////////////////
			$arr = explode("\n",$msg);

				$data['d_title'] = $arr[1];
				$data['intro'] = $arr[5];
				if(preg_match('/https?:\/\/[\w=.?&\/;]+/',$arr[4],$match)) {
					$url = str_replace('amp;', '', $match[0]);
					$host = parse_url($url, PHP_URL_HOST);
					if ($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com') {
						$data1 = get_url_data($url);

						$iid = $data1['id'];
					} elseif ($host == 's.click.taobao.com') {
						$data1 = get_item($url);
						$iid = $data1['id'];
					}

					if ($iid != '') {
						$info = get_item_info($iid);
						unset($data['id']);
						$data['num_iid'] = $iid;
						$data['title'] = $info->title;
						$data['pic_url'] = $info->pict_url;
						$data['price'] = $info->zk_final_price;
						$data['nick'] = $info->nick;
						$data['sellerId'] = $info->seller_id;
						$data['volume'] = $info->volume;


						if (preg_match('/https?:\/\/[\w=.?&\/;]+/', $arr[3], $match)) {
							$url = str_replace('amp;', '', $match[0]);
							$html = http_get_content($url);

							preg_match('/[r][e][s][t]\">\d*/', $html, $rest);
							if (empty($rest)) {
								exit;
							}

							preg_match_all('/([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]|[0-9][1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8])))/', $html, $effectdate);
							if (empty($effectdate)) {
								exit;
							}
							$data['coupon_start_time'] = strtotime($effectdate[0][0]);
							$data['coupon_end_time'] = strtotime($effectdate[0][1]);

							$restnum = explode(">", $rest[0]);
							$data['Quan_surplus'] = ($restnum[1]);
							preg_match('/<dt>\d*/', $html, $quan);
							if (empty($quan)) {
								exit;
							}
							$quanprice = explode("<dt>", $quan[0]);
							$data['quan'] = ($quanprice[1]);
							$data['coupon_price'] = $data['price']-$data['quan'];
							$data['quanurl'] = $quan_link;
							if($quan_link != ""){
								$quan_data = get_url_data($quan_link);
								if($quan_data['activity_id'] == "")$quan_id = $quan_data['activityId'];
								else $quan_id = $quan_data['activity_id'];
								$data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=";
							}
							$data['add_time'] = time();

						}
						if (M("CaijiqqItems", 'cmf_', 'DB_DATAOKE')->where(array("num_iid" => $iid))->find())
							M("CaijiqqItems", 'cmf_', 'DB_DATAOKE')->save($data);
						else
							M("CaijiqqItems", 'cmf_', 'DB_DATAOKE')->add($data);
					}


				}

		}
	}

	public function save_goods1(){
		if(IS_POST) {
			$appname = C("SITE_APPNAME");
			$msg = $_POST['msg'];

			$arr = explode(']',$msg);
			$arr = explode("\n",$arr[1]);
			$memo = "";
			$i = 0;

			foreach($arr as $str){
				if(preg_match('/https?:\/\/[\w=.?&\/;]+/',$str,$match)){
					$url = str_replace('amp;','',$match[0]);
					$host = parse_url($url, PHP_URL_HOST);
					if($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com'){
						$data = get_url_data($url);

						$iid = $data['id'];
					}

					elseif($host == 's.click.taobao.com'){
						$data = get_item($url);
						$iid = $data['id'];
					}
					else {
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
						$quan_link = $dd['url'];

						if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
							$quan_surpluse = $match[1];
						if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
							$quan_receive = $match[1];
						if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
							$qtime = $match[1];
						if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
							$quan = $match[1];
					}
				}
				else {
					if($i == 0)$memo = trim($str);
					else $memo = $memo . "\n". trim($str) ;
				}
				$i++;
			}
			if($iid != ''){

				if($_POST['item'] == 'item')
					$taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
				elseif($_POST['item'] == 'item2')$taoke_model = M('TbkItem2','cmf_','DB_DATAOKE');
				$taoke = $taoke_model->where(array("iid"=>$iid))->find();
				$curtime = date("Y-m-d H:i:s");
				$data = array();

				if(!$taoke){

					$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
					$str = openhttp_header($u, '', '');
					$arr = json_decode($str, true);
					$item = $arr['data']['pageList'][0]['title'];
					$img = $arr['data']['pageList'][0]['pictUrl'];
					$eventRate = $arr['data']['pageList'][0]['eventRate'];
					$tkRate = $arr['data']['pageList'][0]['tkRate'];

					if($item == ""){
						$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
						sleep(1);
						$str = openhttp_header($u, '', '');
						$arr = json_decode($str, true);
						$item = $arr['data']['pageList'][0]['title'];
						$img = $arr['data']['pageList'][0]['pictUrl'];
						$eventRate = $arr['data']['pageList'][0]['eventRate'];
						$tkRate = $arr['data']['pageList'][0]['tkRate'];
						if($item == ""){
							$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
							sleep(1);
							$str = openhttp_header($u, '', '');
							$arr = json_decode($str, true);
							$item = $arr['data']['pageList'][0]['title'];
							$img = $arr['data']['pageList'][0]['pictUrl'];
							$eventRate = $arr['data']['pageList'][0]['eventRate'];
							$tkRate = $arr['data']['pageList'][0]['tkRate'];
						}
					}
					$data['iid'] = $iid;
					$data['item'] = $item;
					$data['img'] = $img;
					$data['memo'] = $memo;
					$data['quan_link'] = $quan_link;
					$data['quan_surpluse'] = $quan_surpluse;
					$data['quan_receive'] = $quan_receive;
					$data['qtime'] = $qtime;
					$data['quan'] = $quan;
					$data['itime'] = $curtime;
					//$dataoke_result =$taoke_model->add($data);
					echo $memo;
				}

			}


			///////////////////////////////////////////////////
			$arr = explode("\n",$msg);
			if(count($arr) == 6){
				$data['D_Title'] = $arr[1];
				$data['intro'] = $arr[5];
				if(preg_match('/https?:\/\/[\w=.?&\/;]+/',$arr[4],$match)) {
					$url = str_replace('amp;', '', $match[0]);
					$host = parse_url($url, PHP_URL_HOST);
					if ($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com') {
						$data1 = get_url_data($url);

						$iid = $data1['id'];
					}
					elseif($host == 's.click.taobao.com'){
						$data1 = get_item($url);
						$iid = $data1['id'];
					}

					$info = get_item_info($iid);
					unset($data['id']);
					$data['num_iid'] = $iid;
					$data['title'] = $info->title;
					$data['pic_url'] = $info->pict_url;
					$data['price'] = $info->zk_final_price;
					$data['nick'] = $info->nick;
					$data['sellerId'] = $info->seller_id;
					$data['volume'] = $info->volume;


					if(preg_match('/https?:\/\/[\w=.?&\/;]+/',$arr[3],$match)) {
						$url = str_replace('amp;', '', $match[0]);
						$html = http_get_content($url);

						preg_match('/[r][e][s][t]\">\d*/', $html, $rest);
						if(empty($rest)){
							exit;
						}

						preg_match_all('/([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]|[0-9][1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8])))/', $html, $effectdate);
						if(empty($effectdate)){
							exit;
						}
						$data['coupon_start_time'] = strtotime($effectdate[0][0]);
						$data['coupon_end_time'] = strtotime($effectdate[0][1]);

						$restnum = explode(">",$rest[0]);
						$data['Quan_surplus'] = ($restnum[1]);
						preg_match('/<dt>\d*/', $html, $quan);
						if(empty($quan)){
							exit;
						}
						$quanprice = explode("<dt>",$quan[0]);
						$data['coupon_price'] = ($quanprice[1]);


					}

				}
				print_r($data);
				if(M("CaijiqqItems",'cmf_','DB_DATAOKE')->where(array("num_iid"=>$iid))->find())
					M("CaijiqqItems",'cmf_','DB_DATAOKE')->save($data);
				else
					M("CaijiqqItems",'cmf_','DB_DATAOKE')->add($data);
			}

		}
	}

	public function find_quan(){
		$iid = $_GET['iid'];
		$taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
		$taoke = $taoke_model->where(array("iid"=>$iid))->find();
		if($taoke){
			$data = get_url_data($taoke['quan_link']);
			if($data['activity_id'] == "")$quan_id = $data['activityId'];
			else $quan_id = $data['activity_id'];
			echo $quan_id;
			exit();
		}
		else{
			$taoke_model = M('TbkItem2','cmf_','DB_DATAOKE');
			$taoke = $taoke_model->where(array("iid"=>$iid))->find();
			if($taoke){
				$data = get_url_data($taoke['quan_link']);
				if($data['activity_id'] == "")$quan_id = $data['activityId'];
				else $quan_id = $data['activity_id'];
				echo $quan_id;
				exit();
			}
			else {
				$taoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
				$taoke = $taoke_model->where(array("iid"=>$iid))->find();
				if($taoke){
					$data = get_url_data($taoke['quan_link']);
					if($data['activity_id'] == "")$quan_id = $data['activityId'];
					else $quan_id = $data['activity_id'];
					echo $quan_id;
					exit();
				}
			}
		}
	}

	public function test_taotoken(){
		$iid = $_GET['iid'];
		$item = get_item_info($iid);

		$pid = "mm_113095813_20098166_68878161";
$quan_id = "";
			$data = array();
			$data['url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $pid . "&itemId=" . $iid . "&src=cd_cdll";
			$data['logo'] = $item->pict_url;
			$data['text'] = $item->title;
echo $data['url'] . "<br>";
			echo get_taotoken($data);

	}



}