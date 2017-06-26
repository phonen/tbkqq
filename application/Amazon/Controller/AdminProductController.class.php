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
class AdminProductController extends AdminbaseController {
	protected $product_model;
	function _initialize() {
		parent::_initialize();
		$this->product_model = D("AmazonProduct");
	}
	function index(){
		$where_ands=array("1=1");
		$fields=array(
			'product'=> array("field"=>"product","operator"=>"like"),
			'asin'=> array("field"=>"asin","operator"=>"like"),
			'shop'=> array("field"=>"shop","operator"=>"like"),
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

		$count=M('AmazonProduct')->where($where)
			->count();

		$page = $this->page($count, 20);
		$products=M('AmazonProduct')->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		$this->assign("products",$products);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->display();
	}

	function indexehome(){
		$where_ands=array("owner='ehome'");
		$fields=array(
			'product'=> array("field"=>"product","operator"=>"like"),
			'asin'=> array("field"=>"asin","operator"=>"like"),
			'shop'=> array("field"=>"shop","operator"=>"like"),
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

		$count=M('AmazonProduct')->where($where)
			->count();

		$page = $this->page($count, 20);
		$products=M('AmazonProduct')->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		$this->assign("products",$products);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->display();
	}

	function add(){
		$this->display();
	}

	function add_post(){
		if(IS_POST){
			$product=I("post.product");
			if ($this->product_model->create($product)){
				if ($this->product_model->add()!==false) {
					$this->success("添加成功", U("AdminProduct/index" . $product['owner']));
				} else {
					$this->error("添加失败");
				}
			} else {
				$this->error($this->product_model->getError());
			}
		}
	}
	function edit(){
		$id = intval(I("get.id"));
		$data = M('AmazonProduct')->where(array("id" => $id))->find();

		$this->assign("product",$data);
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			$id=intval($_POST['product']['id']);
			$product=I("post.product");
			$result=M('AmazonProduct')->save($product);
			if ($result!==false) {
					$this->success("修改成功！");
				} else {
					$this->error("修改失败！");
			}
		}
	}

	public function task(){
		$where_ands=array("1=1");
		$fields=array(
			'pdate'=> array("field"=>"pdate","operator"=>"="),
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
		$count=M('AmazonProductTask')
			->where($where)
			->count();

		$page = $this->page($count, 50);
		$tasks=M('AmazonProductTask')
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

		$products = M("AmazonProduct")->select();
		foreach($products as $product){
			$producta[$product['id']]=$product['product'];
		}
		$this->assign("product",$producta);
		$this->assign("types",array('b'=>'刷单','c'=>'add cart','w'=>'add wishlist'));
		$this->assign("tasks",$tasks);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->display();
	}

	public function taskehome(){
		$where_ands=array("owner='ehome'");
		$fields=array(
			'pdate'=> array("field"=>"pdate","operator"=>"="),
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
		$count=M('AmazonProductTask')
			->where($where)
			->count();

		$page = $this->page($count, 50);
		$tasks=M('AmazonProductTask')
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

		$products = M("AmazonProduct")->select();
		foreach($products as $product){
			$producta[$product['id']]=$product['product'];
		}
		$this->assign("product",$producta);
		$this->assign("types",array('b'=>'刷单','c'=>'add cart','w'=>'add wishlist'));
		$this->assign("tasks",$tasks);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->display('');
	}

	public function tasknum_post(){
		$model = D("AmazonProductTask");
		$ids = $_POST['tasknums'];
		foreach ($ids as $id => $r) {
			$data['tasknum'] = $r;
			$model->where(array("id" => $id))->save($data);
		}
		$this->success("任务数更新成功！");
	}

	public function task_delete(){
		$task_model = M("AmazonProductTask");
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			$data['status']=0;
			if ($task_model->where("id=$id")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			if ($task_model->where("id in ($ids)")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}

	public function product_account(){

		$shops = M("AmazonProduct")->field("shop")->group("shop")->select();
		$result = array();
		foreach($shops as $shop){
			$products = M("AmazonProduct")->where(array("shop"=>$shop['shop']))->select();
			$p_ids = array();
			foreach($products as $product){
				$p_ids[] = $product['id'];
			}
			$pids = join(",",$p_ids);

			$accounts = M("AmazonOrder")->where("p_id in ($pids)")->field("a_id")->select();
			$a_ids = array();
			foreach ($accounts as $account) $a_ids[]=$account['a_id'];
			foreach($p_ids as $p_id){
				$data = array();
				$data['id'] = $p_id;
				$data['account'] = join(",",$a_ids);
				$result[] = $data;
			}
		}
		foreach($result as $data){
			$product_account = M("AmazonProductAccount")->where(array("id"=>$data['id']))->find();
			if($product_account) M("AmazonProductAccount")->save($data);
			else M("AmazonProductAccount")->add($data);
		}
		/*
		$products = M("AmazonProduct")->select();
		foreach ($products as $product){
			$data = array();
			$accounts = M("AmazonOrder")->where(array("p_id"=>$product['id']))->field("a_id")->select();
			$a_ids = array();
			foreach ($accounts as $account) $a_ids[]=$account['a_id'];
			$data['id'] = $product['id'];
			$data['account'] = join(",",$a_ids);
			$product_account = M("AmazonProductAccount")->where(array("id"=>$product['id']))->find();
			if($product_account) M("AmazonProductAccount")->save($data);
			else M("AmazonProductAccount")->add($data);
		}
		*/
		$this->success("成功");
	}

	public function create_cworder(){
		set_time_limit(0);
		$products = M("AmazonProduct")->select();
		foreach ($products as $product){
			$data = array();
			$accounts = M("AmazonOrder")->where(array("p_id"=>$product['id']))->field("a_id")->select();
			$a_ids = array();
			foreach ($accounts as $account) $a_ids[]=$account['a_id'];
			$data['id'] = $product['id'];
			$data['account'] = join(",",$a_ids);
			$product_account = M("AmazonProductAccount")->where(array("id"=>$product['id']))->find();
			if($product_account) M("AmazonProductAccount")->save($data);
			else M("AmazonProductAccount")->add($data);
		}


		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);



			$ctasks = M("AmazonProductTask")->where("id in ($ids) and type='c'")->order('tasknum desc')->select();
			if($ctasks) {
				$account_model = M("AmazonAccount");
				$order_model = M("AmazonOrder");
				foreach ($ctasks as $task) {
					$product = $this->product_model->where(array("id"=>$task['pid']))->find();


					$product_account = M("AmazonProductAccount")->where(array("id"=>$task['pid']))->field("account")->find();

					if($product_account['account'] == "") $accounts = $account_model->where("status='1' and type='cw'")->order("rand()")->select();
					else $accounts = $account_model->where("status='1' and type='cw' and id not in (" . $product_account['account'] . ")")->order("rand()")->select();

					if($accounts){
						$i = 0;
						foreach($accounts as $account){
							//$order = $order_model->where("a_id='" . $account['id'] . "' and otime>DATE_ADD(NOW(), INTERVAL -7 DAY)")->find();
							//if($order) continue;
							if(empty($count[$account['id']])) $count[$account['id']] =0;
							if($i>=$task['tasknum']) break;
							if($count[$account['id']]<3){
								$order = array();
								$order['a_id'] = $account['id'];
								$order['p_id'] = $task['pid'];
								$order['type'] = 'c';
								$order['owner'] = $product['owner'];
								$count[$account['id']] ++;
								$i++;
								$order['status'] = '-2';
								$order['odate'] = $task['pdate'];

								$paccount[$task['pid']][] = $account['id'];

								if($order_model->add($order) !== false) $result += 1;
							}
						}
					}
				}

				foreach($paccount as $pid => $aids){
					$pa[$pid] = join(",",$aids);
				}
			}

			$wtasks = M("AmazonProductTask")->where("id in ($ids) and type='w'")->order('tasknum desc')->select();
			if($wtasks) {
				$account_model = M("AmazonAccount");
				$order_model = M("AmazonOrder");
				foreach ($wtasks as $task) {
					$product = $this->product_model->where(array("id"=>$task['pid']))->find();


					$product_account = M("AmazonProductAccount")->where(array("id"=>$task['pid']))->field("account")->find();


					if($product_account['account'] == "") $accounts = $account_model->where("status='1' and type='cw'")->order("rand()")->select();
					else {
						if($pa[$task['pid']] != "")
							$accounts = $account_model->where("status='1' and type='cw' and id not in (" . $product_account['account'] . "," .$pa[$task['pid']] . ")")->order("rand()")->select();
						else
							$accounts = $account_model->where("status='1' and type='cw' and id not in (" . $product_account['account'] . ")")->order("rand()")->select();
					}

					if($accounts){
						$i = 0;
						foreach($accounts as $account){
							//$order = $order_model->where("a_id='" . $account['id'] . "' and otime>DATE_ADD(NOW(), INTERVAL -7 DAY)")->find();
							//if($order) continue;
							if(empty($count[$account['id']])) $count[$account['id']] =0;
							if($i>=$task['tasknum']) break;
							if($count[$account['id']]<3){
								$order = array();
								$order['a_id'] = $account['id'];
								$order['p_id'] = $task['pid'];
								$order['type'] = 'w';
								$order['owner'] = $product['owner'];
								$count[$account['id']] ++;
								$i++;
								$order['status'] = '-2';
								$order['odate'] = $task['pdate'];

								if($order_model->add($order) !== false) $result += 1;
							}
						}
					}
				}
			}

			$this->success("生成成功$result个！");
		}
	}

	function create_order(){
		set_time_limit(0);
		$shops = M("AmazonProduct")->field("shop")->group("shop")->select();
		$result = array();
		foreach($shops as $shop){
			$products = M("AmazonProduct")->where(array("shop"=>$shop['shop']))->select();
			$p_ids = array();
			foreach($products as $product){
				$p_ids[] = $product['id'];
			}
			$pids = join(",",$p_ids);

			$accounts = M("AmazonOrder")->where("p_id in ($pids)")->field("a_id")->select();
			$a_ids = array();
			foreach ($accounts as $account) $a_ids[]=$account['a_id'];
			foreach($p_ids as $p_id){
				$data = array();
				$data['id'] = $p_id;
				$data['account'] = join(",",$a_ids);
				$result[] = $data;
			}
		}
		foreach($result as $data){
			$product_account = M("AmazonProductAccount")->where(array("id"=>$data['id']))->find();
			if($product_account) M("AmazonProductAccount")->save($data);
			else M("AmazonProductAccount")->add($data);
		}


		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);

			$tasks = M("AmazonProductTask")->where("id in ($ids) and type='b'")->select();
			if($tasks){
				$count=array();
				$amounts = array();
				$account_model = M("AmazonAccount");
				$order_model = M("AmazonOrder");

				foreach ($tasks as $task) {
					$product = $this->product_model->where(array("id"=>$task['pid']))->find();
					$little = (($product['amount']/5)-intval($product['amount']/5))*5;

					$product_account = M("AmazonProductAccount")->where(array("id"=>$product['id']))->field("account")->find();

					if($product_account['account'] == "") $accounts = $account_model->where("status='1' and type='b'")->order("rand()")->select();
					else $accounts = $account_model->where("status='1'  and type='b' and id not in (" . $product_account['account'] . ")")->order("rand()")->select();

					if($accounts){
						$i = 0;
						foreach($accounts as $account){
							$order = $order_model->where("a_id='" . $account['id'] . "' and otime>DATE_ADD(NOW(), INTERVAL -7 DAY)")->find();
							if($order) continue;
							if(empty($count[$account['id']])) $count[$account['id']] =0;
							if($i>=$task['tasknum']) break;
							if($count[$account['id']]<1){
								$order = array();
								$order['a_id'] = $account['id'];
								$order['p_id'] = $product['id'];
								if($account['amount']>=$product['amount']) {
									$c_amount = 0;
								}
								else if($account['amount']>=$little){
									$c_amount = $product['amount'] - $little;
								}
								else {
									$c_amount = $product['amount'] -$little +5;
								}
								$count[$account['id']] ++;
								$i++;
								$order['status'] = '-2';
								$order['type'] = 'b';
								$order['owner'] = $product['owner'];
								$order['odate'] = $task['pdate'];
								$order['c_amount'] = $c_amount;

								if($order_model->add($order) !== false) $result += 1;
							}
						}
					}
				}
			}
			$ctasks = M("AmazonProductTask")->where("id in ($ids) and type='c'")->order('tasknum desc')->select();
			if($ctasks) {
				$account_model = M("AmazonAccount");
				$order_model = M("AmazonOrder");
				foreach ($ctasks as $task) {
					$product = $this->product_model->where(array("id"=>$task['pid']))->find();


					$product_account = M("AmazonProductAccount")->where(array("id"=>$task['pid']))->field("account")->find();

					if($product_account['account'] == "") $accounts = $account_model->where("status='1' and type='cw'")->order("rand()")->select();
					else $accounts = $account_model->where("status='1' and type='cw' and id not in (" . $product_account['account'] . ")")->order("rand()")->select();

					if($accounts){
						$i = 0;
						foreach($accounts as $account){
							//$order = $order_model->where("a_id='" . $account['id'] . "' and otime>DATE_ADD(NOW(), INTERVAL -7 DAY)")->find();
							//if($order) continue;
							if(empty($count[$account['id']])) $count[$account['id']] =0;
							if($i>=$task['tasknum']) break;
							if($count[$account['id']]<3){
								$order = array();
								$order['a_id'] = $account['id'];
								$order['p_id'] = $task['pid'];
								$order['type'] = 'c';
								$order['owner'] = $product['owner'];
								$count[$account['id']] ++;
								$i++;
								$order['status'] = '-2';
								$order['odate'] = $task['pdate'];

								$paccount[$task['pid']][] = $account['id'];

								if($order_model->add($order) !== false) $result += 1;
							}
						}
					}
				}

				foreach($paccount as $pid => $aids){
					$pa[$pid] = join(",",$aids);
				}
			}

			$wtasks = M("AmazonProductTask")->where("id in ($ids) and type='w'")->order('tasknum desc')->select();
			if($wtasks) {
				$account_model = M("AmazonAccount");
				$order_model = M("AmazonOrder");
				foreach ($ctasks as $task) {
					$product = $this->product_model->where(array("id"=>$task['pid']))->find();


					$product_account = M("AmazonProductAccount")->where(array("id"=>$task['pid']))->field("account")->find();


					if($product_account['account'] == "") $accounts = $account_model->where("status='1' and type='cw'")->order("rand()")->select();
					else {
						if($pa[$task['pid']] != "")
							$accounts = $account_model->where("status='1' and type='cw' and id not in (" . $product_account['account'] . "," .$pa[$task['pid']] . ")")->order("rand()")->select();
						else
							$accounts = $account_model->where("status='1' and type='cw' and id not in (" . $product_account['account'] . ")")->order("rand()")->select();
					}

					if($accounts){
						$i = 0;
						foreach($accounts as $account){
							//$order = $order_model->where("a_id='" . $account['id'] . "' and otime>DATE_ADD(NOW(), INTERVAL -7 DAY)")->find();
							//if($order) continue;
							if(empty($count[$account['id']])) $count[$account['id']] =0;
							if($i>=$task['tasknum']) break;
							if($count[$account['id']]<3){
								$order = array();
								$order['a_id'] = $account['id'];
								$order['p_id'] = $task['pid'];
								$order['type'] = 'w';
								$order['owner'] = $product['owner'];
								$count[$account['id']] ++;
								$i++;
								$order['status'] = '-2';
								$order['odate'] = $task['pdate'];

								if($order_model->add($order) !== false) $result += 1;
							}
						}
					}
				}
			}

			$this->success("生成成功$result个！");
		}
	}

//创建任务
	function create(){
		set_time_limit(0);
		$products = M("AmazonProduct")->select();
		foreach ($products as $product){
			$data = array();
			$accounts = M("AmazonOrder")->where(array("p_id"=>$product['id']))->field("a_id")->select();
			$a_ids = array();
			foreach ($accounts as $account) $a_ids[]=$account['a_id'];
			$data['id'] = $product['id'];
			$data['account'] = join(",",$a_ids);
			$product_account = M("AmazonProductAccount")->where(array("id"=>$product['id']))->find();
			if($product_account) M("AmazonProductAccount")->save($data);
			else M("AmazonProductAccount")->add($data);
		}

		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			$tasks = M("AmazonProductTask")->where("id in ($ids)")->select();

			$count=array();
			$amounts = array();
			$account_model = M("AmazonAccount");
			$order_model = M("AmazonOrder");

			foreach ($tasks as $task) {
				$product = $this->product_model->where(array("id"=>$task['pid']))->find();
				$little = (($product['amount']/5)-intval($product['amount']/5))*5;
	//			$subQuery = $order_model->where(array("p_id"=>$product['id']))->field("a_id")->buildSql();
//				$subQuery = M("AmazonProductAccount")->where(array("id"=>$product['id']))->field("account")->buildSql();
				$product_account = M("AmazonProductAccount")->where(array("id"=>$product['id']))->field("account")->find();
			//	$subQuery1 = $order_model->field("a_id,max(otime) ot")->group("a_id")->buildSql();
			//	$subQuery2 = M()->table($subQuery1.' a')->where("a.ot>DATE_ADD(NOW(), INTERVAL -7 DAY)")->field("a.a_id")->buildSql();
			//	$accounts = $account_model->where("id not in " . $subQuery . " and id not in " .$subQuery2)->order("rand()")->select();
				if($product_account['account'] == "") $accounts = $account_model->where("status='1'")->order("rand()")->select();
				else $accounts = $account_model->where("status='1' and id not in (" . $product_account['account'] . ")")->order("rand()")->select();

				if($accounts){
					$i = 0;
					foreach($accounts as $account){
						$order = $order_model->where("a_id='" . $account['id'] . "' and otime>DATE_ADD(NOW(), INTERVAL -7 DAY)")->find();
						if($order) continue;
						if(empty($count[$account['id']])) $count[$account['id']] =0;
						if($i>=$task['tasknum']) break;
						if($count[$account['id']]<1){
							$order = array();
							$order['a_id'] = $account['id'];
							$order['p_id'] = $product['id'];
							if($account['amount']>=$product['amount']) {
								$c_amount = 0;
							}
							else if($account['amount']>=$little){
								$c_amount = $product['amount'] - $little;
							}
							else {
								$c_amount = $product['amount'] -$little +5;
							}
							$count[$account['id']] ++;
							$i++;
							$order['status'] = '-2';
							$order['odate'] = $task['pdate'];
							$order['c_amount'] = $c_amount;
							$order['type'] = 'b';
							$order['owner'] = $product['owner'];

							if($order_model->add($order) !== false) $result += 1;
						}
					}
				}
			}
			/*
			foreach($tasks as $task){
				$product = $this->product_model->where(array("id"=>$task['pid']))->find();
				$little = (($product['amount']/10)-intval($product['amount']/10))*10;
				$subQuery = $order_model->where(array("p_id"=>$product['id']))->field("a_id")->buildSql();
				$accounts = $account_model->where("id not in " . $subQuery)->order("rand()")->select();
//			$accounts = M()->query("select * from cmf_amazon_account where id not in (select a_id from cmf_amazon_order where p_id = " .$product['id'] . ") and status='1' order by rand()");

//				$accounts = $account_model->where($wheres)->order("rand()")->select();
				if($accounts){
					$i = 0;
					foreach($accounts as $account){
						if(empty($count[$account['id']])) $count[$account['id']] =0;
						if($i>=$task['tasknum']) break;
						if($count[$account['id']] == 0) $amounts[$account['id']] = floatval($account['amount']);
//					if($count[$account['id']]<3 && (($amounts[$account['id']]>$little && $amounts[$account['id']]<10) || $account['amount'] == 0)){
//						if($count[$account['id']]<3 && (($amounts[$account['id']]>=$little) || $account['amount'] == 0)){
						if($count[$account['id']]<3 && $amounts[$account['id']]>=$little){
							$order = array();
							$order['a_id'] = $account['id'];
							$order['p_id'] = $product['id'];

							if($amounts[$account['id']]>=$product['amount']) {
								$amounts[$account['id']] -= $product['amount'];

//						$card = $card_model->where("amount>$amount-10")->order("amount")->find();
								$c_amount = $product['amount'] - $product['amount'];
							}
							else {
								$amounts[$account['id']] -= $little;

//						$card = $card_model->where("amount>$amount-10")->order("amount")->find();
								$c_amount = $product['amount'] - $little;
							}

							$count[$account['id']] ++;
							$i++;
//					$order['c_id'] = $card['id'];

							$order['status'] = '-2';
							$order['odate'] = $task['pdate'];
							$order['c_amount'] = $c_amount;

							if($order_model->add($order) !== false) $result += 1;
//							dump($count);
						}
					}
					if($i<$task['tasknum']){
						$accounts = $account_model->where("id not in " . $subQuery)->order("rand()")->select();
//						$accounts = M()->query("select id from cmf_amazon_account where id not in (select a_id from cmf_amazon_order where p_id = " .$product['id'] . ") and status='1' order by rand()");

//						$accounts = $account_model->where($wheres)->order("rand()")->select();
						foreach($accounts as $account){
							if(empty($count[$account['id']])) $count[$account['id']] =0;
							if($i>=$task['tasknum']) break;
							if($count[$account['id']] == 0) $amounts[$account['id']] = $account['amount'];
//					if($count[$account['id']]<3 && (($amounts[$account['id']]>$little && $amounts[$account['id']]<10) || $account['amount'] == 0)){
//						if($count[$account['id']]<3 && (($amounts[$account['id']]>=$little) || $account['amount'] == 0)){
							if($count[$account['id']]<3){
								$order = array();
								$order['a_id'] = $account['id'];
								$order['p_id'] = $product['id'];

								if($amounts[$account['id']]>=$product['amount']) {
									$amounts[$account['id']] -= $product['amount'];

//						$card = $card_model->where("amount>$amount-10")->order("amount")->find();
									$c_amount = $product['amount'] - $product['amount'];
								}
								else if($amounts[$account['id']]>=$little){
									$amounts[$account['id']] -= $little;

//						$card = $card_model->where("amount>$amount-10")->order("amount")->find();
									$c_amount = $product['amount'] - $little;
								}
								else {
//						$card = $card_model->where("amount>$amount")->order("amount")->find();
									$c_amount = $product['amount'] -$little +10;
									$amounts[$account['id']] += $c_amount - $product['amount'];
								}
								$count[$account['id']] ++;
								$i++;
//					$order['c_id'] = $card['id'];

								$order['status'] = '-2';
								$order['odate'] = $task['pdate'];
								$order['c_amount'] = $c_amount;

								if($order_model->add($order) !== false) $result += 1;
//							dump($count);
							}
						}
					}
//				if($result == $task['tasknum']) {
//					$this->success("生成成功$result个！");
//				}
//				else $this->errors( "生成失败$tasknum-$result！");
				}
			}
			*/
			$this->success("生成成功$result个！");
		}
	}

