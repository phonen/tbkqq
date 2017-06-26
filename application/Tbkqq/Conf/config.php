<?php
$configs = array(
    'TAGLIB_BUILD_IN' => THINKCMF_CORE_TAGLIBS . ',Portal\Lib\Taglib\Portal',
    'TMPL_TEMPLATE_SUFFIX' => '.html', // 默认模板文件后缀
    'TMPL_FILE_DEPR' => '/', // 模板文件MODULE_NAME与ACTION_NAME之间的分割符
);

return array_merge($configs);
