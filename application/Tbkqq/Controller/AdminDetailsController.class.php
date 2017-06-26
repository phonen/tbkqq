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
class AdminDetailsController extends AdminbaseController {
    function _initialize() {
		parent::_initialize();
    }

    public function details_clean(){
        if(IS_POST) {
            $startdate = $_POST['startdate'];
            $enddate = $_POST['enddate'];
            $username = $_POST['username'];
            if($startdate == "")$this->error("删除失败，请选择时间！");
            else {
                if($enddate == "")	$where = "username='" . $username ."' and ctime>='" . $startdate. "'";
                else $where = "username='" . $username ."' and ctime>='" . $startdate. "' and ctime<'" .$enddate . "'";
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
                $username = $_POST['username'];
                if ($username != "") {

                    $this->details_import($filename, $exts,$username);
                    $this->success("导入成功");
                }
                $this->error("没有选择帐号");
            }
        }
    }

    protected function details_import($filename,$exts,$username){
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
            $data['username'] = $username;
            M("TbkqqTaokeDetails")->add($data);
        }

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


}
