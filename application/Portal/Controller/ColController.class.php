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
class ColController extends HomebaseController {
	
    //首页
	public function index() {

    }

    public function get_dataoke_by_api(){
        set_time_limit(0);
        $dataoke_model = M('TbkItems');
        $caiji_cates = M("CaijiCate")->where("source='dataoke'")->select();
        $cates=array();
        foreach ($caiji_cates as $cate){
            $cates[$cate['cate1']] = $cate['cate'];
        }
        $iids = array();

        $dataokes = $dataoke_model->field('iid')->select();

        foreach($dataokes as $dataoke){

            $iids[] = $dataoke['iid'];
        }
        $u = "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey=bnsdd1etil&v=2&page=1";
        $str = http_get_content($u);
        $json = json_decode($str,true);
        if($json['total_num'] != '' && $json['total_num']>0){

            for($p=2;$p<=$json['total_num'] /200 +1;$p++){
                $str = "";
                $u = "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey=bnsdd1etil&v=2&page=$p";
                echo $u;
                $str = http_get_content($u);
//echo $str;
                $json = json_decode($str,true);
                //print_r($json);
                if($json['result']){
                    foreach($json['result'] as $item){

                        $data = array();
//$iids[$item['GoodsID']]++;
                        $data['num_iid'] = $item['GoodsID'];
                        $data['title'] = $item['Title'];
                        $data['dtitle'] = $item['D_title'];
                        $data['pic_url'] = $item['Pic'];
                        $data['cate_id'] = $cates[$item['Cid']];
                        $data['price'] = $item['Org_Price'];
                        $data['coupon_price'] = $item['Price'];
                        //$data['istmall'] = $item['IsTmall'];
                        $data['volume'] = $item['Sales_num'];
                        //$data['dsr'] = $item['Dsr'];
                        $data['sellerId'] = $item['SellerID'];
                        //$data['commission'] = $item['Commission'];
                        //$data['commission_jihua'] = $item['Commission_jihua'];
                        //$data['commission_queqiao'] = $item['Commission_queqiao'];
                        //$data['jihua_link'] = $item['Jihua_link'];
                        //$data['que_siteid'] = $item['Que_siteid'];
                        //$data['jihua_shenhe'] = $item['Jihua_shenhe'];
                        $data['intro'] = $item['Introduce'];
                        //$data['quan_id'] = $item['Quan_id'];
                        $data['quan'] = $item['Quan_price'];
                        $data['quan_time'] = $item['Quan_time'];
                        $data['quan_surplus'] = $item['Quan_surplus'];
                        $data['quan_receive'] = $item['Quan_receive'];
                        $data['quan_condition'] = $item['Quan_condition'];
                        $data['quanurl'] = $item['Quan_m_link'];
                        //$data['quan_link'] = $item['Quan_link'];
                        $data['add_time'] = time();
                        //if($dataoke_model->where(array("iid"=>$item['GoodsID']))->find())
                        if(in_array($item['GoodsID'],$iids))
                            $dataoke_result =$dataoke_model->where(array("iid"=>$item['GoodsID']))->save($data);
                        //continue;
                        else {
                            $dataoke_result =$dataoke_model->add($data);

                        }
                    }
                }

            }
        }
//print_r($iids);


    }

    public function  get_haodanku(){
        set_time_limit(0);
        echo "get_haodanku start";
        $dataoke_model = M('TbkItem3','cmf_','DB_DATAOKE');
        $dataokes = $dataoke_model->field('iid')->select();
        $curtime = date("Y-m-d H:i:s");
        foreach($dataokes as $dataoke){
            $iids[] = $dataoke['iid'];
        }

        $p = $_GET['page'];
        for($p=1;$p<100;$p++){
            $str = "";
            $u = "http://www.haodanku.com/index/index/nav/3/starttime/7/p/" . $p .".html?json=true";
            echo $u;
            $str = file_get_contents($u);
            if($str == '[]')break;
            $json = json_decode($str,true);
            if($json){
                foreach($json as $item){

                    $data = array();

                    $data['iid'] = $item['itemid'];
                    $data['item'] = $item['itemtitle'];

                    $data['img'] = $item['itempic'];

                    $data['aftprice'] = $item['itemprice']-$item['couponmoney'];
                    //$data['quan_price'] = $item['couponmoney'];
                    $data['qtime'] = date("Y-m-d",$item['couponendtime']);
                    $data['quan_surplus'] = $item['couponsurplus'];
                    $data['quan_receive'] = $item['couponreceive'];
                    $data['quan'] = $item['couponexplain'];

                    $data['quan_link'] = $item['couponurl'];
                    $data['itime'] = $curtime;
                    if(in_array($item['itemid'],$iids))
                        $dataoke_result =$dataoke_model->where(array("iid"=>$item['itemid']))->save($data);
                    //continue;
                    else {
                        $dataoke_result =$dataoke_model->add($data);

                    }
                }
            }

        }

    }

