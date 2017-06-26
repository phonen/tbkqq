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
class AdminUtilsController extends AdminbaseController {

	function _initialize() {
		parent::_initialize();
	}
	function listfile(){
		$appname = C("SITE_APPNAME");
		$dir = "/data/wwwroot/" . $appname. "/thinkcmfx/Uploads/";  //要获取的目录
		echo "********** 获取目录下所有文件和文件夹 ***********<hr/>";
		//先判断指定的路径是不是一个文件夹
		if (is_dir($dir)){
			if ($dh = opendir($dir)){
				$files=array();
				while (($file = readdir($dh))!= false){
					//文件名的全路径 包含文件名
					$filePath = $dir.$file;
					if(is_file($filePath)){
						$filename [] = $filePath;
						$fmt[] = filemtime($filePath);
						$filesize[] =  filesize($filePath);
					}
					//获取文件修改时间

				}
				array_multisort($filename, $fmt,$filesize);
				for($i=0;$i<count($filename);$i++){
					echo "<span style='color:#666'>(".date("Y-m-d H:i:s",$fmt[$i]).")</span> ". "<span style='color:#666'>(".$filesize[$i].")</span> " . $filename[$i]."<br/>";
				}
				closedir($dh);
			}
		}

	}

	public function dwz() {
		if (IS_POST) {
			$url=trim($_POST['url']);
			$_GET['url'] = $url;
			$dwz = convert_dwz($url);

		}
		$this->assign("dwz",$dwz);
		$this->assign("formget",$_GET);
		$this->display();
	}

}