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
class AdminProxyController extends AdminbaseController {
	function _initialize() {
		parent::_initialize();
	}

    public function pre_effect_l2(){
        $liuman = C("YONGJIN_RATE");
        $fcrate = $liuman;
        $effect_model = M('TbkqqEffectDay');
        $where_ands = array("1=1");
        $fields=array(
            'startdate'=> array("field"=>"edate","operator"=>">="),
            'enddate'=> array("field"=>"edate","operator"=>"<"),
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

        $effects = $effect_model->where($where)->group("proxy")->field(array("proxy","sum(pre_effect)"=>"pre_effect"))->select();
        $json = array();
        if($effects){
            foreach($effects as $effect){
                $all[$effect['proxy']] = $effect['pre_effect'];

            }

            $fcrate = get_yongjin_by_proxy($_SESSION['name']);
            $proxy = M("TbkqqProxy")->where(array("proxy"=>$_SESSION['name']))->find();
            $i2 = 0;
            $i3 = 0;
            if($proxy['fcrate'])$liuman = $fcrate * $proxy['fcrate'];

            $proxys2 = M("TbkqqProxy")->where(array("parent"=>$proxy['id']))->group("proxy")->select();
            if($proxys2){
                foreach($proxys2 as $proxy2){
                    if($proxy2['fcrate']) $liuman2 = $fcrate *$proxy2['fcrate'];
                    else $liuman2 = $fcrate;
                    $i2 += $all[$proxy2['proxy']]*$liuman2*0.15;
                    $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>$proxy2['proxy'],"二级收入"=>round($all[$proxy2['proxy']]*$liuman2*0.15,2));
                }
            }
            else $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>"","二级收入"=>"");
            $json[] = array("一级代理"=>"一级合计","一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>"二级合计","二级收入"=>round($i2,2));
        }
        $this->assign("json",json_encode(array("table"=>$json)));
        $this->assign("formget",$_GET);
        $this->display("pre_effect");
    }


