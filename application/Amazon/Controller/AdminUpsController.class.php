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
class AdminUpsController extends AdminbaseController {
	protected $card_model;
	function _initialize() {
		parent::_initialize();
		$this->ups_model = D("AmazonUps");
	}
	function index(){

		$_GET['status']=$_REQUEST["status"];
		
		if($_REQUEST["status"] == "all") $where_ands=array("1=1");
		else $where_ands=empty($_REQUEST["status"])?array("1=1"):array("status='" .  $_REQUEST['status'] . "'");
		$fields=array(
			'ups'=> array("field"=>"ups","operator"=>"like"),
			'address'=> array("field"=>"address","operator"=>"like"),
		);

		if(IS_POST){
			foreach ($fields as $param =>$val){
				if (isset($_POST[$param]) && !empty($_POST[$param])) {
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_POST[$param];
					$_GET[$param]=$get;
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}
		else{
			foreach ($fields as $param =>$val){
				if (isset($_GET[$param]) && !empty($_GET[$param])) {
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_GET[$param];
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}
		$where= join(" and ", $where_ands);
		$count=$this->ups_model
			->where($where)
			->count();
		$page = $this->page($count, 20);
		$upss=$this->ups_model
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		$this->assign("upss",$upss);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->display();
	}

	function edit(){
		$id = intval(I("get.id"));
		$ups = M('AmazonUps')->where(array("id" => $id))->find();

		$this->assign("ups",$ups);
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			$ups=I("post.ups");
				if ($this->ups_model->save($ups)!==false) {

					$this->success("修改成功！");
				} else {
					$this->error("修改失败！");
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