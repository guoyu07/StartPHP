<?php
defined('START_PATH')||exit();
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-13
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

final class StartPHP{
	
	public static function init(){
		// 当前分组公共文件路径
		defined('COMMON_PATH')||define('COMMON_PATH', IS_GROUP?APP_PATH.'/Common':'');
		// 当前分组公共配置路径
		defined('COMMON_CONFIG_PATH')||define('COMMON_CONFIG_PATH', IS_GROUP?COMMON_PATH.'/Config':'');
		if(isset($_SERVER['PATH_INFO'])){
			$pathinfo=explode('/', trim($_SERVER['PATH_INFO'],'/'));
			$group=$pathinfo[0];
		}else{
			$group=isset($_GET[C('VAR_GROUP')])?$_GET[C('VAR_GROUP')]:C('DEFAULT_GROUP');
		}
		//公共文件目录
		defined('APP_CONFIG_PATH')||define('APP_CONFIG_PATH',IS_GROUP?GROUP_PATH.'/'.$group.'/Config':rtrim(APP_PATH,'/').'/Config');
		//解析路由
		if(IS_GROUP){
			//应用公共配置
			$commConfile=COMMON_CONFIG_PATH.'/Config.php';
			is_file($commConfile)&&C(require $commConfile);
			Route::groupPathinfo();
		}
		//应用配置
		$configFile=APP_CONFIG_PATH.'/Config.php';
		is_file($configFile)&&C(require $configFile);
		Route::appPathinfo();
		self::AppDir();
		if(C('SESSION_AUTO_START')){
			$sessionName=is_null(C('SESSION_NAME'))?'STARTPHP':C('SESSION_NAME');
			session_name($sessionName);
			session_id()||session_start();
		}
		date_default_timezone_set(C('TIMEZONE_SET'));
		//当前时间
		defined('NOW_TIME')||define('NOW_TIME', $_SERVER['REQUEST_TIME']);
		//微秒时间
		defined('NOW_MICROTIME')||define("NOW_MICROTIME", microtime(true));
		//判断请求方式
		defined('MAGIC_QUOTES_GPC')||define("MAGIC_QUOTES_GPC", @get_magic_quotes_gpc() ? true : false);
		defined('REQUEST_METHOD')||define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
		defined('IS_GET')||define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
		defined('IS_POST')||define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
		define("IS_AJAX", ajax_request());
		//是否PATH_INFO
		defined('IS_PATH_INFO')||define('IS_PATH_INFO',isset($_SERVER['PATH_INFO'])&&C('URL_TYPE')==1?true:false);
		spl_autoload_register(array(__CLASS__,'autoload'));
		set_error_handler(array(__CLASS__,'error'));
		set_exception_handler(array(__CLASS__,'execption'));
		//定义缓存常量
		defined('TEMP_TPL_NAME')||define('TEMP_TPL_NAME',IS_PATH_INFO?__WEB__.$_SERVER['PATH_INFO']:__URL__);
		defined('TPL_FILE')||define('TPL_FILE',TPL_PATH.'/'.CONTROL.'/'.ACTION.'.'.ltrim(C('TPL_TEMPLATE_SUFFIX'),'.'));
		defined('COMPILE_FILE')||define('COMPILE_FILE',COMPILE_PATH.'/'.sha1(TEMP_TPL_NAME).'.php');
		defined('CACHE_FILE')||define('CACHE_FILE',CACHE_PATH.'/'.sha1(TEMP_TPL_NAME).'.html');
	}
	
	
	//定义应用目录
	private static function AppDir(){
		// 当前配置文件路径
		defined('CONFIG_PATH')||define('CONFIG_PATH',(IS_GROUP?GROUP_PATH.'/'.GROUP:APP_PATH).'/Config');
		// 当前控制器路径
		defined('CONTROL_PATH')||define('CONTROL_PATH', (IS_GROUP?GROUP_PATH.'/'.GROUP:APP_PATH).'/'.C('APP_CONTROL_DIR'));
		// 当前模型路径
		defined('MODEL_PATH')||define('MODEL_PATH', (IS_GROUP?GROUP_PATH.'/'.GROUP:APP_PATH).'/'.C('APP_MODLE_DIR'));
		// 当前模板路径
		defined('TPL_PATH')||define('TPL_PATH', (IS_GROUP?GROUP_PATH.'/'.GROUP:APP_PATH).'/'.C('APP_TPL_DIR'));
		// 当前扩展路径
		defined('EXTEND_PATH')||define('EXTEND_PATH', (IS_GROUP?GROUP_PATH.'/'.GROUP:APP_PATH).'/'.C('APP_EXTEND_DIR'));
		// 当前扩展语言包路径
		defined('EXTEND_LANG_PATH')||define('EXTEND_LANG_PATH', EXTEND_PATH.'/'.C('APP_EXTEND_LANG'));
		// 当前扩展标签路径
		defined('EXTEND_TAG_PATH')||define('EXTEND_TAG_PATH', EXTEND_PATH.'/'.C('APP_EXTEND_TAG'));
		// 当前扩展插件包路径
		defined('EXTEND_PLUGIN_PATH')||define('EXTEND_PLUGIN_PATH',EXTEND_PATH.'/'.C('APP_EXTEND_PLUGIN'));
		// 当前扩展函数路径
		defined('EXTEND_FUNCTION_PATH')||define('EXTEND_FUNCTION_PATH',EXTEND_PATH.'/'.C('APP_EXTEND_FUNCTION'));
		// 当前扩展类路径
		defined('EXTEND_CLASS_PATH')||define('EXTEND_CLASS_PATH',EXTEND_PATH.'/'.C('APP_EXTEND_CLASS'));
		
		// 当前分组公共插件路径
		defined('COMMON_PLUGIN_PATH')||define('COMMON_PLUGIN_PATH', IS_GROUP?COMMON_PATH.'/'.C('APP_EXTEND_PLUGIN'):'');
		// 当前分组公共标签路径
		defined('COMMON_TAG_PATH')||define('COMMON_TAG_PATH', IS_GROUP?COMMON_PATH.'/'.C('APP_EXTEND_TAG'):'');
		//当前分组公共语言包路径
		defined('COMMON_LANG_PATH')||define('COMMON_LANG_PATH', IS_GROUP?COMMON_PATH.'/'.C('APP_EXTEND_LANG'):'');
		// 编译文件路径
		defined('COMPILE_PATH')||define('COMPILE_PATH', (IS_GROUP?TEMP_PATH.'/Compile/'.GROUP:TEMP_PATH.'/Compile').'/'.CONTROL);
		// 缓存文件路径
		defined('CACHE_PATH')||define('CACHE_PATH', (IS_GROUP?TEMP_PATH.'/Cache/'.GROUP:TEMP_PATH.'/Cache').'/'.CONTROL);
		// 表字段缓存路径
		defined('CACHE_TABLE_PATH')||define('CACHE_TABLE_PATH', TEMP_PATH.'/Table');
		// 日志文件路径
		defined('LOG_PATH')||define('LOG_PATH', TEMP_PATH.'/Log');
		
	}
	
