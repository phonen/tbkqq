<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
/**
 * 文章内页
 */
namespace Tbkqq\Controller;
use Common\Controller\HomebaseController;
class ArticleController extends HomebaseController {
    //文章内页
    public function index() {
		$wx = $_GET['wx'];
		$proxy_model = M("TbkqqProxy");
		$proxys = $proxy_model->where(array('wxstatus1'=>'1','sendwx1'=>$wx))->select();
		$maxno = M("TbkqqTaokeItem")->max('no');
		$result = array();
		foreach($proxys as $proxy) {
			if($proxy['wxno']+1>$maxno) $data['wxno'] = 1;
			else $data['wxno'] = $proxy['wxno'] +1;

			$item = M("TbkqqTaokeItem")->where(array("no"=>$data['wxno'],"status"=>"1"))->find();
			$shorturl = "";

					$proxyid = substr($proxy['proxy'],8);
					$itemurl = M("TbkqqTaokeItemurl")->where(array("iid"=>$item['iid'],"proxyid"=>$proxyid))->find();
					$shorturl = $itemurl['shorturl'];
			$result['no'] = $data['wxno'];
			$result['group']= $proxy['wxgroup'];
			$result['memo'] = "下单链接：" . $itemurl['shorturl'] . "\n" .$item['memo'];
$result_all[] = $result;

			$proxy_model->where(array('id'=>$proxy['id']))->save($data);
		}
		if(empty($result_all)) echo "";
		else echo json_encode($result_all);
    }

	public function keyset(){
		$proxywx = $_GET['wx'];
		$wxstatus1 = $_GET['op'];
		$wxgroup = $_GET['group'];
		$result = M("TbkqqProxy")->where(array('proxywx'=>$proxywx,'wxgroup'=>$wxgroup))->save(array('wxstatus1'=>$wxstatus1));
		if($result == false) echo "oo,错了";
		else if($result==0) echo "oo，没有权限";
		else if($result == 1) {
			if($wxstatus1 == '1') echo "启动成功";
			if($wxstatus1 == '0') echo "停止成功";
		}
		else echo "不知道";
	}
}
