<?php

/**
 * 会员
 */
namespace Ihub\Controller;
use Common\Controller\AdminbaseController;
class IndexadminController extends AdminbaseController {
    function index(){
        $this->_lists();
    }
    function add(){
        $this->display(":add");
    }
    function add_post(){
        $ihub_model=M("IhubUrl");
        if (IS_POST) {
            $urls = explode("\n",$_POST['post']['post_url']);
            foreach($urls as $url){
                $count = $ihub_model->where(array("url"=>$url))->count();
                if($count>=1) continue;
                $post['url'] = $url;
                $post['turl']=$_POST['post']['post_turl'];
                $post['intarval']=$_POST['post']['post_intarval'];
                $post['type']=$_POST['post']['post_type'];
                $post['description']=$_POST['post']['post_description'];
                $result = $ihub_model->add($post);
            }


            if ($result) {
                    $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }

        }
    }
    public function edit(){
        $id=  intval(I("get.id"));
        $ihub_model=M("IhubUrl");
        $ihub=$ihub_model->where("id=$id")->find();
        $this->assign("ihub",$ihub);
        $this->display(":edit");
    }

    public function edit_post()
    {
        if (IS_POST) {
            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            $_POST['post']['smeta'] = json_encode($_POST['smeta']);
            unset($_POST['post']['post_author']);
            $result = $this->posts_obj->save($_POST['post']);
            //echo($this->posts_obj->getLastSql());die;
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }
    private  function _lists(){
        /*
        $term_id=0;
        if(!empty($_REQUEST["term"])){
            $term_id=intval($_REQUEST["term"]);
            $term=$this->terms_obj->where("term_id=$term_id")->find();
            $this->assign("term",$term);
            $_GET['term']=$term_id;
        }
*/
//        $where_ands=empty($term_id)?array("a.status=$status"):array("a.term_id = $term_id and a.status=$status");
        $where_ands = array("1"=>"1");
        $fields=array(
            'type'=> array("field"=>"type","operator"=>"="),
            'description'  => array("field"=>"description","operator"=>"="),
            'url'  => array("field"=>"url","operator"=>"like"),
            'status'=> array("field"=>"status","operator"=>"="),
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
        }else{
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
        $ihub_model=M("IhubUrl");
        $count=$ihub_model->where($where)->count();
        $page = $this->page($count, 1000);
        $lists = $ihub_model
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign('lists', $lists);
        $this->assign("page", $page->show('Admin'));

        $this->display(":index");

        $this->assign("current_page",$page->GetCurrentPage());
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget",$_GET);
    }
    function stop(){
        if(isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if ($id) {
                $rst = M("IhubUrl")->where(array("id" => $id))->setField('status', 2);
                if ($rst) {
                    $this->success("停用成功！", U("index"));
                } else {
                    $this->error('停用失败！');
                }
            } else {
                $this->error('数据传入失败！');
            }
        }
        if(isset($_POST['ids'])) {
            $data['status']=2;
            $ids=join(",",$_POST['ids']);
            if (M('IhubUrl')->where("id in ($ids)")->save($data)) {
                $this->success("停用成功！");
            } else {
                $this->error("停用失败！");
            }
        }
    }
    
    function start(){
        if(isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if ($id) {
                $rst = M("IhubUrl")->where(array("id" => $id))->setField('status', '1');
                if ($rst) {
                    $this->success("启用成功！", U("index"));
                } else {
                    $this->error('启用失败！');
                }
            } else {
                $this->error('数据传入失败！');
            }
        }
        if(isset($_POST['ids'])) {
            $data['status']=1;
            $ids=join(",",$_POST['ids']);
            if (M('IhubUrl')->where("id in ($ids)")->save($data)) {
                $this->success("启用成功！");
            } else {
                $this->error("启用失败！");
            }
        }
    }
    function delete(){
        if(isset($_GET['id'])){
            $id = intval(I("get.id"));
            if (M('IhubUrl')->where("id=$id")->delete()) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if(isset($_POST['ids'])){
            $ids=join(",",$_POST['ids']);
            if (M('IhubUrl')->where("id in ($ids)")->delete()) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }
    function createxml(){
        $where=array('status'=>1);
        $ihub_model=M("IhubUrl");

        if(isset($_GET["redi"])){
            $where= join(" and ", array('type'=>'REDI'));
            $type_str = '<REDI-URL';
        }
        if(isset($_GET["push"])){
            $where= join(" and ", array('type'=>'PUSH'));
            $type_str = '<PUSH-URL';
        }
        $lists = $ihub_model
            ->where($where)
            ->select();
        $xmlc='';
        foreach($lists as $ihub){
            $xmlc .=$type_str . ' url="' . $ihub['url'] . '" id="' . $ihub['id'] . '" intarval="' . $ihub['intarval'] . '" start="0" end="86400" type="1" target="' . $ihub['turl'] . '"/>\n';
        }
        $this->assign('xmlc', $xmlc);
        $this->display(":createxml");
    }
}
