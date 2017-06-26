<?php
namespace Ftxia\Controller;
use Common\Controller\FtxiabaseController;
class JumpController extends FtxiabaseController {

    public function _initialize() {
        parent::_initialize();
		$this->_mod = D('Items');
		$this->_commentmod = M('ItemsComment','cmf_','DB_DATAOKE');
		$this->site_setting = get_site_setting();
    }

    /**
     * 淘宝跳转
     */
    public function index() {
		$appname = C("SITE_APPNAME");
		$proxyid = $_GET['u'];

		$site = M("TbkqqProxy")->where(array("proxy"=>$appname . $proxyid))->find();

		$id = I('id','', 'trim');
		$iid = $this->_mod->where(array('id'=>$id))->getField('num_iid');
		if(!$iid){
		$iid = $_GET['iid'];
		}
		$tpl = 'index';
		if($id){
			if(strlen($id)>9){
					$item = $this->_mod->where(array('num_iid' => $id))->find();
			}else{
				$item = $this->_mod->where(array('id' => $id))->find();
			}
			if(!$item){
				$item['num_iid'] = $id;	
			}
		}
		if($iid){
			$this->_mod  = M('Items','cmf_','DB_DATAOKE');
			$item = $this->_mod ->where(array('num_iid' => $iid))->find();
			if(!$item){
				$item['num_iid'] = $iid;	
			}
		}
		
		if($this->site_setting['ftx_click_ai']){
			$tpl = 'taobao';
			if( $item['click_url'] && 0 < strpos( $item['click_url'], "s.click" ) ){
				$this->jump_hidden_referer( $item['click_url'] );
			}else if ( 0 < strpos( $item['click_url'], "redirect.simba.taobao.com" ) ){
				$this->jump_hidden_referer( $item['click_url'] );
			}
		}

		$taodianjin = $this->site_setting['ftx_taojindian_html'];
		if(strpos($taodianjin,'text/javascript')){
			$pid = get_word($taodianjin,'pid: "','"');
		}else{
			$pid = $taodianjin;
		}
		$this->assign('pid', $site['pid']);
		$this->assign('item', $item);
		$this->assign('site', $site);
        $this->display($tpl);
    }
	public function jump_hidden_referer( $url, $wait = 0 )
    {
        $s = "<script language=\"javascript\">var iurl=\"".$url."\";document.write(\"<meta http-equiv=\\\"refresh\\\" content=\\\"0;url=\"+iurl+\"\\\" />\");</script>";
        if ( strpos( $_SERVER['HTTP_USER_AGENT'], "AppleWebKit" ) )
        {
            $s = "<script language=\"javascript\">var iurl=\"data:text/html;base64,".base64_encode( $s )."\";document.write(\"<meta http-equiv=\\\"refresh\\\" content=\\\"".$wait.";url=\"+iurl+\"\\\" />\");</script>";
        }
        else
        {
            $s = str_replace( "\"0;", "\"".$wait.";", $s );
        }
        echo $s;
        exit( );
    }
}