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
class AdminTaokeController extends AdminbaseController {
    function _initialize() {
//		parent::_initialize();
    }


    public function effect(){
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
                $medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy']))->select();
                $where_ors = array();
                foreach($medias as $media){
                    $pids = explode("_",$media['pid']);
                    $mediaid = $pids[2];
                    $adid=$pids[3];
                    array_push($where_ors,"sourceid = '" . $mediaid . "'");
                }
                $where2 = "(" . join(" or ",$where_ors) . ")";
                if($medias)	{
                    $where = $where1 . " and " . $where2;
                    $effect = M("TbkqqTaokeDetail")->where($where)->field("'" . $proxy['proxy'] . "' as proxy,count(orderid) as paycount,sum(effect) as pre_effect,sum(pre_amount) as pre_amount")->find();
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

            $medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$_REQUEST["proxy"]))->select();
            $where_ors = array();
            foreach($medias as $media){
                $pids = explode("_",$media['pid']);
                $mediaid = $pids[2];
                $adid=$pids[3];
                array_push($where_ors,"sourceid = '" . $mediaid . "'");
            }
            array_push($where_ands, "(" . join(" or ",$where_ors) . ")");
            $where = join(" and ", $where_ands);
            $effects = M("TbkqqTaokeDetail")->where($where)->field(array("DATE_FORMAT(ctime,'%Y-%m-%d') edate","'" . $_REQUEST['proxy'] . "'as proxy","count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->group("edate")->order("edate desc")->select();
        }

        $proxys=M("TbkqqProxy")
            ->select();
        $this->assign("proxys",$proxys);
        $this->assign("effects",$effects);
        $this->assign("formget",$_GET);
        $this->display();
    }



    public function item_dsh(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
        $act = $_POST['act'];
        if($act == "export_itemlink"){
            $str = "=======================================================================================\r\n";
            $items=$item_model
                ->where("status='0'")
                ->select();
            foreach($items as $item){
                $str .= $item['itemurl'] . "\r\n";

            }

            //	$str = iconv('utf-8','gb2312',$str);

            $fileName = date('Ymd').'.txt';
//			echo $str;
            export_csv($fileName,$str);
            exit;
        }
        else {

            $items = $item_model->where("status='0'")->select();
            foreach ($items as $item) {
                $item['urlc']  = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid']))->count();
                $data[] = $item;
            }
            $media = M("TbkqqTaokeMedia")->field("username")->group("username")->select();
            $this->assign("media",$media);
            $this->assign("items",$data);
            $this->display();
        }

    }

