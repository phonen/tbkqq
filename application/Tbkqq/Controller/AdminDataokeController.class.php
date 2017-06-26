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
class AdminDataokeController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}


	public function item(){
		$dataoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
		$class_model =  M('TbkqqClass','cmf_','DB_DATAOKE');
		$class = $class_model->select();
		foreach($class as $data){
			$cids[$data['cid']] = $data['class'];
		}
		//if($_REQUEST["cid"] == "all")
		$where_ands=array("1=1");
		$fields=array(
			'cid'=> array("field"=>"cid","operator"=>"="),
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
		$count=$dataoke_model
			->where($where)
			->count();

		$page = $this->page($count, 100);
		$items=$dataoke_model
			->where($where)->order("id desc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

		$this->assign("items",$items);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->assign("cids",$cids);
		$this->display();
	}

	public function item_add(){
		$class_model =  M('TbkqqClass','cmf_','DB_DATAOKE');
		$class = $class_model->select();
		$this->assign("class",$class);
		$this->display();
	}

	public function item_add_post(){
		if (IS_POST) {
			$item_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
			$class_model =  M('TbkqqClass','cmf_','DB_DATAOKE');
			$class = $class_model->select();
			foreach($class as $data){
				$cids[$data['cid']] = $data['class'];
			}
			$item=I("post.item");
			$item['itime'] = date("Y-m-d H:i:s",time());
			if($item_model->where(array("iid"=>$item['iid']))->find())
			{
				$result = true;
				//$result=$item_model->where(array("iid"=>$item['iid']))->save($item);
				if ($result) {
					$this->success("添加成功！");
				} else {
					$this->error("添加失败！");
				}
			}
			else {
				$result=$item_model->add($item);

				if ($result) {
					$this->success("添加成功！");
				} else {
					$this->error("添加失败！");
				}
			}

		}
	}
	public function item_tuiguang(){
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);

			$dataoke_model = M('TbkqqTuiguang','cmf_','DB_DATAOKE');
			$items = M("TbkqqTaokeItem")->where("id in ($ids)")->select();
			if($items){
				foreach ($items as $item) {
					M("TbkqqTaokeItemHistory")->add($item);
					$iid[] = $item['iid'];
				}
				$iids = join(",",$iid);
				$itemurls = M("TbkqqTaokeItemurl")->where("iid in ($iids)")->select();
				if($itemurls){
					foreach($itemurls as $itemurl){
						M("TbkqqTaokeItemurlHistory")->add($itemurl);
					}
					M("TbkqqTaokeItemurl")->where("iid in ($iids)")->delete();
				}

				if (M("TbkqqTaokeItem")->where("id in ($ids)")->delete()) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
			else $this->error("删除失败！");
		}
	}



	public function item_dsh_post(){
		if(IS_POST){
			$ids = $_POST['ids'];

			foreach($ids as $id){
				$no = M("TbkqqTaokeItem")->where(array("status"=>"1"))->max("no");
				$no = $no?$no:0;
				$no = $no+1;
				$no1 = M("TbkqqTaokeItem")->where(array("id"=>$id))->getField("no");
				if($no1)$data['no'] = $no1;
				else $data['no'] = $no;
				$data['status'] = '1';
				M("TbkqqTaokeItem")->where(array("id"=>$id))->save($data);
			}
			$this->success("正式推广成功！");
		}
	}

}