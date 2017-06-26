<?php

namespace Xiaoxue\Controller;
use Common\Controller\HomebaseController;
class PageController extends HomebaseController {
    public function index() {

        $item_model = M("XiaoxueInfo");
        $infos = $item_model->order("no")->select();
        $this->assign("infos",$infos);
        $tplname=isset($smeta['template'])?$smeta['template']:"";

        $tplname=sp_get_apphome_tpl($tplname, "page");

        $this->display(":$tplname");
    }

    public function add(){
        $this->display(":add");
    }
    public function add_post(){
        if (IS_POST) {
            $item=I("post.item");
            $item_model = M("XiaoxueInfo");
                $result=$item_model->add($item);
                if ($result) {
                    $this->success("添加成功！");
                } else {
                    $this->error("添加失败！");
                }
        }
    }
}
