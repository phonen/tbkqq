<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
function get_url_data($str){
    $data = array();
    $parameter = explode('&',end(explode('?',$str)));
    foreach($parameter as $val){
        $tmp = explode('=',$val);
        $data[$tmp[0]] = $tmp[1];
    }
    return $data;
}
/*
function get_item_url($clickurl){
    $headers = get_headers($clickurl, TRUE);
    $tu = $headers['Location'];
    $eturl = unescape($tu);
    $u = parse_url($eturl);
    $param = $u['query'];
    $ref = str_replace('tu=', '', $param);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ref);
    curl_setopt($ch, CURLOPT_REFERER, $tu);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch, CURLOPT_MAXREDIRS,2);
    $out = curl_exec($ch);
    $dd =  curl_getinfo($ch);
    curl_close($ch);
    $item_url = $dd['url'];
    return $item_url;
}
*/

function get_location($u){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $u);
    //curl_setopt($ch, CURLOPT_REFERER, $tu);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch, CURLOPT_MAXREDIRS,2);
    $out = curl_exec($ch);
    $dd =  curl_getinfo($ch);
    //curl_close($ch);
    $ref = $dd['url'];
    return $ref;
}

function get_item($u){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $u);
    //curl_setopt($ch, CURLOPT_REFERER, $tu);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch, CURLOPT_MAXREDIRS,2);
    $out = curl_exec($ch);
    $dd =  curl_getinfo($ch);
    //curl_close($ch);
    $ref = $dd['url'];

    $aa = explode('tu=',$ref);
    $tu = $aa[1];
    //$tu = $headers['Location'];
    $eturl = unescape($tu);
    //$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $eturl);
    curl_setopt($ch, CURLOPT_REFERER, $ref);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,false);
    curl_setopt($ch, CURLOPT_MAXREDIRS,2);
    $out = curl_exec($ch);
    $dd =  curl_getinfo($ch);
    curl_close($ch);

    $item_url = $dd['redirect_url'];
    return $data = get_url_data($item_url);

}


function get_dwz($url){
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,"http://dwz.cn/create.php");
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $data=array('url'=>$url . "&f=tth");
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    $strRes=curl_exec($ch);
    curl_close($ch);
    $arrResponse=json_decode($strRes,true);
    if($arrResponse['status']==0)
    {
        /**错误处理*/
        echo iconv('UTF-8','GBK',$arrResponse['err_msg'])."\n";
    }
    /** tinyurl */
    return $arrResponse['tinyurl']."\n";
}

function unescape($str) {
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i ++)
    {
        if ($str[$i] == '%' && $str[$i + 1] == 'u')
        {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f)
                $ret .= chr($val);
            else
                if ($val < 0x800)
                    $ret .= chr(0xc0 | ($val >> 6)) .
                        chr(0x80 | ($val & 0x3f));
                else
                    $ret .= chr(0xe0 | ($val >> 12)) .
                        chr(0x80 | (($val >> 6) & 0x3f)) .
                        chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else
            if ($str[$i] == '%')
            {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else
                $ret .= $str[$i];
    }
    return $ret;
}

function export_txt($filename, $data){
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-Disposition:attachment;filename=".$filename.".txt");
    header("Expires: 0");
    header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
    header( "Pragma:public ");

    echo $data;
}
function export_csv($filename, $data){
    header("Content-type:text/csv");
    header("Content-Disposition:attachment;filename=".$filename);
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');
    echo $data;

}