    public function item(){
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
        $act = $_POST['act'];
        $where="status='1'";
        if($act == "export"){
            $str = "=======================================================================================\r\n";
            $items=$item_model
                ->where($where)
                ->select();
            foreach($items as $item){
                $str .= $item['item'] . "\r\n";
                $itemurls = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid']))->order("proxyid")->select();
                foreach($itemurls as $itemurl){
                    $str .= $itemurl['proxy'] . "   " .$itemurl['shorturl'] . "\r\n";
                }
                $str .= "=======================================================================================\r\n";
            }

            //	$str = iconv('utf-8','gb2312',$str);

            $fileName = date('Ymd').'.txt';
//			echo $str;
            export_csv($fileName,$str);
            exit;
        }
        else if($act == "report"){
            $str = "序号,商品名,原价,券后价\n";
            $items=$item_model
                ->where($where)
                ->select();
            foreach($items as $item){
                $str .= $item['no'] . "," . $item['item'] . "," . $item['price'] . "," . $item['aftprice'] . "\n";
            }
//			$str = iconv('utf-8','gb2312',$str);
            $str = mb_convert_encoding($str,'GBK','UTF-8');
            $fileName = date('Ymd').'.csv';
            export_csv($fileName,$str);
            exit;
        }
        else {
            $where_ands =array("status='1'");
            $fields=array(
                'startdate'=> array("field"=>"itime","operator"=>">="),
                'enddate'=> array("field"=>"itime","operator"=>"<="),
                'class'=>array("field"=>"class","operator"=>"="),
                'item'=> array("field"=>"item","operator"=>"like"),
                'qtime'=>array("field"=>"qtime","operator"=>"<")
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
            $items =$item_model->where($where)->order("no")->select();
            foreach ($items as $item) {
                $item['urlc']  = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid']))->count();
                $data[] = $item;
            }
            $this->assign("items",$data);
            $this->assign("formget",$_GET);
            $this->display();
        }
    }

    public function item_history(){

        $where_ands =array("status='1'");
        $fields=array(
            'startdate'=> array("field"=>"itime","operator"=>">="),
            'enddate'=> array("field"=>"itime","operator"=>"<="),
            'class'=>array("field"=>"class","operator"=>"="),
            'item'=> array("field"=>"item","operator"=>"like"),
            'qtime'=>array("field"=>"qtime","operator"=>"<")
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
        $count=M("TbkqqTaokeItemHistory")
            ->where($where)
            ->count();

        $page = $this->page($count, 20);
        $items = M("TbkqqTaokeItemHistory")->where($where)->order('itime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($items as $item) {
            $item['urlc']  = M("TbkqqTaokeItemurls")->where(array("iid"=>$item['iid']))->count();
            $data[] = $item;
        }
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page",$page->GetCurrentPage());
        $this->assign("items",$data);
        $this->assign("formget",$_GET);
        $this->display();

    }

    public function item_restore(){
        if(isset($_POST['ids'])){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            $ids=join(",",$_POST['ids']);
            $data['status'] = '0';
            $items =$item_model->where("id in ($ids)")->save($data);
            if($items){
                $this->success("恢复成功！");
            }
            else $this->error("恢复失败！");
        }
    }



    public function item_add(){
        $this->display();
    }

    public function item_addqq(){
        $this->display();
    }

    public function item_add_post(){
        if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');

            $item=I("post.item");
            $url = str_replace("amp;","",$item['itemurl']);

            $host = parse_url($url, PHP_URL_HOST);
            if($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com'){
                $data = get_url_data($url);

                $iid = $data['id'];
            }

            elseif($host == 's.click.taobao.com'){
                $data = get_item($url);
                $iid = $data['id'];
            }
            else {
                $data = get_url_data($url);

                $iid = $data['id'];
            }
            $item['iid'] = $iid;

            $item['memo'] = htmlspecialchars_decode($item['memo']);
            $item['memo'] = str_replace("</p><p>","<br>",$item['memo']);
            $item['imgmemo'] = htmlspecialchars_decode($item['imgmemo']);

            $item['status'] = '0';
            $item['itime'] = date("Y-m-d H:i:s",time());


//			$url = $item['quan_link'];
            $url = str_replace("amp;","",$item['quan_link']);
            $header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_REFERER, $tu);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            //curl_setopt($ch, CURLOPT_NOBODY,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
            $out = curl_exec($ch);
            $dd =  curl_getinfo($ch);
            curl_close($ch);
            $host = parse_url($dd['url'], PHP_URL_HOST);
            if($host == 'login.taobao.com' || $host == 'login.m.taobao.com'){
                $urldata = get_url_data($dd['url']);
                $url = urldecode($urldata['redirectURL']);

                $url = "https://market.m.taobao.com/apps/aliyx/coupon/detail.html?" . parse_url($url, PHP_URL_QUERY);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                //curl_setopt($ch, CURLOPT_REFERER, $tu);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                //curl_setopt($ch, CURLOPT_NOBODY,1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
                curl_setopt($ch, CURLOPT_MAXREDIRS,2);
                $out = curl_exec($ch);
                $dd =  curl_getinfo($ch);
                curl_close($ch);
            }
            $quan_link = $dd['url'];

            if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                $quan_surpluse = $match[1];
            if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                $quan_receive = $match[1];
            if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                $qtime = $match[1];
            if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
                $quan = $match[1];

            $item['quan_link'] = str_replace("amp;","",$quan_link);
            $item['quan_left'] = $quan_surpluse;
            $item['qtime'] = $qtime;
            $item['quan'] = $quan;


            if($iid != ""){
                $taoke = $item_model->where(array("iid"=>$iid))->find();


                if(!$taoke){
                    $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
                    $str = openhttp_header($u, '', '');
                    $arr = json_decode($str, true);
                    $title = $arr['data']['pageList'][0]['title'];
                    $img = $arr['data']['pageList'][0]['pictUrl'];
                    $eventRate = $arr['data']['pageList'][0]['eventRate'];
                    $tkRate = $arr['data']['pageList'][0]['tkRate'];

                    if($title == ""){
                        $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
                        sleep(1);
                        $str = openhttp_header($u, '', '');
                        $arr = json_decode($str, true);
                        $title = $arr['data']['pageList'][0]['title'];
                        $img = $arr['data']['pageList'][0]['pictUrl'];

                        $eventRate = $arr['data']['pageList'][0]['eventRate'];
                        $tkRate = $arr['data']['pageList'][0]['tkRate'];
                        if($title == ""){
                            $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
                            sleep(1);
                            $str = openhttp_header($u, '', '');
                            $arr = json_decode($str, true);
                            $title = $arr['data']['pageList'][0]['title'];
                            $img = $arr['data']['pageList'][0]['pictUrl'];

                            $eventRate = $arr['data']['pageList'][0]['eventRate'];
                            $tkRate = $arr['data']['pageList'][0]['tkRate'];
                        }
                    }

                    $item['item'] = $title;
                    if(strstr($img,"http:")!==false)
                        $img = $img;
                    else $img = 'http:' . $img;
                    $item['img'] = $img;
                    $item['type'] = '1';

                    $result=$item_model->add($item);
                    if(C("SITE_APPNAME") == 'taotehui'){
                        //$dataoke_result = M('TbkqqTaokeItem','cmf_','DB_DATAOKE')->add($item);
                        //$bag_result = M('TbkqqTaokeItem','cmf_','DB_BAG')->add($item);
                        //$exeou_result = M('TbkqqTaokeItem','cmf_','DB_TC')->add($item);
                        //	$zbw_result = M('TbkqqTaokeItem','cmf_','DB_ZBW')->add($item);
                        //$taotui_result = M('TbkqqTaokeItem','cmf_','DB_TAOTUI')->add($item);
                    }


                    if ($result) {
                        $this->success("添加成功！");
                    } else {
                        $this->error("添加失败！");
                    }
                }

            }
            else $this->error("添加失败！");
        }
    }

