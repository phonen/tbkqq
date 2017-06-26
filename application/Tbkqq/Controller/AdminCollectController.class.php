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
use Think\Model;

class AdminCollectController extends AdminbaseController {
	function _initialize() {
//		parent::_initialize();
	}



	public function items_upload(){
		if(IS_POST) {
			header("Content-Type:text/html;charset=utf-8");
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728;// 设置附件上传大小
			$upload->exts = array('xls', 'xlsx');// 设置附件上传类
			$upload->savePath = '/'; // 设置附件上传目录
			// 上传文件
			$info = $upload->upload();
			if (!$info) {// 上传错误提示错误信息
				$this->error($upload->getError());
			} else {// 上传成功
				foreach ($info as $file) {
					$exts = $file['ext'];
					$data = explode(".", $file['name']);
					$filename = './Uploads' . $file['savepath'] . $file['savename'];
					$this->item_import($filename, $exts, $data[0]);
				}
			}
		}
	}

	protected function item_import($filename,$exts,$proxyid){
		import("Org.Util.PHPExcel");
		$PHPExcel=new \PHPExcel();
		if($exts == 'xls'){
			import("Org.Util.PHPExcel.Reader.Excel5");
			$PHPReader=new \PHPExcel_Reader_Excel5();
		}else if($exts == 'xlsx'){
			import("Org.Util.PHPExcel.Reader.Excel2007");
			$PHPReader=new \PHPExcel_Reader_Excel2007();
		}

		//载入文件
		$PHPExcel=$PHPReader->load($filename);
		//获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
		$currentSheet=$PHPExcel->getSheet(0);
		//获取总列数
		$allColumn=$currentSheet->getHighestColumn();
		if($allColumn == "M")$linktype = "dingxiang";
		else if($allColumn == "R")$linktype = "queqiao";

		$allColumn++;
		//获取总行数
		$allRow=$currentSheet->getHighestRow();
		//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
		for($currentRow=2;$currentRow<=$allRow;$currentRow++) {
			$data = array();
			$data['proxyid'] = $proxyid;
			$dataurl = array();
			$dataurl['proxyid'] = $proxyid;
			for ($currentColumn = 'A'; $currentColumn != $allColumn; $currentColumn++) {
				//数据坐标
				$address = $currentColumn . $currentRow;
				//读取到的数据，保存到数组$arr中
				$cell = $currentSheet->getCell($address)->getValue();

				if ($cell instanceof PHPExcel_RichText) {
					$cell = $cell->__toString();
				}
				if($linktype == "dingxiang"){
					switch ($currentColumn)
					{
						case 'A':
							$data['iid']=$cell;
							$dataurl['iid']=$cell;
							break;
						case 'B':
							$data['item']=$cell;
							break;
						case 'C':
							$data['img']=$cell;
							break;
						case 'D':
							$data['itemurl']=$cell;
							break;
						case 'E':
							$data['seller']=$cell;
							break;
						case 'F':
							$data['price']=$cell;
							break;
						case 'G':
							$data['sellcount']=$cell;
							break;
						case 'H':
							$data['srrate']=$cell;
							break;
						case 'I':
							$data['yongjin']=$cell;
							break;
						case 'J':
							$data['wangwang']=$cell;
							break;
						case 'K':
							$dataurl['shorturl']=$cell;
							break;
						case 'L':
							$dataurl['url']=$cell;
							break;
						case 'M':
							$dataurl['taokl']=$cell;
							break;
					}
				}
				else if($linktype == "queqiao"){
					switch ($currentColumn)
					{
						case 'A':
							$data['iid']=$cell;
							$dataurl['iid']=$cell;
							break;
						case 'B':
							$data['item']=$cell;
							break;
						case 'C':
							$data['img']=$cell;
							break;
						case 'D':
							$data['itemurl']=$cell;
							break;
						case 'E':
							$data['seller']=$cell;
							break;
						case 'F':
							$data['price']=$cell;
							break;
						case 'G':
							$data['sellcount']=$cell;
							break;
						case 'H':
							$data['srrate']=$cell;
							break;
						case 'I':
							$data['yongjin']=$cell;
							break;
						case 'O':
							$data['wangwang']=$cell;
							break;
						case 'P':
							$dataurl['shorturl']=$cell;
							break;
						case 'Q':
							$dataurl['url']=$cell;
							break;
						case 'R':
							$dataurl['taokl']=$cell;
							break;
					}
				}
			}
//
			$curtime = date("Y-m-d H:i:s");
			$item = M("TbkqqTaokeItem")->where(array('iid'=>$data['iid']))->find();
			if($item['status']=='-1')$data['status'] = '0';
			$data['itime'] = $curtime;
			if($item)M("TbkqqTaokeItem")->where(array("id"=>$item['id']))->save($data);
//			else M("TbkqqTaokeItem")->add($data);

			$itemurl = M("TbkqqTaokeItemurl")->where(array('iid'=>$data['iid'],'proxyid'=>$proxyid))->find();
			$dataurl['itime'] = $curtime;
			if($itemurl)M("TbkqqTaokeItemurl")->where(array("id"=>$itemurl['id']))->save($dataurl);
			else M("TbkqqTaokeItemurl")->add($dataurl);
		}

	}


