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
class AdminFanliController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}


	public function media(){
		$where_ands = array("1=1");
		$fields=array(
			'username'=> array("field"=>"username","operator"=>"="),
		);
		if(IS_POST) {
			foreach ($fields as $param => $val) {
				if (isset($_POST[$param]) && !empty($_POST[$param])) {
					$operator = $val['operator'];
					$field = $val['field'];
					$get = $_POST[$param];
					$_GET[$param] = $get;
					if ($operator == "like") {
						$get = "%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}

		}
		$where = join(" and ", $where_ands);
		$medias=M("TbkqqFanliMedia")
			->where($where)
			->select();
		$this->assign("medias",$medias);

		$this->display();
	}

	public function media_add(){
		$this->display();
	}
	public function media_add_post(){
		if(IS_POST){
			$media=I("post.media");
			$media_model = M("TbkqqFanliMedia");
			if ($media_model->create($media)){
				if ($media_model->add()!==false) {
					$this->success(L('ADD_SUCCESS'), U("AdminFanli/media"));
				} else {
					$this->error(L('ADD_FAILED'));
				}
			} else {
				$this->error($media_model->getError());
			}

		}
	}
	public function media_edit(){
		$id = intval(I("get.id"));
		$data = M('TbkqqFanliMedia')->where(array("id" => $id))->find();

		$this->assign("media",$data);
		$this->display();
	}

	public function media_edit_post(){
		if (IS_POST) {
			$id=intval($_POST['media']['id']);
			$media=I("post.media");
			$result=M('TbkqqFanliMedia')->save($media);
			if ($result!==false) {
				$this->success("修改成功！");
			} else {
				$this->error("修改失败！");
			}
		}
	}

	/**
	 *  删除
	 */
	public function media_delete() {
		$id = intval(I("get.id"));

		if (M("TbkqqFanliMedia")->delete($id)!==false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}

	public function details_clean(){
		if(IS_POST) {
			$startdate = $_POST['startdate'];
			if($startdate == "")$this->error("删除失败，请选择时间！");
			else {
				$where = "ctime>='" . $startdate . "'";
				if (M("TbkqqFanliDetails")->where($where)->delete() !== false) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}

	public function details_upload(){
		if(IS_POST) {

			header("Content-Type:text/html;charset=utf-8");
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 9145728;// 设置附件上传大小
			$upload->exts = array('xls', 'xlsx');// 设置附件上传类
			$upload->savePath = '/'; // 设置附件上传目录
			// 上传文件
			$info = $upload->uploadOne($_FILES['detail_file']);
			$filename = './Uploads' . $info['savepath'] . $info['savename'];
			$exts = $info['ext'];
			//print_r($info);exit;

			if (!$info) {// 上传错误提示错误信息
				$this->error($upload->getError());
			} else {// 上传成功
				$act = $_POST['act'];
				if ($act == "clean") {
					M("TbkqqFanliDetails")->where("1=1")->delete();
				}
				$this->details_import($filename, $exts);
				$this->success("导入成功");
			}


		}
	}

	protected function details_import($filename,$exts){
		import("Org.Util.PHPExcel");
		$PHPExcel=new \PHPExcel();
		if($exts == 'xls'){
			import("Org.Util.PHPExcel.Reader.Excel5");
			$PHPReader=new \PHPExcel_Reader_Excel5();
		}else if($exts == 'xlsx'){
			import("Org.Util.PHPExcel.Reader.Excel2007");
			$PHPReader=new \PHPExcel_Reader_Excel2007();
		}
//$ctime = M("TbkqqTaokeDetail")->max(ctime);
//		$maxctime = strtotime($ctime);
		//载入文件
		$PHPExcel=$PHPReader->load($filename);
		//获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
		$currentSheet=$PHPExcel->getSheet(0);
		//获取总列数
		$allColumn=$currentSheet->getHighestColumn();
		$allColumn++;
		//获取总行数
		$allRow=$currentSheet->getHighestRow();
		//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
		for($currentRow=2;$currentRow<=$allRow;$currentRow++) {
			$data = array();
			for ($currentColumn = 'A'; $currentColumn != $allColumn; $currentColumn++) {
				//数据坐标
				$address = $currentColumn . $currentRow;
				//读取到的数据，保存到数组$arr中
				$cell = $currentSheet->getCell($address)->getValue();

				if ($cell instanceof PHPExcel_RichText) {
					$cell = $cell->__toString();
				}

				switch ($currentColumn)
				{
					case 'A':
						$data['ctime']=$cell;
						break;
					case 'C':
						$data['goods']=$cell;
						break;
					case 'D':
						$data['gid']=$cell;
						break;
					case 'E':
						$data['wangwang']=$cell;
						break;
					case 'F':
						$data['shop']=$cell;
						break;
					case 'G':
						$data['gcount']=$cell;
						break;
					case 'H':
						$data['gamount']=$cell;
						break;
					case 'I':
						$data['ostatus']=$cell;
						break;
					case 'J':
						$data['otype']=$cell;
						break;
					case 'K':
						$data['srrate']=$cell;
						break;
					case 'L':
						$data['fcrate']=$cell;
						break;
					case 'M':
						$data['fukuan']=$cell;
						break;
					case 'N':
						$data['effect']=$cell;
						break;
					case 'O':
						$data['jiesuan']=$cell;
						break;
					case 'P':
						$data['pre_amount']=$cell;
						break;
					case 'Q':
						$data['jstime']=$cell;
						break;
					case 'R':
						$data['yjrate']=$cell;
						break;
					case 'S':
						$data['yongjin']=$cell;
						break;
					case 'T':
						$data['btrate']=$cell;
						break;
					case 'U':
						$data['butie']=$cell;
						break;
					case 'V':
						$data['bttype']=$cell;
						break;
					case 'W':
						$data['third']=$cell;
						break;
					case 'X':
						$data['pingtai']=$cell;
						break;
					case 'Y':
						$data['orderid']=$cell;
						break;
					case 'Z':
						$data['class']=$cell;
						break;
					case 'AA':
						$data['sourceid']=$cell;
						break;
					case 'AD':
						$data['adname']=$cell;
						break;
				}
			}
//			print_r($data);
			//$detail = M("TbkqqTaokeDetail")->where(array("orderid"=>$data['orderid'],"gid"=>$data['gid'],"gcount"=>$data['gcount']))->find();
			//if($detail) M("TbkqqTaokeDetail")->where(array("orderid"=>$data['orderid'],"gid"=>$data['gid'],"gcount"=>$data['gcount']))->save($data);
			M("TbkqqFanliDetails")->add($data);
		}

	}

	public function jiesuans_upload(){
		if(IS_POST) {

			header("Content-Type:text/html;charset=utf-8");
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 9145728;// 设置附件上传大小
			$upload->exts = array('xls', 'xlsx');// 设置附件上传类
			$upload->savePath = '/'; // 设置附件上传目录
			// 上传文件
			$info = $upload->uploadOne($_FILES['detail_file']);
			$filename = './Uploads' . $info['savepath'] . $info['savename'];
			$exts = $info['ext'];
			//print_r($info);exit;
			if (!$info) {// 上传错误提示错误信息
				$this->error($upload->getError());
			} else {// 上传成功
				$act = $_POST['act'];
				if ($act == "clean") {
					M("TbkqqFanliJiesuans")->where("1=1")->delete();
				}
				$this->jiesuans_import($filename, $exts);
			}
		}
	}

	protected function jiesuans_import($filename,$exts){
		import("Org.Util.PHPExcel");
		$PHPExcel=new \PHPExcel();
		if($exts == 'xls'){
			import("Org.Util.PHPExcel.Reader.Excel5");
			$PHPReader=new \PHPExcel_Reader_Excel5();
		}else if($exts == 'xlsx'){
			import("Org.Util.PHPExcel.Reader.Excel2007");
			$PHPReader=new \PHPExcel_Reader_Excel2007();
		}

		//载入文件
		$PHPExcel=$PHPReader->load($filename);
		//获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
		$currentSheet=$PHPExcel->getSheet(0);
		//获取总列数
		$allColumn=$currentSheet->getHighestColumn();
		$allColumn++;
		//获取总行数
		$allRow=$currentSheet->getHighestRow();
		//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
		for($currentRow=2;$currentRow<=$allRow;$currentRow++) {
			$data = array();
			for ($currentColumn = 'A'; $currentColumn != $allColumn; $currentColumn++) {
				//数据坐标
				$address = $currentColumn . $currentRow;
				//读取到的数据，保存到数组$arr中
				$cell = $currentSheet->getCell($address)->getValue();

				if ($cell instanceof PHPExcel_RichText) {
					$cell = $cell->__toString();
				}

				switch ($currentColumn)
				{
					case 'A':
						$data['ctime']=$cell;
						break;
					case 'C':
						$data['goods']=$cell;
						break;
					case 'D':
						$data['gid']=$cell;
						break;
					case 'E':
						$data['wangwang']=$cell;
						break;
					case 'F':
						$data['shop']=$cell;
						break;
					case 'G':
						$data['gcount']=$cell;
						break;
					case 'H':
						$data['gamount']=$cell;
						break;
					case 'I':
						$data['ostatus']=$cell;
						break;
					case 'J':
						$data['otype']=$cell;
						break;
					case 'K':
						$data['srrate']=$cell;
						break;
					case 'L':
						$data['fcrate']=$cell;
						break;
					case 'M':
						$data['fukuan']=$cell;
						break;
					case 'N':
						$data['effect']=$cell;
						break;
					case 'O':
						$data['jiesuan']=$cell;
						break;
					case 'P':
						$data['pre_amount']=$cell;
						break;
					case 'Q':
						$data['jstime']=$cell;
						break;
					case 'R':
						$data['yjrate']=$cell;
						break;
					case 'S':
						$data['yongjin']=$cell;
						break;
					case 'T':
						$data['btrate']=$cell;
						break;
					case 'U':
						$data['butie']=$cell;
						break;
					case 'V':
						$data['bttype']=$cell;
						break;
					case 'W':
						$data['third']=$cell;
						break;
					case 'X':
						$data['pingtai']=$cell;
						break;
					case 'Y':
						$data['orderid']=$cell;
						break;
					case 'Z':
						$data['class']=$cell;
						break;
					case 'AA':
						$data['sourceid']=$cell;
						break;
					case 'AD':
						$data['adname']=$cell;
						break;
				}
			}

			M("TbkqqFanliJiesuans")->add($data);
		}
		$this->success("导入成功");
	}

	function details(){
		$where_ands = array("1=1");
		$fields=array(
			'startdate'=> array("field"=>"ctime","operator"=>">="),
			'enddate'=> array("field"=>"ctime","operator"=>"<="),
			'orderid'=>array("field"=>"orderid","operator"=>"="),
		);
		if(IS_POST) {
			foreach ($fields as $param => $val) {
				if (isset($_POST[$param]) && !empty($_POST[$param])) {
					$operator = $val['operator'];
					$field = $val['field'];
					$get = $_POST[$param];
					$_GET[$param] = $get;
					if ($operator == "like") {
						$get = "%$get%";
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
		$where = join(" and ", $where_ands);
		$count=M('TbkqqFanliDetails')
			->where($where)
			->count();
		$page = $this->page($count, 20);
		$details=M("TbkqqFanliDetails")
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->order("ctime desc")
			->select();

		if(C('SITE_APPNAME') == "yhg") $liuman = 0.3;

		else $liuman = 0.89;
		$this->assign("liuman",$liuman);
		$this->assign("details",$details);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->display();
	}

	public function jiesuans(){
		$_GET['proxy']=$_REQUEST["proxy"];
		if($_REQUEST["proxy"] == ""){
			$where_ands = array("ostatus='订单结算'");
			$fields=array(
				'startdate'=> array("field"=>"jstime","operator"=>">="),
				'enddate'=> array("field"=>"jstime","operator"=>"<"),
			);
			if(IS_POST) {
				foreach ($fields as $param => $val) {
					if (isset($_POST[$param]) && !empty($_POST[$param])) {
						$operator = $val['operator'];
						$field = $val['field'];
						$get = $_POST[$param];
						$_GET[$param] = $get;
						if ($operator == "like") {
							$get = "%$get%";
						}
						array_push($where_ands, "$field $operator '$get'");
					}
				}
			}
			$where1 = join(" and ", $where_ands);
			$proxys = M("TbkqqProxy")->select();
			foreach($proxys as $proxy){
				$medias = M("TbkqqFanliMedia")->where(array("proxy"=>$proxy['proxy']))->select();
				$where_ors = array();
				foreach($medias as $media){
					array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
				}
				$where2 = "(" . join(" or ",$where_ors) . ")";
				if($medias)	{
					$where = $where1 . " and " . $where2;
					$effect = M("TbkqqFanliJiesuans")->where($where)->field("'" . $proxy['proxy'] . "' as proxy,count(orderid) as paycount,sum(effect) as pre_effect,sum(pre_amount) as pre_amount")->find();
					if($effect){
						$effect['fcrate'] = $proxy['fcrate'];
						$effects[] = $effect;
					}
				}
			}
		}
		else {
			$where_ands = array("ostatus='订单结算'");
			$fields=array(
				'startdate'=> array("field"=>"jstime","operator"=>">="),
				'enddate'=> array("field"=>"jstime","operator"=>"<"),
			);
			if(IS_POST) {
				foreach ($fields as $param => $val) {
					if (isset($_POST[$param]) && !empty($_POST[$param])) {
						$operator = $val['operator'];
						$field = $val['field'];
						$get = $_POST[$param];
						$_GET[$param] = $get;
						if ($operator == "like") {
							$get = "%$get%";
						}
						array_push($where_ands, "$field $operator '$get'");
					}
				}
			}

			$medias = M("TbkqqFanliMedia")->where(array("proxy"=>$_REQUEST["proxy"]))->select();
			$where_ors = array();
			foreach($medias as $media){
				array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
			}
			array_push($where_ands, "(" . join(" or ",$where_ors) . ")");
			$where = join(" and ", $where_ands);
			$effects = M("TbkqqFanliJiesuans")->where($where)->field(array("DATE_FORMAT(jstime,'%Y-%m-%d') edate","'" . $_REQUEST['proxy'] . "'as proxy","count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->group("edate")->order("edate desc")->select();
		}

		$proxys=M("TbkqqProxy")
			->select();
		$this->assign("proxys",$proxys);
		$this->assign("effects",$effects);
		$this->assign("formget",$_GET);
		$this->display();
	}

	public function effects(){
		$_GET['proxy']=$_REQUEST["proxy"];
		if($_REQUEST["proxy"] == ""){
			$where_ands = array("ostatus<>'订单失效'");
			$fields=array(
				'startdate'=> array("field"=>"ctime","operator"=>">="),
				'enddate'=> array("field"=>"ctime","operator"=>"<"),
			);
			if(IS_POST) {
				foreach ($fields as $param => $val) {
					if (isset($_POST[$param]) && !empty($_POST[$param])) {
						$operator = $val['operator'];
						$field = $val['field'];
						$get = $_POST[$param];
						$_GET[$param] = $get;
						if ($operator == "like") {
							$get = "%$get%";
						}
						array_push($where_ands, "$field $operator '$get'");
					}
				}
			}
			$where1 = join(" and ", $where_ands);
			$proxys = M("TbkqqProxy")->select();
			foreach($proxys as $proxy){
				$medias = M("TbkqqFanliMedia")->where(array("proxy"=>$proxy['proxy']))->select();
				$where_ors = array();
				foreach($medias as $media){
					array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
				}
				$where2 = "(" . join(" or ",$where_ors) . ")";
				if($medias)	{
					$where = $where1 . " and " . $where2;
					$effect = M("TbkqqFanliDetails")->where($where)->field("'" . $proxy['proxy'] . "' as proxy,count(orderid) as paycount,sum(effect) as pre_effect,sum(pre_amount) as pre_amount")->find();
					if($effect)	$effects[] = $effect;
				}
			}
		}
		else {
			$where_ands = array("ostatus<>'订单失效'");
			$fields=array(
				'startdate'=> array("field"=>"ctime","operator"=>">="),
				'enddate'=> array("field"=>"ctime","operator"=>"<"),
			);
			if(IS_POST) {
				foreach ($fields as $param => $val) {
					if (isset($_POST[$param]) && !empty($_POST[$param])) {
						$operator = $val['operator'];
						$field = $val['field'];
						$get = $_POST[$param];
						$_GET[$param] = $get;
						if ($operator == "like") {
							$get = "%$get%";
						}
						array_push($where_ands, "$field $operator '$get'");
					}
				}
			}

			$medias = M("TbkqqFanliMedia")->where(array("proxy"=>$_REQUEST["proxy"]))->select();
			$where_ors = array();
			foreach($medias as $media){
				array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
			}
			array_push($where_ands, "(" . join(" or ",$where_ors) . ")");
			$where = join(" and ", $where_ands);
			$effects = M("TbkqqFanliDetails")->where($where)->field(array("DATE_FORMAT(ctime,'%Y-%m-%d') edate","'" . $_REQUEST['proxy'] . "'as proxy","count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->group("edate")->order("edate desc")->select();
		}

		$proxys=M("TbkqqProxy")
			->select();

		if(C('SITE_APPNAME') == "yhg") $liuman = 0.3;

		else $liuman = 0.89;
		$liuman = C("YONGJIN_RATE");
		$this->assign("liuman",$liuman);
		$this->assign("proxys",$proxys);
		$this->assign("effects",$effects);
		$this->assign("formget",$_GET);
		$this->display();
	}
}