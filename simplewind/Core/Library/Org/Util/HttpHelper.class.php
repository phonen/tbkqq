<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/18
 * Time: 1:42
 */

class HttpHelper
{
    protected $appId;
    protected $key;
    protected $siteName;
    protected $logo;
    protected $documentUrl;

    public function __construct($appId, $key, $siteName, $logo, $documentUrl)
    {
        $this->appId = $appId;
        $this->key = $key;
        $this->siteName = $siteName;
        $this->logo = $logo;
        $this->documentUrl = $documentUrl;
    }


    /**
     * @param $url
     * @param $requestUrl
     * @param array $param
     * @param string $method
     * @param bool $isAjax
     * @param string $cookie
     * @param string $refer
     * @param null $userAgent
     * @return string
     */
    public function getHtml($url, $requestUrl, $param = array(), $method = 'GET', $isAjax = null, $cookie = NULL, $refer = null, $userAgent = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        empty($refer) && $refer = @$_SERVER['HTTP_REFERER'];
        $ua = $userAgent;
        empty($ua) && $ua = @$_SERVER['HTTP_USER_AGENT'];
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_REFERER, $refer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $header = array(
            'APPID: ' . $this->appId,
            'APPKEY: ' . $this->key,
            'CMS-HOST: ' . @$_SERVER["HTTP_HOST"],
            'SITE-NAME: ' . $this->siteName,
            'DOCUMENT-URL: ' . $this->documentUrl,
            'REQUEST-URL: ' . $requestUrl,
            'LOGO: ' . $this->logo,
        );
        $_isAjax = false;
        if ($isAjax) {
            $_isAjax = true;
        }
        if (!$_isAjax && $isAjax === null) {
            $_isAjax = $this->getIsAjaxRequest();
        }
        if ($_isAjax) {
            $header[] = 'X_REQUESTED_WITH: XMLHttpRequest';
        }
        $clientIp = $this->get_real_ip();
        if (!empty($clientIp)) {
            $header[] = 'CLIENT-IP: ' . $clientIp;
            $header[] = 'X-FORWARDED-FOR: ' . $clientIp;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if (empty($cookie)) {
            $cookie = $_COOKIE;
        }
        if (is_array($cookie)) {
            $str = '';
            foreach ($cookie as $k => $v) {
                $str .= $k . '=' . $v . '; ';
            }
            $cookie = $str;
        }
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if (strtolower($method) == 'post') {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            if ($param) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
            }
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
            if ($param) {
                $urlInfo = parse_url($url);
                $q = array();
                if (isset($urlInfo['query']) && !empty($urlInfo['query'])) {
                    parse_str($urlInfo['query'], $q);
                }
                $q = array_merge($q, $param);
                $cUrl = sprintf('%s://%s%s%s%s',
                    $urlInfo['scheme'],
                    $urlInfo['host'],
                    isset($urlInfo['port']) ? ':' . $urlInfo['port'] : '',
                    isset($urlInfo['path']) ? $urlInfo['path'] : '',
                    count($q) ? '?' . http_build_query($q) : '');
                curl_setopt($ch, CURLOPT_URL, $cUrl);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $r = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = mb_substr($r, 0, $headerSize);
        $r = mb_substr($r, $headerSize);
        curl_close($ch);
        unset($ch);
        $headers = explode("\r\n", $header);
        foreach ($headers as $h) {
            $h = trim($h);
            if (empty($h) || preg_match('/^(HTTP|Connection|EagleId|Server|X\-Powered\-By|Date|Transfer\-Encoding|Content)/i', $h)) {
                continue;
            }
            header($h);
        }
        return $r;
    }

    function get_real_ip()
    {
        if (@$_SERVER["HTTP_X_FORWARDED_FOR"]) {
            $ip = @$_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (@$_SERVER["HTTP_CLIENT_IP"]) {
            $ip = @$_SERVER["HTTP_CLIENT_IP"];
        } elseif (@$_SERVER["REMOTE_ADDR"]) {
            $ip = @$_SERVER["REMOTE_ADDR"];
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "";
        }
        return $ip;
    }

    public function getIsAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}