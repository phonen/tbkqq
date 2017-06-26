<?php
namespace Ftxia\Model;
use Common\Model\CommonModel;
class SettingModel extends CommonModel {
    protected $connection = 'DB_DATAOKE';

    /**
     * 获取配置信息写入缓存
     */
    public function setting_cache() {
        $setting = array();
        $res = $this->getField('name,data');
        foreach ($res as $key=>$val) {
            $setting['ftx_'.$key] = unserialize($val) ? unserialize($val) : $val;
        }
        S('site_setting', $setting);
        return $setting;
    }

    /**
     * 后台有更新则删除缓存
     */
    protected function _before_write($data, $options) {
        S('site_setting', NULL);
    }
}