    public function item_campaign_post(){
        set_time_limit(0);
        if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
            }
            $username = C("PRODUCT_USERNAME");

            $cookie = get_cookie_by_username($username);
            $ret = "";
            if($cookie != ""){
                $items = $item_model->where("id in ($ids)")->select();
                if($items) {
                    foreach ($items as $item) {
                        $iid = $item['iid'];
                        $type = apply_campaign($cookie,$iid);

                        if($type == '0')$item_model->where(array("iid"=>$iid))->save(array("type"=>$type));
                    }
                }
            }

            $this->success($ret);
        }
    }

    public function item_addqq_post(){
        if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');

            $item=I("post.item");

            $item['imgmemo'] = htmlspecialchars_decode($item['imgmemo']);
            $appname = C("SITE_APPNAME");
            $msg = $_POST['msg'];
            $arr = explode("\n",$msg);
            $memo = "";
            $i = 0;
            foreach($arr as $str){
                if(preg_match('/https?:\/\/[\w=.?&\/;]+/',$str,$match)){
                    $url = str_replace('amp;','',$match[0]);
                    $host = parse_url($url, PHP_URL_HOST);
                    if($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com'){
                        $data = get_url_data($url);

                        $iid = $data['id'];
                    }

                    elseif($host == 's.click.taobao.com'){
                        $data = get_item($url);
                        $iid = $data['id'];
                    }
                    else {

                        $header[] = "Accept-Language: zh-CN,zh;q=0.8";
                        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        //curl_setopt($ch, CURLOPT_REFERER, $tu);
                        curl_setopt($ch, CURLOPT_HEADER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        //curl_setopt($ch, CURLOPT_NOBODY,1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
                        curl_setopt($ch, CURLOPT_MAXREDIRS,2);
                        $out = curl_exec($ch);
                        $dd =  curl_getinfo($ch);
                        curl_close($ch);
                        $host = parse_url($dd['url'], PHP_URL_HOST);
                        if($host == 'login.taobao.com' || $host == 'login.m.taobao.com'){
                            $urldata = get_url_data($dd['url']);
                            $url = urldecode($urldata['redirectURL']);

                            $url = "https://market.m.taobao.com/apps/aliyx/coupon/detail.html?" . parse_url($url, PHP_URL_QUERY);
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            //curl_setopt($ch, CURLOPT_REFERER, $tu);
                            curl_setopt($ch, CURLOPT_HEADER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                            //curl_setopt($ch, CURLOPT_NOBODY,1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
                            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
                            $out = curl_exec($ch);
                            $dd =  curl_getinfo($ch);
                            curl_close($ch);
                        }
                        $quan_link = $dd['url'];

                        if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                            $quan_surpluse = $match[1];
                        if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                            $quan_receive = $match[1];
                        if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                            $qtime = $match[1];
                        if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
                            $quan = $match[1];
                    }
                }
                else {
                    if($i == 0)$memo = trim($str);
                    else $memo = $memo . "\n". trim($str) ;
                }
                $i++;
            }
            if($iid != ''){


                $taoke = $item_model->where(array("iid"=>$iid))->find();
                $curtime = date("Y-m-d H:i:s");
                $data = array();

                if(!$taoke){

                    $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
                    $str = openhttp_header($u, '', '');
                    $arr = json_decode($str, true);
                    $title = $arr['data']['pageList'][0]['title'];
                    $img =  $arr['data']['pageList'][0]['pictUrl'];

                    $eventRate = $arr['data']['pageList'][0]['eventRate'];
                    $tkRate = $arr['data']['pageList'][0]['tkRate'];

                    if($title == ""){
                        $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
                        sleep(1);
                        $str = openhttp_header($u, '', '');
                        $arr = json_decode($str, true);
                        $title = $arr['data']['pageList'][0]['title'];
                        $img = $arr['data']['pageList'][0]['pictUrl'];
                        $eventRate = $arr['data']['pageList'][0]['eventRate'];
                        $tkRate = $arr['data']['pageList'][0]['tkRate'];
                        if($title == ""){
                            $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" . $iid . "&auctionTag=&perPageSize=40&shopTag=";
                            sleep(1);
                            $str = openhttp_header($u, '', '');
                            $arr = json_decode($str, true);
                            $title = $arr['data']['pageList'][0]['title'];
                            $img = $arr['data']['pageList'][0]['pictUrl'];
                            $eventRate = $arr['data']['pageList'][0]['eventRate'];
                            $tkRate = $arr['data']['pageList'][0]['tkRate'];
                        }
                    }
                    $data['iid'] = $iid;
                    $data['item'] = $title;
                    if(strstr($img,"http:")!==false)
                        $img = $img;
                    else $img = 'http:' . $img;
                    $data['img'] = $img;
                    $data['memo'] = $memo;
                    $data['quan_link'] = $quan_link;
                    $data['quan_left'] = $quan_surpluse;
                    $data['quan_receive'] = $quan_receive;
                    $data['qtime'] = $qtime;
                    $data['quan'] = $quan;
                    $data['itime'] = $curtime;
                    $data['status'] = '0';
                    $data['type'] = '1';
                    $data['imgmemo'] = $item['imgmemo'];
                    $data['aftprice'] = $item['aftprice'];

                    $result=$item_model->add($data);
                    if(C("SITE_APPNAME") == 'taotehui'){
                        //$dataoke_result = M('TbkqqTaokeItem','cmf_','DB_DATAOKE')->add($data);
                        //$bag_result = M('TbkqqTaokeItem','cmf_','DB_BAG')->add($data);
                        //$exeou_result = M('TbkqqTaokeItem','cmf_','DB_TC')->add($data);
                        //	$zbw_result = M('TbkqqTaokeItem','cmf_','DB_ZBW')->add($item);
                        //$taotui_result = M('TbkqqTaokeItem','cmf_','DB_TAOTUI')->add($data);
                    }


                    if ($result) {
                        $this->success("添加成功！");
                    } else {
                        $this->error("添加失败！");
                    }

                }
                else $this->error("已存在！");
            }
            else $this->error("添加失败！");


        }
    }

    public function item_edit(){
        $id=  intval(I("get.id"));
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
        $item = $item_model->where("id=$id")->find();
        $this->assign("item",$item);
        $this->display();
    }

    public function item_edit_post(){
        if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            $item=I("post.item");
            $result=$item_model->save($item);
            if ($result!==false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

    public function item_dsh_post(){
        set_time_limit(0);
        if(IS_POST){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            $ids = $_POST['ids'];
            //$username=I("post.usernames");
            $username = C("PRODUCT_USERNAME");
            $proxys = M("TbkqqTaokeMedia")->where(array("username"=>$username,'status'=>'1'))->select();
            //$proxys1 = M("TbkqqTaokeMedia")->where(array("username"=>'15219198262','status'=>'1'))->select();
            //if($proxys1)$proxys = array_merge($proxys,$proxys1);
            foreach($ids as $id){
                $no = $item_model->where(array("status"=>"1"))->max("no");
                $no = $no?$no:0;
                $no = $no+1;
                $item = $item_model->where(array("id"=>$id))->find();
//				$no1 = M("TbkqqTaokeItem")->where(array("id"=>$id))->getField("no");
                $no1 = $item['no'];
                $iid = $item['iid'];
                if($no1)$data['no'] = $no1;
                else $data['no'] = $no;
                $data['status'] = '1';
                $item_model->where(array("id"=>$id))->save($data);
                M("TbkqqTaokeItemurls")->where(array("iid"=>$iid))->delete();
                foreach($proxys as $proxy){
                    $itemurl = array();
                    $proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
                    //$itemurl = M("TbkqqTaokeItemurls")->where(array("iid"=>$iid,"proxy"=>$proxy['proxy']))->find();
                    //if($itemurl){
                    //    continue;
                    //}
                    //else {
                        $itemurl['iid'] = $iid;
                        $data = get_url_data($item['quan_link']);
                        if($data['activity_id'] == "")$quan_id = $data['activityId'];
                        else $quan_id = $data['activity_id'];
                        $itemurl['qurl'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=".$item['type'];
                        $itemurl['shorturl'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=" . $item['type'];

                        //if($proxyid == '001' || $proxyid == '0001'){
                        $token_data = array();
                        $token_data['logo'] = $item['img'];
                        $token_data['text'] = $item['item'];
                        $token_data['url'] = $itemurl['qurl'];
                        $taotokenstr = '';
                        $taotokenstr = get_taotoken($token_data);
                        if($taotokenstr == '')$taotokenstr = get_taotoken($token_data);
                        if($taotokenstr == '')$taotokenstr = get_taotoken($token_data);
                        if($taotokenstr == '')$taotokenstr = get_taotoken($token_data);

                        //   $itemurl['quankl'] = get_taotoken($token_data);
                        $itemurl['quankl'] = $taotokenstr;
                        //    $itemurl['quankl'] = get_taotoken($token_data);
                        //}

                        $itemurl['proxy'] = $proxy['proxy'];
                        $itemurl['itime'] = date("Y-m-d H:i:s",time());
                        unset($itemurl['id']);

                        M("TbkqqTaokeItemurls")->add($itemurl);
                    //}

                }
            }
/*
            $ids_str = json_encode($ids);
            $post_data = array("username"=>"卜一电子商务","ids"=>$ids_str);
            $u = "http://buyids.net/?g=Tbkqq&m=AdminCollect&a=item_dsh_post";
            $header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $u);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);


            $return = curl_exec($ch);

            curl_close($ch);


            $post_data = array("username"=>"张峰2009","ids"=>$ids_str);
            $u = "http://2690.cn/?g=Tbkqq&m=AdminCollect&a=item_dsh_post";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $u);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);


            $return = curl_exec($ch);

            curl_close($ch);
*/
            $this->success("正式推广成功！");
        }
    }

    public function item_post(){
        if(IS_POST){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            $ids = $_POST['nos'];
            foreach ($ids as $id => $r) {
                $data['no'] = $r;
                $item_model->where(array("id" => $id))->save($data);
            }
            $this->success("编号更新成功！");
        }
    }

    public function item_all_post(){
        if(IS_POST){
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            $ids = $_POST['ids'];
            $i = 1;
            foreach ($ids as $id) {
                $data['no'] = $i;
                $item_model->where(array("id" => $id))->save($data);
                $i++;
            }
            $this->success("编号更新成功！");
        }
    }

    public function item_delete(){
        set_time_limit(0);
        $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
        $data['status']='-1';
        if(isset($_GET['id'])){
            $id = intval(I("get.id"));

            $item = $item_model->where(array("id"=>$id))->find();
            if($item){
                M("TbkqqTaokeItemHistory")->add($item);

                if ($item_model->where("id=$id")->delete()) {
                    $this->success("删除成功！");
                } else {
                    $this->error("删除失败！");
                }
            }
            else $this->error("删除失败！");
        }

        if(isset($_POST['ids'])){
            $ids=join(",",$_POST['ids']);
            $items = $item_model->where("id in ($ids)")->select();
            if($items){
                foreach ($items as $item) {
                    M("TbkqqTaokeItemHistory")->add($item);
                    $iid[] = $item['iid'];
                }
                $iids = join(",",$iid);


                if ($item_model->where("id in ($ids)")->delete()) {
                    $this->success("删除成功！");
                } else {
                    $this->error("删除失败！");
                }
            }
            else $this->error("删除失败！");
        }
    }
    public function item_view(){

        $proxys = M("TbkqqTaokeItemurls")->field("proxy")->group("proxy")->select();
        foreach($proxys as $proxy){
            $proxyids[] = $proxy['proxy'];
        }
        $this->assign("proxyids", $proxyids);
        $this->display();
    }

    public function item_view_link(){
        $iid = intval(I("get.iid"));
        $itemurls = M("TbkqqTaokeItemurls")->where(array("iid"=>$iid))->select();
        $itemurla = array();

        foreach($itemurls as $itemurl){
            $proxyid = $itemurl['proxy'];
            $itemurla[$proxyid] = $itemurl['shorturl'];
        }
        $proxys = M("TbkqqProxy")->group('proxy')->select();
        $this->assign("proxys",$proxys);
        $this->assign("itemurl",$itemurla);

        $this->display();
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
        $medias=M("TbkqqTaokeMedia")
            ->where($where)->order("username,media,adname")
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
            $media_model = M("TbkqqTaokeMedia");
            if ($media_model->create($media)){
                if ($media_model->add()!==false) {
                    $this->success(L('ADD_SUCCESS'), U("AdminTaoke/media"));
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
        $data = M('TbkqqTaokeMedia')->where(array("id" => $id))->find();

        $this->assign("media",$data);
        $this->display();
    }

    public function media_edit_post(){
        if (IS_POST) {
            $id=intval($_POST['media']['id']);
            $media=I("post.media");
            $result=M('TbkqqTaokeMedia')->save($media);
            if ($result!==false) {
                $this->success("修改成功！");
            } else {
                $this->error("修改失败！");
            }
        }
    }

    public function cookie_add(){
        $media = M("TbkqqTaokeMedia")->field("username")->group("username")->select();
        $media1_model = M("TbkqqFanliMedia");
        if($media1_model)	$media1 = M("TbkqqFanliMedia")->field("username")->group("username")->select();
        if($media1)	$this->assign("media",array_merge($media,$media1));
        else $this->assign("media",$media);
        $this->display();
    }

    public function cookie_add_post(){
        if(IS_POST) {
            $data['option_name']="cookie_options";
            $data['option_value']=json_encode($_POST['options']);
            $options_model = M("Options");
            $option=$options_model->where("option_name='cookie_options'")->find();
            if($option){
                $options = (array)json_decode($option['option_value']);
                $options[] = $_POST['options'];
                $data['option_value']=json_encode($options);
                $r=$options_model->where("option_name='cookie_options'")->save($data);
            }
            else {
                $options[] = $_POST['options'];
                $data['option_value']=json_encode($options);
                $r=$options_model->add($data);
            }

            if ($r!==false) {
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }
        }
    }

    public function cookie_edit(){
        $options_model = M("Options");
        $option=$options_model->where("option_name='cookie_options'")->find();
        if($option){
            $options = (array)json_decode($option['option_value'],true);
            $this->assign("cookies",$options);
            $this->assign("option_id",$option['option_id']);
        }
        $this->display();
    }

    public function cookie_edit_post(){
        if (IS_POST) {
            if(isset($_POST['option_id'])){
                $data['option_id']=intval($_POST['option_id']);
            }
            $options_model = M("Options");
            $data['option_name']="cookie_options";
            $data['option_value']=json_encode($_POST['options']);
            if($options_model->where("option_name='cookie_options'")->find()){
                $r=$options_model->where("option_name='cookie_options'")->save($data);
            }else{
                $r=$options_model->add($data);
            }
            if ($r!==false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

    public function item_load(){
        $media = M("TbkqqTaokeMedia")->field("username")->group("username")->select();
        $this->assign("media",$media);
        $this->display();
    }

    public function item_load_post(){
        if (IS_POST) {
            $username=I("post.username");
            $proxy1=I("post.proxy1");
            $proxy2=I("post.proxy2");
            $options_model = M("Options");
            $option=$options_model->where("option_name='cookie_options'")->find();
            if($option){
                $options = (array)json_decode($option['option_value'],true);
                foreach($options as $data) {
                    if($data['username'] == $username) $cookie = $data['cookie'];
                }
            }
            $groupid=I("post.groupid");
            $map['username'] = $username;

            if($proxy2 == '') $map['proxy'] = array('egt',$proxy1);
            else $map['proxy'] = array('between',array($proxy1,$proxy2));
            $map['status'] = '1';
            $medias = M("TbkqqTaokeMedia")->where($map)->select();
            foreach($medias as $media) {
                $u="http://pub.alimama.com/favorites/item/export.json?scenes=1&adzoneId=" . $media['adid'] . "&siteId=" . $media['mediaid'] . "&groupId=" . $groupid;

                $str = openhttp_header($u, '', $cookie);

                //$proxyid = substr($media['proxy'], -3, 3);
                $proxyid = substr($media['proxy'],strlen(C('SITE_APPNAME')));
                $filename =  './Uploads/' . $proxyid . ".xls";
                $f=fopen($filename,'w');
                if($f){
                    fwrite($f,$str);
                    $this->item_import($filename, "xls", $proxyid);
                    fclose($f);
                    sleep(3);
                }
                else $this->error("保存失败！");
            }
            $this->success("保存成功！");
        }
    }

    public function item_favload_post(){
        if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
            }

            $items = $item_model->where("id in ($ids)")->select();
            if($items){
                foreach($items as $item){
                    $iid[] = $item['iid'];
                }
                $iids = join(",",$iid);
            }
            $post['groupId'] = $_POST['groupId'];
            $post['itemListStr'] = $iids;
            $u = "http://pub.alimama.com/favorites/item/batchAdd.json";
            $username=I("post.username");

            $options_model = M("Options");
            $option=$options_model->where("option_name='cookie_options'")->find();
            if($option){
                $options = (array)json_decode($option['option_value'],true);
                foreach($options as $data) {
                    if($data['username'] == $username) $cookie = $data['cookie'];
                }
            }
            $str = $this->openhttp_header($u,$post,$cookie);
            $this->success($str);
        }
    }

    public function item_fav_load_post(){
        if (IS_POST) {
            $item_model = M('TbkqqTaokeItem','cmf_','DB_DATAOKE');
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
            }

            $items = $item_model->where("id in ($ids)")->select();
            if($items){
                foreach($items as $item){
                    $iid[] = $item['iid'];
                }
                $iids = join(",",$iid);
            }
            $post['groupId'] = $_POST['groupId'];
            $post['itemListStr'] = $iids;
            $u = "http://pub.alimama.com/favorites/item/batchAdd.json";
            $username=I("post.username");

            $options_model = M("Options");
            $option=$options_model->where("option_name='cookie_options'")->find();
            if($option){
                $options = (array)json_decode($option['option_value'],true);
                foreach($options as $data) {
                    if($data['username'] == $username) $cookie = $data['cookie'];
                }
            }
            $str = $this->openhttp_header($u,$post,$cookie);
            $this->success($str);
        }
    }


    protected function openhttp_header($url, $post='',$cookie='',$referfer='')
    {
        $header[] = "Host: pub.alimama.com";
        //$header[] = "Accept-Encoding: gzip,deflate,sdch";
        $header[] = "Accept-Language: zh-CN,zh;q=0.8";
        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIE,$cookie);
//	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //放在服务器上，会提示出错
        if(!empty($referfer)) curl_setopt($ch, CURLOPT_REFERER, $referfer);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        if($post != "") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        $return = curl_exec($ch);

        curl_close($ch);

        return $return;
    }
    /**
     *  删除
     */
    public function media_delete() {
        $id = intval(I("get.id"));

        if (M("TbkqqTaokeMedia")->delete($id)!==false) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    public function proxy(){
        $proxys = M("TbkqqProxy")->select();
        $parents = array();
        foreach($proxys as $proxy){
            $parents[$proxy['id']] = $proxy['proxy'];
        }
        $this->assign("parents",$parents);
        $this->assign("proxys",$proxys);
        $this->display();
    }

    public function proxy_post(){
        if(IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
                $data['sendqq'] = $_POST['sendqq'];
                $result=M('TbkqqProxy')->where("id in ($ids)")->save($data);
                if ($result!==false) {
                    $this->success("指定成功！");
                } else {
                    $this->error("指定失败！");
                }
            }
        }
    }

    public function proxy_sendwx_post(){
        if(IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
                $data['sendwx'] = $_POST['sendwx'];
                $result=M('TbkqqProxy')->where("id in ($ids)")->save($data);
                if ($result!==false) {
                    $this->success("指定成功！");
                } else {
                    $this->error("指定失败！");
                }
            }
        }
    }

    public function proxy_add(){
        $parents = M("TbkqqProxy")->select();
        $this->assign("parents",$parents);
        $this->display();
    }

    public function proxy_add_post(){
        if(IS_POST){
            $proxy=I("post.proxy");
            /*
            if($proxy['qqgnum']>=1000)$proxy['fcrate'] = "70%";
            else if($proxy['qqgnum']>=500)$proxy['fcrate'] = "60%";
            else if($proxy['qqgnum']>=300)$proxy['fcrate'] = "55%";
            else if($proxy['qqgnum']>=100)$proxy['fcrate'] = "50%";
            else $proxy['fcrate'] = "0";
            */
            $proxy_model = M("TbkqqProxy");
            if ($proxy_model->create($proxy)){
                if ($proxy_model->add()!==false) {
                    $this->success(L('ADD_SUCCESS'), U("AdminTaoke/proxy"));
                } else {
                    $this->error(L('ADD_FAILED'));
                }
            } else {
                $this->error($proxy_model->getError());
            }
        }
    }

    public function proxy_edit(){
        $id = intval(I("get.id"));
        $parents = M("TbkqqProxy")->select();
        $data = M('TbkqqProxy')->where(array("id" => $id))->find();

        $this->assign("proxy",$data);
        $this->assign("parents",$parents);
        $this->display();
    }

    public function proxy_edit_post(){
        if (IS_POST) {
            $id=intval($_POST['proxy']['id']);
            $proxy=I("post.proxy");
            /*
            if($proxy['qqgnum']>=1000)$proxy['fcrate'] = "70%";
            else if($proxy['qqgnum']>=500)$proxy['fcrate'] = "60%";
            else if($proxy['qqgnum']>=300)$proxy['fcrate'] = "55%";
            else if($proxy['qqgnum']>=100)$proxy['fcrate'] = "50%";
            else $proxy['fcrate'] = "0";
            */
            $result=M('TbkqqProxy')->save($proxy);
            if ($result!==false) {
                $this->success("修改成功！");
            } else {
                $this->error("修改失败！");
            }
        }
    }

    public function proxy_delete() {
        $id = intval(I("get.id"));

        if (M("TbkqqProxy")->delete($id)!==false) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    public function proxy_qqstatus_post(){
        if(IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
                $data['qqstatus'] = $_POST['qqstatus'];
                $result=M('TbkqqProxy')->where("id in ($ids)")->save($data);
                if ($result!==false) {
                    $this->success("设置成功！");
                } else {
                    $this->error("设置失败！");
                }
            }
        }
    }

    public function proxy_wxstatus_post(){
        if(IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
                $data['wxstatus'] = $_POST['wxstatus'];
                $result=M('TbkqqProxy')->where("id in ($ids)")->save($data);
                if ($result!==false) {
                    $this->success("设置成功！");
                } else {
                    $this->error("设置失败！");
                }
            }
        }
    }

    public function proxygroup_qqstatus_post(){
        if(IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
                $data['qqstatus'] = $_POST['qqstatus'];
                $result=M('TbkqqProxyQqgrp')->where("id in ($ids)")->save($data);
                if ($result!==false) {
                    $this->success("设置成功！");
                } else {
                    $this->error("设置失败！");
                }
            }
        }
    }

    public function proxygroup_wxstatus_post(){
        if(IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
                $data['wxstatus'] = $_POST['wxstatus'];
                $result=M('TbkqqProxyQqgrp')->where("id in ($ids)")->save($data);
                if ($result!==false) {
                    $this->success("设置成功！");
                } else {
                    $this->error("设置失败！");
                }
            }
        }
    }

    public function proxygroup(){
        $proxys = M("TbkqqProxyQqgrp")->select();
        $this->assign("proxys",$proxys);
        $this->display();
    }

    public function proxygroup_post(){
        if(IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
                $data['sendqq'] = $_POST['sendqq'];
                $result=M('TbkqqProxyQqgrp')->where("id in ($ids)")->save($data);
                if ($result!==false) {
                    $this->success("指定成功！");
                } else {
                    $this->error("指定失败！");
                }
            }
        }
    }

    public function proxygroup_sendwx_post(){
        if(IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
                $data['sendwx'] = $_POST['sendwx'];
                $result=M('TbkqqProxyQqgrp')->where("id in ($ids)")->save($data);
                if ($result!==false) {
                    $this->success("指定成功！");
                } else {
                    $this->error("指定失败！");
                }
            }
        }
    }

    public function proxygroup_add(){

        $this->display();
    }

    public function proxygroup_add_post(){
        if(IS_POST){
            $proxy=I("post.proxy");

            $proxy_model = M("TbkqqProxyQqgrp");
            if ($proxy_model->create($proxy)){
                if ($proxy_model->add()!==false) {
                    $this->success(L('ADD_SUCCESS'), U("AdminTaoke/proxygroup"));
                } else {
                    $this->error(L('ADD_FAILED'));
                }
            } else {
                $this->error($proxy_model->getError());
            }
        }
    }

    public function proxygroup_edit(){
        $id = intval(I("get.id"));

        $data = M('TbkqqProxyQqgrp')->where(array("id" => $id))->find();

        $this->assign("proxy",$data);
        $this->display();
    }

    public function proxygroup_edit_post(){
        if (IS_POST) {
            $id=intval($_POST['proxy']['id']);
            $proxy=I("post.proxy");

            $result=M('TbkqqProxyQqgrp')->save($proxy);
            if ($result!==false) {
                $this->success("修改成功！");
            } else {
                $this->error("修改失败！");
            }
        }
    }

    public function proxygroup_delete() {
        $id = intval(I("get.id"));

        if (M("TbkqqProxyQqgrp")->delete($id)!==false) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    public function details_clean(){
        if(IS_POST) {
            $startdate = $_POST['startdate'];
            $enddate = $_POST['enddate'];
            if($startdate == "")$this->error("删除失败，请选择时间！");
            else {
                if($enddate == "")	$where = "ctime>='" . $startdate. "'";
                else $where = "ctime>='" . $startdate. "' and ctime<='" .$enddate . "'";
                if ( M("TbkqqTaokeDetails")->where($where)->delete() !==false) {
                    $this->success("删除成功！");
                } else {
                    $this->error("删除失败！");
                }
            }

        }
    }

    public function details_upload(){
        set_time_limit(0);
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
                    $where = "1=1";

                    M("TbkqqTaokeDetails")->where($where)->delete();
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
                /*
                                if ($cell instanceof PHPExcel_RichText) {
                                    $cell = $cell->__toString();
                                }
                */
                if(is_object($cell))  $cell= $cell->__toString();
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

            M("TbkqqTaokeDetails")->add($data);
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
                    M("TbkqqTaokeJiesuans")->where("1=1")->delete();
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
                /*
                                if ($cell instanceof PHPExcel_RichText) {
                                    $cell = $cell->__toString();
                                }
                */
                if(is_object($cell))  $cell= $cell->__toString();
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

            M("TbkqqTaokeJiesuans")->add($data);
        }
        $this->success("导入成功");
    }

    function details(){
        $startad = $_GET['startad'];
        $endad = $_GET['endad'];
        if($startad == "" || $endad == "")$where_ands = array("1=1");
        else $where_ands = array("adname>='" . $startad . "' and adname<'" . $endad . "'");

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
        $where = join(" and ", $where_ands);
        $count=M('TbkqqTaokeDetails')
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $details=M("TbkqqTaokeDetails")
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("ctime desc")
            ->select();

        //if(C('SITE_APPNAME') == "yhg") $liuman = 0.3;

        //else $liuman = 0.89;
        //$liuman = C("YONGJIN_RATE");
        $liuman = 0.89;
        $this->assign("liuman",$liuman);
        $media = M("TbkqqTaokeMedia")->field("username")->group("username")->select();
        $this->assign("media",$media);
        $this->assign("details",$details);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page",$page->GetCurrentPage());
        $this->assign("formget",$_GET);
        $this->display();
    }

    function ex_details(){
        $where_ands = array("adname>='0400' and adname<'0500'");
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
        $where = join(" and ", $where_ands);
        $count=M('TbkqqTaokeDetails')
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $details=M("TbkqqTaokeDetails")
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("ctime desc")
            ->select();

        if(C('SITE_APPNAME') == "yhg") $liuman = 0.3;

        else $liuman = 0.89;
        $liuman = C("YONGJIN_RATE");
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
                $medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy']))->select();
                $where_ors = array();
                foreach($medias as $media){
                    array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
                }
                $where2 = "(" . join(" or ",$where_ors) . ")";
                if($medias)	{
                    $where = $where1 . " and " . $where2;
                    $effect = M("TbkqqTaokeJiesuans")->where($where)->field("'" . $proxy['proxy'] . "' as proxy,count(orderid) as paycount,sum(effect) as pre_effect,sum(pre_amount) as pre_amount")->find();
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

            $medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$_REQUEST["proxy"]))->select();
            $where_ors = array();
            foreach($medias as $media){
                array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
            }
            array_push($where_ands, "(" . join(" or ",$where_ors) . ")");
            $where = join(" and ", $where_ands);
            $effects = M("TbkqqTaokeJiesuans")->where($where)->field(array("DATE_FORMAT(jstime,'%Y-%m-%d') edate","'" . $_REQUEST['proxy'] . "'as proxy","count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->group("edate")->order("edate desc")->select();
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
                $medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$proxy['proxy']))->select();
                $where_ors = array();
                foreach($medias as $media){
                    array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
                }
                $where2 = "(" . join(" or ",$where_ors) . ")";
                if($medias)	{
                    $where = $where1 . " and " . $where2;
                    $effect = M("TbkqqTaokeDetails")->where($where)->field("'" . $proxy['proxy'] . "' as proxy,count(orderid) as paycount,sum(effect) as pre_effect,sum(pre_amount) as pre_amount")->find();
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

            $medias = M("TbkqqTaokeMedia")->where(array("proxy"=>$_REQUEST["proxy"]))->select();
            $where_ors = array();
            foreach($medias as $media){
                array_push($where_ors,"sourceid = '" . $media['mediaid'] . "' and adname='" . $media['adname'] . "'");
            }
            array_push($where_ands, "(" . join(" or ",$where_ors) . ")");
            $where = join(" and ", $where_ands);
            $effects = M("TbkqqTaokeDetails")->where($where)->field(array("DATE_FORMAT(ctime,'%Y-%m-%d') edate","'" . $_REQUEST['proxy'] . "'as proxy","count(orderid)"=>"paycount","sum(effect)"=>"pre_effect","sum(pre_amount)"=>"pre_amount"))->group("edate")->order("edate desc")->select();
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