    public function clean_quan(){
        set_time_limit(0);
        $taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
        $taoke_model->where("qtime<now() - interval 1 day")->delete();
        $items = $taoke_model->select();

        foreach($items as $item){
            $header = array();
            $match = array();
            $quan_surpluse = "";
            $quan_receive = "";
            $qtime = "";
            $quan_link = $item['quan_link'];
            $iid = $item['iid'];
            //$header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $quan_link);
            //curl_setopt($ch, CURLOPT_REFERER, $tu);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            //curl_setopt($ch, CURLOPT_NOBODY,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
            $out = curl_exec($ch);
            curl_close($ch);


            if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                $quan_surpluse = $match[1];

            if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                $quan_receive = $match[1];
            if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                $qtime = $match[1];

            if($quan_surpluse == '')
                $taoke_model->where(array("iid"=>$iid))->delete();

            else
                $taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"qtime"=>$qtime));
        }




        ///////////////////////////////////
        $taoke_model = M('TbkItem3','cmf_','DB_DATAOKE');
        $taoke_model->where("qtime<now() - interval 1 day")->delete();
        $items = $taoke_model->select();

        foreach($items as $item){
            $header = array();
            $match = array();
            $quan_surpluse = "";
            $quan_receive = "";
            $qtime = "";
            $quan_link = $item['quan_link'];
            $iid = $item['iid'];
            //$header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $quan_link);
            //curl_setopt($ch, CURLOPT_REFERER, $tu);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            //curl_setopt($ch, CURLOPT_NOBODY,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
            $out = curl_exec($ch);
            curl_close($ch);


            if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                $quan_surpluse = $match[1];

            if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                $quan_receive = $match[1];
            if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                $qtime = $match[1];

            if($quan_surpluse == '')
                $taoke_model->where(array("iid"=>$iid))->delete();

            else
                $taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"qtime"=>$qtime));
        }

        $taoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
        $taoke_model->where("quan_time<now() - interval 1 day")->delete();
        $items = $taoke_model->select();

        foreach($items as $item){
            $header = array();
            $match = array();
            $quan_surpluse = "";
            $quan_receive = "";
            $qtime = "";
            $quan_link = $item['Quan_link'];
            $iid = $item['iid'];
            //$header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $quan_link);
            //curl_setopt($ch, CURLOPT_REFERER, $tu);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            //curl_setopt($ch, CURLOPT_NOBODY,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
            $out = curl_exec($ch);
            curl_close($ch);


            if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                $quan_surpluse = $match[1];

            if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                $quan_receive = $match[1];
            if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                $qtime = $match[1];

            if($quan_surpluse == '')
                $taoke_model->where(array("iid"=>$iid))->delete();

            else
                $taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"quan_time"=>$qtime));
        }
    }


    public function clean_dataoke(){
        set_time_limit(0);
        $taoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
        $taoke_model->where("quan_time<now() - interval 1 day")->delete();
        $count = $taoke_model->count();
        for($i=0;$i<=$count/100;$i++){
            $items = $taoke_model->limit($i*100 . ",100")->select();

            foreach($items as $item){
                $header = array();
                $match = array();
                $quan_surpluse = "";
                $quan_receive = "";
                $qtime = "";
                $quan_link = $item['Quan_link'];
                $iid = $item['iid'];
                //$header[] = "Accept-Language: zh-CN,zh;q=0.8";
                $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $quan_link);
                //curl_setopt($ch, CURLOPT_REFERER, $tu);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                //curl_setopt($ch, CURLOPT_NOBODY,1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
                curl_setopt($ch, CURLOPT_MAXREDIRS,2);
                $out = curl_exec($ch);
                curl_close($ch);


                if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                    $quan_surpluse = $match[1];

                if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                    $quan_receive = $match[1];
                if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                    $qtime = $match[1];

                if($quan_surpluse == '')
                    $taoke_model->where(array("iid"=>$iid))->delete();

                else
                    $taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"quan_time"=>$qtime));
            }
        }


    }

    public function detail_autoload_post(){
        set_time_limit(0);
        $startdate = date("Y-m-d",time()-2*86400);
        $enddate =date("Y-m-d");
        $where = "ctime>='" . $startdate . "'";
        $filename_arr = array();
        $filename = "";
        $u="http://pub.alimama.com/report/getTbkPaymentDetails.json?queryType=1&payStatus=&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime=$startdate&endTime=$enddate";

        $options_model = M("Options");
        $option=$options_model->where("option_name='cookie_options'")->find();
        if($option){

            $options = (array)json_decode($option['option_value'],true);

            foreach($options as $data) {
                $media = M("TbkqqTaokeMedia")->where(array("username" => $data['username']))->find();
                if ($media) {

                    $cookie = $data['cookie'];
                    $str = openhttp_header($u, '', $cookie);

                    if($str == "") exit();
                    $curtime = time();
                    $filename = './Uploads/details_' . $curtime . ".xls";
                    $f = fopen($filename, 'w');
                    echo $data['username'] . "\n";
                    if ($f) {
                        fwrite($f, $str);
                        $filename_arr[] = $filename;
                        fclose($f);
                        sleep(3);
                    }

                }
            }

            M("TbkqqTaokeDetails")->where($where)->delete();
            foreach($filename_arr as $filename){
                $this->details_import($filename, "xls",'taoke');
            }

            $filename_arr = array();
            $filename = "";
            foreach($options as $data) {
                $fanli_media = M("TbkqqFanliMedia")->where(array("username"=>$data['username']))->find();
                if($fanli_media){
                    $cookie = $data['cookie'];
                    $str = openhttp_header($u, '', $cookie);
                    if($str == "") exit();
                    $curtime = time();
                    $filename =  './Uploads/details_' . $curtime . ".xls";
                    $f=fopen($filename,'w');
                    echo $data['username'] . "\n";
                    if($f){
                        fwrite($f,$str);
                        $filename_arr[] = $filename;
                        fclose($f);
                        sleep(3);
                    }
                }
            }

            M("TbkqqFanliDetails")->where($where)->delete();
            foreach($filename_arr as $filename){
                $this->details_import($filename, "xls",'fanli');
            }
        }

    }

    protected function details_import($filename,$exts,$type){
        if($type == "taoke")	$model = M("TbkqqTaokeDetails");
        if($type == "fanli") $model = M("TbkqqFanliDetails");
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

            $model->add($data);

        }

    }

    public function item_campaign_post(){
        set_time_limit(0);
        if (IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
            }
            $username=I("post.username");

            $options_model = M("Options");
            $option=$options_model->where("option_name='cookie_options'")->find();
            if($option){
                $options = (array)json_decode($option['option_value'],true);
                foreach($options as $data) {
                    if($data['username'] == $username) $cookie = $data['cookie'];
                }
            }
            $ret = "";
            if($cookie != ""){
                $items = M("TbkqqTaokeItem")->where("id in ($ids)")->select();
                if($items) {
                    foreach ($items as $item) {
                        $t = time();
                        $iid = $item['iid'];
                        $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" .$iid . "&auctionTag=&perPageSize=40&shopTag=";
                        $str = openhttp_header($u,'',$cookie);
                        $arr = json_decode($str,true);
                        $sellerId = $arr['data']['pageList'][0]['sellerId'];
                        $tkRate = $arr['data']['pageList'][0]['tkRate'];
                        $eventRate = $arr['data']['pageList'][0]['eventRate'];
                        $type = '1';
                        $u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
                        $str = openhttp_header($u, '', $cookie);
                        $arr = json_decode($str, true);
                        if ($arr['ok'] == '1' && $arr['data']) {
                            $rate = $tkRate;
                            if($eventRate != ''){
                                if($rate<$eventRate){
                                    $rate = $eventRate;
                                    $type = '0';
                                }
                            }
                            $cid = '';
                            $keeperid = '';
                            $post = array();

                            foreach ($arr['data'] as $data) {
                                if($data['manualAudit'] == '1') continue;
                                if ($data['commissionRate'] > $rate) {
                                    $rate = $data['commissionRate'];
                                    $cid = $data['CampaignID'];
                                    $keeperid = $data['ShopKeeperID'];
                                }
                            }
                            if($cid != ""){
                                $post['campId'] = $cid;
                                $post['keeperid'] = $keeperid;
                                $post['applyreason'] = "淘特惠淘客推广申请";
                                $cookie_data = excookie($cookie);
                                $post['_tb_token_'] = $cookie_data['_tb_token_'];
                                $post['t'] = $t;
                                $type = '1';
                                $post_str = "campId=" . $post['campId'] . "&keeperid=" . $post['keeperid'] . "&applyreason=" . $post['applyreason'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
                                //print_r($post);
                                $u = "http://pub.alimama.com/pubauc/applyForCommonCampaign.json";
                                $reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" . $item['itemurl'];
                                sleep(1);
                                $ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
                                sleep(1);

                            }
                            if($type == '0')M("TbkqqTaokeItem")->where(array("iid"=>$iid))->save(array("type"=>$type));
                        }
                    }
                }
            }

            $this->success($ret);
        }
    }

    public function check_cun(){
        set_time_limit(0);
        $taoke_model = M('Items','cmf_','DB_DATAOKE');

        $items = $taoke_model->field("id,num_iid")->where("isnull(cun)")->select();

        foreach($items as $item){
            $content = "";
            $url = "https://cunlist.taobao.com/?q=" . $item['num_iid'];
            $content = http_get_content($url);
            if(preg_match('/<div class=\"b1\">.*<\/div>/',$content,$match))
                $data['cun'] = '1';
            else $data['cun'] = '0';
            $data['id'] = $item['id'];
            $taoke_model->save($data);
            echo $match['0'];
        }

    }

    public function check_cun_v1(){
        set_time_limit(0);
        $taoke_model = M('Items','cmf_','DB_DATAOKE');
        $dataoke_model = M('TbkDataokeItem','cmf_','DB_DATAOKE');
        $cun_model = M("CunItems");
        $cun_iids = $cun_model->field('num_iid')->where(array("cun"=>"1"))->select();
        foreach($cun_iids as $cun_iid){

            $iids[] = $cun_iid['num_iid'];
        }
        $count = $taoke_model->where("isnull(cun)")->count();
        echo $count;
        //$page = $this->page($count, 100);
        $p = $count/100;
        for($i=0;$i<=$p+1;$i++){
            echo $i;
            $limit = "'" . $i*100 . ",100'";
            echo $limit;
            $items = $taoke_model->where("isnull(cun)")
                ->limit($limit)
                ->select();
            //$items = $taoke_model->where("isnull(cun)")->select();

            foreach($items as $item){

                if(in_array($item['num_iid'],$iids)) {
                }
                else{
                    if(is_cun($item['num_iid']))
                    {
                        unset($cun_item);

                        $data['cun'] = '1';
                        $cun_item = $item;
                        $cun_item['click_url'] = str_replace('mm_110341117_13180074_52464478','mm_120456532_20542124_69830211',$item['click_url']);
                        $quan_data = get_url_data($item['quanurl']);
                        if($quan_data['activity_id'] == "")$quan_id = $quan_data['activityId'];
                        else $quan_id = $quan_data['activity_id'];
                        $cun_item['quanurl'] = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $item['sellerId'] ."&activity_id=" . $quan_id;
                        $cun_item['source'] = 'dataoke';
                        $cun_item['dtitle'] = $item['title'];

                        $da_item = $dataoke_model->where(array("iid"=>$item['num_iid']))->find();
                        if($da_item){
                            $cun_item['dtitle'] = $da_item['d_title'];
                            //$cun_item['']
                        }
                        unset($cun_item['id']);
                        $cun_model->add($cun_item);
                    }

                    else $data['cun'] = '0';
                    $data['id'] = $item['id'];
                    $taoke_model->save($data);
                }

            }
        }

    }
    public function clean_cun_quan(){
        set_time_limit(0);
        $taoke_model = M('CunItems');
        //$taoke_model->where("from_unixtime(coupon_end_time)<now()")->delete();
        $items = $taoke_model->select();

        foreach($items as $item){
            $header = array();
            $match = array();
            $quan_surpluse = "";
            $quan_receive = "";
            $qtime = "";

            $data = get_url_data($item['quanurl']);
            if($data['activity_id'] == "")$quan_id = $data['activityId'];
            else $quan_id = $data['activity_id'];
            $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $item['sellerId'] ."&activity_id=" . $quan_id;

            $iid = $item['iid'];
            //$header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $quan_link);
            //curl_setopt($ch, CURLOPT_REFERER, $tu);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            //curl_setopt($ch, CURLOPT_NOBODY,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
            $out = curl_exec($ch);
            curl_close($ch);


            if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                $quan_surpluse = $match[1];

            if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                $quan_receive = $match[1];
            if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                $qtime = $match[1];

            if($quan_surpluse == '')
                $taoke_model->where(array("num_iid"=>$iid))->delete();

            else
                $taoke_model->where(array("num_iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse));
        }

    }

    public function check_cun_v2(){
        set_time_limit(0);
        $cun_model = M("CunItems");


        for($p=1;$p<=110;$p++){
            $str = "";
            $u = "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey=bnsdd1etil&v=2&page=$p";
            echo $u;
            $str = file_get_contents($u);
//echo $str;
            $json = array();
            $json = json_decode($str,true);
            //print_r($json);
            if($json['result']){
                foreach($json['result'] as $item){

                    $data = array();
//$iids[$item['GoodsID']]++;
                    $data['num_iid'] = $item['GoodsID'];
                    $data['title'] = $item['Title'];
                    $data['dtitle'] = $item['D_title'];
                    $data['pic_url'] = $item['Pic'];
                    $data['cate_id'] = $item['Cid'];
                    $data['price'] = $item['Org_Price'];
                    $data['coupon_price'] = $item['Price'];
                    if($item['IsTmall'] == '1')$data['shop_type'] = 'B';
                    else $data['shop_type'] = 'C';
                    $data['volume'] = $item['Sales_num'];
                    //$data['dsr'] = $item['Dsr'];
                    $data['sellerId'] = $item['SellerID'];
                    $data['commission'] = 0;
                    if($item['Commission']<=$item['Commission_jihua'])$data['commission'] = $item['Commission_jihua'];
                    if($item['Commission_queqiao']>$data['commission'])$data['commission'] = $item['Commission_queqiao'];


                    //$data['jihua_link'] = $item['Jihua_link'];
                    //$data['que_siteid'] = $item['Que_siteid'];
                    //$data['jihua_shenhe'] = $item['Jihua_shenhe'];
                    $data['intro'] = $item['Introduce'];
                    $data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$item['Quan_id'] ."&pid=mm_110341117_13180074_52464478&itemId=535668601295&src=cd_cdll" ;
                    $data['quan'] = $item['Quan_price'];
                    //$data['quan_time'] = $item['Quan_time'];
                    $data['quan_surplus'] = $item['Quan_surplus'];
                    $data['quan_receive'] = $item['Quan_receive'];
                    $data['quan_condition'] = $item['Quan_condition'];
                    //$data['quan_m_link'] = $item['Quan_m_link'];
                    $data['quanurl'] = $item['Quan_link'];
                    $data['add_time'] = time();
                    //if($dataoke_model->where(array("iid"=>$item['GoodsID']))->find())
                    $da_item = $cun_model->where(array("num_iid"=>$data['num_iid']))->find();
                    if($da_item){
                        //$cun_item['']
                    }
                    else {
                        $url = "https://cunlist.taobao.com/?q=" . $data['num_iid'];
                        $content = http_get_content($url);
                        if(preg_match('/<div class=\"b1\">.*<\/div>/',$content,$match)){
                            $data['cun'] = '1';
                            $data['source'] = 'dataoke';

                            $cun_model->add($data);
                        }

                    }
                }
            }
            //sleep(3);
        }

    }

    public function get_taoyingke(){
        set_time_limit(0);
        $dataoke_model = M('CunItems');
$ccc = 0;
        for($p=1;$p<=110;$p++){
            echo $p;
            $str = "";
            $u = "http://120.76.76.124/api.php/api/goods/queryGoodsInfoInner";

            //$cookie = 'NZZDATA1258823063=76715795-1482110247-%7C1482110247; qtk_sin=vqjpmbc3cpd7fiv3ggu3h5mjo3; qtkwww_=9f8b7d357c8e2e558451a99314f15895e633cea9a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D';

            $header[] = "Accept-Language: zh-CN,zh;q=0.8";
    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $u);
            //curl_setopt($ch, CURLOPT_COOKIE,$cookie);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //放在服务器上，会提示出错
            if(!empty($referfer)) curl_setopt($ch, CURLOPT_REFERER, $referfer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            $post['current_page'] = $p;
            $post['token'] = "1";
            //$post['pageSize'] = 20;
            if($post != "") {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }

            $str = curl_exec($ch);
//echo $return;
            curl_close($ch);

            //$str = file_get_contents($u);
//echo $str;
            $json = array();
            $json = json_decode($str,true);
            //print_r($json);
            if(count($json['data'])>0){

                foreach($json['data'] as $item){
if($item['goodsStatus'] == '2'){
    $data = array();
//$iids[$item['GoodsID']]++;
    $data['orig_id'] = $item['goodsId'];
    $data['num_iid'] = $item['correspondId'];
    $data['title'] = $item['goodsName'];



    $picdata = get_url_data($item['productImg']);

    $data['pic_url'] = str_replace('_430x430q90.jpg','',$picdata['realPicUrl']);




    $data['price'] = $item['presentPrice'];

    $data['coupon_start_time'] = strtotime($item['activityStartTime']);
    $data['volume'] = $item['monthlySales'];

    $data['coupon_end_time'] = strtotime($item['activityEndTime']);



    $data['commission_rate'] = $item['commissionRate'];


    $data['commission'] = $item['commission'];

    $data['intro'] = $item['introduction'];


    $data['quan'] = $item['couponAmount'];
    $data['coupon_price'] = $data['price'] - $data['quan'];
    $data['quan_surplus'] = $item['couponSurplus'];
    $data['quan_receive'] = $item['couponReceive'];

    $data['quanurl'] = $item['couponLink'];

    $urldata = get_url_data($item['couponLink']);
    if($urldata['activity_id'] == "")$quan_id = $urldata['activityId'];
    else $quan_id = $urldata['activity_id'];
    if($urldata['seller_id'] == "")$data['sellerId'] = $urldata['sellerId'];
    else $data['sellerId'] = $urldata['seller_id'];

    $data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_20542124_69830211&itemId=" . $data['num_iid'] . "&src=cd_cdll" ;
    if($item['goodsType'] == '1' && $item['LogisticsCost'] == '0'){
        $data['cun'] = '1';

    }
    $data['source'] = 'taoyingke';
    $data['add_time'] = time();

    $dataoke_item = $dataoke_model->where(array("num_iid"=>$item['correspondId']))->find();
    if($dataoke_item){
        if($dataoke_item['source'] == '2690')continue;
        else{
            if($data['cun'] == '1')
            $dataoke_result =$dataoke_model->where(array("num_iid"=>$item['correspondId']))->save($data);
        }

    }

    //continue;
    else
        $dataoke_result =$dataoke_model->add($data);

}
                }

            }
            //sleep(3);
        }
//print_r($iids);

echo $ccc;
    }

    public function get_cun_haodanku(){

        set_time_limit(0);
        echo "get_haodanku start";
        $dataoke_model = M('CunItems');
        //$dataokes = $dataoke_model->field('num_iid')->select();
        //$curtime = date("Y-m-d H:i:s");
        //foreach($dataokes as $dataoke){
         //   $iids[] = $dataoke['num_iid'];
        //}


        for($p=1;$p<10;$p++){
            $str = "";
            $u = "http://www.haodanku.com/Index/index/nav/1/starttime/7/cuntao/" . $p .".html?json=true";
            echo $u;
            $str = file_get_contents($u);
            if($str == '[]'|| $str == '')break;
            $json = json_decode($str,true);
            if($json){
                foreach($json as $item){

                    $data = array();

                    $data['num_iid'] = $item['itemid'];
                    $data['title'] = $item['itemtitle'];
                    $data['dtitle'] = $item['itemshorttitle'];
                    $data['intro'] = $item['itemdesc'];
                    $data['pic_url'] = $item['itempic'];

                   // $data['sellerId'] = $item['seller_id'];

                    $data['price'] = $item['itemprice'];
                    $data['quan'] = $item['couponmoney'];
                    $data['coupon_price'] = $item['itemprice'] - $item['couponmoney'];

                    if($item['ctrates'] != '' && $item['ctrates'] != '0.00'){
                        $data['cun'] = '1';
                        $data['commission_rate'] = $item['ctrates'];
                    }
                    else $data['commission_rate'] = $item['tkrates'];

                    //$data['quan_price'] = $item['couponmoney'];
                    $data['coupon_start_time'] = $item['couponstarttime'];
                    $data['coupon_end_time'] = $item['couponendtime'];

                    $data['quan_surplus'] = $item['couponsurplus'];
                    $data['quan_receive'] = $item['couponreceive'];
                    $data['quan_condition'] = $item['couponexplain'];

                    $data['quanurl'] = $item['couponurl'];


                    $urldata = get_url_data($item['couponurl']);
                    if($urldata['activity_id'] == "")$quan_id = $urldata['activityId'];
                    else $quan_id = $urldata['activity_id'];
                    if($urldata['seller_id'] != "")$data['sellerId'] = $urldata['seller_id'];
                    else $data['sellerId'] = $urldata['sellerId'];

                    $data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_20542124_69830211&itemId=" . $data['num_iid'] . "&src=cd_cdll" ;

                    $data['add_time'] = time();
                    $data['source'] = 'haodanku';
                    $dataoke_item = $dataoke_model->where(array("num_iid"=>$item['itemid']))->find();
                    if($dataoke_item){
                        if($dataoke_item['source'] != '2690')$dataoke_result =$dataoke_model->where(array("num_iid"=>$item['itemid']))->save($data);
                        else continue;
                    }

                    //continue;
                    else {
                        $dataoke_result =$dataoke_model->add($data);

                    }
                }
            }

        }
    }


    public function get_taoyingke_test(){
        set_time_limit(0);
$ccc=0;
        for($p=1;$p<=110;$p++){
            //echo $p;
            $str = "";
            $u = "http://120.76.76.124/api.php/api/goods/queryGoodsInfoInner";

            //$cookie = 'NZZDATA1258823063=76715795-1482110247-%7C1482110247; qtk_sin=vqjpmbc3cpd7fiv3ggu3h5mjo3; qtkwww_=9f8b7d357c8e2e558451a99314f15895e633cea9a%3A4%3A%7Bi%3A0%3Bs%3A4%3A%225147%22%3Bi%3A1%3Bs%3A12%3A%22pioul%40qq.com%22%3Bi%3A2%3Bi%3A31536000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D';

            $header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $u);
            //curl_setopt($ch, CURLOPT_COOKIE,$cookie);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //放在服务器上，会提示出错
            if(!empty($referfer)) curl_setopt($ch, CURLOPT_REFERER, $referfer);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            $post['current_page'] = $p;
            $post['token'] = "sldkjfowei";
            //$post['pageSize'] = 20;
            if($post != "") {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }

            $str = curl_exec($ch);
//echo $return;
            curl_close($ch);

            //$str = file_get_contents($u);
//echo $str;
            $json = array();
            $json = json_decode($str,true);
            //print_r($json);
            if(count($json['data'])>0){

                foreach($json['data'] as $item){
                    if($item['goodsStatus'] == '2'){
                        if($item['goodsType'] == '1'){
                            if($item['LogisticsCost'] == '-1')
                                $ccc ++;
                            //echo $item['correspondId'] . "\n";
                        }


                    }



                }

            }
            //sleep(3);
        }
//print_r($iids);

    }

    public function update_cun_quan(){
        set_time_limit(0);
        $taoke_model = M('CunItems');
        $count = $taoke_model->count();
        echo $count;
        //$page = $this->page($count, 100);
        $p = $count/100;

        for($i=0;$i<=$p+1;$i++) {
            echo $i;
            $limit = "'" . $i * 100 . ",100'";
            echo $limit;
            $items = $taoke_model
                ->limit($limit)
                ->select();

            foreach($items as $item){

                $data = get_url_data(str_replace("&amp;","&",$item['quanurl']));
                if($data['activity_id'] == "")$quan_id = $data['activityId'];
                else $quan_id = $data['activity_id'];
                if($data['seller_id'] != "")$sellerId = $data['seller_id'];
                else $sellerId = $data['sellerId'];
                $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $sellerId ."&activity_id=" . $quan_id;

                $iid = $item['num_iid'];

                $coupon_info = get_coupon_info($quan_link);

                if($coupon_info){
                    if($coupon_info['coupon_start_time'] == ""){
                        $taoke_model->where(array("num_iid"=>$iid))->delete();
                        echo $iid . "--" .$quan_link . "\n";
                        print_r($coupon_info);
                    }

                    else {
                        $coupon_info['quan_receive'] = $coupon_info['Quan_receive'];
                        $coupon_info['quan_surplus'] = $coupon_info['Quan_surplus'];
                        $coupon_info['quan_condition'] = $coupon_info['Quan_condition'];
                        unset($coupon_info['Quan_receive']);
                        unset($coupon_info['Quan_surplus']);
                        unset($coupon_info['Quan_condition']);

                        $taoke_model->where(array("num_iid"=>$iid))->save($coupon_info);
                    }


                }
else  {
    $taoke_model->where(array("num_iid"=>$iid))->delete();
    echo $quan_link . "\n";
    print_r($coupon_info);
}

            }
        }


        //$taoke_model->where("from_unixtime(coupon_end_time)<now()")->delete();
        //$items = $taoke_model->select();



    }


    public function update_dataoke_quan(){
        set_time_limit(0);
        $taoke_model = M('Items','cmf_','DB_DATAOKE');
        $count = $taoke_model->count();
        echo $count;
        //$page = $this->page($count, 100);
        $p = $count/100;

        for($i=0;$i<=$p+1;$i++) {
            echo $i;
            $limit = "'" . $i * 100 . ",100'";
            echo $limit;
            $items = $taoke_model
                ->limit($limit)
                ->select();

            foreach($items as $item){

                $data = get_url_data(str_replace("&amp;","&",$item['quanurl']));
                if($data['activity_id'] == "")$quan_id = $data['activityId'];
                else $quan_id = $data['activity_id'];

                $sellerId = $item['sellerid'];
                $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $sellerId ."&activity_id=" . $quan_id;

                $iid = $item['num_iid'];

                $coupon_info = get_coupon_info($quan_link);

                if($coupon_info){
                    if($coupon_info['coupon_start_time'] == ""){
                        $taoke_model->where(array("num_iid"=>$iid))->delete();
                        echo $iid . "--" .$quan_link . "\n";
                        print_r($coupon_info);
                    }

                    else {
                        $coupon_info['quan_receive'] = $coupon_info['Quan_receive'];
                        $coupon_info['quan_surplus'] = $coupon_info['Quan_surplus'];
                        $coupon_info['quan_condition'] = $coupon_info['Quan_condition'];
                        unset($coupon_info['Quan_receive']);
                        unset($coupon_info['Quan_surplus']);
                        unset($coupon_info['Quan_condition']);

                        $taoke_model->where(array("num_iid"=>$iid))->save($coupon_info);
                    }


                }
                else  {
                    //$taoke_model->where(array("num_iid"=>$iid))->delete();
                    echo $quan_link . "\n";
                    print_r($coupon_info);
                }

            }
        }


        //$taoke_model->where("from_unixtime(coupon_end_time)<now()")->delete();
        //$items = $taoke_model->select();



    }
}


