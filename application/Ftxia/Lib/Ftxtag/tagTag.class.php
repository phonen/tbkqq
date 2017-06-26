<?php
/**
 * 文章模板标签解析
 *
 * @author andery
 */
namespace Ftxia\Lib\Ftxtag;
class tagTag {

	public function lists($options) {
        $tag_mod = M('Tag','cmf_','DB_DATAOKE');
		$options['num'] = isset($options['num']) ? trim($options['num']) : '4';
		$options['order'] = isset($options['order']) ? trim($options['order']) : 'id desc';
		$map['pass'] = '1';
        $data = $tag_mod->where($map)->limit('0 ,' . $options['num'])->order($options['order'])->select();
        return $data;
    }
}