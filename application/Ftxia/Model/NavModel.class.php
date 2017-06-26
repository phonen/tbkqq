<?php
namespace Common\Model;
use Common\Model\CommonModel;

class NavModel extends CommonModel {
    protected $connection = 'DB_DATAOKE';
    /**
     * 读取导航信息写入缓存
     */
    public function nav_cache() {
        $nav_list = array();
        $nav_data = $this->field('type,name,alias,icon,icon1,hot,des,link,target')->where('status=1')->order('ordid')->select();
        foreach ($nav_data as $val) {
            switch ($val['type']) {
                case 'main':
                    $nav_list['main'][] = $val;
                    break;
                
                case 'top':
                    $nav_list['top'][] = $val;
                    break;
					
				case 'foot':
                    $nav_list['foot'][] = $val;
                    break;	
					
				case 'other':
                    $nav_list['other'][] = $val;
                    break;	
					
				case 'wap':
                    $nav_list['wap'][] = $val;
                    break;	
            }
        }
        F('nav_list', $nav_list);
        return $nav_list;
    }

    /**
     * 更新则删除缓存
     */
    protected function _before_write(&$data) {
        F('nav_list', NULL);
    }

    /**
     * 删除也删除缓存
     */
    protected function _after_delete($data, $options) {
        F('nav_list', NULL);
    }
}