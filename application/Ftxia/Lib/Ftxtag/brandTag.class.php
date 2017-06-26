<?php
/**
 * 宝贝标签
 */
class brandTag {    


	public function lists($options) {
		$brand = D('brand');
		$brand_cate = D('brand_cate');
		$map['pass'] = '1';		
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '8';
		$options['order'] = isset($options['order']) ? trim($options['order']) : '';
		if($options['cid']){
			$id_arr = $brand_cate->get_child_ids($options['cid'], true);
            $map['cate_id'] = array('IN', $id_arr);
		}			
        $data = $brand->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;		
	}	
	public function newbrand($options) {
		$brand_mod = D('brand');
		$map['pass'] = '1';		
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '8';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'id desc';		
		if($options['id']){			
		$id_arr = D('brand_cate')->get_child_ids($options['cid'], true);
		$map['cate_id'] = array('IN', $id_arr);
		}		
        $data = $brand_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;		
	}	
		
}