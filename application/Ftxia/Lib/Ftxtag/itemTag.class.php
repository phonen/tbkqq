<?php
/**
 * 宝贝标签
 */
namespace Ftxia\Lib\Ftxtag;
class itemTag {    

    public function orlike($options) {
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$items_mod = D('Items');
		$items_cate_mod = D('ItemsCate');
		$map = array('pass'=>'1');
		if($options['cid']){
			$id_arr = $items_cate_mod->get_child_ids($options['cid'], true);
            $map['cate_id'] = array('IN', $id_arr);
		}

		if(C('ftx_orlike_shop_type')){$map['shop_type'] = C('ftx_orlike_shop_type');}

		if(C('ftx_orlike_time') == '1'){
			$map['coupon_start_time'] = array('egt',time());
		}elseif(C('ftx_orlike_time') =='2'){
			$map['coupon_start_time'] = array('elt',time());
		}
		if(C('ftx_orlike_end_time') == '1'){$map['coupon_end_time'] = array('egt',time());}



		if(C('ftx_orlike_ems') == '1'){$map['ems'] = '1';}

		if(C('ftx_orlike_mix_price')>0){$map['coupon_price'] = array('egt',C('ftx_orlike_mix_price'));}
		if(C('ftx_orlike_max_price')>0){$map['coupon_price'] = array('elt',C('ftx_orlike_max_price'));}
		if(C('ftx_orlike_mix_price')>0 && C('ftx_orlike_max_price')>0){$map['coupon_price'] =  array(array('egt',C('ftx_orlike_mix_price')),array('elt',C('ftx_orlike_max_price')),'and');}
		if(C('ftx_orlike_mix_volume')>0){$map['volume'] = array('egt',C('ftx_orlike_mix_volume'));}
		if(C('ftx_orlike_max_volume')>0){$map['volume'] = array('elt',C('ftx_orlike_max_volume'));}
		if(C('ftx_orlike_mix_volume')>0 && C('ftx_orlike_max_volume')>0){$map['volume'] = array(array('egt',C('ftx_orlike_mix_volume')),array('elt',C('ftx_orlike_max_volume')),'and');}

		$data = $items_mod->where($map)->limit('0,'.C('ftx_orlike_page_size').'')->order(C('ftx_orlike_sort'))->select();
        return $data;
    }


	public function zhi($options) {
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '1';
		$items_mod = D('Items');
		$items_cate_mod = D('ItemsCate');
		$map = array('pass'=>'1');
		$map = array('shop_type'=>'B');
		if($options['cid']){
			$id_arr = $items_cate_mod->get_child_ids($options['cid'], true);
            $map['cate_id'] = array('IN', $id_arr);
		}
		
		if(C('ftx_zhi_shop_type')){$map['shop_type'] = C('ftx_zhi_shop_type');}

		if(C('ftx_zhi_time') == '1'){
			$map['coupon_start_time'] = array('egt',time());
		}elseif(C('ftx_zhi_time') =='2'){
			$map['coupon_start_time'] = array('elt',time());
		}
		if(C('ftx_zhi_end_time') == '1'){$map['coupon_end_time'] = array('egt',time());}

		if(C('ftx_zhi_ems') == '1'){$map['ems'] = '1';}

		if(C('ftx_zhi_mix_price')>0){$map['coupon_price'] = array('egt',C('ftx_zhi_mix_price'));}
		if(C('ftx_zhi_max_price')>0){$map['coupon_price'] = array('elt',C('ftx_zhi_max_price'));}
		if(C('ftx_zhi_mix_price')>0 && C('ftx_zhi_max_price')>0){$map['coupon_price'] =  array(array('egt',C('ftx_zhi_mix_price')),array('elt',C('ftx_zhi_max_price')),'and');}

		if(C('ftx_zhi_mix_volume')>0){$map['volume'] = array('egt',C('ftx_zhi_mix_volume'));}
		if(C('ftx_zhi_max_volume')>0){$map['volume'] = array('elt',C('ftx_zhi_max_volume'));}
		if(C('ftx_zhi_mix_volume')>0 && C('ftx_zhi_max_volume')>0){$map['volume'] = array(array('egt',C('ftx_zhi_mix_volume')),array('elt',C('ftx_zhi_max_volume')),'and');}

		$data = $items_mod->where($map)->limit('0,'.$options['num'])->order(C('ftx_zhi_sort'))->select();
        return $data;
    }

	/**
	 *	status  0：默认 1：不显示结束 2：只显示未开始
	 */

	public function lists($options) {
		$items_mod = D('Items');
		$map['pass'] = '1';
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '6';
		$options['status'] = isset($options['status']) ? trim($options['status']) : '1';
		$options['min_price'] = isset($options['min_price']) ? trim($options['min_price']) : '';
		$options['max_price'] = isset($options['max_price']) ? trim($options['max_price']) : '';
		$options['min_volume'] = isset($options['min_volume']) ? trim($options['min_volume']) : '';
		$options['max_volume'] = isset($options['max_volume']) ? trim($options['max_volume']) : '';

		if($options['min_price']>0){$map['coupon_price'] = array('egt',$options['min_price']);}
		if($options['max_price']>0){$map['coupon_price'] = array('elt',$options['max_price']);}
		if($options['min_price']>0 && $options['max_price']>0){$map['coupon_price'] = array(array('egt',$options['min_price']),array('elt',$options['max_price']),'and');}

		if($options['min_volume']>0){$map['volume'] = array('egt',$options['min_volume']);}
		if($options['max_volume']>0){$map['volume'] = array('elt',$options['max_volume']);}
		if($options['max_volume']>0 && $options['min_volume']>0){$map['volume'] = array(array('egt',$options['min_volume']),array('elt',$options['max_volume']),'and');}

		if($options['status'] == 1){
			$map['coupon_end_time'] = array('egt',time());
		}else if($options['status'] == 2){
			$map['coupon_start_time'] = array('egt',time());
		}
		if($options['cid']){
			$id_arr = D('ItemsCate')->get_child_ids($options['cid'], true);
			$map['cate_id'] = array('IN', $id_arr);
		}

        $data = $items_mod->where($map)->limit('0 ,' . $options['num'])->order(C('ftx_index_sort'))->select();
		return $data;
	}
	
