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
class AdminAccountController extends AdminbaseController {
	protected $account_model;
	function _initialize() {
		parent::_initialize();
		$this->account_model =D("AmazonAccount");
	}
	function index(){
		$act = $_POST['act'];
		$this->assign("status",$_REQUEST["status"]);
		$_GET['status']=$_REQUEST["status"];
		$where_ands=empty($_REQUEST["status"])?array("status='1'"):array("status='" .  $_REQUEST['status'] . "'");
		$fields=array(
			'startdate'=> array("field"=>"lasttime","operator"=>">="),
			'enddate'=> array("field"=>"lasttime","operator"=>"<="),
				'username'  => array("field"=>"username","operator"=>"like"),
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

		if($act == "export"){
			$accounts=$this->account_model
				->field("id,lastip,username,password,province,address,amount,lasttime")
				->where($where)
				->select();
			$this->accounts_export($accounts);
			exit();
		}
		$count=$this->account_model->where($where)->count();
		$page = $this->page($count, 20);
		
		$accounts=$this->account_model->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();

		$this->assign("Page", $page->show('Admin'));
		$this->assign("formget",$_GET);
		$this->assign("accounts",$accounts);
		$this->display();
	}

	//导出数据方法
	protected function accounts_export($accounts=array())
	{
		//print_r($goods_list);exit;
		$data = $accounts;
//		$data = array();

		//print_r($goods_list);
		//print_r($data);exit;

		foreach ($data as $field=>$v){
			if($field == 'id'){
				$headArr[]='ID';
			}

			if($field == 'lastip'){
				$headArr[]='IP';
			}

			if($field == 'username'){
				$headArr[]='帐号';
			}

			if($field == 'password'){
				$headArr[]='密码';
			}

			if($field == 'province'){
				$headArr[]='州';
			}

			if($field == 'address'){
				$headArr[]='地址';
			}

			if($field == 'amount'){
				$headArr[]='余额';
			}
			if($field == 'lasttime'){
				$headArr[]='操作时间';
			}

		}

		$filename="accounts";

		$this->getExcel($filename,$headArr,$data);
	}


	private  function getExcel($fileName,$headArr,$data){
		//导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
		import("Org.Util.PHPExcel");
		import("Org.Util.PHPExcel.Writer.Excel5");
		import("Org.Util.PHPExcel.IOFactory.php");

		$date = date("Y_m_d",time());
		$fileName .= "_{$date}.xls";

		//创建PHPExcel对象，注意，不能少了\
		$objPHPExcel = new \PHPExcel();
		$objProps = $objPHPExcel->getProperties();

		//设置表头
		$key = ord("A");
		//print_r($headArr);exit;
		foreach($headArr as $v){
			$colum = chr($key);
			$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
			$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
			$key += 1;
		}

		$column = 2;
		$objActSheet = $objPHPExcel->getActiveSheet();

		//print_r($data);exit;
		foreach($data as $key => $rows){ //行写入
			$span = ord("A");
			foreach($rows as $keyName=>$value){// 列写入
				$j = chr($span);
				$objActSheet->setCellValue($j.$column, $value);
				$span++;
			}
			$column++;
		}

		$fileName = iconv("utf-8", "gb2312", $fileName);

		//重命名表
		//$objPHPExcel->getActiveSheet()->setTitle('test');
		//设置活动单指数到第一个表,所以Excel打开这是第一个表
		$objPHPExcel->setActiveSheetIndex(0);
		ob_end_clean();//清除缓冲区,避免乱码
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); //文件通过浏览器下载
		exit;
	}

	public function address(){
		$this->assign("status",$_REQUEST["status"]);
		$_GET['status']=$_REQUEST["status"];
		$where_ands=empty($_REQUEST["status"])?array("status='0'"):array("status='" .  $_REQUEST['status'] . "'");
		$fields=array(
			'start_time'=> array("field"=>"post_date","operator"=>">"),
			'end_time'  => array("field"=>"post_date","operator"=>"<"),
			'keyword'  => array("field"=>"post_title","operator"=>"like"),
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
$address_model = M("AmazonAddress");
		$count=$address_model->where($where)->count();
		$page = $this->page($count, 20);

		$addresss=$address_model->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();

		$this->assign("Page", $page->show('Admin'));
		$this->assign("formget",$_GET);
		$this->assign("addresss",$addresss);
		$this->display();
	}
	function add(){
		$id= intval(I("get.id"));
		$account = M("AmazonAddress")->where("id=$id")->find();
		$result = M("AmazonAddress")->save(array("id"=>$id,"status"=>"1"));
//		$account=$this->account_model->where("id=$id")->find();
		$account['address'] = $account['address1'] ."|". $account['address2'] ."|". $account['city'] ."|". $account['province'] . "|" . $account['code'] ."|". $account['phone'];

		$this->assign("account",$account);
         $this->display();
	}
	
	function add_post(){
		if (IS_POST) {
			$account=I("post.account");
			$addid = $_POST['addid'];
			$result = M("AmazonAddress")->save(array("id"=>$addid,"status"=>"2"));
			if($result !== false){
				$result=$this->account_model->add($account);

				if ($result !== false) {
					//
					$this->success("注册成功！" ,U("AdminAccount/address"));
				} else {
					$result = M("AmazonAddress")->save(array("id"=>$addid,"status"=>"-1"));
					$this->error("注册失败！");
				}

			}
			else $this->error("注册失败！");
		}

	}
	
	public function edit(){
		$id= intval(I("get.id"));
		$account=$this->account_model->where("id=$id")->find();
		$this->assign("account",$account);
		$this->display();
	}
	
	public function edit_post(){
		if (IS_POST) {
			$account=I("post.account");
			$account['lasttime'] = date("Y-m-d H:i:s",time());
			$result=$this->account_model->save($account);
			if ($result !== false) {
				//
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		}
	}
	
	function delete(){
		if(isset($_POST['ids'])){
			$ids = implode(",", $_POST['ids']);
			$data=array("status"=>"-9");
			if ($this->account_model->where("id in ($ids)")->save($data)) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = intval(I("get.id"));
				$data=array("id"=>$id,"status"=>"-9");
				if ($this->account_model->save($data)) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}
	
	function restore(){
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			$data=array("id"=>$id,"post_status"=>"1");
			if ($this->account_model->save($data)) {
				$this->success("还原成功！");
			} else {
				$this->error("还原失败！");
			}
		}
	}
	
	function clean(){
		
		if(isset($_POST['ids'])){
			$ids = implode(",", $_POST['ids']);
			if ($this->account_model->where("id in ($ids)")->delete()!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			if ($this->posts_model->delete($id)!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
	
	public function changestatus_comm(){
        if(isset($_GET['id'])){
            $id = intval(I("get.id"));
            $data=array("id"=>$id,"status"=>"11");
            if ($this->account_model->save($data)) {
                $this->success("设置无法评论成功！");
            } else {
                $this->error("设置无法评论失败！");
            }
        }

    }
	
}