	function create0(){
		set_time_limit(0);


		if(isset($_POST['ids'])) {
			$ids = join(",", $_POST['ids']);
			$tasks = M("AmazonProductTask")->where("id in ($ids)")->select();
			foreach ($tasks as $task) {
				$p_ids[] = $task['pid'];
			}
			$pids = join(",", $p_ids);
			$accounts0 = M("AmazonOrder")->where("p_id in ($pids)")->field("a_id")->select();
			$a_ids = array();
			foreach ($accounts0 as $account) $a_ids[] = $account['a_id'];
			foreach ($p_ids as $p_id) {
				$data = array();
				$data['id'] = $p_id;
				$data['account'] = join(",", $a_ids);
				$result[] = $data;
			}
			foreach ($result as $data) {
				$product_account = M("AmazonProductAccount")->where(array("id" => $data['id']))->find();
				if ($product_account) M("AmazonProductAccount")->save($data);
				else M("AmazonProductAccount")->add($data);
			}
$result =0;
			$count = array();
			$amounts = array();
			$account_model = M("AmazonAccount");
			$order_model = M("AmazonOrder");

			foreach ($tasks as $task) {
				$product = $this->product_model->where(array("id" => $task['pid']))->find();
				$little = (($product['amount'] / 5) - intval($product['amount'] / 5)) * 5;
				//			$subQuery = $order_model->where(array("p_id"=>$product['id']))->field("a_id")->buildSql();
//				$subQuery = M("AmazonProductAccount")->where(array("id"=>$product['id']))->field("account")->buildSql();
				$product_account = M("AmazonProductAccount")->where(array("id" => $product['id']))->field("account")->find();
				//	$subQuery1 = $order_model->field("a_id,max(otime) ot")->group("a_id")->buildSql();
				//	$subQuery2 = M()->table($subQuery1.' a')->where("a.ot>DATE_ADD(NOW(), INTERVAL -7 DAY)")->field("a.a_id")->buildSql();
				//	$accounts = $account_model->where("id not in " . $subQuery . " and id not in " .$subQuery2)->order("rand()")->select();
				if ($product_account['account'] == "") $accounts = $account_model->where("status='1'")->order("rand()")->select();
				else $accounts = $account_model->where("status='1' and id not in (" . $product_account['account'] . ")")->order("rand()")->select();

				if ($accounts) {
					$i = 0;
					foreach ($accounts as $account) {
						$order = $order_model->where("a_id='" . $account['id'] . "' and otime>DATE_ADD(NOW(), INTERVAL -7 DAY)")->find();
						if ($order) continue;
						if (empty($count[$account['id']])) $count[$account['id']] = 0;
						if ($i >= $task['tasknum']) break;
						if ($count[$account['id']] < 1) {
							$order = array();
							$order['a_id'] = $account['id'];
							$order['p_id'] = $product['id'];
							if ($account['amount'] >= $product['amount']) {
								$c_amount = 0;
							} else if ($account['amount'] >= $little) {
								$c_amount = $product['amount'] - $little;
							} else {
								$c_amount = $product['amount'] - $little + 5;
							}
							$count[$account['id']]++;
							$i++;
							$order['status'] = '-2';
							$order['odate'] = $task['pdate'];
							$order['c_amount'] = $c_amount;
							$order['owner'] = $product['owner'];
							if ($order_model->add($order) !== false) $result += 1;
						}
					}
				}
			}

			$this->success("生成成功！");
		}
	}


/**
*  删除
*/
	public function delete() {
		$id = intval(I("get.id"));
		
		if ($this->product_model->delete($id)!==false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	public function task_import(){
		$upload = new \Think\Upload();// 实例化上传类
//		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		$upload->exts      =     array('csv');// 设置附件上传类型
		$upload->savePath  =   '/'; // 设置附件上传目录    // 上传单个文件
		//$source = array("a"=>"美国购买","b"=>"虚拟卡","c"=>"电子卡");

		$info   =   $upload->uploadOne($_FILES['csvfile']);
//		$imptime = date("Y-m-d H:i:s",time());
		if(!$info) {// 上传错误提示错误信息
			$this->error($upload->getError());    }
		else{// 上传成功 获取上传文件信息
			$file = fopen("./Uploads/".$info['savepath'].$info['savename'],'r');
			while ($data = fgetcsv($file)) {
				$task = array();
				$task['pdate'] = $data[0];
				$task['pid'] = $data[1];
				$task['product'] = $data[2];
				$task['tasknum'] = $data[3];
				$task['type'] = $data[4];

				$result = M("AmazonProductTask")->add($task);
			}
			fclose($file);
			$this->success("导入成功！");
		}
	}
}