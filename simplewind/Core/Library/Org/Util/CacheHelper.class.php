<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/18
 * Time: 1:40
 */

class CacheHelper
{
    protected $dir = '';

    public function __construct($proxyid)
    {
        $this->dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . $proxyid . '.cache';
        if (is_dir($this->dir)) {
            return;
        }
        @mkdir($this->dir);
    }

    public function Set($key, $value, $expire = 360)
    {
        $data = array(
            'time' => time(),
            'expire' => $expire,
            'value' => $value
        );
        @file_put_contents($this->dir . DIRECTORY_SEPARATOR . md5($key) . '.cache', serialize($data));
    }

    public function Get($key)
    {
        $file = $this->dir . DIRECTORY_SEPARATOR . md5($key) . '.cache';
        if (!file_exists($file)) {
            return false;
        }
        $str = @file_get_contents($file);
        if (empty($str)) {
            return false;
        }
        $data = @unserialize($str);
        if (!isset($data['time']) || !isset($data['expire']) || !isset($data['value'])) {
            return false;
        }
        if ($data['time'] + $data['expire'] < time()) {
            return false;
        }
        return $data['value'];
    }

    static function isMobile()
    {
        $ua = @$_SERVER['HTTP_USER_AGENT'];
        return preg_match('/(iphone|android|Windows\sPhone)/i', $ua);
    }

    public function clean()
    {
        if (!empty($this->dir) && is_dir($this->dir)) {
            @rmdir($this->dir);
        }
        $files = scandir($this->dir);
        foreach ($files as $file) {
            @unlink($this->dir . DIRECTORY_SEPARATOR . $file);
        }
    }


    static function isMicroMessenger()
    {
        $ua = @$_SERVER['HTTP_USER_AGENT'];
        return preg_match('/MicroMessenger/i', $ua);
    }

    static function isIPhone()
    {
        $ua = @$_SERVER['HTTP_USER_AGENT'];
        return preg_match('/iPhone/i', $ua);
    }

    static function isIPad()
    {
        $ua = @$_SERVER['HTTP_USER_AGENT'];
        return preg_match('/(iPad|)/i', $ua);
    }
}