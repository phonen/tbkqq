<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController;
class MpController extends HomebaseController{
	public function index() {
	    $openid = $_GET['openid'];
		$proxy = M("TbkqqProxy")->where(array("openid"=>$openid))->find();
        if($proxy){
            header("Location: http://cms.taotehui.co/index.php?r=index/wap&pid=" . $proxy['pid']);
        }
		
	}
	

}