	/**
	 *	status  0：默认 1：不显示结束 2：只显示未开始
	 */

	public function brand($options) {
		$branditems_mod = D('BrandItems');
		$map['pass'] = '1';		
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '8';
		$options['order'] = isset($options['order']) ? trim($options['order']) : '';			
		
		$map['activityId'] = $options['cid'];
			
        $data = $branditems_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;		
	}	
	
	public function hotbrand($options) {
		$brand_mod = D('Brand');
		$map['pass'] = '1';
		$map['hot'] = '1';
		$options['id'] = isset($options['id']) ? trim($options['id']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '8';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'id desc';		
		if($options['id']){			
		$id_arr = D('brand_cate')->get_child_ids($options['id'], true);
		$map['cate_id'] = array('IN', $id_arr);
		}		
        $data = $brand_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;		
	}	
	public function rebrand($options) {
		$brand_mod = D('Brand');
		$map['pass'] = '1';		
		$options['id'] = isset($options['id']) ? trim($options['id']) : '';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'id desc';	
		if($options['id']){			
		$id_arr = D('BrandCate')->get_child_ids($options['id'], true);
		$map['cate_id'] = array('IN', $id_arr);
		}		
        $data = $brand_mod->where($map)->order($options['order'])->select();
		return $data;		
	}
	public function morebrand($options) {
		$brand_mod = D('Brand');
		$map['pass'] = '1';		
		$options['id'] = isset($options['id']) ? trim($options['id']) : '';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'id desc';	
		$options['num'] = isset($options['num']) ? trim($options['num']) : '8';
		if($options['id']){			
		$id_arr = D('BrandCate')->get_child_ids($options['id'], true);
		$map['cate_id'] = array('IN', $id_arr);
		}		
        $data = $brand_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;		
	}
	public function fushibrand($options) {
		$brand_mod = D('Brand');
		$map['pass'] = '1';		
		$options['id'] = isset($options['id']) ? trim($options['id']) : '1';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'id desc';	
		$options['num'] = isset($options['num']) ? trim($options['num']) : '10';
		if($options['id']){			
		$id_arr = D('BrandCate')->get_child_ids($options['id'], true);
		$map['cate_id'] = array('IN', $id_arr);
		}		
        $data = $brand_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;		
	}
	public function muyingbrand($options) {
		$brand_mod = D('Brand');
		$map['pass'] = '1';		
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '5';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'id desc';	
		$options['num'] = isset($options['num']) ? trim($options['num']) : '15';
		if($options['cid']){			
		$id_arr = D('BrandCate')->get_child_ids($options['cid'], true);
		$map['cate_id'] = array('IN', $id_arr);
		}		
        $data = $brand_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;		
	}
	public function muying($options) {
		$brand_mod = D('BrandItems');
		$map['pass'] = '1';		
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '5';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'id desc';	
		$options['num'] = isset($options['num']) ? trim($options['num']) : '8';
		if($options['cid']){			
		$id_arr = D('BrandCate')->get_child_ids($options['cid'], true);
		$map['cate_id'] = array('IN', $id_arr);
		}		
        $data = $brand_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;		
	}
   public function tehui($options) {
        $tehui_mod = M('Tehui');
        $options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '4';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'add_time desc';	
		if($options['cid']){			
		$id_arr = D('TehuiCate')->get_child_ids($options['cid'], true);
		$map['cate_id'] = array('IN', $id_arr);
		}
		$map['pass'] = '1';
        $data = $tehui_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
        return $data;
    }
	public function items($options) {
        $items_mod = D('Items');
        $options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '3';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'add_time desc';	
		$options['status'] = isset($options['status']) ? trim($options['status']) : '1';
		if($options['status'] == 1){
			$map['coupon_end_time'] = array('egt',time());
		}else if($options['status'] == 2){
			$map['coupon_start_time'] = array('egt',time());
		}
		if($options['cid']){
			$id_arr = D('ItemsCate')->get_child_ids($options['cid'], true);
			$map['cate_id'] = array('IN', $id_arr);
		}
		$map['pass'] = '1';
        $data = $items_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
        return $data;
    }	
	public function dian($options) {
		$items_mod = D('Dian');
		$map['pass'] = '1';
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '12';
		$options['order'] = isset($options['order']) ? trim($options['order']) : '';
		if($options['cid']){
			$id_arr = D('ItemsCate')->get_child_ids($options['cid'], true);
			$map['cate_id'] = array('IN', $id_arr);
		}

        $data = $items_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;
	}
      public function dapei($options) {
		$dapei_mod = D('Dapei');
		$map['status'] = '1';
		$options['cid'] = isset($options['cid']) ? trim($options['cid']) : '';
		$options['num'] = isset($options['num']) ? trim($options['num']) : '5';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'add_time desc';
		$options['cid'] && $map['cate_id'] = $options['cid'];
        $data = $dapei_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
		return $data;
	}		
}