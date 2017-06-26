<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Amazon\Controller;
use Common\Controller\AdminbaseController;
class AdminSearchController extends AdminbaseController {

	function _initialize() {
		//parent::_initialize();

	}
	function index(){
		$items = M("AmazonSearch")->select();
		$this->assign("items",$items);

		$this->display();
	}

	public function search(){
		$model = M("AmazonSearch");
		if(isset($_POST['ids'])) {
			$ids = join(",", $_POST['ids']);

			$items = $model->where("id in ($ids)")->select();
		}
		else $items = $model->select();
			foreach($items as $item){
				for($p =1;$p<100;$p++){
					$u = "https://www.amazon.com/s/ref=sr_pg_" . $p . "?page=" . $p . "&keywords=" . $item['keyword'] . "&ie=UTF8&qid=" . time();
					//echo $u;
					$str = openhttp_header($u);
					if(strpos($str,$item['asin'])) {
						$data['page'] = $p;
						$model->where(array("id"=>$item['id']))->save($data);
						break;
					}
				}

			}

		$this->success("搜索成功！");
	}


	function edit(){
		$id = intval(I("get.id"));
		$ups = M('AmazonSearch')->where(array("id" => $id))->find();

		$this->assign("ups",$ups);
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			$item=I("post.ups");
				if (M("AmazonSearch")->save($item)!==false) {

					$this->success("修改成功！");
				} else {
					$this->error("修改失败！");
				}

		}
	}

	public function add(){
		$this->display();
	}

	public function add_post(){
		if (IS_POST) {
			$item=I("post.ups");
			if (M("AmazonSearch")->add($item)!==false) {

				$this->success("添加成功！");
			} else {
				$this->error("添加失败！");
			}

		}
	}

	public function status_post(){
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			$data['status']=1;
			$data['udate'] = date("Y-m-d");
			if ($this->ups_model->where("id in ($ids)")->save($data)) {
				$this->success("标记使用成功！");
			} else {
				$this->error("标记使用失败！");
			}
		}
	}

	public function import(){
		$upload = new \Think\Upload();// 实例化上传类
//		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		$upload->exts      =     array('csv');// 设置附件上传类型
		$upload->savePath  =   '/'; // 设置附件上传目录    // 上传单个文件

		$info   =   $upload->uploadOne($_FILES['csvfile']);
		if(!$info) {// 上传错误提示错误信息
			$this->error($upload->getError());    }
		else{// 上传成功 获取上传文件信息
			$file = fopen("./Uploads/".$info['savepath'].$info['savename'],'r');
			while ($data = fgetcsv($file)) {
				$ups = array();
				$ups['ups'] = $data[0];
				$ups['address'] = $data[1];

				$ups['status'] = '0';
				if(M("AmazonUps")->where(array("ups"=>$ups['ups']))->find())$result = M("AmazonUps")->where(array("ups"=>$ups['ups']))->save($ups);
				else $result = M("AmazonUps")->add($ups);
			}
			fclose($file);
			$this->success("导入成功！");
		}
	}
	//排序
	public function listorders() {
		$status = parent::_listorders($this->terms_model);
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}
	
	/**
	 *  删除
	 */
	public function delete() {
		$id = intval(I("get.id"));
		$count = $this->terms_model->where(array("parent" => $id))->count();
		
		if ($count > 0) {
			$this->error("该菜单下还有子类，无法删除！");
		}
		
		if ($this->terms_model->delete($id)!==false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
}