	public function media(){
		$where_ands = array("1=1");
		$fields=array(
			'username'=> array("field"=>"username","operator"=>"="),
		);
		if(IS_POST) {
			foreach ($fields as $param => $val) {
				if (isset($_POST[$param]) && !empty($_POST[$param])) {
					$operator = $val['operator'];
					$field = $val['field'];
					$get = $_POST[$param];
					$_GET[$param] = $get;
					if ($operator == "like") {
						$get = "%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}

		}
		$where = join(" and ", $where_ands);
		$medias=M("TbkqqTaokeMedia")
			->where($where)->order("username,media,adname")
			->select();
		$this->assign("medias",$medias);

		$this->display();
	}

	public function item_load(){
		$media = M("TbkqqTaokeMedia")->field("username")->group("username")->select();
		$this->assign("media",$media);
		$this->display();
	}

	public function item_load_post(){
		if (IS_POST) {
			$username=I("post.username");
			$proxy=I("post.proxy");
			$options_model = M("Options");
			$option=$options_model->where("option_name='cookie_options'")->find();
			if($option){
				$options = (array)json_decode($option['option_value'],true);
				foreach($options as $data) {
					if($data['username'] == $username) $cookie = $data['cookie'];
				}
			}
			$groupid=I("post.groupid");
			$map['username'] = $username;
			$map['proxy'] = array('egt',$proxy);
			$map['status'] = '1';
			$medias = M("TbkqqTaokeMedia")->where($map)->select();
			$header[] = "Host: pub.alimama.com";
			//$header[] = "Accept-Encoding: gzip,deflate,sdch";
			$header[] = "Accept-Language: zh-CN,zh;q=0.8";
			$header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36";

			foreach($medias as $media) {
				$u="http://pub.alimama.com/favorites/item/export.json?scenes=1&adzoneId=" . $media['adid'] . "&siteId=" . $media['mediaid'] . "&groupId=" . $groupid;
/*
				$ch=curl_init();
				$a_opt=array(
					CURLOPT_URL => $u,
					CURLOPT_HEADER => 0,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_NOPROGRESS => 1,//手册上说这个关掉可以显示进度，我怎么没看到？
					CURLOPT_READFUNCTION => 'abc', //这个手册上说设置一个回调函数，这个怎么用?
					CURLOPT_COOKIE => $cookie,
				);
				curl_setopt_array($ch,$a_opt);
//				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				$str=curl_exec($ch);
*/
				$str = openhttp_header($u, '', $cookie);

				//$proxyid = substr($media['proxy'], -3, 3);

				$proxyid = substr($media['proxy'],strlen(C('SITE_APPNAME')));
				$filename =  './Uploads/' . $proxyid . ".xls";
				$f=fopen($filename,'w');
				if($f){
					fwrite($f,$str);
					$this->item_import($filename, "xls", $proxyid);
					fclose($f);
					sleep(3);
				}
				else $this->error("保存失败！");
			}
			$this->success("保存成功！");
		}
	}

	public function item_favload_post(){
		if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
			if(isset($_POST['ids'])) {
				$ids = join(",", $_POST['ids']);
			}

			$items = $item_model->where("id in ($ids)")->select();
			if($items){
				foreach($items as $item){
					$iid[] = $item['iid'];
				}
				$iids = join(",",$iid);
			}
			$post['groupId'] = $_POST['groupId'];
			$post['itemListStr'] = $iids;
			$u = "http://pub.alimama.com/favorites/item/batchAdd.json";
			$username=I("post.username");

			$options_model = M("Options");
			$option=$options_model->where("option_name='cookie_options'")->find();
			if($option){
				$options = (array)json_decode($option['option_value'],true);
				foreach($options as $data) {
					if($data['username'] == $username) $cookie = $data['cookie'];
				}
			}
			$str = openhttp_header($u,$post,$cookie);



			$this->success($str);
		}
	}

	public function item_fav_load_post(){
		if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
			if(isset($_POST['ids'])) {
				$ids = join(",", $_POST['ids']);
			}

			$items = $item_model->where("id in ($ids)")->select();
			if($items){
				foreach($items as $item){
					$iid[] = $item['iid'];
				}
				$iids = join(",",$iid);
			}
			$post['groupId'] = $_POST['groupId'];
			$post['itemListStr'] = $iids;
			$u = "http://pub.alimama.com/favorites/item/batchAdd.json";
			$username=I("post.username");

			$options_model = M("Options");
			$option=$options_model->where("option_name='cookie_options'")->find();
			if($option){
				$options = (array)json_decode($option['option_value'],true);
				foreach($options as $data) {
					if($data['username'] == $username) $cookie = $data['cookie'];
				}
			}
			$str = openhttp_header($u,$post,$cookie);
			$this->success($str);
		}
	}

    public function item_campaign_post1(){
        set_time_limit(0);
        if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
            }
            $username=I("post.username");

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
                $items = $item_model->where("id in ($ids)")->select();
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
                            if($type == '0')M("TbkqqTaokeItem")->where(array("iid"=>$iid))->save(array("type"=>$type));
                        }
                    }
                }
            }

            $this->success($ret);
        }
    }

	public function item_campaign_post(){
        set_time_limit(0);
		if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
			if(isset($_POST['ids'])) {
				$ids = join(",", $_POST['ids']);
			}
			$username=I("post.username");

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
                $items = $item_model->where("id in ($ids)")->select();
                if($items) {
                    foreach ($items as $item) {
                        $iid = $item['iid'];
                        $type = apply_campaign($cookie,$iid);

                        if($type == '0')$item_model->where(array("iid"=>$iid))->save(array("type"=>$type));
                    }
                }
            }

			$this->success($ret);
		}
	}

	public function details_load(){
//		$media = M("TbkqqTaokeMedia")->field("username")->group("username")->select();
//		$this->assign("media",$media);
		$this->display();
	}

	public function details_load_post(){
		if (IS_POST) {
			$type = I("post.type");
			$startdate=I("post.startdate");
			$options_model = M("Options");

			$enddate = date("Y-m-d");
			if($startdate == "")  $this->error("导入失败！");
			else $where = "ctime>='" . $startdate . "'";
			M("TbkqqTaokeDetails")->where($where)->delete();
			$u="http://pub.alimama.com/report/getTbkPaymentDetails.json?queryType=1&payStatus=&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime=$startdate&endTime=$enddate";
			$option=$options_model->where("option_name='cookie_options'")->find();
			if($option){
				$options = (array)json_decode($option['option_value'],true);
				foreach($options as $data) {
					$cookie = $data['cookie'];
					$str = openhttp_header($u, '', $cookie);
					$curtime = time();
					$filename =  './Uploads/' . $curtime . ".xls";
					$f=fopen($filename,'w');
					if($f){
						fwrite($f,$str);
//						$this->details_import($filename, "xls");
						fclose($f);
						sleep(3);
					}
					else $this->error("导入失败！");
				}
			}

			$this->success("导入成功！");
		}
	}

	public function detail_autoload_post1(){
		set_time_limit(0);
		$startdate = date("Y-m-d",time()-2*86400);
		$enddate =date("Y-m-d");
		$where = "ctime>='" . $startdate . "'";
		$filename_arr = array();
		$filename = "";
		$u="http://pub.alimama.com/report/getTbkPaymentDetails.json?queryType=1&payStatus=&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime=$startdate&endTime=$enddate";

		$options_model = M("Options");
		$option=$options_model->where("option_name='cookie_options'")->find();
		if($option){

			$options = (array)json_decode($option['option_value'],true);

			foreach($options as $data) {
				$media = M("TbkqqTaokeMedia")->where(array("username" => $data['username']))->find();
				if ($media) {

					$cookie = $data['cookie'];
					$str = openhttp_header($u, '', $cookie);

if($str == "") exit();
					$curtime = time();
					$filename = './Uploads/details_' . $curtime . ".xls";
					$f = fopen($filename, 'w');
					echo $data['username'] . "\n";
					if ($f) {
						fwrite($f, $str);
						$filename_arr[] = $filename;
						fclose($f);
						sleep(3);
					}

				}
			}

			M("TbkqqTaokeDetails")->where($where)->delete();
			foreach($filename_arr as $filename){
				$this->details_import($filename, "xls",'taoke');
			}

			$filename_arr = array();
			$filename = "";
			foreach($options as $data) {
				$fanli_media = M("TbkqqFanliMedia")->where(array("username"=>$data['username']))->find();
				if($fanli_media){
					$cookie = $data['cookie'];
					$str = openhttp_header($u, '', $cookie);
					if($str == "") exit();
					$curtime = time();
					$filename =  './Uploads/details_' . $curtime . ".xls";
					$f=fopen($filename,'w');
					echo $data['username'] . "\n";
					if($f){
						fwrite($f,$str);
						$filename_arr[] = $filename;
						fclose($f);
						sleep(3);
					}
				}
			}

			M("TbkqqFanliDetails")->where($where)->delete();
			foreach($filename_arr as $filename){
				$this->details_import($filename, "xls",'fanli');
			}
		}

	}

    public function detail_autoload(){
        set_time_limit(0);
        $startdate = date("Y-m-d",time()-2*86400);
        $enddate =date("Y-m-d");
        $where = "ctime>='" . $startdate . "'";
        $filename_arr = array();
        $filename = "";
        $u="http://pub.alimama.com/report/getTbkPaymentDetails.json?queryType=1&payStatus=&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime=$startdate&endTime=$enddate";

        $options_model = M("Options");
        $option=$options_model->where("option_name='cookie_options'")->find();
        if($option){

            $options = (array)json_decode($option['option_value'],true);

            foreach($options as $data) {
                $media = M("TbkqqTaokeMedia")->where(array("username" => $data['username']))->find();
                if ($media) {

                    $cookie = $data['cookie'];
                    $str = openhttp_header($u, '', $cookie);

                    if($str == "") continue;
                    $curtime = time();
                    $filename = './Uploads/details_' . $curtime . ".xls";
                    $f = fopen($filename, 'w');
                    echo $data['username'] . "\n";
                    if ($f) {
                        fwrite($f, $str);
                        //$filename_arr[] = $filename;
                        fclose($f);
                        sleep(3);
                        M("TbkqqTaokeDetails")->where($where . " and username='" . $data['username'] ." '")->delete();
                        $this->details_import($filename, "xls",'taoke',$data['username']);
                    }

                }
            }

        }

    }

	public function detail_testload_post(){
		$startdate = date("Y-m-d",time()-86400);
		$enddate =date("Y-m-d");
		$where = "ctime>='" . $startdate . "'";
		M("TbkqqDetailsTemp")->where("1=1")->delete();
		$u="http://pub.alimama.com/report/getTbkPaymentDetails.json?queryType=1&payStatus=&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime=$startdate&endTime=$enddate";
		$options_model = M("Options");
		$option=$options_model->where("option_name='cookie_options'")->find();
		if($option){
			$options = (array)json_decode($option['option_value'],true);
			foreach($options as $data) {
				$media = M("TbkqqTaokeMedia")->where(array("username" => $data['username']))->find();
				if ($media) {
					$cookie = $data['cookie'];
					$str = openhttp_header($u, '', $cookie);
					if($str == "") echo "ssldkjfl";
					$curtime = time();
					$filename = './Uploads/' . $curtime . ".xls";
					$f = fopen($filename, 'w');
					echo $data['username'] . "\n";
					if ($f) {
						fwrite($f, $str);
						//$this->details_import($filename, "xls");
						fclose($f);
						sleep(3);
					}
//					else $this->error("导入失败！");

				}
			}

			//
			M("TbkqqDetailsTemp")->where("1=1")->delete();
			foreach($options as $data) {
				$fanli_media = M("TbkqqFanliMedia")->where(array("username"=>$data['username']))->find();
				if($fanli_media){
					$cookie = $data['cookie'];
					$str = openhttp_header($u, '', $cookie);
					$curtime = time();
					$filename =  './Uploads/' . $curtime . ".xls";
					$f=fopen($filename,'w');
					if($f){
						fwrite($f,$str);
						//$this->details_import($filename, "xls");
						fclose($f);
						sleep(3);
					}
//					else $this->error("导入失败！");
				}
			}
			//
			M("TbkqqDetailsTemp")->where("1=1")->delete();
		}

//		$this->success("导入成功！");
	}

	protected function details_import($filename,$exts,$type,$username){
		if($type == "taoke")	$model = M("TbkqqTaokeDetails");
		if($type == "fanli") $model = M("TbkqqFanliDetails");
		import("Org.Util.PHPExcel");
		$PHPExcel=new \PHPExcel();
		if($exts == 'xls'){
			import("Org.Util.PHPExcel.Reader.Excel5");
			$PHPReader=new \PHPExcel_Reader_Excel5();
		}else if($exts == 'xlsx'){
			import("Org.Util.PHPExcel.Reader.Excel2007");
			$PHPReader=new \PHPExcel_Reader_Excel2007();
		}
//$ctime = M("TbkqqTaokeDetail")->max(ctime);
//		$maxctime = strtotime($ctime);
		//载入文件
		$PHPExcel=$PHPReader->load($filename);
		//获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
		$currentSheet=$PHPExcel->getSheet(0);
		//获取总列数
		$allColumn=$currentSheet->getHighestColumn();
		$allColumn++;
		//获取总行数
		$allRow=$currentSheet->getHighestRow();
		//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
		for($currentRow=2;$currentRow<=$allRow;$currentRow++) {
			$data = array();
			for ($currentColumn = 'A'; $currentColumn != $allColumn; $currentColumn++) {
				//数据坐标
				$address = $currentColumn . $currentRow;
				//读取到的数据，保存到数组$arr中
				$cell = $currentSheet->getCell($address)->getValue();
				/*
                                if ($cell instanceof PHPExcel_RichText) {
                                    $cell = $cell->__toString();
                                }
                */
				if(is_object($cell))  $cell= $cell->__toString();
				switch ($currentColumn)
				{
					case 'A':
						$data['ctime']=$cell;
						break;
					case 'C':
						$data['goods']=$cell;
						break;
					case 'D':
						$data['gid']=$cell;
						break;
					case 'E':
						$data['wangwang']=$cell;
						break;
					case 'F':
						$data['shop']=$cell;
						break;
					case 'G':
						$data['gcount']=$cell;
						break;
					case 'H':
						$data['gamount']=$cell;
						break;
					case 'I':
						$data['ostatus']=$cell;
						break;
					case 'J':
						$data['otype']=$cell;
						break;
					case 'K':
						$data['srrate']=$cell;
						break;
					case 'L':
						$data['fcrate']=$cell;
						break;
					case 'M':
						$data['fukuan']=$cell;
						break;
					case 'N':
						$data['effect']=$cell;
						break;
					case 'O':
						$data['jiesuan']=$cell;
						break;
					case 'P':
						$data['pre_amount']=$cell;
						break;
					case 'Q':
						$data['jstime']=$cell;
						break;
					case 'R':
						$data['yjrate']=$cell;
						break;
					case 'S':
						$data['yongjin']=$cell;
						break;
					case 'T':
						$data['btrate']=$cell;
						break;
					case 'U':
						$data['butie']=$cell;
						break;
					case 'V':
						$data['bttype']=$cell;
						break;
					case 'W':
						$data['third']=$cell;
						break;
					case 'X':
						$data['pingtai']=$cell;
						break;
					case 'Y':
						$data['orderid']=$cell;
						break;
					case 'Z':
						$data['class']=$cell;
						break;
					case 'AA':
						$data['sourceid']=$cell;
						break;
					case 'AD':
						$data['adname']=$cell;
						break;
				}
			}
			$data['username'] = $username;
//			print_r($data);
			//$detail = M("TbkqqTaokeDetail")->where(array("orderid"=>$data['orderid'],"gid"=>$data['gid'],"gcount"=>$data['gcount']))->find();
			//if($detail) M("TbkqqTaokeDetail")->where(array("orderid"=>$data['orderid'],"gid"=>$data['gid'],"gcount"=>$data['gcount']))->save($data);

			$model->add($data);

		}

	}

	public function get_dataoke(){
		set_time_limit(0);
		$dataoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
		$dataokes = $dataoke_model->field('iid')->select();
		$curtime = date("Y-m-d H:i:s");
		foreach($dataokes as $dataoke){

			$iids[] = $dataoke['iid'];
		}

		$u = "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey=bnsdd1etil&v=2";
		$str = file_get_contents($u);

		$json = json_decode($str,true);
		if($json['result']){
			foreach($json['result'] as $item){

				$data = array();

				$data['iid'] = $item['GoodsID'];
				$data['title'] = $item['Title'];
				$data['d_title'] = $item['D_title'];
				$data['img'] = $item['Pic'];
				$data['cid'] = $item['Cid'];
				$data['org_price'] = $item['Org_Price'];
				$data['price'] = $item['Price'];
				$data['istmall'] = $item['IsTmall'];
				$data['sales_num'] = $item['Sales_num'];
				$data['dsr'] = $item['Dsr'];
				$data['sellerid'] = $item['SellerID'];
				$data['commission'] = $item['Commission'];
				$data['commission_jihua'] = $item['Commission_jihua'];
				$data['commission_queqiao'] = $item['Commission_queqiao'];
				$data['jihua_link'] = $item['Jihua_link'];
				$data['que_siteid'] = $item['Que_siteid'];
				$data['jihua_shenhe'] = $item['Jihua_shenhe'];
				$data['introduce'] = $item['Introduce'];
				$data['quan_id'] = $item['Quan_id'];
				$data['quan_price'] = $item['Quan_price'];
				$data['quan_time'] = $item['Quan_time'];
				$data['quan_surplus'] = $item['Quan_surplus'];
				$data['quan_receive'] = $item['Quan_receive'];
				$data['quan_condition'] = $item['Quan_condition'];
				$data['quan_m_link'] = $item['Quan_m_link'];
				$data['quan_link'] = $item['Quan_link'];
				$data['itime'] = $curtime;
				if(in_array($item['GoodsID'],$iids))
					$dataoke_result =$dataoke_model->where(array("iid"=>$item['GoodsID']))->save($data);
				//continue;
				else {
					$dataoke_result =$dataoke_model->add($data);
					$username = C("SITE_USERNAME");
					$t = time();
					$options_model = M("Options");
					$option=$options_model->where("option_name='cookie_options'")->find();
					if($option) {
						$options = (array)json_decode($option['option_value'], true);
						foreach ($options as $data) {
							if ($data['username'] == $username) $cookie = $data['cookie'];
						}
						if ($cookie != "") {
							$u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $data['iid'] . "&auctionTag=&perPageSize=40&shopTag=";
							$str = openhttp_header($u, '', $cookie);
							$arr = json_decode($str, true);
							$sellerId = $arr['data']['pageList'][0]['sellerId'];
							$tkRate = $arr['data']['pageList'][0]['tkRate'];
							$eventRate = $arr['data']['pageList'][0]['eventRate'];


							$u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" .$data['iid'];
							$str = openhttp_header($u, '', $cookie);
							$arr = json_decode($str, true);

							if ($arr['ok'] == '1' && $arr['data']) {
								$rate = $tkRate;
								if ($eventRate != '') {
									if ($rate < $eventRate) {
										$rate = $eventRate;
									}
								}
								$cid = '';
								$keeperid = '';
								$post = array();

								foreach ($arr['data'] as $data) {
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
									$reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $data['iid'];
									sleep(1);
									$ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
								}

							} else {
								$rate = $tkRate;
								if ($eventRate != '') {
									if ($rate < $eventRate) {
										$rate = $eventRate;
									}
								}
							}
						}
					}
				}
			}
		}


	}

	public function  get_haodanku(){
        set_time_limit(0);
		echo "get_haodanku start";
        $dataoke_model = M('TbkItem3','cmf_','DB_DATAOKE');
        $dataokes = $dataoke_model->field('iid')->select();
        $curtime = date("Y-m-d H:i:s");
        foreach($dataokes as $dataoke){
            $iids[] = $dataoke['iid'];
        }

        $p = $_GET['page'];

            $u = "http://www.haodanku.com/index/index/nav/3/starttime/7/p/" . $p .".html?json=true";
            echo $u;
            $str = file_get_contents($u);

            $json = json_decode($str,true);
            if($json){
                foreach($json as $item){

                    $data = array();

                    $data['iid'] = $item['itemid'];
                    $data['item'] = $item['itemtitle'];

                    $data['img'] = $item['itempic'];

                    $data['aftprice'] = $item['itemprice']-$item['couponmoney'];
                    //$data['quan_price'] = $item['couponmoney'];
                    $data['qtime'] = date("Y-m-d",$item['couponendtime']);
                    $data['quan_surplus'] = $item['couponsurplus'];
                    $data['quan_receive'] = $item['couponreceive'];
                    $data['quan'] = $item['couponexplain'];

                    $data['quan_link'] = $item['couponurl'];
                    $data['itime'] = $curtime;
                    if(in_array($item['itemid'],$iids))
                        $dataoke_result =$dataoke_model->where(array("iid"=>$item['itemid']))->save($data);
                    //continue;
                    else {
                        $dataoke_result =$dataoke_model->add($data);
                        /*
                        $username = C("SITE_USERNAME");
                        $t = time();
                        $options_model = M("Options");
                        $option=$options_model->where("option_name='cookie_options'")->find();
                        if($option) {
                            $options = (array)json_decode($option['option_value'], true);
                            foreach ($options as $data) {
                                if ($data['username'] == $username) $cookie = $data['cookie'];
                            }
                            if ($cookie != "") {
                                $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $data['iid'] . "&auctionTag=&perPageSize=40&shopTag=";
                                $str = openhttp_header($u, '', $cookie);
                                $arr = json_decode($str, true);
                                $sellerId = $arr['data']['pageList'][0]['sellerId'];
                                $tkRate = $arr['data']['pageList'][0]['tkRate'];
                                $eventRate = $arr['data']['pageList'][0]['eventRate'];


                                $u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" .$data['iid'];
                                $str = openhttp_header($u, '', $cookie);
                                $arr = json_decode($str, true);

                                if ($arr['ok'] == '1' && $arr['data']) {
                                    $rate = $tkRate;
                                    if ($eventRate != '') {
                                        if ($rate < $eventRate) {
                                            $rate = $eventRate;
                                        }
                                    }
                                    $cid = '';
                                    $keeperid = '';
                                    $post = array();

                                    foreach ($arr['data'] as $data) {
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
                                        $reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $data['iid'];
                                        sleep(1);
                                        $ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
                                    }

                                } else {
                                    $rate = $tkRate;
                                    if ($eventRate != '') {
                                        if ($rate < $eventRate) {
                                            $rate = $eventRate;
                                        }
                                    }
                                }
                            }
                        }
                        */
                    }
                }
            }

    }

	public function clean_quan(){
		set_time_limit(0);
		$taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
		$taoke_model->where("qtime<now() - interval 1 day")->delete();
		$items = $taoke_model->select();

		foreach($items as $item){
			$header = array();
			$match = array();
			$quan_surpluse = "";
			$quan_receive = "";
			$qtime = "";
			$quan_link = $item['quan_link'];
			$iid = $item['iid'];
			//$header[] = "Accept-Language: zh-CN,zh;q=0.8";
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


			if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
				$quan_surpluse = $match[1];

			if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
				$quan_receive = $match[1];
			if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
				$qtime = $match[1];

			if($quan_surpluse == '')
					$taoke_model->where(array("iid"=>$iid))->delete();

			else
					$taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"qtime"=>$qtime));
		}




		///////////////////////////////////
		$taoke_model = M('TbkItem2','cmf_','DB_DATAOKE');
		$taoke_model->where("qtime<now() - interval 1 day")->delete();
		$items = $taoke_model->select();

		foreach($items as $item){
			$header = array();
			$match = array();
			$quan_surpluse = "";
			$quan_receive = "";
			$qtime = "";
			$quan_link = $item['quan_link'];
			$iid = $item['iid'];
			//$header[] = "Accept-Language: zh-CN,zh;q=0.8";
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


			if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
				$quan_surpluse = $match[1];

			if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
				$quan_receive = $match[1];
			if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
				$qtime = $match[1];

			if($quan_surpluse == '')
				$taoke_model->where(array("iid"=>$iid))->delete();

			else
				$taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"qtime"=>$qtime));
		}
	}


	public function clean_dataoke(){
		set_time_limit(0);
		$taoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
		$taoke_model->where("quan_time<now() - interval 1 day")->delete();
		$items = $taoke_model->select();

		foreach($items as $item){
			$header = array();
			$match = array();
			$quan_surpluse = "";
			$quan_receive = "";
			$qtime = "";
			$quan_link = $item['Quan_link'];
			$iid = $item['iid'];
			//$header[] = "Accept-Language: zh-CN,zh;q=0.8";
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


			if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
				$quan_surpluse = $match[1];

			if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
				$quan_receive = $match[1];
			if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
				$qtime = $match[1];

			if($quan_surpluse == '')
				$taoke_model->where(array("iid"=>$iid))->delete();

			else
				$taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"quan_time"=>$qtime));
		}

	}

	public function get_hongbao(){
		$media_model = M("TbkqqTaokeMedia");
		$options_model = M("Options");
		$option=$options_model->where("option_name='cookie_options'")->find();
		if($option) {
			$options = (array)json_decode($option['option_value'], true);
			foreach ($options as $data) {
				$medias = $media_model->where(array("username" => $data['username']))->select();
				foreach ($medias as $media) {
					$cookie = $data['cookie'];
					$u = 'http://pub.alimama.com/superCoupon/getUrlNew.json?adzoneid=' . $media['adid'] . '&siteid=' . $media['mediaid'] .'&eventId=6&_input_charset=utf-8';
					$str = openhttp_header($u, '', $cookie);
					if($str != ''){
						$json = json_decode($str,true);

					}
				}
			}
		}

	}


    public function item_dsh_post(){
        set_time_limit(0);
        if(IS_POST){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            $ids = json_decode($_POST['ids'],true);
            $username=$_POST['username'];
            $proxys = M("TbkqqTaokeMedia")->where(array("username"=>$username,'status'=>'1'))->select();
            //$proxys1 = M("TbkqqTaokeMedia")->where(array("username"=>'15219198262','status'=>'1'))->select();
            //if($proxys1)$proxys = array_merge($proxys,$proxys1);
            foreach($ids as $id){
                $no = $item_model->where(array("status"=>"1"))->max("no");
                $no = $no?$no:0;
                $no = $no+1;
                $item = $item_model->where(array("id"=>$id))->find();
//				$no1 = M("TbkqqTaokeItem")->where(array("id"=>$id))->getField("no");
                $no1 = $item['no'];
                $iid = $item['iid'];
                if($no1)$data['no'] = $no1;
                else $data['no'] = $no;
                $data['status'] = '1';
                $item_model->where(array("id"=>$id))->save($data);

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
}