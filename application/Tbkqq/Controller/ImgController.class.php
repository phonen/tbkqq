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
/**
 * 首页
 */
class ImgController extends HomebaseController {

	//首页
	public function index() {

		$id = $_GET['id'];
		if($id != ''){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
			$itemurl = M("TbkqqTaokeItemurl")->where(array("id"=>$id))->find();
			if($itemurl){
				$url = $itemurl['shorturl'];
				$this->assign("url",$url);

				if($this->is_weixin()){

					$item = $item_model->where(array("iid"=>$itemurl['iid']))->find();
					if($item){
						$this->assign("item",$item);

					}
					$this->display(":dwz_index_wx");
				}
				else {
					$this->display(":dwz_index");
				}


			}
			else {
				$itemurl = M("TbkqqTaokeItemurlHistory")->where(array("id"=>$id))->find();
				if($itemurl){
					$url = $itemurl['shorturl'];
					$this->assign("url",$url);

					if($this->is_weixin()){
						$item = $item_model->where(array("iid"=>$itemurl['iid']))->find();
						if($item){
							$this->assign("item",$item);

						}
						$this->display(":dwz_index_wx");
					}
					else {
						$this->display(":dwz_index");
					}


				}
			}
		}
	}

	public function quan0(){
		$id = $_GET['id'];
		$baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?a=quan&id=";
		$dwz = M("TbkqqDwz")->where("id=$id")->find();
		if($dwz){
			$this->assign("url",$dwz['url']);
			if($this->is_weixin()){
				/*
				if($_GET['uid'] != ""){
					$itemurl = M("TbkqqTaokeItemurl")->where(array("id"=>$_GET['uid']))->find();
					if($itemurl){
						$this->assign("shorturl",$baseurl . $id);
						$this->assign("iid",$itemurl['iid']);
						$this->display(":dwz_quan_wx1");
					}
				}
				*/
				$this->display(":dwz_quan_wx");
			}
			else{
				header("location:" . $dwz['url']);
			}
		}

	}

	public function quan(){
		$baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?id=";
		$quanurl = "http://dwz." . C("BASE_DOMAIN") . "/?a=quan&id=";
		$id = $_GET['id'];
		$uid = $_GET['uid'];
		if(is_numeric($id)){

            if($this->is_weixin()){
                $this->assign("url",$quanurl . $id . "&uid=" . $uid);
                $this->display(":dwz_quan_wx");
            }
            else {
                if($_GET['uid'] != ""){
					if(is_numeric($uid)){
						$itemurl = M("TbkqqTaokeItemurl")->where(array("id"=>$_GET['uid']))->find();
						if($itemurl){

							$this->assign("url",$quanurl . $id);
							$this->assign("shorturl",$baseurl . $_GET['uid']);
							$this->assign("iid",$itemurl['iid']);
							$this->display(":dwz_quan_wx1");
						}
						else{
							$itemurl = M("TbkqqTaokeItemurlHistory")->where(array("id"=>$_GET['uid']))->find();
							if($itemurl){

								$this->assign("url",$quanurl . $id);
								$this->assign("shorturl",$baseurl . $_GET['uid']);
								$this->assign("iid",$itemurl['iid']);
								$this->display(":dwz_quan_wx1");
							}
						}
					}

                }
                else {
                    $dwz = M("TbkqqDwz")->where("id=$id")->find();
                    if($dwz){
                        if($dwz['type'] == '1'){
                            if(sp_is_mobile()){
                                $this->assign("url",$dwz['url']);
                                $this->display(":dwz_index");
                                exit();
                            }
                        }
                        //$this->assign("url",$dwz['url']);
                        //if($this->is_weixin()){
                        //	$this->display(":dwz_quan_wx");
                        //}
                        //else{
                        header("location:" . $dwz['url']);
                        //}
                    }
                }
            }
        }
	}

