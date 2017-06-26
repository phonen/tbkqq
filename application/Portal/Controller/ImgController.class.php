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

define('PNG_TEMPLATE', "./model.jpg");//模板文件
define('PNG_FONT',"./msyh.ttc" );//字体


define('PNG_PIC_RECT', serialize(array(0,0,444,396)));//图片、、一个矩行的左上角和右下角
define('PNG_QR_RECT', serialize(array(65,590,183,709)));//二维码

define('PNG_TITLE_TEXT', serialize(array(20,397,424,473)));//标题
define('PNG_PRICE_TEXT', serialize(array(269,595,303,608)));//原价
define('PNG_COUPON_PRICE_TEXT', serialize(array(379,595,409,608)));//优惠后价
define('PNG_DESC_TEXT', serialize(array(64,477,397,580)));//详情
define('PNG_COUPON_TEXT', serialize(array(298,640,398,684)));//优惠券

define('PNG_QR_LEVEL', QR_ECLEVEL_M);//二维码容错率
define('PNG_QR_SIZE', 3);//二维码大小




define ('DT_LEFT', 0);//二进制定位
define ('DT_RIGHT', 1);
define ('DT_CENTER', 2);
define ('DT_FLAG_HORZ', 3);

define ('DT_BOTTOM', 0);
define ('DT_TOP', 4);
define ('DT_VCENTER', 8);
define ('DT_FLAG_VERT', 12);
class ImgController extends HomebaseController {

    //首页
    public function index() {

        $id = $_GET['id'];
        if($id != ''){
            $itemurl = M("TbkqqTaokeItemurl")->where(array("id"=>$id))->find();
            if($itemurl){
                $url = $itemurl['shorturl'];
                $this->assign("url",$url);

                if($this->is_weixin()){
                    $item = M("TbkqqTaokeItem")->where(array("iid"=>$itemurl['iid']))->find();
                    if($item){
                        $this->assign("item",$item);

                    }
                    $this->display(":dwz_index_wx");
                }
                else {
                    $this->display(":dwz_index");
                }


            }
            else {
                $itemurl = M("TbkqqTaokeItemurlHistory")->where(array("id"=>$id))->find();
                if($itemurl){
                    $url = $itemurl['shorturl'];
                    $this->assign("url",$url);

                    if($this->is_weixin()){
                        $item = M("TbkqqTaokeItem")->where(array("iid"=>$itemurl['iid']))->find();
                        if($item){
                            $this->assign("item",$item);

                        }
                        $this->display(":dwz_index_wx");
                    }
                    else {
                        $this->display(":dwz_index");
                    }


                }
            }
        }
    }

    public function quan0(){
        $id = $_GET['id'];
        $baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?a=quan&id=";
        $dwz = M("TbkqqDwz")->where("id=$id")->find();
        if($dwz){
            $this->assign("url",$dwz['url']);
            if($this->is_weixin()){
                /*
                if($_GET['uid'] != ""){
                    $itemurl = M("TbkqqTaokeItemurl")->where(array("id"=>$_GET['uid']))->find();
                    if($itemurl){
                        $this->assign("shorturl",$baseurl . $id);
                        $this->assign("iid",$itemurl['iid']);
                        $this->display(":dwz_quan_wx1");
                    }
                }
                */
                $this->display(":dwz_quan_wx");
            }
            else{
                header("location:" . $dwz['url']);
            }
        }

    }

    public function quan(){
        $baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?id=";
        $quanurl = "http://dwz." . C("BASE_DOMAIN") . "/?a=quan&id=";
        $id = $_GET['id'];
        $uid = $_GET['uid'];
        if(is_numeric($id)){

            if($this->is_weixin()){
                $this->assign("url",$quanurl . $id . "&uid=" . $uid);
                $this->display(":dwz_quan_wx");
            }
            else {
                if($_GET['uid'] != ""){
                    if(is_numeric($uid)){
                        $itemurl = M("TbkqqTaokeItemurl")->where(array("id"=>$_GET['uid']))->find();
                        if($itemurl){

                            $this->assign("url",$quanurl . $id);
                            $this->assign("shorturl",$baseurl . $_GET['uid']);
                            $this->assign("iid",$itemurl['iid']);
                            $this->display(":dwz_quan_wx1");
                        }
                        else{
                            $itemurl = M("TbkqqTaokeItemurlHistory")->where(array("id"=>$_GET['uid']))->find();
                            if($itemurl){

                                $this->assign("url",$quanurl . $id);
                                $this->assign("shorturl",$baseurl . $_GET['uid']);
                                $this->assign("iid",$itemurl['iid']);
                                $this->display(":dwz_quan_wx1");
                            }
                        }
                    }

                }
                else {
                    $dwz = M("TbkqqDwz")->where("id=$id")->find();
                    if($dwz){
                        if($dwz['type'] == '1'){
                            if(sp_is_mobile()){
                                $this->assign("url",$dwz['url']);
                                $this->display(":dwz_index");
                                exit();
                            }
                        }
                        //$this->assign("url",$dwz['url']);
                        //if($this->is_weixin()){
                        //	$this->display(":dwz_quan_wx");
                        //}
                        //else{
                        header("location:" . $dwz['url']);
                        //}
                    }
                }
            }
        }
    }

