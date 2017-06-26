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
class DwzController extends HomebaseController {

	//首页
	public function index() {

		$id = $_GET['id'];
		if($id != ''){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
			$itemurl = M("TbkqqTaokeItemurls")->where(array("id"=>$id))->find();
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

		$id = $_GET['id'];
		$uid = $_GET['uid'];
		$host = $_SERVER['HTTP_HOST'];
        $baseurl = "http://" . $host ."/?id=";
        $quanurl = "http://" . $host . "/?a=quan&id=";
		if(is_numeric($id)){

            if($this->is_weixin()){
                $this->assign("url",$quanurl . $id . "&uid=" . $uid);
                $this->display(":dwz_quan_wx");
            }
            else {
                if($_GET['uid'] != ""){
					if(is_numeric($uid)){
						$itemurl = M("TbkqqTaokeItemurls")->where(array("id"=>$_GET['uid']))->find();
						if($itemurl){

							$this->assign("url",$quanurl . $id);
							$this->assign("shorturl",$baseurl . $_GET['uid']);
							$this->assign("iid",$itemurl['iid']);
							$this->display(":dwz_quan_wx1");
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

	public function img(){
		$id = $_GET['id'];
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
		$item = $item_model->where("id=$id")->find();
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

	public function dtkimg(){
		$id = $_GET['id'];
		$taoke_model = M('CaijiqqItems','cmf_','DB_DATAOKE');
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
		$no = $_GET['no'];
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
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


