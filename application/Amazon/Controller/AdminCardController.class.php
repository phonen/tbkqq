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
class AdminCardController extends AdminbaseController {
	protected $card_model;
	function _initialize() {
		parent::_initialize();
		$this->card_model = D("AmazonCard");
	}
	function index(){

		$_GET['status']=$_REQUEST["status"];
		$_GET['num']=$_REQUEST["num"];
		if($_REQUEST["status"] == "all") $where_ands=array("1=1");
		else $where_ands=empty($_REQUEST["status"])?array("status='0'"):array("status='" .  $_REQUEST['status'] . "'");

		$limit=empty($_REQUEST["num"])?200: $_REQUEST['num'];
		$fields=array(
			'amount'=> array("field"=>"amount","operator"=>"="),
			'cardno'=> array("field"=>"cardno","operator"=>"like"),
		);

		$amounts=M("AmazonCard")->group("amount")->field("amount")->select();

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
		$cards=$this->card_model
			->where($where)
			->limit($limit)
			->select();
		$this->assign("cards",$cards);
		$this->assign("amounts",$amounts);
		$this->assign("formget",$_GET);
		$this->display();
	}

	function edit(){
		$id = intval(I("get.id"));
		$card = M('AmazonCard')->where(array("id" => $id))->find();

		$this->assign("card",$card);
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			$card=I("post.card");
				if ($this->card_model->save($card)!==false) {

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
			if ($this->card_model->where("id in ($ids)")->save($data)) {
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
		$source = array("a"=>"美国购买","b"=>"虚拟卡","c"=>"电子卡");

		$info   =   $upload->uploadOne($_FILES['csvfile']);
		$imptime = date("Y-m-d H:i:s",time());
		if(!$info) {// 上传错误提示错误信息
			$this->error($upload->getError());    }
		else{// 上传成功 获取上传文件信息
			$file = fopen("./Uploads/".$info['savepath'].$info['savename'],'r');
			while ($data = fgetcsv($file)) {
				$card = array();
				$card['cdate'] = $data[0];
				$card['source'] = $source[$data[1]];
				$card['cardno'] = $data[2];
				$card['amount'] = $data[3];
				$card['status'] = $data[4];
				$card['imptime'] = $imptime;
				$result = M("AmazonCard")->add($card);
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
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			if (M("AmazonCard")->where("id=$id")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			if (M("AmazonCard")->where("id in ($ids)")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}

	function logindex(){

		$_GET['status']=$_REQUEST["status"];
		$_GET['num']=$_REQUEST["num"];
		if($_REQUEST["status"] == "all") $where_ands=array("1=1");
		else $where_ands=empty($_REQUEST["status"])?array("status='0'"):array("status='" .  $_REQUEST['status'] . "'");

		$limit=empty($_REQUEST["num"])?200: $_REQUEST['num'];
		$fields=array(
			'amount'=> array("field"=>"amount","operator"=>"="),
			'cardno'=> array("field"=>"cardno","operator"=>"like"),
		);

		$amounts=M("AmazonCard")->group("amount")->field("amount")->select();

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
		$cards=$this->card_model
			->where($where)
			->limit($limit)
			->select();
		$this->assign("cards",$cards);
		$this->assign("amounts",$amounts);
		$this->assign("formget",$_GET);
		$this->display();
	}


}