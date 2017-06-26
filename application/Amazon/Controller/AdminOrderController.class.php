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
class AdminOrderController extends AdminbaseController {
	protected $order_model;
	function _initialize() {
		parent::_initialize();
		$this->order_model = D("AmazonOrder");
	}
	function shuadan_index(){
		$where = "(status = '0' or status = '1') and type='b' and owner='other'";

		$orders=$this->order_model
			->where($where)->order("rand()")
			->select();
		$products = M("AmazonProduct")->select();
		foreach($products as $product){
			$producta[$product['id']]=$product['product'];
		}
		$this->assign("orders",$orders);
		$this->assign("product",$producta);
		$this->display();
	}

	function shuadancw_index(){
		$where = "(status = '0' or status = '1') and (type = 'c' or type = 'w') and owner='other'";

		$orders=$this->order_model
			->where($where)->order("rand()")
			->select();
		$products = M("AmazonProduct")->select();
		foreach($products as $product){
			$producta[$product['id']]=$product['product'];
		}
		$this->assign("orders",$orders);
		$this->assign("product",$producta);
		$this->display();
	}

	function shuadan_indexehome(){
		$where = "(status = '0' or status = '1') and type='b' and owner='ehome'";

		$orders=$this->order_model
			->where($where)->order("rand()")
			->select();
		$products = M("AmazonProduct")->select();
		foreach($products as $product){
			$producta[$product['id']]=$product['product'];
		}
		$this->assign("orders",$orders);
		$this->assign("product",$producta);
		$this->display();
	}

	function shuadancw_indexehome(){
		$where = "(status = '0' or status = '1') and (type = 'c' or type = 'w') and owner='ehome'";

		$orders=$this->order_model
			->where($where)->order("rand()")
			->select();
		$products = M("AmazonProduct")->select();
		foreach($products as $product){
			$producta[$product['id']]=$product['product'];
		}
		$this->assign("orders",$orders);
		$this->assign("product",$producta);
		$this->display();
	}

