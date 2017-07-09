<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Tbkqq\Controller;
use Common\Controller\HomebaseController;
/**
 * 首页
 */
class QingController extends HomebaseController {

    //首页
    public function index() {
        $proxyid = $_GET['id'];
        $f = $_GET['f'];
        $hour = date("H",time());
//		if($hour>='09' && $hour<'19' && $f != "tth"){
        if(1!=1){
            $this->display();
        }
        else{
            $appname = C("SITE_APPNAME");
            $proxy = M("TbkqqProxy",'cmf_','DB_DATAOKE')->where(array("proxy"=>$appname . $proxyid))->find();
            if($proxy){
                import("Org.Util.HttpHelper");
                import("Org.Util.CacheHelper");
                import("Org.Util.simple_html_dom");
                $item_model = M("TbkqqTaokeItem");
                $appId = $proxy['appid']; // 站点的APPID
                $appKey = $proxy['appkey'];// 站点的APP KEY
                $siteName = $proxy['sitename']; // 站点的名称
                $logoImagePath = $proxy['logo']; // 站点的LOGO图片地址
                if($proxy['cmshost'] != '')$host = $proxy['cmshost'];
                else $host = "http://cms3.qingtaoke.com";

                $requestMethod = strtoupper(@$_SERVER["REQUEST_METHOD"]);
                $requestUrl = @$_SERVER["REQUEST_URI"];

                $cache = new \CacheHelper($proxyid);

                if (isset($_REQUEST['clean'])) {
                    $cache->clean();
                    echo '已清除缓存';
                    exit;
                }
                $key = md5($requestUrl . \CacheHelper::isMobile() . \CacheHelper::isIPad() . \CacheHelper::isIPhone() . \CacheHelper::isMicroMessenger());
                if ($requestMethod == 'GET') {
                    $cacheData = $cache->Get($key);
                    if ($cacheData !== false) {
                        echo $cacheData;
                        exit;
                    }
                }

                $documentUrl = @$_SERVER["PHP_SELF"];
                $httpHelper = new \HttpHelper($appId, $appKey, $siteName, $logoImagePath, $documentUrl);
                $html = $httpHelper->getHtml($host, $requestUrl, $requestMethod == 'POST' ? @$_POST : array(), $requestMethod);
                if ($requestMethod == 'GET' && !empty($html)) {
                    $cache->Set($key, $html, 60);
                }
                echo $html;
            }
        }

    }

    public function test(){
        print_r($_SESSION['user']);
    }

    public function item_view(){
        if(sp_is_user_login()){
            $proxyid = strstr($_SESSION['user']['user_login'], 'i');
            $items = M("TbkqqTaokeItem")->where(array("status" => "1"))->select();
            foreach ($items as $item) {
                $where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
                $itemurls = M("TbkqqTaokeItemurl")->where($where)->select();
                $urls = "";
                foreach ($itemurls as $itemurl) {
                    $urls .= $itemurl['proxyid'] . "  " . $itemurl['shorturl'] . "<br>";
                }
                $item['urls'] = $urls;
                $data[] = $item;
            }
            $this->assign("items", $data);
            $this->display();
        }
    }
}


