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
/**
 * 首页
 */
class CuntaoController extends HomebaseController {
	
    //首页
	public function index(){

			$appname = C("SITE_APPNAME");

			$dataoke_model = M('CunItems');


			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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

			array_push($where_ands,"cun='1'");

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 20;

			import('Page');


			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));

			$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			$this->display("qlist_ctgy");


	}

	public function search(){

		$appname = C("SITE_APPNAME");

		$dataoke_model = M('CunItems');


		$where_ands = array("1=1");
		$fields=array(

			'kw'  => array("field"=>"title","operator"=>"like"),
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
		}else{
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

		$count = $dataoke_model->where($where)->count();

		$pagesize = 100;

		import('Page');


		$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
		$PageParam = C("VAR_PAGE");
		$page = new \Page($count,$pagesize);
		$page->setLinkWraper("li");
		$page->__set("PageParam", $PageParam);
		$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));

		$items = $dataoke_model->where($where)->order("source,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();


		$this->assign("formget",$_GET);
		$content['items']=$items;
		$content['page']=$page->show('default');
		$content['count']=$count;
		$this->assign("lists",$content);
		$this->display("qlist");
	}

	public function qlist_ctgy(){
		$appname = C("SITE_APPNAME");


		$dataoke_model = M('CunItems');


			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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

			array_push($where_ands,"cun='1'");

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 60;

				import('Page');


				$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));

				$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			$this->display();


	}

	public function weixin(){
		$this->display();
	}
	public function login_reg(){
		$this->display();
	}
	public function login(){
		$this->display();
	}

	public function top_tui(){
		$this->display();
	}

	public function Video(){
		$this->display();
	}

	public function qlist() {
		$appname = C("SITE_APPNAME");

		$dataoke_model = M('CunItems');


			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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

			array_push($where_ands,"isnull(cun)");

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 100;

				import('Page');


				$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));

				$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			$this->display();


    }



	public function gettpl(){
		$id = $_GET['id'];
		$dataoke_model = M('CunItems');
		$item = $dataoke_model->where(array("id"=>$id))->find();
		if($item){
			echo "<img src=\"http://cunimg.2690.cn/qr/" .$item['num_iid'] .".jpg\">";
		}
		//$data = get_url_data($item['quan_link']);
//		$item['quan_link'] = "http://uland.taobao.com/coupon/edetail?activityId=" .$data['activity_id'] ."&pid=" . $site['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=1";
//		$this->assign("item",$item);
//		$this->display("item");


	}


}