    public function erwei(){
        $iid = $_GET['id'];
        $dataoke_model = M('CunItems');
        $item =$dataoke_model->where("num_iid=$iid")->find();
        if($item) {
            header("Content-type: image/jpg");
            if($item['dtitle'] == '')$item['dtitle'] = $item['title'];
            $this->generatePic($item['dtitle'],$item['pic_url'],$item['click_url'],$item['price'],$item['coupon_price'],$item['intro']);

        }
    }

    public function qrcode($level=3,$size=4){
        $iid = $_GET['id'];
        $dataoke_model = M('CunItems');
        $item =$dataoke_model->where("num_iid=$iid")->find();
        if($item) {
            Vendor('phpqrcode.phpqrcode');

            $errorCorrectionLevel =intval($level) ;//容错级别
            $matrixPointSize = intval($size);//生成图片大小
//生成二维码图片
            //echo $_SERVER['REQUEST_URI'];
            $object = new \QRcode();
//			$object->png($item['quan_link'],false, $errorCorrectionLevel, $matrixPointSize, 2);
            $object->jpg($item['click_url'], "./itemimg/$iid.jpg", $level, $size);

            $qr_source = imagecreatefromjpeg("./itemimg/$iid.jpg");
            $qr_size = getimagesize("./itemimg/$iid.jpg");
            $target_img = imagecreatetruecolor(400,750);
            $white = imagecolorallocate($target_img,0xFF,0xFF,0xFF);
            $black = imagecolorallocate($target_img,0x00,0x00,0x00);
            imagefill($target_img, 0, 0, $white);

            $img = file_get_contents($item['pic_url']. "_400x400.jpg");
            $img_source = imagecreatefromstring($img);
            $img_size = getimagesizefromstring($img);

            imagecopy ($target_img,$img_source,0,0,0,0,$img_size[0],$img_size[1]);

            $str = $item['dtitle'] . "\n【原 价】 " . $item['price'] . "元 【券后价】 " . $item['coupon_price'] . "元\n 请使用手机村淘扫描下面二维码领券购买";
            imagettftext($target_img,15,0,2,420,$black,"./simkai.ttf",$str);
            imagecopy ($target_img,$qr_source,(400-$qr_size[0])/2,480,0,0,$qr_size[0],$qr_size[1]);
            imagejpeg($target_img);
        }

    }

    public function dtkimg(){
        $id = $_GET['id'];
        $taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
        $item = $taoke_model->where("id=$id")->find();
        if($item) {
            if($item['imgmemo'] == '')		$image = file_get_contents($item['img'] ."_290x290.jpg");  //假设当前文件夹已有图片001.jpg
            else {
                $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
                preg_match($preg, $item['imgmemo'], $imgArr);
                $imgurl = $imgArr[1];
                $image = file_get_contents($imgurl);
            }
            header('Content-type: image/jpg');
            echo $image;
        }
    }
    public function img_by_no(){
        $no = $_GET['no'];
        $item = M("TbkqqTaokeItem")->where(array("no"=>$no,"status"=>'1'))->find();
        if($item) {
            if($item['imgmemo'] == '')		$image = file_get_contents($item['img'] ."_290x290.jpg");  //假设当前文件夹已有图片001.jpg
            else {
                $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
                preg_match($preg, $item['imgmemo'], $imgArr);
                $imgurl = $imgArr[1];
                $image = file_get_contents($imgurl);
            }
            header('Content-type: image/jpg');
            echo $image;
        }
    }

    private function bitblt($dest,$src,$rect){//写入图片

        $pic = imagecreatefromstring(file_get_contents($src));

        $src_width = imagesx ($pic);
        $src_height = imagesy ($pic);

        $dest_width =  $rect[2]-$rect[0]+1;
        $dest_height = $rect[3]-$rect[1]+1;

        imagecopyresampled ( $dest, $pic, $rect[0], $rect[1], 0, 0, $dest_width, $dest_height, $src_width, $src_height);
    }