	/**
	 * 自动载入类
	 * @access private
	 * @param string $className 自动捕获的类名
	 * @return null 
	 */
	private static function autoLoad($className){
		$class=$className.C('DEFAULT_CLASS_FIX').'.php';
		if(strpos($className,C('DEFAULT_CONTROL_FIX'))>0){
			$classFile=GROUP_PATH.'/'.GROUP.'/'.C('APP_CONTROL_DIR').'/'.$class;
			loadFile($classFile);	
		}elseif(substr($className,-5)=='Model'){
			require_array(array(
				MODEL_PATH.'/'.$class,
				START_MODEL_PATH.'/'.$class,
			));
		}elseif(substr($className,0,2)=='Db'){
			require_array(array(
				START_DB_PATH.'/'.$class,
			));
		}elseif(substr($className,0,7)=='Session'){
			require_array(array(
				START_SESSION_PATH.'/'.$class,
			));
		}elseif(substr($className,-5)=='Cache'){
			require_array(array(
				START_CACHE_PATH.'/'.$class,
			));
		}else{
			$classFile=START_CORE_PATH.'/'.$className.'.class.php';
			if(!is_file($classFile)){
				$classFile=START_EXTEND_PATH.'/Tool/'.$className.'.class.php';
			}
			loadFile($classFile);
		}
		
// 		error('文件载入失败！！');
	}
	
	/**
	 * 错误处理
	 * @param  $errno 		错误类型
	 * @param  $errStr 		错误信息
	 * @param  $errFile 	错误文件
	 * @param  $errLine 	错误所在行号
	 */
	static function error($errno,$errStr,$errFile,$errLine){
		switch ($errno){
			case E_ERROR:
			case E_USER_ERROR:
				$errMsg='错误类型：ERROR　错误编号：['.$errno.']　错误消息：<stong>'.$errStr.'</strong>　错误文件：'.$errFile.'　错误行号：['.$errLine.']';
				error($errMsg);
				break;
			case E_USER_NOTICE:
				$args=func_get_args();
				$args[]='NOTICE';
				notic($args);
				break;
			case E_USER_WARNING:
			default:
				$args=func_get_args();
				$args[]='WARNING';
				notic($args);
				break;	
		}
	}
	
	public static function execption($e){
		error($e->show());
	}
	
}

?>