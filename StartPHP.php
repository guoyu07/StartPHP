<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-11
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

header('Content-type:text/html;Charset=utf-8');
final class Start{
	
	public static function run(){
				//定义StartPHP版本
		define('START_VERSION', '2013.11.11');
		//定义网站根物理路径
		define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']));
		//框架物理路径
		defined('START_PATH')||define('START_PATH', str_replace('\\', '/', dirname(__FILE__)));
		//DEBUG调试模式
		defined('DEBUG')||define('DEBUG', false);
		
		if (defined('APP_NAME')){
			strstr(APP_NAME, '/') && exit('应用名称：APP_NAME不能有斜杠‘/’');
			define('APP_PATH', './'.APP_NAME);
			define('APP_RELATIVE_PATH', '/'.APP_NAME);
		}else{
			define('APP_NAME','App');
 			define('APP_PATH','./'.APP_NAME);
		}
		if(defined('GROUP_NAME')){
			strstr(GROUP_NAME, '/') && exit('分组名称：GROUP_NAME不能有斜杠‘/’');
			define('GROUP_PATH', APP_PATH.'/'.GROUP_NAME);
		}
		//应用分组判断
		define('IS_GROUP', defined('GROUP_NAME'));
		
		//缓存目录
		define('TEMP_PATH',APP_PATH.'/Temp');
		//核心编译文件
		define('TEMP_FILE', TEMP_PATH.'/Temp.php');
		self::loadRuntime();
	}

	private static function loadRuntime(){
		if(is_file(TEMP_FILE)&&!DEBUG){
			require TEMP_FILE;
		}else{
			require START_PATH.'/Lib/Core/Boot.class.php';
			Boot::run(); 
		}
	}
	
	
}

Start::run();
?>