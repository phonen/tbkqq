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
class AdminSellerController extends AdminbaseController {

	function _initialize() {
		//parent::_initialize();

	}
	function index(){
		$where_ands = array();
		$fields=array(
			'orderid'=> array("field"=>"orderid","operator"=>"like"),
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
		//$where= join(" and ", $where_ands);
		$where = $where_ands[0];
		if($where != ""){
			$order=M("AmazonOrder")
				->where($where)
				->find();
			$orders_assign = array();

			$data = $order;
			$data['province'] = M("AmazonAccount")->where(array("id"=>$order['a_id']))->getField("province");
			$orders_assign[]=$data;

			$products = M("AmazonProduct")->select();
			foreach($products as $product){
				$producta[$product['id']]=$product['product'];
			}
		}


		$this->assign("orders",$orders_assign);

		$this->assign("formget",$_GET);
		$this->assign("product",$producta);
		$this->display();
	}


	
}