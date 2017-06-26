<?php
/**
 * 文章模板标签解析
 *
 * @author andery
 */
class tehuiTag {

	public function lists($options) {
        $tehui_mod = M('tehui');
        $options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '4';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'add_time desc';	
		if($options['cid']){			
		$id_arr = D('tehui_cate')->get_child_ids($options['cid'], true);
		$map['cate_id'] = array('IN', $id_arr);
		}
		$map['pass'] = '1';
        $data = $tehui_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
        return $data;
    }
	    

    public function cate($options) {
        $options['field'] = isset($options['field']) ? trim($options['field']) : '*';
        $options['cateid'] = isset($options['cateid']) ? intval($options['cateid']) : 0;
        $options['where'] = isset($options['where']) ? trim($options['where']) : '';
        $options['num'] = isset($options['num']) ? intval($options['num']) : 0;
        $options['order'] = isset($options['order']) ? trim($options['order']) : 'ordid';
        if ($options['field'] != '*' || $options['where'] || $options['order'] != 'ordid') {
            $tehui_cate_mod = M('tehui_cate');
            $map = array('status'=>'1');
            $select = $tehui_cate_mod->field($options['field']); //字段
            $options['cateid'] && $map['pid'] = $options['cateid'];
            $options['where'] && $map['_string'] = $options['where'];
            $select->where($map); //条件
            $options['num'] && $select->limit($options['num']); //个数
            $select->order($options['order']); //排序
            $data = $select->select();
        } else {
            //分类缓存
            if (false === $cate_list = F('artcate_list')) {
                $cate_list = D('tehui_cate')->cate_cache();
            }
            if ($options['cateid'] == 0) {
                $cate_list = $cate_list['p'];
            } else {
                $cate_list = $cate_list['s'][$options['cateid']];
            }
            $options['num'] && $cate_list = array_slice($cate_list, 0, $options['num']);
            $data = $cate_list;
        }
        return $data;
    }
}