    public function pre_effect(){

        $details_model = M('TbkqqTaokeDetails');
        $where_ands = array("1=1");
        $fields=array(
            'startdate'=> array("field"=>"edate","operator"=>">="),
            'enddate'=> array("field"=>"edate","operator"=>"<"),
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
            $sql = "select b.proxy,sum(a.effect) pre_effect,count(a.orderid) c_order from cmf_tbkqq_taoke_details a,cmf_tbkqq_taoke_media b where a.ctime>= '" . $_POST['startdate'] . "' and a.ctime< '" . $_POST['enddate'] ."' and a.sourceid=b.mediaid and a.adname=b.adname group by b.proxy";
        }
        else {
            $startdate = date('Y-m-01');
            $sql = "select b.proxy,sum(a.effect) pre_effect,count(a.orderid) c_order from cmf_tbkqq_taoke_details a,cmf_tbkqq_taoke_media b where a.ctime>= '" . $startdate . "' and a.sourceid=b.mediaid and a.adname=b.adname group by b.proxy";
        }

        $where = join(" and ", $where_ands);

        $effects = $details_model->query($sql);
        //$effects = $details_model->where($where)->group("proxy")->field(array("proxy","sum(pre_effect)"=>"pre_effect"))->select();
        $json = array();
        if($effects){
            foreach($effects as $effect){
                $all[$effect['proxy']] = $effect['pre_effect'];

            }
            $fcrate = get_yongjin_by_proxy($_SESSION['name']);
            $proxy = M("TbkqqProxy")->where(array("proxy"=>$_SESSION['name']))->find();
            $i2 = 0;
            $i3 = 0;
            if($proxy['fcrate'])$liuman = $fcrate * $proxy['fcrate'];
            else $liuman = $fcrate;
            $proxys2 = M("TbkqqProxy")->where(array("parent"=>$proxy['id']))->group("proxy")->select();
            if($proxys2){
                foreach($proxys2 as $proxy2){
                    if($proxy2['fcrate']) $liuman2 = $fcrate *$proxy2['fcrate'];
                    $i2 += $all[$proxy2['proxy']]*$liuman2*0.2;
                    $proxys3 = M("TbkqqProxy")->where(array("parent"=>$proxy2['id']))->group("proxy")->select();
                    if($proxys3){
                        foreach($proxys3 as $proxy3){
                            if($proxy3['fcrate']) $liuman3 = $fcrate *$proxy3['fcrate'];
                            $i3 += $all[$proxy3['proxy']]*$liuman3*0.03;
                            $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>$proxy2['proxy'],"二级收入"=>round($all[$proxy2['proxy']]*$liuman2*0.2,2),"三级代理"=>$proxy3['proxy'],"三级收入"=>round($all[$proxy3['proxy']]*$liuman3*0.03,2));
                        }
                    }
                    else $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>$proxy2['proxy'],"二级收入"=>round($all[$proxy2['proxy']]*$liuman2*0.2,2),"三级代理"=>"","三级收入"=>"");
                }
            }
            else $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>"","二级收入"=>"","三级代理"=>"","三级收入"=>"");
            $json[] = array("一级代理"=>"一级合计","一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>"二级合计","二级收入"=>round($i2,2),"三级代理"=>"三级合计","三级收入"=>round($i3,2));
        }
        $this->assign("json",json_encode(array("table"=>$json)));
        $this->assign("formget",$_GET);
        $this->display();
    }

	public function pre_effect0(){
		$liuman = C("YONGJIN_RATE");
		$fcrate = $liuman;
		$effect_model = M('TbkqqEffectDay');
		$where_ands = array("1=1");
		$fields=array(
			'startdate'=> array("field"=>"edate","operator"=>">="),
			'enddate'=> array("field"=>"edate","operator"=>"<"),
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
		$fanlis = M("TbkqqFanliDay")->where($where)->group("proxy")->field(array("proxy","sum(pre_effect)"=>"pre_effect"))->select();
		if($fanlis){
			foreach($fanlis as $fanli){
				$fall[$fanli['proxy']] = $fanli['pre_effect'];
			}
		}
		$effects = $effect_model->where($where)->group("proxy")->field(array("proxy","sum(pre_effect)"=>"pre_effect"))->select();
		$json = array();
		if($effects){
			foreach($effects as $effect){
				$all[$effect['proxy']] = $effect['pre_effect'];

			}
            $fcrate = get_yongjin_by_proxy($_SESSION['name']);
			$proxy = M("TbkqqProxy")->where(array("proxy"=>$_SESSION['name']))->find();
			$i2 = 0;
			$i3 = 0;
			if($proxy['fcrate'])$liuman = $fcrate * $proxy['fcrate'];
			$proxys2 = M("TbkqqProxy")->where(array("parent"=>$proxy['id']))->group("proxy")->select();
			if($proxys2){
				foreach($proxys2 as $proxy2){
					if($proxy2['fcrate']) $liuman2 = $fcrate *$proxy2['fcrate'];
					$i2 += $all[$proxy2['proxy']]*$liuman2*0.2;
					$proxys3 = M("TbkqqProxy")->where(array("parent"=>$proxy2['id']))->group("proxy")->select();
					if($proxys3){
						foreach($proxys3 as $proxy3){
							if($proxy3['fcrate']) $liuman3 = $fcrate *$proxy3['fcrate'];
							$i3 += $all[$proxy3['proxy']]*$liuman3*0.03;
							$json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2)." 返利收入:" .round($fall[$proxy['proxy']] * $fcrate*0.5,2),"二级代理"=>$proxy2['proxy'],"二级收入"=>round($all[$proxy2['proxy']]*$liuman2*0.2,2),"三级代理"=>$proxy3['proxy'],"三级收入"=>round($all[$proxy3['proxy']]*$liuman3*0.03,2));
						}
					}
					else $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2)." 返利收入:" .round($fall[$proxy['proxy']] * $fcrate*0.5,2),"二级代理"=>$proxy2['proxy'],"二级收入"=>round($all[$proxy2['proxy']]*$liuman2*0.2,2),"三级代理"=>"","三级收入"=>"");
				}
			}
			else $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2)." 返利收入:" .round($fall[$proxy['proxy']] * $fcrate*0.5,2),"二级代理"=>"","二级收入"=>"","三级代理"=>"","三级收入"=>"");
			$json[] = array("一级代理"=>"一级合计","一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2)." 返利收入:" .round($fall[$proxy['proxy']] * $fcrate*0.5,2),"二级代理"=>"二级合计","二级收入"=>round($i2,2),"三级代理"=>"三级合计","三级收入"=>round($i3,2));
		}
		$this->assign("json",json_encode(array("table"=>$json)));
		$this->assign("formget",$_GET);
		$this->display();
	}
	public function effect(){
		$effect_model = M('TbkqqTaokeDetails');
		$medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$_SESSION['name']))->select();
		if($medias) {
			$data = array();
			foreach ($medias as $media) {
				$data[] = "sourceid='" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'";
			}
			$where_ands[] ="(" . join(" or ", $data) . ")";
		}
		else $where_ands = array("1=1");
		$day_ands =  $where_ands;
		$week_ands = $where_ands;
		$month_ands = $where_ands;
		$curdate = date("Y-m-d",time());

		array_push($day_ands, "ctime>='" . $curdate . "'");
		array_push($week_ands,"YEARWEEK(date_format(ctime,'%Y-%m-%d'),1) = YEARWEEK(now(),1)");
		array_push($month_ands,"date_format(ctime,'%Y-%m')=date_format(now(),'%Y-%m') ");
		$day_where = join(" and ",$day_ands);
		$week_where = join(" and ",$week_ands);
		$month_where = join(" and ",$month_ands);
		$day_effects = $effect_model->where($day_where)->field(array("count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->find();
		$week_effects = $effect_model->where($week_where)->field(array("count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->find();
		$month_effects = $effect_model->where($month_where)->field(array("count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->find();


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
		$where = join(" and ", $where_ands);

		$proxys=M('TbkqqTaokeDetails')->where($where)->group("edate")->field(array("DATE_FORMAT(ctime,'%Y-%m-%d') edate","count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->order("edate desc")->select();
		//if(C('SITE_APPNAME') == "yhg") $liuman = 0.3;

		//else $liuman = 0.89;
		//$liuman = C("YONGJIN_RATE");
		$proxy = M("TbkqqProxy")->where(array("proxy"=>$_SESSION['name']))->find();
		if($proxy){
		    $fcrate = get_yongjin_by_proxy($_SESSION['name']);
			if($proxy['fcrate'])	$liuman =  $fcrate * $proxy['fcrate'];
			else $liuman = C("YONGJIN_RATE");
			$this->assign("liuman",$liuman);
			$this->assign("day_effect",$day_effects);
			$this->assign("week_effect",$week_effects);
			$this->assign("month_effect",$month_effects);
			$this->assign("proxys",$proxys);
			$this->display();
		}

	}

	function detail(){

		$medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$_SESSION['name']))->select();
		if($medias){
			$data = array();
			foreach($medias as $media){
				$data[] = "sourceid='" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'";
			}
			$where_ands[] ="(" . join(" or ", $data) . ")";
		}
		else $where_ands = array("1=1");
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

//		$details=M('TbkqqTaokeDetails')->where($where)->order("ctime desc")->select();
		$count=M('TbkqqTaokeDetails')
			->where($where)
			->count();
		$page = $this->page($count, 20);
		$details=M('TbkqqTaokeDetails')->where($where)->order("ctime desc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		//if(C('SITE_APPNAME') == "yhg") $liuman = 0.3;

		//else $liuman = 0.89;
		//$liuman = C("YONGJIN_RATE");
		$proxy = M("TbkqqProxy")->where(array("proxy"=>$_SESSION['name']))->find();
		if($proxy) {
            $fcrate = get_yongjin_by_proxy($_SESSION['name']);
			if ($proxy['fcrate']) $liuman = $fcrate * $proxy['fcrate'];
			else $liuman = $fcrate;
			$this->assign("liuman",$liuman);
			$this->assign("details",$details);
			$this->assign("Page", $page->show('Admin'));
			$this->assign("current_page",$page->GetCurrentPage());
			$this->assign("formget",$_GET);
			$this->display();
		}

	}

	function fanli_detail(){

		$medias = M("TbkqqFanliMedia")->where(array("proxy"=>$_SESSION['name']))->select();
		if($medias){
			$data = array();
			foreach($medias as $media){
				$data[] = "sourceid='" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'";
			}
			$where_ands[] ="(" . join(" or ", $data) . ")";
		}
		else $where_ands = array("1=0");
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

//		$details=M('TbkqqFanliDetails')->where($where)->order("ctime desc")->select();
		$count=M('TbkqqFanliDetails')
			->where($where)
			->count();
		$page = $this->page($count, 20);
		$details=M('TbkqqFanliDetails')->where($where)->order("ctime desc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

		//if(C('SITE_APPNAME') == "yhg") $liuman = 0.3;

		//else $liuman = 0.89;
		//$liuman = C("YONGJIN_RATE");
		$proxy = M("TbkqqProxy")->where(array("proxy"=>$_SESSION['name']))->find();
		if($proxy) {
            $fcrate = get_yongjin_by_proxy($_SESSION['name']);
			if ($proxy['fcrate']) $liuman = $fcrate * $proxy['fcrate'];
			else $liuman = $fcrate;
			$this->assign("liuman",$liuman);
			$this->assign("details",$details);
			$this->assign("Page", $page->show('Admin'));
			$this->assign("current_page",$page->GetCurrentPage());
			$this->assign("formget",$_GET);
			$this->display();
		}

	}

	function fanli_jiesuan(){

		$medias = M("TbkqqFanliMedia")->where(array("proxy"=>$_SESSION['name']))->select();
		if($medias){
			$data = array();
			foreach($medias as $media){
				$data[] = "sourceid='" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'";
			}
			$where_ands[] ="(" . join(" or ", $data) . ")";
		}
		else $where_ands = array("1=0");
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
		$where = join(" and ", $where_ands);

//		$details=M('TbkqqFanliDetails')->where($where)->order("ctime desc")->select();
		$count=M('TbkqqFanliJiesuans')
			->where($where)
			->count();
		$page = $this->page($count, 20);
		$details=M('TbkqqFanliJiesuans')->where($where)->order("ctime desc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		$this->assign("details",$details);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->display();
	}

	function jiesuan_detail(){

		$medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$_SESSION['name']))->select();
		if($medias){
			$data = array();
			foreach($medias as $media){
				$data[] = "sourceid='" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'";
			}
			$where_ands[] ="(" . join(" or ", $data) . ")";
		}
		else $where_ands = array("1=1");
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
		$where = join(" and ", $where_ands);

		$details=M('TbkqqTaokeJiesuans')->where($where)->order("jstime desc")->select();
		$count=M('TbkqqTaokeJiesuans')
			->where($where)
			->count();
		$page = $this->page($count, 20);
		$details=M('TbkqqTaokeJiesuans')->where($where)->order("jstime desc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		$this->assign("details",$details);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->assign("formget",$_GET);
		$this->display();
	}

	public function index(){
		$proxys = M("TbkqqProxy")->select();
		$this->assign("proxys",$proxys);
		$this->display();
	}

	public function jiesuan(){
		$medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$_SESSION['name']))->select();
		if($medias) {
			$data = array();
			foreach ($medias as $media) {
				$data[] = "sourceid='" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'";
			}
			$where_ands[] ="(" . join(" or ", $data) . ")";
		}
		else $where_ands = array("1=1");
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
		$where = join(" and ", $where_ands);
		$proxys=M('TbkqqTaokeJiesuans')->where($where)->group("edate")->field(array("DATE_FORMAT(jstime,'%Y-%m-%d') edate","count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->order("edate desc")->select();


		$this->assign("proxys",$proxys);
		$this->display();
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
	
}