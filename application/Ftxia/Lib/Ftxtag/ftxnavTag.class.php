<?php
/**
 * 导航标签
 */
namespace Ftxia\Lib\Ftxtag;

class ftxnavTag {
    /**
     * 导航列表
     * @param array $options 
     */
    public function lists($options) {

		$options['field'] = isset($options['field']) ? trim($options['field']) : '*';
        $options['style'] = isset($options['style']) ? trim($options['style']) : 'main';
        $options['num'] = isset($options['num']) ? intval($options['num']) : 0;
        $options['order'] = isset($options['order']) ? trim($options['order']) : 'ordid';

        if ( $options['field'] != '*' || $options['order'] != 'ordid') {
            $nav_mod = M('Nav');
            $select = $nav_mod->field($field);
            $map = array('status'=>'1');
            $options['style'] && $map = array('type' => $options['style']); //导航类型
            $select->where($map);
            $options['num'] && $select->limit($options['num']); //数量
            $select->order($options['order']); //排序
            $data = $select->select();
        } else {
            if (false === $nav_list = F('nav_list')) {
                $nav_list = D('Nav')->nav_cache();
            }
            $nav_list = $nav_list[$options['style']];
            $options['num'] && $nav_list = array_slice($nav_list, 0, $options['num']);
            $data = $nav_list;
        }
        foreach ($data as $key=>$val) {
            switch ($val['alias']) {
				case 'index':
                    $data[$key]['link'] = U('index/index');
                    break;
				case 'cate':
                    $data[$key]['link'] = U('index/cate');
                    break;
                case 'gift':
                    $data[$key]['link'] = U('gift/index');
                    break;
				case 'article':
                    $data[$key]['link'] = U('article/index');
                    break;
				case 'help':
                    $data[$key]['link'] = U('help/index');
                    break;
                case 'jiu':
                    $data[$key]['link'] = U('jiu/index');
                    break;
				case 'shijiu':
                    $data[$key]['link'] = U('shijiu/index');
                    break;
				case 'meilishuo':
                    $data[$key]['link'] = U('meilishuo/index');
                    break;
				case 'tehui':
                    $data[$key]['link'] = U('tehui/index');
                    break;	
				case 'dapei':
                    $data[$key]['link'] = U('dapei/index');
                    break;
				case 'yugao':
                    $data[$key]['link'] = U('yugao/index');
                    break;				
				case 'tag':
                    $data[$key]['link'] = U('tag/index');
                    break;
				case 'brand':
                    $data[$key]['link'] = U('brand/index');
                    break;
				case 'sitemap':
                    $data[$key]['link'] = U('sitemap/index');
                    break;	
            }
        }
        return $data;
    }
}