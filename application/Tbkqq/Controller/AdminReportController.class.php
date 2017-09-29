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
class AdminReportController extends AdminbaseController {

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

            $proxys = M("TbkqqProxy")->group("proxy")->order("proxy")->select();
            foreach($proxys as $proxy){
                $i2 = 0;
                $i3 = 0;
                if($proxy['fcrate'])$liuman = $fcrate * $proxy['fcrate'];
                else $liuman = $fcrate;
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
            $sql = "select b.proxy,sum(a.effect) pre_effect,count(a.orderid) c_order from cmf_tbkqq_taoke_details a,cmf_tbkqq_taoke_media b where a.ctime>= '" . $_POST['startdate'] . "' and a.ctime< '" . $_POST['enddate'] ."'' and a.sourceid=b.mediaid and a.adname=b.adname group by b.proxy";
        }
        else {
            $startdate = date('Y-m-01');
            $sql = "select b.proxy,sum(a.effect) pre_effect,count(a.orderid) c_order from cmf_tbkqq_taoke_details a,cmf_tbkqq_taoke_media b where a.ctime>= '" . $startdate . "' and a.sourceid=b.mediaid and a.adname=b.adname group by b.proxy";
        }

		$where = join(" and ", $where_ands);
		$fanlis = M("TbkqqFanliDay")->where($where)->group("proxy")->field(array("proxy","sum(pre_effect)"=>"pre_effect"))->select();
		if($fanlis){
			foreach($fanlis as $fanli){
				$fall[$fanli['proxy']] = $fanli['pre_effect'];
			}
		}