    private function drawText($dest,$text,$font,$size,$color,$text_rect,$alignment) {//写入文字


        $width = $text_rect[2]-$text_rect[0];

        $lines = $this->autowrap($size, 0, PNG_FONT, $text, $width);

        if ( !is_array($lines) ) $lines = [$lines];
        if ( is_array($lines) && count($lines)==0 ) return;

        $rect = ImageTTFBBox($size,0, $font, $lines[0]);
        $line_height = $rect[1]-$rect[5];




        $max_row = ceil(($text_rect[3]-$text_rect[1])/$line_height);
        $rows = $max_row>count($lines)?count($lines):$max_row;

        $height = $rows*$line_height;




        $align = $alignment & DT_FLAG_VERT;
        switch($align) {
            case DT_TOP:
                $y = $text_rect[1];
                break;
            case DT_BOTTOM:
                $y = $text_rect[3];
                break;
            case DT_VCENTER:
                $y = ($text_rect[3]-$text_rect[1]-$height)/2+$text_rect[1]+$line_height;
                break;
        }

        for( $i=0;$i<$rows;$i++ ) {

            $this->outText($dest,$lines[$i],$font,$size,$color,[$text_rect[0], $y, $text_rect[2], $y+$line_height],$alignment);
            $y += $line_height;

        }
    }

    private function outText($dest,$text,$font,$size,$color,$text_rect,$alignment) {//写入文字

        $rect = ImageTTFBBox($size,0, $font, $text);

        $width = $rect[4]-$rect[0];
        $height = $rect[1]-$rect[5];

        $x = 0;
        $y = $text_rect[1];

        $align = $alignment & DT_FLAG_HORZ;

        switch($align) {
            case DT_LEFT:
                $x = $text_rect[0];
                break;
            case DT_RIGHT:
                $x = $text_rect[2]-$width;
                break;
            case DT_CENTER:
                $x = ($text_rect[2]-$text_rect[0]-$width)/2+$text_rect[0];
                break;
        }


        imagettftext($dest, $size, 0, $x, $y, $color, $font, $text);
    }

    private function generatePic($dtitle, $pic_url, $click_url, $price, $coupon_price, $intro) {

        $im = imagecreatefromjpeg (PNG_TEMPLATE);//导入模板

        $rect = unserialize(PNG_PIC_RECT);//导入图片
        $this->bitblt($im, $pic_url, $rect);

        $qr = $this->generateQR($click_url);
        $rect = unserialize(PNG_QR_RECT);
        $this->bitblt($im, $qr, $rect);//导入二维码

        $color = imagecolorallocate($im, 0, 0, 0);
        $rect = unserialize(PNG_TITLE_TEXT);
        $this->drawText($im,$dtitle,PNG_FONT,14,$color,$rect,DT_VCENTER|DT_CENTER);//写入标题

        $color = imagecolorallocate($im, 162, 26, 48);
        $rect = unserialize(PNG_PRICE_TEXT);
        $this->drawText($im,number_format($price),PNG_FONT,14,$color,$rect,DT_LEFT|DT_VCENTER);//写入原始价格

        $rect = unserialize(PNG_COUPON_PRICE_TEXT);
        $this->drawText($im,number_format($coupon_price),PNG_FONT,14,$color,$rect,DT_LEFT|DT_VCENTER);//写入优惠价格

        $rect = unserialize(PNG_DESC_TEXT);
        $this->drawText($im,$intro,PNG_FONT,12,$color,$rect,DT_LEFT|DT_VCENTER);


        $rect = unserialize(PNG_COUPON_TEXT);
        $this->drawText($im,number_format($price-$coupon_price,1),PNG_FONT,36,$color,$rect,DT_LEFT|DT_VCENTER);//

        imagejpeg($im);
        imagedestroy($im);
        return;
    }


    private function generateQR($value) { //获得二维码
        Vendor('phpqrcode.phpqrcode');
        $object = new \QRcode();
        $file = "./itemimg/" . md5($value);
        if ( !file_exists($file)) {
            $object->png($value, $file, PNG_QR_LEVEL, PNG_QR_SIZE,2);
        }
        return $file;
    }

    private function autowrap($fontsize, $angle, $fontface, $string, $width) {//文字换行
        $content = "";
        $lines = [];
        // 将字符串拆分成一个个单字 保存到数组 letter 中
        preg_match_all("/./u",$string,$arr);
        $letter = $arr[0];
        foreach ($letter as $l) {
            $teststr = $content.$l;
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                array_push($lines, $content);
                $content = '';
            }
            $content .= $l;
        }
        if ( $content!=='') array_push($lines, $content);
        return $lines;
    }
}


