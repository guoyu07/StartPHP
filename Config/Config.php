<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-6
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/
defined('START_PATH')||exit();

/**
 * StartPHP惯例配置文件
 * 该文件请不要修改，如果要覆盖惯例配置的值，可在项目配置文件中设定和惯例不符的配置项
 * 配置名称大小写任意，系统会统一转换成小写
 * 所有配置参数都可以在生效前动态改变
 */
return array(
		//应用目录配置
		'APP_CONTROL_DIR'		=>'Control',				// 控制器目录
		'APP_MODLE_DIR'			=>'Modle',					// 数据模型目录
		'APP_TPL_DIR'					=>'Tpl',						// 模板目录
		'APP_EXTEND_DIR'			=>'Extend',				// 扩展目录
		'APP_EXTEND_FUNCTION'	=>'Function',				// 扩展函数目录
		'APP_EXTEND_CLASS'		=>'Lib',						// 扩展类目录
		'APP_EXTEND_LANG'		=>'Lang',					// 扩展语言包目录
		'APP_EXTEND_TAG'			=>'Tag',						// 扩展标签库目录
		'APP_EXTEND_PLUGIN'		=>'Plugin',					// 扩展插件目录
		'APP_STATIC_DIR'			=>'Static',					// 纯静态html目录
		
		//数据库连接配置
		'DB_DRIVER'				=>'mysql',					// 数据库驱动 mysql,mysqli,pdo
		'DB_HOST'					=>'127.0.0.1',				// 数据库地址
		'DB_USER'					=>'root',					// 数据库用户名
		'DB_PWD'					=>'',							// 数据库密码
		'DB_NAME'					=>'test',					// 数据库名称
		'DB_PORT'					=>3306,					// 数据库端口
		'DB_CHARSET'				=>'utf8',					// 数据库编码(utf8不能加“-”)
		'DB_PREFIX'				=>'',							// 数据表前缀
		'DB_DATA_TYPE'			=>'array',					// object为对象数组，array为关联数组(查找返回的数组类型)
		'DB_TABLA_CACHE'		=>true,						// 是否缓存数据表
		
		/*系统配置*/
		'SHOW_TIME'				=>0,			//是否显示运行时间
		'DEBUG_SHOW'			=>0,			//是否显示DEBUG 1直接显示，0为键钮
		'NOTIC_SHOW'			=>1,			//是否开启提示性错误
		'DEBUG_TPL'				=>START_TPL_PATH.'/Debug.txt',//错误异常模板
		'ERROR_MESSAGE'		=>'啊哦！！页面出错了哦…………',//DEBUG调试模式关闭后显示的错误信息
		'TIMEZONE_SET'	=>'PRC',			//默认时区
		
		//日志处理
		'LOG_START'		=>1,		//开启日志
		'LOG_TYPE'			=>array('ERROR','WARNING','NOTICE','SQL'),//日志处理类型
		'LOG_SIZE'			=>2*pow(1024, 1),//日志大小限制
		
		
		
		/*缓存配置*/
		'CACHE_TYPE'				=>'1',	// 1为文件缓存，2为memcache缓存，3为redis缓存
		'MEMCACHE_LIFETIME'				=>1800,		// Memcache缓存生命周期
		
		'CACHE_MEMCACHE'                => array(       //多个服务器设置二维数组
				'host'      => '127.0.0.1',     //主机
				'port'      => 11211,           //端口
				'timeout'   => 1,               //超时时间(单位为秒)
				'weight'    => 1,               //权重
				'pconnect'  => 1,               //持久连接
		),
		'CACHE_REDIS'                   => array( //多个服务器设置二维数组
				'host'      => '127.0.0.1',     //主机
				'port'      => 6379,            //端口
				'password'  => '',              //密码
				'timeout'   => 1,               //超时时间
				'Db'        => 0,               //数据库
				'pconnect'  => 0,               //持久连接
		),
		'CACHE_TIME'                    => 3600,        //全局默认缓存时间 0为永久缓存
		'CACHE_SELECT_TIME'             => -1,          //SQL SELECT查询缓存时间 -1为不缓存 0为永久缓存
		'CACHE_SELECT_LENGTH'           => 30,          //缓存最大条数
		'CACHE_TPL_TIME'                => -1,           //模板缓存时间 -1为不缓存 0为永久缓存
		
		/*URL配置*/
		'HTTPS'						=>false,		// https协议
		'URL_TYPE'					=>1,				// URL类型，1 PATH_INFO(例如：index.php/Index/index)，2 兼容模式(例如：index.php?q=Index/index)，3 普通URL例如(index.php?c=Index&a=index)
		'URL_REWRITE'			=>1,				// URL重写 
		'PATHINFO_LIMIT'		=>'/',				// PATHINFO分割符
		'PATHINFO_VAR'			=>'q',			// 兼容模式GET变量
		'VAR_GROUP'				=>'g',			// 分组变量
		'VAR_CONTROL'			=>'c',			// 控制器变量
		'VAR_ACTION'				=>'a',			// 动作方法变量
		'PATHINFO_FIX'			=>'.html',		// PATHINFO伪静态后缀
		'URL_ROUTE'				=>array(		// URL路由设置
						'news'=>'/Index/News/index',
						'aricle'=>'Index/Aricle/index',
						),
		
		/*默认设置*/
		'DEFAULT_M_DIR'				=>'Model',					// 默认的模型层名称
		'DEFAULT_C_DIR'					=>'Control',				// 默认的控制器名称
		'DEFAULT_V_DIR'					=>'Tpl',						// 默认的视图层名称
		'DEFAULT_GROUP'				=>'Index',					// 默认分组名称(分组)
		'DEFAULT_CONTROL'			=>'Index',					// 默认模块名称(控制器)
		'DEFAULT_ACTION'				=>'index',					// 默认操作名称(方法)
		'DEFAULT_TIMEZOOE'			=>'PRC',					// 默认时区
		'DEFAULT_FILTER'				=>'htmlspecialchars',	// 默认数据过滤函数
		'DEFAULT_JUMP_TIME'			=>3,							// 默认页面跳转时间
		'DEFAULT_CONTROL_FIX'		=>'Control',				// 默认控制器后缀
		'DEFAULT_CLASS_FIX'			=>'.class',					// 默认名类后缀
		
		/* 模板引擎设置 */
		'TPL_CONTENT_TYPE'		=>'text/html', 										// 默认模板输出类型
		'TPL_ACTION_ERROR'		=>START_PATH.'/Lib/Tpl/Error.txt', 			// 默认错误跳转对应的模板文件
		'TPL_ACTION_SUCCESS'	=>START_PATH.'/Lib/Tpl/Success.txt', 		// 默认成功跳转对应的模板文件
		'TPL_EXCEPTION_FILE'		=>START_PATH.'/Lib/Tpl/Halt.txt',				// 异常页面的模板文件
		'TPL_404_ERROR'				=>START_PATH.'/Lib/Tpl/404.txt',				// 404错误模板文件
		'TPL_TEMPLATE_FILE'		=>START_PATH.'/Lib/Tpl/Template.txt',		// 当前控制器方法下的模板文件
		'TPL_AUTO_CREATE'			=>1,							// 自动创建模板，false不创建
		'TPL_TEMPLATE_SUFFIX'	=> '.tpl',    				// 默认模板文件后缀
		'TPL_DELIMITER_LEFT'		=>'{',						// 左定界符
		'TPL_DELIMITER_RIGHT'	=>'}',						// 右定界符
		'TPL_FUNCTION_LIMIT'		=>'.',							// 模板函数解析分割符
		'TPL_TAGS_AUTO_PARSE'	=>1,							// 是否自动解析自定义标签
		'TPL_STATIC_CACHE'	  	=>false,					// html静态缓存是否开启 (如果服务器内存过小请开启此缓存)
		'TPL_CACHE_LIFETIME'		=>1800,					// html静态缓存生命周期
		'TPL_TAGS'						=>array(					// 自定义模板块标签
													'test',
												),
		
		/*RBAC配置*/
		'RBAC_TYPE' 							=> 1,					// 1时时认证｜2登录认证
		'RBAC_SUPER_ADMIN'				=> 'root',			 	// 超级管理员SESSION名
		'RBAC_USERNAME_FIELD'			=> 'username',  		// 用户名字段
		'RBAC_PASSWORD_FIELD'			=> 'password',  		// 密码字段
		'RBAC_AUTH_KEY'					=> 'uid',     			// 用户SESSION名
		'RBAC_NO_AUTH'						=> array(),     		// 不需要验证的控制器或方法如:array('index/index')表示index控制器的index方法不需要验证
		'RBAC_USER_TABLE'					=> 'user',      			// 用户表
		'RBAC_ROLE_TABLE'					=> 'role',     			// 角色表
		'RBAC_NODE_TABLE'				=> 'node',      		// 节点表
		'RBAC_ROLE_USER_TABLE'		=> 'user_role', 		// 角色与用户关联表
		'RABC_ACCESS_TABLE'				=> 'access',    		// 权限分配表
		'ENCRYPTION_TYPE'					=> 'sha1',				// 加密方式(md5及sha1加密)
		
		/*分页配置*/
		'PAGE_SIZE'					=>10,					// 默认分页数
		'VAR_PAGE'						=>'page',				// 分页GET变量
		'PAGE_ROW_SIZE'			=>3,						// 页码数量
		'PAGE_STYLE'					=>1,						// 分页风格样式
		'PAGE_INFO'					=>array(				// 说明文字
												'pre'		=>'上一页',
												'next'		=>'下一页',
												'first'		=>'首页',
												'end'		=>'尾页',
												'unit'		=>'条'
											),
		
		
		/*上传配置 */
		'UP_PIC_PATH'				=>ROOT_PATH.'/Uploads/Images',			//上传图片文件位置
		'UP_PIC_TYPE'					=>array('jpg','gif','png'), 						//允许上传图片的类型
		'UP_PIC_MAX_SIZE'			=>pow(1024,3),									//允许上传图片最大大小
		'UP_PIC_MAX_WIDTH'		=>800,												//允许图片最大宽度，超过会自动裁切
		'UP_FILE_PATH'				=>ROOT_PATH.'/Uploads/Files',				//上传文件文件位置
		'UP_FILE_TYPE'				=>array('zip','7z','doc','txt','ppt','rar'),	//允许上传文件的类型
		'UP_FILE_MAX_SIZE'		=>pow(1024,3)*10,								//允许上传文件最大大小
		
		
		/*系统常量设置*/
		'__PUBLIC__'					=>'./Public',						//公用文件，如CSS JS IMG等,方便设置每个分组不同的public路径
		
		/*验证码配置*/
		'VERIFY_CODE'			=>true,					// 开启验证码
		'MATCH_CASE'				=>false,				// 是否区分大小写
		'CODE_BGCOLOR'		=>'#339900',			// 验证码背景颜色
		'CODE_LENGTH'			=>4,						// 验证码长度
		'CODE_FONT'				=>START_PATH.'Extends/Fonts/VRINDAB.TTF',//验证码字体
		
		/*SESSON配置*/
		'SESSION_AUTO_START'	=>true,					// 自动开启SESSION
		'SESSION_NAME'				=>'startphp',			// SESSION名称
		'SESSION_ENGINE'                => 'file',      //引擎:file,mysql,memcache
		'SESSION_SAVE_PATH'             => '',          //以文件处理时的位置
		'SESSION_LIFETIME'              => 1440,        //SESSION过期时间
		'SESSION_TABLE_NAME'            => 'session',   //SESSION的表名
		'SESSION_GC_DIVISOR'            => 10,          //SESSION清理频率,数字越小清理越频繁
		'SESSION_MEMCACHE'              => array(       //Memcache配置,支持集群
				'host' => '127.0.0.1',  //主机
				'port' => 11211         //端口
		),
		'SESSION_REDIS'                 => array(       //Redis配置,支持集群
				'host' => '127.0.0.1',          //主机
				'port' => 6379,                 //端口
				'password' => '',               //密码
				'Db' => 0,                      //数据库
		),
		
		
		/*水印配置*/
		'MARK_TYPE'				=>2,								// 水印类型，0为关闭水印，1为文件水印，2为图片水印
		'MARK_TEXT'				=>'www.startphp.net',		// 文字水印内容
		'MARK_COLOR'			=>'#ff0000',					// 文字水印颜色
		'MARK_ALPHA'				=>40,							// 水印透明度(0为完全透明，100不透明)
		'MARK_FONT'				=>'./images/Arial.ttf',		// 水印字体
		'MARK_FONT_SIZE'		=>25,							// 水印字体大小
		'MARK_PIC'					=>'./images/mark1.png',	// 水印图片(只能是PNG图片，建议透明，效果会更好)
		'MARK_POSITION'		=>1,								// 水印位置，1正中间，2左边中间，3右边中间，4下边中间，5上边中间，6左下角，7右下角，8左上角，9右上角
		'MARK_ANGLE'				=>0,								// 水印倾斜角度(图片水印不能倾斜)
		'MARK_SKEWING'			=>20,							// 水印偏移位置
		
		/*邮箱配置*/
		'EMAIL_USERNAME'		=> '',				// 邮箱用户名
		'EMAIL_PASSWORD'	=> '',				// 邮箱密码
		'EMAIL_HOST'				=> '',				// smtp地址如smtp.gmail.com或smtp.126.com建议使用126服务器
		'EMAIL_PORT'				=> 25,				// smtp端口 126为25，gmail为465
		'EMAIL_SSL'				=> 0,				// 是否采用SSL,126为false,google必须为true
		'EMAIL_CHARSET'		=> '',				// 字符集设置,中文乱码就是这个没有设置好 如utf8
		'EMAIL_FORMMAIL'		=> '',				// 发送人发件箱显示的邮箱址址
		'EMAIL_FROMNAME'		=> 'StartPHP'   	// 发送人发件箱显示的用户名
		
		
	
);

?>