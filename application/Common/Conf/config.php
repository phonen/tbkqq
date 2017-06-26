<?php
if(file_exists("data/conf/db.php")){
	$db=include "data/conf/db.php";
}else{
	$db=array();
}
if(file_exists("data/conf/config.php")){
	$runtime_config=include "data/conf/config.php";
}else{
    $runtime_config=array();
}

if (file_exists("data/conf/route.php")) {
    $routes = include 'data/conf/route.php';
} else {
    $routes = array();
}

$domains = array(
	'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名配置
	'APP_SUB_DOMAIN_RULES'    =>    array(
		'www.taotehui.co'    =>   array('Tbkqq/Qing','id=001'), // 二级泛域名指向Test模块
		'002.taotehui.co'	=> array('Tbkqq/Qing','id=002'),
		'003.taotehui.co' => array('Tbkqq/Qing','id=003'),
		'004.taotehui.co' => array('Tbkqq/Qing','id=004'),
		'005.taotehui.co' => array('Tbkqq/Qing','id=005'),
		'006.taotehui.co' => array('Tbkqq/Qing','id=006'),
		'007.taotehui.co' => array('Tbkqq/Qing','id=007'),
		'008.taotehui.co' => array('Tbkqq/Qing','id=008'),
		'009.taotehui.co' => array('Tbkqq/Qing','id=009'),
		'010.taotehui.co' => array('Tbkqq/Qing','id=010'),
		'011.taotehui.co' => array('Tbkqq/Qing','id=011'),
		'012.taotehui.co' => array('Tbkqq/Qing','id=012'),
		'013.taotehui.co' => array('Tbkqq/Qing','id=013'),
		'014.taotehui.co' => array('Tbkqq/Qing','id=014'),
		'015.taotehui.co' => array('Tbkqq/Qing','id=015'),
		'016.taotehui.co' => array('Tbkqq/Qing','id=016'),
		'017.taotehui.co' => array('Tbkqq/Qing','id=017'),
		'018.taotehui.co' => array('Tbkqq/Qing','id=018'),
		'019.taotehui.co' => array('Tbkqq/Qing','id=019'),
		'020.taotehui.co' => array('Tbkqq/Qing','id=020'),
		'021.taotehui.co' => array('Tbkqq/Qing','id=021'),
		'022.taotehui.co' => array('Tbkqq/Qing','id=022'),
		'023.taotehui.co' => array('Tbkqq/Qing','id=023'),
		'024.taotehui.co' => array('Tbkqq/Qing','id=024'),
		'025.taotehui.co' => array('Tbkqq/Qing','id=025'),
		'026.taotehui.co' => array('Tbkqq/Qing','id=026'),
		'027.taotehui.co' => array('Tbkqq/Qing','id=027'),
		'028.taotehui.co' => array('Tbkqq/Qing','id=028'),
		'029.taotehui.co' => array('Tbkqq/Qing','id=029'),
		'030.taotehui.co' => array('Tbkqq/Qing','id=030'),
		'031.taotehui.co' => array('Tbkqq/Qing','id=031'),
		'032.taotehui.co' => array('Tbkqq/Qing','id=032'),
		'033.taotehui.co' => array('Tbkqq/Qing','id=033'),
		'034.taotehui.co' => array('Tbkqq/Qing','id=034'),
		'035.taotehui.co' => array('Tbkqq/Qing','id=035'),
		'036.taotehui.co' => array('Tbkqq/Qing','id=036'),
		'037.taotehui.co' => array('Tbkqq/Qing','id=037'),
		'038.taotehui.co' => array('Tbkqq/Qing','id=038'),
		'039.taotehui.co' => array('Tbkqq/Qing','id=039'),
		'040.taotehui.co' => array('Tbkqq/Qing','id=040'),
		'041.taotehui.co' => array('Tbkqq/Qing','id=041'),
		'042.taotehui.co' => array('Tbkqq/Qing','id=042'),
		'043.taotehui.co' => array('Tbkqq/Qing','id=043'),
		'044.taotehui.co' => array('Tbkqq/Qing','id=044'),
		'045.taotehui.co' => array('Tbkqq/Qing','id=045'),
		'046.taotehui.co' => array('Tbkqq/Qing','id=046'),
		'047.taotehui.co' => array('Tbkqq/Qing','id=047'),
		'048.taotehui.co' => array('Tbkqq/Qing','id=048'),
		'049.taotehui.co' => array('Tbkqq/Qing','id=049'),
		'050.taotehui.co' => array('Tbkqq/Qing','id=050'),
		'051.taotehui.co' => array('Tbkqq/Qing','id=051'),
		'052.taotehui.co' => array('Tbkqq/Qing','id=052'),
		'053.taotehui.co' => array('Tbkqq/Qing','id=053'),
		'054.taotehui.co' => array('Tbkqq/Qing','id=054'),
		'055.taotehui.co' => array('Tbkqq/Qing','id=055'),
		'056.taotehui.co' => array('Tbkqq/Qing','id=056'),
		'057.taotehui.co' => array('Tbkqq/Qing','id=057'),
		'058.taotehui.co' => array('Tbkqq/Qing','id=058'),
		'059.taotehui.co' => array('Tbkqq/Qing','id=059'),
		'060.taotehui.co' => array('Tbkqq/Qing','id=060'),
		'061.taotehui.co' => array('Tbkqq/Qing','id=061'),
		'062.taotehui.co' => array('Tbkqq/Qing','id=062'),
		'063.taotehui.co' => array('Tbkqq/Qing','id=063'),
		'064.taotehui.co' => array('Tbkqq/Qing','id=064'),
		'065.taotehui.co' => array('Tbkqq/Qing','id=065'),
		'066.taotehui.co' => array('Tbkqq/Qing','id=066'),
		'067.taotehui.co' => array('Tbkqq/Qing','id=067'),
		'068.taotehui.co' => array('Tbkqq/Qing','id=068'),
		'069.taotehui.co' => array('Tbkqq/Qing','id=069'),
		'070.taotehui.co' => array('Tbkqq/Qing','id=070'),
		'071.taotehui.co' => array('Tbkqq/Qing','id=071'),
		'072.taotehui.co' => array('Tbkqq/Qing','id=072'),
		'073.taotehui.co' => array('Tbkqq/Qing','id=073'),
		'074.taotehui.co' => array('Tbkqq/Qing','id=074'),
		'075.taotehui.co' => array('Tbkqq/Qing','id=075'),
		'076.taotehui.co' => array('Tbkqq/Qing','id=076'),
		'077.taotehui.co' => array('Tbkqq/Qing','id=077'),
		'078.taotehui.co' => array('Tbkqq/Qing','id=078'),
		'079.taotehui.co' => array('Tbkqq/Qing','id=079'),
		'080.taotehui.co' => array('Tbkqq/Qing','id=080'),
		'081.taotehui.co' => array('Tbkqq/Qing','id=081'),
		'082.taotehui.co' => array('Tbkqq/Qing','id=082'),
		'083.taotehui.co' => array('Tbkqq/Qing','id=083'),
		'084.taotehui.co' => array('Tbkqq/Qing','id=084'),
		'085.taotehui.co' => array('Tbkqq/Qing','id=085'),
		'086.taotehui.co' => array('Tbkqq/Qing','id=086'),
		'087.taotehui.co' => array('Tbkqq/Qing','id=087'),
		'088.taotehui.co' => array('Tbkqq/Qing','id=088'),
		'089.taotehui.co' => array('Tbkqq/Qing','id=089'),
		'090.taotehui.co' => array('Tbkqq/Qing','id=090'),
		'091.taotehui.co' => array('Tbkqq/Qing','id=091'),
		'092.taotehui.co' => array('Tbkqq/Qing','id=092'),
		'093.taotehui.co' => array('Tbkqq/Qing','id=093'),
		'094.taotehui.co' => array('Tbkqq/Qing','id=094'),
		'095.taotehui.co' => array('Tbkqq/Qing','id=095'),
		'096.taotehui.co' => array('Tbkqq/Qing','id=096'),
		'097.taotehui.co' => array('Tbkqq/Qing','id=097'),
		'098.taotehui.co' => array('Tbkqq/Qing','id=098'),
		'099.taotehui.co' => array('Tbkqq/Qing','id=099'),
		'100.taotehui.co' => array('Tbkqq/Qing','id=100'),
		'101.taotehui.co'	=> array('Tbkqq/Qing','id=101'),
		'102.taotehui.co'	=> array('Tbkqq/Qing','id=102'),
		'103.taotehui.co' => array('Tbkqq/Qing','id=103'),
		'104.taotehui.co' => array('Tbkqq/Qing','id=104'),
		'105.taotehui.co' => array('Tbkqq/Qing','id=105'),
		'106.taotehui.co' => array('Tbkqq/Qing','id=106'),
		'107.taotehui.co' => array('Tbkqq/Qing','id=107'),
		'108.taotehui.co' => array('Tbkqq/Qing','id=108'),
		'109.taotehui.co' => array('Tbkqq/Qing','id=109'),
		'110.taotehui.co' => array('Tbkqq/Qing','id=110'),
		'111.taotehui.co' => array('Tbkqq/Qing','id=111'),
		'112.taotehui.co' => array('Tbkqq/Qing','id=112'),
		'113.taotehui.co' => array('Tbkqq/Qing','id=113'),
		'114.taotehui.co' => array('Tbkqq/Qing','id=114'),
		'115.taotehui.co' => array('Tbkqq/Qing','id=115'),
		'116.taotehui.co' => array('Tbkqq/Qing','id=116'),
		'117.taotehui.co' => array('Tbkqq/Qing','id=117'),
		'118.taotehui.co' => array('Tbkqq/Qing','id=118'),
		'119.taotehui.co' => array('Tbkqq/Qing','id=119'),
		'120.taotehui.co' => array('Tbkqq/Qing','id=120'),
		'121.taotehui.co' => array('Tbkqq/Qing','id=121'),
		'122.taotehui.co' => array('Tbkqq/Qing','id=122'),
		'123.taotehui.co' => array('Tbkqq/Qing','id=123'),
		'124.taotehui.co' => array('Tbkqq/Qing','id=124'),
		'125.taotehui.co' => array('Tbkqq/Qing','id=125'),
		'126.taotehui.co' => array('Tbkqq/Qing','id=126'),
		'127.taotehui.co' => array('Tbkqq/Qing','id=127'),
		'128.taotehui.co' => array('Tbkqq/Qing','id=128'),
		'129.taotehui.co' => array('Tbkqq/Qing','id=129'),
		'130.taotehui.co' => array('Tbkqq/Qing','id=130'),
		'131.taotehui.co' => array('Tbkqq/Qing','id=131'),
		'132.taotehui.co' => array('Tbkqq/Qing','id=132'),
		'133.taotehui.co' => array('Tbkqq/Qing','id=133'),
		'134.taotehui.co' => array('Tbkqq/Qing','id=134'),
		'135.taotehui.co' => array('Tbkqq/Qing','id=135'),
		'136.taotehui.co' => array('Tbkqq/Qing','id=136'),
		'137.taotehui.co' => array('Tbkqq/Qing','id=137'),
		'138.taotehui.co' => array('Tbkqq/Qing','id=138'),
		'139.taotehui.co' => array('Tbkqq/Qing','id=139'),
		'140.taotehui.co' => array('Tbkqq/Qing','id=140'),
		'141.taotehui.co' => array('Tbkqq/Qing','id=141'),
		'142.taotehui.co' => array('Tbkqq/Qing','id=142'),
		'143.taotehui.co' => array('Tbkqq/Qing','id=143'),
		'144.taotehui.co' => array('Tbkqq/Qing','id=144'),
		'145.taotehui.co' => array('Tbkqq/Qing','id=145'),
		'146.taotehui.co' => array('Tbkqq/Qing','id=146'),
		'147.taotehui.co' => array('Tbkqq/Qing','id=147'),
		'148.taotehui.co' => array('Tbkqq/Qing','id=148'),
		'149.taotehui.co' => array('Tbkqq/Qing','id=149'),
		'150.taotehui.co' => array('Tbkqq/Qing','id=150'),
		'151.taotehui.co' => array('Tbkqq/Qing','id=151'),
		'152.taotehui.co' => array('Tbkqq/Qing','id=152'),
		'153.taotehui.co' => array('Tbkqq/Qing','id=153'),
		'154.taotehui.co' => array('Tbkqq/Qing','id=154'),
		'155.taotehui.co' => array('Tbkqq/Qing','id=155'),
		'156.taotehui.co' => array('Tbkqq/Qing','id=156'),
		'157.taotehui.co' => array('Tbkqq/Qing','id=157'),
		'158.taotehui.co' => array('Tbkqq/Qing','id=158'),
		'159.taotehui.co' => array('Tbkqq/Qing','id=159'),
		'160.taotehui.co' => array('Tbkqq/Qing','id=160'),
		'161.taotehui.co' => array('Tbkqq/Qing','id=161'),
		'162.taotehui.co' => array('Tbkqq/Qing','id=162'),
		'163.taotehui.co' => array('Tbkqq/Qing','id=163'),
		'164.taotehui.co' => array('Tbkqq/Qing','id=164'),
		'165.taotehui.co' => array('Tbkqq/Qing','id=165'),
		'166.taotehui.co' => array('Tbkqq/Qing','id=166'),
		'167.taotehui.co' => array('Tbkqq/Qing','id=167'),
		'168.taotehui.co' => array('Tbkqq/Qing','id=168'),
		'169.taotehui.co' => array('Tbkqq/Qing','id=169'),
		'170.taotehui.co' => array('Tbkqq/Qing','id=170'),
		'171.taotehui.co' => array('Tbkqq/Qing','id=171'),
		'172.taotehui.co' => array('Tbkqq/Qing','id=172'),
		'173.taotehui.co' => array('Tbkqq/Qing','id=173'),
		'174.taotehui.co' => array('Tbkqq/Qing','id=174'),
		'175.taotehui.co' => array('Tbkqq/Qing','id=175'),
		'176.taotehui.co' => array('Tbkqq/Qing','id=176'),
		'177.taotehui.co' => array('Tbkqq/Qing','id=177'),
		'178.taotehui.co' => array('Tbkqq/Qing','id=178'),
		'179.taotehui.co' => array('Tbkqq/Qing','id=179'),
		'180.taotehui.co' => array('Tbkqq/Qing','id=180'),
		'181.taotehui.co' => array('Tbkqq/Qing','id=181'),
		'182.taotehui.co' => array('Tbkqq/Qing','id=182'),
		'183.taotehui.co' => array('Tbkqq/Qing','id=183'),
		'184.taotehui.co' => array('Tbkqq/Qing','id=184'),
		'185.taotehui.co' => array('Tbkqq/Qing','id=185'),
		'186.taotehui.co' => array('Tbkqq/Qing','id=186'),
		'187.taotehui.co' => array('Tbkqq/Qing','id=187'),
		'188.taotehui.co' => array('Tbkqq/Qing','id=188'),
		'189.taotehui.co' => array('Tbkqq/Qing','id=189'),
		'190.taotehui.co' => array('Tbkqq/Qing','id=190'),
		'191.taotehui.co' => array('Tbkqq/Qing','id=191'),
		'192.taotehui.co' => array('Tbkqq/Qing','id=192'),
		'193.taotehui.co' => array('Tbkqq/Qing','id=193'),
		'194.taotehui.co' => array('Tbkqq/Qing','id=194'),
		'195.taotehui.co' => array('Tbkqq/Qing','id=195'),
		'196.taotehui.co' => array('Tbkqq/Qing','id=196'),
		'197.taotehui.co' => array('Tbkqq/Qing','id=197'),
		'198.taotehui.co' => array('Tbkqq/Qing','id=198'),
		'199.taotehui.co' => array('Tbkqq/Qing','id=199'),
		'200.taotehui.co' => array('Tbkqq/Qing','id=200'),
		'dwz.taotehui.co'	=>	'Tbkqq/Dwz',
	),
);
$configs= array(
        "LOAD_EXT_FILE"=>"extend",
        'UPLOADPATH' => 'data/upload/',
        //'SHOW_ERROR_MSG'        =>  true,    // 显示错误信息
        'SHOW_PAGE_TRACE'		=> false,
        'TMPL_STRIP_SPACE'		=> true,// 是否去除模板文件里面的html空格与换行
        'THIRD_UDER_ACCESS'		=> false, //第三方用户是否有全部权限，没有则需绑定本地账号
        /* 标签库 */
        'TAGLIB_BUILD_IN' => THINKCMF_CORE_TAGLIBS,
        'MODULE_ALLOW_LIST'  => array('Admin','Portal','Asset','Api','User','Wx','Comment','Qiushi','Tpl','Topic','Install','Bug','Better','Pay','Cas'),
        'TMPL_DETECT_THEME'     => false,       // 自动侦测模板主题
        'TMPL_TEMPLATE_SUFFIX'  => '.html',     // 默认模板文件后缀
        'DEFAULT_MODULE'        =>  'Portal',  // 默认模块
        'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
        'DEFAULT_ACTION'        =>  'index', // 默认操作名称
        'DEFAULT_M_LAYER'       =>  'Model', // 默认的模型层名称
        'DEFAULT_C_LAYER'       =>  'Controller', // 默认的控制器层名称
        
        'DEFAULT_FILTER'        =>  'htmlspecialchars', // 默认参数过滤方法 用于I函数...htmlspecialchars
        
        'LANG_SWITCH_ON'        =>  true,   // 开启语言包功能
        'DEFAULT_LANG'          =>  'zh-cn', // 默认语言
        'LANG_LIST'				=>  'zh-cn,en-us,zh-tw',
        'LANG_AUTO_DETECT'		=>  false,
        
        'VAR_MODULE'            =>  'g',     // 默认模块获取变量
        'VAR_CONTROLLER'        =>  'm',    // 默认控制器获取变量
        'VAR_ACTION'            =>  'a',    // 默认操作获取变量
        
        'APP_USE_NAMESPACE'     =>   true, // 关闭应用的命名空间定义
        'APP_AUTOLOAD_LAYER'    =>  'Controller,Model', // 模块自动加载的类库后缀
        
        'SP_TMPL_PATH'     		=> 'themes/',       // 前台模板文件根目录
        'SP_DEFAULT_THEME'		=> 'simplebootx',       // 前台模板文件
        'SP_TMPL_ACTION_ERROR' 	=> 'error', // 默认错误跳转对应的模板文件,注：相对于前台模板路径
        'SP_TMPL_ACTION_SUCCESS' 	=> 'success', // 默认成功跳转对应的模板文件,注：相对于前台模板路径
        'SP_ADMIN_STYLE'		=> 'flat',
        'SP_ADMIN_TMPL_PATH'    => 'admin/themes/',       // 各个项目后台模板文件根目录
        'SP_ADMIN_DEFAULT_THEME'=> 'simplebootx',       // 各个项目后台模板文件
        'SP_ADMIN_TMPL_ACTION_ERROR' 	=> 'Admin/error.html', // 默认错误跳转对应的模板文件,注：相对于后台模板路径
        'SP_ADMIN_TMPL_ACTION_SUCCESS' 	=> 'Admin/success.html', // 默认成功跳转对应的模板文件,注：相对于后台模板路径
        'TMPL_EXCEPTION_FILE'   => SITE_PATH.'public/exception.html',
        
        'AUTOLOAD_NAMESPACE' => array('plugins' => './plugins/'), //扩展模块列表
        
        'ERROR_PAGE'            =>'',//不要设置，否则会让404变302
        
        'VAR_SESSION_ID'        => 'session_id',
        
        "UCENTER_ENABLED"		=>0, //UCenter 开启1, 关闭0
        "COMMENT_NEED_CHECK"	=>0, //评论是否需审核 审核1，不审核0
        "COMMENT_TIME_INTERVAL"	=>60, //评论时间间隔 单位s
        
        /* URL设置 */
        'URL_CASE_INSENSITIVE'  => true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
        'URL_MODEL'             => 0,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
        // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
        'URL_PATHINFO_DEPR'     => '/',	// PATHINFO模式下，各参数之间的分割符号
        'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置
        
        'VAR_PAGE'				=>"p",
        
        'URL_ROUTER_ON'			=> true,
        'URL_ROUTE_RULES'       => $routes,
        		
        /*性能优化*/
        'OUTPUT_ENCODE'			=>true,// 页面压缩输出
        
        'HTML_CACHE_ON'         =>    false, // 开启静态缓存
        'HTML_CACHE_TIME'       =>    60,   // 全局静态缓存有效期（秒）
        'HTML_FILE_SUFFIX'      =>    '.html', // 设置静态缓存文件后缀
        
        'TMPL_PARSE_STRING'=>array(
        	'/Public/upload'=>'/data/upload',
        	'__UPLOAD__' => __ROOT__.'/data/upload/',
        	'__STATICS__' => __ROOT__.'/statics/',
        )
);

return  array_merge($configs,$db,$runtime_config,$domains);