	function index(){
		$act = $_POST['act'];
		$_GET['status']=$_REQUEST["status"];
		if($_REQUEST["status"] == "all") $where_ands=array("1=1");
		else $where_ands=empty($_REQUEST["status"])?array("status='0'"):array("status='" .  $_REQUEST['status'] . "'");
		$username = $_REQUEST["username"];
		if($username != ""){
			$account = M("AmazonAccount")->where("username = '" . $username . "'")->find();
			if($account){
				array_push($where_ands,"a_id=" .$account['id']);
			}
		}

        $productname = $_REQUEST["product"];
        if($productname != ""){
            $product = M("AmazonProduct")->where("product = '" . $productname . "'")->find();
            if($product){
                array_push($where_ands,"p_id=" .$product['id']);
            }
        }
		//array_push($where_ands,"owner='other'");
		$fields=array(
			'odate'=> array("field"=>"odate","operator"=>"="),
			'cardno'=> array("field"=>"cardno","operator"=>"like"),
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
		$where= join(" and ", $where_ands);
		if($act == "export"){
			$str = "ID,日期,IP,帐号,密码,州,产品,券,订单号,刷单人,刷单时间\n";
			$orders=$this->order_model
				->where($where)
				->select();
			foreach($orders as $order){
				$account = M("AmazonAccount")->where(array("id"=>$order['a_id']))->find();
				$product = M("AmazonProduct")->where(array("id"=>$order['p_id']))->find();
				$str .= $order['id'] . "," . $order['odate'] ."," .$order['lastip'] . "," . $account['username'] . "," . $account['password'] . "," . $order['province'] ."," . $product['product'] . "," . $order['cardno'] . "," .  $order['orderid'] . "," . $order['userid'] . "," . $order['otime'] . "\n";
			}

		$str = iconv('utf-8','gb2312',$str);

			$fileName = date('Ymd').'.csv';
			export_csv($fileName,$str);
			exit;
		}

		$count=$this->order_model
			->where($where)
			->count();

		$page = $this->page($count, 20);
		$orders=$this->order_model
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		$orders_assign = array();
		foreach($orders as $order){
			$data = $order;
			$data['province'] = M("AmazonAccount")->where(array("id"=>$order['a_id']))->getField("province");
			$orders_assign[]=$data;
		}
		$products = M("AmazonProduct")->select();
		foreach($products as $product){
			$producta[$product['id']]=$product['product'];
		}
		$this->assign("orders",$orders_assign);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->assign("product",$producta);
		$this->display();
	}

	function indexehome(){
		$act = $_POST['act'];
		$_GET['status']=$_REQUEST["status"];
		if($_REQUEST["status"] == "all") $where_ands=array("1=1");
		else $where_ands=empty($_REQUEST["status"])?array("status='0'"):array("status='" .  $_REQUEST['status'] . "'");
		$username = $_REQUEST["username"];
		if($username != ""){
			$account = M("AmazonAccount")->where("username = '" . $username . "'")->find();
			if($account){
				array_push($where_ands,"a_id=" .$account['id']);
			}
		}

		array_push($where_ands,"owner='ehome'");
		$fields=array(
			'odate'=> array("field"=>"odate","operator"=>"="),
			'cardno'=> array("field"=>"cardno","operator"=>"like"),
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
		$where= join(" and ", $where_ands);
		if($act == "export"){
			$str = "ID,日期,IP,帐号,密码,州,产品,券,订单号,刷单人,刷单时间\n";
			$orders=$this->order_model
				->where($where)
				->select();
			foreach($orders as $order){
				$account = M("AmazonAccount")->where(array("id"=>$order['a_id']))->find();
				$product = M("AmazonProduct")->where(array("id"=>$order['p_id']))->find();
				$str .= $order['id'] . "," . $order['odate'] ."," .$order['lastip'] . "," . $account['username'] . "," . $account['password'] . "," . $order['province'] ."," . $product['product'] . "," . $order['cardno'] . "," .  $order['orderid'] . "," . $order['userid'] . "," . $order['otime'] . "\n";
			}

			$str = iconv('utf-8','gb2312',$str);

			$fileName = date('Ymd').'.csv';
			export_csv($fileName,$str);
			exit;
		}

		$count=$this->order_model
			->where($where)
			->count();

		$page = $this->page($count, 20);
		$orders=$this->order_model
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		$orders_assign = array();
		foreach($orders as $order){
			$data = $order;
			$data['province'] = M("AmazonAccount")->where(array("id"=>$order['a_id']))->getField("province");
			$orders_assign[]=$data;
		}
		$products = M("AmazonProduct")->select();
		foreach($products as $product){
			$producta[$product['id']]=$product['product'];
		}
		$this->assign("orders",$orders_assign);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->assign("product",$producta);
		$this->display();
	}

	public function create_export(){
		if(isset($_POST['ids'])) {
			$ids = join(",", $_POST['ids']);
			$str = "ID,帐号ID,产品ID,日期,帐号,密码,产品,产品单价,券面额,券,地址\n";
			$orders=$this->order_model
				->where("id in ($ids)")
				->select();
			foreach($orders as $order){
				$account = M("AmazonAccount")->where(array("id"=>$order['a_id']))->find();
				$product = M("AmazonProduct")->where(array("id"=>$order['p_id']))->find();
				$str .= $order['id'] . "," . $order['a_id'] . "," . $order['p_id'] ."," . $order['odate'] . "," . $account['username'] . "," . $account['password'] . "," . $product['product'] ."," . $product['amount'] . "," . $order['c_amount'] . "," . $order['cardno']  . "\n";
			}

			$str = iconv('utf-8','gb2312',$str);

			$fileName = date('Ymd').'.csv';
			export_csv($fileName,$str);
			exit;
		}
	}

	public function create(){
		$orders=$this->order_model
			->where(array("status"=>"-2"))
			->select();

		foreach($orders as $order){
			$account = M("AmazonAccount")->where(array("id"=>$order['a_id']))->find();
			$product = M("AmazonProduct")->where(array("id"=>$order['p_id']))->find();
			$order['username'] = $account['username'];
			$order['password'] = $account['password'];
			$order['product'] = $product['product'];
			$order['p_amount'] = $product['amount'];

			$ordera[] =$order;
		}

		$this->assign("orders",$ordera);
		$this->display();
	}

	public function create_post(){
			$model = D("AmazonOrder");
			$ids = $_POST['cardno'];
		$address = $_POST['address'];
			foreach ($ids as $id => $r) {
				$data['cardno'] = $r;
				$data['address'] = $address['$id'];
				$model->where(array("id" => $id))->save($data);
			}
			$this->success("信息更新成功！");
	}

	public function status_post(){
		if(isset($_POST['ids'])){
			$ids = $_POST['ids'];
			foreach ($ids as $id) {
				$data = array();
				$p_id = M("AmazonOrder")->where(array("id"=>$id))->getField("p_id");
				$upstatus = M("AmazonProduct")->where(array("id"=>$p_id))->getField('ups');
				$ups = M("AmazonUps")->where(array("status"=>'0'))->find();
				if($upstatus == '1')$data['address'] = $ups['address'];
				$data['ups'] = $ups['ups'];
				$data['status'] = '0';
				$this->order_model->where(array("id" => $id))->save($data);
				M("AmazonUps")->where(array("id"=>$ups['id']))->save(array("status"=>"1"));
			}

				$this->success("正式生成成功！");
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
			$filename = './Uploads' . $info['savepath'].$info['savename'];
			$file = fopen($filename,'r');
			while ($data = fgetcsv($file)) {
				$order = array();
				$order['id'] = $data[0];
				$order['cardno'] = $data[1];
				$order['address'] = $data[2];
				$result = M("AmazonOrder")->save($order);
			}
			fclose($file);
			$this->success("更新成功！");
		}
	}


	public function change(){

		if(IS_POST){
			$id=intval(I("post.id"));
			$username = I("post.username");
			$wheres[] = " username like '" . $username . "'";
		}
else {
	$id=  intval(I("get.id"));
}

		$order =$this->order_model->where(array("id"=>$id))->find();
		$product = M("AmazonProduct")->where(array("id"=>$order['p_id']))->find();
		$account = M("AmazonAccount")->where(array("id"=>$order['a_id']))->find();

			$orders=$this->order_model->where(array("p_id"=>$order['p_id']))->select();
			foreach($orders as $data){
				$aids[] = $data['a_id'];
			}
			$a_ids=join(",", $aids);
			$wheres[] = " amount>=" . $product['amount'] . "-" . $order['c_amount'] . " ";
			$wheres[] = " id not in ($a_ids) ";
			$wheres[] = " status='1' ";
			$where = join("and",$wheres);
			$accounts= M("AmazonAccount")->where($where)->order("amount desc")->select();
			$this->assign("accounts",$accounts);
			$this->assign("account",$account);
			$this->assign("oid",$id);
			$this->display();
	}

	public function change_post(){
		$order = I("post.order");
		$status = $_POST['status'];
		$a_id = $this->order_model->where(array("id"=>$order['id']))->getField("a_id");
		$memo = M("AmazonAccount")->where(array("id"=>$a_id))->getField("memo");
		$lasttime = date("Y-m-d H:i:s",time());
		$data = array("memo"=>$memo . " | orderid:" . $order['id'] . $order['memo'],"status"=>$status,"lasttime"=>$lasttime);
		M("AmazonAccount")->where(array("id"=>$a_id))->save($data);
		if ($this->order_model->save($order) !== false) {
			$this->success("更改帐号成功！",U("AdminOrder/index"));
		} else {
			$this->error("更改帐号失败！");
		}
	}



	public function shuadan_post(){
		if (IS_POST) {
			$i =0;
			$act = $_POST['act'];
			if($act == 'unnormal') {
				$ids = I("post.ids");
				$lastip=$_POST['lastip'];
				$reason = $_POST['reason'];
				foreach($ids as $id){
					$order['id'] = $id;
					$order['status'] = '-1';
					$order['lastip'] = $lastip;
					$memo = $this->order_model->where(array("id"=>$id))->getField("memo");
					$order['memo'] = $memo . " | " . $reason;
					$result=$this->order_model->save($order);
					if($result!==false) {
						$i++;
					}
				}
				if($i == count($ids)) $this->success("标记异常成功！");
				else $this->error("标记异常失败！");
				exit();
			}

			$orders=I("post.order");
			$account=I("post.account");

			$account['lasttime'] = date("Y-m-d H:i:s",time());
			foreach($orders as $id=>$order){
				$order['id'] = $id;
				$order['status'] = $account['status'];
				if($order['status'] == '') $order['status'] = '2';
				$order['lastip'] = $account['lastip'];
				$order['userid'] = $_SESSION['name'];
				$order['otime'] = date("Y-m-d H:m:s",time());
				$account_model = M("AmazonAccount");
				$a_id = $this->order_model->where("id=$id")->getField("a_id");
				$result = $this->order_model->save($order);
				if($result!==false){
					$i++;
				}
			}
			if($i == count($orders)){

				if(M('AmazonAccount')->save($account)){
					$this->success("刷单保存成功！");
				}
				else $this->error("刷单保存失败！");
			}
			else $this->error("刷单保存失败！");
		}
	}
	public function shuadan(){
		$id=  intval(I("get.id"));
		$a_id = $this->order_model->where("id=$id")->getField('a_id');
		$account = M("AmazonAccount")->where("id=$a_id")->find();
//		$orders = $this->order_model->join('cmf_amazon_product ON cmf_amazon_order.p_id = cmf_amazon_product.id')->where("cmf_amazon_order.a_id=$a_id and (cmf_amazon_order.status='0' or (cmf_amazon_order.status='1' and cmf_amazon_order.userid='" . $_SESSION['name'] . "'))")->select();
		$orders = $this->order_model->where("a_id=$a_id and (status='0' or (status='1' and userid='" . $_SESSION['name'] ."'))")->select();
		foreach($orders as $order){
			$assign_order = array();
			$assign_order['id'] = $order['id'];
			$assign_order['address'] = $order['address'];
			$assign_order['province'] = $order['province'];
			$product = M("AmazonProduct")->where(array("id"=>$order['p_id']))->find();
			$assign_order['product'] = $product['product'];
			$assign_order['shop'] = $product['shop'];
			$assign_order['asin'] = $product['asin'];
			$assign_order['keyword'] = $product['keyword'];
			$assign_order['amount'] = $product['amount'];
			$this->order_model->where(array("id"=>$order['id']))->save(array("status"=>"1","userid"=>$_SESSION['name']));
			$assign_order['cardno'] = $order['cardno'];
			$assign_order['type'] = $order['type'];
			$assign_orders[] = $assign_order;
		}

		$this->assign("orders",$assign_orders);
		$this->assign("account",$account);
		$this->display();

	}

	public function shuadan_buy(){
		$id=  intval(I("get.id"));
		$a_id = $this->order_model->where("id=$id")->getField('a_id');
		$account = M("AmazonAccount")->where("id=$a_id")->find();
//		$orders = $this->order_model->join('cmf_amazon_product ON cmf_amazon_order.p_id = cmf_amazon_product.id')->where("cmf_amazon_order.a_id=$a_id and (cmf_amazon_order.status='0' or (cmf_amazon_order.status='1' and cmf_amazon_order.userid='" . $_SESSION['name'] . "'))")->select();
		$orders = $this->order_model->where("a_id=$a_id and (status='0' or (status='1' and userid='" . $_SESSION['name'] ."'))")->select();
		foreach($orders as $order){
			$assign_order = array();
			$assign_order['id'] = $order['id'];
			$assign_order['address'] = $order['address'];
			$assign_order['province'] = $order['province'];
			$product = M("AmazonProduct")->where(array("id"=>$order['p_id']))->find();
			$assign_order['product'] = $product['product'];
			$assign_order['shop'] = $product['shop'];
			$assign_order['asin'] = $product['asin'];
			$assign_order['keyword'] = $product['keyword'];
			$assign_order['amount'] = $product['amount'];
			$this->order_model->where(array("id"=>$order['id']))->save(array("status"=>"1","userid"=>$_SESSION['name']));
			$assign_order['cardno'] = $order['cardno'];
			$assign_orders[] = $assign_order;
		}

		$this->assign("orders",$assign_orders);
		$this->assign("account",$account);
		$this->display();

	}

	function add(){
		$products = M("AmazonProduct")->select();
		$this->assign("products",$products);
		$this->display();
	}

	function add_post(){
		if (IS_POST) {
			$order=I("post.order");
			$orders=$this->order_model->where(array("p_id"=>$order['p_id']))->select();
			foreach($orders as $data){
				$aids[] = $data['a_id'];
			}
			$a_ids=join(",", $aids);

			$wheres[] = " id not in ($a_ids) ";
			$wheres[] = " status='1' ";
			$where = join("and",$wheres);
			$account= M("AmazonAccount")->where($where)->order("rand()")->find();
			$order['a_id'] = $account['id'];
			$result=$this->order_model->add($order);
			if ($result) {
				$this->success("添加成功！",U("AdminOrder/index"));
			} else {
				$this->error("添加失败！");
			}

		}
	}

	public function edit(){
		$id=  intval(I("get.id"));
		$order=$this->order_model->where("id=$id")->find();
		$account = M("AmazonAccount")->where(array("id"=>$order['a_id']))->find();
		$product = M("AmazonProduct")->where(array("id"=>$order['p_id']))->find();
		$order['username'] = $account['username'];
		$order['password'] = $account['password'];
		$order['product'] = $product['product'];
		$order['astatus'] = $account['status'];
$order['province'] = $account['province'];
		$this->assign("order",$order);
		$this->display();
	}
	
	public function edit_post(){
		if (IS_POST) {
			$id=intval($_POST['order']['id']);
			$order=I("post.order");
			$result=$this->order_model->save($order);
			if ($result!==false) {
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		}
	}
	
	function delete(){
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			$data['status']=0;
			if ($this->order_model->where("id=$id")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			if ($this->order_model->where("id in ($ids)")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}

	function move(){
		if(IS_POST){
			if(isset($_GET['ids']) && isset($_POST['term_id'])){
				$tids=$_GET['ids'];
				if ( $this->term_relationships_model->where("tid in ($tids)")->save($_POST) !== false) {
					$this->success("移动成功！");
				} else {
					$this->error("移动失败！");
				}
			}
		}else{
			$parentid = intval(I("get.parent"));
			
			$tree = new \Tree();
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$terms = $this->terms_model->order(array("path"=>"asc"))->select();
			$new_terms=array();
			foreach ($terms as $r) {
				$r['id']=$r['term_id'];
				$r['parentid']=$r['parent'];
				$new_terms[] = $r;
			}
			$tree->init($new_terms);
			$tree_tpl="<option value='\$id'>\$spacer\$name</option>";
			$tree=$tree->get_tree(0,$tree_tpl);
			 
			$this->assign("terms_tree",$tree);
			$this->display();
		}
	}
	
	function recyclebin(){
		$this->_lists(0);
		$this->_getTree();
		$this->display();
	}
	
	function clean(){
		if(isset($_POST['ids'])){
			$ids = implode(",", $_POST['ids']);
			$tids= implode(",", array_keys($_POST['ids']));
			$data=array("post_status"=>"0");
			$status=$this->term_relationships_model->where("tid in ($tids)")->delete();
			if($status!==false){
				foreach ($_POST['ids'] as $post_id){
					$post_id=intval($post_id);
					$count=$this->term_relationships_model->where(array("object_id"=>$post_id))->count();
					if(empty($count)){
						$status=$this->posts_model->where(array("id"=>$post_id))->delete();
					}
				}
				
			}
			
			if ($status!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = intval(I("get.id"));
				$tid = intval(I("get.tid"));
				$status=$this->term_relationships_model->where("tid = $tid")->delete();
				if($status!==false){
					$count=$this->term_relationships_model->where(array("object_id"=>$id))->count();
					if(empty($count)){
						$status=$this->posts_model->where("id=$id")->delete();
					}
					
				}
				if ($status!==false) {
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
			$data=array("tid"=>$id,"status"=>"1");
			if ($this->term_relationships_model->save($data)) {
				$this->success("还原成功！");
			} else {
				$this->error("还原失败！");
			}
		}
	}
	
}