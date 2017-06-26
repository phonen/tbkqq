<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------

function export_csv($filename, $data){
	header("Content-type:text/csv");
	header("Content-Disposition:attachment;filename=".$filename);
	header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
	header('Expires:0');
	header('Pragma:public');
	echo $data;

}
function openhttp_header($url, $post='',$cookie='',$referfer='')
{
	$header[] = "Host: www.amazon.com";
//    $header[] = "Accept-Encoding: gzip, deflate, sdch";
	$header[] = "Accept-Language: zh-CN,zh;q=0.8";
	$header[] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";

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