function openhttp_header($url, $post='',$cookie='',$referfer='',$head='')
{
    $header[] = "Host: pub.alimama.com";
//    $header[] = "Accept-Encoding: gzip, deflate, sdch";
    $header[] = "Accept-Language: zh-CN,zh;q=0.8";
    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
    if($head == '1') {
        $header[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $header[] = "Accept: application/json, text/javascript, */*; q=0.01";
    }
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

function convert_dwz($matches,$type = '2'){
    $baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?a=quan&id=";

    $dwz = M("TbkqqDwz")->where(array("url"=>$matches))->find();
    if($dwz){
        $id = $dwz['id'];
    }
    else {
        $id = M("TbkqqDwz")->add(array("url"=>$matches,"type"=>$type));

    }
    return $baseurl . $id;
}

function is_weixin(){
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }
    return false;
}

function convert_a($matches){
    return "<a href='" . $matches . "' >" . $matches . "</a>";
}

function excookie($cookies){
    $cookies_arr = explode(";",$cookies);
    foreach($cookies_arr as $data){

    }

    $data = array();
    $parameter = explode(";",$cookies);
    foreach($parameter as $val){
        $tmp = explode('=',$val);
        $data[ltrim($tmp[0])] = $tmp[1];
    }
    return $data;
}

function create_password($pw_length = 6){
    $randpwd = '';
    for ($i = 0; $i < $pw_length; $i++)
    {
        $randpwd .= chr(mt_rand(33, 126));
    }
    return $randpwd;
}

function campaign($cookie,$iid,$s){
    $u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
    $str = openhttp_header($u, '', $cookie);
    $arr = json_decode($str, true);
    $t = time();

    if ($arr['ok'] == '1' && $arr['data']) {
        $rate = 0;
        $cid = '';
        $keeperid = '';
        $post = array();

        foreach ($arr['data'] as $data) {
            if ($data['commissionRate'] > $rate) {
                $rate = $data['commissionRate'];
                $cid = $data['CampaignID'];
                $keeperid = $data['ShopKeeperID'];
            }
        }
        $post['campId'] = $cid;
        $post['keeperid'] = $keeperid;
        $post['applyreason'] = "淘特惠淘客推广申请";
        $cookie_data = excookie($cookie);
        $post['_tb_token_'] = $cookie_data['_tb_token_'];
        $post['t'] = $t;


        $post_str = "campId=" .$post['campId'] . "&keeperid=" . $post['keeperid'] . "&applyreason=" . $post['applyreason'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
        //print_r($post);
        $u = "http://pub.alimama.com/pubauc/applyForCommonCampaign.json";
        $reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" .$s;
        sleep(1);
        $ret = openhttp_header($u,$post_str,$cookie,$reffer,'1');
        return $ret;
    }
}

function get_qingtaoke_quan($iid){

}

function get_taotoken($data){

    Vendor('TaobaoApi.TopSdk');
    date_default_timezone_set('Asia/Shanghai');
    $c = new TopClient;
    $c->appkey = C('TOKEN_APPKEY');
    $c->secretKey = C('TOKEN_SECRETKEY');
    $c->format = 'json';
    $req = new WirelessShareTpwdCreateRequest;
    $tpwd_param = new IsvTpwdInfo;
    $tpwd_param->ext="{\"xx\":\"xx\"}";
    $tpwd_param->logo=$data['logo'];
    $tpwd_param->text=$data['text'];
    $tpwd_param->url=$data['url'];
    $tpwd_param->user_id="24234234234";
    $req->setTpwdParam(json_encode($tpwd_param));
    $resp = $c->execute($req);

    return $resp->model;
}

function addslashes_deep($value) {
    $value = is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
    return $value;
}

function attach($attach, $type) {
    if (false === strpos($attach, 'http://')) {
        //本地附件
        //$site_setting = sp_get_site_setting();
//        return __ROOT__ . '/' . $site_setting['ftx_attach_path'] . $type . '/' . $attach;
        return __ROOT__ . '/data/upload/' . $type . '/' . $attach;
        //远程附件
        //todo...
    } else {
        //URL链接
        return $attach;
    }
}

function get_site_setting(){
    $site_setting = F("site_setting");
    if(empty($site_setting)){
        $site_setting = array();
        $res = D("Setting")->getField('name,data');
        foreach ($res as $key=>$val) {
            $site_setting['ftx_'.$key] = unserialize($val) ? unserialize($val) : $val;
        }
        F("site_setting", $site_setting);
    }
    // $site_options['site_tongji']=htmlspecialchars_decode($site_options['site_tongji']);
    return $site_setting;
}

/**
 * 获取后台管理设置的网站信息，此类信息一般用于前台
 */
function sp_get_site_setting(){
    get_site_setting();
}

function get_thumb($img, $suffix = '_thumb') {
    if (false === strpos($img, 'http://')) {
        $ext = array_pop(explode('.', $img));
        $thumb = $img;
    } else {
        if (false !== strpos($img, 'taobaocdn.com') || false !== strpos($img, 'taobao.com') || false !== strpos($img, 'alicdn.com')|| false !== strpos($img, 'tbcdn.cn')) {
            //淘宝图片 _s _m _b
            switch ($suffix) {
                case '_s':
                    $thumb = $img . '_100x100.jpg';
                    break;
                case '_g':
                    $thumb = $img . '_150x150.jpg';
                    break;
                case '_m':
                    $thumb = $img . '_240x240.jpg';
                    break;
                case '_b':
                    $thumb = $img . '_310x310.jpg';
                    break;
                case '_a':
                    $thumb = $img . '_320x320.jpg';
                    break;
                case '_t':
                    $thumb = $img . '_350x350.jpg';
                    break;
                case '_p':
                    $thumb = $img . '_200x200.jpg';
                    break;
            }
        }else{
            $thumb = $img;
        }
    }
    return $thumb;
}

function newicon($time){
    $date = '';
    if (date('Y-m-d') == date('Y-m-d',$time)){
        $date = '<span class="new-icon">新品</span>';
    }
    return $date;
}

function wapnewicon($time){
    $date = '';
    if (date('Y-m-d') == date('Y-m-d',$time)){
        $date = '<span class="wapnewicon">新品</span>';
    }
    return $date;
}

function vmwan($volume)
{
    $wan = $volume / 10000;
    $wan = number_format($wan, 1);
    return $wan . '万';
}

/**
 * 获取用户头像
 */
function avatar($uid, $size) {
    $site_setting = get_site_setting();
    $avatar_size = explode(',', $site_setting['ftx_avatar_size']);
    $size = in_array($size, $avatar_size) ? $size : '100';
    $avatar_dir = avatar_dir($uid);
    $avatar_file = $avatar_dir . md5($uid) . "_{$size}.jpg";
    if (!is_file($site_setting['ftx_attach_path'] . 'avatar/' . $avatar_file)) {
        $avatar_file = "default_{$size}.jpg";
    }
    return __ROOT__ . '/' . $site_setting['ftx_attach_path'] . 'avatar/' . $avatar_file;
}

function avatar_dir($uid) {
    $uid = abs(intval($uid));
    $suid = sprintf("%09d", $uid);
    $dir1 = substr($suid, 0, 3);
    $dir2 = substr($suid, 3, 2);
    $dir3 = substr($suid, 5, 2);
    return $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
}

function getlike($uid) {
    $itemlike = M('ItemsLike','cmf_','DB_DATAOKE')->where(array('uid' => $uid))->count();
    return $itemlike;
}

function get_word($html,$star,$end){
    $pat = '/'.$star.'(.*?)'.$end.'/s';
    if(!preg_match_all($pat, $html, $mat)) {
    }else{
        $wd= $mat[1][0];
    }
    return $wd;
}

