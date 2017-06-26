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
class AdminReportController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}

	public function work(){
		$where_ands=array("1=1");
		$fields=array(
			'startdate'=> array("field"=>"odate","operator"=>">="),
			'enddate'=> array("field"=>"odate","operator"=>"<="),
            'type' => array("field"=>"type","operator"=>"="),
            'userid' => array("field"=>"userid","operator"=>"="),
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
			$where= join(" and ", $where_ands);
			$reports=M("AmazonOrder")
				->field('odate,userid,count(userid) as count')
				->where($where)
				->group("odate,userid")
				->select();
			$this->assign("reports",$reports);
			$this->assign("formget",$_GET);

		}
		$this->display();
	}

	public function card(){
		$_GET['status']=$_REQUEST["status"];
		if($_REQUEST["status"] == "all") $where_ands=array("1=1");
		else $where_ands=empty($_REQUEST["status"])?array("status='0'"):array("status='" .  $_REQUEST['status'] . "'");
		$fields=array(
			'startdate'=> array("field"=>"imptime","operator"=>">="),
			'enddate'=> array("field"=>"imptime","operator"=>"<="),
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
		$reports=M("AmazonCard")
			->field('amount,count(amount) as count')
			->where($where)
			->group("amount")
			->select();
		$this->assign("reports",$reports);
		$this->assign("formget",$_GET);
		$this->display();
	}

	public function task(){
		$where_ands=array("1=1");
		$fields=array(
			'startdate'=> array("field"=>"pdate","operator"=>">="),
			'enddate'=> array("field"=>"pdate","operator"=>"<="),
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
		$reports=M("AmazonProductTask")
			->field('pid,product,sum(tasknum) as tasknum')
			->where($where)
			->group("pid")
			->select();
		$products = M("AmazonProduct")->select();
		foreach($products as $product){
			$producta[$product['id']]=$product['amount'];
		}
		$this->assign("reports",$reports);
		$this->assign("product",$producta);
		$this->assign("formget",$_GET);
		$this->display();
	}

	public function cardlog(){
		$fields=array(
			'startdate'=> array("field"=>"imptime","operator"=>">="),
			'enddate'=> array("field"=>"imptime","operator"=>"<="),
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
		$reports=M("AmazonCard")
			->field('imptime,amount,count(amount) as count')
			->where($where)
			->group("imptime desc,amount")
			->select();
		$this->assign("reports",$reports);
		$this->assign("formget",$_GET);
		$this->display();
	}

	public function card_uselog(){
		$fields=array(
			'startdate'=> array("field"=>"imptime","operator"=>">="),
			'enddate'=> array("field"=>"imptime","operator"=>"<="),
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
		$reports=M("AmazonCard")
			->field('udate,amount,count(amount) as count')
			->where($where)
			->group("udate desc,amount")
			->select();
		$this->assign("reports",$reports);
		$this->assign("formget",$_GET);
		$this->display();
	}
}