	public function erwei(){
		$iid = $_GET['id'];
		$dataoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
		$item =$dataoke_model->where("iid=$iid")->find();
		if($item) {
			if($item['imgmemo'] == '')		$image = file_get_contents($item['img'] ."_310x310.jpg");  //假设当前文件夹已有图片001.jpg
			else {
				$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
				preg_match($preg, $item['imgmemo'], $imgArr);
				$imgurl = $imgArr[1];
				$image = file_get_contents($imgurl);
			}
			header('Content-type: image/jpg');
			echo $image;
		}
	}

	public function qrcode($level=3,$size=4){
		$iid = $_GET['id'];
		$dataoke_model = M('CunItems');
		$item =$dataoke_model->where("num_iid=$iid")->find();
		if($item) {
			Vendor('phpqrcode.phpqrcode');

			$errorCorrectionLevel =intval($level) ;//容错级别
			$matrixPointSize = intval($size);//生成图片大小
//生成二维码图片
			//echo $_SERVER['REQUEST_URI'];
			$object = new \QRcode();
//			$object->png($item['quan_link'],false, $errorCorrectionLevel, $matrixPointSize, 2);
			$object->jpg($item['click_url'], "./$iid.jpg", $level, $size);

			$qr_source = imagecreatefromjpeg("./$iid.jpg");
			$qr_size = getimagesize("./$iid.jpg");
			$target_img = imagecreatetruecolor(400,800);
			$white = imagecolorallocate($target_img,0xFF,0xFF,0xFF);
			$black = imagecolorallocate($target_img,0x00,0x00,0x00);
			imagefill($target_img, 0, 0, $white);

			$img = file_get_contents($item['pic_url']. "_400x400.jpg");
			$img_source = imagecreatefromstring($img);
			$img_size = getimagesizefromstring($img);

			imagecopy ($target_img,$img_source,0,0,0,0,$img_size[0],$img_size[1]);

			$str = $item['dtitle'] . "\n【原 价】 " . $item['price'] . "元 【券后价】 " . $item['counpon_price'] . "元\n 请使用手机村淘扫描下面二维码领券购买";
			imagettftext($target_img,15,0,2,420,$black,"./simkai.ttf",$str);
			imagecopy ($target_img,$qr_source,(400-$qr_size[0])/2,500,0,0,$qr_size[0],$qr_size[1]);
			imagejpeg($target_img);
		}

	}

	public function dtkimg(){
		$id = $_GET['id'];
		$taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
		$item = $taoke_model->where("id=$id")->find();
		if($item) {
			if($item['imgmemo'] == '')		$image = file_get_contents($item['img'] ."_290x290.jpg");  //假设当前文件夹已有图片001.jpg
			else {
				$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
				preg_match($preg, $item['imgmemo'], $imgArr);
				$imgurl = $imgArr[1];
				$image = file_get_contents($imgurl);
			}
			header('Content-type: image/jpg');
			echo $image;
		}
	}
	public function img_by_no(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$no = $_GET['no'];
		$item = $item_model->where(array("no"=>$no,"status"=>'1'))->find();
		if($item) {
			if($item['imgmemo'] == '')		$image = file_get_contents($item['img'] ."_290x290.jpg");  //假设当前文件夹已有图片001.jpg
			else {
				$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
				preg_match($preg, $item['imgmemo'], $imgArr);
				$imgurl = $imgArr[1];
				$image = file_get_contents($imgurl);
			}
			header('Content-type: image/jpg');
			echo $image;
		}
	}

	protected function is_weixin(){
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			return true;
		}
		return false;
	}

	protected function code62($x){
		$show='';
		while($x>0){
			$s=$x % 62;
			if ($s>35){
				$s=chr($s+61);
			}elseif($s>9&&$s<=35){
				$s=chr($s+55);
			}
			$show.=$s;
			$x=floor($x/62);
		}
		return $show;
	}
	protected function shorturl($url){
		$url=crc32($url);
		$result=sprintf("%u",$url);
		return code62($result);
	}

}


