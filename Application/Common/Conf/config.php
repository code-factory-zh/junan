<?php
return array(
	'MODULE_ALLOW_LIST' => ['Manage', 'Wechat'],
	'DEFAULT_MODULE' => 'Manage',
	'DEFAULT_GROUP' => 'Manage',

	'DB_PREFIX' => '', // 数据库表前缀 
	'DB_CHARSET'=> 'utf8', // 字符集
	'URL_MODEL' => 2,

	// Mysql信息
	'DB_TYPE'   => 'mysqli',
	'DB_HOST' => 'rm-8vb866v4t334z1d53do.mysql.zhangbei.rds.aliyuncs.com',
	'DB_NAME' => 'junan',
	'DB_USER' => 'root',
	'DB_PWD' => 'Joinersafe111111',
	'SESSION_OPTIONS' => array(
        'path' => RUNTIME_PATH . 'Cache/Manage/',
        'use_cookies' => 1,         //是否在客户端用 cookie 来存放会话 ID，1是开启
        'use_trans_sid' => true,    //跨页传递
        'expire' => 1800,
    ),
	'DB_PORT'   => 3306, // 端口
);