        $effects = $details_model->query($sql);
//		$effects = $effect_model->where($where)->group("proxy")->field(array("proxy","sum(pre_effect)"=>"pre_effect"))->select();
		$json = array();
		if($effects){
			foreach($effects as $effect){
				$all[$effect['proxy']] = $effect['pre_effect'];

			}

			$proxys = M("TbkqqProxy")->group("proxy")->order("proxy")->select();
			foreach($proxys as $proxy){
                $fcrate = get_yongjin_by_proxy($proxy['proxy']);
				$i2 = 0;
				$i3 = 0;
				if($proxy['fcrate'])$liuman = $fcrate * $proxy['fcrate'];
				else $liuman = $fcrate;
				$proxys2 = M("TbkqqProxy")->where(array("parent"=>$proxy['id']))->group("proxy")->select();
				if($proxys2){

					foreach($proxys2 as $proxy2){
                        if($proxy2['fcrate']) $liuman2 = $fcrate *$proxy2['fcrate'];
                        else $liuman2 = $fcrate;
						$i2 += $all[$proxy2['proxy']]*$liuman2*0.2;
						$proxys3 = M("TbkqqProxy")->where(array("parent"=>$proxy2['id']))->group("proxy")->select();
						if($proxys3){

							foreach($proxys3 as $proxy3){
								if($proxy3['fcrate']) $liuman3 = $fcrate *$proxy3['fcrate'];
								$i3 += $all[$proxy3['proxy']]*$liuman3*0.03;
								$json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" . round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>$proxy2['proxy'],"二级收入"=>round($all[$proxy2['proxy']]*$liuman2*0.2,2),"三级代理"=>$proxy3['proxy'],"三级收入"=>round($all[$proxy3['proxy']]*$liuman3*0.03,2));
							}
						}
						else $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>$proxy2['proxy'],"二级收入"=>round($all[$proxy2['proxy']]*$liuman2*0.2,2),"三级代理"=>"","三级收入"=>"");
					}
				}
				else $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>"","二级收入"=>"","三级代理"=>"","三级收入"=>"");
				$json[] = array("一级代理"=>"一级合计","一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2),"二级代理"=>"二级合计","二级收入"=>round($i2,2),"三级代理"=>"三级合计","三级收入"=>round($i3,2));
			}
		}
		$this->assign("json",json_encode(array("table"=>$json)));
		$this->assign("formget",$_GET);
		$this->display();
	}

    public function pre_effect_gongzi(){
        $details_model = M('TbkqqTaokeDetails');
        $where_ands = array("1=1");

        if(IS_POST) {

            $startdate = $_POST['startdate'];
            $enddate = $_POST['enddate'];
            $_GET = $_POST;

            $sql = "select b.proxy,sum(a.effect) pre_effect,count(a.orderid) c_order from cmf_tbkqq_taoke_details a,cmf_tbkqq_taoke_media b where a.ctime>= '" . $startdate . "' and a.ctime< '" . $enddate ."' and a.sourceid=b.mediaid and a.adname=b.adname group by b.proxy";
            \Think\Log::write($sql,'WARN');
        }
        else {
            $startdate = date('Y-m-01');
            $sql = "select b.proxy,sum(a.effect) pre_effect,count(a.orderid) c_order from cmf_tbkqq_taoke_details a,cmf_tbkqq_taoke_media b where a.ctime>= '" . $startdate . "' and a.sourceid=b.mediaid and a.adname=b.adname group by b.proxy";
        }


        $proxys = M("TbkqqProxy")->group("proxy")->order("proxy")->select();
        foreach($proxys as $proxy){
            $fcrate = get_yongjin_by_proxy($proxy['proxy']);
            if($proxy['fcrate'])$liuman = $fcrate * $proxy['fcrate'];
            else $liuman = $fcrate;
            $medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy']))->select();
            $where_ors = array();
            foreach($medias as $media){
                array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
            }
            $where1 = "1=1";
            $where2 = "(" . join(" or ",$where_ors) . ")";
            if($medias)	{
                $where = $where1 . " and " . $where2;
                $eff = M("TbkqqZhongjiangDetails")->where($where)->field("'" . $proxy['proxy'] . "' as proxy,count(orderid) as paycount,sum(effect) as pre_effect,sum(pre_amount) as pre_amount")->find();
                if($eff)	{
                    $zhongjiang[$eff['proxy']] = round($eff['pre_effect'] * $liuman,2);

                }
            }
        }

        $where = join(" and ", $where_ands);
        $effects = $details_model->query($sql);
//        $effects = $effect_model->where($where)->group("proxy")->field(array("proxy","sum(pre_effect)"=>"pre_effect"))->select();
        $json = array();
        if($effects){
            foreach($effects as $effect){
                $all[$effect['proxy']] = $effect['pre_effect'];

            }

            $proxys = M("TbkqqProxy")->group("proxy")->order("proxy")->select();
            foreach($proxys as $proxy){
                $i2 = 0;
                $i3 = 0;
                $fcrate = get_yongjin_by_proxy($proxy['proxy']);
                if($proxy['fcrate'])$liuman = $fcrate * $proxy['fcrate'];
                else $liuman = $fcrate;
                $proxys2 = M("TbkqqProxy")->where(array("parent"=>$proxy['id']))->group("proxy")->select();
                if($proxys2){

                    foreach($proxys2 as $proxy2){
                        if($proxy2['fcrate']) $liuman2 = $fcrate *$proxy2['fcrate'];
                        else $liuman2 = $fcrate;
                        $i2 += $all[$proxy2['proxy']]*$liuman2*0.2;
                        $proxys3 = M("TbkqqProxy")->where(array("parent"=>$proxy2['id']))->group("proxy")->select();
                        if($proxys3){

                            foreach($proxys3 as $proxy3){
                                if($proxy3['fcrate']) $liuman3 = $fcrate *$proxy3['fcrate'];
                                else $liuman3 = $fcrate;
                                $i3 += $all[$proxy3['proxy']]*$liuman3*0.03;
                                //$json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" . round($all[$proxy['proxy']] * $liuman,2) ." 返利收入:" . round($fall[$proxy['proxy']] * $fcrate*0.5,2) ,"二级代理"=>$proxy2['proxy'],"二级收入"=>round($all[$proxy2['proxy']]*$liuman2*0.2,2),"三级代理"=>$proxy3['proxy'],"三级收入"=>round($all[$proxy3['proxy']]*$liuman3*0.03,2));
                            }
                        }
                        //else $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2)." 返利收入:" .round($fall[$proxy['proxy']] * $fcrate*0.5,2),"二级代理"=>$proxy2['proxy'],"二级收入"=>round($all[$proxy2['proxy']]*$liuman2*0.2,2),"三级代理"=>"","三级收入"=>"");
                    }
                }
                //else $json[] = array("一级代理"=>$proxy['proxy'],"一级收入"=>"推广收入：" .round($all[$proxy['proxy']] * $liuman,2)." 返利收入:" .round($fall[$proxy['proxy']] * $fcrate*0.5,2),"二级代理"=>"","二级收入"=>"","三级代理"=>"","三级收入"=>"");
                $json[] = array('proxy'=>$proxy['proxy'],"tg"=>round($all[$proxy['proxy']] * $liuman,2),"fanli" =>0,"erji"=>round($i2,2),"sanji"=>round($i3,2),'zhongjiang'=>$zhongjiang[$proxy['proxy']],'heji'=>round($all[$proxy['proxy']] * $liuman,2)+round($i2,2)+round($i3,2)-$zhongjiang[$proxy['proxy']]);
            }
        }
//        $this->assign("json",json_encode(array("table"=>$json)));

        $this->assign("json",$json);
//        $this->assign("zhongjiang",$zhongjiang);
        $this->assign("formget",$_GET);
        $this->display();
    }

	function effect_realtime_topn(){

		$curdate = date("Y-m-d",time());
		$where1 = "ctime>='" . $curdate . "'";
			$proxys = M("TbkqqProxy")->select();
			foreach($proxys as $proxy){
				$medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy']))->select();
				$where_ors = array();
				foreach($medias as $media){
					array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
				}
				$where2 = "(" . join(" or ",$where_ors) . ")";
				if($medias)	{
					$where = $where1 . " and " . $where2;
					$effect = M("TbkqqTaokeDetails")->where($where)->field("'" . $proxy['proxy'] . "' as proxy,count(orderid) as paycount,sum(effect) as pre_effect,sum(pre_amount) as pre_amount")->find();
					if($effect)	{
						$effects[$effect['proxy']] = $effect['pre_effect'];

					}
				}
			}
		arsort($effects);

		$this->assign("effects",$effects);

		$this->display();
	}

	public function good_effect_topn(){
		$where_ands=array("1=1");
		$fields=array(
			'startdate'=> array("field"=>"ctime","operator"=>">="),
			'enddate'=> array("field"=>"ctime","operator"=>"<"),
		);
		if(IS_POST) {
			$orderby = $_POST['orderby'];
			$_GET['orderby'] = $orderby;
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
			$where = join(" and ", $where_ands);
			$effects = M("TbkqqTaokeDetails")->where($where)->field("goods ,count(orderid) as paycount,sum(effect) as pre_effect,sum(pre_amount) as pre_amount")->group("goods")->order($orderby. " desc")->select();
			$this->assign("effects",$effects);
			$this->assign("formget",$_GET);
		}
		$this->display();
	}


}